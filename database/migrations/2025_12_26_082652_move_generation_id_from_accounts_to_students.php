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
        // Step 1: Add generation_id to students table after school_id
        Schema::table('students', function (Blueprint $table) {
            $table->uuid('generation_id')->nullable()->after('school_id');
            $table->foreign('generation_id')->references('id')->on('generations')->nullOnDelete();
        });

        // Step 2: Fill all students with the specified generation_id
        $generationId = '019b00af-ebbf-715b-8d6f-aac03065f014';
        \DB::table('students')->update(['generation_id' => $generationId]);

        // Step 3: Drop foreign keys from accounts table using raw SQL (old constraint names)
        \DB::statement('ALTER TABLE accounts DROP CONSTRAINT IF EXISTS participants_school_id_foreign');
        \DB::statement('ALTER TABLE accounts DROP CONSTRAINT IF EXISTS participants_generation_id_foreign');
        \DB::statement('ALTER TABLE accounts DROP CONSTRAINT IF EXISTS accounts_school_id_foreign');
        \DB::statement('ALTER TABLE accounts DROP CONSTRAINT IF EXISTS accounts_generation_id_foreign');

        // Step 4: Drop school_id and generation_id columns from accounts table
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['school_id', 'generation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Re-add columns to accounts table
        Schema::table('accounts', function (Blueprint $table) {
            $table->uuid('school_id')->nullable()->after('nomor_participant');
            $table->uuid('generation_id')->nullable()->after('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
            $table->foreign('generation_id')->references('id')->on('generations')->nullOnDelete();
        });

        // Step 2: Remove generation_id from students table
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['generation_id']);
            $table->dropColumn('generation_id');
        });
    }
};
