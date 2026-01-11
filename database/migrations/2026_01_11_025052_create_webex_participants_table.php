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
        Schema::create('webex_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ekskul_id')->constrained('webex_ekskuls', 'id')->onDelete('cascade');
            $table->foreignUuid('student_id')->constrained('students', 'id')->onDelete('cascade');
            $table->enum('status', ['registered', 'active', 'inactive'])->default('active');
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: satu student hanya bisa daftar 1x per ekskul
            $table->unique(['ekskul_id', 'student_id'], 'webex_participants_ekskul_student_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webex_participants');
    }
};
