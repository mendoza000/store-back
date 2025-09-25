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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            // Cambiado a UUID para coincidir con el tipo de products.id
            $table->uuid('product_id');

            // Información del pedido
            $table->integer('quantity')->unsigned();
            $table->decimal('price', 10, 2); // Precio unitario al momento del pedido

            // Snapshot del producto para preservar información histórica
            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->string('product_sku')->nullable();
            $table->string('product_image')->nullable(); // URL de la imagen principal

            // Información de variante (JSON para flexibilidad)
            $table->json('variant_info')->nullable(); // Color, talla, etc.

            // Metadatos adicionales
            $table->json('metadata')->nullable(); // Para información adicional

            $table->timestamps();
            $table->softDeletes();

            // Índices para optimización
            $table->index(['order_id', 'product_id']);
            $table->index('product_id');

            // Foreign key constraint para products
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
