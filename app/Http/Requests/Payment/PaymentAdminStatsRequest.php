<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\HandlesValidationErrors;

class PaymentAdminStatsRequest extends FormRequest
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
            'period' => ['nullable', 'string', 'in:today,week,month,quarter,year,custom'],
            'start_date' => ['nullable', 'date', 'required_if:period,custom'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date', 'required_if:period,custom'],
            'status' => ['nullable', 'string', 'in:pending,verified,rejected,refunded'],
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
            'include_details' => ['nullable', 'boolean'],
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
            'period.in' => 'El período debe ser uno de: today, week, month, quarter, year, custom.',
            'start_date.required_if' => 'La fecha de inicio es requerida cuando el período es personalizado.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'end_date.required_if' => 'La fecha de fin es requerida cuando el período es personalizado.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'status.in' => 'El estado debe ser uno de: pending, verified, rejected, refunded.',
            'payment_method_id.integer' => 'El ID del método de pago debe ser un número entero.',
            'payment_method_id.exists' => 'El método de pago especificado no existe.',
            'include_details.boolean' => 'El campo include_details debe ser verdadero o falso.',
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
            'period' => 'período',
            'start_date' => 'fecha de inicio',
            'end_date' => 'fecha de fin',
            'status' => 'estado',
            'payment_method_id' => 'método de pago',
            'include_details' => 'incluir detalles',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Establecer valores por defecto si no se proporcionan
        if (!$this->has('period')) {
            $this->merge(['period' => 'month']);
        }

        if (!$this->has('include_details')) {
            $this->merge(['include_details' => false]);
        }
    }
} 