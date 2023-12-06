<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $queryable = User::query()->orderBy('name', 'asc');

        $filterByNameOrEmail = $request->query('search');

        if ($filterByNameOrEmail) {
            $queryable->where('name', 'like', "%{$filterByNameOrEmail}%")
                ->orWhere('email', 'like', "%{$filterByNameOrEmail}%");
        }

        return $queryable->paginate(10);  
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

        return $user;
    }

    //todo: authorization admin
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted user'
        ], 200);
    }
}
