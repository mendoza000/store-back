<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
                ];
            }),
            'customer' => $this->whenLoaded('order.user', function () {
                return [
                    'id' => $this->order->user->id,
                    'name' => $this->order->user->name,
                    'email' => $this->order->user->email,
                ];
            }),
            'amount' => [
                'value' => $this->amount,
                'formatted' => '$' . number_format($this->amount, 2),
            ],
            'reference_number' => $this->reference_number,
            'receipt_url' => $this->receipt_url,
            'notes' => $this->notes,
            'status' => $this->status,
            'status_message' => $this->getStatusMessage(),
            'dates' => [
                'paid_at' => $this->paid_at?->toISOString(),
                'verified_at' => $this->verified_at?->toISOString(),
                'rejected_at' => $this->rejected_at?->toISOString(),
                'refunded_at' => $this->refunded_at?->toISOString(),
                'created_at' => $this->created_at->toISOString(),
                'updated_at' => $this->updated_at->toISOString(),
            ],
            'verified_by' => $this->when($this->verified_by, function () {
                return [
                    'id' => $this->verified_by,
                    'name' => $this->verifiedBy?->name ?? 'Usuario eliminado',
                ];
            }),
            'admin_notes' => $this->when($this->admin_notes, $this->admin_notes),
            'rejection_reason' => $this->when($this->rejection_reason, $this->rejection_reason),
            'verification_info' => $this->when($this->relationLoaded('verifications'), function () {
                $latestVerification = $this->verifications->sortByDesc('actioned_at')->first();
                
                return [
                    'has_verifications' => $this->verifications->count() > 0,
                    'total_verifications' => $this->verifications->count(),
                    'latest_verification' => $latestVerification ? [
                        'id' => $latestVerification->id,
                        'action' => $latestVerification->action,
                        'notes' => $latestVerification->notes,
                        'reasons_rejection' => $latestVerification->reasons_rejection,
                        'actioned_at' => $latestVerification->actioned_at?->toISOString(),
                        'verified_by' => [
                            'id' => $latestVerification->user_id,
                            'name' => $latestVerification->user?->name ?? 'Usuario eliminado',
                        ],
                    ] : null,
                ];
            }),
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
} 