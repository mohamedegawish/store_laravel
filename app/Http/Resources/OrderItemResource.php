<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product_id,
                'name' => $this->whenLoaded('product', fn () => $this->product->name),
            ],
            'variant' => [
                'id' => $this->product_variant_id,
                'sku' => $this->whenLoaded('productVariant', fn () => $this->productVariant->sku),
            ],
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'total_price' => (float) $this->total_price,
        ];
    }
}
