<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('schools', 'account_id')) {
            return;
        }

        Schema::table('schools', function (Blueprint $table) {
            $table->uuid('account_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('schools', 'account_id')) {
            return;
        }

        Schema::table('schools', function (Blueprint $table) {
            $table->uuid('account_id')->nullable(false)->change();
        });
    }
};
