<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderStatusHistory>
 */
class OrderStatusHistoryFactory extends Factory
{
    protected $model = OrderStatusHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = OrderStatus::values();
        $newStatus = $this->faker->randomElement($statuses);

        // Obtener un estado anterior lógico
        $previousStatus = $this->getPreviousStatus($newStatus);

        return [
            'order_id' => Order::factory(),
            'created_by' => $this->faker->optional(0.7)->randomElement([User::factory(), null]),
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'notes' => $this->faker->optional(0.6)->sentence(),
            'reason' => $this->getReasonForStatus($newStatus),
            'customer_notified' => $this->faker->boolean(30),
            'notified_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 week', 'now'),
        ];
    }

    /**
     * Cambio para pedido específico
     */
    public function forOrder(Order $order): static
    {
        return $this->state(fn(array $attributes) => [
            'order_id' => $order->id,
        ]);
    }

    /**
     * Cambio a estado específico
     */
    public function toStatus(OrderStatus|string $status): static
    {
        $statusValue = $status instanceof OrderStatus ? $status->value : $status;
        $previousStatus = $this->getPreviousStatus($statusValue);

        return $this->state(fn(array $attributes) => [
            'previous_status' => $previousStatus,
            'new_status' => $statusValue,
            'reason' => $this->getReasonForStatus($statusValue),
        ]);
    }

    /**
     * Cambio desde estado específico
     */
    public function fromStatus(OrderStatus|string $status): static
    {
        $statusValue = $status instanceof OrderStatus ? $status->value : $status;

        return $this->state(fn(array $attributes) => [
            'previous_status' => $statusValue,
        ]);
    }

    /**
     * Cambio realizado por usuario específico
     */
    public function byUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'created_by' => $user->id,
        ]);
    }

    /**
     * Cambio automático del sistema
     */
    public function automatic(): static
    {
        return $this->state(fn(array $attributes) => [
            'created_by' => null,
            'notes' => 'Cambio automático del sistema',
        ]);
    }

    /**
     * Cambio manual por administrador
     */
    public function manual(): static
    {
        return $this->state(fn(array $attributes) => [
            'created_by' => User::factory(),
            'notes' => $this->faker->sentence(),
        ]);
    }

    /**
     * Cambio con notificación al cliente
     */
    public function notified(): static
    {
        return $this->state(fn(array $attributes) => [
            'customer_notified' => true,
            'notified_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    /**
     * Cambio sin notificación
     */
    public function notNotified(): static
    {
        return $this->state(fn(array $attributes) => [
            'customer_notified' => false,
            'notified_at' => null,
        ]);
    }

    /**
     * Cambio a pagado
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'previous_status' => OrderStatus::PENDING->value,
            'new_status' => OrderStatus::PAID->value,
            'notes' => 'Pago confirmado',
            'reason' => 'payment_confirmed',
            'customer_notified' => true,
            'notified_at' => now(),
        ]);
    }

    /**
     * Cambio a enviado
     */
    public function shipped(): static
    {
        return $this->state(fn(array $attributes) => [
            'previous_status' => OrderStatus::PROCESSING->value,
            'new_status' => OrderStatus::SHIPPED->value,
            'notes' => 'Pedido enviado con número de tracking',
            'reason' => 'order_shipped',
            'customer_notified' => true,
            'notified_at' => now(),
            'metadata' => [
                'tracking_number' => $this->faker->regexify('[A-Z]{2}[0-9]{10}'),
                'carrier' => $this->faker->randomElement(['DHL', 'FedEx', 'UPS', 'Correos']),
            ],
        ]);
    }

    /**
     * Cambio a entregado
     */
    public function delivered(): static
    {
        return $this->state(fn(array $attributes) => [
            'previous_status' => OrderStatus::SHIPPED->value,
            'new_status' => OrderStatus::DELIVERED->value,
            'notes' => 'Pedido entregado exitosamente',
            'reason' => 'delivery_confirmed',
            'customer_notified' => true,
            'notified_at' => now(),
        ]);
    }

    /**
     * Cambio a cancelado
     */
    public function cancelled(): static
    {
        $reasons = [
            'customer_request' => 'Cancelación solicitada por el cliente',
            'out_of_stock' => 'Producto sin stock',
            'payment_failed' => 'Pago no procesado',
            'fraud_detection' => 'Detección de fraude',
        ];

        $reason = $this->faker->randomElement(array_keys($reasons));

        return $this->state(fn(array $attributes) => [
            'previous_status' => $this->faker->randomElement([
                OrderStatus::PENDING->value,
                OrderStatus::PAID->value
            ]),
            'new_status' => OrderStatus::CANCELLED->value,
            'notes' => $reasons[$reason],
            'reason' => $reason,
            'customer_notified' => true,
            'notified_at' => now(),
        ]);
    }

    /**
     * Cambio con metadata específica
     */
    public function withMetadata(array $metadata): static
    {
        return $this->state(fn(array $attributes) => [
            'metadata' => $metadata,
        ]);
    }

    /**
     * Obtener estado anterior lógico basado en el nuevo estado
     */
    private function getPreviousStatus(string $newStatus): ?string
    {
        return match ($newStatus) {
            OrderStatus::PAID->value => OrderStatus::PENDING->value,
            OrderStatus::PROCESSING->value => OrderStatus::PAID->value,
            OrderStatus::SHIPPED->value => OrderStatus::PROCESSING->value,
            OrderStatus::DELIVERED->value => OrderStatus::SHIPPED->value,
            OrderStatus::CANCELLED->value => $this->faker->randomElement([
                OrderStatus::PENDING->value,
                OrderStatus::PAID->value
            ]),
            default => null,
        };
    }

    /**
     * Obtener razón típica para un estado
     */
    private function getReasonForStatus(string $status): ?string
    {
        return match ($status) {
            OrderStatus::PAID->value => 'payment_confirmed',
            OrderStatus::PROCESSING->value => 'order_processing',
            OrderStatus::SHIPPED->value => 'order_shipped',
            OrderStatus::DELIVERED->value => 'delivery_confirmed',
            OrderStatus::CANCELLED->value => $this->faker->randomElement([
                'customer_request',
                'out_of_stock',
                'payment_failed'
            ]),
            default => null,
        };
    }
}
