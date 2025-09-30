<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Store;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'verified', 'rejected']);
        $paidAt = fake()->dateTimeBetween('-30 days', 'now');
        
        return [
            'store_id' => Store::factory(),
            'order_id' => Order::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'reference_number' => 'REF-' . Str::upper(Str::random(12)),
            'receipt_url' => fake()->boolean(60) ? fake()->imageUrl(800, 600, 'receipt', true) : null,
            'notes' => fake()->boolean(40) ? fake()->sentence(8) : null,
            'status' => $status,
            'paid_at' => $paidAt,
            'verified_at' => $status === 'verified' ? fake()->dateTimeBetween($paidAt, 'now') : null,
            'verified_by' => $status === 'verified' ? User::factory() : null,
            'rejected_at' => $status === 'rejected' ? fake()->dateTimeBetween($paidAt, 'now') : null,
            'refunded_at' => null,
        ];
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'verified_at' => null,
            'verified_by' => null,
            'rejected_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'verified',
            'verified_at' => fake()->dateTimeBetween($attributes['paid_at'] ?? '-7 days', 'now'),
            'verified_by' => User::factory(),
            'rejected_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'verified_at' => null,
            'verified_by' => null,
            'rejected_at' => fake()->dateTimeBetween($attributes['paid_at'] ?? '-7 days', 'now'),
        ]);
    }

    /**
     * Indicate that the payment is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'refunded_at' => fake()->dateTimeBetween($attributes['paid_at'] ?? '-7 days', 'now'),
        ]);
    }

    /**
     * Indicate that the payment belongs to a specific store.
     */
    public function forStore(Store $store): static
    {
        return $this->state(fn (array $attributes) => [
            'store_id' => $store->id,
        ]);
    }

    /**
     * Indicate that the payment belongs to a specific order.
     */
    public function forOrder(Order $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => $order->id,
            'amount' => $order->total,
        ]);
    }

    /**
     * Indicate that the payment uses a specific payment method.
     */
    public function withPaymentMethod(PaymentMethod $paymentMethod): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method_id' => $paymentMethod->id,
        ]);
    }
} 