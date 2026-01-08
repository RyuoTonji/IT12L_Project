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
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        //Manager User
        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'status' => 'active',
            ]
        );

        // Cashier User
        User::updateOrCreate(
            ['email' => 'cashier@example.com'],
            [
                'name' => 'Cashier User',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'status' => 'active',
            ]
        );

        //Inventory User
        User::updateOrCreate(
            ['email' => 'inventory@example.com'],
            [
                'name' => 'Inventory User',
                'password' => Hash::make('password'),
                'role' => 'inventory',
                'status' => 'active',
            ]
        );

        // Branch 1 Users
        User::updateOrCreate(
            ['email' => 'manager1@example.com'],
            [
                'name' => 'Branch 1 Manager',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'branch_id' => 1,
                'status' => 'active',
            ]
        );


        User::updateOrCreate(
            ['email' => 'cashier1@example.com'],
            [
                'name' => 'Branch 1 Cashier',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'branch_id' => 1,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'inventory1@example.com'],
            [
                'name' => 'Branch 1 Inventory',
                'password' => Hash::make('password'),
                'role' => 'inventory',
                'branch_id' => 1,
                'status' => 'active',
            ]
        );

        // Branch 2 Users
        User::updateOrCreate(
            ['email' => 'manager2@example.com'],
            [
                'name' => 'Branch 2 Manager',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'branch_id' => 2,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'cashier2@example.com'],
            [
                'name' => 'Branch 2 Cashier',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'branch_id' => 2,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'inventory2@example.com'],
            [
                'name' => 'Branch 2 Inventory',
                'password' => Hash::make('password'),
                'role' => 'inventory',
                'branch_id' => 2,
                'status' => 'active',
            ]
        );
    }
}
