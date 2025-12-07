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
            $table->boolean('is_hero')->default(false)->after('is_approved');
            $table->integer('hero_order')->nullable()->after('is_hero');
            $table->bigInteger('read_count')->default(0)->after('hero_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perpossagar_books', function (Blueprint $table) {
            $table->dropColumn(['is_hero', 'hero_order', 'read_count']);
        });
    }
};
