<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenants;
use App\Models\Properties;
use App\Models\TenantProperty;

class TenantController extends Controller
{
    /**
     * Show Tenants list screen
     */
    public function getScreen()
    {
        $tenants = Tenants::orderBy('id', 'desc')->get();
        $properties = Properties::orderBy('building_name')->get();

        return view('layouts.tenants', compact('tenants', 'properties'));
    }

    /**
     * Save or update a Tenant (AJAX)
     */
    public function ajaxSaveTenant(Request $request)
    {
        $validated = $request->validate([
            'id'             => 'nullable|integer|exists:tenants,id',
            'company_name'   => 'nullable|string|max:255',
            'entity_name'    => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'telephone'      => 'nullable|string|max:50',
            'cell_number'    => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
        ]);

        $tenant = Tenants::updateOrCreate(
            ['id' => $validated['id'] ?? null],
            $validated
        );

        return response()->json([
            'success' => true,
            'tenant' => $tenant,
            'message' => 'Tenant saved successfully',
        ]);
    }

    /**
     * Delete a Tenant
     */
    public function ajaxDeleteTenant($id)
    {
        $tenant = Tenants::find($id);

        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Tenant not found'], 404);
        }

        $tenant->delete();

        return response()->json(['success' => true, 'message' => 'Tenant deleted successfully']);
    }

    /**
     * Get a single Tenant (AJAX)
     */
    public function ajaxGetTenant($id)
    {
        $tenant = Tenants::find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        return response()->json($tenant);
    }

    /**
     * Assign one or more Properties to a Tenant
     */
    public function ajaxAssignProperties(Request $request, $id)
    {
        $tenant = Tenants::findOrFail($id);
        $propertyIds = $request->input('properties', []);

        // Reset existing associations
        TenantProperty::where('tenant_id', $tenant->id)->delete();

        // Insert new links
        foreach ($propertyIds as $pid) {
            TenantProperty::create([
                'tenant_id' => $tenant->id,
                'property_id' => $pid,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Properties assigned successfully',
            'tenant_id' => $tenant->id,
            'property_ids' => $propertyIds,
        ]);
    }

    public function ajaxGetTenantProperties($id)
    {
        return TenantProperty::where('tenant_id', $id)
                             ->get(['property_id']);
    }

}
