<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // seed for branches
        $this->command->info('Seeding branches...');

        if (DB::table('branches')->count() == 0) {
            $branches = [
                ['name' => 'BBQ Lagao Branch', 'address' => 'Lagao, Davao City', 'phone' => '(082) 1234-5678', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'BBQ Ulas Branch', 'address' => 'Ulas, Davao City', 'phone' => '(082) 8765-4321', 'created_at' => now(), 'updated_at' => now()]
            ];
            DB::table('branches')->insert($branches);
            $this->command->info('✓ Branches seeded successfully');
        } else {
            $this->command->info('⊘ Branches already exist, skipping...');
        }
    }
}
