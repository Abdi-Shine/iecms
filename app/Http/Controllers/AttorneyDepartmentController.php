<?php

namespace App\Http\Controllers;

use App\Models\AttorneyDepartment;
use Illuminate\Http\Request;

class AttorneyDepartmentController extends Controller
{
    public function index()
    {
        $departments = AttorneyDepartment::orderBy('name')->get();

        return view('attorney.Settings.department_registration', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:attorney_departments,name',
            'description' => 'nullable|string|max:1000',
        ]);

        AttorneyDepartment::create($request->only('name', 'description'));

        return response()->json(['success' => true, 'message' => 'Department created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:attorney_departments,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        $department = AttorneyDepartment::findOrFail($id);
        $department->update($request->only('name', 'description'));

        return response()->json(['success' => true, 'message' => 'Department updated successfully.']);
    }

    public function destroy($id)
    {
        $department = AttorneyDepartment::findOrFail($id);
        $department->delete();

        return response()->json(['success' => true, 'message' => 'Department deleted successfully.']);
    }
}
