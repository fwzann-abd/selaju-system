<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('tingkat')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('rombel')->nullable();
            $table->string('slug')->unique();
            $table->foreignUuid('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('academic_year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};