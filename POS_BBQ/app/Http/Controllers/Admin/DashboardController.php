<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\MenuItem;
// use App\Models\User;
use App\Models\Inventory;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total sales for today
        $todaySales = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Get total orders for today
        $todayOrders = Order::whereDate('created_at', today())->count();

        // Get active orders (not completed or cancelled)
        $activeOrders = Order::whereNotIn('status', ['completed', 'cancelled'])->count();

        // Get top selling items
        $topSellingItems = MenuItem::select('menu_items.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Get available menu items count
        $menuItemsCount = MenuItem::where('is_available', true)->count();

        // Get new stock items (added in last 7 days)
        $newStockItems = Inventory::where('created_at', '>=', now()->subDays(7))->get();

        // Get recent orders
        $recentOrders = Order::with(['user', 'table'])
            ->latest()
            ->limit(5)
            ->get();

        // Get low stock inventory items
        $lowStockItems = Inventory::whereRaw('quantity <= reorder_level')
            ->get();

        return view('admin.dashboard', compact(
            'todaySales',
            'todayOrders',
            'activeOrders',
            'topSellingItems',
            'recentOrders',
            'lowStockItems',
            'menuItemsCount',
            'newStockItems'
        ));
    }
}
