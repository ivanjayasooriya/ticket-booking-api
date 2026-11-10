<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'date' => $this->date,
            'total_tickets' => $this->total_tickets,
            'ticket_price' => $this->ticket_price,
            'organizer' => new UserResource($this->whenLoaded('organizer')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
