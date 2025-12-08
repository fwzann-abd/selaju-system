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
        Schema::create('perpossagar_books', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('author_id')->nullable();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('desc')->nullable();
            $table->string('language')->nullable();
            $table->string('photo')->nullable();
            $table->string('color_hex', 10)->nullable();
            $table->string('filename')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_hero')->default(false);
            $table->integer('hero_order')->nullable();
            $table->bigInteger('read_count')->default(0);
            $table->string('author_name')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->foreign('author_id')->references('uuid')->on('perpossagar_authors')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perpossagar_books');
    }
};
