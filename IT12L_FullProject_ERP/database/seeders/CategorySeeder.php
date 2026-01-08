<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // seed for categories
        $this->command->info('Seeding categories...');

        if (DB::table('crm_categories')->count() == 0) {
            $categories = [
                ['name' => 'Rice Meals', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Barbecue', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Pares', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Beverages', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Sides', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Desserts', 'created_at' => now(), 'updated_at' => now()]
            ];
            DB::table('crm_categories')->insert($categories);
            $this->command->info('✓ Categories seeded successfully');
        } else {
            $this->command->info('⊘ Categories already exist, skipping...');
        }
    }
}
