<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaseTypeController extends Controller
{
    public function index()
    {
        $caseTypes = \App\Models\CaseType::orderBy('case_name')->get();
        return view('setting.case_type', compact('caseTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'case_name'   => 'required|string|max:255|unique:case_types,case_name',
            'description' => 'nullable|string|max:1000',
        ]);

        \App\Models\CaseType::create($request->only('case_name', 'description'));

        return response()->json(['success' => true, 'message' => 'Case type created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'case_name'   => 'required|string|max:255|unique:case_types,case_name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        $caseType = \App\Models\CaseType::findOrFail($id);
        $caseType->update($request->only('case_name', 'description'));

        return response()->json(['success' => true, 'message' => 'Case type updated successfully.']);
    }

    public function destroy($id)
    {
        $caseType = \App\Models\CaseType::findOrFail($id);
        $caseType->delete();

        return response()->json(['success' => true, 'message' => 'Case type deleted successfully.']);
    }
}
