<?php

namespace App\Http\Controllers;

use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    public function index()
    {
        // return PropertyType::orderBy('type')->get();
        if (request()->ajax()) {
      return PropertyType::orderBy('type')->get();
  }

  return view('property_types.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:100|unique:property_types,type',
        ]);

        return PropertyType::create([
            'type' => $request->type,
        ]);
    }

    public function update(Request $request, $id)
    {
        $propertyType = PropertyType::findOrFail($id);

        $request->validate([
            'type' => 'required|string|max:100|unique:property_types,type,' . $propertyType->id,
        ]);

        $propertyType->update([
            'type' => $request->type,
        ]);

        return $propertyType;
    }

    public function destroy($id)
    {
        PropertyType::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
