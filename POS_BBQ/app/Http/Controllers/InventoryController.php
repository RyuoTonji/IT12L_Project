<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Activity;
use App\Models\InventoryAdjustment;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function report(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));

        // Stock In (Ingredients Added)
        $stockIns = InventoryAdjustment::with(['inventory', 'recorder'])
            ->where('adjustment_type', 'stock_in')
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        // Prepared Dishes (Sales)
        $preparedDishes = OrderItem::with('menuItem')
            ->whereHas('order', function ($query) use ($date) {
                $query->whereDate('created_at', $date)
                    ->whereNotIn('status', ['cancelled']);
            })
            ->select('menu_item_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(unit_price * quantity) as total_amount'))
            ->groupBy('menu_item_id')
            ->get();

        $inventoryItems = Inventory::orderBy('name')->get(); // For manual stock in dropdown

        return view('inventory.report', compact('stockIns', 'preparedDishes', 'date', 'inventoryItems'));
    }

    public function stockIn(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string',
            'reason' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $inventory = Inventory::findOrFail($request->inventory_id);

            // Create Adjustment Record
            InventoryAdjustment::create([
                'inventory_id' => $inventory->id,
                'adjustment_type' => 'stock_in',
                'quantity' => $request->quantity,
                'reason' => $request->reason,
                'recorded_by' => Auth::id(),
            ]);

            // Update Inventory
            $inventory->increment('quantity', $request->quantity);
            $inventory->increment('stock_in', $request->quantity);
        });

        return redirect()->route('inventory.report')->with('success', 'Stock added successfully.');
    }
}
