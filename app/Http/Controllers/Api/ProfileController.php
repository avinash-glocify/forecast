<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = Auth::user();
        $user->profile = $user->profile;
        return response()->json([
            'success' => true,
            'data'    => $user,
            'message' => 'Success'
        ]);
    }

    public function update(Request $request)
    {
        $user       = Auth::user();
        $rules      = ['email' => 'required|email|unique:users,email,'.$user->id];
        $validator  = Validator::make($request->all(), $rules);

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

        $user->update(['email' => $request->email]);
        $profileData = $request->only('dob','first_name','last_name','city','state','phone_number', 'address', 'country','zip_code','budget');

        $user->profile()->update(array_filter($profileData));
        $user->profile = $user->profile;
        return response()->json([
            'success' => true,
            'data'    => $user,
            'message' => 'Your account has been updated!'
        ]);
    }
}
