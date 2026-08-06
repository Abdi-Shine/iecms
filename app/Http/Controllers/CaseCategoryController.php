<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaseCategoryController extends Controller
{
    public function index()
    {
        $caseTypes  = \App\Models\CaseType::orderBy('case_name')->get();
        $categories = \App\Models\CaseCategory::orderBy('case_name')->get();
        return view('setting.case_category', compact('categories', 'caseTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'case_name'   => 'required|string|max:255',
            'sub_case'    => 'nullable|string|max:255',
            'rule'        => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        \App\Models\CaseCategory::create($request->only('case_name', 'sub_case', 'rule', 'description'));

        return response()->json(['success' => true, 'message' => 'Case category created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'case_name'   => 'required|string|max:255',
            'sub_case'    => 'nullable|string|max:255',
            'rule'        => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = \App\Models\CaseCategory::findOrFail($id);
        $category->update($request->only('case_name', 'sub_case', 'rule', 'description'));

        return response()->json(['success' => true, 'message' => 'Case category updated successfully.']);
    }

    public function destroy($id)
    {
        $category = \App\Models\CaseCategory::findOrFail($id);
        $category->delete();

        return response()->json(['success' => true, 'message' => 'Case category deleted successfully.']);
    }
}
