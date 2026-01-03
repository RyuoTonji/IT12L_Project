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
            // For inventory role
            return view('reports.create', compact('today', 'reportType', 'reportHistory'));
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

        // Admin role doesn't need reports
        if ($user->role === 'admin') {
            return response()->json(['has_report' => true]);
        }

        // 1. Get the latest report for this user
        $lastReport = ShiftReport::where('user_id', $user->id)
            ->latest()
            ->first();

        // If no report ever, or last report is too old (e.g., > 12 hours), assume new shift
        // We use 12 hours as a safe buffer for a single shift. 
        if (!$lastReport || $lastReport->created_at->diffInHours(now()) > 12) {
            // If no report recent, we check if there's any activity TODAY.
            // If they logged in but did nothing, maybe we don't force it?
            // But to be safe and consistent with previous behavior (always require), we return false.
            // However, strictly:
            return response()->json(['has_report' => false]);
        }

        // 2. If report exists and is recent (< 12 hours), check for Pending Activities SINCE that report
        $reportTime = $lastReport->created_at;

        // Check Orders (created or updated after report) - for Cashier/Manager
        $hasNewOrders = Order::withTrashed()
            ->where('branch_id', $user->branch_id) // Assuming orders are branch-scoped
            // Ideally filter by user_id if strict, but manager might oversee all. 
            // The constraint says "if the USER... has transactions".
            ->where('user_id', $user->id)
            ->where('updated_at', '>', $reportTime)
            ->exists();

        if ($hasNewOrders) {
            return response()->json(['has_report' => false, 'reason' => 'new_orders']);
        }

        // Check Inventory Logs (for Inventory/Manager)
        $hasNewInventory = DB::table('inventory_adjustments')
            ->where('recorded_by', $user->id)
            ->where('created_at', '>', $reportTime)
            ->exists();

        if ($hasNewInventory) {
            return response()->json(['has_report' => false, 'reason' => 'new_inventory']);
        }

        // Check Void Requests (for Manager/Cashier)
        // Requester
        $hasNewVoidRequests = DB::table('void_requests')
            ->where('requester_id', $user->id)
            ->where('created_at', '>', $reportTime)
            ->exists();

        // Approver (Manager)
        $hasApprovedVoids = DB::table('void_requests')
            ->where('approver_id', $user->id)
            ->where('updated_at', '>', $reportTime)
            ->exists();

        if ($hasNewVoidRequests || $hasApprovedVoids) {
            return response()->json(['has_report' => false, 'reason' => 'new_voids']);
        }

        // If we get here: Report exists, is recent, and NO new activities found.
        return response()->json(['has_report' => true]);
    }

    /**
     * Store a newly created shift report.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $reportType = in_array($user->role, ['manager', 'cashier']) ? 'sales' : 'inventory';

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

            ShiftReport::create([
                'user_id' => $user->id,
                'report_type' => 'sales',
                'shift_date' => $shiftDate,
                'total_sales' => $totalSales,
                'total_refunds' => $totalRefunds,
                'total_orders' => $totalOrders,
                'content' => $request->input('content'),
                'status' => 'submitted',
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

            ShiftReport::create([
                'user_id' => $user->id,
                'report_type' => 'inventory',
                'shift_date' => $shiftDate,
                'stock_in' => $request->stock_in,
                'stock_out' => $request->stock_out,
                'remaining_stock' => $request->remaining_stock,
                'spoilage' => $request->spoilage,
                'returns' => $request->returns,
                'return_reason' => $request->return_reason,
                'content' => $request->input('content'),
                'status' => 'submitted',
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Shift report submitted successfully.');
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
