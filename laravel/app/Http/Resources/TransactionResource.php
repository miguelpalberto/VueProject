<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'payment_reference' => $this->payment_reference, 
            'type' => $this->type == 'D' ? 'Debit' : 'Credit',
            'datetime' => $this->datetime,
            'value' => $this->type == 'C' ? '+' . $this->value . ' €' : '-' . $this->value . ' €',
            'numericValue' => $this->type == 'C' ? $this->value : -$this->value,
            'new_balance' => $this->new_balance,
            'payment_type' => $this->payment_type,
            'category_id' => $this->category ? $this->category->name : '',
        ];
    }
}
