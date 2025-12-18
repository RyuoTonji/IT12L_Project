<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
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

        return view('admin.inventory.report', compact('stockIns', 'preparedDishes', 'date', 'inventoryItems'));
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

        return redirect()->route('admin.inventory.report')->with('success', 'Stock added successfully.');
    }

    public function index(Request $request)
    {
        $query = Inventory::query();

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $inventoryItems = $query->orderBy('category')->orderBy('name')->get();
        // $suppliers = Inventory::select('supplier')->distinct()->whereNotNull('supplier')->pluck('supplier'); // No longer needed for dropdown
        // $categories = Inventory::select('category')->distinct()->whereNotNull('category')->pluck('category'); // No longer needed for dropdown

        // Group inventory items by category
        $inventoryByCategory = $inventoryItems->groupBy('category');

        return view('admin.inventory.index', compact('inventoryItems', 'inventoryByCategory'));
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
        ]);

        DB::transaction(function () use ($request) {
            $inventory = Inventory::create([
                'name' => $request->name,
                'supplier' => $request->supplier,
                'category' => $request->category,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'stock_in' => $request->quantity, // Initialize stock_in
                'stock_out' => 0,
            ]);

            // Create Adjustment Record
            InventoryAdjustment::create([
                'inventory_id' => $inventory->id,
                'adjustment_type' => 'stock_in',
                'quantity' => $request->quantity,
                'quantity_before' => 0,
                'quantity_after' => $request->quantity,
                'reason' => 'Initial Stock (Admin Created)',
                'recorded_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item created successfully');
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
        ]);

        DB::transaction(function () use ($request, $inventory) {
            $oldQuantity = $inventory->quantity;
            $newQuantity = $request->quantity;
            
            // Update details
            $inventory->update([
                'name' => $request->name,
                'supplier' => $request->supplier,
                'category' => $request->category,
                'unit' => $request->unit,
                // We update quantity via update call, but need to track if it changed for adjustment
                'quantity' => $newQuantity
            ]);

            // If quantity changed, log it
            if ($oldQuantity != $newQuantity) {
                $diff = $newQuantity - $oldQuantity;
                $type = $diff > 0 ? 'stock_in' : 'stock_out'; // Or 'adjustment'
                
                // If it's positive, we might count it as stock_in. If negative, maybe not 'stock_out' (sales), but manual adjustment.
                // However, for simplicity and reporting, positive manual changes usually mean adding stock.
                if ($diff > 0) {
                    $inventory->increment('stock_in', $diff);
                }
                
                InventoryAdjustment::create([
                    'inventory_id' => $inventory->id,
                    'adjustment_type' => $diff > 0 ? 'stock_in' : 'adjustment', // 'stock_out' usually reserved for sales? Let's use 'adjustment' for negative manual fix if needed or just keep generic
                    'quantity' => abs($diff),
                    'quantity_before' => $oldQuantity,
                    'quantity_after' => $newQuantity,
                    'reason' => 'Manual Update (Admin)',
                    'recorded_by' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item updated successfully');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item deleted successfully'); // Corrected route name
    }
}
