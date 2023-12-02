<?php

namespace App\Http\Controllers;

use App\Models\AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AuthUserResource;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use App\Models\VCard;

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

    public function changePassword(ChangePasswordRequest $request){
        $validRequest = $request->validated();
        $user = $request->user();

        if (!Hash::check($validRequest['current_password'], $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => [
                        'The current password is incorrect'
                    ]
                ]
            ], 422);
        }

        $actualUser = null;

        if ($user->user_type == 'A') {
            $actualUser = User::find($user->id);
        } else {
            $actualUser = VCard::find($user->username);
        }

        $actualUser->password = bcrypt($validRequest['new_password']);
        $actualUser->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully changed password'
        ], 200);
    }
}
