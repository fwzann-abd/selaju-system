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
        Schema::create('participants', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('nomor_participant')->unique()->nullable();
            $table->uuid('school_id')->nullable();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
            $table->string('username')->unique();
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('email')->unique();
            $table->string('photo')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
