<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'product_name',
        'product_description',
        'product_sku',
        'product_image',
        'variant_info',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'variant_info' => 'array',
        'metadata' => 'array',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Validaciones antes de crear
        static::creating(function ($orderItem) {
            $orderItem->validateQuantity();
            $orderItem->validatePrice();
        });

        // Validaciones antes de actualizar
        static::updating(function ($orderItem) {
            if ($orderItem->isDirty(['quantity', 'price'])) {
                $orderItem->validateQuantity();
                $orderItem->validatePrice();
            }
        });

        // Recalcular total del pedido cuando se modifica un item
        static::saved(function ($orderItem) {
            if ($orderItem->order) {
                $orderItem->order->updateTotal();
            }
        });

        // Recalcular total del pedido cuando se elimina un item
        static::deleted(function ($orderItem) {
            if ($orderItem->order) {
                $orderItem->order->updateTotal();
            }
        });
    }

    /**
     * Relación con Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relación con Product (futuramente ProductVariant)
     * TODO: Cambiar a ProductVariant cuando se implemente
     */
    public function product(): BelongsTo
    {
        // return $this->belongsTo(Product::class);
        // Por ahora retornamos null hasta que se implemente Product
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    /**
     * Accessor: Calcular total del item (precio * cantidad)
     */
    protected function total(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->price * $this->quantity,
        );
    }

    /**
     * Accessor: Obtener información completa del producto
     */
    protected function productSnapshot(): Attribute
    {
        return Attribute::make(
            get: fn() => [
                'id' => $this->product_id,
                'name' => $this->product_name,
                'description' => $this->product_description,
                'sku' => $this->product_sku,
                'image' => $this->product_image,
                'variant_info' => $this->variant_info,
                'price_at_time' => $this->price,
                'quantity_ordered' => $this->quantity,
                'total' => $this->price * $this->quantity,
            ],
        );
    }

    /**
     * Accessor: Obtener URL completa de la imagen
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->product_image) {
                    return null;
                }

                // Si ya es una URL completa, devolverla tal como está
                if (str_starts_with($this->product_image, 'http')) {
                    return $this->product_image;
                }

                // Construir URL completa para imágenes locales
                return url('storage/' . $this->product_image);
            },
        );
    }

    /**
     * Accessor: Obtener descripción de la variante
     */
    protected function variantDescription(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->variant_info || !is_array($this->variant_info)) {
                    return null;
                }

                $descriptions = [];
                foreach ($this->variant_info as $attribute => $value) {
                    $descriptions[] = ucfirst($attribute) . ': ' . $value;
                }

                return implode(', ', $descriptions);
            },
        );
    }

    /**
     * Scope: Items de un pedido específico
     */
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope: Items de un producto específico
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope: Items con variante específica
     */
    public function scopeWithVariant($query, array $variantInfo)
    {
        return $query->where('variant_info', $variantInfo);
    }

    /**
     * Validar cantidad
     */
    protected function validateQuantity(): void
    {
        if ($this->quantity <= 0) {
            throw new \InvalidArgumentException('La cantidad debe ser mayor a 0');
        }

        if ($this->quantity > 999) {
            throw new \InvalidArgumentException('La cantidad no puede ser mayor a 999');
        }
    }

    /**
     * Validar precio
     */
    protected function validatePrice(): void
    {
        if ($this->price < 0) {
            throw new \InvalidArgumentException('El precio no puede ser negativo');
        }

        if ($this->price > 999999.99) {
            throw new \InvalidArgumentException('El precio es demasiado alto');
        }
    }

    /**
     * Actualizar cantidad del item
     */
    public function updateQuantity(int $quantity): bool
    {
        $this->quantity = $quantity;
        $this->validateQuantity();

        return $this->save();
    }

    /**
     * Actualizar precio del item
     */
    public function updatePrice(float $price): bool
    {
        $this->price = $price;
        $this->validatePrice();

        return $this->save();
    }

    /**
     * Crear snapshot del producto desde el producto actual
     * TODO: Implementar cuando se cree el modelo Product
     */
    public static function createSnapshotFromProduct($product): array
    {
        // Por ahora retornamos un snapshot básico
        // Cuando implementemos Product, esto será más completo
        return [
            'name' => $product['name'] ?? 'Producto sin nombre',
            'description' => $product['description'] ?? null,
            'sku' => $product['sku'] ?? null,
            'image' => $product['image'] ?? null,
            'variant_info' => $product['variant_info'] ?? null,
        ];
    }

    /**
     * Verificar si el item tiene descuento comparado con el precio actual del producto
     * TODO: Implementar cuando se cree el modelo Product
     */
    public function hasDiscount(): bool
    {
        // Por ahora retornamos false
        // Cuando implementemos Product, compararemos con el precio actual
        return false;
    }

    /**
     * Obtener porcentaje de descuento
     * TODO: Implementar cuando se cree el modelo Product
     */
    public function getDiscountPercentage(): float
    {
        // Por ahora retornamos 0
        // Cuando implementemos Product, calcularemos el descuento real
        return 0;
    }

    /**
     * Verificar si el producto está disponible
     * TODO: Implementar cuando se cree el modelo Product
     */
    public function isProductAvailable(): bool
    {
        // Por ahora retornamos true
        // Cuando implementemos Product, verificaremos disponibilidad real
        return true;
    }

    /**
     * Obtener información para mostrar en facturas/recibos
     */
    public function getInvoiceData(): array
    {
        return [
            'product_name' => $this->product_name,
            'variant_description' => $this->variant_description,
            'sku' => $this->product_sku,
            'quantity' => $this->quantity,
            'unit_price' => $this->price,
            'total' => $this->total,
        ];
    }

    /**
     * Duplicar item para otro pedido
     */
    public function duplicateForOrder(Order $newOrder): self
    {
        return static::create([
            'order_id' => $newOrder->id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'product_name' => $this->product_name,
            'product_description' => $this->product_description,
            'product_sku' => $this->product_sku,
            'product_image' => $this->product_image,
            'variant_info' => $this->variant_info,
            'metadata' => $this->metadata,
        ]);
    }
}
