<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('personal_access_tokens', 'tokenable_id')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                // The morphs('tokenable') method automatically creates an index named 'personal_access_tokens_tokenable_type_tokenable_id_index'
                $table->dropIndex('personal_access_tokens_tokenable_type_tokenable_id_index');
            });

            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->dropColumn('tokenable_id');
            });
        }

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->uuid('tokenable_id')->nullable()->after('tokenable_type');
            $table->index(['tokenable_type', 'tokenable_id'], 'tokenable_type_tokenable_id_index');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('personal_access_tokens', 'tokenable_id')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->dropIndex('tokenable_type_tokenable_id_index');
            });

            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->dropColumn('tokenable_id');
            });
        }

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('tokenable_id')->after('tokenable_type');
            $table->index(['tokenable_type', 'tokenable_id'], 'personal_access_tokens_tokenable_type_tokenable_id_index');
        });
    }
};
