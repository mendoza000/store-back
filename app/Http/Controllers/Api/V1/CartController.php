<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Requests\Cart\MergeCartRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    use ApiResponseTrait;

    /**
     * Constructor - Aplicar middleware de autenticación opcional
     */
    public function __construct()
    {
        // Permitir acceso tanto a usuarios autenticados como guests
        $this->middleware('auth:sanctum')->except(['index', 'store', 'update', 'destroy', 'clear']);
        $this->middleware('throttle:60,1'); // Rate limiting
    }

    /**
     * Obtener carrito actual del usuario o guest
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cart = $this->getCurrentCart($request);

            if (!$cart) {
                return $this->successResponse([
                    'cart' => null,
                    'items' => [],
                    'summary' => $this->getEmptyCartSummary(),
                ], 'Carrito vacío');
            }

            $cartData = $this->formatCartResponse($cart);

            return $this->successResponse($cartData, 'Carrito obtenido exitosamente');
        } catch (\Exception $e) {
            return $this->errorResponse('CART_ERROR', 'Error al obtener el carrito', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Agregar item al carrito
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $cart = $this->getOrCreateCart($request);

            // Obtener precio del producto (por ahora hardcoded, luego desde Product model)
            $productPrice = $request->getPrice() ?? $this->getProductPrice($request->getProductId());

            $cartItem = CartItem::addToCart(
                cartId: $cart->id,
                productId: $request->getProductId(),
                quantity: $request->getQuantity(),
                price: $productPrice
            );

            DB::commit();

            $cart->refresh();
            $cartData = $this->formatCartResponse($cart);

            return $this->successResponse($cartData, 'Producto agregado al carrito exitosamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('CART_ADD_ERROR', 'Error al agregar producto al carrito', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar cantidad de item en el carrito
     */
    public function update(UpdateCartItemRequest $request, int $itemId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $cartItem = $this->findCartItem($itemId, $request);

            if (!$cartItem) {
                return $this->errorResponse('Item no encontrado en el carrito', 404);
            }

            $cartItem->updateQuantity($request->getQuantity());

            DB::commit();

            $cart = $cartItem->cart;
            $cartData = $this->formatCartResponse($cart);

            return $this->successResponse($cartData, 'Cantidad actualizada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('CART_UPDATE_ERROR', 'Error al actualizar el item', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remover item del carrito
     */
    public function destroy(Request $request, int $itemId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $cartItem = $this->findCartItem($itemId, $request);

            if (!$cartItem) {
                return $this->errorResponse('Item no encontrado en el carrito', 404);
            }

            $cart = $cartItem->cart;
            $cartItem->delete();

            DB::commit();

            $cart->refresh();
            $cartData = $this->formatCartResponse($cart);

            return $this->successResponse($cartData, 'Producto removido del carrito exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('CART_REMOVE_ERROR', 'Error al remover el producto', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Vaciar carrito completo
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $cart = $this->getCurrentCart($request);

            if (!$cart) {
                return $this->successResponse([
                    'cart' => null,
                    'items' => [],
                    'summary' => $this->getEmptyCartSummary(),
                ], 'El carrito ya está vacío');
            }

            DB::beginTransaction();

            $cart->clear();

            DB::commit();

            $cartData = $this->formatCartResponse($cart);

            return $this->successResponse($cartData, 'Carrito vaciado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('CART_CLEAR_ERROR', 'Error al vaciar el carrito', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fusionar carrito de guest con carrito de usuario autenticado
     */
    public function merge(MergeCartRequest $request): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return $this->errorResponse('UNAUTHENTICATED', 'Debe estar autenticado para fusionar carritos', 401);
            }

            DB::beginTransaction();

            $guestCart = Cart::where('session_id', $request->getGuestSessionId())
                ->where('status', 'active')
                ->first();

            if (!$guestCart) {
                return $this->errorResponse('GUEST_CART_NOT_FOUND', 'Carrito de invitado no encontrado', 404);
            }

            $userCart = Cart::getForUser(Auth::id());
            $mergedCart = $guestCart->mergeWithUserCart($userCart);

            DB::commit();

            $cartData = $this->formatCartResponse($mergedCart);

            return $this->successResponse($cartData, 'Carritos fusionados exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('CART_MERGE_ERROR', 'Error al fusionar carritos', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener carrito actual basado en autenticación o sesión
     */
    protected function getCurrentCart(Request $request): ?Cart
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())
                ->where('status', 'active')
                ->with('items')
                ->first();
        }

        $sessionId = $request->header('X-Session-ID') ?? $request->cookie('guest_session_id');

        if (!$sessionId) {
            return null;
        }

        return Cart::where('session_id', $sessionId)
            ->where('status', 'active')
            ->with('items')
            ->first();
    }

    /**
     * Obtener o crear carrito
     */
    protected function getOrCreateCart(Request $request): Cart
    {
        if (Auth::check()) {
            return Cart::getForUser(Auth::id());
        }

        $sessionId = $request->header('X-Session-ID') ?? $request->cookie('guest_session_id') ?? Str::uuid();

        return Cart::getForGuest($sessionId);
    }

    /**
     * Encontrar item del carrito que pertenece al usuario actual
     */
    protected function findCartItem(int $itemId, Request $request): ?CartItem
    {
        $cart = $this->getCurrentCart($request);

        if (!$cart) {
            return null;
        }

        return $cart->items()->where('id', $itemId)->first();
    }

    /**
     * Obtener precio del producto
     * TODO: Implementar cuando se cree el modelo Product
     */
    protected function getProductPrice(int $productId): float
    {
        // Por ahora retornamos un precio fijo
        // Cuando implementemos Product, será: Product::find($productId)->price
        return 99.99;
    }

    /**
     * Formatear respuesta del carrito
     */
    protected function formatCartResponse(?Cart $cart): array
    {
        if (!$cart) {
            return [
                'cart' => null,
                'items' => [],
                'summary' => $this->getEmptyCartSummary(),
            ];
        }

        return [
            'cart' => [
                'id' => $cart->id,
                'user_id' => $cart->user_id,
                'session_id' => $cart->session_id,
                'status' => $cart->status,
                'expires_at' => $cart->expires_at?->toISOString(),
                'created_at' => $cart->created_at->toISOString(),
                'updated_at' => $cart->updated_at->toISOString(),
            ],
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                    'product_info' => $item->productInfo,
                    'created_at' => $item->created_at->toISOString(),
                    'updated_at' => $item->updated_at->toISOString(),
                ];
            })->toArray(),
            'summary' => [
                'total_items' => $cart->totalItems,
                'subtotal' => $cart->subtotal,
                'items_count' => $cart->items->count(),
                'is_empty' => $cart->isEmpty(),
            ],
        ];
    }

    /**
     * Obtener resumen de carrito vacío
     */
    protected function getEmptyCartSummary(): array
    {
        return [
            'total_items' => 0,
            'subtotal' => 0,
            'items_count' => 0,
            'is_empty' => true,
        ];
    }
}
