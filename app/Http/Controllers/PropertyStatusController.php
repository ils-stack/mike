<?php

namespace App\Http\Controllers;

use App\Models\PropertyStatus;
use Illuminate\Http\Request;

class PropertyStatusController extends Controller
{
    public function index()
    {
        if (!request()->ajax()) {
            return view('property_status.index');
        }

        return PropertyStatus::orderBy('status')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'status'        => 'required|string|max:100|unique:property_status,status',
            'marker_letter' => 'required|string|size:2',
            'marker_color'  => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        PropertyStatus::create([
            'status'        => $request->status,
            'marker_letter' => strtoupper($request->marker_letter),
            'marker_color'  => $request->marker_color,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $status = PropertyStatus::findOrFail($id);

        $request->validate([
            'status'        => 'required|string|max:100|unique:property_status,status,' . $status->id,
            'marker_letter' => 'required|string|size:2',
            'marker_color'  => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $status->update($request->only([
            'status',
            'marker_letter',
            'marker_color',
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        PropertyStatus::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
