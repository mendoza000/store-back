<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HandlesValidationErrors;
use App\Services\CurrentStore;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductsRequest extends FormRequest
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
        $isCreate = $this->isMethod('post');
        $productId = $this->route('product');
        $storeId = CurrentStore::id();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                $isCreate
                    ? Rule::unique('products', 'slug')
                    : Rule::unique('products', 'slug')->ignore($productId)
            ],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'track_quantity' => ['required', 'integer', 'min:0'],
            'sku' => [
                'required',
                'string',
                'max:100',
                $isCreate
                    ? Rule::unique('products', 'sku')
                    : Rule::unique('products', 'sku')->ignore($productId)
            ],
            'status' => ['required', Rule::in(['active', 'inactive', 'out_of_stock'])],
        ];

        // category_id requerido en creación; opcional en update, pero si viene debe existir en la misma tienda
        $categoryExistsInStore = Rule::exists('categories', 'id')
            ->where(fn($q) => $q->where('store_id', $storeId));

        if ($isCreate) {
            $rules['category_id'] = ['required', 'integer', $categoryExistsInStore];
        } else {
            $rules['category_id'] = ['nullable', 'integer', $categoryExistsInStore];
        }

        return $rules;
    }
}
