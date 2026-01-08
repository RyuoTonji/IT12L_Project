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
            $table->foreignId('approved_by')->nullable()->constrained('crm_users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            if (!Schema::hasColumn('crm_orders', 'address')) {
                $table->string('address')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_by', 'approved_at']);
        });
    }
};
