<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('email', 100)->unique();
                $table->string('password');
                $table->string('phone', 20)->nullable();
                $table->boolean('is_admin')->default(false);
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            // Add is_admin column if table exists but column doesn't
            Schema::table('users', function (Blueprint $table) {
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