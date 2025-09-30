<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MethodPaymentResource extends JsonResource
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
            'type' => $this->type,
            'account_info' => $this->account_info,
            'instructions' => $this->instructions,
            'status' => $this->status,
            'store' => $this->store_id,
        ];
    }
}
