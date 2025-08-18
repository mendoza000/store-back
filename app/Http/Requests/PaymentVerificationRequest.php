<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentVerificationRequest extends FormRequest
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
            
            'payment_id' => 'required|exists:payment,id',
            'user_id' => 'required|exists:users,id',
            'action' => 'required|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'actioned_at' => 'nullable|date',
            'verification_history' => 'nullable|array',
            'reasons_rejection' => 'nullable|string|max:2000',
            

        ];
    }
}
