<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Properties;

class PropertyNotesController extends Controller
{
    public function load($id)
    {
        $property = Properties::find($id);

        return response()->json([
            'success' => true,
            'note' => $property->notes ?? ''
        ]);
    }

    public function save(Request $request, $id)
    {
        $property = Properties::find($id);
        $property->notes = $request->note;
        $property->save();

        return response()->json(['success' => true]);
    }
}
