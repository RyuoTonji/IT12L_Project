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
        if (!Schema::hasTable('crm_inventory_adjustments')) {
            Schema::create('crm_inventory_adjustments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_id')->constrained('crm_inventories')->onDelete('cascade');
                $table->enum('adjustment_type', ['stock_in', 'return', 'spoilage', 'damaged', 'other']);
                $table->decimal('quantity', 10, 2);
                $table->decimal('quantity_before', 10, 2)->nullable();
                $table->decimal('quantity_after', 10, 2)->nullable();
                $table->text('reason')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('recorded_by');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_inventory_adjustments');
    }
};
