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
            $orders = Order::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->get();

            $totalOrders = $orders->count();
            $totalSales = $orders->where('payment_status', 'paid')->sum('total_amount');
            $totalRefunds = $orders->where('status', 'cancelled')
                ->where('payment_status', 'refunded')
                ->sum('total_amount');

            return view('reports.create', compact('totalOrders', 'totalSales', 'totalRefunds', 'today', 'reportType', 'reportHistory'));
        } else {
            // For inventory role
            return view('reports.create', compact('today', 'reportType', 'reportHistory'));
        }
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
            $orders = Order::where('user_id', $user->id)
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

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('shift_date', $request->date);
        }

        $reports = $query->latest()->paginate(20);

        return view('admin.shift_reports.index', compact('reports'));
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
}
