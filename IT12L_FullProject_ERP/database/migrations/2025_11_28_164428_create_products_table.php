<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create products table
        if (!Schema::hasTable('crm_products')) {
            Schema::create('crm_products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->constrained('crm_branches')->onDelete('cascade');
                $table->foreignId('category_id')->constrained('crm_categories')->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->string('image')->nullable();
                $table->boolean('is_available')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }


    public function down(): void
    {
        Schema::dropIfExists('crm_products');
    }
};