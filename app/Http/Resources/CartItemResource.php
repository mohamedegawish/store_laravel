<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'variant' => new ProductVariantResource($this->whenLoaded('productVariant')),
            // We can resolve product via variant in actual use to keep it simple, or eager load product via variant.
            'product' => $this->whenLoaded('productVariant', fn () => new ProductResource($this->productVariant->product)),
        ];
    }
}
