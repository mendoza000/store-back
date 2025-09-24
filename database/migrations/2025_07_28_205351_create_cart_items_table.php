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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            // Por ahora usamos product_id, luego será product_variant_id cuando implementemos variantes
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->unsigned();
            $table->decimal('price', 10, 2); // Precio al momento de agregar al carrito
            $table->timestamps();
            $table->softDeletes(); // Agregar soporte para soft deletes

            // Índices para mejorar performance
            $table->index(['cart_id', 'product_id']);
            $table->unique(['cart_id', 'product_id']); // Un producto por carrito

            // Foreign key constraint (cuando se cree la tabla products)
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
