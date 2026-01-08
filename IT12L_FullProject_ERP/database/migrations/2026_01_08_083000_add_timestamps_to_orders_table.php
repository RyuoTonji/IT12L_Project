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
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->timestamp('preparing_at')->nullable()->after('approved_at');
            $table->timestamp('ready_at')->nullable()->after('preparing_at');
            $table->timestamp('picked_up_at')->nullable()->after('ready_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->dropColumn(['preparing_at', 'ready_at', 'picked_up_at']);
        });
    }
};
