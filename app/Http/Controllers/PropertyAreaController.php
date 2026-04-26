<?php

namespace App\Http\Controllers;

use App\Models\PropertyArea;
use Illuminate\Http\Request;

class PropertyAreaController extends Controller
{
    public function index()
    {
        // Page load
        if (!request()->ajax()) {
            return view('property_area.index');
        }

        // AJAX list
        return PropertyArea::orderBy('area')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'area' => 'required|string|max:100|unique:property_area,area',
        ]);

        PropertyArea::create([
            'area' => $request->area,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $area = PropertyArea::findOrFail($id);

        $request->validate([
            'area' => 'required|string|max:100|unique:property_area,area,' . $area->id,
        ]);

        $area->update([
            'area' => $request->area,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        PropertyArea::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
