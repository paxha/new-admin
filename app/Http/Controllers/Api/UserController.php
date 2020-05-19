<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['These credentials do not match our records.']
            ], 422);
        }

        $bearer = $user->createToken('MyApp')->accessToken;

        $response = [
            'bearer' => $bearer
        ];

        return response($response, 200);
    }

    public function user()
    {
        $response = [
            'user' => auth()->user()
        ];

        return response($response, 200);
    }

    public function logout()
    {
        auth()->logout();

        return response('', 204);
    }
}
