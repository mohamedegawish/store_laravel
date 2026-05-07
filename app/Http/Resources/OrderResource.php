<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'total_amount' => (float) $this->total_amount,
            'shipping_address' => $this->shipping_address,
            'notes' => $this->notes,
            'user' => new UserResource($this->whenLoaded('user')),
            'company' => [
                'id' => $this->company_id,
                'name' => $this->whenLoaded('company', fn () => $this->company->company_name),
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
