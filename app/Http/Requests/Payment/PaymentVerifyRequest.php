<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\HandlesValidationErrors;

class PaymentVerifyRequest extends FormRequest
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
            'notes' => ['nullable', 'string', 'max:1000'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
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
            'notes.string' => 'Las notas deben ser texto válido.',
            'notes.max' => 'Las notas no pueden exceder 1000 caracteres.',
            'admin_notes.string' => 'Las notas administrativas deben ser texto válido.',
            'admin_notes.max' => 'Las notas administrativas no pueden exceder 1000 caracteres.',
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
            'notes' => 'notas',
            'admin_notes' => 'notas administrativas',
        ];
    }
} 