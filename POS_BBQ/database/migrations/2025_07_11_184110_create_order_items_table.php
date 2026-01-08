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
        if (!Schema::hasTable('pos_order_items')) {
            Schema::create('pos_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('pos_orders')->onDelete('cascade');
                $table->foreignId('menu_item_id')->constrained('pos_menu_items');
                $table->integer('quantity');
                $table->decimal('unit_price', 8, 2);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_order_items');
    }
};
