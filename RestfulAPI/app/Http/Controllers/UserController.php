<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved users',
            'data' => $users
        ], 200);
    }

    //todo: authorization admin
    public function store(UserRequest $request)
    {
        $validRequest = $request->validated();

        $user = DB::transaction(function () use ($validRequest, $request) {
            $newUser = new User();
            $newUser->name = $validRequest['name'];
            $newUser->email = $validRequest['email'];
            $newUser->password = $validRequest['password'];
            $newUser->customOptions = $validRequest['custom_options'] ?? null;
            $newUser->customData = $validRequest['custom_data'] ?? null;

            $newUser->save();
            return $newUser; //?

        });

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be created'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully created user',
            'data' => $user
        ], 201);
    }

    //todo: authorization admin
    public function delete(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted user'
        ], 200);
    }
}
