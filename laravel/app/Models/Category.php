<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    protected $fillable = [
        'vcard',
        'type',
        'name',
        'custom_options',
        'custom_data'
    ];

    protected $casts = [
        'vcard' => 'string',
        'custom_options' => 'json',
        'custom_data' => 'json'
    ];

    public function vCard() : BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard', 'phone_number');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id', 'id');
    }
}
