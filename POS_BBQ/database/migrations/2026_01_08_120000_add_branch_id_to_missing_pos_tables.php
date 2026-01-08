<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pos_void_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_void_requests', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('id')->constrained('pos_branches')->nullOnDelete();
            }
        });

        Schema::table('pos_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_activities', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('id')->constrained('pos_branches')->nullOnDelete();
            }
        });

        Schema::table('pos_inventory_adjustments', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_inventory_adjustments', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('id')->constrained('pos_branches')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_void_requests', function (Blueprint $table) {
            if (Schema::hasColumn('pos_void_requests', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });

        Schema::table('pos_activities', function (Blueprint $table) {
            if (Schema::hasColumn('pos_activities', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });

        Schema::table('pos_inventory_adjustments', function (Blueprint $table) {
            if (Schema::hasColumn('pos_inventory_adjustments', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
    }
};
