<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // seed for users
        $this->command->info('Seeding users...');

        $usersToSeed = [
            [
                'name' => 'Admin User',
                'email' => 'admin@foodorder.com',
                'password' => Hash::make('admin12345'),
                'phone' => '09171234567',
                'address' => 'Admin Address',
                'role' => 'admin',
                'is_admin' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'lola lol',
                'email' => 'lola@example.com',
                'password' => Hash::make('lola12345'),
                'phone' => '09187654321',
                'address' => 'Customer Address 1',
                'role' => 'customer',
                'is_admin' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'phone' => '09199876543',
                'address' => 'Customer Address 2',
                'role' => 'customer',
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
    }
}
