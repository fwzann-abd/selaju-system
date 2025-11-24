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
        // Add UUID column to users table if it doesn't exist
        if (!Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('uuid')->after('id')->unique();

                // Only add user_group_id if it doesn't exist
                if (!Schema::hasColumn('users', 'user_group_id')) {
                    $table->foreignUuid('user_group_id')->nullable()->after('password');
                }
            });

            // Generate UUIDs for existing users (only for rows where uuid is null)
            DB::table('users')->whereNull('uuid')->update([
                'uuid' => DB::raw('UUID()')
            ]);
        }

        // Update sessions table to use UUID if necessary
        if (Schema::hasTable('sessions')) {
            // Only modify sessions.user_id -> user_uuid if user_id exists
            if (Schema::hasColumn('sessions', 'user_id')) {
                try {
                    Schema::table('sessions', function (Blueprint $table) {
                        // Drop foreign if exists (best-effort)
                        try {
                            $table->dropForeign(['user_id']);
                        } catch (\Throwable $e) {
                            // ignore if foreign key does not exist
                        }

                        // Drop user_id column if present
                        try {
                            $table->dropColumn('user_id');
                        } catch (\Throwable $e) {
                            // ignore
                        }

                        if (!Schema::hasColumn('sessions', 'user_uuid')) {
                            $table->uuid('user_uuid')->nullable()->after('id')->index();
                        }
                    });

                    // Add foreign key if user_uuid exists
                    if (Schema::hasColumn('sessions', 'user_uuid') && Schema::hasColumn('users', 'uuid')) {
                        try {
                            Schema::table('sessions', function (Blueprint $table) {
                                $table->foreign('user_uuid')->references('uuid')->on('users')->onDelete('set null');
                            });
                        } catch (\Throwable $e) {
                            // ignore if fk exists
                        }
                    }
                } catch (\Throwable $e) {
                    // best-effort: ignore any issue modifying sessions table
                }
            }
        }

        // Add foreign key constraint for user_group_id if column exists and fk not present
        if (Schema::hasColumn('users', 'user_group_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('set null');
                });
            } catch (\Throwable $e) {
                // ignore if fk already exists
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key constraints
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_group_id']);
            $table->dropColumn(['uuid', 'user_group_id']);
        });

        // Restore sessions table
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_uuid']);
            $table->dropColumn('user_uuid');
            $table->foreignId('user_id')->nullable()->after('id')->index();
        });
    }
};
