<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Only include config if requested
        if ($request->has('include') && in_array('config', explode(',', $request->input('include')))) {
            $data['config'] = new StoreConfigResource($this->config);
        }

        return $data;
    }
}

