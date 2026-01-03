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

        // Branch 1 Users
        User::create([
            'name' => 'Branch 1 Manager',
            'email' => 'manager1@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'branch_id' => 1,
            'status' => 'active',
        ]);


        User::create([
            'name' => 'Branch 1 Cashier',
            'email' => 'cashier1@example.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Branch 1 Inventory',
            'email' => 'inventory1@example.com',
            'password' => Hash::make('password'),
            'role' => 'inventory',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        // Branch 2 Users
        User::create([
            'name' => 'Branch 2 Manager',
            'email' => 'manager2@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'branch_id' => 2,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Branch 2 Cashier',
            'email' => 'cashier2@example.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'branch_id' => 2,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Branch 2 Inventory',
            'email' => 'inventory2@example.com',
            'password' => Hash::make('password'),
            'role' => 'inventory',
            'branch_id' => 2,
            'status' => 'active',
        ]);
    }
}
