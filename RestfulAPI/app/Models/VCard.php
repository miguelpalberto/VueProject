<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VCard extends Model
{
    use HasFactory;

    protected $primaryKey = 'phone_number';
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
}