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
        if (!Schema::hasTable('pos_users')) {
            Schema::create('pos_users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->enum('role', ['admin', 'manager', 'cashier', 'inventory', 'customer']);
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->rememberToken();
                $table->timestamp('last_login_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            Schema::table('pos_users', function (Blueprint $table) {
                if (!Schema::hasColumn('pos_users', 'role')) {
                    $table->enum('role', ['admin', 'manager', 'cashier', 'inventory', 'customer'])->after('password');
                }
                if (!Schema::hasColumn('pos_users', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
                }
                if (!Schema::hasColumn('pos_users', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('status');
                }
                if (!Schema::hasColumn('pos_users', 'last_login_at')) {
                    $table->timestamp('last_login_at')->nullable()->after('remember_token');
                }
            });
        }

        // Create the password reset tokens table
        // This table stores temporary tokens for password reset functionality
        // Tokens expire after a set period for security purposes
        if (!Schema::hasTable('pos_password_reset_tokens')) {
            Schema::create('pos_password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();  // Email of the user requesting reset
                $table->string('token');              // Unique reset token (hashed)
                $table->timestamp('created_at')->nullable();  // Token creation time
            });
        }

        // Create the sessions table for managing user sessions
        // Stores active session data for authenticated users
        if (!Schema::hasTable('pos_sessions')) {
            Schema::create('pos_sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index()->constrained('pos_users')->onDelete('cascade');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_users');
        Schema::dropIfExists('pos_password_reset_tokens');
        Schema::dropIfExists('pos_sessions');
    }
};
