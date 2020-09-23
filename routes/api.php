<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::Post('login', 'Api\AuthController@login');
Route::Post('register', 'Api\AuthController@register');

// Route::group(['middleware' => 'auth:api'], function() {
//     Route::get('logout', 'Api\AuthController@logout');
//     Route::get('users', 'Api\UserController@getAllUser');
//     Route::get('users/{id}', 'Api\UserController@getUser');
//     Route::get('posts', 'Api\Postcontroller@getPosts');
//     Route::get('posts/{id}', 'Api\Postcontroller@getSinglePost');
//     Route::post('add/friend/{id}', 'Api\UserController@addFriend');
// });
