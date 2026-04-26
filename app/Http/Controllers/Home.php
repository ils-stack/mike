<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Properties as Property;
use App\Models\Landlord;

use App\Models\PropertyType;
use App\Models\PropertyStatus;
use App\Models\PropertyZoning;
use App\Models\PropertyLocation;
use App\Models\PropertyArea;

class Home extends Controller
{
    public function getScreen(Request $request)
    {
        $properties = Property::with('landlords')->get();
        $landlords  = Landlord::orderBy('company_name')->get();

        // 🔹 Locations for Google Map (with status)
        $locations = Property::query()
            ->whereNotNull('properties.latitude')
            ->whereNotNull('properties.longitude')
            ->leftJoin('property_status', 'properties.status_type', '=', 'property_status.id')
            ->get([
                'properties.id',
                'properties.latitude',
                'properties.longitude',
                'properties.building_name',
                'property_status.status',
                'property_status.marker_letter',
                'property_status.marker_color',
            ]);


        return view('layouts.dashboard', [
            'properties' => $properties,
            'landlords'  => $landlords,
            'locations'  => $locations,

            // 🔽 MASTER DROPDOWNS
            'propertyTypes'      => PropertyType::orderBy('type')->get(),
            'propertyStatus'     => PropertyStatus::orderBy('status')->get(),
            'propertyZonings'    => PropertyZoning::orderBy('zoning')->get(),
            'propertyLocations' => PropertyLocation::orderBy('location')->get(),
            'propertyAreas'      => PropertyArea::orderBy('area')->get(),

            // 🔽 Shared blades expect this
            'property' => null,
        ]);
    }

    public function infobox($id)
    {
        $property = Property::with('units')->findOrFail($id);
        return view('properties.partials.infobox', compact('property'));
    }

    // PropertyController.php
    public function propertiesInBounds(Request $r)
    {
        return Property::query()
            ->whereNotNull('properties.latitude')
            ->whereNotNull('properties.longitude')
            ->whereBetween('properties.latitude', [$r->sw_lat, $r->ne_lat])
            ->whereBetween('properties.longitude', [$r->sw_lng, $r->ne_lng])
            ->leftJoin('property_status', 'properties.status_type', '=', 'property_status.id')
            ->select([
                'properties.id',
                'properties.building_name',
                'properties.latitude',
                'properties.longitude',
                'properties.property_locale',
                'property_status.status',
                'property_status.marker_letter',
                'property_status.marker_color',
            ])
            ->get();
    }




}
