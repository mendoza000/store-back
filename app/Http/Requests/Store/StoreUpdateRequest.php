<?php

namespace App\Http\Requests\Store;

use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
{
    use HandlesValidationErrors;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');
        return [
            'name' => 'sometimes|string|unique:store,name,' . $id . ',id',
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
