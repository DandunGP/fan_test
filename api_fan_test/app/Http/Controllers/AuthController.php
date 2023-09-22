<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request, JwtAuth $jwtAuth){
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if(!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'unregistered email',
            ], 404);
        }

        if ($user) {
            if (Hash::check($credentials['password'], $user->password)) {
                $token = $jwtAuth->createJwtToken($user);

                $user->token = $token;

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'result' => $user,
                ]);
            }

            return response()->json([
                'code' => 401,
                'status' => 'failed',
                'result' => 'Password incorrect',
            ], 401);
        }

        return response()->json([
            'code' => 401,
            'status' => 'error',
            'message' => 'Unauthorized',
        ], 401);
    }
}
