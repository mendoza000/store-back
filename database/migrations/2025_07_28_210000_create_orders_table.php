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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Información básica del pedido
            $table->string('order_number', 50)->unique()->index();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Estados del pedido: pending, paid, processing, shipped, delivered, cancelled
            $table->enum('status', [
                'pending',
                'paid',
                'processing',
                'shipped',
                'delivered',
                'cancelled'
            ])->default('pending')->index();

            // Información monetaria
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Direcciones (JSON para flexibilidad)
            $table->json('shipping_address');
            $table->json('billing_address');

            // Metadatos adicionales
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Para información adicional flexible

            // Fechas importantes
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices para optimización de consultas
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('paid_at');
            $table->index('shipped_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
