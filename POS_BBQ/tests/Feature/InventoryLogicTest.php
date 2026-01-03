<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryLogicTest extends TestCase
{
    // use RefreshDatabase; // Commented out to avoid wiping user's DB if not configured correctly, will filter by unique names

    public function test_admin_create_inventory_creates_adjustment()
    {
        $admin = User::find(1); // Assuming ID 1 is admin, or I will mock login
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_test@example.com']);
        }

        $response = $this->actingAs($admin)->post(route('admin.inventory.store'), [
            'name' => 'AutoTest Item ' . uniqid(),
            'quantity' => 10,
            'unit' => 'kg',
            'supplier' => 'Test Supplier',
            'category' => 'Test Category'
        ]);

        $response->assertRedirect(route('admin.inventory.index'));
        $response->assertSessionHas('success');

        $item = Inventory::where('name', 'like', 'AutoTest Item%')->latest()->first();
        $this->assertNotNull($item);
        $this->assertEquals(10, $item->quantity);
        $this->assertEquals(10, $item->stock_in);

        // Check Adjustment
        $adjustment = InventoryAdjustment::where('inventory_id', $item->id)->first();
        $this->assertNotNull($adjustment);
        $this->assertEquals(10, $adjustment->quantity);
        $this->assertEquals('stock_in', $adjustment->adjustment_type);
    }

    public function test_admin_update_quantity_creates_adjustment()
    {
        $admin = User::find(1);
         if (!$admin) {
            $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_test2@example.com']);
        }

        // Create initial item
        $item = Inventory::create([
            'name' => 'UpdateTest Item ' . uniqid(),
            'quantity' => 10,
            'unit' => 'kg',
            'stock_in' => 10,
            'stock_out' => 0
        ]);

        // Update quantity to 15
        $response = $this->actingAs($admin)->put(route('admin.inventory.update', $item), [
            'name' => $item->name,
            'quantity' => 15,
            'unit' => 'kg',
            'supplier' => 'New Supplier'
        ]);

        $response->assertRedirect(route('admin.inventory.index'));

        // Verify Changes
        $item->refresh();
        $this->assertEquals(15, $item->quantity);
        $this->assertEquals(15, $item->stock_in); // 10 initial + 5 added

        // Check Adjustment
        $adjustment = InventoryAdjustment::where('inventory_id', $item->id)
            ->where('reason', 'Manual Update (Admin)')
            ->first();
        
        $this->assertNotNull($adjustment);
        $this->assertEquals(5, $adjustment->quantity);
        $this->assertEquals('stock_in', $adjustment->adjustment_type);
    }
}
