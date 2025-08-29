<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\Payment\ReportPaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest;
use App\Http\Requests\Payment\PaymentVerifyRequest;
use App\Http\Requests\Payment\PaymentRejectRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Payment = Payment::all();
        
        return response()->json([
            'data' => $Payment
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request)
    {
        
        $payment = Payment::create($request->validated());

        return response()->json([
            'message' => 'Payment created successfully',
            'data' => $payment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with(['order', 'paymentMethod', 'verifications'])->find($id);

        if (!$payment) {
            return $this->notFoundResponse('Pago no encontrado');
        }

        // Verificar que el usuario sea el dueño de la orden o un admin
        $user = Auth::user();
        if ($user->role !== 'admin' && $payment->order->user_id !== $user->id) {
            return $this->forbiddenResponse('No tienes permisos para ver este pago');
        }

        // Determinar el estado en español
        $statusMessages = [
            'pending' => 'Pendiente de verificación',
            'verified' => 'Verificado y aprobado',
            'rejected' => 'Rechazado',
            'refunded' => 'Reembolsado'
        ];

        return $this->successResponse(new PaymentResource($payment), 'Estado del pago obtenido exitosamente');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, string $id): JsonResponse
    {
        $payment = Payment::with('order')->find($id);

        if (!$payment) {
            return $this->notFoundResponse('Pago no encontrado');
        }

        // Verificar que el usuario sea el dueño de la orden
        $user = Auth::user();
        if ($user->role !== 'admin' && $payment->order->user_id !== $user->id) {
            return $this->forbiddenResponse('No tienes permisos para actualizar este pago');
        }

        // Solo permitir actualizar si está en estado pendiente (para usuarios normales)
        if ($user->role !== 'admin' && $payment->status !== 'pending') {
            return $this->conflictResponse(
                'Solo se pueden actualizar pagos en estado pendiente',
                ['current_status' => $payment->status]
            );
        }

        // Si se está actualizando el método de pago, verificar que pertenezca a la misma tienda
        if ($request->has('payment_method_id')) {
            $paymentMethod = PaymentMethod::find($request->payment_method_id);
            if (!$paymentMethod || $paymentMethod->store_id !== $payment->order->store_id) {
                return $this->forbiddenResponse('El método de pago no pertenece a esta tienda');
            }
        }

        $payment->update($request->validated());

        return $this->updatedResponse(
            new PaymentResource($payment->fresh(['order', 'paymentMethod'])),
            'Comprobante de pago actualizado exitosamente'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }

    /**
     * Reportar pago para una orden específica
     */
    public function reportPayment(ReportPaymentRequest $request, string $order): JsonResponse
    {
        $order = Order::find($order);

        if (!$order) {
            return $this->notFoundResponse('Orden no encontrada');
        }

        // Verificar que el usuario sea el dueño de la orden
        if (Auth::id() !== $order->user_id) {
            return $this->forbiddenResponse('No tienes permisos para reportar pagos en esta orden');
        }

        // Verificar que la orden esté en un estado que permita pagos
        if (in_array($order->status, ['cancelled', 'delivered', 'refunded'])) {
            return $this->conflictResponse(
                'No se pueden reportar pagos para una orden en estado: ' . $order->status,
                ['order_status' => $order->status]
            );
        }

        // Verificar que el método de pago pertenezca a la misma tienda
        $paymentMethod = PaymentMethod::find($request->payment_method_id);
        if (!$paymentMethod || $paymentMethod->store_id !== $order->store_id) {
            return $this->forbiddenResponse('El método de pago no pertenece a esta tienda');
        }

        // Verificar que el método de pago esté activo
        if ($paymentMethod->status !== 'active') {
            return $this->conflictResponse('El método de pago no está disponible');
        }

        // Verificar que el monto no exceda el total de la orden
        $totalPaid = $order->payments()->where('status', '!=', 'rejected')->sum('amount');
        $remainingAmount = $order->total - $totalPaid;

        if ($request->amount > $remainingAmount) {
            return $this->conflictResponse(
                'El monto del pago excede el saldo pendiente de la orden',
                [
                    'order_total' => $order->total,
                    'amount_paid' => $totalPaid,
                    'remaining_amount' => $remainingAmount,
                    'attempted_amount' => $request->amount
                ]
            );
        }

        // Crear el pago
        $paymentData = $request->validated();
        $paymentData['order_id'] = $order->id;
        $paymentData['store_id'] = $order->store_id;
        $paymentData['status'] = 'pending';
        $paymentData['paid_at'] = $paymentData['paid_at'] ?? now();

        $payment = Payment::create($paymentData);

        return $this->createdResponse(
            new PaymentResource($payment->load(['order', 'paymentMethod'])),
            'Pago reportado exitosamente. Está pendiente de verificación.'
        );
    }

    // ==========================================
    // MÉTODOS ADMINISTRATIVOS
    // ==========================================

    /**
     * Lista de pagos para administradores con filtros y paginación
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Payment::with(['order.user', 'paymentMethod', 'store'])
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->store_id, function ($q, $storeId) {
                return $q->where('store_id', $storeId);
            })
            ->when($request->payment_method_id, function ($q, $paymentMethodId) {
                return $q->where('payment_method_id', $paymentMethodId);
            })
            ->when($request->from_date, function ($q, $fromDate) {
                return $q->whereDate('paid_at', '>=', $fromDate);
            })
            ->when($request->to_date, function ($q, $toDate) {
                return $q->whereDate('paid_at', '<=', $toDate);
            })
            ->when($request->min_amount, function ($q, $minAmount) {
                return $q->where('amount', '>=', $minAmount);
            })
            ->when($request->max_amount, function ($q, $maxAmount) {
                return $q->where('amount', '<=', $maxAmount);
            })
            ->when($request->search, function ($q, $search) {
                return $q->whereHas('order', function ($orderQuery) use ($search) {
                    $orderQuery->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            });

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['created_at', 'paid_at', 'amount', 'status'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $perPage = min($request->get('per_page', 15), 100);
        $payments = $query->paginate($perPage);

        return $this->successResponse([
            'payments' => PaymentResource::collection($payments->items()),
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'last_page' => $payments->lastPage(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
            ],
            'filters' => [
                'status' => $request->status,
                'store_id' => $request->store_id,
                'payment_method_id' => $request->payment_method_id,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'min_amount' => $request->min_amount,
                'max_amount' => $request->max_amount,
                'search' => $request->search,
            ]
        ], 'Lista de pagos obtenida exitosamente');
    }

    /**
     * Verificar y aprobar un pago
     */
    public function verify(PaymentVerifyRequest $request, string $id): JsonResponse
    {
        $payment = Payment::with(['order', 'paymentMethod', 'store'])->find($id);

        if (!$payment) {
            return $this->notFoundResponse('Pago no encontrado');
        }

        if ($payment->status !== 'pending') {
            return $this->conflictResponse(
                'Solo se pueden verificar pagos en estado pendiente',
                ['current_status' => $payment->status]
            );
        }

        $payment->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'admin_notes' => $request->admin_notes,
        ]);

        $payment->verifications()->create([
            'user_id' => Auth::id(),
            'action' => 'verified',
            'notes' => $request->admin_notes,
            'actioned_at' => now(),
        ]);

        // Verificar si la orden está completamente pagada
        $order = $payment->order;
        $totalPaid = $order->payments()->where('status', 'verified')->sum('amount');
        
        if ($totalPaid >= $order->total) {
            $order->update(['payment_status' => 'paid']);
        }

        return $this->successResponse(
            new PaymentResource($payment->fresh(['order', 'paymentMethod', 'verifications'])),
            'Pago verificado y aprobado exitosamente'
        );
    }

    /**
     * Rechazar un pago
     */
    public function reject(PaymentRejectRequest $request, string $id): JsonResponse
    {
        $payment = Payment::with(['order', 'paymentMethod', 'store'])->find($id);

        if (!$payment) {
            return $this->notFoundResponse('Pago no encontrado');
        }

        if ($payment->status !== 'pending') {
            return $this->conflictResponse(
                'Solo se pueden rechazar pagos en estado pendiente',
                ['current_status' => $payment->status]
            );
        }

        $payment->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
            'admin_notes' => $request->admin_notes,
        ]);

        $payment->verifications()->create([
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'notes' => $request->rejection_reason . ($request->admin_notes ? "\n\nNotas adicionales: " . $request->admin_notes : ''),
            'reasons_rejection' => $request->rejection_reason,
            'actioned_at' => now(),
        ]);

        return $this->successResponse(
            new PaymentResource($payment->fresh(['order', 'paymentMethod', 'verifications'])),
            'Pago rechazado exitosamente'
        );
    }

    /**
     * Estadísticas de pagos para administradores
     */
    public function stats(Request $request): JsonResponse
    {
        $fromDate = $request->from_date ? \Carbon\Carbon::parse($request->from_date)->startOfDay() : \Carbon\Carbon::now()->startOfMonth();
        $toDate = $request->to_date ? \Carbon\Carbon::parse($request->to_date)->endOfDay() : \Carbon\Carbon::now()->endOfMonth();
        
        $baseQuery = Payment::whereBetween('created_at', [$fromDate, $toDate]);
        
        if ($request->store_id) {
            $baseQuery->where('store_id', $request->store_id);
        }

        // Estadísticas generales
        $totalPayments = (clone $baseQuery)->count();
        $pendingPayments = (clone $baseQuery)->where('status', 'pending')->count();
        $verifiedPayments = (clone $baseQuery)->where('status', 'verified')->count();
        $rejectedPayments = (clone $baseQuery)->where('status', 'rejected')->count();
        $refundedPayments = (clone $baseQuery)->where('status', 'refunded')->count();

        // Montos por estado
        $totalAmount = (clone $baseQuery)->sum('amount');
        $pendingAmount = (clone $baseQuery)->where('status', 'pending')->sum('amount');
        $verifiedAmount = (clone $baseQuery)->where('status', 'verified')->sum('amount');
        $rejectedAmount = (clone $baseQuery)->where('status', 'rejected')->sum('amount');
        $refundedAmount = (clone $baseQuery)->where('status', 'refunded')->sum('amount');

        // Estadísticas por método de pago
        $paymentsByMethod = (clone $baseQuery)
            ->with('paymentMethod')
            ->get()
            ->groupBy('payment_method_id')
            ->map(function ($payments) {
                $first = $payments->first();
                return [
                    'payment_method' => $first->paymentMethod->name ?? 'Método eliminado',
                    'count' => $payments->count(),
                    'total_amount' => $payments->sum('amount'),
                    'pending' => $payments->where('status', 'pending')->count(),
                    'verified' => $payments->where('status', 'verified')->count(),
                    'rejected' => $payments->where('status', 'rejected')->count(),
                ];
            })
            ->values();

        // Estadísticas por tienda (si no se filtró por tienda específica)
        $paymentsByStore = [];
        if (!$request->store_id) {
            $paymentsByStore = (clone $baseQuery)
                ->with('store')
                ->get()
                ->groupBy('store_id')
                ->map(function ($payments) {
                    $first = $payments->first();
                    return [
                        'store' => $first->store->name ?? 'Tienda eliminada',
                        'count' => $payments->count(),
                        'total_amount' => $payments->sum('amount'),
                        'pending' => $payments->where('status', 'pending')->count(),
                        'verified' => $payments->where('status', 'verified')->count(),
                        'rejected' => $payments->where('status', 'rejected')->count(),
                    ];
                })
                ->values();
        }

        // Estadísticas diarias para el período
        $dailyStats = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Tiempo promedio de verificación
        $avgVerificationTime = (clone $baseQuery)
            ->where('status', 'verified')
            ->whereNotNull('verified_at')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (verified_at - created_at)) / 3600) as avg_hours')
            ->value('avg_hours');

        return $this->successResponse([
            'period' => [
                'from' => $fromDate->toDateString(),
                'to' => $toDate->toDateString(),
            ],
            'summary' => [
                'total_payments' => $totalPayments,
                'total_amount' => round($totalAmount, 2),
                'by_status' => [
                    'pending' => [
                        'count' => $pendingPayments,
                        'amount' => round($pendingAmount, 2),
                        'percentage' => $totalPayments > 0 ? round(($pendingPayments / $totalPayments) * 100, 2) : 0,
                    ],
                    'verified' => [
                        'count' => $verifiedPayments,
                        'amount' => round($verifiedAmount, 2),
                        'percentage' => $totalPayments > 0 ? round(($verifiedPayments / $totalPayments) * 100, 2) : 0,
                    ],
                    'rejected' => [
                        'count' => $rejectedPayments,
                        'amount' => round($rejectedAmount, 2),
                        'percentage' => $totalPayments > 0 ? round(($rejectedPayments / $totalPayments) * 100, 2) : 0,
                    ],
                    'refunded' => [
                        'count' => $refundedPayments,
                        'amount' => round($refundedAmount, 2),
                        'percentage' => $totalPayments > 0 ? round(($refundedPayments / $totalPayments) * 100, 2) : 0,
                    ],
                ],
            ],
            'by_payment_method' => $paymentsByMethod,
            'by_store' => $paymentsByStore,
            'daily_stats' => $dailyStats,
            'metrics' => [
                'avg_verification_time_hours' => $avgVerificationTime ? round($avgVerificationTime, 2) : null,
                'verification_rate' => $totalPayments > 0 ? round((($verifiedPayments + $rejectedPayments) / $totalPayments) * 100, 2) : 0,
                'approval_rate' => ($verifiedPayments + $rejectedPayments) > 0 ? round(($verifiedPayments / ($verifiedPayments + $rejectedPayments)) * 100, 2) : 0,
            ],
        ], 'Estadísticas de pagos obtenidas exitosamente');
    }
}
