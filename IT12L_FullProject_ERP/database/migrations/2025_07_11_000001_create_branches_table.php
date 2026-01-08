<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('crm_branches')) {
            Schema::create('crm_branches', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('code')->nullable();
                $table->string('address', 255)->nullable();
                $table->string('phone', 20)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_branches');
    }
};