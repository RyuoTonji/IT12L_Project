<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * Display a listing of branches.
     */
    public function index()
    {
        $branches = DB::table('branches')
            ->whereNull('deleted_at')
            ->select(
                'branches.*',
                DB::raw('(SELECT COUNT(*) FROM orders WHERE orders.branch_id = branches.id AND orders.deleted_at IS NULL) as orders_count'),
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.branch_id = branches.id AND products.deleted_at IS NULL) as inventories_count'),
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.branch_id = branches.id AND products.is_available = 1 AND products.deleted_at IS NULL) as available_menu_items_count')
            )
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean'
        ]);

        DB::table('branches')->insert([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'is_active' => $request->is_active ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch created successfully!');
    }

    /**
     * Display the specified branch with operations.
     */
    public function show($id)
    {
        // Get branch details
        $branch = DB::table('branches')->where('id', $id)->first();

        if (!$branch) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found!');
        }

        // Get today's sales for this branch
        $todaySales = DB::table('orders')
            ->where('branch_id', $id)
            ->whereDate('created_at', today())
            ->whereIn('status', ['confirmed', 'picked up'])
            ->whereNull('deleted_at')
            ->sum('total_amount');

        // Get today's orders for this branch
        $todayOrders = DB::table('orders')
            ->where('branch_id', $id)
            ->whereDate('created_at', today())
            ->whereNull('deleted_at')
            ->count();

        // Get active orders for this branch - FIXED: Added user join
        $activeOrders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.branch_id', $id)
            ->whereNotIn('orders.status', ['picked up', 'cancelled'])
            ->whereNull('orders.deleted_at')
            ->select(
                'orders.*',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->orderBy('orders.created_at', 'desc')
            ->get();

        // Get recent orders for this branch
        $recentOrders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.branch_id', $id)
            ->whereNull('orders.deleted_at')
            ->select(
                'orders.*',
                'users.name as user_name'
            )
            ->orderBy('orders.created_at', 'desc')
            ->limit(10)
            ->get();

        // Get available menu items for this branch grouped by category
        $menuItemsRaw = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.branch_id', $id)
            ->where('products.is_available', 1)
            ->whereNull('products.deleted_at')
            ->select(
                'products.*',
                'categories.name as category_name'
            )
            ->orderBy('categories.name')
            ->orderBy('products.name')
            ->get();

        // Group menu items by category
        $menuItems = $menuItemsRaw->groupBy('category_name');

        return view('admin.branches.show', compact(
            'branch',
            'todaySales',
            'todayOrders',
            'activeOrders',
            'recentOrders',
            'menuItems'
        ));
    }

    public function edit($id)
    {
        $branch = DB::table('branches')->where('id', $id)->first();

        if (!$branch) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found!');
        }

        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean'
        ]);

        $branch = DB::table('branches')->where('id', $id)->first();

        if (!$branch) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found!');
        }

        DB::table('branches')->where('id', $id)->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'is_active' => $request->is_active ? 1 : 0,
            'updated_at' => now()
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully!');
    }

    public function destroy($id)
    {
        $branch = DB::table('branches')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$branch) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found!');
        }

        // Soft delete
        DB::table('branches')->where('id', $id)->update([
            'deleted_at' => now()
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch archived successfully!');
    }

    /**
     * Switch the active branch in session.
     */
    public function switchBranch(Request $request, $id)
    {
        $branch = DB::table('branches')->where('id', $id)->first();

        if (!$branch) {
            return redirect()->back()->with('error', 'Branch not found!');
        }

        $request->session()->put('active_branch_id', $id);
        
        return redirect()->back()->with('success', "Switched to {$branch->name}");
    }

    /**
     * Display archived branches.
     */
    public function archived()
    {
        $branches = DB::table('branches')
            ->whereNotNull('deleted_at')
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.branches.archived', compact('branches'));
    }

    /**
     * Restore archived branch.
     */
    public function restore($id)
    {
        $branch = DB::table('branches')
            ->where('id', $id)
            ->whereNotNull('deleted_at')
            ->first();

        if (!$branch) {
            return redirect()->route('admin.branches.archived')->with('error', 'Branch not found!');
        }

        DB::table('branches')->where('id', $id)->update([
            'deleted_at' => null,
            'updated_at' => now()
        ]);

        return redirect()->route('admin.branches.archived')->with('success', 'Branch restored successfully!');
    }
}