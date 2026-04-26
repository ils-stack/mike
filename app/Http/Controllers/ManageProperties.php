<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManageProperties extends Controller
{
  public static function getScreen(){
    return view('layouts.add_prop');
  }
}
