<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'username' => $this->username,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'role' => $this->whenLoaded('teacher', 'teacher', $this->whenLoaded('student', 'student', 'super_admin')),
            'name' => $this->whenLoaded('teacher', fn () => $this->teacher?->name, $this->whenLoaded('student', fn () => $this->student?->name, $this->username)),
            'created_at' => $this->created_at,
        ];
    }
}
