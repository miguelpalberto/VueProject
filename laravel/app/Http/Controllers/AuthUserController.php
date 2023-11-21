<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthUserResource;
use App\Models\AuthUser;
use Illuminate\Http\Request;

class AuthUserController extends Controller
{
    public function index()
    {
        return AuthUser::all();
    }
    
    public function me(Request $request)
    {
        return new AuthUserResource($request->user());
    }
}
