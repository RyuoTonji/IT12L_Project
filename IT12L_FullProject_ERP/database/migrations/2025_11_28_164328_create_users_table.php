<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('crm_users')) {
            Schema::create('crm_users', function (Blueprint $table) {
                $table->id();
                $table->string('google_id')->nullable();
                $table->string('name', 100);
                $table->string('email', 100)->unique();
                $table->string('avatar')->nullable();
                $table->string('password');
                $table->string('phone', 20)->nullable();
                $table->string('address')->nullable();
                $table->string('role')->default('customer');
                $table->boolean('is_active')->default(true);
                $table->boolean('is_admin')->default(false);
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('crm_deletion_logs')) {
            Schema::create('crm_deletion_logs', function (Blueprint $table) {
                $table->id();
                $table->string('table_name');
                $table->unsignedBigInteger('record_id');
                $table->json('data')->nullable(); // Store snapshot of deleted data
                $table->foreignId('deleted_by')->nullable()->constrained('crm_users')->nullOnDelete();
                $table->string('reason')->nullable();
                $table->timestamp('deleted_at');
                $table->timestamps(); // created_at = when log was created
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_users');
        Schema::dropIfExists('crm_deletion_logs');
    }
};