<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VCard;
use App\Models\AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AuthUserResource;
use App\Http\Requests\AuthUserUpdateRequest;
use App\Http\Requests\ChangePasswordRequest;

class AuthUserController extends Controller
{
    public function me(Request $request)
    {
        return new AuthUserResource($request->user());
    }

    public function changePassword(ChangePasswordRequest $request)
    {
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

    public function update(AuthUserUpdateRequest $request)
    {
        $validRequest = $request->validated();
        $user = $request->user();
        $actualUser = null;

        if ($user->user_type == 'A') {
            if (User::where('email', $validRequest['email'])->whereNot('id', $user ->id)->exists()) {
                return response()->json([
                    'errors' => [
                        'email' => [
                            'The email has already been taken.'
                        ]
                    ]
                ], 422);
            }
            $actualUser = User::find($user->id);
        } else {
            $actualUser = VCard::find($user->username);
        }

        $actualUser->name = $validRequest['name'];
        $actualUser->email = $validRequest['email'];

        $actualUser->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated vCard',
        ], 200);
    }
}
