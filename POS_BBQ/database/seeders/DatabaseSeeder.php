<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            MenuItemSeeder::class, // Now creates inventory items and links them
            TableSeeder::class,
            InventorySeeder::class, // Simplified, mostly empty
            OrderSeeder::class,
            VoidRefundSeeder::class,
            // InventoryAdjustmentSeeder::class, // Removed - can be run separately if needed
        ]);
    }
}
