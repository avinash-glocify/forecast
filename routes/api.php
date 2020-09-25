<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::Post('login', 'Api\AuthController@login');
Route::Post('register', 'Api\AuthController@register');
Route::post('forgot-password', 'Api\AuthController@forgotPassword');
Route::get('privacy', 'HomeController@privacy');
Route::get('terms', 'HomeController@terms');

Route::group(['middleware' => 'auth:api'], function() {
    Route::get('profile', 'Api\ProfileController@profile');
    Route::post('profile-update', 'Api\ProfileController@update');
    Route::post('add_expense ', 'Api\ExpenseController@create');
    Route::get('expenses ', 'Api\ExpenseController@list');
    // Route::get('logout', 'Api\AuthController@logout');
    // Route::get('users', 'Api\UserController@getAllUser');
    // Route::get('users/{id}', 'Api\UserController@getUser');
    // Route::get('posts', 'Api\Postcontroller@getPosts');
    // Route::get('posts/{id}', 'Api\Postcontroller@getSinglePost');
    // Route::post('add/friend/{id}', 'Api\UserController@addFriend');
});
