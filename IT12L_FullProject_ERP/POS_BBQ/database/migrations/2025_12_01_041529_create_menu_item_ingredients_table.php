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
        Schema::create('menu_item_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->decimal('quantity_needed', 10, 2); // Amount of ingredient needed per menu item
            $table->string('unit')->nullable(); // Unit of measurement (e.g., grams, pieces)
            $table->timestamps();

            // Ensure unique combination
            $table->unique(['menu_item_id', 'inventory_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_ingredients');
    }
};
