<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productNames = [
            'iPhone 14 Pro',
            'Samsung Galaxy S23',
            'MacBook Pro M2',
            'Dell XPS 13',
            'Sony WH-1000XM4',
            'Apple Watch Series 8',
            'iPad Air',
            'AirPods Pro',
            'PlayStation 5',
            'Nintendo Switch',
        ];

        $productName = $this->faker->randomElement($productNames);
        $price = $this->faker->randomFloat(2, 29.99, 1999.99);
        $quantity = $this->faker->numberBetween(1, 3);

        return [
            'order_id' => Order::factory(),
            'product_id' => $this->faker->numberBetween(1, 100),
            'quantity' => $quantity,
            'price' => $price,
            'product_name' => $productName,
            'product_description' => $this->faker->text(200),
            'product_sku' => $this->generateSku($productName),
            'product_image' => $this->faker->imageUrl(400, 400, 'technics'),
            'variant_info' => $this->generateVariantInfo($productName),
        ];
    }

    /**
     * Item para pedido específico
     */
    public function forOrder(Order $order): static
    {
        return $this->state(fn(array $attributes) => [
            'order_id' => $order->id,
        ]);
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
     * Item de producto específico
     */
    public function withProduct(int $productId, string $productName = null): static
    {
        return $this->state(fn(array $attributes) => [
            'product_id' => $productId,
            'product_name' => $productName ?? "Producto #{$productId}",
            'product_sku' => $this->generateSku($productName ?? "Producto #{$productId}"),
        ]);
    }

    /**
     * Item de electrónico
     */
    public function electronic(): static
    {
        $products = [
            'iPhone 14 Pro' => 999.99,
            'Samsung Galaxy S23' => 899.99,
            'MacBook Pro M2' => 1999.99,
            'iPad Air' => 599.99,
            'AirPods Pro' => 249.99,
        ];

        $productName = $this->faker->randomElement(array_keys($products));
        $price = $products[$productName];

        return $this->state(fn(array $attributes) => [
            'product_name' => $productName,
            'price' => $price,
            'product_sku' => $this->generateSku($productName),
            'variant_info' => $this->generateElectronicVariant(),
        ]);
    }

    /**
     * Item de ropa
     */
    public function clothing(): static
    {
        $products = [
            'Camiseta Nike' => 29.99,
            'Jeans Levi\'s' => 79.99,
            'Sudadera Adidas' => 59.99,
            'Zapatos Converse' => 89.99,
            'Chaqueta North Face' => 149.99,
        ];

        $productName = $this->faker->randomElement(array_keys($products));
        $price = $products[$productName];

        return $this->state(fn(array $attributes) => [
            'product_name' => $productName,
            'price' => $price,
            'product_sku' => $this->generateSku($productName),
            'variant_info' => $this->generateClothingVariant(),
        ]);
    }

    /**
     * Item de alto valor
     */
    public function expensive(): static
    {
        return $this->state(fn(array $attributes) => [
            'price' => $this->faker->randomFloat(2, 500.00, 2999.99),
            'quantity' => 1,
        ]);
    }

    /**
     * Item de bajo valor
     */
    public function cheap(): static
    {
        return $this->state(fn(array $attributes) => [
            'price' => $this->faker->randomFloat(2, 9.99, 49.99),
            'quantity' => $this->faker->numberBetween(1, 5),
        ]);
    }

    /**
     * Item sin imagen
     */
    public function withoutImage(): static
    {
        return $this->state(fn(array $attributes) => [
            'product_image' => null,
        ]);
    }

    /**
     * Item con variante específica
     */
    public function withVariant(array $variantInfo): static
    {
        return $this->state(fn(array $attributes) => [
            'variant_info' => $variantInfo,
        ]);
    }

    /**
     * Generar SKU basado en el nombre del producto
     */
    private function generateSku(string $productName): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $productName), 0, 3));
        $suffix = $this->faker->randomNumber(4);

        return "{$prefix}-{$suffix}";
    }

    /**
     * Generar información de variante para electrónicos
     */
    private function generateElectronicVariant(): array
    {
        $variants = [
            [
                'color' => $this->faker->randomElement(['Negro', 'Blanco', 'Azul', 'Rojo']),
                'storage' => $this->faker->randomElement(['64GB', '128GB', '256GB', '512GB']),
            ],
            [
                'color' => $this->faker->randomElement(['Negro', 'Plata', 'Dorado']),
                'screen_size' => $this->faker->randomElement(['13"', '14"', '15"', '16"']),
            ],
        ];

        return $this->faker->randomElement($variants);
    }

    /**
     * Generar información de variante para ropa
     */
    private function generateClothingVariant(): array
    {
        return [
            'size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'color' => $this->faker->randomElement(['Negro', 'Blanco', 'Azul', 'Rojo', 'Verde', 'Gris']),
        ];
    }
}
