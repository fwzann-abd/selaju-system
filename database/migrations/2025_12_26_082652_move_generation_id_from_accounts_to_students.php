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
        // Step 1: Add generation_id to students table after school_id
        Schema::table('students', function (Blueprint $table) {
            $table->uuid('generation_id')->nullable()->after('school_id');
            $table->foreign('generation_id')->references('id')->on('generations')->nullOnDelete();
        });

        // Step 2: Fill all students with the specified generation_id
        $generationId = '019b00af-ebbf-715b-8d6f-aac03065f014';
        \DB::table('students')->update(['generation_id' => $generationId]);

        // Step 3: Drop foreign keys from accounts table using raw SQL (old constraint names)
        // Handle SQLite differently since it doesn't support DROP CONSTRAINT
        if (\DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table without the foreign key columns
            \DB::statement('PRAGMA foreign_keys=off');
            \DB::statement('ALTER TABLE accounts RENAME TO accounts_old');
            \DB::statement('CREATE TABLE accounts (
                uuid TEXT PRIMARY KEY,
                nomor_participant TEXT UNIQUE,
                username TEXT UNIQUE,
                birth_date DATE,
                no_telp TEXT,
                email TEXT UNIQUE,
                email_verified_at DATETIME,
                photo TEXT,
                password TEXT,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME,
                updated_at DATETIME
            )');
            \DB::statement('INSERT INTO accounts SELECT uuid, nomor_participant, username, birth_date, no_telp, email, email_verified_at, photo, password, is_active, created_at, updated_at FROM accounts_old');
            \DB::statement('DROP TABLE accounts_old');
            \DB::statement('PRAGMA foreign_keys=on');
        } else {
            \DB::statement('ALTER TABLE accounts DROP CONSTRAINT IF EXISTS participants_school_id_foreign');
            \DB::statement('ALTER TABLE accounts DROP CONSTRAINT IF EXISTS participants_generation_id_foreign');
            \DB::statement('ALTER TABLE accounts DROP CONSTRAINT IF EXISTS accounts_school_id_foreign');
            \DB::statement('ALTER TABLE accounts DROP CONSTRAINT IF EXISTS accounts_generation_id_foreign');
        }

        // Step 4: Drop school_id and generation_id columns from accounts table (skip for SQLite as we recreated the table)
        if (\DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn(['school_id', 'generation_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Re-add columns to accounts table
        if (\DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, recreate table with the columns
            \DB::statement('PRAGMA foreign_keys=off');
            \DB::statement('ALTER TABLE accounts RENAME TO accounts_old');
            \DB::statement('CREATE TABLE accounts (
                uuid TEXT PRIMARY KEY,
                nomor_participant TEXT UNIQUE,
                school_id TEXT,
                generation_id TEXT,
                username TEXT UNIQUE,
                birth_date DATE,
                no_telp TEXT,
                email TEXT UNIQUE,
                email_verified_at DATETIME,
                photo TEXT,
                password TEXT,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE SET NULL,
                FOREIGN KEY (generation_id) REFERENCES generations(id) ON DELETE SET NULL
            )');
            \DB::statement('INSERT INTO accounts SELECT uuid, nomor_participant, NULL as school_id, NULL as generation_id, username, birth_date, no_telp, email, email_verified_at, photo, password, is_active, created_at, updated_at FROM accounts_old');
            \DB::statement('DROP TABLE accounts_old');
            \DB::statement('PRAGMA foreign_keys=on');
        } else {
            Schema::table('accounts', function (Blueprint $table) {
                $table->uuid('school_id')->nullable()->after('nomor_participant');
                $table->uuid('generation_id')->nullable()->after('school_id');
                $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
                $table->foreign('generation_id')->references('id')->on('generations')->nullOnDelete();
            });
        }

        // Step 2: Remove generation_id from students table
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['generation_id']);
            $table->dropColumn('generation_id');
        });
    }
};
