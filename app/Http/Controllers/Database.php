<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Database extends Controller
{
  public static function getScreen(){
    // return view('layouts.search');
    return view('layouts.database_1');
  }

  public static function getScreenOne(){
    return view('layouts.database');
  }
}
