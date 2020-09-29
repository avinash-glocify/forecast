<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;

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
            'success'      => true,
            'message'      => 'Successfully Logged In',
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ],200)->header('Content-Type', 'application/json');
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data'    => $user->with('profile')->get(),
            'message' => 'Success'
        ]);
    }

    public function register(Request $request)
    {
      $validator = Validator::make($request->all(), [
                    'email'        =>'required|unique:users',
                    'password'     => 'required',
                    'country'      =>'required',
                    'zip_code'     =>'required',
                    'phone_number' =>'required',
                    'budget'       =>'required',
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
              'phone_number'  => strtolower($request->phone_number),
              'email'         => strtolower($request->email),
              'password'      => Hash::make($request->password),
      ]);

      $user->profile()->create([
          'phone_number' => $request->phone_number,
          'country'      => $request->country,
          'zip_code'     => $request->zip_code,
          'budget'       => $request->budget,
      ]);

      $userDetail        = $user->profile;
      $userDetail->email = $user->email;
      return response ([
          'success'   => true,
          'message'   => 'Account Created',
          'user_data' => $userDetail,
        ],200)->header('Content-Type', 'application/json');
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    public function forgotPassword(Request $request)
    {
      $input      = $request->all();
      $rules      = ['email' => "required|email"];
      $validator  = Validator::make($input, $rules);
      $arr        = [];

      if ($validator->fails()) {
          $arr = ["status" => 400,'success' => true, "message" => $validator->errors()->first(), "data" => array()];
      } else {
          try {
              $response = Password::sendResetLink($request->only('email'), function (Message $message) {
                  $message->subject($this->getEmailSubject());
              });
              switch ($response) {
                  case Password::RESET_LINK_SENT:
                      return \Response::json(array("status" => 200,'success' => true, "message" => trans($response), "data" => array()));
                  case Password::INVALID_USER:
                      return \Response::json(array("status" => 400,'success' => true, "message" => trans($response), "data" => array()));
              }
          } catch (\Swift_TransportException $ex) {
            $arr = [
              'status' => 400,
              'success' => true,
              "message" => $ex->getMessage()
            ];
          } catch (Exception $ex) {
            $arr = [
              'status' => 400,
              'success' => true,
              "message" => $ex->getMessage()
            ];

          }
      }
      return \Response::json($arr);
      }

      public function reset() {
        $credentials = request()->validate([
            'email'    => 'required|email',
            'token'    => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(['success' => true,"message" => "Invalid token provided"], 400);
        }

        return response()->json(['success' => true, "message" => "Password has been successfully changed"]);
    }

    public function changePassword(Request $request)
    {
      $validator = Validator::make($request->all(), [
                    'current_password' => 'required',
                    'new_password'     => 'required',
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

      $user             = Auth::User();
      $current_password = $user->password;
      if(Hash::check($request['current_password'], $current_password)){
        $user->password = Hash::make($request['new_password']);
        $user->save();
        return response([
            'success' => true,
            'message' => 'Password change Successfully',
          ], 200)->header('Content-Type', 'application/json');
      }
      else{
        return response([
            'success' => false,
            'message' => 'Current Password not matched',
          ], 200)->header('Content-Type', 'application/json');
      }
    }
}
