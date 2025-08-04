<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;

class MergeCartRequest extends FormRequest
{
    use HandlesValidationErrors;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autorización manejada por middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guest_session_id' => [
                'required',
                'string',
                'uuid',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'guest_session_id.required' => 'El ID de sesión de invitado es obligatorio.',
            'guest_session_id.string' => 'El ID de sesión debe ser una cadena de texto.',
            'guest_session_id.uuid' => 'El ID de sesión debe ser un UUID válido.',
        ];
    }

    /**
     * Get validated guest session ID.
     */
    public function getGuestSessionId(): string
    {
        return $this->validated('guest_session_id');
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalizar datos antes de validación
        $this->merge([
            'guest_session_id' => trim($this->guest_session_id),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validaciones adicionales
            $this->validateGuestCartExists($validator);
        });
    }

    /**
     * Validate that guest cart exists.
     */
    protected function validateGuestCartExists($validator): void
    {
        $guestSessionId = $this->input('guest_session_id');

        if (!$guestSessionId) {
            return;
        }

        // Verificar que existe un carrito guest con ese session_id
        $guestCart = \App\Models\Cart::where('session_id', $guestSessionId)
            ->where('status', 'active')
            ->first();

        if (!$guestCart) {
            $validator->errors()->add(
                'guest_session_id',
                'No se encontró un carrito activo para la sesión proporcionada.'
            );
            return;
        }

        // Verificar que el carrito tiene items
        if ($guestCart->items()->count() === 0) {
            $validator->errors()->add(
                'guest_session_id',
                'El carrito de invitado está vacío.'
            );
        }
    }
}
