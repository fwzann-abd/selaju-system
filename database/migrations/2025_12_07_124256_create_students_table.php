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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('participants')->onDelete('cascade');
            $table->foreignUuid('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('nama');
            $table->string('nipd')->nullable();
            $table->string('nisn')->unique();
            $table->enum('jk', ['L', 'P']);
            $table->timestamps();

            $table->index('nisn');
            $table->index('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
