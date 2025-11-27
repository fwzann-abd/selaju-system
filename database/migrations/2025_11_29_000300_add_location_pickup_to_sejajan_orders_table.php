<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sejajan_orders', function (Blueprint $table) {
            $table->string('location_pickup')->nullable()->after('pickup_time');
        });
    }

    public function down(): void
    {
        Schema::table('sejajan_orders', function (Blueprint $table) {
            $table->dropColumn('location_pickup');
        });
    }
};
