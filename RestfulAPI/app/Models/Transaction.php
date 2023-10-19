<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =  [
        'vcard',
        'type',
        'value',
        'old_balance',
        'new_balance',
        'payment_type',
        'payment_reference',
        'pair_transaction',
        'pair_vcard',
        'category_id',
        'description',
        'custom_options',
        'custom_data'
    ];

    protected $casts = [
        'custom_options' => 'json',
        'custom_data' => 'json'
    ];

    public function vCard() : BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard', 'phone_number');
    }

    public function pairTransaction() : BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'pair_transaction', 'id');
    }

    public function pairVCard() : BelongsTo
    {
        return $this->belongsTo(VCard::class, 'pair_vcard', 'phone_number');
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
