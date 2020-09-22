<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
          'email'      => 'required|string|email',
          'password'   => 'required|string',
        ];

        $request->validate($rules);
        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials)) {
          return response()->json([
            'message' => 'Unauthorized',
          ], 401);
        }

        $user        = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token       = $tokenResult->token;


        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
