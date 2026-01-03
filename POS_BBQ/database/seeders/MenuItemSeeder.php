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
            // Skewers
            [
                'category_id' => $skewersCategory,
                'name' => 'Pork BBQ Skewers',
                'description' => 'Classic Filipino-style barbecued pork skewers glazed with sweet BBQ sauce',
                'price' => 25.00,
                'inventory_name' => 'Raw Pork BBQ Skewer',
                'unit' => 'pcs',
            ],
            [
                'category_id' => $skewersCategory,
                'name' => 'Chicken Isaw',
                'description' => 'Grilled chicken intestines brushed with special spiced glaze',
                'price' => 15.00,
                'inventory_name' => 'Raw Chicken Isaw',
                'unit' => 'pcs',
            ],
            [
                'category_id' => $skewersCategory,
                'name' => 'Pork Isaw',
                'description' => 'Char-grilled pork intestines marinated Filipino-style',
                'price' => 15.00,
                'inventory_name' => 'Raw Pork Isaw',
                'unit' => 'pcs',
            ],
            [
                'category_id' => $skewersCategory,
                'name' => 'Betamax',
                'description' => 'Grilled coagulated pork or chicken blood skewers',
                'price' => 10.00,
                'inventory_name' => 'Raw Betamax',
                'unit' => 'pcs',
            ],
            [
                'category_id' => $skewersCategory,
                'name' => 'ChickeN Feet',
                'description' => 'Marinated chicken feet grilled to perfection',
                'price' => 20.00,
                'inventory_name' => 'Raw Chicken Feet',
                'unit' => 'pcs',
            ],

            // Lunch & Dinner
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Pork Adobo',
                'description' => 'Slow-cooked pork in soy sauce, vinegar, garlic, and bay leaves',
                'price' => 89.00,
                'inventory_name' => 'Pork Adobo Portion',
                'unit' => 'serving',
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Chicken Adobo',
                'description' => 'Classic Filipino chicken adobo with tender meat and rich sauce',
                'price' => 85.00,
                'inventory_name' => 'Chicken Adobo Portion',
                'unit' => 'serving',
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Beef Caldereta',
                'description' => 'Hearty beef stew with tomato sauce, potatoes, and bell peppers',
                'price' => 120.00,
                'inventory_name' => 'Beef Caldereta Portion',
                'unit' => 'serving',
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Kare-Kare',
                'description' => 'Oxtail peanut stew served with bagoong',
                'price' => 130.00,
                'inventory_name' => 'Kare-Kare Portion',
                'unit' => 'serving',
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Sinigang na Baboy',
                'description' => 'Sour pork soup made with tamarind and vegetables',
                'price' => 95.00,
                'inventory_name' => 'Sinigang Portion',
                'unit' => 'serving',
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Pancit',
                'description' => 'Stir-fried noodles with vegetables, chicken, and pork',
                'price' => 75.00,
                'inventory_name' => 'Pancit Platter',
                'unit' => 'serving',
            ],
            [
                'category_id' => $lunchDinnerCategory,
                'name' => 'Sisig',
                'description' => 'Sizzling pork sisig with onions, calamansi, and chili',
                'price' => 110.00,
                'inventory_name' => 'Sisig Portion',
                'unit' => 'serving',
            ],

            // Drinks
            [
                'category_id' => $drinksCategory,
                'name' => 'Coca-Cola',
                'description' => 'Chilled Coke in can or bottle',
                'price' => 20.00,
                'inventory_name' => 'Coke Can/Bottle',
                'unit' => 'can/bottle',
            ],
            [
                'category_id' => $drinksCategory,
                'name' => 'Pepsi',
                'description' => 'Cold Pepsi drink',
                'price' => 20.00,
                'inventory_name' => 'Pepsi Can/Bottle',
                'unit' => 'can/bottle',
            ],
            [
                'category_id' => $drinksCategory,
                'name' => '7Up',
                'description' => 'Refreshing lemon-lime soda',
                'price' => 20.00,
                'inventory_name' => '7Up Can/Bottle',
                'unit' => 'can/bottle',
            ],
            [
                'category_id' => $drinksCategory,
                'name' => 'Mountain Dew',
                'description' => 'Citrus soda drink',
                'price' => 20.00,
                'inventory_name' => 'Mountain Dew Can/Bottle',
                'unit' => 'can/bottle',
            ],
            [
                'category_id' => $drinksCategory,
                'name' => 'Royal Orange',
                'description' => 'Classic Filipino orange soda',
                'price' => 20.00,
                'inventory_name' => 'Royal Can/Bottle',
                'unit' => 'can/bottle',
            ],
            [
                'category_id' => $drinksCategory,
                'name' => 'Iced Tea',
                'description' => 'House-blend iced tea',
                'price' => 20.00,
                'inventory_name' => 'Iced Tea Powder Mix', // Simplified
                'unit' => 'serving',
            ],
            [
                'category_id' => $drinksCategory,
                'name' => 'Mango Shake',
                'description' => 'Fresh ripe mango shake',
                'price' => 30.00,
                'inventory_name' => 'Ripe Mango',
                'unit' => 'pcs',
            ],

            // Desserts
            [
                'category_id' => $filipinoDessertCategory,
                'name' => 'Leche Flan',
                'description' => 'Creamy Filipino caramel custard dessert',
                'price' => 55.00,
                'inventory_name' => 'Leche Flan Mold',
                'unit' => 'pcs',
            ],
            [
                'category_id' => $filipinoDessertCategory,
                'name' => 'Halo-Halo',
                'description' => 'Shaved ice dessert with milk, beans, jellies, and leche flan',
                'price' => 50.00,
                'inventory_name' => 'Halo-Halo Ingredients Set',
                'unit' => 'serving',
            ],
            [
                'category_id' => $filipinoDessertCategory,
                'name' => 'Bibingka',
                'description' => 'Soft rice cake baked in banana leaves',
                'price' => 25.00,
                'inventory_name' => 'Bibingka Piece',
                'unit' => 'pcs',
            ],
            [
                'category_id' => $filipinoDessertCategory,
                'name' => 'Puto',
                'description' => 'Steamed Filipino rice cakes',
                'price' => 10.00,
                'inventory_name' => 'Puto Mix',
                'unit' => 'serving',
            ],
            [
                'category_id' => $filipinoDessertCategory,
                'name' => 'Turon',
                'description' => 'Banana and jackfruit spring roll coated in caramel',
                'price' => 10.00,
                'inventory_name' => 'Uncooked Turon',
                'unit' => 'pcs',
            ],
        ];

        foreach ($menuItems as $item) {
            // 1. Create or Update Menu Item
            $menuItem = MenuItem::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $item['category_id'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'availability' => true, // This is just the boolean flag, actual availability depends on stock
                ]
            );

            // 2. Create corresponding Inventory Item if it doesn't exist
            // Giving them 100 Initial Stock so they are sellable
            $inventory = \App\Models\Inventory::firstOrCreate(
                ['name' => $item['inventory_name']],
                [
                    'category' => 'Ingredients',
                    'quantity' => 100, // Initial stock
                    'unit' => $item['unit'],
                    'sold' => 0,
                    'spoilage' => 0,
                    'stock_in' => 100,
                    'stock_out' => 0,
                    'reorder_level' => 10,
                ]
            );

            // 3. Link Menu Item to Inventory Item
            if (!$menuItem->inventoryItems()->where('inventory_id', $inventory->id)->exists()) {
                $menuItem->inventoryItems()->attach($inventory->id, [
                    'quantity_needed' => 1,
                    'unit' => $item['unit']
                ]);
            }
        }
    }
}
