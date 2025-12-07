<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to check and drop foreign key if exists
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('perpossagar_books', function (Blueprint $table) {
            // Make author_id nullable
            $table->uuid('author_id')->nullable()->change();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Re-add foreign key
        Schema::table('perpossagar_books', function (Blueprint $table) {
            $table->foreign('author_id')->references('uuid')->on('perpossagar_authors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perpossagar_books', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['author_id']);

            // Make author_id not nullable
            $table->uuid('author_id')->nullable(false)->change();

            // Re-add foreign key
            $table->foreign('author_id')->references('uuid')->on('perpossagar_authors')->cascadeOnDelete();
        });
    }
};
