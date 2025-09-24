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


        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });


        Schema::create('products', function (Blueprint $table) {
            $table->uuid("id")->primary();

            $table->uuid('category_id')->constrained('categories')->onDelete('cascade');

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('track_quantity')->default(0);
            $table->string('sku')->unique();
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');

            // Columnas adicionales para el seeder
            $table->integer('stock')->default(0);
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->string('image')->nullable();
            $table->json('metadata')->nullable();
            

            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id') 
        ->constrained('products') 
        ->onDelete('cascade');
            $table->string('image_path');
            $table->integer('sort_order')->default(0);
            $table->string('url')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id')->constrained('products')->onDelete('cascade');
            $table->string('variant_name');
            $table->string('variant_value');
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('sku')->unique();
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('product_variants');
    }
};
