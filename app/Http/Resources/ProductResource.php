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
            'description' => $this->description,
            'base_image' => $this->base_image,
            'is_active' => $this->is_active,
            'attributes' => $this->attributes,
            'company' => [
                'id' => $this->company_id,
                'name' => $this->whenLoaded('company', fn () => $this->company->company_name),
            ],
            'category' => [
                'id' => $this->category_id,
                'name' => $this->whenLoaded('category', fn () => $this->category->name),
            ],
            'variants' => ProductVariantResource::collection(
                $this->whenLoaded('variants')
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
