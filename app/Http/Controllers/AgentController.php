<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agents;
use App\Models\Properties;
use App\Models\AgentProperty;

class AgentController extends Controller
{
    /**
     * Show Agents list screen
     */
    public function getScreen()
    {
        $agents = Agents::orderBy('id', 'desc')->get();
        $properties = Properties::orderBy('building_name')->get();

        return view('layouts.agents', compact('agents', 'properties'));
    }

    /**
     * Save or update an Agent (AJAX)
     */
    public function ajaxSaveAgent(Request $request)
    {
        $validated = $request->validate([
            'id'             => 'nullable|integer|exists:agents,id',
            'company_name'   => 'required|string|max:255',
            'entity_name'    => 'nullable|string|max:255',
            'manager_name'   => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'telephone'      => 'nullable|string|max:50',
            'cell_number'    => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
        ]);

        $agent = Agents::updateOrCreate(
            ['id' => $validated['id'] ?? null],
            $validated
        );

        // 🔗 Handle property assignments if provided
        $propertyIds = $request->input('properties', []);
        if (!empty($propertyIds)) {
            AgentProperty::where('agent_id', $agent->id)->delete();
            foreach ($propertyIds as $pid) {
                AgentProperty::create([
                    'agent_id' => $agent->id,
                    'property_id' => $pid,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'agent' => $agent,
            'message' => 'Agent and assigned properties saved successfully',
        ]);
    }

    /**
     * Delete an Agent
     */
    public function ajaxDeleteAgent($id)
    {
        $agent = Agents::find($id);

        if (!$agent) {
            return response()->json(['success' => false, 'message' => 'Agent not found'], 404);
        }

        AgentProperty::where('agent_id', $id)->delete();
        $agent->delete();

        return response()->json(['success' => true, 'message' => 'Agent deleted successfully']);
    }

    /**
     * Get a single Agent (AJAX)
     */
    public function ajaxGetAgent($id)
    {
        $agent = Agents::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        return response()->json($agent);
    }

    /**
     * Get linked properties for an agent
     */
     public function ajaxGetAgentProperties($id)
     {
         $properties = AgentProperty::where('agent_id', $id)
             ->get(['property_id']);

         return response()->json($properties);
     }

    /**
     * Assign or update properties for an agent
     */
    public function ajaxAssignProperties(Request $request, $id)
    {
        $agent = Agents::findOrFail($id);
        $propertyIds = $request->input('properties', []);

        AgentProperty::where('agent_id', $agent->id)->delete();

        foreach ($propertyIds as $pid) {
            AgentProperty::create([
                'agent_id' => $agent->id,
                'property_id' => $pid,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Properties assigned successfully',
            'agent_id' => $agent->id,
            'property_ids' => $propertyIds,
        ]);
    }
}
