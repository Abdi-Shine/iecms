<?php

namespace App\Http\Controllers;

use App\Models\CriminalCaseOccurrenceBook;
use Illuminate\Http\Request;

class CriminalObController extends Controller
{
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
        $query = CriminalCaseOccurrenceBook::with(['criminalCase', 'assignedInvestigator', 'supervisor']);

        if ($internal !== null) {
            $query->where('is_internal', $internal);
        }

        if ($archived) {
            $query->whereHas('criminalCase', fn ($q) => $q->where('status', 'Closed'));
        }

        if ($request->filled('ob_number')) {
            $query->where('ob_number', 'like', '%' . $request->ob_number . '%');
        }
        if ($request->filled('officer')) {
            $query->where('assigned_investigator_id', $request->officer);
        }
        if ($request->filled('offence_type')) {
            $query->where('offence_nature', 'like', '%' . $request->offence_type . '%');
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('from')) {
            $query->whereDate('ob_datetime', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('ob_datetime', '<=', $request->to);
        }

        $obs = $query->latest('ob_datetime')->paginate(15)->withQueryString();

        $investigators = \App\Models\User::whereHas('group', function ($q) {
            $q->whereHas('roles', fn ($r) => $r->where('name', 'Investigator'));
        })->orderBy('name')->get();

        $view = $archived ? 'cid.cases.ob_archive' : 'cid.cases.ob_registry';

        return view($view, ['obs' => $obs, 'investigators' => $investigators, 'internal' => $internal]);
    }
}
