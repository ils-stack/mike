<?php

namespace App\Http\Controllers;

use App\Models\Properties as PropModel;
use App\Models\Landlord;
use App\Models\Tenants;
use App\Models\UnitDetail;

use Illuminate\Http\Request;

class AjaxController extends Controller
{
  public function ajaxSaveProp(Request $request)
  {
      $validated = $request->validate([
          'id'                => 'nullable|integer|exists:properties,id',
          'building_name'     => 'required|string',
          'blurb'             => 'nullable|string',
          'type_id'           => 'nullable|integer',
          'status_type'       => 'nullable|integer',
          'erf_no'            => 'nullable|string',
          'erf_size'          => 'nullable|string',
          'gla'               => 'nullable|string',
          'zoning'            => 'nullable|string',
          'property_locale'   => 'nullable|string',
          'latitude'          => 'nullable|string',
          'longitude'         => 'nullable|string',
      ]);

      $property = PropModel::updateOrCreate(
          ['id' => $validated['id'] ?? null],
          [
              'building_name'     => $validated['building_name'],
              'blurb'             => $validated['blurb'],
              'type_id'           => $validated['type_id'],
              'status_type'       => $validated['status_type'],
              'erf_no'            => $validated['erf_no'],
              'erf_size'          => $validated['erf_size'],
              'gla'               => $validated['gla'],
              'zoning'            => $validated['zoning'],
              'property_locale'   => $validated['property_locale'],
              'latitude'          => $validated['latitude'],
              'longitude'         => $validated['longitude'],
          ]
      );

      return response()->json([
          'success' => true,
          // 'property' => $property
      ]);
  }

  public function ajaxSavePropertyManager(Request $request)
  {
      $validated = $request->validate([
          'id'                => 'nullable|integer|exists:property_managers,id',
          'company_name'      => 'required|string',
          'contact_person'    => 'required|string',
          'telephone'         => 'nullable|string',
          'cell_number'       => 'nullable|string',
          'email'             => 'nullable|string|email',
      ]);

      $propertyManager = \App\Models\PropertyManager::updateOrCreate(
          ['id' => $validated['id'] ?? null],
          [
              'company_name'   => $validated['company_name'],
              'contact_person' => $validated['contact_person'],
              'telephone'      => $validated['telephone'] ?? null,
              'cell_number'    => $validated['cell_number'] ?? null,
              'email'          => $validated['email'] ?? null,
          ]
      );

      return response()->json([
          'success' => true,
          'property_manager' => $propertyManager
      ]);
  }

  public function ajaxSaveTenant(Request $request)
  {
      $validated = $request->validate([
          'id'              => 'nullable|integer|exists:tenants,id',
          'company_name'    => 'nullable|string',
          'entity_name'     => 'nullable|string',
          'contact_person'  => 'nullable|string',
          'telephone'       => 'nullable|string',
          'cell_number'     => 'nullable|string',
          'email'           => 'nullable|string|email|unique:tenants,email,' . $request->id,
      ]);

      $tenant = Tenants::updateOrCreate(
          ['id' => $validated['id'] ?? null],
          [
              'company_name'    => $validated['company_name'] ?? null,
              'entity_name'     => $validated['entity_name'] ?? null,
              'contact_person'  => $validated['contact_person'] ?? null,
              'telephone'       => $validated['telephone'] ?? null,
              'cell_number'     => $validated['cell_number'] ?? null,
              'email'           => $validated['email'] ?? null,
          ]
      );

      return response()->json([
          'success' => true,
          'tenant' => $tenant
      ]);
  }
}
