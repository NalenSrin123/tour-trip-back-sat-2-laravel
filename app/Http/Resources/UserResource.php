<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id ?? $this->user_id,
            'user_id'         => $this->user_id ?? $this->id,
            'name'            => $this->name ?? $this->full_name,
            'full_name'       => $this->full_name ?? $this->name,
            'email'           => $this->email,
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
