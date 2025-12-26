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
        // Rename the participants table to accounts
        Schema::rename('participants', 'accounts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename back to participants
        Schema::rename('accounts', 'participants');
    }
};
