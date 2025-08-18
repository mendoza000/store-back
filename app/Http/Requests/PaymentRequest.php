<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
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
        return [
            
            'order_id' => 'required|exists:orders,id',
            'payment_method_id' => 'required|exists:payment_method,id',
            'amount' => 'required|numeric|min:0',
            'reference_number' => 'required|string|max:255|unique:payment,reference_number',
            'receipt_url' => 'nullable|url|max:255',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,verified,rejected,refunded',
            'paid_at' => 'nullable|date',
            'verified_at' => 'nullable|date',
            'verified_by' => 'nullable|date',
            'rejected_at' => 'nullable|date',
            'refunded_at' => 'nullable|date',

        ];
    }
}
