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
        if (!Schema::hasTable('crm_feedbacks')) {
            Schema::create('crm_feedbacks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('crm_users')->onDelete('set null');
                $table->foreignId('order_id')->nullable()->constrained('crm_orders')->onDelete('set null');
                $table->string('customer_name');
                $table->string('customer_email');
                $table->enum('feedback_type', ['feedback', 'complaint', 'suggestion']);
                $table->enum('customer_type', ['dine-in', 'pick-up', 'take-out']);
                $table->text('message');
                $table->enum('status', ['new', 'read', 'resolved'])->default('new');
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
        Schema::dropIfExists('crm_feedbacks');
    }
};
