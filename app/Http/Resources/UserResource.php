<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * Transforms User model into standard API JSON format.
 */
class UserResource extends JsonResource
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
            'id'            => $this->id,
            'staff_id'      => $this->staff_id,
            'name'          => $this->name,
            'email'         => $this->email,
            'role'          => $this->role,
            'status'        => $this->status,
            'dept_id'       => $this->dept_id,
            'department'    => $this->whenLoaded('department', function () {
                return [
                    'id'   => $this->department?->id,
                    'name' => $this->department?->name,
                    'code' => $this->department?->code,
                ];
            }),
            'avatar_url'    => $this->avatar_path ? asset('storage/' . $this->avatar_path) : null,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
