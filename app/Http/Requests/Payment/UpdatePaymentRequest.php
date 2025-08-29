<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Traits\HandlesValidationErrors;

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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $paymentId = $this->route('id');
        
        return [
            'payment_method_id' => ['sometimes', 'integer', 'exists:payment_methods,id'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'reference_number' => [
                'sometimes', 
                'string', 
                'max:255', 
                Rule::unique('payments', 'reference_number')->ignore($paymentId)
            ],
            'receipt_url' => ['nullable', 'url', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'paid_at' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(['pending', 'verified', 'rejected', 'refunded'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_method_id.exists' => 'El método de pago seleccionado no existe.',
            'amount.min' => 'El monto debe ser mayor a 0.',
            'reference_number.unique' => 'Este número de referencia ya existe.',
            'receipt_url.url' => 'La URL del comprobante debe ser válida.',
            'paid_at.date' => 'La fecha de pago debe ser una fecha válida.',
            'status.in' => 'El estado del pago no es válido.',
        ];
    }
} 