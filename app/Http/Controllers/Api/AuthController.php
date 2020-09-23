<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
      $validator = Validator::make($request->all(), [
                    'email'       =>'required|email',
                    'password'  =>'required'
                  ],
                );

      if ($validator->fails()) {
          $errors = array();
          foreach ($validator->messages()->all() as $message){
            array_push($errors,$message);
          }
          return response([
              'success' => false,
              'errors' => $errors
            ], 200)->header('Content-Type', 'application/json');
      }

        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials)) {
          return response()->json([
            'message' => 'Unauthorized',
          ], 401)->header('Content-Type', 'application/json');
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
        ],200)->header('Content-Type', 'application/json');
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
