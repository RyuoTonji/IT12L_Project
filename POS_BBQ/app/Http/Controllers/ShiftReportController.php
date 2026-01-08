<?php

namespace App\Http\Controllers;

use App\Models\ShiftReport;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftReportController extends Controller
{
    /**
     * Determine if a user needs to submit a shift report.
     * Returns true if a report is required, false otherwise.
     */
    public static function needsReport($user)
    {
        // Admin role doesn't need reports
        if ($user->role === 'admin') {
            return false;
        }

        $today = Carbon::today();

        // 1. Check Role-Specific Requirements
        if ($user->role === 'inventory') {
            // Inventory needs BOTH start and end reports.
            // If end report exists, they are done for the day.
            $hasEndReport = ShiftReport::where('user_id', $user->id)
                ->where('report_type', 'inventory_end')
                ->whereDate('shift_date', $today)
                ->exists();

            if ($hasEndReport) {
                return false;
            }

            // If no end report, check if they have a start report.
            $startReport = ShiftReport::where('user_id', $user->id)
                ->where('report_type', 'inventory_start')
                ->whereDate('shift_date', $today)
                ->latest()
                ->first();

            if (!$startReport) {
                // If no start report, do they have ANY activity today?
                return self::hasActivitySince($user, $today);
            } else {
                // They have a start report. They need an end report if they have activity SINCE the start report.
                // Or if it's logout time, we typically expect an end report regardless if they are active.
                // But to be consistent with "no activity = no report", we check activity since start report.
                return self::hasActivitySince($user, $startReport->created_at);
            }
        } elseif (in_array($user->role, ['cashier', 'manager'])) {
            // Cashier/Manager needs one 'sales' report.
            $hasSalesReport = ShiftReport::where('user_id', $user->id)
                ->where('report_type', 'sales')
                ->whereDate('shift_date', $today)
                ->exists();

            if ($hasSalesReport) {
                return false;
            }

            // Check if they have ANY activity today.
            return self::hasActivitySince($user, $today);
        }

        return false;
    }

    /**
     * Check for any relevant activity since a given time.
     */
    public static function hasActivitySince($user, $sinceTime)
    {
        // Check Orders (created or updated)
        $hasNewOrders = Order::withTrashed()
            ->where('branch_id', $user->branch_id)
            ->where('user_id', $user->id)
            ->where('updated_at', '>', $sinceTime)
            ->exists();

        if ($hasNewOrders)
            return true;

        // Check Inventory Logs
        $hasNewInventory = DB::table('pos_inventory_adjustments')
            ->where('recorded_by', $user->id)
            ->where('created_at', '>', $sinceTime)
            ->exists();

        if ($hasNewInventory)
            return true;

        // Check Void Requests
        $hasNewVoids = DB::table('pos_void_requests')
            ->where(function ($q) use ($user) {
                $q->where('requester_id', $user->id)
                    ->orWhere('approver_id', $user->id);
            })
            ->where('updated_at', '>', $sinceTime)
            ->exists();

        if ($hasNewVoids)
            return true;

        /*
        // Check Activities
        $hasNewActivities = DB::table('pos_activities')
            ->where('user_id', $user->id)
            ->where('created_at', '>', $sinceTime)
            ->exists();

        if ($hasNewActivities)
            return true;
        */

        return false;
    }
    /**
     * Show the form for creating a new shift report.
     */
    public function create()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $reportType = in_array($user->role, ['manager', 'cashier']) ? 'sales' : 'inventory';

        // Fetch user's report history
        $reportHistory = ShiftReport::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        if ($reportType === 'sales') {
            // Calculate today's stats for manager/cashier
            $orders = Order::with('orderItems.menuItem')
                ->withTrashed()
                ->where('branch_id', $user->branch_id)
                ->whereDate('created_at', $today)
                ->get();

            $totalOrders = $orders->count();
            $totalSales = $orders->where('payment_status', 'paid')->sum('total_amount');
            $totalRefunds = $orders->where('status', 'cancelled')
                ->where('payment_status', 'refunded')
                ->sum('total_amount');

            return view('reports.create', compact('totalOrders', 'totalSales', 'totalRefunds', 'today', 'reportType', 'reportHistory', 'orders'));
        } else {
            // For inventory role, they should generally use the inventory-specific dashboard report,
            // but if they come here, we redirect them to the correct route or show a basic unified view.
            return redirect()->route('inventory.daily-report');
        }
    }

    /**
     * Check if user has submitted a shift report for the current session.
     * Logic:
     * 1. Check if a report was created reasonably recently (e.g., last 12-16 hours) to handle midnight crossovers.
     * 2. If a report exists, check if ANY new activity (orders, inventory, voids) occurred AFTER that report.
     * 3. If new activity exists, require a new report.
     */
    public function check()
    {
        $user = Auth::user();

        if (self::needsReport($user)) {
            // If needsReport returns true, it means they rely have activity and NO report today.
            // We can optionally determine specific reason if we want to modify needsReport checks,
            // but for now, simple boolean is enough to say "Activity detected, please report".
            // To match old response format:
            return response()->json(['has_report' => false, 'reason' => 'daily_activity_detected']);
        }

        return response()->json(['has_report' => true]);
    }



    /**
     * Store a newly created shift report.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $reportType = in_array($user->role, ['manager', 'cashier']) ? 'sales' : 'inventory';

        // Check if report is needed just in case (optional, but good for validation)
        if (!self::needsReport($user)) {
            // If they are submitting but don't need to...
            // If there is already a report, we handle duplicates below.
            // If there is NO activity, they shouldn't be submitting a report?
            // Actually, user might WANT to submit an empty report manually?
            // The user requirement: "if the user log in and no changes happens ... don't persist"
            // This can be interpreted as "Don't AUTOSAVE empty reports" or "Don't allow manual save if empty".
            // Given "Shift report persists to submit...", I'll assume preventing duplicate is the main key. 
            // If they manually explicitly fill the form and submit, we generally trust them, UNLESS it's a duplicate.
        }


        if ($reportType === 'sales') {
            $request->validate([
                'shift_date' => 'required|date',
                'content' => 'required|string',
            ]);

            $shiftDate = Carbon::parse($request->shift_date);

            // Recalculate stats for the specified date
            $orders = Order::withTrashed()
                ->where('branch_id', $user->branch_id)
                ->whereDate('created_at', $shiftDate)
                ->get();

            $totalOrders = $orders->count();
            $totalSales = $orders->where('payment_status', 'paid')->sum('total_amount');
            $totalRefunds = $orders->where('status', 'cancelled')
                ->where('payment_status', 'refunded')
                ->sum('total_amount');

            // Prevent duplicate sales reports for the same day
            $existingReport = ShiftReport::where('user_id', $user->id)
                ->where('report_type', 'sales')
                ->whereDate('shift_date', $shiftDate)
                ->first();

            if ($existingReport) {
                // Return success immediately or update? 
                // User asked: "persists to submit a report even if the report is already submitted... don't persist"
                // So we stick with the existing one.
                return redirect()->route('dashboard')->with('success', 'Shift report already submitted for today. You can now log out.');
            }

            ShiftReport::create([
                'user_id' => $user->id,
                'report_type' => 'sales',
                'shift_date' => $shiftDate,
                'total_sales' => $totalSales,
                'total_refunds' => $totalRefunds,
                'total_orders' => $totalOrders,
                'content' => $request->input('content'),
                'status' => 'submitted',
                'branch_id' => $user->branch_id,
            ]);
        } else {
            $request->validate([
                'shift_date' => 'required|date',
                'stock_in' => 'required|numeric|min:0',
                'stock_out' => 'required|numeric|min:0',
                'remaining_stock' => 'required|numeric|min:0',
                'spoilage' => 'nullable|numeric|min:0',
                'returns' => 'nullable|numeric|min:0',
                'return_reason' => 'required_with:returns|string|nullable',
                'content' => 'required|string',
            ]);

            $shiftDate = Carbon::parse($request->shift_date);

            $existingReport = ShiftReport::where('user_id', $user->id)
                ->whereIn('report_type', ['inventory', 'inventory_end'])
                ->whereDate('shift_date', $shiftDate)
                ->first();

            if ($existingReport) {
                return redirect()->route('dashboard')->with('success', 'Inventory report already submitted for today. You can now log out.');
            }

            ShiftReport::create([
                'user_id' => $user->id,
                'report_type' => 'inventory_end', // Standardization
                'shift_date' => $shiftDate,
                'stock_in' => $request->stock_in,
                'stock_out' => $request->stock_out,
                'remaining_stock' => $request->remaining_stock,
                'spoilage' => $request->spoilage,
                'returns' => $request->returns,
                'return_reason' => $request->return_reason,
                'content' => $request->input('content'),
                'status' => 'submitted',
                'branch_id' => $user->branch_id,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Shift report submitted successfully. You can now log out.');
    }

    /**
     * Display a listing of shift reports (Admin/Manager view).
     */
    public function index(Request $request)
    {
        $query = ShiftReport::with('user');

        // Tab Filtering (Role-based)
        $activeTab = $request->get('tab', 'cashier');
        $validTabs = ['cashier', 'inventory', 'manager'];

        if (!in_array($activeTab, $validTabs)) {
            $activeTab = 'cashier';
        }

        // Apply role filter based on tab
        $query->whereHas('user', function ($q) use ($activeTab) {
            $q->where('role', $activeTab);
        });

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($uq) use ($searchTerm) {
                    $uq->where('name', 'like', '%' . $searchTerm . '%');
                })->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by Staff (Scoped to the current role tab potentially, but existing logic assumes all users)
        // Ideally we only show staff matching the role, but for now we keep generic filter if needed, 
        // or rely on the tab filter to narrow down the results primarily.
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by Date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('shift_date', $request->date);
        }

        $reports = $query->latest()->paginate(20)
            ->appends($request->all()); // Preserve filters in pagination

        // Fetch users for the filter dropdown - strictly speaking good to filter by role too but generic is fine
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.shift_reports.index', compact('reports', 'users', 'activeTab'));
    }

    /**
     * Show a specific shift report.
     */
    public function show(ShiftReport $shiftReport)
    {
        $shiftReport->load('user');
        return view('admin.shift_reports.show', compact('shiftReport'));
    }

    /**
     * Update the shift report with admin reply.
     */
    public function reply(Request $request, ShiftReport $shiftReport)
    {
        $request->validate([
            'admin_reply' => 'required|string',
        ]);

        $shiftReport->update([
            'admin_reply' => $request->admin_reply,
            'status' => 'reviewed',
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }

    /**
     * Export all shift reports as PDF (with current filters).
     */
    public function exportAll(Request $request)
    {
        $query = ShiftReport::with('user');

        //Apply the same filters as index method
        $activeTab = $request->get('tab', 'cashier');
        $validTabs = ['cashier', 'inventory', 'manager'];

        if (!in_array($activeTab, $validTabs)) {
            $activeTab = 'cashier';
        }

        $query->whereHas('user', function ($q) use ($activeTab) {
            $q->where('role', $activeTab);
        });

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($uq) use ($searchTerm) {
                    $uq->where('name', 'like', '%' . $searchTerm . '%');
                })->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('shift_date', $request->date);
        }

        $reports = $query->latest()->get();

        // Get metadata
        $exporter = auth()->user();
        $branch = $exporter->branch;
        $exportDate = now()->format('F d, Y');
        $exportTime = now()->format('h:i A');
        $filterSummary = $this->buildFilterSummary($request, $activeTab);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.shift_reports_all_pdf', compact(
            'reports',
            'exporter',
            'branch',
            'exportDate',
            'exportTime',
            'activeTab',
            'filterSummary'
        ));

        return $pdf->download('shift_reports_all_' . now()->format('Y-m-d_His') . '.pdf');
    }

    /**
     * Build filter summary string for export
     */
    private function buildFilterSummary(Request $request, $activeTab)
    {
        $filters = [];
        $filters[] = "Role: " . ucfirst($activeTab);

        if ($request->has('date') && $request->date != '') {
            $filters[] = "Date: " . \Carbon\Carbon::parse($request->date)->format('M d, Y');
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $user = \App\Models\User::find($request->user_id);
            if ($user) {
                $filters[] = "Staff: " . $user->name;
            }
        }

        if ($request->has('status') && $request->status != '') {
            $filters[] = "Status: " . ucfirst($request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $filters[] = "Search: " . $request->search;
        }

        return implode(' | ', $filters);
    }
}
