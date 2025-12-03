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
        // Only rename if the old table exists
        if (Schema::hasTable('books')) {
            Schema::rename('books', 'perpossagar_books');
        }
        if (Schema::hasTable('books_category')) {
            Schema::rename('books_category', 'perpossagar_book_categories');
        }
        if (Schema::hasTable('books_categories_pivots')) {
            Schema::rename('books_categories_pivots', 'perpossagar_book_categories_pivots');
        }
        if (Schema::hasTable('book_reviews')) {
            Schema::rename('book_reviews', 'perpossagar_book_reviews');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('perpossagar_books')) {
            Schema::rename('perpossagar_books', 'books');
        }
        if (Schema::hasTable('perpossagar_book_categories')) {
            Schema::rename('perpossagar_book_categories', 'books_category');
        }
        if (Schema::hasTable('perpossagar_book_categories_pivots')) {
            Schema::rename('perpossagar_book_categories_pivots', 'books_categories_pivots');
        }
        if (Schema::hasTable('perpossagar_book_reviews')) {
            Schema::rename('perpossagar_book_reviews', 'book_reviews');
        }
    }
};
