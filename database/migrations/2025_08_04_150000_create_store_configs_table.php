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
        Schema::create('store_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            $table->boolean('products')->default(true);
            $table->boolean('categories')->default(true);
            $table->boolean('cupons')->default(false);
            $table->boolean('gifcards')->default(false);
            $table->boolean('wishlist')->default(false);
            $table->boolean('reviews')->default(false);
            $table->boolean('notifications_emails')->default(true);
            $table->boolean('notifications_telegram')->default(false);
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('store')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_configs');
    }
};

