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
        Branch::create([
            'name' => 'Branch 1',
            'code' => 'BR1',
            'address' => 'Main Street, City Center',
            'phone' => '(123) 456-7890',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'Branch 2',
            'code' => 'BR2',
            'address' => 'Second Avenue, Downtown',
            'phone' => '(123) 456-7891',
            'is_active' => true,
        ]);
    }
}
