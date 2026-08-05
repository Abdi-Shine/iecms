<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use Illuminate\Http\Request;

class CriminalCaseWorkflowController extends Controller
{
    public const ARREST_TYPES = ['with_warrant', 'without_warrant'];

    public function show(Request $request, $id)
    {
        $case = CriminalCase::with(['arrest', 'occurrenceBook', 'assignment'])->findOrFail($id);

        $obComplete = $case->occurrenceBook?->isComplete() ?? false;
        $assignmentComplete = (bool) $case->assignment;

        $steps = [
            [
                'key'         => 'arrest',
                'title'       => 'Arrest',
                'description' => 'Warrant / Without Warrant',
                'enabled'     => true,
                'route'       => route('criminal-cases.workflow.arrest.form', $case->id),
                'complete'    => (bool) $case->arrest,
            ],
            [
                'key'         => 'occurrence_book',
                'title'       => 'Occurrence Book',
                'description' => 'Initial Report & Assignment',
                'enabled'     => (bool) $case->arrest,
                'route'       => $case->arrest ? route('criminal-cases.workflow.ob.form', $case->id) : null,
                'complete'    => $obComplete,
            ],
            [
                'key'         => 'case_assignment_evidence',
                'title'       => 'Case Assignment & Evidence',
                'description' => 'Formal assignment and evidence collection',
                'enabled'     => $obComplete,
                'route'       => $obComplete ? route('criminal-cases.workflow.assignment.form', $case->id) : null,
                'complete'    => $assignmentComplete,
            ],
            [
                'key'         => 'custody_court_scheduling',
                'title'       => 'Custody & Court Scheduling',
                'description' => 'Detention status and court appearances',
                'enabled'     => $assignmentComplete,
                'route'       => null,
                'complete'    => false,
            ],
            [
                'key'         => 'final_report_ago_submission',
                'title'       => 'Final Report & AGO Submission',
                'description' => 'Compile file and submit for prosecution decision',
                'enabled'     => false,
                'route'       => null,
                'complete'    => false,
            ],
        ];

        return view('cid.cases.workflow', [
            'case'  => $case,
            'steps' => $steps,
        ]);
    }

    public function arrestForm(Request $request, $id)
    {
        $case = CriminalCase::with('arrest')->findOrFail($id);

        return view('cid.cases.arrest_form', compact('case'));
    }

    public function storeArrest(Request $request, $id)
    {
        $case = CriminalCase::findOrFail($id);

        $data = $request->validate([
            'arrest_type' => 'required|in:' . implode(',', self::ARREST_TYPES),

            'arrestee_name'         => 'required|string|max:150',
            'arrestee_national_id'  => 'nullable|string|max:100',
            'arrestee_dob'          => 'nullable|date',
            'arrestee_gender'       => 'nullable|string|max:10',
            'arrestee_nationality'  => 'nullable|string|max:100',
            'arrestee_address'      => 'nullable|string|max:255',
            'arrestee_contact'      => 'nullable|string|max:50',

            'arresting_officer_name'  => 'required|string|max:150',
            'arresting_officer_badge' => 'nullable|string|max:50',
            'arresting_officer_unit'  => 'nullable|string|max:100',

            'arrest_date'     => 'required|date',
            'arrest_time'     => 'nullable',
            'arrest_location' => 'nullable|string|max:255',
            'arrest_gps'      => 'nullable|string|max:50',

            'alleged_offence'    => 'required|string|max:255',
            'statute_reference'  => 'nullable|string|max:150',

            'warrant_number'         => 'required_if:arrest_type,with_warrant|nullable|string|max:100',
            'warrant_issuing_court'  => 'nullable|string|max:150',
            'warrant_issuing_judge'  => 'nullable|string|max:150',
            'warrant_issue_date'     => 'nullable|date',
            'warrant_expiry_date'    => 'nullable|date',

            'warrantless_justification' => 'required_if:arrest_type,without_warrant|nullable|string',
            'warrantless_witnesses'     => 'nullable|string',

            'physical_condition'              => 'nullable|string',
            'items_seized_preliminary'        => 'nullable|string',
            'detention_location_after_arrest' => 'nullable|string|max:150',
        ]);

        $data['added_by'] = $request->user()->name ?? 'Staff';

        if ($case->arrest) {
            $case->arrest->update($data);
        } else {
            $case->arrest()->create($data);
        }

        $case->advanceStageTo('occurrence_book');

        return redirect()->route('criminal-cases.workflow', $case->id)
            ->with('success', 'Arrest record saved. Case advanced to Occurrence Book.');
    }

    public function obForm(Request $request, $id)
    {
        $case = CriminalCase::with(['arrest', 'occurrenceBook'])->findOrFail($id);

        if (!$case->arrest) {
            return redirect()->route('criminal-cases.workflow.arrest.form', $case->id)
                ->with('error', 'Complete the Arrest stage before the Occurrence Book.');
        }

        $investigators = \App\Models\User::whereHas('group', function ($q) use ($case) {
            $q->whereHas('roles', fn ($r) => $r->where('name', 'Investigator'))
              ->where('institution_id', $case->institution_id);
        })->orderBy('name')->get();

        return view('cid.cases.ob_form', compact('case', 'investigators'));
    }

    public function storeOb(Request $request, $id)
    {
        $case = CriminalCase::with('arrest')->findOrFail($id);

        if (!$case->arrest) {
            abort(422, 'Complete the Arrest stage before the Occurrence Book.');
        }

        $data = $request->validate([
            'ob_datetime'               => 'required|date',
            'reporting_officer'         => 'required|string|max:150',
            'offence_nature'            => 'required|string|max:255',
            'incident_datetime'         => 'nullable|date',
            'incident_location'         => 'nullable|string|max:255',
            'complainant_name'          => 'nullable|string|max:150',
            'complainant_contact'       => 'nullable|string|max:50',
            'complainant_id'            => 'nullable|string|max:100',
            'complainant_relationship'  => 'nullable|string|max:100',
            'suspect_details'           => 'nullable|string',
            'narrative'                 => 'nullable|string',
            'witnesses'                 => 'nullable|string',
            'initial_evidence_noted'    => 'nullable|string',
            'priority'                  => 'required|in:Routine,Urgent,Critical',
            'assigned_investigator_id'  => 'nullable|exists:users,id',
            'assigned_unit'             => 'nullable|string|max:100',
        ]);

        $obData = collect($data)->except(['priority'])->all();
        $obData['added_by'] = $request->user()->name ?? 'Staff';

        if ($case->occurrenceBook) {
            $case->occurrenceBook->update($obData);
        } else {
            $case->occurrenceBook()->create($obData);
        }

        $case->update(['priority' => $data['priority']]);

        return redirect()->route('criminal-cases.workflow.ob.form', $case->id)
            ->with('success', 'Occurrence Book entry saved.');
    }

    public function acknowledgeOb(Request $request, $id)
    {
        $case = CriminalCase::with('occurrenceBook')->findOrFail($id);
        $ob   = $case->occurrenceBook;

        if (!$ob) {
            abort(404);
        }

        $roleNames = $request->user()->group?->roles->pluck('name') ?? collect();
        if (!$roleNames->intersect(['CID Institution Admin', 'Investigator'])->count()) {
            abort(403, 'Only an Investigator or Institution Admin can acknowledge an Occurrence Book entry.');
        }

        if (!$ob->assigned_investigator_id) {
            return back()->withErrors(['assigned_investigator_id' => 'Assign an investigator before acknowledging.']);
        }

        $ob->update([
            'supervisor_acknowledged_by' => $request->user()->id,
            'supervisor_acknowledged_at' => now(),
        ]);

        $case->advanceStageTo('case_assignment_evidence');

        return redirect()->route('criminal-cases.workflow', $case->id)
            ->with('success', 'Occurrence Book acknowledged. Case advanced to Case Assignment & Evidence.');
    }

    public function assignmentForm(Request $request, $id)
    {
        $case = CriminalCase::with(['occurrenceBook', 'assignment', 'evidenceItems'])->findOrFail($id);

        if (!$case->occurrenceBook?->isComplete()) {
            return redirect()->route('criminal-cases.workflow.ob.form', $case->id)
                ->with('error', 'Complete and acknowledge the Occurrence Book before Case Assignment.');
        }

        $investigators = \App\Models\User::whereHas('group', function ($q) use ($case) {
            $q->whereHas('roles', fn ($r) => $r->where('name', 'Investigator'))
              ->where('institution_id', $case->institution_id);
        })->orderBy('name')->get();

        return view('cid.cases.assignment_form', compact('case', 'investigators'));
    }

    public function storeAssignment(Request $request, $id)
    {
        $case = CriminalCase::with('occurrenceBook')->findOrFail($id);

        if (!$case->occurrenceBook?->isComplete()) {
            abort(422, 'Complete and acknowledge the Occurrence Book before Case Assignment.');
        }

        $data = $request->validate([
            'assigned_investigator_id' => 'required|exists:users,id',
            'secondary_officers'       => 'nullable|string',
            'investigation_plan'       => 'nullable|string',
            'investigation_start_date' => 'required|date',
            'target_completion_date'   => 'nullable|date|after_or_equal:investigation_start_date',
        ]);

        $data['added_by'] = $request->user()->name ?? 'Staff';

        if ($case->assignment) {
            $case->assignment->update($data);
        } else {
            $case->assignment()->create($data);
        }

        $case->advanceStageTo('custody_court_scheduling');

        return redirect()->route('criminal-cases.workflow', $case->id)
            ->with('success', 'Case Assignment saved. Case advanced to Custody & Court Scheduling.');
    }

    public function evidenceIndex(Request $request, $id)
    {
        $case = CriminalCase::with('evidenceItems.custodyLogs')->findOrFail($id);

        return view('cid.cases.evidence_index', compact('case'));
    }

    public function storeEvidenceItem(Request $request, $id)
    {
        $case = CriminalCase::findOrFail($id);

        $data = $request->validate([
            'description'         => 'required|string|max:255',
            'evidence_type'       => 'required|in:physical,digital,documentary,biological',
            'collection_date'     => 'required|date',
            'collection_location' => 'nullable|string|max:255',
            'collected_by'        => 'required|string|max:150',
            'storage_location'    => 'nullable|string|max:150',
        ]);

        $data['added_by'] = $request->user()->name ?? 'Staff';
        $data['status']   = 'collected';

        $item = $case->evidenceItems()->create($data);

        $item->custodyLogs()->create([
            'to_officer'     => $data['collected_by'],
            'transferred_at' => now(),
            'reason'         => 'Initial collection',
        ]);

        return redirect()->route('criminal-cases.workflow.evidence.index', $case->id)
            ->with('success', 'Evidence item logged.');
    }

    public function updateEvidenceStatus(Request $request, $id, $itemId)
    {
        $case = CriminalCase::findOrFail($id);
        $item = $case->evidenceItems()->findOrFail($itemId);

        $data = $request->validate([
            'status' => 'required|in:' . implode(',', \App\Models\CriminalCaseEvidenceItem::STATUSES),
        ]);

        $item->update($data);

        return redirect()->route('criminal-cases.workflow.evidence.index', $case->id)
            ->with('success', 'Evidence status updated.');
    }

    public function transferEvidenceCustody(Request $request, $id, $itemId)
    {
        $case = CriminalCase::findOrFail($id);
        $item = $case->evidenceItems()->findOrFail($itemId);

        $data = $request->validate([
            'to_officer' => 'required|string|max:150',
            'reason'     => 'nullable|string|max:255',
        ]);

        $lastLog = $item->custodyLogs()->first();

        $item->custodyLogs()->create([
            'from_officer'   => $lastLog->to_officer ?? null,
            'to_officer'     => $data['to_officer'],
            'transferred_at' => now(),
            'reason'         => $data['reason'] ?? null,
        ]);

        return redirect()->route('criminal-cases.workflow.evidence.index', $case->id)
            ->with('success', 'Custody transfer logged.');
    }
}
