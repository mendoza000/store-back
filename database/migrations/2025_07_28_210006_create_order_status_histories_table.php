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
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Información del cambio de estado
            $table->string('previous_status')->nullable();
            $table->string('new_status');

            // Detalles del cambio
            $table->text('notes')->nullable();
            $table->string('reason')->nullable(); // Cancelación, pago confirmado, etc.

            // Metadatos adicionales
            $table->json('metadata')->nullable(); // Para información adicional como datos de tracking

            // Información de notificación
            $table->boolean('customer_notified')->default(false);
            $table->timestamp('notified_at')->nullable();

            $table->timestamps();

            // Índices para optimización
            $table->index(['order_id', 'created_at']);
            $table->index(['order_id', 'new_status']);
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
