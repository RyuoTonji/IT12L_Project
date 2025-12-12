<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::orderBy('category')->orderBy('name')->get();

        // Group inventory items by category for the view
        $inventoriesByCategory = $inventories->groupBy('category');

        return view('inventory.dashboard', compact('inventories', 'inventoriesByCategory'));
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'reorder_level' => 'required|numeric|min:0',
        ]);

        $inventory = Inventory::create($request->all());

        // Automated Activity Logging
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'add_stock',
            'details' => "Added stock: {$inventory->name} ({$inventory->quantity} {$inventory->unit})",
            'status' => 'info',
            'related_id' => $inventory->id,
            'related_model' => Inventory::class,
        ]);

        return redirect()->route('inventory.dashboard')->with('success', 'Stock added successfully.');
    }

    public function updateStock(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);

        $oldQuantity = $inventory->quantity;
        $inventory->quantity += $request->quantity;
        $inventory->save();

        // Automated Activity Logging
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'update_stock',
            'details' => "Updated stock for {$inventory->name}: Added {$request->quantity} {$inventory->unit}. New Total: {$inventory->quantity}",
            'status' => 'info',
            'related_id' => $inventory->id,
            'related_model' => Inventory::class,
        ]);

        return redirect()->route('inventory.dashboard')->with('success', 'Stock updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        $name = $inventory->name;
        $inventory->delete(); // Soft delete

        // Automated Activity Logging
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'archive_stock',
            'details' => "Archived stock: {$name}",
            'status' => 'info',
            'related_id' => $inventory->id,
            'related_model' => Inventory::class,
        ]);

        return redirect()->route('inventory.dashboard')->with('success', 'Stock archived successfully.');
    }
}
