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
    Schema::table('donations', function (Blueprint $table) {
      if (Schema::hasColumn('donations', 'user_id')) {
        $table->dropForeign(['user_id']); // This might fail if key name differs
        $table->dropColumn('user_id');
      }

      if (!Schema::hasColumn('donations', 'account_id')) {
        $table->uuid('account_id')->nullable()->after('id');
        $table->foreign('account_id')->references('uuid')->on('accounts')->nullOnDelete();
      }
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('donations', function (Blueprint $table) {
      if (Schema::hasColumn('donations', 'account_id')) {
        $table->dropForeign(['account_id']);
        $table->dropColumn('account_id');
      }
    });
  }
};
