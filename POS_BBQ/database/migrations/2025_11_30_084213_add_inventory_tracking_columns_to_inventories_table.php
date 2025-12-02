<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->decimal('sold', 10, 2)->default(0)->after('quantity');
            $table->decimal('spoilage', 10, 2)->default(0)->after('sold');
            $table->decimal('stock_in', 10, 2)->default(0)->after('spoilage');
            $table->decimal('stock_out', 10, 2)->default(0)->after('stock_in');
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['sold', 'spoilage', 'stock_in', 'stock_out']);
        });
    }
};
