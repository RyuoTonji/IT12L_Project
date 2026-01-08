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
        if (!Schema::hasTable('pos_menu_item_branch')) {
            Schema::create('pos_menu_item_branch', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_item_id')->constrained('pos_menu_items')->onDelete('cascade');
                $table->foreignId('branch_id')->constrained('pos_branches')->onDelete('cascade');
                $table->boolean('is_available')->default(true);
                $table->timestamps();

                // Ensure unique combination of menu_item and branch
                $table->unique(['menu_item_id', 'branch_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_menu_item_branch');
    }
};
