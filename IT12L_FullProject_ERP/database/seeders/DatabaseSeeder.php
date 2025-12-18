<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
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

        // ========================================================================
        // 2. SEED CATEGORIES
        // ========================================================================
        $this->command->info('Seeding categories...');
        
        if (DB::table('categories')->count() == 0) {
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
        } else {
            $this->command->info('⊘ Categories already exist, skipping...');
        }

        // ========================================================================
        // 3. SEED USERS
        // ========================================================================
        $this->command->info('Seeding users...');
        
        $usersToSeed = [
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
                'name' => 'lola lol',
                'email' => 'lola@example.com',
                'password' => Hash::make('lola12345'),
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

        foreach ($usersToSeed as $userData) {
            if (!DB::table('users')->where('email', $userData['email'])->exists()) {
                DB::table('users')->insert($userData);
                $this->command->info("✓ User {$userData['email']} created");
            } else {
                $this->command->info("⊘ User {$userData['email']} already exists, skipping...");
            }
        }

        // ========================================================================
        // 4. SEED PRODUCTS WITH IMAGES
        // ========================================================================
        $this->command->info('Seeding products...');
        
        if (DB::table('products')->count() == 0) {
            // Ensure products directory exists
            $productsDir = public_path('storage/products');
            if (!File::exists($productsDir)) {
                File::makeDirectory($productsDir, 0755, true);
                $this->command->info('✓ Created products directory');
            }

            $products = [
                // Branch 1 - Rice Meals (category_id: 1)
                [
                    'branch_id' => 1, 
                    'category_id' => 1, 
                    'name' => 'Chicken BBQ with Rice', 
                    'price' => 99.00, 
                    'image' => 'products/1766059046_6943ec26608f5.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 1, 
                    'name' => 'Pork BBQ with Rice', 
                    'price' => 99.00, 
                    'image' => 'products/1766059033_6943ec19b0fda.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 1, 
                    'name' => 'Beef Tapa with Rice', 
                    'price' => 120.00, 
                    'image' => 'products/1766059016_6943ec08683f8.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 1, 
                    'name' => 'Sisig with Rice', 
                    'price' => 110.00, 
                    'image' => 'products/1766059004_6943ebfcd6fd9.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 2 - Rice Meals
                [
                    'branch_id' => 2, 
                    'category_id' => 1, 
                    'name' => 'Chicken BBQ with Rice', 
                    'price' => 99.00, 
                    'image' => 'products/1766058993_6943ebf199973.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 1, 
                    'name' => 'Pork BBQ with Rice', 
                    'price' => 99.00, 
                    'image' => 'products/1766058982_6943ebe6a9dff.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 1, 
                    'name' => 'Beef Tapa with Rice', 
                    'price' => 120.00, 
                    'image' => 'products/1766058971_6943ebdb0ee0b.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 1, 
                    'name' => 'Sisig with Rice', 
                    'price' => 110.00, 
                    'image' => 'products/1766058960_6943ebd0b8604.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 1 - Barbecue (category_id: 2)
                [
                    'branch_id' => 1, 
                    'category_id' => 2, 
                    'name' => 'Pork Isaw (Grilled pork intestines)', 
                    'price' => 65.00, 
                    'image' => 'products/1766058949_6943ebc50b9e6.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 2, 
                    'name' => 'Adidas (Grilled chicken feet)', 
                    'price' => 120.00, 
                    'image' => 'products/1766058910_6943eb9ee45d1.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 2, 
                    'name' => 'Chicken Isaw (Grilled chicken intestines)', 
                    'price' => 150.00, 
                    'image' => 'products/1766058892_6943eb8c3dcd1.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 2 - Barbecue
                [
                    'branch_id' => 2, 
                    'category_id' => 2, 
                    'name' => 'Pork Isaw (Grilled pork intestines)', 
                    'price' => 65.00, 
                    'image' => 'products/1766058936_6943ebb83a8da.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 2, 
                    'name' => 'Adidas (Grilled chicken feet)', 
                    'price' => 120.00, 
                    'image' => 'products/1766058925_6943ebad21615.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 2, 
                    'name' => 'Chicken Isaw (Grilled chicken intestines)', 
                    'price' => 150.00, 
                    'image' => 'products/1766058862_6943eb6e2982d.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 1 - Pares (category_id: 3)
                [
                    'branch_id' => 1, 
                    'category_id' => 3, 
                    'name' => 'Pares Classic', 
                    'price' => 85.00, 
                    'image' => 'products/qdvqTViXKQSYopJ4tTWVNP1QecgbzyzYfpHcywwA.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 3, 
                    'name' => 'Beef Pares (House Special)', 
                    'price' => 95.00, 
                    'image' => 'products/DdIVBLOw3agxENXMIds2SvKa5x1MpWiLttXFidfy.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 3, 
                    'name' => 'Pares Overload', 
                    'price' => 110.00, 
                    'image' => 'products/1766058786_6943eb22d8fcb.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 2 - Pares
                [
                    'branch_id' => 2, 
                    'category_id' => 3, 
                    'name' => 'Pares Classic', 
                    'price' => 85.00, 
                    'image' => 'products/1766058800_6943eb3018599.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 3, 
                    'name' => 'Beef Pares (House Special)', 
                    'price' => 95.00, 
                    'image' => 'products/1766058812_6943eb3cd62eb.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 3, 
                    'name' => 'Pares Overload', 
                    'price' => 110.00, 
                    'image' => 'products/1766058825_6943eb49b2b68.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 1 - Beverages (category_id: 4)
                [
                    'branch_id' => 1, 
                    'category_id' => 4, 
                    'name' => 'Iced Tea', 
                    'price' => 35.00, 
                    'image' => 'products/hNK2SqBbD2vAbat5WLpFzueY0YwkeKKgxsKE09ha.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 4, 
                    'name' => 'Coke', 
                    'price' => 40.00, 
                    'image' => 'products/p03DtmyLDn0TEXpaQe7aBLNwXX9AzW8LMoZiIL5c.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 4, 
                    'name' => 'Sprite', 
                    'price' => 40.00, 
                    'image' => 'products/fRWM7R66sL5Y8JpmiV43ug5vmtSZSF6e6Zc9oQCZ.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 4, 
                    'name' => 'Bottled Water', 
                    'price' => 25.00, 
                    'image' => 'products/QRsXmsHgTHw7lGGhljoqO8K4UPqekDKryHEtRROP.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 2 - Beverages
                [
                    'branch_id' => 2, 
                    'category_id' => 4, 
                    'name' => 'Iced Tea', 
                    'price' => 35.00, 
                    'image' => 'products/5mojch638KdzvQHTRIfBF1hUkhfsRkZN3GJisHHB.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 4, 
                    'name' => 'Coke', 
                    'price' => 40.00, 
                    'image' => 'products/RD8drLpFcme9sVZihUvYZAPJyykP2sMxFV5Mv2kY.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 4, 
                    'name' => 'Sprite', 
                    'price' => 40.00, 
                    'image' => 'products/iT4APIybQCAd0tEnclSHuI6jSLsStslqOoy4GA7S.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 4, 
                    'name' => 'Bottled Water', 
                    'price' => 25.00, 
                    'image' => 'products/gbchbzxnnWhDmyhvCkdHteEMSDtkCJu8KDwcvN3B.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 1 - Sides (category_id: 5)
                [
                    'branch_id' => 1, 
                    'category_id' => 5, 
                    'name' => 'French Fries', 
                    'price' => 50.00, 
                    'image' => 'products/jC42exxAW8Wm2khcCnwRZjupa7x0AsWQ9JJyt1gc.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 5, 
                    'name' => 'Onion Rings', 
                    'price' => 60.00, 
                    'image' => 'products/2FquCTpsWbTDnl3HJEpALz2EaJC5iR98BIiHSbCE.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 5, 
                    'name' => 'Coleslaw', 
                    'price' => 40.00, 
                    'image' => 'products/4HX5P3ZqCHsiTxTUfmVO9nSGFOyGR81lv0PwdLuQ.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 2 - Sides
                [
                    'branch_id' => 2, 
                    'category_id' => 5, 
                    'name' => 'French Fries', 
                    'price' => 50.00, 
                    'image' => 'products/46eqtmsclRBhCpeZzDAIpPjzHqNFddE5nRNEXC7Q.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 5, 
                    'name' => 'Onion Rings', 
                    'price' => 60.00, 
                    'image' => 'products/svCD8hPam3DTT4Sqljo5qMYS5hCq7viItLTsQ1qS.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 5, 
                    'name' => 'Coleslaw', 
                    'price' => 40.00, 
                    'image' => 'products/6WguLTXXOdnzc4QwB87qve2wdtyabyMLsp1MS30e.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 1 - Desserts (category_id: 6)
                [
                    'branch_id' => 1, 
                    'category_id' => 6, 
                    'name' => 'Halo-Halo', 
                    'price' => 80.00, 
                    'image' => 'products/H9qr9gAQ5A8GqTWvqut5gJyilm5o9V3nf5I1tdil.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 6, 
                    'name' => 'Ice Cream Sundae', 
                    'price' => 70.00, 
                    'image' => 'products/Bw45Em6lyUS3jsS1NdwrYcfyyjcmnH5d3YfiK7b7.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 1, 
                    'category_id' => 6, 
                    'name' => 'Leche Flan', 
                    'price' => 60.00, 
                    'image' => 'products/E45iF7rQT1Rm5jlaDLdoz1y4sOfZYk2FnYz1Dr33.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                
                // Branch 2 - Desserts
                [
                    'branch_id' => 2, 
                    'category_id' => 6, 
                    'name' => 'Halo-Halo', 
                    'price' => 80.00, 
                    'image' => 'products/MZgSoAvyZxYulduWOTp6NPumw7jaHMgrTFbI9Y6E.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 6, 
                    'name' => 'Ice Cream Sundae', 
                    'price' => 70.00, 
                    'image' => 'products/5RXwDTy7RwUjNGy2WZK9NvCxWUSxZYljD9birfTo.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'branch_id' => 2, 
                    'category_id' => 6, 
                    'name' => 'Leche Flan', 
                    'price' => 60.00, 
                    'image' => 'products/r2SOvAv28fO2uXn85lxNbNrFYcs8syE8mN4S8fzq.png', 
                    'is_available' => 1, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ]
            ];

            DB::table('products')->insert($products);
            $this->command->info('✓ Products seeded successfully with images');
        } else {
            $this->command->info('⊘ Products already exist, skipping...');
        }

        // ========================================================================
        // 5. SEED SAMPLE ORDERS (Call the SampleOrdersSeeder)
        // ========================================================================
        $this->call(SampleOrdersSeeder::class);

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
        $this->command->info('  Email: lola@example.com');
        $this->command->info('  Password: lola12345');
        $this->command->info('');
        $this->command->info('  Email: jane@example.com');
        $this->command->info('  Password: password123');
        $this->command->info('');
        $this->command->info('Database Summary:');
        $this->command->info('  Branches: ' . DB::table('branches')->count());
        $this->command->info('  Categories: ' . DB::table('categories')->count());
        $this->command->info('  Products: ' . DB::table('products')->count());
        $this->command->info('  Users: ' . DB::table('users')->count());
        $this->command->info('  Orders: ' . DB::table('orders')->count());
        $this->command->info('');
        $this->command->info('NOTE: Make sure your product images exist in public/storage/products/');
        $this->command->info('Run: php artisan storage:link (if not already done)');
    }
}