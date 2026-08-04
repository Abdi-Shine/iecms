<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Role;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::withCount('users')->with('roles');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $groups = $query->orderBy('id', 'desc')->get();
        $roles  = Role::orderBy('display_name')->get();

        $stats = [
            'total'    => Group::count(),
            'active'   => Group::where('status', 'active')->count(),
            'inactive' => Group::where('status', 'inactive')->count(),
        ];

        return view('Courts.setting.group_view', compact('groups', 'roles', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100|unique:groups,name',
            'color'  => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $group = Group::create([
            'name'        => $request->name,
            'description' => $request->description,
            'color'       => $request->color,
            'status'      => $request->status,
            'addedBy'     => auth()->user()->name ?? 'Admin',
            'addedDate'   => now()->format('Y-m-d'),
        ]);

        if ($request->filled('roles')) {
            $group->roles()->sync($request->roles);
        }

        return redirect()->route('groups.index')->with('success', 'Group created successfully.');
    }

    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:100|unique:groups,name,' . $id,
            'color'  => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $group->update([
            'name'        => $request->name,
            'description' => $request->description,
            'color'       => $request->color,
            'status'      => $request->status,
            'updatedBy'   => auth()->user()->name ?? 'Admin',
            'updatedDate' => now()->format('Y-m-d'),
        ]);

        $group->roles()->sync($request->roles ?? []);

        return redirect()->route('groups.index')->with('success', 'Group updated successfully.');
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->users()->update(['group_id' => null]);
        $group->roles()->detach();
        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }
}
