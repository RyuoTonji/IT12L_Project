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
        if (!Schema::hasTable('pos_tables')) {
            Schema::create('pos_tables', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->nullable()->constrained('pos_branches')->onDelete('cascade');
                $table->string('name');
                $table->integer('capacity');
                $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
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
        Schema::dropIfExists('pos_tables');
    }
};
