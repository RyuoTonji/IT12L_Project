<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
<<<<<<< Updated upstream
        $branches = DB::table('branches')
=======
        $branches = Branch::withCount([
            'orders' => function ($query) {
                $query->whereNull('deleted_at');
            },
            'products' => function ($query) {
                $query->whereNull('deleted_at');
            },
            'products as available_menu_items_count' => function ($query) {
                $query->where('is_available', 1)->whereNull('deleted_at');
            }
        ])
>>>>>>> Stashed changes
            ->orderBy('id', 'desc')
            ->paginate(20);

        // Rename withCount attributes for view compatibility if needed, 
        // but index view uses $branch->orders_count, $branch->available_menu_items_count, $branch->inventories_count
        // we can alias in withCount or just rename them in the collection if needed.
        // Actually inventories_count is products_count in our withCount.

        $branches->getCollection()->transform(function ($branch) {
            $branch->inventories_count = $branch->products_count;
            return $branch;
        });

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
            'is_active' => 'boolean'
        ]);

        Branch::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
<<<<<<< Updated upstream
            'is_active' => $request->has('is_active') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now()
=======
            'is_active' => $request->is_active ? 1 : 0
>>>>>>> Stashed changes
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch created successfully!');
    }

<<<<<<< Updated upstream
=======
    /**
     * Display the specified branch with operations.
     */
    public function show($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found!');
        }

        // Get today's sales for this branch
        $todaySales = Order::where('branch_id', $id)
            ->whereDate('created_at', today())
            ->whereIn('status', ['confirmed', 'picked up'])
            ->sum('total_amount');

        // Get today's orders for this branch
        $todayOrders = Order::where('branch_id', $id)
            ->whereDate('created_at', today())
            ->count();

        // Get active orders for this branch
        $activeOrders = Order::with('user')
            ->where('branch_id', $id)
            ->whereNotIn('status', ['picked up', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get recent orders for this branch
        $recentOrders = Order::with('user')
            ->where('branch_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get available menu items for this branch grouped by category
        $menuItemsRaw = Product::with('category')
            ->where('branch_id', $id)
            ->where('is_available', 1)
            ->orderBy('category_id') // We'll group by category name below
            ->orderBy('name')
            ->get();

        // Group menu items by category name
        $menuItems = $menuItemsRaw->groupBy(function ($item) {
            return $item->category->name;
        });

        return view('admin.branches.show', compact(
            'branch',
            'todaySales',
            'todayOrders',
            'activeOrders',
            'recentOrders',
            'menuItems'
        ));
    }

>>>>>>> Stashed changes
    public function edit($id)
    {
        $branch = Branch::find($id);

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
            'is_active' => 'boolean'
        ]);

        $branch = Branch::find($id);

        if (!$branch) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found!');
        }

        $branch->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
<<<<<<< Updated upstream
            'is_active' => $request->has('is_active') ? 1 : 0,
            'updated_at' => now()
=======
            'is_active' => $request->is_active ? 1 : 0
>>>>>>> Stashed changes
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully!');
    }

    public function destroy($id)
    {
<<<<<<< Updated upstream
        $branch = DB::table('branches')->where('id', $id)->first();
=======
        $branch = Branch::find($id);
>>>>>>> Stashed changes

        if (!$branch) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found!');
        }

<<<<<<< Updated upstream
        // Check if branch has products
        $productCount = DB::table('products')->where('branch_id', $id)->count();
        if ($productCount > 0) {
            return redirect()->route('admin.branches.index')->with('error', 'Cannot delete branch with existing products!');
        }

        DB::table('branches')->where('id', $id)->delete();

        return redirect()->route('admin.branches.index')->with('success', 'Branch deleted successfully!');
=======
        $branch->delete();

        return redirect()->route('admin.branches.index')->with('success', 'Branch archived successfully!');
    }

    /**
     * Switch the active branch in session.
     */
    public function switchBranch(Request $request, $id)
    {
        $branch = Branch::find($id);

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
        $branches = Branch::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.branches.archived', compact('branches'));
    }

    /**
     * Restore archived branch.
     */
    public function restore($id)
    {
        $branch = Branch::onlyTrashed()->find($id);

        if (!$branch) {
            return redirect()->route('admin.branches.archived')->with('error', 'Branch not found!');
        }

        $branch->restore();

        return redirect()->route('admin.branches.archived')->with('success', 'Branch restored successfully!');
>>>>>>> Stashed changes
    }
}