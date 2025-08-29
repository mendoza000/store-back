<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\HandlesValidationErrors;

class ReportPaymentRequest extends FormRequest
{
    use HandlesValidationErrors;
    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'reference_number' => 'required|string|max:255|unique:payments,reference_number',
            'receipt_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
            'paid_at' => 'nullable|date|before_or_equal:now',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'payment_method_id.required' => 'El método de pago es requerido.',
            'payment_method_id.exists' => 'El método de pago seleccionado no es válido.',
            'amount.required' => 'El monto es requerido.',
            'amount.numeric' => 'El monto debe ser un número válido.',
            'amount.min' => 'El monto debe ser mayor a 0.',
            'reference_number.required' => 'El número de referencia es requerido.',
            'reference_number.unique' => 'Este número de referencia ya existe.',
            'receipt_url.url' => 'La URL del comprobante debe ser válida.',
            'paid_at.date' => 'La fecha de pago debe ser una fecha válida.',
            'paid_at.before_or_equal' => 'La fecha de pago no puede ser futura.',
        ];
    }
} 