<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'created_by',
        'previous_status',
        'new_status',
        'notes',
        'reason',
        'metadata',
        'customer_notified',
        'notified_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'customer_notified' => 'boolean',
        'notified_at' => 'datetime',
    ];

    protected $dates = [
        'notified_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Validaciones antes de crear
        static::creating(function ($statusHistory) {
            $statusHistory->validateStatusChange();
        });

        // Auto-notificación después de crear (si está configurado)
        static::created(function ($statusHistory) {
            if ($statusHistory->shouldAutoNotify()) {
                $statusHistory->notifyCustomer();
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
     * Relación con User (quien hizo el cambio)
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor: Obtener enum del estado anterior
     */
    protected function previousStatusEnum(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->previous_status ? OrderStatus::from($this->previous_status) : null,
        );
    }

    /**
     * Accessor: Obtener enum del nuevo estado
     */
    protected function newStatusEnum(): Attribute
    {
        return Attribute::make(
            get: fn() => OrderStatus::from($this->new_status),
        );
    }

    /**
     * Accessor: Obtener tiempo transcurrido desde el cambio
     */
    protected function timeAgo(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at->diffForHumans(),
        );
    }

    /**
     * Accessor: Verificar si fue un cambio automático
     */
    protected function isAutomatic(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_by === null,
        );
    }

    /**
     * Scope: Cambios de un pedido específico
     */
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope: Cambios a un estado específico
     */
    public function scopeToStatus($query, string|OrderStatus $status)
    {
        $statusValue = $status instanceof OrderStatus ? $status->value : $status;
        return $query->where('new_status', $statusValue);
    }

    /**
     * Scope: Cambios desde un estado específico
     */
    public function scopeFromStatus($query, string|OrderStatus $status)
    {
        $statusValue = $status instanceof OrderStatus ? $status->value : $status;
        return $query->where('previous_status', $statusValue);
    }

    /**
     * Scope: Cambios realizados por usuario específico
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope: Cambios automáticos del sistema
     */
    public function scopeAutomatic($query)
    {
        return $query->whereNull('created_by');
    }

    /**
     * Scope: Cambios manuales por usuarios
     */
    public function scopeManual($query)
    {
        return $query->whereNotNull('created_by');
    }

    /**
     * Scope: Cambios no notificados al cliente
     */
    public function scopeNotNotified($query)
    {
        return $query->where('customer_notified', false);
    }

    /**
     * Scope: Cambios en un rango de fechas
     */
    public function scopeInDateRange($query, Carbon $from, Carbon $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Validar cambio de estado
     */
    protected function validateStatusChange(): void
    {
        // Validar que el nuevo estado es válido
        if (!in_array($this->new_status, OrderStatus::values())) {
            throw new \InvalidArgumentException("Estado inválido: {$this->new_status}");
        }

        // Validar que el estado anterior es válido (si existe)
        if ($this->previous_status && !in_array($this->previous_status, OrderStatus::values())) {
            throw new \InvalidArgumentException("Estado anterior inválido: {$this->previous_status}");
        }

        // Validar que hay un cambio real de estado
        if ($this->previous_status === $this->new_status) {
            throw new \InvalidArgumentException('El estado anterior y nuevo son iguales');
        }
    }

    /**
     * Verificar si debe notificar automáticamente al cliente
     */
    protected function shouldAutoNotify(): bool
    {
        // Notificar automáticamente en ciertos cambios importantes
        $notifiableStatuses = [
            OrderStatus::PAID->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::DELIVERED->value,
            OrderStatus::CANCELLED->value,
        ];

        return in_array($this->new_status, $notifiableStatuses);
    }

    /**
     * Notificar al cliente sobre el cambio
     */
    public function notifyCustomer(): bool
    {
        if ($this->customer_notified) {
            return false; // Ya notificado
        }

        // TODO: Implementar notificación real (email, SMS, etc.)
        // Por ahora solo marcamos como notificado

        $this->update([
            'customer_notified' => true,
            'notified_at' => now(),
        ]);

        return true;
    }

    /**
     * Obtener descripción legible del cambio
     */
    public function getChangeDescription(): string
    {
        $from = $this->previous_status ? OrderStatus::from($this->previous_status)->getLabel() : 'Sin estado';
        $to = OrderStatus::from($this->new_status)->getLabel();

        return "Estado cambiado de '{$from}' a '{$to}'";
    }

    /**
     * Obtener información completa del cambio para logs
     */
    public function getLogData(): array
    {
        return [
            'order_id' => $this->order_id,
            'order_number' => $this->order?->order_number,
            'previous_status' => $this->previous_status,
            'new_status' => $this->new_status,
            'changed_by' => $this->created_by,
            'changed_by_name' => $this->createdBy?->name ?? 'Sistema',
            'notes' => $this->notes,
            'reason' => $this->reason,
            'timestamp' => $this->created_at->toISOString(),
            'is_automatic' => $this->is_automatic,
            'customer_notified' => $this->customer_notified,
        ];
    }

    /**
     * Crear registro de cambio de estado
     */
    public static function recordChange(
        int $orderId,
        ?string $previousStatus,
        string $newStatus,
        ?string $notes = null,
        ?string $reason = null,
        ?int $createdBy = null,
        ?array $metadata = null
    ): self {
        return static::create([
            'order_id' => $orderId,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'reason' => $reason,
            'created_by' => $createdBy,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Obtener último cambio de estado para un pedido
     */
    public static function getLatestForOrder(int $orderId): ?self
    {
        return static::where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Obtener historial completo de un pedido
     */
    public static function getHistoryForOrder(int $orderId): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('order_id', $orderId)
            ->with(['createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener estadísticas de cambios por estado
     */
    public static function getStatusChangeStats(Carbon $from = null, Carbon $to = null): array
    {
        $query = static::query();

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->selectRaw('new_status, count(*) as total')
            ->groupBy('new_status')
            ->pluck('total', 'new_status')
            ->toArray();
    }
}
