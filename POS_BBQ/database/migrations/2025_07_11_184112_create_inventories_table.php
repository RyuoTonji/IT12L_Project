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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('name');
            $table->string('supplier')->nullable();
            $table->string('category')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->decimal('sold', 10, 2)->default(0);
            $table->decimal('spoilage', 10, 2)->default(0);
            $table->decimal('stock_in', 10, 2)->default(0);
            $table->decimal('stock_out', 10, 2)->default(0);
            $table->string('unit');
            $table->decimal('reorder_level', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
