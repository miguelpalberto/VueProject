<?php

namespace App\Http\Controllers;

use App\Models\AuthUser;

class AuthUserController extends Controller
{
    public function index()
    {
        return AuthUser::all();
    }
}
