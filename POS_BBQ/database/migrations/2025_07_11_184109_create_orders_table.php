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
        if (!Schema::hasTable('pos_orders')) {
            Schema::create('pos_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('table_id')->nullable()->constrained('pos_tables')->onDelete('set null');
                $table->foreignId('user_id')->nullable()->constrained('pos_users')->onDelete('set null');
                $table->foreignId('branch_id')->constrained('pos_branches')->onDelete('cascade');
                $table->string('customer_name')->nullable();
                $table->enum('order_type', ['dine-in', 'takeout'])->default('dine-in');
                $table->enum('status', ['new', 'preparing', 'ready', 'served', 'completed', 'cancelled'])->default('new');
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
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
        Schema::dropIfExists('pos_orders');
    }
};
