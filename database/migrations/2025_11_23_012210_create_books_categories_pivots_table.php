<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perpossagar_book_categories_pivots', function (Blueprint $table) {
            $table->uuid('uuid')->default(DB::raw('gen_random_uuid()'))->primary();
            $table->uuid('category_id');
            $table->uuid('book_id');
            $table->timestamps();

            $table->foreign('category_id')->references('uuid')->on('perpossagar_book_categories')->cascadeOnDelete();
            $table->foreign('book_id')->references('uuid')->on('perpossagar_books')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perpossagar_book_categories_pivots');
    }
};
