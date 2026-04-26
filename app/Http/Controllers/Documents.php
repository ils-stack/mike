<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Properties as Property;

class Documents extends Controller
{
  public static function getScreen(){
    // return view('asset.list');

    $propertyId = 1;

    $property = Property::findOrFail($propertyId);
    return view('asset.list', compact('property'));
  }
}
