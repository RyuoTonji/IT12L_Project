<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing status values to new format
        // 'active' stays 'active'
        // 'inactive' becomes 'disabled' (as it was manually set)
        DB::table('users')
            ->where('status', 'inactive')
            ->update(['status' => 'disabled']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert disabled back to inactive
        DB::table('users')
            ->where('status', 'disabled')
            ->update(['status' => 'inactive']);
    }
};
