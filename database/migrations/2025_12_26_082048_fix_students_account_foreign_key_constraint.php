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
        Schema::table('students', function (Blueprint $table) {
            // Drop existing foreign key with cascade delete
            $table->dropForeign(['account_id']);

            // Re-add foreign key with nullOnDelete instead of cascadeOnDelete
            // This prevents students from being deleted when account is deleted
            $table->foreign('account_id')
                ->references('uuid')
                ->on('accounts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop the null on delete foreign key
            $table->dropForeign(['account_id']);

            // Re-add with cascade delete
            $table->foreign('account_id')
                ->references('uuid')
                ->on('accounts')
                ->cascadeOnDelete();
        });
    }
};
