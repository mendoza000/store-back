<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariantRequest extends FormRequest
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
            
            'product_id' => 'required|exists:products,id',
            'variant_name' => 'required|string|max:255',
            'variant_value' => 'required|string|max:255',
            'price' => 'required|numeric',
            'compare_price' => 'nullable|numeric',
            'sku' => 'required|string|max:255|unique:product_variants,sku',
            'status' => 'required|in:active,inactive,out_of_stock',
            'stock' => 'required|integer',

        ];
    }
}
