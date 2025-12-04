<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * Display a listing of branches.
     */
    public function index()
    {
        $branches = Branch::withCount(['orders', 'inventories', 'availableMenuItems'])
            ->with([
                'orders' => function ($query) {
                    $query->whereDate('created_at', today())
                        ->where('payment_status', 'paid');
                }
            ])
            ->get();

        return view('admin.branches.index', compact('branches'));
    }

    /**
     * Display the specified branch with operations.
     */
    public function show(Branch $branch)
    {
        // Get today's sales for this branch
        $todaySales = Order::where('branch_id', $branch->id)
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Get today's orders for this branch
        $todayOrders = Order::where('branch_id', $branch->id)
            ->whereDate('created_at', today())
            ->count();

        // Get active orders for this branch
        $activeOrders = Order::where('branch_id', $branch->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with(['orderItems.menuItem', 'table'])
            ->get();

        // Get recent orders for this branch
        $recentOrders = Order::where('branch_id', $branch->id)
            ->with(['user', 'table', 'orderItems.menuItem'])
            ->latest()
            ->limit(10)
            ->get();

        // Get available menu items for this branch
        $menuItems = $branch->availableMenuItems()
            ->with('category')
            ->get()
            ->groupBy('category.name');

        return view('admin.branches.show', compact(
            'branch',
            'todaySales',
            'todayOrders',
            'activeOrders',
            'recentOrders',
            'menuItems'
        ));
    }

    /**
     * Switch the active branch in session.
     */
    public function switchBranch(Request $request, Branch $branch)
    {
        $request->session()->put('active_branch_id', $branch->id);

        return redirect()->back()->with('success', "Switched to {$branch->name}");
    }
}
