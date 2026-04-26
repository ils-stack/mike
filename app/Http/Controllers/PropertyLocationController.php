<?php

namespace App\Http\Controllers;

use App\Models\PropertyLocation;
use Illuminate\Http\Request;

class PropertyLocationController extends Controller
{
    public function index()
    {
        // Page load
        if (!request()->ajax()) {
            return view('property_location.index');
        }

        // AJAX list
        return PropertyLocation::orderBy('location')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string|max:100|unique:property_location,location',
        ]);

        PropertyLocation::create([
            'location' => $request->location,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $location = PropertyLocation::findOrFail($id);

        $request->validate([
            'location' => 'required|string|max:100|unique:property_location,location,' . $location->id,
        ]);

        $location->update([
            'location' => $request->location,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        PropertyLocation::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
