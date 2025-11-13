<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This will add a new `id` VARCHAR(36) column to participants,
     * copy existing `uuid` values into it, and add a unique index.
     * We intentionally keep `uuid` as the primary key for now.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('participants', 'id')) {
            Schema::table('participants', function (Blueprint $table) {
                // add a nullable string column first (no change to primary key)
                $table->string('id', 36)->nullable()->after('uuid');
            });

            // Copy uuid values into id for existing rows
            // Use raw statement for performance and to avoid model side-effects
            DB::statement("UPDATE participants SET id = uuid WHERE id IS NULL");

            // Add unique index on id so future code can rely on uniqueness.
            // We don't change primary key in this migration.
            Schema::table('participants', function (Blueprint $table) {
                $table->unique('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('participants', 'id')) {
            Schema::table('participants', function (Blueprint $table) {
                // drop unique index then drop column
                try {
                    $table->dropUnique(['id']);
                } catch (\Throwable $e) {
                    // ignore if index doesn't exist
                }
                $table->dropColumn('id');
            });
        }
    }
};
