<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryAdjustmentController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'adjustment_type' => 'required|in:stock_in,return,spoilage,damaged,other',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        $result = $this->inventoryService->adjustStock(
            $request->inventory_id,
            $request->quantity,
            $request->adjustment_type,
            $request->reason,
            Auth::id()
        );

        if ($result) {
            return back()->with('success', 'Inventory adjustment recorded successfully.');
        }

        return back()->with('error', 'Failed to record adjustment.');
    }
}
