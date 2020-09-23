<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Auth, Hash;

use Carbon\Carbon;
use App\User;

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

    public function register(Request $request){
      $validator = Validator::make($request->all(), [
                    'email'        =>'required|unique:users',
                    'first_name'   =>'required',
                    'last_name'    =>'required',
                    'phone_number' =>'required',
                    'password'     =>'required',
                  ],
                  ['email.unique'=>'Email Address already registered please sign in using credentials or click forgot password to reset.']
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

      $user = User::create([
              'first_name'    => strtolower($request->first_name),
              'last_name'     => strtolower($request->last_name),
              'phone_number'  => strtolower($request->phone_number),
              'email'         => strtolower($request->email),
              'password'      => Hash::make($request->password),
      ]);

      return response ([
          'success'   => true,
          'message'   => 'Account Created',
          'user_data' => $user,
        ],200)->header('Content-Type', 'application/json');
    }
}
