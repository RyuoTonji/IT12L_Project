<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all tables with their status, filtered by branch
        $tables = Table::when($user->branch_id, function ($query) use ($user) {
            return $query->where('branch_id', $user->branch_id);
        })
            ->get();

        // Get active orders (new, preparing, ready), filtered by branch
        $activeOrders = Order::whereIn('status', ['new', 'preparing', 'ready'])
            ->when($user->branch_id, function ($query) use ($user) {
                return $query->where('branch_id', $user->branch_id);
            })
            ->with(['table', 'orderItems.menuItem'])
            ->latest()
            ->get();

        // Get today's completed orders for the logged-in cashier
        $completedOrders = Order::where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();

        // Get today's sales amount for the logged-in cashier
        $todaySales = Order::where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        return view('cashier.dashboard', compact(
            'tables',
            'activeOrders',
            'completedOrders',
            'todaySales'
        ));
    }

    public function kitchenDisplay()
    {
        $user = Auth::user();

        // Get orders that need to be prepared or are being prepared, filtered by branch
        $orders = Order::whereIn('status', ['new', 'preparing'])
            ->when($user->branch_id, function ($query) use ($user) {
                return $query->where('branch_id', $user->branch_id);
            })
            ->with(['orderItems.menuItem', 'table'])
            ->latest()
            ->get();

        return view('cashier.kitchen-display', compact('orders'));
    }

    public function getOrderDetails($id)
    {
        $order = Order::with(['user', 'branch', 'table', 'orderItems.menuItem', 'payments'])
            ->findOrFail($id);

        return view('cashier.orders.partials.details', compact('order'));
    }
}
