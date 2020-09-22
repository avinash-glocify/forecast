<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\FriendRequest;

class UserController extends Controller
{
    public function getAllUser()
    {
        $users = User::with('sender')->where('id', '!=', auth()->id())->get();
        return response()->json($users);
    }

    public function getUser($id)
    {
        $user = User::findOrfail($id);
        return response()->json($user);
    }

    public function addFriend($id)
    {
        $user     = User::findOrfail($id);
        $reciever =  FriendRequest::where(['sender_id' => auth()->id(), 'reciever_id' => $user->id])->first();

        if($user && !$reciever) {
          $msg = ['Request Sent Successfully'];
          FriendRequest::create(['sender_id' => auth()->id(), 'reciever_id' => $user->id]);
        } else {
          $msg = ['Request already Sent'];
        }
        return response()->json(['data' => $msg]);
    }
}
