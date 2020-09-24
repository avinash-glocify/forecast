<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function privacy()
    {
        return view('home');
    }

    public function terms()
    {
        return view('home');
    }

    public function success()
    {
        return view('password-set');
    }
}
