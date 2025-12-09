<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'participant_id')) {
                $table->uuid('participant_id')->nullable()->after('id')->index();
            }
        });

        if (Schema::hasColumn('students', 'user_id')) {
            DB::statement('UPDATE students SET participant_id = user_id');

            Schema::table('students', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        Schema::table('students', function (Blueprint $table) {
            $table->foreign('participant_id')
                ->references('uuid')
                ->on('participants')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'user_id')) {
                $table->uuid('user_id')->nullable()->after('id');
            }
        });

        DB::statement('UPDATE students SET user_id = participant_id');

        Schema::table('students', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('uuid')
                ->on('participants')
                ->cascadeOnDelete();

            $table->dropForeign(['participant_id']);
            $table->dropColumn('participant_id');
        });
    }
};
