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
        // Drop column if it exists from previous failed migration
        Schema::table('participants', function (Blueprint $table) {
            if (Schema::hasColumn('participants', 'generation_id')) {
                $table->dropColumn('generation_id');
            }
        });

        // Now add it fresh
        Schema::table('participants', function (Blueprint $table) {
            $table->uuid('generation_id')->nullable()->after('school_id');
            $table->foreign('generation_id')->references('id')->on('generations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['generation_id']);
            $table->dropColumn('generation_id');
        });
    }
};
