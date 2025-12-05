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
        // Drop and recreate the pivot table with proper structure
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::dropIfExists('perpossagar_book_categories_pivots');

        Schema::create('perpossagar_book_categories_pivots', function (Blueprint $table) {
            $table->uuid('book_id');
            $table->uuid('category_id');
            $table->timestamps();

            $table->primary(['book_id', 'category_id']);
            $table->foreign('book_id')->references('uuid')->on('perpossagar_books')->cascadeOnDelete();
            $table->foreign('category_id')->references('uuid')->on('perpossagar_book_categories')->cascadeOnDelete();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::dropIfExists('perpossagar_book_categories_pivots');

        Schema::create('perpossagar_book_categories_pivots', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('category_id');
            $table->uuid('book_id');
            $table->timestamps();

            $table->foreign('category_id')->references('uuid')->on('perpossagar_book_categories')->cascadeOnDelete();
            $table->foreign('book_id')->references('uuid')->on('perpossagar_books')->cascadeOnDelete();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
