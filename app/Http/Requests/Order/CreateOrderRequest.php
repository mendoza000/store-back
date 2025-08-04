<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateOrderRequest extends FormRequest
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
            // Dirección de envío (obligatoria)
            'shipping_address' => ['required', 'array'],
            'shipping_address.first_name' => ['required', 'string', 'max:50'],
            'shipping_address.last_name' => ['required', 'string', 'max:50'],
            'shipping_address.company' => ['nullable', 'string', 'max:100'],
            'shipping_address.address_line_1' => ['required', 'string', 'max:255'],
            'shipping_address.address_line_2' => ['nullable', 'string', 'max:255'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.state' => ['required', 'string', 'max:100'],
            'shipping_address.postal_code' => ['required', 'string', 'max:20'],
            'shipping_address.country' => ['required', 'string', 'max:100'],
            'shipping_address.phone' => ['required', 'string', 'max:20'],

            // Dirección de facturación (opcional, usa shipping si no se proporciona)
            'billing_address' => ['nullable', 'array'],
            'billing_address.first_name' => ['required_with:billing_address', 'string', 'max:50'],
            'billing_address.last_name' => ['required_with:billing_address', 'string', 'max:50'],
            'billing_address.company' => ['nullable', 'string', 'max:100'],
            'billing_address.address_line_1' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.address_line_2' => ['nullable', 'string', 'max:255'],
            'billing_address.city' => ['required_with:billing_address', 'string', 'max:100'],
            'billing_address.state' => ['required_with:billing_address', 'string', 'max:100'],
            'billing_address.postal_code' => ['required_with:billing_address', 'string', 'max:20'],
            'billing_address.country' => ['required_with:billing_address', 'string', 'max:100'],
            'billing_address.phone' => ['required_with:billing_address', 'string', 'max:20'],

            // Notas del pedido (opcional)
            'notes' => ['nullable', 'string', 'max:1000'],

            // Usar dirección de envío como facturación
            'use_shipping_as_billing' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            // Mensajes para dirección de envío
            'shipping_address.required' => 'La dirección de envío es obligatoria.',
            'shipping_address.first_name.required' => 'El nombre es obligatorio.',
            'shipping_address.last_name.required' => 'El apellido es obligatorio.',
            'shipping_address.address_line_1.required' => 'La dirección es obligatoria.',
            'shipping_address.city.required' => 'La ciudad es obligatoria.',
            'shipping_address.state.required' => 'El estado/provincia es obligatorio.',
            'shipping_address.postal_code.required' => 'El código postal es obligatorio.',
            'shipping_address.country.required' => 'El país es obligatorio.',
            'shipping_address.phone.required' => 'El teléfono es obligatorio.',

            // Mensajes para dirección de facturación
            'billing_address.first_name.required_with' => 'El nombre es obligatorio cuando se proporciona dirección de facturación.',
            'billing_address.last_name.required_with' => 'El apellido es obligatorio cuando se proporciona dirección de facturación.',
            'billing_address.address_line_1.required_with' => 'La dirección es obligatoria cuando se proporciona dirección de facturación.',
            'billing_address.city.required_with' => 'La ciudad es obligatoria cuando se proporciona dirección de facturación.',
            'billing_address.state.required_with' => 'El estado/provincia es obligatorio cuando se proporciona dirección de facturación.',
            'billing_address.postal_code.required_with' => 'El código postal es obligatorio cuando se proporciona dirección de facturación.',
            'billing_address.country.required_with' => 'El país es obligatorio cuando se proporciona dirección de facturación.',
            'billing_address.phone.required_with' => 'El teléfono es obligatorio cuando se proporciona dirección de facturación.',

            // Otros mensajes
            'notes.max' => 'Las notas no pueden exceder 1000 caracteres.',
        ];
    }

    /**
     * Get validated shipping address.
     */
    public function getShippingAddress(): array
    {
        return $this->validated('shipping_address');
    }

    /**
     * Get validated billing address (or shipping if not provided).
     */
    public function getBillingAddress(): array
    {
        $billingAddress = $this->validated('billing_address');
        $useShippingAsBilling = $this->validated('use_shipping_as_billing', false);

        if (!$billingAddress || $useShippingAsBilling) {
            return $this->getShippingAddress();
        }

        return $billingAddress;
    }

    /**
     * Get validated notes.
     */
    public function getNotes(): ?string
    {
        return $this->validated('notes');
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalizar datos booleanos
        $this->merge([
            'use_shipping_as_billing' => $this->boolean('use_shipping_as_billing'),
        ]);

        // Limpiar espacios en blanco de las direcciones
        if ($this->has('shipping_address')) {
            $this->merge([
                'shipping_address' => $this->cleanAddressData($this->input('shipping_address'))
            ]);
        }

        if ($this->has('billing_address')) {
            $this->merge([
                'billing_address' => $this->cleanAddressData($this->input('billing_address'))
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateUserHasActiveCart($validator);
        });
    }

    /**
     * Validate that user has an active cart with items.
     */
    protected function validateUserHasActiveCart($validator): void
    {
        $cart = \App\Models\Cart::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with('items')
            ->first();

        if (!$cart) {
            $validator->errors()->add('cart', 'No tiene un carrito activo para crear el pedido.');
            return;
        }

        if ($cart->isEmpty()) {
            $validator->errors()->add('cart', 'Su carrito está vacío. Agregue productos antes de crear el pedido.');
        }
    }

    /**
     * Clean address data removing extra spaces.
     */
    protected function cleanAddressData(array $address): array
    {
        return array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $address);
    }
}
