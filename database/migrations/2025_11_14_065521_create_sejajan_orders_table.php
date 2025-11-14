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
        Schema::create('sejajan_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sejajan_id')->index();
            $table->uuid('participant_id')->index();
            $table->enum('status', ['pending', 'paid', 'processing', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('pickup_time')->nullable();
            $table->timestamps();

            $table->foreign('sejajan_id')->references('id')->on('sejajans')->onDelete('cascade');
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sejajan_orders');
    }
};
