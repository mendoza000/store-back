<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CancelOrderRequest extends FormRequest
{
    use HandlesValidationErrors;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autorización manejada por middleware y controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => [
                'nullable',
                'string',
                'max:500',
                Rule::in([
                    'customer_request',
                    'payment_issues',
                    'address_issues',
                    'product_unavailable',
                    'duplicate_order',
                    'other'
                ])
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'reason.in' => 'La razón de cancelación debe ser una de las opciones válidas.',
            'reason.max' => 'La razón no puede exceder 500 caracteres.',
            'notes.max' => 'Las notas no pueden exceder 1000 caracteres.',
        ];
    }

    /**
     * Get validated reason.
     */
    public function getReason(): ?string
    {
        return $this->validated('reason');
    }

    /**
     * Get validated notes.
     */
    public function getNotes(): ?string
    {
        return $this->validated('notes');
    }

    /**
     * Get reason label for display.
     */
    public function getReasonLabel(): string
    {
        $reason = $this->getReason();

        return match ($reason) {
            'customer_request' => 'Solicitado por el cliente',
            'payment_issues' => 'Problemas de pago',
            'address_issues' => 'Problemas con la dirección',
            'product_unavailable' => 'Producto no disponible',
            'duplicate_order' => 'Pedido duplicado',
            'other' => 'Otro motivo',
            default => 'No especificado',
        };
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar datos antes de validación
        $this->merge([
            'reason' => $this->reason ? trim($this->reason) : null,
            'notes' => $this->notes ? trim($this->notes) : null,
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateOrderCanBeCancelled($validator);
        });
    }

    /**
     * Validate that the order can be cancelled.
     */
    protected function validateOrderCanBeCancelled($validator): void
    {
        $order = $this->route('order');

        if (!$order) {
            return; // El middleware de route model binding manejará esto
        }

        // Verificar que el pedido puede ser cancelado
        if (!$order->canBeCancelled()) {
            $validator->errors()->add(
                'order',
                "El pedido no puede ser cancelado en su estado actual: {$order->statusEnum->getLabel()}"
            );
        }

        // Verificar que el usuario es propietario del pedido (para usuarios no admin)
        // TODO: Implementar verificación de roles cuando se implemente el sistema de roles
        if ($order->user_id !== Auth::id()) {
            $validator->errors()->add(
                'order',
                'No tiene permisos para cancelar este pedido.'
            );
        }
    }

    /**
     * Get available cancellation reasons.
     */
    public static function getAvailableReasons(): array
    {
        return [
            'customer_request' => 'Solicitado por el cliente',
            'payment_issues' => 'Problemas de pago',
            'address_issues' => 'Problemas con la dirección',
            'product_unavailable' => 'Producto no disponible',
            'duplicate_order' => 'Pedido duplicado',
            'other' => 'Otro motivo',
        ];
    }
}
