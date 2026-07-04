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
            'external_id' => $this->external_id,
            'title' => $this->title,
            'description' => $this->description,
            'brand' => $this->brand,
            'category' => $this->category,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'discount_percentage' => (float) $this->discount_percentage,
            'rating' => (float) $this->rating,
            'stock' => $this->stock,
            'thumbnail' => $this->thumbnail,
            'images' => $this->images ?? [],
            'tags' => $this->tags ?? [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
