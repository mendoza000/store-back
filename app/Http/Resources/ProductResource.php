<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'cost_price' => $this->cost_price,
            'track_quantity' => $this->track_quantity,
            'sku' => $this->sku,
            'status' => $this->status,
            'category' => $this->category,
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => $image->image_path,
                    'sort_order' => $image->sort_order,
                    'url' => $image->url,
                    'is_primary' => $image->is_primary,
                    'is_active' => $image->is_active,
                    'alt_text' => $image->alt_text,
                    'title' => $image->title,            
                ];
            }),
        ];
    }
}
