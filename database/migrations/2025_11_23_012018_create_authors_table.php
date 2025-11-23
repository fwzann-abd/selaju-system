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
    Schema::create('authors', function (Blueprint $table) {
        $table->uuid('uuid')->primary();
        $table->uuid('participant_id');
        $table->timestamp('author_at')->nullable();
        $table->timestamps();

        $table->foreign('participant_id')->references('uuid')->on('participants')->cascadeOnDelete();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
