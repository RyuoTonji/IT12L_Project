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
            $table->foreignId('user_id')->constrained();
            $table->date('shift_date');
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->decimal('total_refunds', 10, 2)->default(0);
            $table->integer('total_orders')->default(0);
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
