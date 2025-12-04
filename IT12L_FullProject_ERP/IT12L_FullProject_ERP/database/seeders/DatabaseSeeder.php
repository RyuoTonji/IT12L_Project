<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        // ========================================================================
        // 1. SEED BRANCHES
        // ========================================================================
        $this->command->info('Seeding branches...');
        $branches = [
            ['name' => 'BBQ Lagao Branch', 'address' => 'Lagao, Davao City', 'phone' => '(082) 1234-5678', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BBQ Ulas Branch', 'address' => 'Ulas, Davao City', 'phone' => '(082) 8765-4321', 'created_at' => now(), 'updated_at' => now()]
        ];
        DB::table('branches')->insert($branches);
        $this->command->info('✓ Branches seeded successfully');

        // ========================================================================
        // 2. SEED CATEGORIES
        // ========================================================================
        $this->command->info('Seeding categories...');
        $categories = [
            ['name' => 'Rice Meals', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Barbecue', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pares', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Beverages', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sides', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desserts', 'created_at' => now(), 'updated_at' => now()]
        ];
        DB::table('categories')->insert($categories);
        $this->command->info('✓ Categories seeded successfully');

        // ========================================================================
        // 3. SEED USERS
        // ========================================================================
        $this->command->info('Seeding users...');
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@foodorder.com',
                'password' => Hash::make('admin12345'),
                'phone' => '09171234567',
                'is_admin' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'phone' => '09187654321',
                'is_admin' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'phone' => '09199876543',
                'is_admin' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        DB::table('users')->insert($users);
        $this->command->info('✓ Users seeded successfully');

        // ========================================================================
        // 4. SEED PRODUCTS
        // ========================================================================
        $this->command->info('Seeding products...');
        $products = [
            // Branch 1 - Rice Meals (category_id: 1)
            ['branch_id' => 1, 'category_id' => 1, 'name' => 'Chicken BBQ with Rice', 'price' => 99.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 1, 'name' => 'Pork BBQ with Rice', 'price' => 99.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 1, 'name' => 'Beef Tapa with Rice', 'price' => 120.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 1, 'name' => 'Sisig with Rice', 'price' => 110.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 2 - Rice Meals
            ['branch_id' => 2, 'category_id' => 1, 'name' => 'Chicken BBQ with Rice', 'price' => 99.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 1, 'name' => 'Pork BBQ with Rice', 'price' => 99.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 1, 'name' => 'Beef Tapa with Rice', 'price' => 120.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 1, 'name' => 'Sisig with Rice', 'price' => 110.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 1 - Barbecue (category_id: 2)
            ['branch_id' => 1, 'category_id' => 2, 'name' => 'Pork Isaw (Grilled pork intestines)', 'price' => 65.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 2, 'name' => 'Adidas (Grilled chicken feet)', 'price' => 120.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 2, 'name' => 'Chicken Isaw (Grilled chicken intestines)', 'price' => 150.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 2 - Barbecue
            ['branch_id' => 2, 'category_id' => 2, 'name' => 'Pork Isaw (Grilled pork intestines)', 'price' => 65.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 2, 'name' => 'Adidas (Grilled chicken feet)', 'price' => 120.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 2, 'name' => 'Chicken Isaw (Grilled chicken intestines)', 'price' => 150.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 1 - Pares (category_id: 3)
            ['branch_id' => 1, 'category_id' => 3, 'name' => 'Pares Classic', 'price' => 85.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 3, 'name' => 'Beef Pares (House Special)', 'price' => 95.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 3, 'name' => 'Pares Overload', 'price' => 110.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 2 - Pares
            ['branch_id' => 2, 'category_id' => 3, 'name' => 'Pares Classic', 'price' => 85.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 3, 'name' => 'Beef Pares (House Special)', 'price' => 95.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 3, 'name' => 'Pares Overload', 'price' => 110.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 1 - Beverages (category_id: 4)
            ['branch_id' => 1, 'category_id' => 4, 'name' => 'Iced Tea', 'price' => 35.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 4, 'name' => 'Coke', 'price' => 40.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 4, 'name' => 'Sprite', 'price' => 40.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 4, 'name' => 'Bottled Water', 'price' => 25.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 2 - Beverages
            ['branch_id' => 2, 'category_id' => 4, 'name' => 'Iced Tea', 'price' => 35.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 4, 'name' => 'Coke', 'price' => 40.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 4, 'name' => 'Sprite', 'price' => 40.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 4, 'name' => 'Bottled Water', 'price' => 25.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 1 - Sides (category_id: 5)
            ['branch_id' => 1, 'category_id' => 5, 'name' => 'French Fries', 'price' => 50.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 5, 'name' => 'Onion Rings', 'price' => 60.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 5, 'name' => 'Coleslaw', 'price' => 40.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 2 - Sides
            ['branch_id' => 2, 'category_id' => 5, 'name' => 'French Fries', 'price' => 50.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 5, 'name' => 'Onion Rings', 'price' => 60.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 5, 'name' => 'Coleslaw', 'price' => 40.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 1 - Desserts (category_id: 6)
            ['branch_id' => 1, 'category_id' => 6, 'name' => 'Halo-Halo', 'price' => 80.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 6, 'name' => 'Ice Cream Sundae', 'price' => 70.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 6, 'name' => 'Leche Flan', 'price' => 60.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Branch 2 - Desserts
            ['branch_id' => 2, 'category_id' => 6, 'name' => 'Halo-Halo', 'price' => 80.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 6, 'name' => 'Ice Cream Sundae', 'price' => 70.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 6, 'name' => 'Leche Flan', 'price' => 60.00, 'image' => null, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()]
        ];
        DB::table('products')->insert($products);
        $this->command->info('✓ Products seeded successfully');

        // ========================================================================
        // SUMMARY
        // ========================================================================
        $this->command->info('');
        $this->command->info('================================================');
        $this->command->info('DATABASE SEEDING COMPLETED!');
        $this->command->info('================================================');
        $this->command->info('');
        $this->command->info('Sample Credentials:');
        $this->command->info('-------------------');
        $this->command->info('Admin User:');
        $this->command->info('  Email: admin@foodorder.com');
        $this->command->info('  Password: admin12345');
        $this->command->info('');
        $this->command->info('Regular Users:');
        $this->command->info('  Email: john@example.com');
        $this->command->info('  Password: password123');
        $this->command->info('');
        $this->command->info('  Email: jane@example.com');
        $this->command->info('  Password: password123');
        $this->command->info('');
        $this->command->info('Branches: 2 branches created');
        $this->command->info('Categories: 6 categories created');
        $this->command->info('Products: ' . count($products) . ' products created');
        $this->command->info('Users: 3 users created');
    }
}