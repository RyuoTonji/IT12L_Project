<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\User;
use Carbon\Carbon;

class InventoryAdjustmentSeeder extends Seeder
{
    public function run()
    {
        $inventories = Inventory::all();
        $users = User::all();

        if ($inventories->isEmpty() || $users->isEmpty()) {
            $this->command->error('Missing inventory or users. Please seed them first.');
            return;
        }

        // 1. Spoilage (50 records)
        $this->seedAdjustments(50, 'spoilage', $inventories, $users);

        // 2. Lost / Damaged (50 records)
        $this->seedAdjustments(50, 'damaged', $inventories, $users);

        // 3. Returns (50 records)
        $this->seedAdjustments(50, 'return', $inventories, $users);
    }

    private function seedAdjustments($count, $type, $inventories, $users)
    {
        $this->command->info("Seeding {$count} {$type} adjustments...");

        for ($i = 0; $i < $count; $i++) {
            $inventory = $inventories->random();
            $recorder = $users->random();

            // Random quantity between 0.5 and 5
            $quantity = rand(5, 50) / 10;

            InventoryAdjustment::create([
                'inventory_id' => $inventory->id,
                'adjustment_type' => $type,
                'quantity' => $quantity,
                'reason' => "Seeded {$type} record #" . ($i + 1),
                'recorded_by' => $recorder->id,
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 24)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 24)),
            ]);
        }
    }
}
