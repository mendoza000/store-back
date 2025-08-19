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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('type', ['mobile_payment', 'bank_transfer', 'paypal', 'cash', 'crypto']);
            $table->string('account_info');
            $table->string('instructions')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');

            // Información del pago
            $table->decimal('amount', 10, 2);
            $table->string('reference_number')->unique()->index();
            $table->string('receipt_url')->nullable();
            $table->string('notes')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'refunded'])->default('pending')->index();

            // Fechas importantes
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            $table->timestamps();
        });

        Schema::create('payment_verifications', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('action')->comment('Action taken on the payment verification, e.g., "verified", "rejected"');
            $table->text('notes')->nullable()->comment('Additional notes regarding the verification action');

            // Fechas importantes
            $table->timestamp('actioned_at')->useCurrent();
            
            $table->json('verification_history')->nullable()->comment('JSON field to store the history of verification actions');
            $table->text('reasons_rejection')->nullable()->comment('Reasons for rejection if the payment is rejected');

            // Índices para optimización de consultas
            $table->index(['payment_id', 'user_id']);
            $table->index('actioned_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_verifications');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
    }
};
