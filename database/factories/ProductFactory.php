<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 10, 1000);
        
        return [
            'id' => Str::uuid(),
            'store_id' => Store::factory(),
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(3),
            'short_description' => fake()->sentence(10),
            'price' => $price,
            'compare_price' => fake()->boolean(30) ? $price * 1.2 : null,
            'cost_price' => fake()->boolean(50) ? $price * 0.7 : null,
            'track_quantity' => fake()->numberBetween(0, 1),
            'sku' => 'SKU-' . Str::upper(Str::random(8)),
            'status' => fake()->randomElement(['active', 'inactive', 'out_of_stock']),
            'stock' => fake()->numberBetween(0, 100),
            'category' => fake()->word(),
            'brand' => fake()->company(),
            'image' => fake()->imageUrl(400, 400, 'product', true),
            'metadata' => [
                'color' => fake()->safeColorName(),
                'size' => fake()->randomElement(['S', 'M', 'L', 'XL']),
                'weight' => fake()->randomFloat(2, 0.1, 10) . 'kg',
            ],
        ];
    }

    /**
     * Indicate that the product is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'stock' => fake()->numberBetween(10, 100),
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'out_of_stock',
            'stock' => 0,
        ]);
    }

    /**
     * Indicate that the product belongs to a specific store.
     */
    public function forStore(Store $store): static
    {
        return $this->state(fn (array $attributes) => [
            'store_id' => $store->id,
        ]);
    }

    /**
     * Indicate that the product belongs to a specific category.
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Indicate that the product has stock.
     */
    public function withStock(int $stock = null): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $stock ?? fake()->numberBetween(10, 100),
            'status' => 'active',
        ]);
    }
} 