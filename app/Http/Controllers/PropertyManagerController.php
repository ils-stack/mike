<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyManager;
use App\Models\Properties;

class PropertyManagerController extends Controller
{
    /**
     * Show Property Managers list screen
     */
    public function getScreen()
    {
        $managers = PropertyManager::orderBy('id', 'desc')->get();
        $properties = Properties::orderBy('building_name')->get();

        return view('layouts.property_managers', compact('managers', 'properties'));
    }

    /**
     * Save or update a Property Manager (AJAX)
     */
    public function ajaxSaveManager(Request $request)
    {
        $validated = $request->validate([
            'id'             => 'nullable|integer|exists:property_managers,id',
            'company_name'   => 'required|string|max:255',
            'entity_name'    => 'nullable|string|max:255',
            'manager_name'   => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'telephone'      => 'nullable|string|max:50',
            'cell_number'    => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'property_ids'   => 'array',
            'property_ids.*' => 'integer|exists:properties,id',
        ]);

        // 🔹 Save manager main data
        $manager = PropertyManager::updateOrCreate(
            ['id' => $validated['id'] ?? null],
            $validated
        );

        // 🔹 Save assigned properties in pivot table
        $propertyIds = $request->input('property_ids', []);
        $manager->properties()->sync($propertyIds);

        return response()->json([
            'success' => true,
            'manager' => $manager
        ]);
    }

    /**
     * Delete a Property Manager
     */
    public function ajaxDeleteManager($id)
    {
        $manager = PropertyManager::find($id);

        if (!$manager) {
            return response()->json(['success' => false, 'message' => 'Manager not found'], 404);
        }

        // Clean pivot as well
        $manager->properties()->detach();

        $manager->delete();

        return response()->json(['success' => true, 'message' => 'Manager deleted successfully']);
    }

    /**
     * Get a single Property Manager (AJAX)
     */
    public function ajaxGetManager($id)
    {
        $manager = PropertyManager::find($id);

        if (!$manager) {
            return response()->json([
                'success' => false,
                'message' => 'Manager not found'
            ], 404);
        }

        // 🔹 Attach property IDs for edit modal
        $manager->property_ids = $manager->properties()->pluck('property_id')->toArray();

        return response()->json($manager);
    }
}
