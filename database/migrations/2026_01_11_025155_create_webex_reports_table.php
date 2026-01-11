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
        Schema::create('webex_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ekskul_id')->constrained('webex_ekskuls', 'id')->onDelete('cascade');
            $table->foreignUuid('created_by')->constrained('students', 'id')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->date('activity_date');
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webex_reports');
    }
};
