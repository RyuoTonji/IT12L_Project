<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum to include 'ready' status
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'preparing', 'ready', 'picked up', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'ready' status from enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'preparing', 'picked up', 'cancelled') DEFAULT 'pending'");
    }
};

// MANUAL CREATION INSTRUCTIONS:
// 1. Create file: database/migrations/2024_12_19_000000_add_ready_status_to_orders_table.php
// 2. Copy this entire code into that file
// 3. Run: php artisan migrate