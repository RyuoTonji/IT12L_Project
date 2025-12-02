<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            ['name' => 'Table 1', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Table 2', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Table 3', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Table 4', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Table 5', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Table 6', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Table 7', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Table 8', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Table 9', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Table 10', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Table 11', 'capacity' => 5, 'status' => 'available'],
            ['name' => 'Table 12', 'capacity' => 5, 'status' => 'available'],
            ['name' => 'Table 13', 'capacity' => 5, 'status' => 'available'],
            ['name' => 'Table 14', 'capacity' => 5, 'status' => 'available'],
            ['name' => 'Table 15', 'capacity' => 5, 'status' => 'available'],
            ['name' => 'Table 16', 'capacity' => 6, 'status' => 'available'],
            ['name' => 'Table 17', 'capacity' => 6, 'status' => 'available'],
            ['name' => 'Table 18', 'capacity' => 6, 'status' => 'available'],
            ['name' => 'Table 19', 'capacity' => 6, 'status' => 'available'],
            ['name' => 'Table 20', 'capacity' => 6, 'status' => 'available'],

        ];
        
        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
