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
        // 1. Cleanup Duplicate Tables
        // We want to keep unique combinations of (name, branch_id)
        // For tables with null branch_id, we keep them as well (per user request)

        $tables = DB::table('pos_tables')->get();
        $seen = [];
        $toDelete = [];

        foreach ($tables as $table) {
            $key = ($table->name ?? '') . '_' . ($table->branch_id ?? 'null');
            if (isset($seen[$key])) {
                $toDelete[] = $table->id;
            } else {
                $seen[$key] = true;
            }
        }

        if (!empty($toDelete)) {
            DB::table('pos_tables')->whereIn('id', $toDelete)->delete();
        }

        // 2. Fix Branchless Inventory
        // Inventory items should ideally belong to a branch.
        // If they are branchless, they are invisible with the new scope.
        // We will assign branchless inventory to Branch 1 by default, or duplicate for each branch.
        // Seeing as MenuItemSeeder needs them per branch, we'll assign existing ones to Branch 1 
        // and let the seeder create Branch 2 ones later.

        DB::table('pos_inventory')
            ->whereNull('branch_id')
            ->update(['branch_id' => 1]);

        // 3. Fix Branchless Orders/Payments if any (safety measure)
        DB::table('pos_orders')
            ->whereNull('branch_id')
            ->update(['branch_id' => 1]);

        DB::table('pos_payments')
            ->whereNull('branch_id')
            ->update(['branch_id' => 1]);

        DB::table('pos_shift_reports')
            ->whereNull('branch_id')
            ->update(['branch_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse cleanup
    }
};
