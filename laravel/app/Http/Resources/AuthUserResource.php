<?php

namespace App\Http\Resources;

use App\Models\VCard;
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
        $vCard = null;
        if ($this->userType != 'A'){
            $vCard = VCard::find($this->username);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'isAdmin' => $this->user_type == 'A' ? true : false, // 'A' is for 'Admin' and 'V' is for a vCard user
            'username' => $this->username,
            'balance' => $vCard ? $vCard->balance : null,
            'max_debit' => $vCard ? $vCard->max_debit : null,
            'email' => $this->email,
            'blocked' => $this->blocked,
            'photo_url' => $this->photo_url,
        ];
    }
}
