<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class AuthUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'view_auth_users';

    protected $fillable = [
        'user_type',
        'username',
        'name',
        'email',
        'blocked',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'confirmation_code',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    
    public function findForPassport($username)
    {
        return $this->where('username', $username)->where('deleted_at', null)->first();
    }

}
