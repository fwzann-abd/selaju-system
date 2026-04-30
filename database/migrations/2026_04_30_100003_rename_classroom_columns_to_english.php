<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            if (Schema::hasColumn('classrooms', 'tingkat')) {
                $table->renameColumn('tingkat', 'level');
            }
            if (Schema::hasColumn('classrooms', 'jurusan')) {
                $table->renameColumn('jurusan', 'major');
            }
            if (Schema::hasColumn('classrooms', 'rombel')) {
                $table->renameColumn('rombel', 'group_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            if (Schema::hasColumn('classrooms', 'level')) {
                $table->renameColumn('level', 'tingkat');
            }
            if (Schema::hasColumn('classrooms', 'major')) {
                $table->renameColumn('major', 'jurusan');
            }
            if (Schema::hasColumn('classrooms', 'group_number')) {
                $table->renameColumn('group_number', 'rombel');
            }
        });
    }
};
