<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Cart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'session_id',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $dates = [
        'expires_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Crear session_id automáticamente si no existe
        static::creating(function ($cart) {
            if (empty($cart->session_id) && empty($cart->user_id)) {
                $cart->session_id = Str::uuid();
            }

            // Establecer fecha de expiración (24 horas por defecto)
            if (empty($cart->expires_at)) {
                $cart->expires_at = Carbon::now()->addHours(24);
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
     * Relación con CartItems
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Scope: Carritos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope: Carritos expirados
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '<=', Carbon::now());
    }

    /**
     * Scope: Carritos de usuarios guest
     */
    public function scopeGuest($query)
    {
        return $query->whereNull('user_id')
            ->whereNotNull('session_id');
    }

    /**
     * Scope: Carritos de usuarios registrados
     */
    public function scopeRegistered($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Obtener o crear carrito para usuario autenticado
     */
    public static function getForUser($userId)
    {
        return static::firstOrCreate(
            ['user_id' => $userId, 'status' => 'active'],
            ['expires_at' => Carbon::now()->addDays(30)] // Carritos de usuarios duran más
        );
    }

    /**
     * Obtener o crear carrito para usuario guest
     */
    public static function getForGuest($sessionId)
    {
        return static::firstOrCreate(
            ['session_id' => $sessionId, 'status' => 'active'],
            ['expires_at' => Carbon::now()->addHours(24)]
        );
    }

    /**
     * Merge carrito guest con carrito de usuario
     */
    public function mergeWithUserCart($userCart)
    {
        foreach ($this->items as $guestItem) {
            $existingItem = $userCart->items()
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($existingItem) {
                // Sumar cantidades si el producto ya existe
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $guestItem->quantity
                ]);
            } else {
                // Mover item al carrito del usuario
                $guestItem->update(['cart_id' => $userCart->id]);
            }
        }

        // Marcar carrito guest como completado
        $this->update(['status' => 'completed']);

        return $userCart;
    }

    /**
     * Calcular subtotal del carrito
     */
    public function getSubtotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Calcular cantidad total de items
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Verificar si el carrito está vacío
     */
    public function isEmpty()
    {
        return $this->items->count() === 0;
    }

    /**
     * Verificar si el carrito ha expirado
     */
    public function isExpired()
    {
        return $this->expires_at && Carbon::now()->isAfter($this->expires_at);
    }

    /**
     * Extender tiempo de expiración
     */
    public function extendExpiration($hours = 24)
    {
        $this->update([
            'expires_at' => Carbon::now()->addHours($hours)
        ]);
    }

    /**
     * Vaciar carrito
     */
    public function clear()
    {
        $this->items()->delete();
        return $this;
    }

    /**
     * Marcar carrito como completado
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
        return $this;
    }

    /**
     * Limpieza automática de carritos expirados
     */
    public static function cleanupExpired()
    {
        $expiredCarts = static::expired()->get();

        foreach ($expiredCarts as $cart) {
            $cart->update(['status' => 'expired']);
        }

        // Eliminar carritos muy antiguos (más de 30 días)
        static::where('status', 'expired')
            ->where('updated_at', '<', Carbon::now()->subDays(30))
            ->delete();

        return $expiredCarts->count();
    }
}
