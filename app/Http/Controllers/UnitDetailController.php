<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitDetail;
use App\Models\Properties;
use App\Models\UnitProperty;
use App\Models\PropertyStatus;
use App\Models\Asset;
use App\Models\AssetAssignment;

use Illuminate\Support\Facades\Storage;

class UnitDetailController extends Controller
{
    /**
     * Show Units list screen
     */
     public function getScreen()
     {
         $units = UnitDetail::orderBy('id', 'desc')->get();
         $properties = Properties::orderBy('building_name')->get();
         $propertyStatus = PropertyStatus::orderBy('status')->get();

         return view('layouts.unit_details', compact(
             'units',
             'properties',
             'propertyStatus'
         ));
     }

    /**
     * Save or update a Unit (AJAX)
     */
     public function ajaxSaveUnit(Request $request)
   {
       $validated = $request->validate([
           'id'                        => 'nullable|integer|exists:unit_details,id',
           'unit_type'                 => 'required|string|max:100',
           'company_as_listing_broker' => 'boolean',
           'listing_broker'            => 'required|string|max:100',
           'deal_file_id'              => 'nullable|string|max:100',
           'availability'              => 'nullable|string|max:100',

           // ✅ NEW
           'unit_status'               => 'required|integer|exists:property_status,id',

           'lease_expiry'              => 'nullable|date',
           'unit_no'                   => 'nullable|string|max:50',
           'unit_size'                 => 'nullable|numeric',
           'gross_rental'              => 'nullable|numeric',
           'sale_price'                => 'nullable|numeric',
           'yield_percentage'          => 'nullable|string|max:150',
           'parking_bays'              => 'nullable|string|max:75',
           'parking_rental'            => 'nullable|string|max:150',

           'property_ids'              => 'array',
           'property_ids.*'            => 'integer|exists:properties,id',
       ]);

       /* 🔑 Separate unit fields from pivot data */
       $unitData = collect($validated)->except('property_ids')->toArray();

       // Save or update the unit
       $unit = UnitDetail::updateOrCreate(
           ['id' => $validated['id'] ?? null],
           $unitData
       );

       // Maintain relations (pivot)
       UnitProperty::where('unit_id', $unit->id)->delete();

       if (!empty($validated['property_ids'])) {
           $relations = collect($validated['property_ids'])->map(fn ($pid) => [
               'unit_id'     => $unit->id,
               'property_id' => $pid,
               'created_at'  => now(),
               'updated_at'  => now(),
           ])->toArray();

           UnitProperty::insert($relations);
       }

       return response()->json([
           'success'      => true,
           'unit'         => $unit,
           'property_ids' => $validated['property_ids'] ?? [],
           'message'      => 'Unit saved successfully',
       ]);
   }


    /**
     * Delete a Unit
     */
    public function ajaxDeleteUnit($id)
    {
        $unit = UnitDetail::find($id);
        if (!$unit) {
            return response()->json(['success' => false, 'message' => 'Unit not found'], 404);
        }

        UnitProperty::where('unit_id', $id)->delete();
        $unit->delete();

        return response()->json(['success' => true, 'message' => 'Unit deleted successfully']);
    }

    /**
     * Get a single Unit (AJAX)
     */
    public function ajaxGetUnit($id)
    {
        $unit = UnitDetail::find($id);
        if (!$unit) {
            return response()->json(['success' => false, 'message' => 'Unit not found'], 404);
        }

        $propertyIds = UnitProperty::where('unit_id', $id)->pluck('property_id')->toArray();

        return response()->json([
            'unit' => $unit,
            'property_ids' => $propertyIds,
        ]);
    }

    /**
     * Assign properties to Unit
     */
    public function ajaxAssignProperties(Request $request, $id)
    {
        $unit = UnitDetail::findOrFail($id);
        $propertyIds = $request->input('properties', []);

        UnitProperty::where('unit_id', $id)->delete();

        $relations = collect($propertyIds)->map(fn($pid) => [
            'unit_id' => $id,
            'property_id' => $pid,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        UnitProperty::insert($relations);

        return response()->json([
            'success' => true,
            'message' => 'Properties assigned successfully',
            'unit_id' => $id,
            'property_ids' => $propertyIds,
        ]);
    }

    public function unitAssets($unitId)
    {
        $assets = Asset::where('user_id', auth()->id())
            ->where('folder', 'images')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($asset) use ($unitId) {

                // build public URL
                $asset->public_url = Storage::disk('public')->url($asset->file_path);

                // assigned flag for unit
                $asset->assigned = AssetAssignment::where([
                    'asset_id'    => $asset->id,
                    'module_type' => 'unit',
                    'module_id'   => $unitId,
                ])->exists();

                return $asset;
            });

        return response()->json($assets);
    }

    public function toggleUnitAsset(Request $r)
    {
        $r->validate([
            'asset_id' => 'required|integer',
            'unit_id'  => 'required|integer',
        ]);

        $assignment = AssetAssignment::where([
            'asset_id'    => $r->asset_id,
            'module_type' => 'unit',
            'module_id'   => $r->unit_id,
        ])->first();

        if ($assignment) {
            $assignment->delete();
            $assigned = false;
        } else {
            AssetAssignment::create([
                'asset_id'    => $r->asset_id,
                'module_type' => 'unit',
                'module_id'   => $r->unit_id,
            ]);
            $assigned = true;
        }

        return response()->json([
            'success'  => true,
            'assigned' => $assigned,
        ]);
    }

}
