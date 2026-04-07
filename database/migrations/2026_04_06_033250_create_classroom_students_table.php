<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classroom_students', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('classroom_id')->constrained()->cascadeOnDelete();
            // Using constrained('students') since the students table exists and has a UUID PK
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('student_position_id')->nullable()->constrained('student_positions')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_students');
    }
};