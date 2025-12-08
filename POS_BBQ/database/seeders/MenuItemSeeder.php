<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run()
    {
        $skewersCategory = Category::where('name', 'Skewers')->first()->id;
        $lunchDinnerCategory = Category::where('name', 'Lunch & Dinner')->first()->id;
        $drinksCategory = Category::where('name', 'Drinks')->first()->id;
        $filipinoDessertCategory = Category::where('name', 'Desserts')->first()->id;

        $menuItems = [
            [
                'category_id' => $skewersCategory,
                'name' => 'Pork BBQ Skewers',
                'description' => 'Classic Filipino-style barbecued pork skewers glazed with sweet BBQ sauce',
                'price' => 25.00,
                'is_available' => true,
            ],
            [
                'category_id' => $skewersCategory,
                'name' => 'Chicken Isaw',
                'description' => 'Grilled chicken intestines brushed with special spiced glaze',
                'price' => 15.00,
                'is_available' => true,
            ],
            [
                'category_id' => $skewersCategory,
                'name' => 'Pork Isaw',
                'description' => 'Char-grilled pork intestines marinated Filipino-style',
                'price' => 15.00,
                'is_available' => true,
            ],
            [
                'category_id' => $skewersCategory,
                'name' => 'Betamax',
                'description' => 'Grilled coagulated pork or chicken blood skewers',
                'price' => 10.00,
                'is_available' => true,
            ],
            [
                'category_id' => $skewersCategory,
                'name' => 'ChickeN Feet',
                'description' => 'Marinated chicken feet grilled to perfection',
                'price' => 20.00,
                'is_available' => true,
            ],

            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Pork Adobo',
                'description' => 'Slow-cooked pork in soy sauce, vinegar, garlic, and bay leaves',
                'price' => 89.00,
                'is_available' => true,
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Chicken Adobo',
                'description' => 'Classic Filipino chicken adobo with tender meat and rich sauce',
                'price' => 85.00,
                'is_available' => true,
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Beef Caldereta',
                'description' => 'Hearty beef stew with tomato sauce, potatoes, and bell peppers',
                'price' => 120.00,
                'is_available' => true,
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Kare-Kare',
                'description' => 'Oxtail peanut stew served with bagoong',
                'price' => 130.00,
                'is_available' => true,
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Sinigang na Baboy',
                'description' => 'Sour pork soup made with tamarind and vegetables',
                'price' => 95.00,
                'is_available' => true,
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Pancit',
                'description' => 'Stir-fried noodles with vegetables, chicken, and pork',
                'price' => 75.00,
                'is_available' => true,
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Sisig',
                'description' => 'Sizzling pork sisig with onions, calamansi, and chili',
                'price' => 110.00,
                'is_available' => true,
            ],

            [
                'category_id' => $drinksCategory,
                'name' => 'Coca-Cola',
                'description' => 'Chilled Coke in can or bottle',
                'price' => 20.00,
                'is_available' => true,
            ],
            [
                'category_id' => $drinksCategory,
                'name' => 'Pepsi',
                'description' => 'Cold Pepsi drink',
                'price' => 20.00,
                'is_available' => true,
            ],
            [
                'category_id' => $drinksCategory,
                'name' => '7Up',
                'description' => 'Refreshing lemon-lime soda',
                'price' => 20.00,
                'is_available' => true,
            ],
            [
                'category_id' => $drinksCategory,
                'name' => 'Mountain Dew',
                'description' => 'Citrus soda drink',
                'price' => 20.00,
                'is_available' => true,
            ],
            [
                'category_id' => $drinksCategory,
                'name' => 'Royal Orange',
                'description' => 'Classic Filipino orange soda',
                'price' => 20.00,
                'is_available' => true,
            ],
            [
                'category_id' => $drinksCategory,
                'name' => 'Iced Tea',
                'description' => 'House-blend iced tea',
                'price' => 20.00,
                'is_available' => true,
            ],
            [
                'category_id' => $drinksCategory,
                'name' => 'Mango Shake',
                'description' => 'Fresh ripe mango shake',
                'price' => 30.00,
                'is_available' => true,
            ],
            [
                'category_id' => $filipinoDessertCategory,
                'name' => 'Leche Flan',
                'description' => 'Creamy Filipino caramel custard dessert',
                'price' => 55.00,
                'is_available' => true,
            ],
            [
                'category_id' => $filipinoDessertCategory,
                'name' => 'Halo-Halo',
                'description' => 'Shaved ice dessert with milk, beans, jellies, and leche flan',
                'price' => 50.00,
                'is_available' => true,
            ],
            [
                'category_id' => $filipinoDessertCategory,
                'name' => 'Bibingka',
                'description' => 'Soft rice cake baked in banana leaves',
                'price' => 25.00,
                'is_available' => true,
            ],
            [
                'category_id' => $filipinoDessertCategory,
                'name' => 'Puto',
                'description' => 'Steamed Filipino rice cakes',
                'price' => 10.00,
                'is_available' => true,
            ],
            [
                'category_id' => $filipinoDessertCategory,
                'name' => 'Turon',
                'description' => 'Banana and jackfruit spring roll coated in caramel',
                'price' => 10.00,
                'is_available' => true,
            ],
        ];

        foreach ($menuItems as $item) {
            MenuItem::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
