<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use Illuminate\Http\Request;

class CriminalCaseController extends Controller
{
    public function index(Request $request)
    {
        $query = CriminalCase::query();

        if ($request->filled('search')) {
            $query->where('case_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('stage')) {
            $query->where('current_stage', $request->stage);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('officer')) {
            $officerId = $request->officer;
            $query->where(function ($q) use ($officerId) {
                $q->whereHas('assignment', fn ($a) => $a->where('assigned_investigator_id', $officerId))
                  ->orWhereHas('occurrenceBook', fn ($o) => $o->where('assigned_investigator_id', $officerId));
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $cases = $query->with(['arrest', 'occurrenceBook.assignedInvestigator', 'assignment.assignedInvestigator', 'custody'])
            ->latest()->paginate(15)->withQueryString();

        $investigators = \App\Models\User::whereHas('group', function ($q) {
            $q->whereHas('roles', fn ($r) => $r->where('name', 'Investigator'));
        })->orderBy('name')->get();

        $isAdmin = $request->user()->group?->roles->contains('name', 'CID Institution Admin') ?? false;

        return view('cid.cases.index', compact('cases', 'investigators', 'isAdmin'));
    }

    public function export(Request $request)
    {
        $cases = CriminalCase::with(['arrest', 'occurrenceBook', 'assignment.assignedInvestigator'])->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="cid_investigation_cases_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($cases) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Case Number', 'OB Number', 'Suspect Name', 'Assigned Investigator', 'Stage', 'Priority', 'Status', 'Opened']);
            foreach ($cases as $case) {
                fputcsv($file, [
                    $case->case_number,
                    $case->occurrenceBook->ob_number ?? '',
                    $case->arrest->arrestee_name ?? '',
                    $case->assignment->assignedInvestigator->name ?? '',
                    $case->current_stage,
                    $case->priority,
                    $case->status,
                    $case->created_at->format('Y-m-d'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkReassign(Request $request)
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'case_ids'    => 'required|array',
            'case_ids.*'  => 'exists:criminal_cases,id',
            'investigator_id' => 'required|exists:users,id',
        ]);

        $cases = CriminalCase::whereIn('id', $data['case_ids'])->with('assignment')->get();
        foreach ($cases as $case) {
            if ($case->assignment) {
                $case->assignment->update(['assigned_investigator_id' => $data['investigator_id']]);
            }
            $case->occurrenceBook?->update(['assigned_investigator_id' => $data['investigator_id']]);
        }

        return redirect()->route('criminal-cases.index')->with('success', count($cases) . ' case(s) reassigned.');
    }

    public function bulkClose(Request $request)
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'case_ids'   => 'required|array',
            'case_ids.*' => 'exists:criminal_cases,id',
        ]);

        CriminalCase::whereIn('id', $data['case_ids'])->update(['status' => 'Closed', 'current_stage' => 'closed']);

        return redirect()->route('criminal-cases.index')->with('success', count($data['case_ids']) . ' case(s) closed.');
    }

    private function authorizeAdmin(Request $request): void
    {
        $isAdmin = $request->user()->group?->roles->contains('name', 'CID Institution Admin') ?? false;
        if (!$isAdmin) {
            abort(403, 'Only an Institution Admin can perform bulk actions.');
        }
    }

    public function store(Request $request)
    {
        $case = CriminalCase::create([
            'priority' => $request->input('priority', 'Routine'),
            'added_by' => $request->user()->name ?? 'Staff',
        ]);

        if ($request->boolean('skip_to_ob')) {
            return redirect()->route('criminal-cases.workflow.ob.form', $case->id);
        }

        return redirect()->route('criminal-cases.workflow.arrest.form', $case->id);
    }

    public function diaryIndex(Request $request, $id)
    {
        $case = CriminalCase::with('diaryEntries.user')->findOrFail($id);

        return view('cid.cases.diary', compact('case'));
    }

    public function storeDiaryEntry(Request $request, $id)
    {
        $case = CriminalCase::findOrFail($id);

        $data = $request->validate([
            'description' => 'required|string',
        ]);

        $case->diaryEntries()->create([
            'entry_type'  => 'manual',
            'action_type' => 'Manual Note',
            'description' => $data['description'],
            'user_id'     => $request->user()->id,
        ]);

        return redirect()->route('criminal-cases.diary', $case->id)
            ->with('success', 'Diary entry added.');
    }
}
