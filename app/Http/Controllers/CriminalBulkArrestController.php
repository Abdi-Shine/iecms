<?php

namespace App\Http\Controllers;

use App\Models\CriminalBulkArrestEvent;
use App\Models\CriminalCase;
use Illuminate\Http\Request;

class CriminalBulkArrestController extends Controller
{
    public function index(Request $request)
    {
        $events = CriminalBulkArrestEvent::withCount('members')->latest('event_date')->paginate(15)->withQueryString();

        return view('cid.cases.bulk-arrest-index', compact('events'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_name'           => 'required|string|max:150',
            'event_date'           => 'required|date',
            'location'             => 'nullable|string|max:255',
            'operation_reference'  => 'nullable|string|max:100',
            'commanding_officer'   => 'required|string|max:150',
        ]);

        $data['added_by'] = $request->user()->name ?? 'Staff';

        $event = CriminalBulkArrestEvent::create($data);

        return redirect()->route('cid-bulk-arrests.show', $event->id)->with('success', 'Bulk arrest event created.');
    }

    public function show(Request $request, $id)
    {
        $event = CriminalBulkArrestEvent::with(['members.assignedInvestigator', 'members.criminalCase'])->findOrFail($id);

        $investigators = \App\Models\User::whereHas('group', function ($q) {
            $q->whereHas('roles', fn ($r) => $r->where('name', 'Investigator'));
        })->orderBy('name')->get();

        return view('cid.cases.bulk-arrest-show', compact('event', 'investigators'));
    }

    public function addMember(Request $request, $id)
    {
        $event = CriminalBulkArrestEvent::findOrFail($id);

        $data = $request->validate([
            'arrestee_name'   => 'required|string|max:150',
            'alleged_offence' => 'required|string|max:255',
        ]);

        $data['added_by'] = $request->user()->name ?? 'Staff';

        $event->members()->create($data);

        return redirect()->route('cid-bulk-arrests.show', $event->id)->with('success', 'Arrestee added.');
    }

    public function assignInvestigator(Request $request, $id)
    {
        $event = CriminalBulkArrestEvent::findOrFail($id);

        $data = $request->validate([
            'member_ids'               => 'required|array',
            'member_ids.*'             => 'exists:criminal_bulk_arrest_members,id',
            'assigned_investigator_id' => 'required|exists:users,id',
        ]);

        $event->members()->whereIn('id', $data['member_ids'])
            ->update(['assigned_investigator_id' => $data['assigned_investigator_id']]);

        return redirect()->route('cid-bulk-arrests.show', $event->id)->with('success', 'Investigator assigned to selected arrestees.');
    }

    public function generateCase(Request $request, $id, $memberId)
    {
        $event = CriminalBulkArrestEvent::findOrFail($id);
        $member = $event->members()->findOrFail($memberId);

        if ($member->criminal_case_id) {
            return back()->withErrors(['member' => 'A case has already been generated for this arrestee.']);
        }

        $case = CriminalCase::create([
            'priority' => 'Urgent',
            'added_by' => $request->user()->name ?? 'Staff',
        ]);

        $case->arrest()->create([
            'arrest_type'                      => 'without_warrant',
            'arrestee_name'                     => $member->arrestee_name,
            'arresting_officer_name'            => $event->commanding_officer,
            'arrest_date'                       => $event->event_date,
            'arrest_location'                   => $event->location,
            'alleged_offence'                   => $member->alleged_offence,
            'warrantless_justification'         => 'Bulk arrest operation: ' . $event->event_name . ($event->operation_reference ? ' (' . $event->operation_reference . ')' : ''),
            'added_by'                           => $request->user()->name ?? 'Staff',
        ]);

        $case->occurrenceBook()->create([
            'ob_datetime'        => now(),
            'reporting_officer'  => $event->commanding_officer,
            'offence_nature'     => $member->alleged_offence,
            'priority'           => 'Urgent',
            'assigned_investigator_id' => $member->assigned_investigator_id,
            'added_by'           => $request->user()->name ?? 'Staff',
        ]);

        $member->update(['criminal_case_id' => $case->id]);

        return redirect()->route('cid-bulk-arrests.show', $event->id)
            ->with('success', 'Case ' . $case->case_number . ' generated for ' . $member->arrestee_name . '.');
    }
}
