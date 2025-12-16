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
        Schema::create('shift_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->enum('report_type', ['sales', 'inventory'])->default('sales');
            $table->date('shift_date');
            $table->timestamp('clock_in_time')->nullable();
            $table->timestamp('clock_out_time')->nullable();
            $table->decimal('total_hours', 8, 2)->nullable();
            $table->decimal('total_sales', 10, 2)->nullable();
            $table->decimal('total_refunds', 10, 2)->nullable();
            $table->integer('total_orders')->nullable();
            $table->decimal('stock_in', 10, 2)->nullable();
            $table->decimal('stock_out', 10, 2)->nullable();
            $table->decimal('remaining_stock', 10, 2)->nullable();
            $table->decimal('spoilage', 10, 2)->nullable();
            $table->decimal('returns', 10, 2)->nullable();
            $table->text('return_reason')->nullable();
            $table->text('content'); // The written report
            $table->text('admin_reply')->nullable();
            $table->enum('status', ['submitted', 'reviewed'])->default('submitted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_reports');
    }
};
