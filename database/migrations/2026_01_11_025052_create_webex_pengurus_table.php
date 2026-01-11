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
        Schema::create('webex_pengurus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ekskul_id')->constrained('webex_ekskuls', 'id')->onDelete('cascade');
            $table->foreignUuid('student_id')->constrained('students', 'id')->onDelete('cascade');
            $table->enum('role', ['ketua', 'wakil', 'bendahara', 'sekretaris', 'anggota'])->default('anggota');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webex_pengurus');
    }
};
