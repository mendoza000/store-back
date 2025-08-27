<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
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
        $paymentId = $this->route('payment') ?? $this->route('id');
        
        return [
            'reference_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('payments', 'reference_number')->ignore($paymentId)
            ],
            'receipt_url' => 'sometimes|nullable|url|max:500',
            'notes' => 'sometimes|nullable|string|max:1000',
            'paid_at' => 'sometimes|nullable|date|before_or_equal:now',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'reference_number.required' => 'El número de referencia es requerido.',
            'reference_number.unique' => 'Este número de referencia ya existe.',
            'receipt_url.url' => 'La URL del comprobante debe ser válida.',
            'paid_at.date' => 'La fecha de pago debe ser una fecha válida.',
            'paid_at.before_or_equal' => 'La fecha de pago no puede ser futura.',
        ];
    }
} 