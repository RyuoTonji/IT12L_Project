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
        $faker = Faker::create();
        $categories = ['Meat', 'Vegetable', 'Condiment', 'Beverage', 'Supply'];
        $units = ['kg', 'g', 'pcs', 'liters', 'bottles'];

        // Seed for both branches
        foreach ([1, 2] as $branchId) {
            // 1. Create 20 "Low Stock" items (Old stock, Quantity <= Reorder Level)
            for ($i = 0; $i < 20; $i++) {
                $reorderLevel = $faker->numberBetween(10, 50);
                Inventory::create([
                    'name' => 'Low Stock ' . $faker->word,
                    'supplier' => $faker->company,
                    'category' => $faker->randomElement($categories),
                    'quantity' => $faker->numberBetween(0, $reorderLevel), // Quantity <= Reorder Level
                    'sold' => $faker->numberBetween(10, 50),
                    'spoilage' => $faker->numberBetween(0, 5),
                    'stock_in' => $faker->numberBetween(50, 100),
                    'stock_out' => $faker->numberBetween(10, 50),
                    'unit' => $faker->randomElement($units),
                    'reorder_level' => $reorderLevel,
                    'branch_id' => $branchId,
                    'created_at' => Carbon::now()->subMonths(2), // Old stock
                    'updated_at' => Carbon::now()->subMonths(2),
                ]);
            }

            // 2. Create 20 "New Stock" items (Recent stock, Normal Quantity)
            for ($i = 0; $i < 20; $i++) {
                $reorderLevel = $faker->numberBetween(10, 50);
                Inventory::create([
                    'name' => 'New Stock ' . $faker->word,
                    'supplier' => $faker->company,
                    'category' => $faker->randomElement($categories),
                    'quantity' => $faker->numberBetween($reorderLevel + 20, 500), // Normal Quantity
                    'sold' => $faker->numberBetween(0, 10),
                    'spoilage' => 0,
                    'stock_in' => $faker->numberBetween(50, 200),
                    'stock_out' => $faker->numberBetween(0, 10),
                    'unit' => $faker->randomElement($units),
                    'reorder_level' => $reorderLevel,
                    'branch_id' => $branchId,
                    'created_at' => Carbon::now()->subDays(rand(0, 6)), // Within last 7 days
                    'updated_at' => Carbon::now()->subDays(rand(0, 6)),
                ]);
            }

            // 3. Create 10 "Normal" items (Old stock, Normal Quantity)
            for ($i = 0; $i < 10; $i++) {
                $reorderLevel = $faker->numberBetween(10, 50);
                Inventory::create([
                    'name' => 'Normal ' . $faker->word,
                    'supplier' => $faker->company,
                    'category' => $faker->randomElement($categories),
                    'quantity' => $faker->numberBetween($reorderLevel + 20, 500), // Normal Quantity
                    'sold' => $faker->numberBetween(10, 100),
                    'spoilage' => $faker->numberBetween(0, 10),
                    'stock_in' => $faker->numberBetween(50, 200),
                    'stock_out' => $faker->numberBetween(10, 100),
                    'unit' => $faker->randomElement($units),
                    'reorder_level' => $reorderLevel,
                    'branch_id' => $branchId,
                    'created_at' => Carbon::now()->subMonths(2), // Old stock
                    'updated_at' => Carbon::now()->subMonths(2),
                ]);
            }
        }
    }
}
