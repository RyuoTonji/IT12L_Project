<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
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

            // Create deletion_logs table to store detailed info about deletions
            if (!Schema::hasTable('deletion_logs')) {
                Schema::create('deletion_logs', function (Blueprint $table) {
                    $table->id();
                    $table->string('table_name');
                    $table->unsignedBigInteger('record_id');
                    $table->json('data')->nullable(); // Store snapshot of deleted data
                    $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
                    $table->string('reason')->nullable();
                    $table->timestamp('deleted_at');
                    $table->timestamps(); // created_at = when log was created
                });
            }
        } else {
            // Add is_admin column if table exists but column doesn't
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'address')) {
                    $table->string('address')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('users', 'is_admin')) {
                    $table->boolean('is_admin')->default(false)->after('phone');
                }
                if (!Schema::hasColumn('users', 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable()->after('is_admin');
                }
                if (!Schema::hasColumn('users', 'remember_token')) {
                    $table->rememberToken()->after('email_verified_at');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};