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
        Schema::table('shift_reports', function (Blueprint $table) {
            $table->timestamp('clock_in_time')->nullable()->after('shift_date');
            $table->timestamp('clock_out_time')->nullable()->after('clock_in_time');
            $table->decimal('total_hours', 8, 2)->nullable()->after('clock_out_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_reports', function (Blueprint $table) {
            $table->dropColumn(['clock_in_time', 'clock_out_time', 'total_hours']);
        });
    }
};
