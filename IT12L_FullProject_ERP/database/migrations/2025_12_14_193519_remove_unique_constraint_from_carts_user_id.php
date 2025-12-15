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
        // Try to drop the unique constraint if it exists
        try {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropUnique(['user_id']);
            });
        } catch (\Exception $e) {
            // Constraint doesn't exist, that's okay
            echo "Note: user_id unique constraint doesn't exist or already dropped\n";
        }
        
        // Add composite index for better query performance
        try {
            Schema::table('carts', function (Blueprint $table) {
                $table->index(['user_id', 'session_id']);
            });
        } catch (\Exception $e) {
            // Index might already exist
            echo "Note: composite index might already exist\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the composite index
        try {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropIndex(['user_id', 'session_id']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist
        }
        
        // Restore the unique constraint (only if no duplicate user_ids exist)
        try {
            Schema::table('carts', function (Blueprint $table) {
                $table->unique('user_id');
            });
        } catch (\Exception $e) {
            echo "Warning: Cannot restore unique constraint (duplicate user_id values may exist)\n";
        }
    }
};