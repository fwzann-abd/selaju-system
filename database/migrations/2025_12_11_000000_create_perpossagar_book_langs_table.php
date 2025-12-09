<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perpossagar_book_langs', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::table('perpossagar_books', function (Blueprint $table) {
            $table->uuid('language_id')->nullable()->after('author_id');
            $table->foreign('language_id')->references('uuid')->on('perpossagar_book_langs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('perpossagar_books', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropColumn('language_id');
        });

        Schema::dropIfExists('perpossagar_book_langs');
    }
};
