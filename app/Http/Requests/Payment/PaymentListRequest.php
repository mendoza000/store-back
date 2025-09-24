<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\HandlesValidationErrors;

class PaymentListRequest extends FormRequest
{
    use HandlesValidationErrors;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el middleware AdminOnly
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Paginación
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            
            // Filtros
            'status' => ['nullable', 'string', 'in:pending,verified,rejected,refunded'],
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            
            // Filtros de fecha
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date', 'after_or_equal:created_from'],
            'verified_from' => ['nullable', 'date'],
            'verified_to' => ['nullable', 'date', 'after_or_equal:verified_from'],
            
            // Filtros de monto
            'amount_min' => ['nullable', 'numeric', 'min:0'],
            'amount_max' => ['nullable', 'numeric', 'gte:amount_min'],
            
            // Búsqueda
            'search' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            
            // Ordenamiento
            'sort_by' => ['nullable', 'string', 'in:created_at,amount,status,verified_at,order_id'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
            
            // Opciones adicionales
            'priority' => ['nullable', 'string', 'in:low,medium,high'],
            'requires_attention' => ['nullable', 'boolean'],
            'include_relationships' => ['nullable', 'array'],
            'include_relationships.*' => ['string', 'in:order,order.user,paymentMethod,store,verifications'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'La página debe ser un número entero.',
            'page.min' => 'La página debe ser mayor a 0.',
            'per_page.integer' => 'Los elementos por página debe ser un número entero.',
            'per_page.min' => 'Los elementos por página debe ser mayor a 0.',
            'per_page.max' => 'Los elementos por página no puede ser mayor a 100.',
            
            'status.in' => 'El estado debe ser uno de: pending, verified, rejected, refunded.',
            'payment_method_id.exists' => 'El método de pago especificado no existe.',
            'customer_id.exists' => 'El cliente especificado no existe.',
            'order_id.exists' => 'La orden especificada no existe.',
            
            'created_from.date' => 'La fecha de creación desde debe ser una fecha válida.',
            'created_to.date' => 'La fecha de creación hasta debe ser una fecha válida.',
            'created_to.after_or_equal' => 'La fecha de creación hasta debe ser posterior o igual a la fecha desde.',
            'verified_from.date' => 'La fecha de verificación desde debe ser una fecha válida.',
            'verified_to.date' => 'La fecha de verificación hasta debe ser una fecha válida.',
            'verified_to.after_or_equal' => 'La fecha de verificación hasta debe ser posterior o igual a la fecha desde.',
            
            'amount_min.numeric' => 'El monto mínimo debe ser un número.',
            'amount_min.min' => 'El monto mínimo debe ser mayor o igual a 0.',
            'amount_max.numeric' => 'El monto máximo debe ser un número.',
            'amount_max.gte' => 'El monto máximo debe ser mayor o igual al monto mínimo.',
            
            'search.string' => 'El término de búsqueda debe ser texto válido.',
            'search.max' => 'El término de búsqueda no puede exceder 255 caracteres.',
            'reference_number.string' => 'El número de referencia debe ser texto válido.',
            'reference_number.max' => 'El número de referencia no puede exceder 255 caracteres.',
            
            'sort_by.in' => 'El campo de ordenamiento debe ser uno de: created_at, amount, status, verified_at, order_id.',
            'sort_direction.in' => 'La dirección de ordenamiento debe ser asc o desc.',
            
            'priority.in' => 'La prioridad debe ser una de: low, medium, high.',
            'requires_attention.boolean' => 'El campo requiere atención debe ser verdadero o falso.',
            
            'include_relationships.array' => 'Las relaciones a incluir deben ser una lista.',
            'include_relationships.*.in' => 'Las relaciones válidas son: order, order.user, paymentMethod, store, verifications.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'page' => 'página',
            'per_page' => 'elementos por página',
            'status' => 'estado',
            'payment_method_id' => 'método de pago',
            'customer_id' => 'cliente',
            'order_id' => 'orden',
            'created_from' => 'fecha de creación desde',
            'created_to' => 'fecha de creación hasta',
            'verified_from' => 'fecha de verificación desde',
            'verified_to' => 'fecha de verificación hasta',
            'amount_min' => 'monto mínimo',
            'amount_max' => 'monto máximo',
            'search' => 'búsqueda',
            'reference_number' => 'número de referencia',
            'sort_by' => 'ordenar por',
            'sort_direction' => 'dirección de ordenamiento',
            'priority' => 'prioridad',
            'requires_attention' => 'requiere atención',
            'include_relationships' => 'relaciones a incluir',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Establecer valores por defecto
        if (!$this->has('per_page')) {
            $this->merge(['per_page' => 15]);
        }

        if (!$this->has('sort_by')) {
            $this->merge(['sort_by' => 'created_at']);
        }

        if (!$this->has('sort_direction')) {
            $this->merge(['sort_direction' => 'desc']);
        }

        if (!$this->has('include_relationships')) {
            $this->merge(['include_relationships' => ['order', 'order.user', 'paymentMethod', 'store']]);
        }
    }
} 