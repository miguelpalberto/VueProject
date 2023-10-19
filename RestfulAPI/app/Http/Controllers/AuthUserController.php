<?php

namespace App\Http\Controllers;

use App\Models\AuthUser;
use Illuminate\Http\Request;

class AuthUserController extends Controller
{
    public function index()
    {
        $authUsers = AuthUser::all();
        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved authorized users',
            'data' => $authUsers
        ], 200);
    }
}
