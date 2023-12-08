<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'status_label' => $this->status_label,
            'payment_issued_at' => $this->payment_issued_at,
            'price' => $this->price,
            'products' => ProductResource::collection($this->products),
            'customer' => CustomerResource::make($this->customer),
            'created_at' => $this->created_at,
        ];
    }
}
