<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NotificationResource
 *
 * Transforms Notification model into standard API JSON format.
 */
class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->type,
            'message'    => $this->message,
            'is_read'    => $this->read_at !== null,
            'read_at'    => $this->read_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
