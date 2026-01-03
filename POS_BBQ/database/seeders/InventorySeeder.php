<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use Faker\Factory as Faker;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: Most inventory items are now created by MenuItemSeeder
        // This seeder is kept minimal to avoid conflicts with the strict inventory system

        $this->command->info('InventorySeeder: Inventory items are now created by MenuItemSeeder.');
        $this->command->info('If you need additional test inventory items, add them here.');

        // Optionally, you can add non-menu-linked inventory items here for testing
        // Example:
        // Inventory::create([
        //     'name' => 'Test Supply Item',
        //     'category' => 'Supply',
        //     'quantity' => 50,
        //     'unit' => 'pcs',
        //     'sold' => 0,
        //     'spoilage' => 0,
        //     'stock_in' => 50,
        //     'stock_out' => 0,
        //     'reorder_level' => 10,
        // ]);
    }
}
