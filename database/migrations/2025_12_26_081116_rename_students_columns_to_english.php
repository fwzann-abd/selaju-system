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
        Schema::table('students', function (Blueprint $table) {
            // Rename columns to English
            $table->renameColumn('nama', 'name');
            $table->renameColumn('nipd', 'student_number'); // Nomor Induk Peserta Didik
            $table->renameColumn('nisn', 'national_id'); // Nomor Induk Siswa Nasional
            $table->renameColumn('jk', 'gender'); // Jenis Kelamin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Rename back to Indonesian
            $table->renameColumn('name', 'nama');
            $table->renameColumn('student_number', 'nipd');
            $table->renameColumn('national_id', 'nisn');
            $table->renameColumn('gender', 'jk');
        });
    }
};
