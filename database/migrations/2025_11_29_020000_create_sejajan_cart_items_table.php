<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sejajan_cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('participant_id')->index();
            $table->uuid('sejajan_id')->index();
            $table->uuid('sejajan_product_id')->index();
            $table->integer('qty')->default(1);
            $table->timestamps();

            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
            $table->foreign('sejajan_id')->references('id')->on('sejajans')->onDelete('cascade');
            $table->foreign('sejajan_product_id')->references('id')->on('sejajan_products')->onDelete('cascade');
            $table->unique(['participant_id', 'sejajan_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sejajan_cart_items');
    }
};
