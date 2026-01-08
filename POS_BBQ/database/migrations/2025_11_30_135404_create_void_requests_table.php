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
        if (!Schema::hasTable('pos_void_requests')) {
            Schema::create('pos_void_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('pos_orders')->onDelete('cascade');
                $table->foreignId('requester_id')->constrained('pos_users');
                $table->foreignId('approver_id')->nullable()->constrained('pos_users');
                $table->string('reason')->nullable();
                $table->json('reason_tags')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_void_requests');
    }
};
