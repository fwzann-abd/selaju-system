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
    Schema::create('book_reviews', function (Blueprint $table) {
        $table->uuid('uuid')->primary();
        $table->uuid('participant_id');
        $table->uuid('book_id');

        $table->text('desc')->nullable();
        $table->integer('rate')->default(0);

        $table->timestamps();

        $table->foreign('participant_id')->references('uuid')->on('participants')->cascadeOnDelete();
        $table->foreign('book_id')->references('uuid')->on('books')->cascadeOnDelete();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reviews');
    }
};
