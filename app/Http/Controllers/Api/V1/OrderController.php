<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * Obtener lista de pedidos del usuario autenticado
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 15), 50); // Máximo 50 por página
            $status = $request->get('status');

            $query = Order::where('user_id', Auth::id())
                ->with(['items', 'statusHistory' => function ($query) {
                    $query->latest()->limit(1); // Solo el último cambio de estado
                }])
                ->orderBy('created_at', 'desc');

            // Filtrar por estado si se proporciona
            if ($status && in_array($status, OrderStatus::values())) {
                $query->where('status', $status);
            }

            $orders = $query->paginate($perPage);

            $orders->getCollection()->transform(function ($order) {
                return $this->formatOrderResponse($order);
            });

            return $this->paginatedResponse($orders, 'Pedidos obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->errorResponse('ORDER_LIST_ERROR', 'Error al obtener los pedidos', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Crear nuevo pedido desde carrito
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Obtener carrito activo del usuario
            $cart = Cart::where('user_id', Auth::id())
                ->where('status', 'active')
                ->with('items')
                ->first();

            if (!$cart || $cart->isEmpty()) {
                return $this->errorResponse('EMPTY_CART', 'No tiene productos en el carrito', 400);
            }

            // Crear pedido desde carrito
            $order = Order::createFromCart(
                cart: $cart,
                shippingAddress: $request->getShippingAddress(),
                billingAddress: $request->getBillingAddress()
            );

            // Agregar notas si las hay
            if ($request->getNotes()) {
                $order->update(['notes' => $request->getNotes()]);
            }

            DB::commit();

            $orderData = $this->formatOrderResponse($order->load(['items', 'statusHistory']));

            return $this->successResponse($orderData, 'Pedido creado exitosamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('ORDER_CREATE_ERROR', 'Error al crear el pedido', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mostrar pedido específico por número de orden
     */
    public function show(string $orderNumber): JsonResponse
    {
        try {
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->with(['items', 'statusHistory.createdBy'])
                ->first();

            if (!$order) {
                return $this->errorResponse('ORDER_NOT_FOUND', 'Pedido no encontrado', 404);
            }

            $orderData = $this->formatOrderResponse($order, true); // Incluir historial completo

            return $this->successResponse($orderData, 'Pedido obtenido exitosamente');
        } catch (\Exception $e) {
            return $this->errorResponse('ORDER_SHOW_ERROR', 'Error al obtener el pedido', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cancelar pedido del usuario
     */
    public function cancel(CancelOrderRequest $request, Order $order): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Verificar que el usuario es propietario del pedido
            if ($order->user_id !== Auth::id()) {
                return $this->errorResponse('UNAUTHORIZED', 'No tiene permisos para cancelar este pedido', 403);
            }

            // Cancelar el pedido
            $order->cancel($request->getReason(), Auth::id());

            // Agregar notas adicionales si las hay
            if ($request->getNotes()) {
                OrderStatusHistory::recordChange(
                    orderId: $order->id,
                    previousStatus: $order->getOriginal('status'),
                    newStatus: OrderStatus::CANCELLED->value,
                    notes: $request->getNotes(),
                    reason: $request->getReason(),
                    createdBy: Auth::id()
                );
            }

            DB::commit();

            $orderData = $this->formatOrderResponse($order->load(['items', 'statusHistory']));

            return $this->successResponse($orderData, 'Pedido cancelado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('ORDER_CANCEL_ERROR', 'Error al cancelar el pedido', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * ADMIN: Obtener lista de todos los pedidos con filtros
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 20), 100);
            $status = $request->get('status');
            $userId = $request->get('user_id');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            $query = Order::with(['user', 'items', 'statusHistory' => function ($query) {
                $query->latest()->limit(1);
            }])
                ->orderBy('created_at', 'desc');

            // Aplicar filtros
            if ($status && in_array($status, OrderStatus::values())) {
                $query->where('status', $status);
            }

            if ($userId) {
                $query->where('user_id', $userId);
            }

            if ($dateFrom) {
                $query->where('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->where('created_at', '<=', $dateTo);
            }

            $orders = $query->paginate($perPage);

            // Transform the collection within the paginator
            $orders->getCollection()->transform(function ($order) {
                return $this->formatOrderResponse($order, false, true); // Incluir datos de admin
            });

            return $this->paginatedResponse($orders, 'Pedidos obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->errorResponse('ADMIN_ORDER_LIST_ERROR', 'Error al obtener los pedidos', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * ADMIN: Actualizar estado de pedido
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        try {
            DB::beginTransaction();

            $newStatus = $request->getStatus();
            $notes = $request->getNotes();
            $reason = $request->getReason();

            // Cambiar estado del pedido
            $order->changeStatus($newStatus, $notes, Auth::id());

            // Agregar metadata adicional para envíos
            if ($newStatus === OrderStatus::SHIPPED && $request->getShippingMetadata()) {
                $order->statusHistory()->latest()->first()?->update([
                    'metadata' => $request->getShippingMetadata()
                ]);
            }

            DB::commit();

            $orderData = $this->formatOrderResponse($order->load(['items', 'statusHistory']));

            return $this->successResponse($orderData, 'Estado del pedido actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('ORDER_STATUS_UPDATE_ERROR', 'Error al actualizar el estado del pedido', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * ADMIN: Obtener estadísticas de pedidos
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $dateFrom = $request->get('date_from', now()->subDays(30)->startOfDay());
            $dateTo = $request->get('date_to', now()->endOfDay());

            $stats = [
                'total_orders' => Order::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                'orders_by_status' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->selectRaw('status, count(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'total_revenue' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->whereIn('status', [OrderStatus::PAID->value, OrderStatus::PROCESSING->value, OrderStatus::SHIPPED->value, OrderStatus::DELIVERED->value])
                    ->sum('total'),
                'average_order_value' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->whereIn('status', [OrderStatus::PAID->value, OrderStatus::PROCESSING->value, OrderStatus::SHIPPED->value, OrderStatus::DELIVERED->value])
                    ->avg('total'),
                'orders_per_day' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->selectRaw('DATE(created_at) as date, count(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Estadísticas obtenidas exitosamente');
        } catch (\Exception $e) {
            return $this->errorResponse('ORDER_STATS_ERROR', 'Error al obtener estadísticas', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Formatear respuesta del pedido
     */
    protected function formatOrderResponse(Order $order, bool $includeFullHistory = false, bool $includeAdminData = false): array
    {
        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'status_label' => $order->statusEnum->getLabel(),
            'total' => $order->total,
            'subtotal' => $order->subtotal,
            'tax_amount' => $order->tax_amount,
            'shipping_amount' => $order->shipping_amount,
            'discount_amount' => $order->discount_amount,
            'shipping_address' => $order->shipping_address,
            'billing_address' => $order->billing_address,
            'notes' => $order->notes,
            'created_at' => $order->created_at->toISOString(),
            'updated_at' => $order->updated_at->toISOString(),
            'paid_at' => $order->paid_at?->toISOString(),
            'shipped_at' => $order->shipped_at?->toISOString(),
            'delivered_at' => $order->delivered_at?->toISOString(),
            'cancelled_at' => $order->cancelled_at?->toISOString(),
        ];

        // Incluir items del pedido
        if ($order->relationLoaded('items')) {
            $data['items'] = $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                    'product_name' => $item->product_name,
                    'product_sku' => $item->product_sku,
                    'product_image' => $item->imageUrl,
                    'variant_description' => $item->variantDescription,
                ];
            });
        }

        // Incluir historial de estados
        if ($order->relationLoaded('statusHistory')) {
            if ($includeFullHistory) {
                $data['status_history'] = $order->statusHistory->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'previous_status' => $history->previous_status,
                        'new_status' => $history->new_status,
                        'notes' => $history->notes,
                        'reason' => $history->reason,
                        'created_by' => $history->createdBy?->name,
                        'created_at' => $history->created_at->toISOString(),
                        'customer_notified' => $history->customer_notified,
                        'metadata' => $history->metadata,
                    ];
                });
            } else {
                $latestStatus = $order->statusHistory->first();
                if ($latestStatus) {
                    $data['latest_status_change'] = [
                        'previous_status' => $latestStatus->previous_status,
                        'new_status' => $latestStatus->new_status,
                        'notes' => $latestStatus->notes,
                        'created_at' => $latestStatus->created_at->toISOString(),
                    ];
                }
            }
        }

        // Incluir datos adicionales para admin
        if ($includeAdminData && $order->relationLoaded('user')) {
            $data['user'] = [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
            ];
            $data['days_old'] = $order->getDaysOld();
            $data['can_be_cancelled'] = $order->canBeCancelled();
        }

        return $data;
    }
}
