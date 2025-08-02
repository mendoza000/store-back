<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Validaciones antes de crear
        static::creating(function ($cartItem) {
            // Validar que la cantidad sea positiva
            if ($cartItem->quantity <= 0) {
                throw new \InvalidArgumentException('La cantidad debe ser mayor a 0');
            }

            // Validar stock disponible (cuando implementemos el modelo Product)
            // $cartItem->validateStock();
        });

        // Validaciones antes de actualizar
        static::updating(function ($cartItem) {
            if ($cartItem->quantity <= 0) {
                throw new \InvalidArgumentException('La cantidad debe ser mayor a 0');
            }

            // Validar stock disponible
            // $cartItem->validateStock();
        });

        // Extender expiración del carrito cuando se modifica un item
        static::saved(function ($cartItem) {
            if ($cartItem->cart) {
                $cartItem->cart->extendExpiration();
            }
        });
    }

    /**
     * Relación con Cart
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
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
     * Calcular total del item (precio * cantidad)
     */
    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    /**
     * Actualizar cantidad del item
     */
    public function updateQuantity($quantity)
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('La cantidad debe ser mayor a 0');
        }

        // Validar stock disponible
        // $this->validateStock($quantity);

        $this->update(['quantity' => $quantity]);

        return $this;
    }

    /**
     * Incrementar cantidad del item
     */
    public function incrementQuantity($amount = 1)
    {
        $newQuantity = $this->quantity + $amount;
        return $this->updateQuantity($newQuantity);
    }

    /**
     * Decrementar cantidad del item
     */
    public function decrementQuantity($amount = 1)
    {
        $newQuantity = $this->quantity - $amount;

        if ($newQuantity <= 0) {
            $this->delete();
            return null;
        }

        return $this->updateQuantity($newQuantity);
    }

    /**
     * Validar stock disponible
     * TODO: Implementar cuando se cree el modelo Product
     */
    protected function validateStock($quantity = null)
    {
        $quantity = $quantity ?? $this->quantity;

        // Por ahora retornamos true
        // Cuando implementemos Product, aquí validaremos:
        // if ($this->product && $this->product->track_quantity) {
        //     if ($this->product->stock < $quantity) {
        //         throw new \Exception("Stock insuficiente. Disponible: {$this->product->stock}");
        //     }
        // }

        return true;
    }

    /**
     * Scope: Items con stock suficiente
     */
    public function scopeInStock($query)
    {
        // TODO: Implementar cuando se cree el modelo Product
        // return $query->whereHas('product', function ($q) {
        //     $q->where('stock', '>', 0)
        //       ->orWhere('track_quantity', false);
        // });
        return $query;
    }

    /**
     * Scope: Items sin stock
     */
    public function scopeOutOfStock($query)
    {
        // TODO: Implementar cuando se cree el modelo Product
        // return $query->whereHas('product', function ($q) {
        //     $q->where('track_quantity', true)
        //       ->whereColumn('stock', '<', 'cart_items.quantity');
        // });
        return $query->whereRaw('1 = 0'); // Por ahora retorna vacío
    }

    /**
     * Verificar si el item tiene stock suficiente
     */
    public function hasStock()
    {
        return $this->validateStock();
    }

    /**
     * Obtener información del producto para mostrar en el carrito
     */
    public function getProductInfoAttribute()
    {
        // TODO: Cuando implementemos Product, retornar información completa
        return [
            'id' => $this->product_id,
            'name' => 'Producto #' . $this->product_id, // Placeholder
            'image' => null,
            'stock_available' => true,
        ];
    }

    /**
     * Crear o actualizar item en carrito
     */
    public static function addToCart($cartId, $productId, $quantity, $price)
    {
        $existingItem = static::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            // Si ya existe, incrementar cantidad
            return $existingItem->incrementQuantity($quantity);
        } else {
            // Crear nuevo item
            return static::create([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
            ]);
        }
    }
}
