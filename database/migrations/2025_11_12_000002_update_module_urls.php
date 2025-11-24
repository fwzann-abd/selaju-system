<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Replace any module URL starting with /dashboard to /admin
        DB::statement("UPDATE modules SET url = REPLACE(url, '/dashboard', '/admin') WHERE url LIKE '/dashboard%'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert admin back to dashboard if needed
        DB::statement("UPDATE modules SET url = REPLACE(url, '/admin', '/dashboard') WHERE url LIKE '/admin%'");
    }
};
