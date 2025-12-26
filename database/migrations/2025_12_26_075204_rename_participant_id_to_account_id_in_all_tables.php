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
        // Tables that have participant_id foreign key to rename
        $tables = [
            'sejajans',
            'sejajan_orders',
            'sejajan_cart_items',
            'perpossagar_authors',
            'students',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'participant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    // Drop existing foreign key
                    $table->dropForeign(['participant_id']);
                });

                // Rename the column
                Schema::table($table, function (Blueprint $table) {
                    $table->renameColumn('participant_id', 'account_id');
                });

                // Re-add foreign key with new name
                Schema::table($table, function (Blueprint $table) {
                    $table->foreign('account_id')->references('uuid')->on('accounts')->onDelete('cascade');
                });
            }
        }

        // Update personal_access_tokens tokenable_type
        DB::statement("UPDATE personal_access_tokens SET tokenable_type = 'App\\\\Models\\\\Account' WHERE tokenable_type = 'App\\\\Models\\\\Participant'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the changes
        $tables = [
            'sejajans',
            'sejajan_orders',
            'sejajan_cart_items',
            'perpossagar_authors',
            'students',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'account_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['account_id']);
                });

                Schema::table($table, function (Blueprint $table) {
                    $table->renameColumn('account_id', 'participant_id');
                });

                Schema::table($table, function (Blueprint $table) {
                    $table->foreign('participant_id')->references('uuid')->on('participants')->onDelete('cascade');
                });
            }
        }

        // Revert personal_access_tokens tokenable_type
        DB::statement("UPDATE personal_access_tokens SET tokenable_type = 'App\\\\Models\\\\Participant' WHERE tokenable_type = 'App\\\\Models\\\\Account'");
    }
};
