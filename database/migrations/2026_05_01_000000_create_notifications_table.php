<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id'); // references accounts.uuid
            $table->string('type'); // assignment_new, assignment_graded, attendance_recorded, material_uploaded, announcement, submission_received
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('link')->nullable(); // frontend route e.g. /assignments
            $table->json('data')->nullable();    // extra payload (icon, actor name, etc)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'read_at']);
            $table->index(['account_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
