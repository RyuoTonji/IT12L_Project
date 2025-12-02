<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\Inventory;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Deduct stock for a menu item when an order is served.
     *
     * @param int $menuItemId
     * @param int $quantity Number of menu items ordered
     * @return bool
     */
    public function deductStock($menuItemId, $quantity)
    {
        try {
            $menuItem = MenuItem::with('ingredients.inventory')->find($menuItemId);

            if (!$menuItem) {
                Log::warning("Menu item not found: {$menuItemId}");
                return false;
            }

            // Check if menu item has ingredients defined
            if ($menuItem->ingredients->isEmpty()) {
                Log::info("Menu item {$menuItem->name} has no ingredients defined, skipping stock deduction.");
                return true; // Not an error, just no ingredients to deduct
            }

            DB::beginTransaction();

            foreach ($menuItem->ingredients as $ingredient) {
                $inventory = $ingredient->inventory;
                $quantityNeeded = $ingredient->quantity_needed * $quantity;

                // Deduct from inventory
                $newQuantity = $inventory->quantity - $quantityNeeded;

                // Update inventory
                $inventory->update([
                    'quantity' => max(0, $newQuantity), // Prevent negative inventory
                    'stock_out' => ($inventory->stock_out ?? 0) + $quantityNeeded,
                ]);

                Log::info("Deducted {$quantityNeeded} {$inventory->unit} of {$inventory->name} for menu item {$menuItem->name}");
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deducting stock for menu item {$menuItemId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deduct stock for an entire order.
     *
     * @param Order $order
     * @return bool
     */
    public function deductStockForOrder(Order $order)
    {
        try {
            $order->load('items.menuItem');

            foreach ($order->items as $orderItem) {
                $this->deductStock($orderItem->menu_item_id, $orderItem->quantity);
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Error deducting stock for order {$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate total stock-out for an inventory item in a date range.
     *
     * @param int $inventoryId
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public function calculateStockOut($inventoryId, $startDate, $endDate)
    {
        $inventory = Inventory::find($inventoryId);

        if (!$inventory) {
            return 0;
        }

        // Get all menu items that use this inventory item
        $menuItems = $inventory->menuItems()->with('orderItems')->get();

        $totalStockOut = 0;

        foreach ($menuItems as $menuItem) {
            // Get order items for this menu item in the date range
            $orderItems = $menuItem->orderItems()
                ->whereHas('order', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                        ->whereIn('status', ['served', 'completed']);
                })
                ->get();

            foreach ($orderItems as $orderItem) {
                $ingredient = $menuItem->ingredients()
                    ->where('inventory_id', $inventoryId)
                    ->first();

                if ($ingredient) {
                    $totalStockOut += $ingredient->quantity_needed * $orderItem->quantity;
                }
            }
        }

        return $totalStockOut;
    }

    /**
     * Check if there's enough stock for a menu item.
     *
     * @param int $menuItemId
     * @param int $quantity
     * @return array ['available' => bool, 'insufficient' => array of inventory names]
     */
    public function checkStockAvailability($menuItemId, $quantity)
    {
        $menuItem = MenuItem::with('ingredients.inventory')->find($menuItemId);

        if (!$menuItem || $menuItem->ingredients->isEmpty()) {
            return ['available' => true, 'insufficient' => []];
        }

        $insufficient = [];

        foreach ($menuItem->ingredients as $ingredient) {
            $inventory = $ingredient->inventory;
            $required = $ingredient->quantity_needed * $quantity;

            if ($inventory->quantity < $required) {
                $insufficient[] = $inventory->name;
            }
        }

        return [
            'available' => empty($insufficient),
            'insufficient' => $insufficient
        ];
    }
}
