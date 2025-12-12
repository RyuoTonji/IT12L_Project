<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::query();

        if ($request->filled('supplier')) {
            $query->where('supplier', 'like', '%' . $request->supplier . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $inventoryItems = $query->orderBy('category')->orderBy('name')->get();
        $suppliers = Inventory::select('supplier')->distinct()->whereNotNull('supplier')->pluck('supplier');
        $categories = Inventory::select('category')->distinct()->whereNotNull('category')->pluck('category');

        // Group inventory items by category
        $inventoryByCategory = $inventoryItems->groupBy('category');

        return view('admin.inventory.index', compact('inventoryItems', 'suppliers', 'categories', 'inventoryByCategory'));
    }

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'reorder_level' => 'required|numeric|min:0',
        ]);

        Inventory::create($request->all());

        return redirect()->route('inventory.index')->with('success', 'Inventory item created successfully');
    }

    public function show(Inventory $inventory)
    {
        return view('admin.inventory.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        return view('admin.inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'reorder_level' => 'required|numeric|min:0',
        ]);

        $inventory->update($request->all());

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully');
    }
}
