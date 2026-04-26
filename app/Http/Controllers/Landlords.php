<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Landlord;

class Landlords extends Controller
{
    /**
     * Landlord list screen
     */
    public function getScreen()
    {

        // $landlords = Landlord::orderBy('id', 'desc')->get();
        // return view('layouts.landlords', compact('landlords'));

        $landlords = Landlord::orderBy('id', 'desc')->get();
        $properties = \App\Models\Properties::orderBy('building_name')->get(); // 👈 fetch properties too

        return view('layouts.landlords', compact('landlords', 'properties'));
    }

    /**
     * Save or update a landlord (AJAX)
     */
    public function ajaxSaveLandlord(Request $request)
    {
        $validated = $request->validate([
            'id'            => 'nullable|integer|exists:landlords,id',
            'company_name'  => 'required|string|max:255',
            'entity_name'   => 'nullable|string|max:255',
            'registration_number'   => 'nullable|string|max:255',
            'contact_person'=> 'nullable|string|max:255',
            'telephone'     => 'nullable|string|max:50',
            'cell_number'   => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',
        ]);

        $landlord = Landlord::updateOrCreate(
            ['id' => $validated['id'] ?? null],
            $validated
        );

        return response()->json([
            'success' => true,
            'landlord' => $landlord
        ]);
    }

    public function ajaxDeleteLandlord($id)
    {
        $landlord = Landlord::find($id);

        if (!$landlord) {
            return response()->json(['success' => false, 'message' => 'Landlord not found'], 404);
        }

        $landlord->delete();

        return response()->json(['success' => true, 'message' => 'Landlord deleted successfully']);
    }

    // public function ajaxAssignProperties(Request $request, $id)
    // {
    //     $landlord = Landlord::findOrFail($id);
    //
    //     // property_ids = [1,2,3]
    //     $propertyIds = $request->input('property_ids', []);
    //
    //     $landlord->properties()->sync($propertyIds);
    //
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Properties assigned successfully',
    //     ]);
    // }

    // public function ajaxAssignProperties(Request $request, $id)
    // {
    //     $landlord = Landlord::findOrFail($id);
    //     $landlord->properties()->sync($request->property_ids ?? []);
    //     return response()->json(['success' => true]);
    // }

    public function ajaxAssignProperties(Request $request, $id)
    {

        $landlord = Landlord::findOrFail($id);

        // Expecting array of property IDs
        $propertyIds = $request->input('properties', []);

        // Sync many-to-many
        $landlord->properties()->sync($propertyIds);

        return response()->json([
            'success' => true,
            'message' => 'Properties assigned successfully',
            'landlord_id' => $id,
            'property_ids' => $propertyIds,
        ]);
    }

    /**
     * Get a single landlord (AJAX)
     */
    // public function ajaxGetLandlord($id)
    // {
    //     $landlord = Landlord::find($id);
    //
    //     if (!$landlord) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Landlord not found'
    //         ], 404);
    //     }
    //
    //     return response()->json($landlord);
    // }

    public function ajaxGetLandlord($id)
{
    // eager load properties so JS can preselect them
    $landlord = Landlord::with('properties')->find($id);

    if (!$landlord) {
        return response()->json([
            'success' => false,
            'message' => 'Landlord not found'
        ], 404);
    }

    return response()->json($landlord);
}

}
