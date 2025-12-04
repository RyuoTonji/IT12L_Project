<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        //Manager User
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);

        // Cashier User
        User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@example.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'status' => 'active',
        ]);

        //Inventory User
        User::create([
            'name' => 'Inventory User',
            'email' => 'inventory@example.com',
            'password' => Hash::make('password'),
            'role' => 'inventory',
            'status' => 'active',
        ]);

    }
}
