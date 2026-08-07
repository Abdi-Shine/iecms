<?php

namespace App\Http\Controllers;

use App\Models\CriminalCaseOccurrenceBook;
use Illuminate\Http\Request;

class CriminalObController extends Controller
{
    public const STATUSES = ['Draft', 'Assigned', 'Active', 'Closed'];

    public function index(Request $request)
    {
        return $this->filteredView($request, internal: false, archived: false);
    }

    public function internal(Request $request)
    {
        $roleNames = $request->user()->group?->roles->pluck('name') ?? collect();
        if (!$roleNames->intersect(['CID Institution Admin', 'Investigator'])->count()) {
            abort(403, 'Internal OB is restricted to Institution Admin and Investigator roles.');
        }

        return $this->filteredView($request, internal: true, archived: false);
    }

    public function archive(Request $request)
    {
        $roleNames = $request->user()->group?->roles->pluck('name') ?? collect();
        if (!$roleNames->intersect(['CID Institution Admin', 'Investigator'])->count()) {
            abort(403, 'OB Archive is restricted to Institution Admin and Investigator roles.');
        }

        return $this->filteredView($request, internal: null, archived: true);
    }

    private function filteredView(Request $request, ?bool $internal, bool $archived)
    {
        $base = CriminalCaseOccurrenceBook::query();
        if ($internal !== null) {
            $base->where('is_internal', $internal);
        }
        if ($archived) {
            $base->whereHas('criminalCase', fn ($q) => $q->where('status', 'Closed'));
        }

        $stats = [
            'draft' => (clone $base)
                ->whereDoesntHave('criminalCase', fn ($q) => $q->where('status', 'Closed'))
                ->whereNull('assigned_investigator_id')
                ->count(),
            'assigned' => (clone $base)
                ->whereDoesntHave('criminalCase', fn ($q) => $q->where('status', 'Closed'))
                ->whereNotNull('assigned_investigator_id')
                ->whereNull('supervisor_acknowledged_at')
                ->count(),
            'active' => (clone $base)
                ->whereDoesntHave('criminalCase', fn ($q) => $q->where('status', 'Closed'))
                ->whereNotNull('supervisor_acknowledged_at')
                ->count(),
            'closed' => (clone $base)
                ->whereHas('criminalCase', fn ($q) => $q->where('status', 'Closed'))
                ->count(),
        ];

        $query = (clone $base)->with(['criminalCase', 'assignedInvestigator', 'supervisor'])->latest('ob_datetime');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ob_number', 'like', "%{$search}%")
                  ->orWhereHas('criminalCase', fn ($c) => $c->where('case_number', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('location')) {
            $query->where('incident_location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('offence_type')) {
            $query->where('offence_nature', 'like', '%' . $request->offence_type . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'Draft':
                    $query->whereDoesntHave('criminalCase', fn ($q) => $q->where('status', 'Closed'))
                          ->whereNull('assigned_investigator_id');
                    break;
                case 'Assigned':
                    $query->whereDoesntHave('criminalCase', fn ($q) => $q->where('status', 'Closed'))
                          ->whereNotNull('assigned_investigator_id')
                          ->whereNull('supervisor_acknowledged_at');
                    break;
                case 'Active':
                    $query->whereDoesntHave('criminalCase', fn ($q) => $q->where('status', 'Closed'))
                          ->whereNotNull('supervisor_acknowledged_at');
                    break;
                case 'Closed':
                    $query->whereHas('criminalCase', fn ($q) => $q->where('status', 'Closed'));
                    break;
            }
        }

        if ($request->filled('from')) {
            $query->whereDate('ob_datetime', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('ob_datetime', '<=', $request->to);
        }

        $perPage = $this->resolvePerPage($request);
        $obs = $query->paginate($perPage)->withQueryString();

        $view = $archived ? 'cid.cases.ob_archive' : 'cid.cases.ob_registry';

        return view($view, [
            'obs'      => $obs,
            'internal' => $internal,
            'stats'    => $stats,
            'statuses' => self::STATUSES,
        ]);
    }
}
