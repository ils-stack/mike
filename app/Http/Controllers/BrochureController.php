<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brochure;
use App\Models\BrochureItem;
use App\Models\UnitDetails;
use App\Models\Agents as Agent;
use Auth;

class BrochureController extends Controller
{
    /**
     * Show list of brochures
     */
    public function index()
    {
        $brochures = Brochure::orderBy('id', 'DESC')->get();

        return view('brochure.index', compact('brochures'));
    }

    /**
     * Brochure builder page
     */
    public function create()
    {
        // Get selected unit IDs from session cart
        $cart = session('brochure_cart', []);

        // Load full unit details
        $units = UnitDetails::whereIn('id', $cart)->get();

        return view('brochure.create', compact('units'));
    }


    /**
     * Save brochure + items (PDF later)
     */
    public function store(Request $request)
    {
        // 1. Save brochure record
        $br = Brochure::create([
            'title'     => $request->title,
            'user_id'   => Auth::id(),
            'file_path' => null,   // set after PDF generation
        ]);

        // 2. Retrieve unit IDs from cart
        $cart = session('brochure_cart', []);

        foreach ($cart as $i => $unitId) {
            BrochureItem::create([
                'brochure_id' => $br->id,
                'unit_id'     => $unitId,   // UPDATED
                'sort_order'  => $i,
            ]);
        }

        // 3. Clear cart
        session()->forget('brochure_cart');

        return response()->json([
            'success' => true,
            'brochure_id' => $br->id
        ]);
    }


    /**
     * Delete brochure
     */
    public function destroy($id)
    {
        $br = Brochure::find($id);

        if ($br) {
            // Delete PDF file from storage if exists
            if ($br->file_path && file_exists(public_path($br->file_path))) {
                @unlink(public_path($br->file_path));
            }

            // Delete DB rows
            BrochureItem::where('brochure_id', $id)->delete();
            $br->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }


    // ============================================================================
    //                                CART OPERATIONS
    // ============================================================================

    /**
     * Add unit to brochure cart
     */
    public function cartAdd(Request $request)
    {
        $id = (int) $request->unit_id;   // UPDATED

        $cart = session('brochure_cart', []);

        if (!in_array($id, $cart)) {
            $cart[] = $id;
            session(['brochure_cart' => $cart]);
        }

        return response()->json([
            'success' => true,
            'count' => count($cart)
        ]);
    }


    /**
     * Remove unit from cart
     */
    public function cartRemove(Request $request)
    {
        $id = (int) $request->unit_id;  // UPDATED

        $cart = session('brochure_cart', []);

        $cart = array_filter($cart, fn($v) => $v != $id);

        session(['brochure_cart' => $cart]);

        return response()->json([
            'success' => true,
            'count' => count($cart)
        ]);
    }


    /**
     * Get count for navbar badge
     */
    public function cartCount()
    {
        return response()->json([
            'count' => count(session('brochure_cart', []))
        ]);
    }


    /**
     * Clear brochure cart
     */
    public function cartClear()
    {
        session()->forget('brochure_cart');

        return response()->json([
            'success' => true,
            'count' => 0
        ]);
    }

    public function preview($id)
    {
        $br = Brochure::with('items')->findOrFail($id);

        // Brochure items contain unit_ids
        $unitIds = $br->items->pluck('unit_id');

        // Load units
        $units = \App\Models\UnitDetails::whereIn('id', $unitIds)->get();

        // Load parent property of first unit
        $property = null;
        if ($units->first()) {
            $property = \App\Models\Properties::find(
                \App\Models\UnitProperty::where('unit_id', $units->first()->id)->value('property_id')
            );
        }

        // Load an agent belonging to property (OPTION B)
        $agent = null;
        if ($property) {
            $agentId = \DB::table('agent_property')->where('property_id', $property->id)->value('agent_id');
            if ($agentId) {
                $agent = \App\Models\Agents::find($agentId);
            }
        }

        return view('brochure.preview', compact('property','units','agent'));
    }

    public function download($id)
{
    // FIX: correct eager load (properties is the actual relationship)
    $brochure = Brochure::with([
        'items.unit.properties'
    ])->findOrFail($id);

    // Load the units included in this brochure
    $units = $brochure->items->map(function($item) {
        return $item->unit;
    });

    // Collect all property_ids from the unit_property pivot table
    $propertyIds = [];

    foreach ($units as $unit) {
        foreach ($unit->properties as $prop) {
            $propertyIds[] = $prop->id;
        }
    }

    $propertyIds = array_unique($propertyIds);

    // Load agent(s)
    $agents = \DB::table('agents')
        ->join('agent_property', 'agents.id', '=', 'agent_property.agent_id')
        ->whereIn('agent_property.property_id', $propertyIds)
        ->select('agents.*')
        ->get();

    // Load property managers
    $managers = \DB::table('property_managers')
        ->join('property_manager_property', 'property_managers.id', '=', 'property_manager_property.manager_id')
        ->whereIn('property_manager_property.property_id', $propertyIds)
        ->select('property_managers.*')
        ->get();

    // Generate PDF
    $pdf = \PDF::loadView('brochure.pdf', [
        'brochure' => $brochure,
        'units'    => $units,
        'agents'   => $agents,
        'managers' => $managers
    ])->setPaper('a4');

    return $pdf->download('brochure-'.$brochure->id.'.pdf');
}


}
