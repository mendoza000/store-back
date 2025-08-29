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
        Schema::table('payments', function (Blueprint $table) {
            // Campos para el rechazo de pagos
            $table->foreignId('rejected_by')->nullable()->after('verified_by')->constrained('users')->onDelete('set null');
            $table->string('rejection_reason')->nullable()->after('rejected_at');
            
            // Notas administrativas
            $table->text('admin_notes')->nullable()->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['rejected_by', 'rejection_reason', 'admin_notes']);
        });
    }
}; 