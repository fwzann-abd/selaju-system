<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_group_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_group_id');
            $table->uuid('module_access_id');
            $table->boolean('status')->default(false);
            $table->timestamps();

            $table->index(['user_group_id', 'module_access_id'], 'ug_module_access_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_group_permissions');
    }
};
