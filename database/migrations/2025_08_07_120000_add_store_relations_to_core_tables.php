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
        // Asegurar soft deletes en la tabla store
        Schema::table('store', function (Blueprint $table) {
            if (!Schema::hasColumn('store', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Users -> store_id (nullable, set null on delete)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'store_id')) {
                $table->uuid('store_id')->nullable()->after('id');
                $table->foreign('store_id')->references('id')->on('store')->nullOnDelete();
                $table->index('store_id');
            }
        });

        // Categories -> store_id
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'store_id')) {
                $table->uuid('store_id')->nullable()->after('id');
                $table->foreign('store_id')->references('id')->on('store')->nullOnDelete();
                $table->index('store_id');
            }
        });

        // Products -> store_id
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'store_id')) {
                $table->uuid('store_id')->nullable()->after('id');
                $table->foreign('store_id')->references('id')->on('store')->nullOnDelete();
                $table->index('store_id');
            }
        });

        // Carts -> store_id (cada carrito pertenece a una tienda)
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'store_id')) {
                $table->uuid('store_id')->nullable()->after('id');
                $table->foreign('store_id')->references('id')->on('store')->nullOnDelete();
                $table->index('store_id');
            }
        });

        // Orders -> store_id
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'store_id')) {
                $table->uuid('store_id')->nullable()->after('id');
                $table->foreign('store_id')->references('id')->on('store')->nullOnDelete();
                $table->index('store_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Orders: quitar store_id
        Schema::table('orders', function (Blueprint $table) {
            try {
                $table->dropForeign(['store_id']);
            } catch (\Throwable $e) {
            }
            if (Schema::hasColumn('orders', 'store_id')) {
                $table->dropColumn('store_id');
            }
        });

        // Carts: quitar store_id
        Schema::table('carts', function (Blueprint $table) {
            try {
                $table->dropForeign(['store_id']);
            } catch (\Throwable $e) {
            }
            if (Schema::hasColumn('carts', 'store_id')) {
                $table->dropColumn('store_id');
            }
        });

        // Products: quitar store_id
        Schema::table('products', function (Blueprint $table) {
            try {
                $table->dropForeign(['store_id']);
            } catch (\Throwable $e) {
            }
            if (Schema::hasColumn('products', 'store_id')) {
                $table->dropColumn('store_id');
            }
        });

        // Categories: quitar store_id
        Schema::table('categories', function (Blueprint $table) {
            try {
                $table->dropForeign(['store_id']);
            } catch (\Throwable $e) {
            }
            if (Schema::hasColumn('categories', 'store_id')) {
                $table->dropColumn('store_id');
            }
        });

        // Users: quitar store_id
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropForeign(['store_id']);
            } catch (\Throwable $e) {
            }
            if (Schema::hasColumn('users', 'store_id')) {
                $table->dropColumn('store_id');
            }
        });

        // Store: quitar soft deletes si lo agregamos
        Schema::table('store', function (Blueprint $table) {
            if (Schema::hasColumn('store', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
