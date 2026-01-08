<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        $this->call([
            BranchSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            SampleOrdersSeeder::class,
        ]);

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
        $this->command->info('  Branches: ' . DB::table('crm_branches')->count());
        $this->command->info('  Categories: ' . DB::table('crm_categories')->count());
        $this->command->info('  Products: ' . DB::table('crm_products')->count());
        $this->command->info('  Users: ' . DB::table('crm_users')->count());
        $this->command->info('  Orders: ' . DB::table('crm_orders')->count());
        $this->command->info('');
        $this->command->info('NOTE: Make sure your product images exist in public/storage/products/');
        $this->command->info('Run: php artisan storage:link (if not already done)');
    }
}