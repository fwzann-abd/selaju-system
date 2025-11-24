<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration will:
     *  - backfill `id` from `uuid` if necessary
     *  - make `id` NOT NULL
     *  - drop existing primary key on `uuid`
     *  - set `id` as the new primary key
     *  - drop the `uuid` column
     *
     * WARNING: This performs structural changes. Run on a backup or dev first.
     */
    public function up(): void
    {
        // Backfill id where empty
        DB::statement("UPDATE participants SET id = uuid WHERE id IS NULL");

        // Make id NOT NULL
        try {
            // MySQL syntax
            DB::statement("ALTER TABLE participants MODIFY id VARCHAR(36) NOT NULL");
        } catch (\Throwable $e) {
            // ignore - non-fatal on some platforms
        }

        // Drop unique index on id if exists (index name may vary)
        try {
            DB::statement('ALTER TABLE participants DROP INDEX participants_id_unique');
        } catch (\Throwable $e) {
            try { DB::statement('ALTER TABLE participants DROP INDEX id'); } catch (\Throwable $e) { /* ignore */ }
        }

        // Drop primary key on uuid
        try {
            DB::statement('ALTER TABLE participants DROP PRIMARY KEY');
        } catch (\Throwable $e) {
            // ignore
        }

        // Add primary key on id
        try {
            DB::statement('ALTER TABLE participants ADD PRIMARY KEY (id)');
        } catch (\Throwable $e) {
            // ignore
        }

        // Finally drop uuid column
        if (Schema::hasColumn('participants', 'uuid')) {
            Schema::table('participants', function (Blueprint $table) {
                try {
                    $table->dropColumn('uuid');
                } catch (\Throwable $e) {
                    // ignore
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     * Best-effort: recreate `uuid` from `id` and set it as primary.
     */
    public function down(): void
    {
        // Add uuid column back as nullable
        if (!Schema::hasColumn('participants', 'uuid')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
            });

            // Copy id -> uuid where null
            DB::statement("UPDATE participants SET uuid = id WHERE uuid IS NULL");

            // Try to drop primary key on id and set uuid primary
            try {
                DB::statement('ALTER TABLE participants DROP PRIMARY KEY');
            } catch (\Throwable $e) {
                // ignore
            }
            try {
                DB::statement('ALTER TABLE participants ADD PRIMARY KEY (uuid)');
            } catch (\Throwable $e) {
                // ignore
            }

            // Optionally drop id primary/unique if present (leave id column)
        }
    }
};
