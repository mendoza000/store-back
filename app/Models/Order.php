<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total',
        'shipping_address',
        'billing_address',
        'notes',
        'metadata',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected $dates = [
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generar número de orden automáticamente
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });

        // Crear registro en historial al cambiar estado
        static::updated(function ($order) {
            if ($order->isDirty('status')) {
                $order->recordStatusChange(
                    previous: $order->getOriginal('status'),
                    new: $order->status,
                    notes: 'Estado actualizado automáticamente'
                );
            }
        });
    }

    /**
     * Relación con User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con OrderItems
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relación con OrderStatusHistory
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Pedidos por estado
     */
    public function scopeByStatus($query, string|OrderStatus $status)
    {
        $statusValue = $status instanceof OrderStatus ? $status->value : $status;
        return $query->where('status', $statusValue);
    }

    /**
     * Scope: Pedidos activos
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', OrderStatus::activeStatuses());
    }

    /**
     * Scope: Pedidos finalizados
     */
    public function scopeFinalized($query)
    {
        return $query->whereIn('status', OrderStatus::finalStatuses());
    }

    /**
     * Scope: Pedidos cancelables
     */
    public function scopeCancellable($query)
    {
        return $query->whereIn('status', OrderStatus::cancellableStatuses());
    }

    /**
     * Scope: Pedidos del usuario
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessor: Obtener enum del estado
     */
    protected function statusEnum(): Attribute
    {
        return Attribute::make(
            get: fn() => OrderStatus::from($this->status),
        );
    }

    /**
     * Generar número de orden único
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $timestamp = now()->format('YmdHis');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$timestamp}-{$random}";
    }

    /**
     * Crear pedido desde carrito
     */
    public static function createFromCart(Cart $cart, array $shippingAddress, array $billingAddress = null): self
    {
        $billingAddress = $billingAddress ?? $shippingAddress;

        $subtotal = $cart->subtotal;
        $total = $subtotal; // Por ahora sin impuestos ni envío

        $order = static::create([
            'user_id' => $cart->user_id,
            'status' => OrderStatus::PENDING->value,
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total' => $total,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
        ]);

        // Crear items del pedido
        foreach ($cart->items as $cartItem) {
            $order->addItem(
                productId: $cartItem->product_id,
                quantity: $cartItem->quantity,
                price: $cartItem->price,
                productSnapshot: $cartItem->productInfo
            );
        }

        // Marcar carrito como completado
        $cart->markAsCompleted();

        return $order;
    }

    /**
     * Agregar item al pedido
     */
    public function addItem(int $productId, int $quantity, float $price, array $productSnapshot): OrderItem
    {
        return $this->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
            'product_name' => $productSnapshot['name'] ?? 'Producto #' . $productId,
            'product_description' => $productSnapshot['description'] ?? null,
            'product_sku' => $productSnapshot['sku'] ?? null,
            'product_image' => $productSnapshot['image'] ?? null,
            'variant_info' => $productSnapshot['variant_info'] ?? null,
        ]);
    }

    /**
     * Cambiar estado del pedido
     */
    public function changeStatus(OrderStatus $newStatus, ?string $notes = null, ?int $changedBy = null): bool
    {
        if ($this->status === $newStatus->value) {
            return false; // Ya está en ese estado
        }

        $previousStatus = $this->status;
        $this->status = $newStatus->value;

        // Actualizar timestamps según el estado
        match ($newStatus) {
            OrderStatus::PAID => $this->paid_at = now(),
            OrderStatus::SHIPPED => $this->shipped_at = now(),
            OrderStatus::DELIVERED => $this->delivered_at = now(),
            OrderStatus::CANCELLED => $this->cancelled_at = now(),
            default => null,
        };

        $saved = $this->save();

        if ($saved && $notes) {
            $this->recordStatusChange($previousStatus, $newStatus->value, $notes, $changedBy);
        }

        return $saved;
    }

    /**
     * Marcar como pagado
     */
    public function markAsPaid(?string $notes = null, ?int $changedBy = null): bool
    {
        return $this->changeStatus(OrderStatus::PAID, $notes, $changedBy);
    }

    /**
     * Marcar como enviado
     */
    public function markAsShipped(?string $notes = null, ?int $changedBy = null): bool
    {
        return $this->changeStatus(OrderStatus::SHIPPED, $notes, $changedBy);
    }

    /**
     * Marcar como entregado
     */
    public function markAsDelivered(?string $notes = null, ?int $changedBy = null): bool
    {
        return $this->changeStatus(OrderStatus::DELIVERED, $notes, $changedBy);
    }

    /**
     * Cancelar pedido
     */
    public function cancel(?string $reason = null, ?int $cancelledBy = null): bool
    {
        if (!$this->statusEnum->isCancellable()) {
            throw new \Exception('El pedido no puede ser cancelado en su estado actual: ' . $this->statusEnum->getLabel());
        }

        return $this->changeStatus(OrderStatus::CANCELLED, $reason, $cancelledBy);
    }

    /**
     * Verificar si puede ser cancelado
     */
    public function canBeCancelled(): bool
    {
        return $this->statusEnum->isCancellable();
    }

    /**
     * Verificar si está activo
     */
    public function isActive(): bool
    {
        return $this->statusEnum->isActive();
    }

    /**
     * Verificar si está finalizado
     */
    public function isFinalized(): bool
    {
        return $this->statusEnum->isFinal();
    }

    /**
     * Obtener días desde la creación
     */
    public function getDaysOld(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Registrar cambio de estado en el historial
     */
    protected function recordStatusChange(?string $previousStatus, string $newStatus, ?string $notes = null, ?int $changedBy = null): void
    {
        $this->statusHistory()->create([
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'created_by' => $changedBy,
        ]);
    }

    /**
     * Calcular total basado en subtotal + impuestos + envío - descuentos
     */
    public function calculateTotal(): float
    {
        return $this->subtotal + $this->tax_amount + $this->shipping_amount - $this->discount_amount;
    }

    /**
     * Actualizar total del pedido
     */
    public function updateTotal(): bool
    {
        $this->total = $this->calculateTotal();
        return $this->save();
    }

    /**
     * Obtener último cambio de estado
     */
    public function getLatestStatusChange(): ?OrderStatusHistory
    {
        return $this->statusHistory()->first();
    }
}
