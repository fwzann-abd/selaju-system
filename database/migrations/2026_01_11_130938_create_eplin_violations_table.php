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
        Schema::create('eplin_violations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('violation_type_id');
            $table->uuid('recorded_by_officer_id');
            $table->date('violation_date');
            $table->text('description')->nullable();
            $table->text('evidence')->nullable();
            $table->enum('status', ['recorded', 'under_review', 'verified', 'dismissed'])->default('recorded');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('violation_type_id')->references('id')->on('eplin_violation_types')->onDelete('cascade');
            $table->foreign('recorded_by_officer_id')->references('id')->on('eplin_officers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eplin_violations');
    }
};
