<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Search extends Controller
{
  public static function getScreen(){
    // return view('layouts.search');
    return view('layouts.search_1');
  }
}
