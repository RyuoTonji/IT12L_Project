<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::firstOrCreate(
            ['code' => 'BR1'],
            [
                'name' => 'Branch 1',
                'address' => 'Corner Ulha Village, Ulas Davao City, Davao Del Sur 8000',
                'phone' => '(123) 456-7890',
                'is_active' => true,
            ]
        );

        Branch::firstOrCreate(
            ['code' => 'BR2'],
            [
                'name' => 'Branch 2',
                'address' => 'Second Avenue, Downtown',
                'phone' => '(123) 456-7891',
                'is_active' => true,
            ]
        );
    }
}
