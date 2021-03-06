<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Category extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $array = [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'key' => $this->id,
            'value' => $this->id,
            'name' => $this->name,
            'title' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'active' => $this->active,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'creator' => $this->creator,
            'updater' => $this->updater,
            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'attributes' => $this->attributes,
        ];

        if ($this->children->count()) {
            $array['children'] = new CategoryCollection($this->children);
        }

        return $array;
    }
}
