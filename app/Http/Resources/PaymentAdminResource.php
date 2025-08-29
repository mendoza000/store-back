<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_number' => $this->whenLoaded('order', function () {
                return $this->order->order_number;
            }),
            'payment_method_id' => $this->payment_method_id,
            'payment_method' => $this->whenLoaded('paymentMethod', function () {
                return [
                    'id' => $this->paymentMethod->id,
                    'name' => $this->paymentMethod->name,
                    'type' => $this->paymentMethod->type,
                    'account_info' => $this->paymentMethod->account_info,
                ];
            }),
            'store' => $this->whenLoaded('store', function () {
                return [
                    'id' => $this->store->id,
                    'name' => $this->store->name,
                    'slug' => $this->store->slug,
                ];
            }),
            'customer' => $this->whenLoaded('order.user', function () {
                return [
                    'id' => $this->order->user->id,
                    'name' => $this->order->user->name,
                    'email' => $this->order->user->email,
                    'phone' => $this->order->user->phone ?? null,
                ];
            }),
            'order_details' => $this->whenLoaded('order', function () {
                return [
                    'total' => $this->order->total,
                    'subtotal' => $this->order->subtotal,
                    'tax' => $this->order->tax,
                    'shipping' => $this->order->shipping,
                    'status' => $this->order->status,
                    'items_count' => $this->order->items?->count() ?? 0,
                ];
            }),
            'amount' => [
                'value' => $this->amount,
                'formatted' => '$' . number_format($this->amount, 2),
            ],
            'reference_number' => $this->reference_number,
            'receipt_url' => $this->receipt_url,
            'notes' => $this->notes,
            'admin_notes' => $this->admin_notes,
            'rejection_reason' => $this->rejection_reason,
            'status' => $this->status,
            'status_message' => $this->getStatusMessage(),
            'priority' => $this->getPriority(),
            'dates' => [
                'paid_at' => $this->paid_at?->toISOString(),
                'verified_at' => $this->verified_at?->toISOString(),
                'rejected_at' => $this->rejected_at?->toISOString(),
                'refunded_at' => $this->refunded_at?->toISOString(),
                'created_at' => $this->created_at->toISOString(),
                'updated_at' => $this->updated_at->toISOString(),
            ],
            'verification_info' => [
                'verified_by' => $this->when($this->verified_by, function () {
                    return [
                        'id' => $this->verified_by,
                        'name' => $this->verifiedBy?->name ?? 'Usuario eliminado',
                    ];
                }),
                'total_verifications' => $this->whenLoaded('verifications', function () {
                    return $this->verifications->count();
                }),
                'latest_verification' => $this->whenLoaded('verifications', function () {
                    $latestVerification = $this->verifications->sortByDesc('actioned_at')->first();
                    
                    return $latestVerification ? [
                        'id' => $latestVerification->id,
                        'action' => $latestVerification->action,
                        'notes' => $latestVerification->notes,
                        'reasons_rejection' => $latestVerification->reasons_rejection,
                        'actioned_at' => $latestVerification->actioned_at?->toISOString(),
                        'verified_by' => [
                            'id' => $latestVerification->user_id,
                            'name' => $latestVerification->user?->name ?? 'Usuario eliminado',
                        ],
                    ] : null;
                }),
            ],
            'admin_metadata' => [
                'days_pending' => $this->status === 'pending' ? now()->diffInDays($this->created_at) : null,
                'processing_time' => $this->verified_at || $this->rejected_at ? 
                    $this->created_at->diffForHumans($this->verified_at ?? $this->rejected_at) : null,
                'requires_attention' => $this->requiresAttention(),
            ],
        ];
    }

    /**
     * Obtener el mensaje de estado en español
     */
    private function getStatusMessage(): string
    {
        $statusMessages = [
            'pending' => 'Pendiente de verificación',
            'verified' => 'Verificado y aprobado',
            'rejected' => 'Rechazado',
            'refunded' => 'Reembolsado'
        ];

        return $statusMessages[$this->status] ?? 'Estado desconocido';
    }

    /**
     * Determinar la prioridad del pago para revisión
     */
    private function getPriority(): string
    {
        if ($this->status !== 'pending') {
            return 'none';
        }

        $daysPending = now()->diffInDays($this->created_at);
        $amount = $this->amount;

        if ($daysPending >= 3 || $amount >= 500) {
            return 'high';
        }

        if ($daysPending >= 1 || $amount >= 100) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Verificar si el pago requiere atención especial
     */
    private function requiresAttention(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $daysPending = now()->diffInDays($this->created_at);
        
        return $daysPending >= 2 || $this->amount >= 300;
    }
} 