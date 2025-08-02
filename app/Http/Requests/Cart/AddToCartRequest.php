<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
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
            'product_id' => [
                'required',
                'integer',
                'min:1',
                // TODO: Agregar Rule::exists('products', 'id') cuando se implemente la tabla products
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:999',
            ],
            'price' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'El ID del producto es obligatorio.',
            'product_id.integer' => 'El ID del producto debe ser un número entero.',
            'product_id.min' => 'El ID del producto debe ser mayor a 0.',

            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad debe ser al menos 1.',
            'quantity.max' => 'La cantidad no puede ser mayor a 999.',

            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
            'price.max' => 'El precio es demasiado alto.',
        ];
    }

    /**
     * Get validated product ID.
     */
    public function getProductId(): int
    {
        return $this->validated('product_id');
    }

    /**
     * Get validated quantity.
     */
    public function getQuantity(): int
    {
        return $this->validated('quantity');
    }

    /**
     * Get validated price (optional).
     */
    public function getPrice(): ?float
    {
        return $this->validated('price');
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar y normalizar datos antes de validación
        $this->merge([
            'product_id' => (int) $this->product_id,
            'quantity' => (int) $this->quantity,
            'price' => $this->price ? (float) $this->price : null,
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validaciones adicionales si es necesario
            $this->validateProductAvailability($validator);
        });
    }

    /**
     * Validate product availability.
     * TODO: Implementar cuando se cree el sistema de productos
     */
    protected function validateProductAvailability($validator): void
    {
        // Por ahora no validamos disponibilidad
        // Cuando implementemos products, aquí verificaremos:
        // - Que el producto existe
        // - Que tiene stock suficiente
        // - Que está activo/disponible
    }
}
