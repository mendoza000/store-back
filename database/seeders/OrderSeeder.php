<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('⚠️  No hay usuarios o productos disponibles para crear pedidos');
            return;
        }

        $ordersCreated = 0;
        $itemsCreated = 0;

        // Crear pedidos con diferentes estados
        $statuses = [
            OrderStatus::PENDING->value => 15,
            OrderStatus::PAID->value => 20,
            OrderStatus::PROCESSING->value => 12,
            OrderStatus::SHIPPED->value => 8,
            OrderStatus::DELIVERED->value => 10,
            OrderStatus::CANCELLED->value => 5,
        ];

        foreach ($statuses as $statusValue => $count) {
            $status = OrderStatus::from($statusValue);
            for ($i = 0; $i < $count; $i++) {
                $user = $users->random();
                $orderDate = now()->subDays(rand(1, 30));

                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => $status->value,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'shipping_amount' => 15.00,
                    'discount_amount' => 0,
                    'total' => 0,
                    'shipping_address' => $this->generateAddress(),
                    'billing_address' => $this->generateAddress(),
                    'notes' => $this->generateOrderNotes($status),
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                $this->setOrderDates($order, $status, $orderDate);
                $ordersCreated++;

                // Agregar items al pedido
                $numItems = rand(1, 4);
                $selectedProducts = $products->random($numItems);
                $subtotal = 0;

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 3);
                    $price = $product->price;
                    $total = $price * $quantity;
                    $subtotal += $total;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'product_name' => $product->name,
                        'product_description' => $product->description,
                        'product_sku' => $product->sku,
                        'product_image' => $product->image,
                        'variant_info' => $product->metadata,
                    ]);

                    $itemsCreated++;
                }

                // Calcular totales
                $taxAmount = $subtotal * 0.16;
                $total = $subtotal + $taxAmount + $order->shipping_amount - $order->discount_amount;

                $order->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total' => $total,
                ]);

                $this->createOrderHistory($order, $status, $orderDate);
            }
        }

        $this->command->info("✅ Pedidos creados exitosamente:");
        $this->command->info("   - {$ordersCreated} pedidos creados");
        $this->command->info("   - {$itemsCreated} items agregados a pedidos");

        $this->showOrderStatistics();
    }

    private function generateAddress(): array
    {
        $cities = [
            'Caracas' => 'Distrito Capital',
            'Valencia' => 'Carabobo',
            'Maracaibo' => 'Zulia',
            'Barquisimeto' => 'Lara',
            'Maracay' => 'Aragua',
        ];

        $city = array_rand($cities);
        $state = $cities[$city];

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company' => rand(0, 1) ? fake()->company() : null,
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => rand(0, 1) ? fake()->secondaryAddress() : null,
            'city' => $city,
            'state' => $state,
            'postal_code' => fake()->postcode(),
            'country' => 'Venezuela',
            'phone' => '+58-' . fake()->numberBetween(200, 299) . '-' . fake()->numberBetween(1000000, 9999999),
        ];
    }

    private function generateOrderNotes(OrderStatus $status): ?string
    {
        $notes = [
            OrderStatus::PENDING->value => [
                'Pedido recién creado, pendiente de pago',
                'Esperando confirmación de pago',
                'Pedido en espera de procesamiento',
            ],
            OrderStatus::PAID->value => [
                'Pago confirmado exitosamente',
                'Pedido pagado, listo para procesar',
                'Pago recibido, preparando envío',
            ],
            OrderStatus::PROCESSING->value => [
                'Pedido en proceso de preparación',
                'Verificando inventario y preparando envío',
                'Procesando pedido en almacén',
            ],
            OrderStatus::SHIPPED->value => [
                'Pedido enviado con número de seguimiento',
                'En tránsito hacia dirección de destino',
                'Enviado por empresa de transporte',
            ],
            OrderStatus::DELIVERED->value => [
                'Pedido entregado exitosamente',
                'Entregado al cliente',
                'Entrega completada',
            ],
            OrderStatus::CANCELLED->value => [
                'Pedido cancelado por el cliente',
                'Cancelado por problemas de pago',
                'Cancelado por falta de stock',
            ],
        ];

        return $notes[$status->value][array_rand($notes[$status->value])];
    }

    private function setOrderDates(Order $order, OrderStatus $status, $orderDate): void
    {
        $dates = [];

        switch ($status) {
            case OrderStatus::PENDING:
                $dates = [
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ];
                break;

            case OrderStatus::PAID:
                $paidDate = $orderDate->copy()->addHours(rand(1, 6));
                $dates = [
                    'created_at' => $orderDate,
                    'updated_at' => $paidDate,
                    'paid_at' => $paidDate,
                ];
                break;

            case OrderStatus::PROCESSING:
                $paidDate = $orderDate->copy()->addHours(rand(1, 6));
                $processingDate = $paidDate->copy()->addHours(rand(1, 24));
                $dates = [
                    'created_at' => $orderDate,
                    'updated_at' => $processingDate,
                    'paid_at' => $paidDate,
                ];
                break;

            case OrderStatus::SHIPPED:
                $paidDate = $orderDate->copy()->addHours(rand(1, 6));
                $processingDate = $paidDate->copy()->addHours(rand(1, 24));
                $shippedDate = $processingDate->copy()->addHours(rand(1, 48));
                $dates = [
                    'created_at' => $orderDate,
                    'updated_at' => $shippedDate,
                    'paid_at' => $paidDate,
                    'shipped_at' => $shippedDate,
                ];
                break;

            case OrderStatus::DELIVERED:
                $paidDate = $orderDate->copy()->addHours(rand(1, 6));
                $processingDate = $paidDate->copy()->addHours(rand(1, 24));
                $shippedDate = $processingDate->copy()->addHours(rand(1, 48));
                $deliveredDate = $shippedDate->copy()->addDays(rand(1, 5));
                $dates = [
                    'created_at' => $orderDate,
                    'updated_at' => $deliveredDate,
                    'paid_at' => $paidDate,
                    'shipped_at' => $shippedDate,
                    'delivered_at' => $deliveredDate,
                ];
                break;

            case OrderStatus::CANCELLED:
                $cancelledDate = $orderDate->copy()->addHours(rand(1, 24));
                $dates = [
                    'created_at' => $orderDate,
                    'updated_at' => $cancelledDate,
                    'cancelled_at' => $cancelledDate,
                ];
                break;
        }

        $order->update($dates);
    }

    private function createOrderHistory(Order $order, OrderStatus $finalStatus, $orderDate): void
    {
        $statuses = [
            OrderStatus::PENDING,
            OrderStatus::PAID,
            OrderStatus::PROCESSING,
            OrderStatus::SHIPPED,
            OrderStatus::DELIVERED,
        ];

        $currentDate = $orderDate->copy();
        $previousStatus = null;

        foreach ($statuses as $status) {
            if ($status === $finalStatus) {
                break;
            }

            $currentDate = $currentDate->addHours(rand(1, 6));

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'previous_status' => $previousStatus,
                'new_status' => $status->value,
                'notes' => $this->getStatusChangeNotes($status),
                'reason' => $this->getStatusChangeReason($status),
                'created_by' => 1,
                'customer_notified' => true,
                'notified_at' => $currentDate,
                'created_at' => $currentDate,
                'updated_at' => $currentDate,
            ]);

            $previousStatus = $status->value;
        }

        $currentDate = $currentDate->addHours(rand(1, 6));

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'previous_status' => $previousStatus,
            'new_status' => $finalStatus->value,
            'notes' => $this->getStatusChangeNotes($finalStatus),
            'reason' => $this->getStatusChangeReason($finalStatus),
            'created_by' => 1,
            'customer_notified' => true,
            'notified_at' => $currentDate,
            'created_at' => $currentDate,
            'updated_at' => $currentDate,
        ]);
    }

    private function getStatusChangeNotes(OrderStatus $status): string
    {
        $notes = [
            OrderStatus::PENDING->value => 'Pedido creado exitosamente',
            OrderStatus::PAID->value => 'Pago confirmado y procesado',
            OrderStatus::PROCESSING->value => 'Pedido en preparación en almacén',
            OrderStatus::SHIPPED->value => 'Pedido enviado con número de seguimiento',
            OrderStatus::DELIVERED->value => 'Pedido entregado al cliente',
            OrderStatus::CANCELLED->value => 'Pedido cancelado según solicitud',
        ];

        return $notes[$status->value];
    }

    private function getStatusChangeReason(OrderStatus $status): string
    {
        $reasons = [
            OrderStatus::PENDING->value => 'order_created',
            OrderStatus::PAID->value => 'payment_confirmed',
            OrderStatus::PROCESSING->value => 'order_processing',
            OrderStatus::SHIPPED->value => 'order_shipped',
            OrderStatus::DELIVERED->value => 'order_delivered',
            OrderStatus::CANCELLED->value => 'customer_request',
        ];

        return $reasons[$status->value];
    }

    private function showOrderStatistics(): void
    {
        $this->command->info("📊 Estadísticas de pedidos:");

        foreach (OrderStatus::cases() as $status) {
            $count = Order::where('status', $status->value)->count();
            $this->command->info("   - {$status->getLabel()}: {$count}");
        }

        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', [
            OrderStatus::PAID->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::DELIVERED->value,
        ])->sum('total');

        $avgOrderValue = Order::whereIn('status', [
            OrderStatus::PAID->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::DELIVERED->value,
        ])->avg('total');

        $this->command->info("   - Total de pedidos: {$totalOrders}");
        $this->command->info("   - Ingresos totales: $" . number_format($totalRevenue, 2));
        $this->command->info("   - Valor promedio por pedido: $" . number_format($avgOrderValue, 2));
    }
}
