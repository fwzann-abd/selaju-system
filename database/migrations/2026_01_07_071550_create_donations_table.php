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
        Schema::create('donations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('donor_name');
            $table->string('donor_ig')->nullable();
            $table->decimal('amount', 15, 2);
            $table->text('message')->nullable();
            $table->string('payment_method'); // qris, virtual_account, manual_transfer
            $table->string('payment_status')->default('pending'); // pending, paid, failed, expired
            $table->string('transaction_id')->nullable()->unique(); // DOKU transaction ID
            $table->text('payment_data')->nullable(); // JSON data dari DOKU
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('payment_status');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
