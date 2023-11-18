<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
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
            'name' => $this->name,
            'isAdmin' => $this->user_type == 'A' ? true : false, // 'A' is for 'Admin' and 'V' is for a vCard user
            'username' => $this->username,
            'email' => $this->email,
            'blocked' => $this->blocked,
            'photo_url' => $this->photo_url,
        ];
    }
}
