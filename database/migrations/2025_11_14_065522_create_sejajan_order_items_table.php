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
        Schema::create('sejajan_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sejajan_order_id')->index();
            $table->uuid('sejajan_product_id')->index();
            $table->integer('qty')->default(1);
            $table->decimal('price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('sejajan_order_id')->references('id')->on('sejajan_orders')->onDelete('cascade');
            $table->foreign('sejajan_product_id')->references('id')->on('sejajan_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sejajan_order_items');
    }
};
