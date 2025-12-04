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
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_hours', 10, 2);
            $table->decimal('hourly_rate', 8, 2);
            $table->decimal('gross_pay', 10, 2);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2);
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_records');
    }
};
