<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StateRegionController extends Controller
{
    public function index()
    {
        $regions = \App\Models\StateRegion::orderBy('state_name')->get();
        return view('Courts.setting.state_region', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'state_name' => 'required|string|max:255|unique:state_regions,state_name',
            'capital' => 'nullable|string|max:255'
        ]);
        
        \App\Models\StateRegion::create($request->only('state_name', 'capital'));
        
        return response()->json(['success' => true, 'message' => 'State/Region created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'state_name' => 'required|string|max:255|unique:state_regions,state_name,' . $id,
            'capital' => 'nullable|string|max:255'
        ]);
        
        $region = \App\Models\StateRegion::findOrFail($id);
        $region->update($request->only('state_name', 'capital'));
        
        return response()->json(['success' => true, 'message' => 'State/Region updated successfully.']);
    }

    public function destroy($id)
    {
        $region = \App\Models\StateRegion::findOrFail($id);
        $region->delete();
        
        return response()->json(['success' => true, 'message' => 'State/Region deleted successfully.']);
    }
}
