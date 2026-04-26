<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Vacancy extends Controller
{
  public static function getScreen(){
    return view('layouts.vacancy');
  }
}
