<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50.00, 500.00);
        $taxAmount = $subtotal * 0.16; // 16% impuesto
        $shippingAmount = $this->faker->randomFloat(2, 5.00, 25.00);
        $discountAmount = 0;
        $total = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        return [
            'store_id' => Store::factory(),
            'order_number' => Order::generateOrderNumber(),
            'user_id' => User::factory(),
            'status' => OrderStatus::PENDING->value,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'shipping_address' => $this->generateAddress(),
            'billing_address' => $this->generateAddress(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Pedido pendiente
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::PENDING->value,
        ]);
    }

    /**
     * Pedido pagado
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::PAID->value,
            'paid_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Pedido en procesamiento
     */
    public function processing(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::PROCESSING->value,
            'paid_at' => $this->faker->dateTimeBetween('-1 week', '-2 days'),
        ]);
    }

    /**
     * Pedido enviado
     */
    public function shipped(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::SHIPPED->value,
            'paid_at' => $this->faker->dateTimeBetween('-2 weeks', '-5 days'),
            'shipped_at' => $this->faker->dateTimeBetween('-3 days', '-1 day'),
        ]);
    }

    /**
     * Pedido entregado
     */
    public function delivered(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::DELIVERED->value,
            'paid_at' => $this->faker->dateTimeBetween('-1 month', '-1 week'),
            'shipped_at' => $this->faker->dateTimeBetween('-1 week', '-3 days'),
            'delivered_at' => $this->faker->dateTimeBetween('-3 days', 'now'),
        ]);
    }

    /**
     * Pedido cancelado
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::CANCELLED->value,
            'cancelled_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Pedido con descuento
     */
    public function withDiscount(float $discountAmount = null): static
    {
        $discount = $discountAmount ?? $this->faker->randomFloat(2, 10.00, 50.00);

        return $this->state(function (array $attributes) use ($discount) {
            $newTotal = $attributes['subtotal'] + $attributes['tax_amount'] + $attributes['shipping_amount'] - $discount;

            return [
                'discount_amount' => $discount,
                'total' => $newTotal,
            ];
        });
    }

    /**
     * Pedido sin envío
     */
    public function freeShipping(): static
    {
        return $this->state(function (array $attributes) {
            $newTotal = $attributes['subtotal'] + $attributes['tax_amount'] + 0 - $attributes['discount_amount'];

            return [
                'shipping_amount' => 0,
                'total' => $newTotal,
            ];
        });
    }

    /**
     * Pedido de alto valor
     */
    public function highValue(): static
    {
        return $this->state(function (array $attributes) {
            $subtotal = $this->faker->randomFloat(2, 1000.00, 5000.00);
            $taxAmount = $subtotal * 0.16;
            $shippingAmount = 0; // Envío gratis para pedidos grandes
            $total = $subtotal + $taxAmount + $shippingAmount - $attributes['discount_amount'];

            return [
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'total' => $total,
            ];
        });
    }

    /**
     * Pedido para usuario específico
     */
    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Pedido con direcciones iguales
     */
    public function sameAddresses(): static
    {
        return $this->state(function (array $attributes) {
            $address = $this->generateAddress();

            return [
                'shipping_address' => $address,
                'billing_address' => $address,
            ];
        });
    }

    /**
     * Pedido para store específico
     */
    public function forStore(Store $store): static
    {
        return $this->state(fn(array $attributes) => [
            'store_id' => $store->id,
        ]);
    }

    /**
     * Generar dirección realista
     */
    private function generateAddress(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'company' => $this->faker->optional()->company(),
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->optional()->secondaryAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
