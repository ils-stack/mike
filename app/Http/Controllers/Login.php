<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Login extends Controller
{
  public static function getScreen(){
    return view('layouts.login');
  }
}
