<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change tokenable_id from integer to varchar so it can store UUIDs.
        // Use raw SQL to avoid requiring doctrine/dbal.
        DB::statement("ALTER TABLE personal_access_tokens MODIFY tokenable_id VARCHAR(191) NOT NULL") ;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to unsigned bigint (may fail if data cannot be cast back)
        DB::statement("ALTER TABLE personal_access_tokens MODIFY tokenable_id BIGINT UNSIGNED NOT NULL") ;
    }
};
