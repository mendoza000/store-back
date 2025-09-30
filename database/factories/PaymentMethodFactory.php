<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['mobile_payment', 'bank_transfer', 'paypal', 'cash', 'crypto']);
        
        return [
            'store_id' => Store::factory(),
            'name' => $this->getNameForType($type),
            'type' => $type,
            'account_info' => $this->getAccountInfoForType($type),
            'instructions' => fake()->sentence(10),
            'status' => fake()->randomElement(['active', 'active', 'active', 'inactive']), // 75% active
        ];
    }

    /**
     * Get a realistic name for each payment type
     */
    private function getNameForType(string $type): string
    {
        return match($type) {
            'mobile_payment' => fake()->randomElement(['Pago Móvil', 'Zelle', 'Venmo', 'Cash App']),
            'bank_transfer' => 'Transferencia Bancaria ' . fake()->randomElement(['Banco A', 'Banco B', 'Banco C']),
            'paypal' => 'PayPal',
            'cash' => 'Efectivo',
            'crypto' => fake()->randomElement(['Bitcoin', 'Ethereum', 'USDT', 'USDC']),
        };
    }

    /**
     * Get realistic account info for each payment type
     */
    private function getAccountInfoForType(string $type): string
    {
        return match($type) {
            'mobile_payment' => fake()->numerify('04##-#######'),
            'bank_transfer' => fake()->numerify('0108-####-##-##########'),
            'paypal' => fake()->email(),
            'cash' => 'En tienda física',
            'crypto' => fake()->regexify('[13][a-km-zA-HJ-NP-Z1-9]{25,34}'),
        };
    }

    /**
     * Indicate that the payment method is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the payment method is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the payment method belongs to a specific store.
     */
    public function forStore(Store $store): static
    {
        return $this->state(fn (array $attributes) => [
            'store_id' => $store->id,
        ]);
    }

    /**
     * Create a mobile payment method.
     */
    public function mobilePayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'mobile_payment',
            'name' => fake()->randomElement(['Pago Móvil', 'Zelle']),
            'account_info' => fake()->numerify('04##-#######'),
        ]);
    }

    /**
     * Create a bank transfer payment method.
     */
    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'bank_transfer',
            'name' => 'Transferencia Bancaria',
            'account_info' => fake()->numerify('0108-####-##-##########'),
        ]);
    }
} 