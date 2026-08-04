<?php

namespace App\Http\Controllers;

use App\Models\AppealCivilRegistration;
use App\Models\AttorneyCase;
use App\Models\DistricCivilRegistration;
use App\Models\DistrictCriminalRegistration;
use App\Models\DistrictExecutionRegistration;
use App\Models\DistrictFamilyRegistration;
use App\Models\Institution;
use App\Models\User;

class PlatformDashboardController extends Controller
{
    public function index()
    {
        $institutions = Institution::withCount('users')->orderBy('name')->get();

        foreach ($institutions as $institution) {
            $institution->case_count = match ($institution->type) {
                'ago' => AttorneyCase::withoutGlobalScopes()->where('institution_id', $institution->id)->count(),
                'court' => DistricCivilRegistration::withoutGlobalScopes()->where('institution_id', $institution->id)->count()
                    + DistrictFamilyRegistration::withoutGlobalScopes()->where('institution_id', $institution->id)->count()
                    + DistrictCriminalRegistration::withoutGlobalScopes()->where('institution_id', $institution->id)->count()
                    + DistrictExecutionRegistration::withoutGlobalScopes()->where('institution_id', $institution->id)->count()
                    + AppealCivilRegistration::withoutGlobalScopes()->where('institution_id', $institution->id)->count(),
                default => 0,
            };
        }

        $stats = [
            'total_institutions'  => $institutions->count(),
            'active_institutions' => $institutions->where('status', 'active')->count(),
            'total_users'         => User::count(),
            'super_admins'        => User::where('is_super_admin', true)->count(),
        ];

        return view('Platform.dashboard', compact('institutions', 'stats'));
    }
}
