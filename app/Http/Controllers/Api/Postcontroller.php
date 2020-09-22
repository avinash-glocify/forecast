<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Post;

class Postcontroller extends Controller
{
    public function getPosts()
    {
      $posts = Post::all();
      return response()->json(['data' => $posts]);
    }

    public function getSinglePost($id)
    {
      $post = Post::findOrFail($id);
      return response()->json(['data' => $post]);
    }
}
