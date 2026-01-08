<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Skewers', 'description' => 'Grilled skewers and street food'],
            ['name' => 'Lunch & Dinner', 'description' => 'Rice meals and main dishes'],
            ['name' => 'Drinks', 'description' => 'Refreshments and beverages'],
            ['name' => 'Desserts', 'description' => 'Sweet treats'],
            ['name' => 'Sides', 'description' => 'Side dishes and appetizers'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
