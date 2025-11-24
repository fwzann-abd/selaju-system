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
        Schema::create('sejajan_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sejajan_id')->index();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('sejajan_id')->references('id')->on('sejajans')->onDelete('cascade');
            $table->unique(['sejajan_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sejajan_products');
    }
};
