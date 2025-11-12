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
        // Remove user_uuid from sessions and restore user_id
        if (Schema::hasTable('sessions')) {
            if (Schema::hasColumn('sessions', 'user_uuid')) {
                try {
                    Schema::table('sessions', function (Blueprint $table) {
                        // drop foreign if exists
                        try { $table->dropForeign(['user_uuid']); } catch (\Throwable $e) {}
                        // drop the column
                        try { $table->dropColumn('user_uuid'); } catch (\Throwable $e) {}
                    });
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            // Add user_id back if missing
            if (!Schema::hasColumn('sessions', 'user_id')) {
                Schema::table('sessions', function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                });
            }
        }

        // Remove uuid column from users if exists
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                try { $table->dropUnique(['uuid']); } catch (\Throwable $e) {}
                try { $table->dropColumn('uuid'); } catch (\Throwable $e) {}
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add uuid column to users and user_uuid in sessions (best-effort)
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('uuid')->after('id')->unique()->nullable();
            });
        }

        if (Schema::hasTable('sessions')) {
            if (!Schema::hasColumn('sessions', 'user_uuid')) {
                Schema::table('sessions', function (Blueprint $table) {
                    $table->uuid('user_uuid')->nullable()->after('id')->index();
                });

                try {
                    Schema::table('sessions', function (Blueprint $table) {
                        $table->foreign('user_uuid')->references('uuid')->on('users')->onDelete('set null');
                    });
                } catch (\Throwable $e) {}
            }

            // remove user_id if we added it in up()
            if (Schema::hasColumn('sessions', 'user_id')) {
                try {
                    Schema::table('sessions', function (Blueprint $table) {
                        $table->dropForeign(['user_id']);
                        $table->dropColumn('user_id');
                    });
                } catch (\Throwable $e) {}
            }
        }
    }
};
