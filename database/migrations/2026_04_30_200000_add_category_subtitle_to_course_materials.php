<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            $table->enum('category', ['document', 'image', 'video', 'other'])->default('document')->after('file_type');
            $table->string('subtitle_path')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            $table->dropColumn(['category', 'subtitle_path']);
        });
    }
};
