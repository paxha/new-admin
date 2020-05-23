<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Brand extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'iso2' => $this->iso2,
            'logo' => $this->logo,
            'cover' => $this->cover,
            'popular' => $this->popular,
            'active' => $this->active,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'creator' => $this->creator,
            'updater' => $this->updater,
            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
        ];
    }
}
