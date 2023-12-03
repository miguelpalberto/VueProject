<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $passportData = [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PASSWORD_GRANT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_GRANT_SECRET'),
            'username' => $request->username,
            'password' => $request->password,
            'scope'         => '',
        ];

        request()->request->add($passportData);

        $innerRequest = Request::create(env('PASSPORT_URL') . '/oauth/token', 'POST');
        $response = Route::dispatch($innerRequest);
        $statusCode = $response->getStatusCode();

        if ($statusCode == '200') {
            $user = AuthUser::where('username', $request->username)->first();
            if ($user && $user->blocked) {
                return response()->json(['error' => 'The user has been blocked. Please contact the administrators.'], 403);
            }

            return json_decode((string) $response->content(), true);
        } else {
            return response()->json(
                ['error' => 'User credentials are invalid'],
                $statusCode
            );
        }
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();
        $token->delete();
        return response(['msg' => 'Token revoked'], 200);
    }
}
