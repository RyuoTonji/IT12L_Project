<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date filter - allow clearing to show all time data
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // ALL TIME STATISTICS (Not affected by date filter)
        $totalOrdersAllTime = DB::table('orders')
            ->whereNull('deleted_at')
            ->count();
        
        $totalRevenueAllTime = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereIn('status', ['confirmed', 'delivered'])
            ->sum('total_amount');

        // TODAY'S STATISTICS (Always show today, regardless of filter)
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $todayOrders = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->count();
        
        $todayRevenue = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereIn('status', ['confirmed', 'delivered'])
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('total_amount');

        // PENDING ORDERS (Current, not affected by date filter)
        $pendingOrders = DB::table('orders')
            ->whereNull('deleted_at')
            ->where('status', 'pending')
            ->count();
        
        // TOTAL PRODUCTS (Current, not affected by date filter)
        $totalProducts = DB::table('products')
            ->whereNull('deleted_at')
            ->count();

        // SALES BY BRANCH (Affected by date filter if applied)
        $salesByBranchQuery = DB::table('orders')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
            ->whereNull('orders.deleted_at')
            ->whereNull('branches.deleted_at')
            ->whereIn('orders.status', ['confirmed', 'delivered'])
            ->select(
                'branches.name as branch_name',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('COALESCE(SUM(orders.total_amount), 0) as total_sales')
            );

        // Apply date filter if provided
        if ($startDate && $endDate) {
            $salesByBranchQuery->whereBetween('orders.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $salesByBranch = $salesByBranchQuery
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('total_sales')
            ->get();

        // TOP SELLING PRODUCTS (Affected by date filter if applied)
        $topProductsQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNull('orders.deleted_at')
            ->whereIn('orders.status', ['confirmed', 'delivered'])
            ->select(
                'order_items.product_name as name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('COALESCE(SUM(order_items.subtotal), 0) as total_sales')
            );

        // Apply date filter if provided
        if ($startDate && $endDate) {
            $topProductsQuery->whereBetween('orders.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $topProducts = $topProductsQuery
            ->groupBy('order_items.product_name')
            ->havingRaw('SUM(order_items.quantity) > 1')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();

        // DAILY SALES CHART (Affected by date filter if applied)
        $dailySalesQuery = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereIn('status', ['confirmed', 'delivered'])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total')
            );

        // Apply date filter if provided
        if ($startDate && $endDate) {
            $dailySalesQuery->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $dailySales = $dailySalesQuery
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // RECENT ORDERS (Always show latest 10, not affected by filter)
        $recentOrders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
            ->whereNull('orders.deleted_at')
            ->select(
                'orders.id',
                'orders.total_amount',
                'orders.status',
                'orders.created_at as ordered_at',
                'users.name as user_name',
                'branches.name as branch_name'
            )
            ->orderBy('orders.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrdersAllTime',
            'totalRevenueAllTime',
            'todayOrders',
            'todayRevenue',
            'pendingOrders',
            'totalProducts',
            'salesByBranch',
            'dailySales',
            'topProducts',
            'recentOrders',
            'startDate',
            'endDate'
        ));
    }
}