<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'data'    => $user->with('profile')->get(),
            'message' => 'Success'
        ]);
    }
}
