<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\Landlord;

use App\Models\Asset;
use App\Models\AssetAssignment;

use App\Models\PropertyType;
use App\Models\PropertyStatus;
use App\Models\PropertyZoning;
use App\Models\PropertyLocation;
use App\Models\PropertyArea;

use Illuminate\Support\Facades\Storage;

class PropList extends Controller
{
    // Property list screen
    public function getScreen()
    {
        // $properties = Properties::with('landlords')->get();
        $properties = Properties::with([
            'landlords',
            'propertyLocation',
            'propertyType',
            'propertyStatus',
            'propertyZoning',
            'propertyArea',
        ])->get();

        $landlords  = Landlord::orderBy('company_name')->get();

        return view('properties.list', [
            // 🔽 CORE
            'properties' => $properties,
            'landlords'  => $landlords,

            // 🔽 MASTER DROPDOWNS (for modal / filters / reuse)
            'propertyTypes'      => PropertyType::orderBy('type')->get(),
            'propertyStatus'     => PropertyStatus::orderBy('status')->get(),
            'propertyZonings'    => PropertyZoning::orderBy('zoning')->get(),
            'propertyLocations' => PropertyLocation::orderBy('location')->get(),
            'propertyAreas'      => PropertyArea::orderBy('area')->get(),

            // 🔽 IMPORTANT: keep property defined for shared blades/modals
            'property' => null,
        ]);
    }

    // Property details screen
    public function propertyDetails($id)
    {
        // 🔄 Load property with relations
        $property = Properties::with([
            'landlords',
            'propertyManagers',
            'tenants',
            'agents',
            'units',
            'propertyLocation'
        ])->findOrFail($id);

        $properties = Properties::orderBy('building_name')->get();

        // 🔁 Load property images
        $propertyImages = Asset::whereHas('assignments', function ($q) use ($id) {
                $q->where('module_type', 'property')
                  ->where('module_id', $id);
            })
            ->where('user_id', auth()->id())
            ->where('folder', 'images')
            ->orderBy('created_at')
            ->get()
            ->map(function ($asset) {
                return (object) [
                    'id'  => $asset->id,
                    'url' => Storage::disk('public')->url($asset->file_path),
                ];
            });

        // 🔁 Attach unit images PER UNIT (ordered)
        $property->units->each(function ($unit) {

            $unit->images = Asset::whereHas('assignments', function ($q) use ($unit) {
                    $q->where('module_type', 'unit')
                      ->where('module_id', $unit->id);
                })
                ->where('user_id', auth()->id())
                ->where('folder', 'images')
                ->orderBy(
                    AssetAssignment::select('sort_order')
                        ->whereColumn('asset_assignments.asset_id', 'assets.id')
                        ->where('module_type', 'unit')
                        ->where('module_id', $unit->id)
                )
                ->get()
                ->map(function ($asset) {
                    return (object) [
                        'id'  => $asset->id,
                        'url' => Storage::disk('public')->url($asset->file_path),
                    ];
                });
        });

        $landlords = Landlord::orderBy('company_name')->get();

        return view('layouts.property_details', [
            // 🔽 CORE
            'property'       => $property,
            'properties'     => $properties,
            'propertyImages' => $propertyImages,
            'landlords'      => $landlords,

            // 🔽 MASTER DROPDOWNS
            'propertyTypes'      => PropertyType::orderBy('type')->get(),
            'propertyStatus'     => PropertyStatus::orderBy('status')->get(),
            'propertyZonings'    => PropertyZoning::orderBy('zoning')->get(),
            'propertyLocations' => PropertyLocation::orderBy('location')->get(),
            'propertyAreas'      => PropertyArea::orderBy('area')->get(),
        ]);
    }

    // BA: gallery
    public function ajaxPropertyAssets($propertyId)
    {
        $assets = Asset::where('user_id', auth()->id())
            ->where('folder', 'images')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($asset) use ($propertyId) {

                // ✅ CORRECT: build public URL from file_path
                $asset->public_url = Storage::disk('public')->url($asset->file_path);

                $asset->assigned = AssetAssignment::where([
                    'asset_id'    => $asset->id,
                    'module_type' => 'property',
                    'module_id'   => $propertyId,
                ])->exists();

                return $asset;
            });

        return response()->json($assets);
    }

  public function ajaxTogglePropertyAsset(Request $request)
  {
      $data = $request->validate([
          'asset_id'    => 'required|integer',
          'property_id' => 'required|integer',
      ]);

      $existing = AssetAssignment::where([
          'asset_id'    => $data['asset_id'],
          'module_type' => 'property',
          'module_id'   => $data['property_id'],
      ])->first();

      if ($existing) {
          $existing->delete();
          return response()->json(['success' => true, 'assigned' => false]);
      }

      AssetAssignment::create([
          'asset_id'    => $data['asset_id'],
          'module_type' => 'property',
          'module_id'   => $data['property_id'],
      ]);

      return response()->json(['success' => true, 'assigned' => true]);
  }


    // AJAX: fetch property for modal
    public function ajaxGetProperty($id)
    {
        $property = Properties::with(['landlords', 'units'])->findOrFail($id);
        return response()->json($property);
    }

    // AJAX: save/update property
    public function ajaxSaveProperty(Request $request)
    {
        $validated = $request->validate([
            // 'building_name'   => 'required|string|max:255',
            'building_name'   => 'nullable|string|max:255',
            // 'location'        => 'nullable|string',
            'location'        => 'nullable|numeric',
            'address'         => 'nullable|string',
            'blurb'           => 'nullable|string',
            'type_id'         => 'required|integer',
            'status_type'     => 'required|integer',
            'erf_no'          => 'nullable|string|max:100',
            'erf_size'        => 'nullable|string|max:100',
            'gla'             => 'nullable|string|max:100',
            'zoning'          => 'nullable|string|max:100',
            'property_locale' => 'nullable|string|max:255',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'scheme_number'   => 'nullable|string|max:100',
        ]);

        $property = Properties::updateOrCreate(
            ['id' => $request->id],
            $validated
        );

        return response()->json([
            'success'  => true,
            'property' => $property
        ]);
    }

    // AJAX: assign landlords to a property
    public function ajaxAssignLandlords(Request $request, $id)
    {
        $property = Properties::findOrFail($id);

        $landlordIds = (array) $request->input('landlord_ids', []);

        $property->landlords()->sync($landlordIds);
        $property->load('landlords');

        return response()->json([
            'success'   => true,
            'message'   => 'Landlords assigned successfully',
            'property'  => $property,
            'landlords' => $property->landlords,
        ]);
    }
}
