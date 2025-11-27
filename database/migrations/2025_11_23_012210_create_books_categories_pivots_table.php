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
    Schema::create('books_categories_pivots', function (Blueprint $table) {
        $table->uuid('uuid')->primary();
        $table->uuid('category_id');
        $table->uuid('book_id');
        $table->timestamps();

        $table->foreign('category_id')->references('uuid')->on('books_category')->cascadeOnDelete();
        $table->foreign('book_id')->references('uuid')->on('books')->cascadeOnDelete();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_categories_pivots');
    }
};
