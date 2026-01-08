<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('crm_cart_items')) {
            Schema::create('crm_cart_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cart_id')->constrained('crm_carts')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('crm_products')->cascadeOnDelete();
                $table->unsignedInteger('quantity')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_cart_items');
    }
};