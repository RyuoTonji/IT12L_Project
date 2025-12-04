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
        Schema::table('shift_reports', function (Blueprint $table) {
            // Make existing sales fields nullable since inventory reports won't use them
            $table->decimal('total_sales', 10, 2)->nullable()->change();
            $table->decimal('total_refunds', 10, 2)->nullable()->change();
            $table->integer('total_orders')->nullable()->change();

            // Add report type to distinguish between sales and inventory reports
            $table->enum('report_type', ['sales', 'inventory'])->default('sales')->after('user_id');

            // Add inventory-specific fields
            $table->decimal('stock_in', 10, 2)->nullable()->after('total_orders');
            $table->decimal('stock_out', 10, 2)->nullable()->after('stock_in');
            $table->decimal('remaining_stock', 10, 2)->nullable()->after('stock_out');
            $table->decimal('spoilage', 10, 2)->nullable()->after('remaining_stock');
            $table->decimal('returns', 10, 2)->nullable()->after('spoilage');
            $table->text('return_reason')->nullable()->after('returns');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_reports', function (Blueprint $table) {
            // Remove inventory fields
            $table->dropColumn(['report_type', 'stock_in', 'stock_out', 'remaining_stock', 'spoilage', 'returns', 'return_reason']);

            // Revert sales fields to non-nullable (if needed in rollback)
            $table->decimal('total_sales', 10, 2)->default(0)->change();
            $table->decimal('total_refunds', 10, 2)->default(0)->change();
            $table->integer('total_orders')->default(0)->change();
        });
    }
};
