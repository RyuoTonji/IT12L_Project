<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Check if this is an AJAX request for live count
        if ($request->ajax() && $request->has('live_count')) {
            return $this->getLiveOrderCount($request);
        }

        // Check if this is an AJAX request for chart data
        if ($request->ajax() && $request->has('chart_type')) {
            return $this->getFilteredChartData($request);
        }

        // ============================================================================
        // STATIC METRICS (NEVER FILTERED)
        // ============================================================================

        // All Time Totals
        $totalOrdersAllTime = DB::table('orders')
            ->whereNull('deleted_at')
            ->count();

        $totalRevenueAllTime = DB::table('orders')
            ->whereNull('deleted_at')
            ->sum('total_amount');

        // Total Products
        $totalProducts = DB::table('products')
            ->whereNull('deleted_at')
            ->count();

        // Current Pending Orders (All Time)
        $pendingOrders = DB::table('orders')
            ->whereNull('deleted_at')
            ->where('status', 'pending')
            ->count();

        // ============================================================================
        // TODAY'S METRICS (DEFAULT - SYNCED WITH LIVE & STATUS)
        // ============================================================================
        $statusStartDate = $request->get('status_start_date');
        $statusEndDate = $request->get('status_end_date');

        // Default to TODAY ONLY if no filter is applied
        if (!$statusStartDate && !$statusEndDate) {
            $statusFilterStart = Carbon::today()->startOfDay();
            $statusFilterEnd = Carbon::today()->endOfDay();
        } else {
            $statusFilterStart = Carbon::parse($statusStartDate)->startOfDay();
            $statusFilterEnd = Carbon::parse($statusEndDate)->endOfDay();
        }

        // TODAY'S ORDERS (uses status filter)
        $todayOrders = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$statusFilterStart, $statusFilterEnd])
            ->count();

        // TODAY'S REVENUE (uses status filter)
        $todayRevenue = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$statusFilterStart, $statusFilterEnd])
            ->sum('total_amount');

        // LIVE ORDER COUNT (uses status filter)
        $recentOrderCount = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$statusFilterStart, $statusFilterEnd])
            ->count();

        // Calculate trend (compare with previous period)
        $periodDuration = $statusFilterStart->diffInSeconds($statusFilterEnd);
        $previousFilterStart = $statusFilterStart->copy()->subSeconds($periodDuration);
        $previousFilterEnd = $statusFilterStart->copy();

        $previousOrderCount = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$previousFilterStart, $previousFilterEnd])
            ->count();

        $orderTrend = 0;
        if ($previousOrderCount > 0) {
            $orderTrend = (($recentOrderCount - $previousOrderCount) / $previousOrderCount) * 100;
        } elseif ($recentOrderCount > 0) {
            $orderTrend = 100;
        }

        // ============================================================================
        // SALES PERFORMANCE CHART (SEPARATE FILTER)
        // ============================================================================
        $salesYear = $request->get('year');
        $salesStartDate = $request->get('start_date');
        $salesEndDate = $request->get('end_date');
        $salesBranchId = $request->get('branch_id');

        // Build query
        $salesQuery = DB::table('orders')->whereNull('deleted_at');

        // Apply date filter based on mode
        if ($salesStartDate && $salesEndDate) {
            // Quick filter mode (30 days, 4 months, 1 year)
            $filterStart = Carbon::parse($salesStartDate)->startOfDay();
            $filterEnd = Carbon::parse($salesEndDate)->endOfDay();
            $salesQuery->whereBetween('created_at', [$filterStart, $filterEnd]);
        } elseif ($salesYear && $salesYear !== 'all') {
            // Year filter mode
            $salesQuery->whereYear('created_at', $salesYear);
        } else {
            // Default to last 6 months
            $salesQuery->whereBetween('created_at', [
                Carbon::now()->subMonths(6)->startOfDay(),
                Carbon::now()->endOfDay()
            ]);
        }

        // Apply branch filter
        if ($salesBranchId && $salesBranchId !== 'all') {
            $salesQuery->where('branch_id', $salesBranchId);
        }

        $salesPerformance = (clone $salesQuery)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Daily Sales for trend chart
        $dailySales = (clone $salesQuery)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // ============================================================================
        // ORDERS BY STATUS (USES STATUS FILTER)
        // ============================================================================
        $ordersByStatus = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$statusFilterStart, $statusFilterEnd])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $statusLabels = ['pending', 'confirmed', 'preparing', 'ready', 'picked up', 'cancelled'];
        $orderStatusData = [];
        foreach ($statusLabels as $status) {
            $orderStatusData[$status] = $ordersByStatus[$status] ?? 0;
        }

        // Quick Insights (filtered totals)
        $filteredTotalOrders = array_sum($orderStatusData);
        $filteredRevenue = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$statusFilterStart, $statusFilterEnd])
            ->sum('total_amount');

        // ============================================================================
        // CONFIRMED SALES BY BRANCH (NEVER FILTERED - ALL TIME)
        // ============================================================================
        $salesByBranch = DB::table('orders')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
            ->whereNull('orders.deleted_at')
            ->whereNull('branches.deleted_at')
            ->where('orders.status', 'picked up')
            ->select(
                'branches.id as branch_id',
                'branches.name as branch_name',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('COALESCE(SUM(orders.total_amount), 0) as total_sales')
            )
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('total_sales')
            ->get();

        // ============================================================================
        // TOP SELLING PRODUCTS (NEVER FILTERED - ALL TIME)
        // ============================================================================
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNull('orders.deleted_at')
            ->where('orders.status', 'picked up')
            ->select(
                'order_items.product_name as name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('COALESCE(SUM(order_items.subtotal), 0) as total_sales')
            )
            ->groupBy('order_items.product_name')
            ->havingRaw('SUM(order_items.quantity) > 0')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();

        // ============================================================================
        // RECENT ORDERS (ALWAYS LATEST 10)
        // ============================================================================
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

        // ============================================================================
        // BRANCH LIST FOR FILTERS
        // ============================================================================
        $branches = DB::table('branches')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', compact(
            // Static Metrics
            'totalOrdersAllTime',
            'totalRevenueAllTime',
            'totalProducts',
            'pendingOrders',

            // Today's Metrics (Synced - all use status filter)
            'todayOrders',
            'todayRevenue',
            'recentOrderCount',
            'previousOrderCount',
            'orderTrend',

            // Sales Performance (Separate Filter)
            'salesPerformance',
            'dailySales',

            // Orders by Status (Synced with Today's Metrics)
            'orderStatusData',
            'filteredTotalOrders',
            'filteredRevenue',
            'statusStartDate',
            'statusEndDate',
            'statusFilterStart',
            'statusFilterEnd',

            // Never Filtered
            'salesByBranch',
            'topProducts',

            // Other
            'recentOrders',
            'branches'
        ));
    }

    /**
     * Get live order count for AJAX requests
     */
    private function getLiveOrderCount(Request $request)
    {
        $statusStartDate = $request->get('status_start_date');
        $statusEndDate = $request->get('status_end_date');

        // Default to TODAY ONLY if no filter
        if (!$statusStartDate && !$statusEndDate) {
            $statusFilterStart = Carbon::today()->startOfDay();
            $statusFilterEnd = Carbon::today()->endOfDay();
        } else {
            $statusFilterStart = Carbon::parse($statusStartDate)->startOfDay();
            $statusFilterEnd = Carbon::parse($statusEndDate)->endOfDay();
        }

        // Current period count
        $recentOrderCount = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$statusFilterStart, $statusFilterEnd])
            ->count();

        // Previous period count (same duration)
        $periodDuration = $statusFilterStart->diffInSeconds($statusFilterEnd);
        $previousFilterStart = $statusFilterStart->copy()->subSeconds($periodDuration);
        $previousFilterEnd = $statusFilterStart->copy();

        $previousOrderCount = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$previousFilterStart, $previousFilterEnd])
            ->count();

        $orderTrend = 0;
        if ($previousOrderCount > 0) {
            $orderTrend = (($recentOrderCount - $previousOrderCount) / $previousOrderCount) * 100;
        } elseif ($recentOrderCount > 0) {
            $orderTrend = 100;
        }

        // Today's stats
        $todayOrders = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$statusFilterStart, $statusFilterEnd])
            ->count();

        $todayRevenue = DB::table('orders')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$statusFilterStart, $statusFilterEnd])
            ->sum('total_amount');

        return response()->json([
            'recentOrderCount' => $recentOrderCount,
            'previousOrderCount' => $previousOrderCount,
            'orderTrend' => $orderTrend,
            'todayOrders' => $todayOrders,
            'todayRevenue' => $todayRevenue
        ]);
    }

    /**
     * Get filtered chart data for AJAX requests
     * Handles separate filters for different chart types
     */
    private function getFilteredChartData(Request $request)
    {
        $chartType = $request->get('chart_type');
        $branchId = $request->get('branch_id');
        $year = $request->get('year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $response = [];

        // Sales Performance Chart (Separate filter)
        if ($chartType === 'sales_performance' || !$chartType) {
            $query = DB::table('orders')->whereNull('deleted_at');

            // Check which filter mode is active
            if ($startDate && $endDate) {
                // Quick filter mode
                $filterStart = Carbon::parse($startDate)->startOfDay();
                $filterEnd = Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('created_at', [$filterStart, $filterEnd]);
            } elseif ($year && $year !== 'all') {
                // Year filter mode
                $query->whereYear('created_at', $year);
            } else {
                // Default to last 6 months
                $query->whereBetween('created_at', [
                    Carbon::now()->subMonths(6)->startOfDay(),
                    Carbon::now()->endOfDay()
                ]);
            }

            // Apply branch filter
            if ($branchId && $branchId !== 'all') {
                $query->where('branch_id', $branchId);
            }

            $salesPerformance = (clone $query)
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('SUM(total_amount) as total_sales'),
                    DB::raw('COUNT(*) as order_count')
                )
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            $dailySales = (clone $query)
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as order_count'),
                    DB::raw('SUM(total_amount) as total')
                )
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date', 'asc')
                ->get();

            $response['salesPerformance'] = $salesPerformance;
            $response['dailySales'] = $dailySales;
        }

        // Orders by Status & Quick Insights (Status filter with branch support)
        if ($chartType === 'order_status' || !$chartType) {
            // Determine date range
            if ($startDate && $endDate) {
                $filterStartDate = Carbon::parse($startDate)->startOfDay();
                $filterEndDate = Carbon::parse($endDate)->endOfDay();
            } else {
                // Default to TODAY ONLY
                $filterStartDate = Carbon::today()->startOfDay();
                $filterEndDate = Carbon::today()->endOfDay();
            }

            $statusQuery = DB::table('orders')
                ->whereNull('deleted_at')
                ->whereBetween('created_at', [$filterStartDate, $filterEndDate]);

            // Apply branch filter for status data
            if ($branchId && $branchId !== 'all') {
                $statusQuery->where('branch_id', $branchId);
            }

            $ordersByStatus = (clone $statusQuery)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();

            $statusLabels = ['pending', 'confirmed', 'preparing', 'ready', 'picked up', 'cancelled'];
            $orderStatusData = [];
            foreach ($statusLabels as $status) {
                $orderStatusData[$status] = $ordersByStatus[$status] ?? 0;
            }

            $filteredTotalOrders = array_sum($orderStatusData);
            $filteredRevenue = (clone $statusQuery)->sum('total_amount');

            // Calculate trend for live metrics
            $periodDuration = $filterStartDate->diffInSeconds($filterEndDate);
            $previousFilterStart = $filterStartDate->copy()->subSeconds($periodDuration);
            $previousFilterEnd = $filterStartDate->copy();

            $previousQuery = DB::table('orders')
                ->whereNull('deleted_at')
                ->whereBetween('created_at', [$previousFilterStart, $previousFilterEnd]);

            // Apply branch filter to previous period as well
            if ($branchId && $branchId !== 'all') {
                $previousQuery->where('branch_id', $branchId);
            }

            $previousOrderCount = $previousQuery->count();

            $orderTrend = 0;
            if ($previousOrderCount > 0) {
                $orderTrend = (($filteredTotalOrders - $previousOrderCount) / $previousOrderCount) * 100;
            } elseif ($filteredTotalOrders > 0) {
                $orderTrend = 100;
            }

            $response['orderStatusData'] = $orderStatusData;
            $response['filteredTotalOrders'] = $filteredTotalOrders;
            $response['filteredRevenue'] = $filteredRevenue;
            $response['recentOrderCount'] = $filteredTotalOrders;
            $response['previousOrderCount'] = $previousOrderCount;
            $response['orderTrend'] = $orderTrend;
            $response['todayOrders'] = $filteredTotalOrders;
            $response['todayRevenue'] = $filteredRevenue;
        }

        return response()->json($response);
    }
}