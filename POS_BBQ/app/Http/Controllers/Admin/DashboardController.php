<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\MenuItem;
// use App\Models\User;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterDate = $request->input('date');

        if ($filterDate) {
            // If date selected, range is that specific day
            $start = Carbon::parse($filterDate)->startOfDay();
            $end = Carbon::parse($filterDate)->endOfDay();
        } else {
            // Default range for charts/lists is last 7 days
            $end = now()->endOfDay();
            $start = now()->subDays(6)->startOfDay();
        }

        // --- 1. SINGLE DAY STATS (Uses $end - either Selected Date or Today) ---
        // Used for the "Today's Sales" type cards

        $targetDate = $end; // Alias for clarity

        // Total sales for Target Date
        $todaySales = Order::whereDate('created_at', $targetDate)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Total orders for Target Date
        $todayOrders = Order::whereDate('created_at', $targetDate)->count();

        // Get ALL TIME total sales
        $totalSales = Order::where('payment_status', 'paid')->sum('total_amount');

        // Get ALL TIME total orders
        $totalOrders = Order::count();

        // Get ALL TIME Total Lost (Spoilage + Damaged)
        $totalSpoilageAllTime = InventoryAdjustment::where('adjustment_type', 'spoilage')->sum('quantity');
        $totalLostAllTime = InventoryAdjustment::where('adjustment_type', 'damaged')->sum('quantity');
        $totalLostCombined = $totalSpoilageAllTime + $totalLostAllTime;

        // Get active orders (Current State - unaffected by date filter usually)
        $activeOrders = Order::with(['user', 'branch', 'table', 'orderItems.menuItem', 'payments'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get();

        // Get active orders count
        $activeOrdersCount = $activeOrders->count();

        // Branch 1 Stats (Target Date)
        $branch1Sales = Order::where('branch_id', 1)
            ->whereDate('created_at', $targetDate)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $branch1Orders = Order::where('branch_id', 1)
            ->whereDate('created_at', $targetDate)
            ->count();

        $branch1ActiveOrders = Order::where('branch_id', 1)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        $branch1MenuCount = MenuItem::whereHas('branches', function ($q) {
            $q->where('branches.id', 1)->where('is_available', true);
        })->count();

        $branch1LowStock = Inventory::where('branch_id', 1)
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->count();

        // Branch 2 Stats (Target Date)
        $branch2Sales = Order::where('branch_id', 2)
            ->whereDate('created_at', $targetDate)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $branch2Orders = Order::where('branch_id', 2)
            ->whereDate('created_at', $targetDate)
            ->count();

        $branch2ActiveOrders = Order::where('branch_id', 2)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        $branch2MenuCount = MenuItem::whereHas('branches', function ($q) {
            $q->where('branches.id', 2)->where('is_available', true);
        })->count();

        $branch2LowStock = Inventory::where('branch_id', 2)
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->count();

        // --- 1b. ALL TIME Breakdown (For Combined Analytics Modals) ---
        $branch1SalesAllTime = Order::where('branch_id', 1)->where('payment_status', 'paid')->sum('total_amount');
        $branch2SalesAllTime = Order::where('branch_id', 2)->where('payment_status', 'paid')->sum('total_amount');

        $branch1OrdersAllTime = Order::where('branch_id', 1)->count();
        $branch2OrdersAllTime = Order::where('branch_id', 2)->count();

        $branch1LostAllTime = InventoryAdjustment::whereHas('inventory', function ($q) {
            $q->where('branch_id', 1);
        })
            ->whereIn('adjustment_type', ['spoilage', 'damaged'])
            ->sum('quantity');

        $branch2LostAllTime = InventoryAdjustment::whereHas('inventory', function ($q) {
            $q->where('branch_id', 2);
        })
            ->whereIn('adjustment_type', ['spoilage', 'damaged'])
            ->sum('quantity');

        // --- 2. RANGE STATS (Uses $start to $end) ---
        // Used for Charts, Top Selling, New Stock (Trends)

        // General Stats
        $menuItemsCount = MenuItem::count();

        // New Stock (Filtered by Range)
        $newStockItems = Inventory::with(['branch'])
            ->whereBetween('updated_at', [$start, $end])
            ->get();

        // Low Stock (Current Status - Snapshot)
        $lowStockItems = Inventory::with(['branch'])
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->get();

        // Top Selling Items (Filtered by Range)
        $topSellingItems = OrderItem::select('menu_item_id', DB::raw('sum(quantity) as total_quantity'))
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                    ->where('payment_status', 'paid');
            })
            ->with('menuItem')
            ->groupBy('menu_item_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get()
            ->map(function ($item) use ($start, $end) {
                // Get breakdown per branch (Filtered by Range)
                $breakdown = OrderItem::where('menu_item_id', $item->menu_item_id)
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('branches', 'orders.branch_id', '=', 'branches.id')
                    ->whereBetween('orders.created_at', [$start, $end])
                    ->where('orders.payment_status', 'paid')
                    ->select('branches.name as branch_name', DB::raw('sum(order_items.quantity) as quantity'))
                    ->groupBy('branches.name')
                    ->get();

                return (object) [
                    'id' => $item->menu_item_id,
                    'name' => $item->menuItem->name ?? 'Unknown Item',
                    'total_quantity' => $item->total_quantity,
                    'branch_breakdown' => $breakdown
                ];
            });

        // Recent Orders (Filtered by Range)
        $recentOrders = Order::with(['user', 'branch', 'table', 'orderItems.menuItem', 'payments'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->take(10)
            ->get();

        // --- NEW METRICS: Spoilage, Lost, Returns, Refunds ---

        // 1. Spoilage
        $spoilageQuery = InventoryAdjustment::where('adjustment_type', 'spoilage')
            ->whereBetween('updated_at', [$start, $end]);
        $spoilageCount = $spoilageQuery->sum('quantity');
        $spoilageCost = 0; // No cost field
        $spoilageItems = $spoilageQuery->with(['inventory.branch', 'inventory'])->get()->sortByDesc('updated_at');

        // 2. Lost (Damaged)
        $lostQuery = InventoryAdjustment::where('adjustment_type', 'damaged')
            ->whereBetween('updated_at', [$start, $end]);
        $lostCount = $lostQuery->sum('quantity');
        $lostCost = 0; // No cost field
        $lostItems = $lostQuery->with(['inventory.branch', 'inventory'])->get()->sortByDesc('updated_at');

        // 3. Returns (Inventory Returns)
        $returnsQuery = InventoryAdjustment::where('adjustment_type', 'return')
            ->whereBetween('updated_at', [$start, $end]);
        $returnsCount = $returnsQuery->sum('quantity');
        $returnsCost = 0; // No cost field
        $returnItems = $returnsQuery->with(['inventory.branch', 'inventory'])->get()->sortByDesc('updated_at');

        // 4. Refunds (Orders)
        $refundsQuery = Order::where('payment_status', 'refunded')
            ->whereBetween('updated_at', [$start, $end]);
        $refundsCount = $refundsQuery->count();
        $refundsCost = $refundsQuery->sum('total_amount');
        $refundItems = $refundsQuery->with('branch')->get()->sortByDesc('updated_at');


        // --- Aggregations for Charts in Modals ---
        $lowStockByBranch = $lowStockItems->groupBy('branch.name')->map->count();
        $newStockByBranch = $newStockItems->groupBy('branch.name')->map->count();

        $spoilageByBranch = $spoilageItems->groupBy(function ($item) {
            return $item->inventory->branch->name ?? 'Unknown';
        })->map->sum('quantity');

        $lostByBranch = $lostItems->groupBy(function ($item) {
            return $item->inventory->branch->name ?? 'Unknown';
        })->map->sum('quantity');

        $returnsByBranch = $returnItems->groupBy(function ($item) {
            return $item->inventory->branch->name ?? 'Unknown';
        })->map->sum('quantity');

        $refundsByBranch = $refundItems->groupBy(function ($item) {
            return $item->branch->name ?? 'Unknown';
        })->map->sum('total_amount'); // Chart by Amount for Refunds

        $menuItemsByCategory = Category::withCount('menuItems')->get()->map(function ($cat) {
            return ['name' => $cat->name, 'count' => $cat->menu_items_count];
        });


        // Prepare chart data (Main Charts)
        $dates = collect();
        $currentDate = $start->copy();

        while ($currentDate <= $end) {
            $dates->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        // Sales chart data
        $salesChartData = $dates->map(function ($date) {
            $branch1 = Order::where('branch_id', 1)
                ->whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            $branch2 = Order::where('branch_id', 2)
                ->whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            return [
                'date' => Carbon::parse($date)->format('M d'),
                'branch1' => (float) $branch1,
                'branch2' => (float) $branch2,
            ];
        });

        // Orders chart data
        $ordersChartData = $dates->map(function ($date) {
            $branch1 = Order::where('branch_id', 1)->whereDate('created_at', $date)->count();
            $branch2 = Order::where('branch_id', 2)->whereDate('created_at', $date)->count();
            return [
                'date' => Carbon::parse($date)->format('M d'),
                'branch1' => $branch1,
                'branch2' => $branch2,
            ];
        });

        // Inventory chart data (aggregated for the selected range)
        $inventoryChartData = [
            'branch1' => [
                'stock_in' => Inventory::where('branch_id', 1)
                    ->whereBetween('updated_at', [$start, $end])
                    ->sum('stock_in'),
                'stock_out' => Inventory::where('branch_id', 1)
                    ->whereBetween('updated_at', [$start, $end])
                    ->sum('stock_out'),
                'spoilage' => Inventory::where('branch_id', 1)
                    ->whereBetween('updated_at', [$start, $end])
                    ->sum('spoilage'),
            ],
            'branch2' => [
                'stock_in' => Inventory::where('branch_id', 2)
                    ->whereBetween('updated_at', [$start, $end])
                    ->sum('stock_in'),
                'stock_out' => Inventory::where('branch_id', 2)
                    ->whereBetween('updated_at', [$start, $end])
                    ->sum('stock_out'),
                'spoilage' => Inventory::where('branch_id', 2)
                    ->whereBetween('updated_at', [$start, $end])
                    ->sum('spoilage'),
            ],
        ];

        return view('admin.dashboard', compact(
            'totalSales',
            'totalOrders',
            'todaySales',
            'todayOrders',
            'activeOrders',
            'activeOrdersCount',
            'branch1Sales',
            'branch1Orders',
            'branch1ActiveOrders',
            'branch1MenuCount',
            'branch1LowStock',
            'branch2Sales',
            'branch2Orders',
            'branch2ActiveOrders',
            'branch2MenuCount',
            'branch2LowStock',
            'menuItemsCount',
            'newStockItems',
            'totalLostCombined',
            'lowStockItems',
            'topSellingItems',
            'recentOrders',
            'salesChartData',
            'ordersChartData',
            'inventoryChartData',
            'filterDate',
            'menuItemsByCategory',
            'lowStockByBranch',
            'newStockByBranch',
            'spoilageCount',
            'spoilageCost',
            'spoilageItems',
            'spoilageByBranch',
            'lostCount',
            'lostCost',
            'lostItems',
            'lostByBranch',
            'returnsCount',
            'returnsCost',
            'returnItems',
            'returnsByBranch',
            'refundsCount',
            'refundsCost',
            'refundItems',
            'refundsByBranch',
            'branch1SalesAllTime',
            'branch2SalesAllTime',
            'branch1OrdersAllTime',
            'branch2OrdersAllTime',
            'branch1LostAllTime',
            'branch2LostAllTime'
        ));
    }

    public function getOrderDetails($id)
    {
        $order = Order::with(['user', 'branch', 'table', 'orderItems.menuItem', 'payments', 'voidRequests.approver', 'voidRequests.requester'])
            ->findOrFail($id);

        return view('admin.orders.partials.details', compact('order'));
    }
}
