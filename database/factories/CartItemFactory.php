<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cart_id' => Cart::factory(),
            'product_id' => $this->faker->numberBetween(1, 100), // Por ahora random, luego será Product::factory()
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $this->faker->randomFloat(2, 10.00, 999.99),
        ];
    }

    /**
     * Item con cantidad específica
     */
    public function withQuantity(int $quantity): static
    {
        return $this->state(fn(array $attributes) => [
            'quantity' => $quantity,
        ]);
    }

    /**
     * Item con precio específico
     */
    public function withPrice(float $price): static
    {
        return $this->state(fn(array $attributes) => [
            'price' => $price,
        ]);
    }

    /**
     * Item con producto específico
     */
    public function withProduct(int $productId): static
    {
        return $this->state(fn(array $attributes) => [
            'product_id' => $productId,
        ]);
    }

    /**
     * Item para un carrito específico
     */
    public function forCart(Cart $cart): static
    {
        return $this->state(fn(array $attributes) => [
            'cart_id' => $cart->id,
        ]);
    }

    /**
     * Item de alto valor
     */
    public function expensive(): static
    {
        return $this->state(fn(array $attributes) => [
            'price' => $this->faker->randomFloat(2, 500.00, 2000.00),
            'quantity' => 1,
        ]);
    }

    /**
     * Item de bajo valor
     */
    public function cheap(): static
    {
        return $this->state(fn(array $attributes) => [
            'price' => $this->faker->randomFloat(2, 5.00, 50.00),
            'quantity' => $this->faker->numberBetween(1, 10),
        ]);
    }

    /**
     * Múltiples items para el mismo carrito
     */
    public function multipleItems(int $count = 3): static
    {
        return $this->count($count)->state(function (array $attributes, int $index) {
            return [
                'product_id' => $index + 1, // Productos diferentes
            ];
        });
    }
}
