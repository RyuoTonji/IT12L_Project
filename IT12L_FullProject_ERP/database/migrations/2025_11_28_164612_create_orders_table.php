<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('crm_orders')) {
            Schema::create('crm_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('crm_users')->onDelete('cascade');
                $table->foreignId('branch_id')->nullable()->constrained('crm_branches')->onDelete('cascade');
                $table->unsignedBigInteger('table_id')->nullable();  // Table number for dine-in orders
                $table->decimal('total_amount', 10, 2);
                $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'picked up', 'cancelled', 'served', 'completed'])->default('pending');
                $table->string('order_type')->nullable();
                $table->string('payment_method')->default('cash');
                $table->string('paymongo_source_id')->nullable();
                $table->string('payment_status')->default('pending');
                $table->boolean('is_synced')->default(false);
                $table->string('customer_name', 100)->nullable();
                $table->string('customer_phone', 20)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('crm_order_items')) {
            Schema::create('crm_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('crm_orders')->onDelete('cascade');
                $table->foreignId('product_id')->nullable()->constrained('crm_products')->onDelete('set null');
                $table->unsignedBigInteger('menu_item_id')->nullable();
                $table->string('product_name', 200);
                $table->string('product_image')->nullable();
                $table->integer('quantity');
                $table->decimal('unit_price', 10, 2);
                $table->decimal('subtotal', 10, 2)->nullable();
                $table->boolean('is_synced')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_order_items');
        Schema::dropIfExists('crm_orders');
    }
};