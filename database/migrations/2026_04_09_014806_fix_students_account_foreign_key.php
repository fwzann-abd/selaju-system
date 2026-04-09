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
        Schema::table('students', function (Blueprint $table) {
            // Drop the old foreign key that references 'participants' (now 'accounts')
            $table->dropForeign(['account_id']);

            // Re-add the foreign key to reference 'accounts' table
            $table->foreign('account_id')->references('uuid')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['account_id']);

            // Re-add the foreign key to reference 'participants' (for rollback)
            $table->foreign('account_id')->references('uuid')->on('participants')->onDelete('cascade');
        });
    }
};
