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
        if (!Schema::hasTable('pos_payments')) {
            Schema::create('pos_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->nullable()->constrained('pos_branches')->onDelete('cascade');
                $table->foreignId('order_id')->constrained('pos_orders');
                $table->decimal('amount', 10, 2);
                $table->string('payment_method');
                $table->json('payment_details')->nullable();
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
        Schema::dropIfExists('pos_payments');
    }
};
