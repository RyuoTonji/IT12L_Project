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
        if (!Schema::hasTable('pos_deleted_data')) {
            Schema::create('pos_deleted_data', function (Blueprint $table) {
                $table->id();
                $table->string('table_name');
                $table->unsignedBigInteger('record_id');
                $table->json('data');
                $table->timestamp('deleted_at');
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_deleted_data');
    }
};
