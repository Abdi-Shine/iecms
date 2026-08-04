<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Institution;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::withCount(['users', 'groups'])->orderBy('name')->get();

        return view('Platform.institutions.index', compact('institutions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150|unique:institutions,name',
            'slug' => 'required|string|max:100|alpha_dash|unique:institutions,slug',
            'type' => 'required|in:court,ago,cid,corrections',
        ]);

        $institution = Institution::create([
            'name'   => $request->name,
            'slug'   => $request->slug,
            'type'   => $request->type,
            'status' => 'active',
        ]);

        $adminRole = Role::where('name', 'Institution Admin')->first();

        $adminGroup = Group::create([
            'name'           => $institution->name . ' Admins',
            'description'    => 'Institution Admin group for ' . $institution->name,
            'institution_id' => $institution->id,
            'status'         => 'active',
            'addedBy'        => auth()->user()->name ?? 'Super Admin',
            'addedDate'      => now()->format('Y-m-d'),
        ]);

        if ($adminRole) {
            $adminGroup->roles()->attach($adminRole->id);
        }

        return redirect()->route('institutions.index')->with('success', 'Institution registered successfully.');
    }

    public function update(Request $request, Institution $institution)
    {
        $request->validate([
            'name'   => 'required|string|max:150|unique:institutions,name,' . $institution->id,
            'status' => 'required|in:active,inactive',
        ]);

        $institution->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('institutions.index')->with('success', 'Institution updated successfully.');
    }

    public function createAdmin(Institution $institution)
    {
        $adminGroup = Group::where('institution_id', $institution->id)
            ->where('name', $institution->name . ' Admins')
            ->first();

        return view('Platform.institutions.create-admin', compact('institution', 'adminGroup'));
    }

    public function storeAdmin(Request $request, Institution $institution)
    {
        $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $adminGroup = Group::firstOrCreate(
            ['institution_id' => $institution->id, 'name' => $institution->name . ' Admins'],
            ['status' => 'active', 'addedBy' => auth()->user()->name ?? 'Super Admin', 'addedDate' => now()->format('Y-m-d')]
        );

        if ($adminGroup->roles()->count() === 0) {
            $adminRole = Role::where('name', 'Institution Admin')->first();
            if ($adminRole) {
                $adminGroup->roles()->attach($adminRole->id);
            }
        }

        User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'group_id'       => $adminGroup->id,
            'institution_id' => $institution->id,
        ]);

        return redirect()->route('institutions.index')
            ->with('success', 'Institution Admin account created for ' . $institution->name . '.');
    }
}
