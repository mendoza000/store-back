<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\Payment\ReportPaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest;
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

        $paymentData = $payment->toArray();
        $paymentData['status_message'] = $statusMessages[$payment->status] ?? 'Estado desconocido';

        return $this->successResponse($paymentData, 'Estado del pago obtenido exitosamente');
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
            $payment->fresh(['order', 'paymentMethod']),
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
            $payment->load(['order', 'paymentMethod']),
            'Pago reportado exitosamente. Está pendiente de verificación.'
        );
    }
}
