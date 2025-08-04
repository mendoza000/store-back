<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
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
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:999',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad debe ser al menos 1.',
            'quantity.max' => 'La cantidad no puede ser mayor a 999.',
        ];
    }

    /**
     * Get validated quantity.
     */
    public function getQuantity(): int
    {
        return $this->validated('quantity');
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar y normalizar datos antes de validación
        $this->merge([
            'quantity' => (int) $this->quantity,
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validaciones adicionales
            $this->validateStockAvailability($validator);
        });
    }

    /**
     * Validate stock availability for the new quantity.
     * TODO: Implementar cuando se cree el sistema de productos
     */
    protected function validateStockAvailability($validator): void
    {
        // Por ahora no validamos stock
        // Cuando implementemos products, aquí verificaremos:
        // - Que hay stock suficiente para la nueva cantidad
        // - Que el producto sigue disponible
    }
}
