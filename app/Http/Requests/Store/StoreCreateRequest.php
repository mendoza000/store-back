<?php

namespace App\Http\Requests\Store;

use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;

class StoreCreateRequest extends FormRequest
{
    use HandlesValidationErrors;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|unique:store,name',
            'config' => 'array',
            'config.products' => 'boolean',
            'config.categories' => 'boolean',
            'config.cupons' => 'boolean',
            'config.gifcards' => 'boolean',
            'config.wishlist' => 'boolean',
            'config.reviews' => 'boolean',
            'config.notifications.emails' => 'boolean',
            'config.notifications.telegram' => 'boolean',
        ];
    }
}
