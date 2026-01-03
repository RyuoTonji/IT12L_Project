<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Table;
use App\Models\Order;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $tables = Table::when($user->branch_id, function ($query) use ($user) {
            return $query->where('branch_id', $user->branch_id);
        })
            ->get();

        return view('cashier.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('cashier.tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tables,name',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved',
        ]);

        $table = new Table();
        $table->name = $request->name;
        $table->capacity = $request->capacity;
        $table->status = $request->status;
        $table->branch_id = auth()->user()->branch_id; // Set branch from user
        $table->save();

        return redirect()->route('tables.index')->with('success', 'Table created successfully');
    }

    public function show(Table $table)
    {
        // Check if user can access this table
        $user = auth()->user();
        if ($user->branch_id && $table->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to this table.');
        }

        // Get active order for this table if any
        $activeOrder = Order::where('table_id', $table->id)
            ->whereIn('status', ['new', 'preparing', 'ready', 'served'])
            ->with(['orderItems.menuItem'])
            ->first();

        // Get menu categories and items for creating new orders
        $categories = Category::with([
            'menuItems' => function ($query) {
                $query->where('availability', true);
            }
        ])->get();

        return view('cashier.tables.show', compact('table', 'activeOrder', 'categories'));
    }

    public function edit(Table $table)
    {
        return view('cashier.tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tables,name,' . $table->id,
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved',
        ]);

        $table->update($request->all());

        return redirect()->route('tables.index')->with('success', 'Table updated successfully');
    }

    public function destroy(Table $table)
    {
        $table->delete();

        return redirect()->route('tables.index')->with('success', 'Table archived successfully');
    }
}
