<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\HandlesValidationErrors;

class PaymentMethodRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255|unique:payment_methods,name',
            'type' => 'required|in:mobile_payment,bank_transfer,paypal,cash,crypto',
            'account_info' => 'required|array',
            'account_info.bank_name' => 'required_if:type,bank_transfer|string|max:255',
            'account_info.account_number' => 'required_if:type,bank_transfer|string|max:255',
            'account_info.account_holder' => 'required_if:type,bank_transfer|string|max:255',
            'account_info.document_type' => 'nullable|string|max:10',
            'account_info.document_number' => 'nullable|string|max:50',
            'account_info.phone' => 'required_if:type,mobile_payment|string|max:50',
            'account_info.email' => 'required_if:type,paypal|email|max:255',
            'account_info.wallet_address' => 'required_if:type,crypto|string|max:500',
            'instructions' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ];
    }
}
