<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    use HandlesValidationErrors;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autorización manejada por middleware de admin
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(OrderStatus::values())
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'reason' => [
                'nullable',
                'string',
                'max:255',
            ],
            'notify_customer' => [
                'nullable',
                'boolean',
            ],
            'tracking_number' => [
                'nullable',
                'string',
                'max:100',
                'required_if:status,shipped'
            ],
            'carrier' => [
                'nullable',
                'string',
                'max:50',
                'required_if:status,shipped'
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser uno de los valores válidos.',
            'notes.max' => 'Las notas no pueden exceder 1000 caracteres.',
            'reason.max' => 'La razón no puede exceder 255 caracteres.',
            'tracking_number.required_if' => 'El número de seguimiento es obligatorio cuando el estado es "enviado".',
            'carrier.required_if' => 'La empresa de envío es obligatoria cuando el estado es "enviado".',
            'tracking_number.max' => 'El número de seguimiento no puede exceder 100 caracteres.',
            'carrier.max' => 'La empresa de envío no puede exceder 50 caracteres.',
        ];
    }

    /**
     * Get validated status as enum.
     */
    public function getStatus(): OrderStatus
    {
        return OrderStatus::from($this->validated('status'));
    }

    /**
     * Get validated notes.
     */
    public function getNotes(): ?string
    {
        return $this->validated('notes');
    }

    /**
     * Get validated reason.
     */
    public function getReason(): ?string
    {
        return $this->validated('reason');
    }

    /**
     * Get validated notify customer flag.
     */
    public function shouldNotifyCustomer(): bool
    {
        return $this->validated('notify_customer', true);
    }

    /**
     * Get validated tracking number.
     */
    public function getTrackingNumber(): ?string
    {
        return $this->validated('tracking_number');
    }

    /**
     * Get validated carrier.
     */
    public function getCarrier(): ?string
    {
        return $this->validated('carrier');
    }

    /**
     * Get metadata for shipping status.
     */
    public function getShippingMetadata(): ?array
    {
        if ($this->getStatus() !== OrderStatus::SHIPPED) {
            return null;
        }

        return [
            'tracking_number' => $this->getTrackingNumber(),
            'carrier' => $this->getCarrier(),
            'shipped_at' => now()->toISOString(),
        ];
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalizar datos antes de validación
        $this->merge([
            'status' => strtolower(trim($this->status ?? '')),
            'notes' => $this->notes ? trim($this->notes) : null,
            'reason' => $this->reason ? trim($this->reason) : null,
            'notify_customer' => $this->boolean('notify_customer', true),
            'tracking_number' => $this->tracking_number ? trim($this->tracking_number) : null,
            'carrier' => $this->carrier ? trim($this->carrier) : null,
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateStatusTransition($validator);
        });
    }

    /**
     * Validate that the status transition is valid.
     */
    protected function validateStatusTransition($validator): void
    {
        $order = $this->route('order');

        if (!$order) {
            return; // El middleware de route model binding manejará esto
        }

        $currentStatus = OrderStatus::from($order->status);
        $newStatus = OrderStatus::from($this->input('status'));

        // No permitir cambiar a un estado final si ya está en estado final
        if ($currentStatus->isFinal() && $newStatus !== $currentStatus) {
            $validator->errors()->add(
                'status',
                "No se puede cambiar el estado desde '{$currentStatus->getLabel()}' a '{$newStatus->getLabel()}'."
            );
            return;
        }

        // Validaciones específicas de transición
        $this->validateSpecificTransitions($validator, $currentStatus, $newStatus);
    }

    /**
     * Validate specific status transitions.
     */
    protected function validateSpecificTransitions($validator, OrderStatus $from, OrderStatus $to): void
    {
        // Reglas específicas de transición
        match ($to) {
            OrderStatus::PAID => $this->validateTransitionToPaid($validator, $from),
            OrderStatus::PROCESSING => $this->validateTransitionToProcessing($validator, $from),
            OrderStatus::SHIPPED => $this->validateTransitionToShipped($validator, $from),
            OrderStatus::DELIVERED => $this->validateTransitionToDelivered($validator, $from),
            OrderStatus::CANCELLED => $this->validateTransitionToCancelled($validator, $from),
            default => null,
        };
    }

    /**
     * Validate transition to paid status.
     */
    protected function validateTransitionToPaid($validator, OrderStatus $from): void
    {
        if ($from !== OrderStatus::PENDING) {
            $validator->errors()->add(
                'status',
                'Solo se puede marcar como pagado un pedido pendiente.'
            );
        }
    }

    /**
     * Validate transition to processing status.
     */
    protected function validateTransitionToProcessing($validator, OrderStatus $from): void
    {
        if (!in_array($from, [OrderStatus::PENDING, OrderStatus::PAID])) {
            $validator->errors()->add(
                'status',
                'Solo se puede procesar un pedido pendiente o pagado.'
            );
        }
    }

    /**
     * Validate transition to shipped status.
     */
    protected function validateTransitionToShipped($validator, OrderStatus $from): void
    {
        if (!in_array($from, [OrderStatus::PAID, OrderStatus::PROCESSING])) {
            $validator->errors()->add(
                'status',
                'Solo se puede enviar un pedido pagado o en procesamiento.'
            );
        }
    }

    /**
     * Validate transition to delivered status.
     */
    protected function validateTransitionToDelivered($validator, OrderStatus $from): void
    {
        if ($from !== OrderStatus::SHIPPED) {
            $validator->errors()->add(
                'status',
                'Solo se puede marcar como entregado un pedido enviado.'
            );
        }
    }

    /**
     * Validate transition to cancelled status.
     */
    protected function validateTransitionToCancelled($validator, OrderStatus $from): void
    {
        if (!$from->isCancellable()) {
            $validator->errors()->add(
                'status',
                "No se puede cancelar un pedido en estado '{$from->getLabel()}'."
            );
        }
    }
}
