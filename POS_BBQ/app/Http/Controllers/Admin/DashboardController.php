<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\MenuItem;
// use App\Models\User;
use App\Models\Inventory;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterDate = $request->input('date');

        if ($filterDate) {
            $start = \Carbon\Carbon::parse($filterDate)->startOfDay();
            $end = \Carbon\Carbon::parse($filterDate)->endOfDay();
        } else {
            $end = now()->endOfDay();
            $start = now()->subDays(6)->startOfDay();
        }
        // Get total sales for today
        $todaySales = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Get total orders for today
        $todayOrders = Order::whereDate('created_at', today())->count();

        // Get ALL TIME total sales
        $totalSales = Order::where('payment_status', 'paid')->sum('total_amount');

        // Get ALL TIME total orders
        $totalOrders = Order::count();

        // Get active orders collection for modals
        $activeOrders = Order::with(['user', 'branch', 'table', 'orderItems.menuItem', 'payments'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get();

        // Get active orders count for display
        $activeOrdersCount = $activeOrders->count();

        // Branch 1 Stats
        $branch1Sales = Order::where('branch_id', 1)
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $branch1Orders = Order::where('branch_id', 1)
            ->whereDate('created_at', today())
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

        // Branch 2 Stats
        $branch2Sales = Order::where('branch_id', 2)
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $branch2Orders = Order::where('branch_id', 2)
            ->whereDate('created_at', today())
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

        // General Stats
        $menuItemsCount = MenuItem::count();
        $newStockItems = Inventory::with('branch')
            ->where('created_at', '>=', now()->subDays(7))
            ->get();
        $lowStockItems = Inventory::with('branch')
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->get();

        $topSellingItems = OrderItem::select('menu_item_id', DB::raw('sum(quantity) as total_quantity'))
            ->with('menuItem')
            ->groupBy('menu_item_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->menuItem->name ?? 'Unknown Item',
                    'total_quantity' => $item->total_quantity,
                ];
            });

        $recentOrders = Order::with(['user', 'branch', 'table', 'orderItems.menuItem', 'payments'])->latest()->take(5)->get();

        // Prepare chart data
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
                'date' => \Carbon\Carbon::parse($date)->format('M d'),
                'branch1' => (float) $branch1,
                'branch2' => (float) $branch2,
            ];
        });

        // Orders chart data
        $ordersChartData = $dates->map(function ($date) {
            $branch1 = Order::where('branch_id', 1)->whereDate('created_at', $date)->count();
            $branch2 = Order::where('branch_id', 2)->whereDate('created_at', $date)->count();
            return [
                'date' => \Carbon\Carbon::parse($date)->format('M d'),
                'branch1' => $branch1,
                'branch2' => $branch2,
            ];
        });

        // Inventory chart data (aggregated for the selected range)
        $inventoryChartData = [
            'branch1' => [
                'stock_in' => Inventory::where('branch_id', 1)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('stock_in'),
                'stock_out' => Inventory::where('branch_id', 1)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('stock_out'),
                'spoilage' => Inventory::where('branch_id', 1)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('spoilage'),
            ],
            'branch2' => [
                'stock_in' => Inventory::where('branch_id', 2)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('stock_in'),
                'stock_out' => Inventory::where('branch_id', 2)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('stock_out'),
                'spoilage' => Inventory::where('branch_id', 2)
                    ->whereBetween('created_at', [$start, $end])
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
            'lowStockItems',
            'topSellingItems',
            'recentOrders',
            'salesChartData',
            'ordersChartData',
            'inventoryChartData',
            'filterDate'
        ));
    }

    public function getOrderDetails($id)
    {
        $order = Order::with(['user', 'branch', 'table', 'orderItems.menuItem', 'payments'])
            ->findOrFail($id);

        return view('admin.orders.partials.details', compact('order'));
    }
}
