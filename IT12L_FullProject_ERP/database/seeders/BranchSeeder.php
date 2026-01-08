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

        $branches = [
            ['id' => 1, 'name' => 'BBQ Lagao Branch', 'address' => 'Lagao, Davao City', 'phone' => '(082) 1234-5678', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'BBQ Ulas Branch', 'address' => 'Ulas, Davao City', 'phone' => '(082) 8765-4321', 'created_at' => now(), 'updated_at' => now()]
        ];

        foreach ($branches as $branch) {
            DB::table('crm_branches')->updateOrInsert(
                ['id' => $branch['id']],
                $branch
            );
        }

        $this->command->info('✓ Branches synced successfully');
    }
}
