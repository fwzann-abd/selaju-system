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
    Schema::create('books', function (Blueprint $table) {
        $table->uuid('uuid')->primary();
        $table->uuid('author_id');

        $table->string('title');
        $table->string('slug')->unique();
        $table->string('subtitle')->nullable();
        $table->text('desc')->nullable();
        $table->string('language')->nullable();
        $table->string('photo')->nullable();
        $table->string('color_hex', 10)->nullable();
        $table->string('filename')->nullable();
        $table->boolean('is_approved')->default(false);
        $table->timestamp('published_at')->nullable();

        $table->timestamps();

        $table->foreign('author_id')->references('uuid')->on('authors')->cascadeOnDelete();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
