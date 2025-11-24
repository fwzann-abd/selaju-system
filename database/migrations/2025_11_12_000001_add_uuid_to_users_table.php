<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add nullable uuid column and unique index. Backfill existing rows with DB UUID() values.
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id')->unique();
        });

        // Backfill existing users with UUIDs. Using DB::raw('UUID()') so each row gets a unique value.
        DB::table('users')->whereNull('uuid')->update(['uuid' => DB::raw('UUID()')]);

        // Optionally, you could alter the column to be not nullable, but that requires doctrine/dbal.
        // We'll leave it nullable to avoid adding dependencies in this migration.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
