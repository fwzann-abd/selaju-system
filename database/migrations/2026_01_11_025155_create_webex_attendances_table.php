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
        Schema::create('webex_attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ekskul_id')->constrained('webex_ekskuls', 'id')->onDelete('cascade');
            $table->foreignUuid('pengurus_id')->constrained('webex_pengurus', 'id')->onDelete('cascade');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'sick', 'excused'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webex_attendances');
    }
};
