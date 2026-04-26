<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Deals extends Controller
{
  public static function getScreen(){
    return view('layouts.search');
  }
  public static function getScreenOne(){
    return view('layouts.deals');
  }
}
