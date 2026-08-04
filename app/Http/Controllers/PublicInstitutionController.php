<?php

namespace App\Http\Controllers;

use App\Models\PublicInstitution;
use Illuminate\Http\Request;

class PublicInstitutionController extends Controller
{
    public function index(Request $request)
    {
        $query = PublicInstitution::orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = $this->resolvePerPage($request);

        $institutions = $query->paginate($perPage)->withQueryString();
        $totalInstitutions = PublicInstitution::count();

        return view('Courts.setting.public_institution', compact('institutions', 'totalInstitutions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:public_institutions,name',
        ]);

        PublicInstitution::create($request->only('name'));

        return response()->json(['success' => true, 'message' => 'Public institution created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:public_institutions,name,' . $id,
        ]);

        $institution = PublicInstitution::findOrFail($id);
        $institution->update($request->only('name'));

        return response()->json(['success' => true, 'message' => 'Public institution updated successfully.']);
    }

    public function destroy($id)
    {
        $institution = PublicInstitution::findOrFail($id);
        $institution->delete();

        return response()->json(['success' => true, 'message' => 'Public institution deleted successfully.']);
    }
}
