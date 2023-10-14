<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VCard extends Model
{
    use HasFactory, SoftDeletes;

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
    ];
}
