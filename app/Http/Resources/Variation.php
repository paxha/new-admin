<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Variation extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'key' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'continue' => $this->continue,
            'active' => $this->active,
            'updated_at' => $this->updated_at->diffForHumans(),
            'updater' => $this->updater,
        ];
    }
}
