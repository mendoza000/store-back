<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreConfigResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'store_id' => $this->store_id,
            'products' => $this->products,
            'categories' => $this->categories,
            'cupons' => $this->cupons,
            'gifcards' => $this->gifcards,
            'wishlist' => $this->wishlist,
            'reviews' => $this->reviews,
            'notifications' => [
                'emails' => $this->notifications_emails,
                'telegram' => $this->notifications_telegram,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

