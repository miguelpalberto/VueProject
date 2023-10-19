<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'VIEW_AUTH_USERS';

    protected $fillable = [
        //'id',
        'user_type',
        'username',
        'password',
        'confirmation_code',
        'name',
        'email',
        'blocked',
        'photo_url',
    ];

    protected $dates = ['deleted_at'];

}
