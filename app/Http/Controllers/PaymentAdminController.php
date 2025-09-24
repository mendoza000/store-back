<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Payment;
use App\Models\PaymentVerification;
use App\Http\Resources\PaymentAdminResource;
use App\Http\Requests\Payment\PaymentVerifyRequest;
use App\Http\Requests\Payment\PaymentRejectRequest;
use App\Http\Requests\Payment\PaymentAdminStatsRequest;
use App\Http\Requests\Payment\PaymentListRequest;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use App\Services\CurrentStore;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentAdminController extends Controller
{
    use ApiResponseTrait;

    protected $currentStore;

    public function __construct(CurrentStore $currentStore)
    {
        $this->currentStore = $currentStore;
    }

    /**
     * Lista de pagos pendientes y filtrados para administradores
     * 
     * @param PaymentListRequest $request
     * @return JsonResponse
     */
    public function index(PaymentListRequest $request): JsonResponse
    {
        try {
            $store = $this->currentStore->get();
            
            $query = Payment::where('store_id', $store->id);

            // Aplicar filtros
            $this->applyFilters($query, $request);

            // Incluir relaciones
            $relationships = $request->input('include_relationships', ['order', 'order.user', 'paymentMethod', 'store']);
            $query->with($relationships);

            // Aplicar ordenamiento
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginación
            $perPage = $request->input('per_page', 15);
            $payments = $query->paginate($perPage);

            return $this->successResponse([
                'payments' => PaymentAdminResource::collection($payments->items()),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                    'from' => $payments->firstItem(),
                    'to' => $payments->lastItem(),
                ],
                'filters_applied' => $request->only([
                    'status', 'payment_method_id', 'customer_id', 'order_id',
                    'created_from', 'created_to', 'amount_min', 'amount_max',
                    'search', 'priority', 'requires_attention'
                ]),
            ], 'Lista de pagos obtenida exitosamente');

        } catch (\Exception $e) {
            return $this->internalErrorResponse('Error al obtener la lista de pagos: ' . $e->getMessage());
        }
    }

    /**
     * Verificar/Aprobar un pago
     * 
     * @param PaymentVerifyRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function verify(PaymentVerifyRequest $request, int $id): JsonResponse
    {
        try {
            $store = $this->currentStore->get();
            
            $payment = Payment::where('store_id', $store->id)
                ->where('id', $id)
                ->first();

            if (!$payment) {
                return $this->notFoundResponse('Pago no encontrado');
            }

            if ($payment->status !== 'pending') {
                return $this->errorResponse('INVALID_STATUS', 'Solo se pueden verificar pagos pendientes', 400);
            }

            DB::beginTransaction();

            try {
                // Actualizar el pago
                $payment->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                    'admin_notes' => $request->input('admin_notes'),
                ]);

                // Crear registro de verificación
                PaymentVerification::create([
                    'payment_id' => $payment->id,
                    'user_id' => Auth::id(),
                    'action' => 'verified',
                    'notes' => $request->input('notes'),
                    'actioned_at' => now(),
                ]);

                // Actualizar el estado de la orden si es necesario
                $order = $payment->order;
                if ($order && $order->status === 'pending_payment') {
                    $order->update(['status' => 'processing']);
                }

                DB::commit();

                // Cargar relaciones para la respuesta
                $payment->load(['order.user', 'paymentMethod', 'store', 'verifications']);

                return $this->successResponse([
                    'payment' => new PaymentAdminResource($payment)
                ], 'Pago verificado exitosamente');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return $this->internalErrorResponse('Error al verificar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar un pago
     * 
     * @param PaymentRejectRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function reject(PaymentRejectRequest $request, int $id): JsonResponse
    {
        try {
            $store = $this->currentStore->get();
            
            $payment = Payment::where('store_id', $store->id)
                ->where('id', $id)
                ->first();

            if (!$payment) {
                return $this->notFoundResponse('Pago no encontrado');
            }

            if ($payment->status !== 'pending') {
                return $this->errorResponse('Solo se pueden rechazar pagos pendientes', 
                400, 'PAYMENT_NOT_PENDING');
            }

            DB::beginTransaction();

            try {
                // Actualizar el pago
                $payment->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                    'verified_by' => Auth::id(),
                    'rejection_reason' => $request->input('rejection_reason'),
                    'admin_notes' => $request->input('admin_notes'),
                ]);

                // Crear registro de verificación
                PaymentVerification::create([
                    'payment_id' => $payment->id,
                    'user_id' => Auth::id(),
                    'action' => 'rejected',
                    'notes' => $request->input('notes'),
                    'reasons_rejection' => $request->input('rejection_reason'),
                    'actioned_at' => now(),
                ]);

                DB::commit();

                // Cargar relaciones para la respuesta
                $payment->load(['order.user', 'paymentMethod', 'store', 'verifications']);

                return $this->successResponse([
                    'payment' => new PaymentAdminResource($payment)
                ], 'Pago rechazado exitosamente');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return $this->internalErrorResponse('Error al rechazar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Estadísticas de pagos para administradores
     * 
     * @param PaymentAdminStatsRequest $request
     * @return JsonResponse
     */
    public function stats(PaymentAdminStatsRequest $request): JsonResponse
    {
        try {
            $store = $this->currentStore->get();
            
            // Obtener el rango de fechas según el período
            $dateRange = $this->getDateRange($request);
            
            $query = Payment::where('store_id', $store->id)
                ->whereBetween('created_at', $dateRange);

            // Filtrar por estado si se especifica
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            // Filtrar por método de pago si se especifica
            if ($request->has('payment_method_id')) {
                $query->where('payment_method_id', $request->input('payment_method_id'));
            }

            // Estadísticas básicas
            $stats = [
                'period' => [
                    'type' => $request->input('period', 'month'),
                    'start_date' => $dateRange[0]->toISOString(),
                    'end_date' => $dateRange[1]->toISOString(),
                ],
                'totals' => [
                    'total_payments' => $query->count(),
                    'total_amount' => $query->sum('amount'),
                    'average_amount' => $query->avg('amount') ?: 0,
                ],
                'by_status' => [
                    'pending' => Payment::where('store_id', $store->id)
                        ->whereBetween('created_at', $dateRange)
                        ->where('status', 'pending')
                        ->count(),
                    'verified' => Payment::where('store_id', $store->id)
                        ->whereBetween('created_at', $dateRange)
                        ->where('status', 'verified')
                        ->count(),
                    'rejected' => Payment::where('store_id', $store->id)
                        ->whereBetween('created_at', $dateRange)
                        ->where('status', 'rejected')
                        ->count(),
                    'refunded' => Payment::where('store_id', $store->id)
                        ->whereBetween('created_at', $dateRange)
                        ->where('status', 'refunded')
                        ->count(),
                ],
                'amounts_by_status' => [
                    'pending' => Payment::where('store_id', $store->id)
                        ->whereBetween('created_at', $dateRange)
                        ->where('status', 'pending')
                        ->sum('amount'),
                    'verified' => Payment::where('store_id', $store->id)
                        ->whereBetween('created_at', $dateRange)
                        ->where('status', 'verified')
                        ->sum('amount'),
                    'rejected' => Payment::where('store_id', $store->id)
                        ->whereBetween('created_at', $dateRange)
                        ->where('status', 'rejected')
                        ->sum('amount'),
                    'refunded' => Payment::where('store_id', $store->id)
                        ->whereBetween('created_at', $dateRange)
                        ->where('status', 'refunded')
                        ->sum('amount'),
                ],
                'processing_metrics' => [
                    'pending_count' => Payment::where('store_id', $store->id)
                        ->where('status', 'pending')
                        ->count(),
                    'requires_attention' => Payment::where('store_id', $store->id)
                        ->where('status', 'pending')
                        ->where(function ($q) {
                            $q->where('created_at', '<=', now()->subDays(2))
                              ->orWhere('amount', '>=', 300);
                        })
                        ->count(),
                    'high_priority' => Payment::where('store_id', $store->id)
                        ->where('status', 'pending')
                        ->where(function ($q) {
                            $q->where('created_at', '<=', now()->subDays(3))
                              ->orWhere('amount', '>=', 500);
                        })
                        ->count(),
                ],
            ];

            // Incluir detalles adicionales si se solicita
            if ($request->input('include_details', false)) {
                $stats['details'] = [
                    'by_payment_method' => Payment::where('store_id', $store->id)
                        ->whereBetween('created_at', $dateRange)
                        ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
                        ->select('payment_methods.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
                        ->groupBy('payment_methods.id', 'payment_methods.name')
                        ->get(),
                    'daily_trend' => Payment::where('store_id', $store->id)
                        ->whereBetween('created_at', $dateRange)
                        ->select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('COUNT(*) as count'),
                            DB::raw('SUM(amount) as total')
                        )
                        ->groupBy(DB::raw('DATE(created_at)'))
                        ->orderBy('date')
                        ->get(),
                ];
            }

            return $this->successResponse($stats, 'Estadísticas de pagos obtenidas exitosamente');

        } catch (\Exception $e) {
            return $this->internalErrorResponse('Error al obtener estadísticas: ' . $e->getMessage());
        }
    }

    /**
     * Aplicar filtros a la consulta de pagos
     */
    private function applyFilters($query, PaymentListRequest $request): void
    {
        // Filtro por estado
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtro por método de pago
        if ($request->has('payment_method_id')) {
            $query->where('payment_method_id', $request->input('payment_method_id'));
        }

        // Filtro por cliente
        if ($request->has('customer_id')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('user_id', $request->input('customer_id'));
            });
        }

        // Filtro por orden
        if ($request->has('order_id')) {
            $query->where('order_id', $request->input('order_id'));
        }

        // Filtros de fecha de creación
        if ($request->has('created_from')) {
            $query->where('created_at', '>=', $request->input('created_from'));
        }
        if ($request->has('created_to')) {
            $query->where('created_at', '<=', $request->input('created_to') . ' 23:59:59');
        }

        // Filtros de fecha de verificación
        if ($request->has('verified_from')) {
            $query->where('verified_at', '>=', $request->input('verified_from'));
        }
        if ($request->has('verified_to')) {
            $query->where('verified_at', '<=', $request->input('verified_to') . ' 23:59:59');
        }

        // Filtros de monto
        if ($request->has('amount_min')) {
            $query->where('amount', '>=', $request->input('amount_min'));
        }
        if ($request->has('amount_max')) {
            $query->where('amount', '<=', $request->input('amount_max'));
        }

        // Búsqueda general
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('admin_notes', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por número de referencia específico
        if ($request->has('reference_number')) {
            $query->where('reference_number', 'like', '%' . $request->input('reference_number') . '%');
        }

        // Filtro por prioridad
        if ($request->has('priority')) {
            $priority = $request->input('priority');
            switch ($priority) {
                case 'high':
                    $query->where('status', 'pending')
                          ->where(function ($q) {
                              $q->where('created_at', '<=', now()->subDays(3))
                                ->orWhere('amount', '>=', 500);
                          });
                    break;
                case 'medium':
                    $query->where('status', 'pending')
                          ->where('created_at', '<=', now()->subDays(1))
                          ->where('created_at', '>', now()->subDays(3))
                          ->where('amount', '<', 500)
                          ->where('amount', '>=', 100);
                    break;
                case 'low':
                    $query->where('status', 'pending')
                          ->where('created_at', '>', now()->subDays(1))
                          ->where('amount', '<', 100);
                    break;
            }
        }

        // Filtro por requiere atención
        if ($request->has('requires_attention') && $request->input('requires_attention')) {
            $query->where('status', 'pending')
                  ->where(function ($q) {
                      $q->where('created_at', '<=', now()->subDays(2))
                        ->orWhere('amount', '>=', 300);
                  });
        }
    }

    /**
     * Obtener el rango de fechas según el período solicitado
     */
    private function getDateRange(PaymentAdminStatsRequest $request): array
    {
        $period = $request->input('period', 'month');

        switch ($period) {
            case 'today':
                return [
                    Carbon::today(),
                    Carbon::today()->endOfDay()
                ];
            case 'week':
                return [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ];
            case 'month':
                return [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ];
            case 'quarter':
                return [
                    Carbon::now()->startOfQuarter(),
                    Carbon::now()->endOfQuarter()
                ];
            case 'year':
                return [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear()
                ];
            case 'custom':
                return [
                    Carbon::parse($request->input('start_date'))->startOfDay(),
                    Carbon::parse($request->input('end_date'))->endOfDay()
                ];
            default:
                return [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ];
        }
    }
}
