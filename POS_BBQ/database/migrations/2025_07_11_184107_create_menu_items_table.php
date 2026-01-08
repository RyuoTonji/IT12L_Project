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
        if (!Schema::hasTable('pos_menu_items')) {
            Schema::create('pos_menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->nullable()->constrained('pos_branches')->onDelete('cascade');
                $table->foreignId('category_id')->constrained('pos_categories')->onDelete('cascade');
                $table->string('name', 200);
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->boolean('is_available')->default(true);
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
        Schema::dropIfExists('products');
    }
};
