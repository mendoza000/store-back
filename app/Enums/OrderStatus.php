<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    /**
     * Obtener todos los valores como array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtener estados que permiten cancelación
     */
    public static function cancellableStatuses(): array
    {
        return [
            self::PENDING->value,
            self::PAID->value,
        ];
    }

    /**
     * Obtener estados que indican el pedido está en proceso
     */
    public static function activeStatuses(): array
    {
        return [
            self::PENDING->value,
            self::PAID->value,
            self::PROCESSING->value,
            self::SHIPPED->value,
        ];
    }

    /**
     * Obtener estados finales
     */
    public static function finalStatuses(): array
    {
        return [
            self::DELIVERED->value,
            self::CANCELLED->value,
        ];
    }

    /**
     * Verificar si el estado permite cancelación
     */
    public function isCancellable(): bool
    {
        return in_array($this->value, self::cancellableStatuses());
    }

    /**
     * Verificar si es un estado activo
     */
    public function isActive(): bool
    {
        return in_array($this->value, self::activeStatuses());
    }

    /**
     * Verificar si es un estado final
     */
    public function isFinal(): bool
    {
        return in_array($this->value, self::finalStatuses());
    }

    /**
     * Obtener el siguiente estado lógico
     */
    public function getNextStatus(): ?self
    {
        return match ($this) {
            self::PENDING => self::PAID,
            self::PAID => self::PROCESSING,
            self::PROCESSING => self::SHIPPED,
            self::SHIPPED => self::DELIVERED,
            default => null,
        };
    }

    /**
     * Obtener descripción legible del estado
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::PAID => 'Pagado',
            self::PROCESSING => 'Procesando',
            self::SHIPPED => 'Enviado',
            self::DELIVERED => 'Entregado',
            self::CANCELLED => 'Cancelado',
        };
    }

    /**
     * Obtener color para UI
     */
    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::PAID => 'blue',
            self::PROCESSING => 'purple',
            self::SHIPPED => 'orange',
            self::DELIVERED => 'green',
            self::CANCELLED => 'red',
        };
    }
}
