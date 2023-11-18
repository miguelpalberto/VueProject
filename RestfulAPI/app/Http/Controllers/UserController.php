<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    // funcao ze update para admins

    public function update(Request $request, User $user)
    {
        // Ve se exites the user
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Validar os dados 
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'string|min:8|confirmed',
            'custom_options' => 'nullable|array',
            'custom_data' => 'nullable|string'
        ]);

        // Apenas o administrador pode atualizar informações do usuário
        
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('custom_options')) {
            $user->custom_options = $request->custom_options;
        }

        if ($request->has('custom_data')) {
            $user->custom_data = $request->custom_data;
        }

        // guarda as cenas
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User information updated successfully',
            'data' => $user
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
            $newUser->custom_options = $validRequest['custom_options'] ?? null;
            $newUser->custom_data = $validRequest['custom_data'] ?? null;

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
