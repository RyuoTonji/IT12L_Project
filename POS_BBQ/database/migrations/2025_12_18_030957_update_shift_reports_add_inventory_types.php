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
        // Update the enum to include new inventory report types
        DB::statement("ALTER TABLE shift_reports MODIFY report_type ENUM('sales', 'inventory', 'inventory_start', 'inventory_end') DEFAULT 'sales'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE shift_reports MODIFY report_type ENUM('sales', 'inventory') DEFAULT 'sales'");
    }
};
