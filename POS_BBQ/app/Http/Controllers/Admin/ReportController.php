<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Activity;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function activities()
    {
        $activities = Activity::with('user')->latest()->paginate(20);
        return view('admin.reports.activities', compact('activities'));
    }

    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to Carbon instances
        $startDateCarbon = Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = Carbon::parse($endDate)->endOfDay();

        // Get sales data
        $sales = Order::whereBetween('created_at', [$startDateCarbon, $endDateCarbon])
            ->where('payment_status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get total sales for the period
        $totalSales = $sales->sum('total_sales');
        $totalOrders = $sales->sum('order_count');

        // Get payment method breakdown
        $paymentMethods = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereBetween('payments.created_at', [$startDateCarbon, $endDateCarbon])
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        return view('admin.reports.sales', compact('sales', 'totalSales', 'totalOrders', 'paymentMethods', 'startDate', 'endDate'));
    }

    public function items(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to Carbon instances
        $startDateCarbon = Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = Carbon::parse($endDate)->endOfDay();

        // Get top selling items
        $topItems = MenuItem::select(
            'menu_items.id',
            'menu_items.name',
            'categories.name as category_name',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
        )
            ->join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDateCarbon, $endDateCarbon])
            ->where('orders.payment_status', 'paid')
            ->groupBy('menu_items.id', 'menu_items.name', 'categories.name')
            ->orderByDesc('total_quantity')
            ->limit(20)
            ->get();

        // Get category breakdown
        $categories = DB::table('categories')
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
            )
            ->join('menu_items', 'categories.id', '=', 'menu_items.category_id')
            ->join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDateCarbon, $endDateCarbon])
            ->where('orders.payment_status', 'paid')
            ->groupBy('categories.name')
            ->orderByDesc('total_sales')
            ->get();

        return view('admin.reports.items', compact('topItems', 'categories', 'startDate', 'endDate'));
    }

    public function staff(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to Carbon instances
        $startDateCarbon = Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = Carbon::parse($endDate)->endOfDay();

        // Get staff performance
        $staffPerformance = User::select(
            'users.id',
            'users.name',
            DB::raw('COUNT(orders.id) as total_orders'),
            DB::raw('SUM(orders.total_amount) as total_sales')
        )
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDateCarbon, $endDateCarbon])
            ->where('orders.payment_status', 'paid')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->get();

        return view('admin.reports.staff', compact('staffPerformance', 'startDate', 'endDate'));
    }
    public function daily(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);

        // 1. Shift Reports for the day
        $shiftReports = \App\Models\ShiftReport::with('user')
            ->whereDate('shift_date', $carbonDate)
            ->get();

        // 2. Aggregated Sales
        $totalSales = Order::whereDate('created_at', $carbonDate)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // 3. Aggregated Refunds
        $totalRefunds = \App\Models\Payment::whereDate('created_at', $carbonDate)
            ->where('amount', '<', 0) // Assuming refunds are negative payments or handled separately
            ->sum('amount'); // Adjust logic if refunds are stored differently

        // 4. Inventory Activities (Stock In/Out)
        $inventoryActivities = \App\Models\Activity::with('user')
            ->whereDate('created_at', $carbonDate)
            ->where('related_model', \App\Models\Inventory::class)
            ->get();

        // 5. Void Requests
        $voidRequests = \App\Models\VoidRequest::with(['order', 'requester', 'approver'])
            ->whereDate('created_at', $carbonDate)
            ->get();

        return view('admin.reports.daily', compact(
            'date',
            'shiftReports',
            'totalSales',
            'totalRefunds',
            'inventoryActivities',
            'voidRequests'
        ));
    }
}
