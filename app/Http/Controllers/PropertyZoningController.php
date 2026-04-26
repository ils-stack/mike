<?php

namespace App\Http\Controllers;

use App\Models\PropertyZoning;
use Illuminate\Http\Request;

class PropertyZoningController extends Controller
{
    public function index()
    {
        // Page load
        if (!request()->ajax()) {
            return view('property_zoning.index');
        }

        // AJAX list
        return PropertyZoning::orderBy('zoning')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'zoning' => 'required|string|max:100|unique:property_zoning,zoning',
        ]);

        PropertyZoning::create([
            'zoning' => $request->zoning,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $zoning = PropertyZoning::findOrFail($id);

        $request->validate([
            'zoning' => 'required|string|max:100|unique:property_zoning,zoning,' . $zoning->id,
        ]);

        $zoning->update([
            'zoning' => $request->zoning,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        PropertyZoning::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
