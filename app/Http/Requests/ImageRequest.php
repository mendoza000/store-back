<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'image_path' => 'required|string|max:255',
            'sort_order' => 'integer|min:0',
            'url' => 'nullable|url|max:255',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
            'alt_text' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
        ];
    }
}
