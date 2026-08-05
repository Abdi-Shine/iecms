<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlatformUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('institution', 'group.roles');

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        $users = $query->orderBy('name')->get();

        if ($request->filled('tier')) {
            $users = $users->filter(fn ($u) => $this->tierOf($u) === $request->tier)->values();
        }

        $lastLogins = AuditLog::where('action', 'login')
            ->select('user_id', DB::raw('MAX(created_at) as last_login'))
            ->groupBy('user_id')
            ->pluck('last_login', 'user_id');

        $users->each(function ($user) use ($lastLogins) {
            $user->tier = $this->tierOf($user);
            $user->last_login = $lastLogins[$user->id] ?? null;
        });

        $institutions = Institution::orderBy('name')->get();

        return view('Platform.users.index', compact('users', 'institutions'));
    }

    private function tierOf(User $user): string
    {
        if ($user->is_super_admin) {
            return 'Super Admin';
        }

        return $user->isInstitutionAdmin() ? 'Institution Admin' : 'Staff';
    }
}
