<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'session_id' => null,
            'status' => 'active',
            'expires_at' => Carbon::now()->addHours(24),
        ];
    }

    /**
     * Cart para usuario guest
     */
    public function guest(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => null,
            'session_id' => Str::uuid(),
            'expires_at' => Carbon::now()->addHours(24),
        ]);
    }

    /**
     * Cart para usuario registrado
     */
    public function registered(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => User::factory(),
            'session_id' => null,
            'expires_at' => Carbon::now()->addDays(30),
        ]);
    }

    /**
     * Cart expirado
     */
    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'expired',
            'expires_at' => Carbon::now()->subHours(1),
        ]);
    }

    /**
     * Cart completado
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Cart que expira pronto
     */
    public function expiringSoon(): static
    {
        return $this->state(fn(array $attributes) => [
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);
    }
}
