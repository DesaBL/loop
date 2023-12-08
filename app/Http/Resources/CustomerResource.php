<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'job_title' => $this->job_title,
            'email' => $this->email,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'registered_at' => $this->registered_at,
            'created_at' => $this->created_at,
        ];
    }
}
