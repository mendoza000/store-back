<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [

            "id" => $this->id,
            "product" => $this->product->name,
            "variant_name" => $this->variant_name,
            "variant_value" => $this->variant_value,
            "price" => $this->price,
            "compare_price" => $this->compare_price,
            "sku" => $this->sku,
            "status" => $this->status,
            "stock" => $this->stock,

        ];

    }
}
