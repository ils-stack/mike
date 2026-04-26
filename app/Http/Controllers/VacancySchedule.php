<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VacancySchedule extends Controller
{
  public static function getScreen(){
    return view('layouts.search');
  }
}
