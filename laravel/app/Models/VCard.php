<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vcards';
    protected $primaryKey = 'phone_number';
    protected $keyType = 'string';

    protected $fillable = [
        'phone_number',
        'name',
        'email',
        'photo_url',
        'confirmation_code',
        'blocked',
        'balance',
        'max_debit',
        'custom_options',
        'custom_data'
    ];

    protected $casts = [
        'custom_options' => 'json',
        'custom_data' => 'json',
        'password' => 'hashed',
        'confirmation_code' => 'hashed',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'vcard', 'phone_number');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'vcard', 'phone_number');
    }

    public function pairTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'pair_vcard', 'phone_number');
    }
}
