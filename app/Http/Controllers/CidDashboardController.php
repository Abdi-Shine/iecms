<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class CidDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $institution = $user->institution;

        $roleNames = $user->group?->roles->pluck('name') ?? collect();
        $roleLabel = match (true) {
            $roleNames->contains('CID Institution Admin') => 'Institution Admin',
            $roleNames->contains('Investigator')          => 'Investigator',
            $roleNames->contains('Officer')                => 'Officer',
            $roleNames->contains('Other Staff (CID)')      => 'Other Staff',
            default                                        => 'Staff',
        };

        $isAdmin = $roleNames->contains('CID Institution Admin');

        $stats = [
            'staffCount' => $institution
                ? Group::where('institution_id', $institution->id)->withCount('users')->get()->sum('users_count')
                : 0,
        ];

        return view('cid.dashboard.index', compact('institution', 'roleLabel', 'isAdmin', 'stats'));
    }
}
