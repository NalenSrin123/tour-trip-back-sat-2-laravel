<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'booking_id'   => $this->booking_id,
            'booking_code' => $this->booking_code,
            'user_id'      => $this->user_id,
            'user'         => new UserResource($this->whenLoaded('user')),
            'schedule_id'  => $this->schedule_id,
            'status'       => $this->status,
            'total_amount' => (float) $this->total_amount,
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}