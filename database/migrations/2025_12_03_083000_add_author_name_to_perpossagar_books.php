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
        Schema::table('perpossagar_books', function (Blueprint $table) {
            if (!Schema::hasColumn('perpossagar_books', 'author_name')) {
                $table->string('author_name')->nullable()->after('author_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perpossagar_books', function (Blueprint $table) {
            if (Schema::hasColumn('perpossagar_books', 'author_name')) {
                $table->dropColumn('author_name');
            }
        });
    }
};
