<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'category_id' => $this->category_id,
            'name'        => $this->name,
            'location'    => $this->location,
            'image'       => $this->image,
            'description' => $this->description,
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
            'category'    => new CategoryResource(Category::find($this->category_id)),
        ];
    }
}
