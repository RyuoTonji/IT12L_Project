<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\InventoryAdjustment;
use App\Models\OrderItem;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManagerController extends Controller
{
    public function index()
    {
        // Fetch recent activities for the dashboard
        $recentActivities = Activity::with('user')->latest()->take(10)->get();

        // Calculate today's stats
        $todaySales = Order::whereDate('created_at', Carbon::today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $todayOrders = Order::whereDate('created_at', Carbon::today())
            ->count();

        return view('manager.dashboard', compact('recentActivities', 'todaySales', 'todayOrders'));
    }

    public function reports()
    {
        return view('manager.reports');
    }

    // Report Methods
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
            ->where('amount', '<', 0)
            ->sum('amount');

        // 4. Inventory Activities
        $inventoryActivities = \App\Models\Activity::with('user')
            ->whereDate('created_at', $carbonDate)
            ->where('related_model', \App\Models\Inventory::class)
            ->get();

        // 5. Void Requests
        $voidRequests = \App\Models\VoidRequest::with(['order', 'requester', 'approver'])
            ->whereDate('created_at', $carbonDate)
            ->get();

        return view('manager.reports.daily', compact(
            'date',
            'shiftReports',
            'totalSales',
            'totalRefunds',
            'inventoryActivities',
            'voidRequests'
        ));
    }

    public function staff(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $startDateCarbon = Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = Carbon::parse($endDate)->endOfDay();

        $staffPerformance = \App\Models\User::select(
            'users.id',
            'users.name',
            \Illuminate\Support\Facades\DB::raw('COUNT(orders.id) as total_orders'),
            \Illuminate\Support\Facades\DB::raw('SUM(orders.total_amount) as total_sales')
        )
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDateCarbon, $endDateCarbon])
            ->where('orders.payment_status', 'paid')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->get();

        return view('manager.reports.staff', compact('staffPerformance', 'startDate', 'endDate'));
    }

    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $startDateCarbon = Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = Carbon::parse($endDate)->endOfDay();

        $sales = Order::whereBetween('created_at', [$startDateCarbon, $endDateCarbon])
            ->where('payment_status', 'paid')
            ->select(
                \Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'),
                \Illuminate\Support\Facades\DB::raw('SUM(total_amount) as total_sales'),
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalSales = $sales->sum('total_sales');
        $totalOrders = $sales->sum('order_count');

        $paymentMethods = \Illuminate\Support\Facades\DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereBetween('payments.created_at', [$startDateCarbon, $endDateCarbon])
            ->select('payment_method', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'), \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        return view('manager.reports.sales', compact('sales', 'totalSales', 'totalOrders', 'paymentMethods', 'startDate', 'endDate'));
    }
    public function inventory(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        // Stock In (Ingredients Added)
        $stockIns = InventoryAdjustment::with(['inventory', 'recorder'])
            ->where('adjustment_type', 'stock_in')
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        // Prepared Dishes (Sales)
        $preparedDishes = OrderItem::with('menuItem')
            ->whereHas('order', function ($query) use ($date) {
                $query->whereDate('created_at', $date)
                    ->whereNotIn('status', ['cancelled']);
            })
            ->select('menu_item_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(unit_price * quantity) as total_amount'))
            ->groupBy('menu_item_id')
            ->get();

        return view('manager.reports.inventory', compact('stockIns', 'preparedDishes', 'date'));
    }
}
