<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 10, 1000);
        $variantNames = ['Color', 'Tamaño', 'Material', 'Estilo'];
        $variantValues = [
            'Color' => ['Rojo', 'Azul', 'Verde', 'Negro', 'Blanco'],
            'Tamaño' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'Material' => ['Algodón', 'Poliéster', 'Seda', 'Lana'],
            'Estilo' => ['Casual', 'Formal', 'Deportivo', 'Elegante'],
        ];

        $variantName = fake()->randomElement($variantNames);
        $variantValue = fake()->randomElement($variantValues[$variantName]);

        return [
            'id' => Str::uuid(),
            'product_id' => Product::factory(),
            'variant_name' => $variantName,
            'variant_value' => $variantValue,
            'price' => $price,
            'compare_price' => fake()->boolean(30) ? $price * 1.2 : null,
            'stock' => fake()->numberBetween(0, 50),
            'sku' => 'VAR-' . Str::upper(Str::random(10)),
            'status' => fake()->randomElement(['active', 'inactive', 'out_of_stock']),
        ];
    }

    /**
     * Indicate that the variant is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'stock' => fake()->numberBetween(10, 50),
        ]);
    }

    /**
     * Indicate that the variant is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the variant is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'out_of_stock',
            'stock' => 0,
        ]);
    }

    /**
     * Indicate that the variant belongs to a specific product.
     */
    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
        ]);
    }

    /**
     * Create a color variant.
     */
    public function color(string $color = null): static
    {
        return $this->state(fn (array $attributes) => [
            'variant_name' => 'Color',
            'variant_value' => $color ?? fake()->randomElement(['Rojo', 'Azul', 'Verde', 'Negro', 'Blanco']),
        ]);
    }

    /**
     * Create a size variant.
     */
    public function size(string $size = null): static
    {
        return $this->state(fn (array $attributes) => [
            'variant_name' => 'Tamaño',
            'variant_value' => $size ?? fake()->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
        ]);
    }
} 