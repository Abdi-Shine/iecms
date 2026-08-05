<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use Illuminate\Http\Request;

class CriminalCaseTakeoverController extends Controller
{
    public function index(Request $request, $id)
    {
        $case = CriminalCase::with([
            'takeovers.outgoingInvestigator', 'takeovers.incomingInvestigator', 'takeovers.adminApprover',
            'assignment.assignedInvestigator',
        ])->findOrFail($id);

        $investigators = \App\Models\User::whereHas('group', function ($q) use ($case) {
            $q->whereHas('roles', fn ($r) => $r->where('name', 'Investigator'))
              ->where('institution_id', $case->institution_id);
        })->orderBy('name')->get();

        return view('cid.cases.takeovers', compact('case', 'investigators'));
    }

    public function store(Request $request, $id)
    {
        $case = CriminalCase::with('assignment')->findOrFail($id);

        $data = $request->validate([
            'reason'                    => 'required|string',
            'incoming_investigator_id'  => 'required|exists:users,id|different:outgoing_investigator_id',
        ]);

        $data['outgoing_investigator_id'] = $case->assignment?->assigned_investigator_id;
        $data['added_by'] = $request->user()->name ?? 'Staff';

        $case->takeovers()->create($data);

        return redirect()->route('criminal-cases.takeovers', $case->id)
            ->with('success', 'Takeover requested. Awaiting outgoing officer acknowledgment.');
    }

    public function acknowledgeOutgoing(Request $request, $id, $takeoverId)
    {
        $case = CriminalCase::findOrFail($id);
        $takeover = $case->takeovers()->findOrFail($takeoverId);

        $isAdmin = $request->user()->group?->roles->contains('name', 'CID Institution Admin') ?? false;
        if ($request->user()->id !== $takeover->outgoing_investigator_id && !$isAdmin) {
            abort(403, 'Only the outgoing investigator can acknowledge this takeover.');
        }

        $takeover->update(['outgoing_acknowledged_at' => now()]);

        return redirect()->route('criminal-cases.takeovers', $case->id)
            ->with('success', 'Outgoing acknowledgment recorded.');
    }

    public function acceptIncoming(Request $request, $id, $takeoverId)
    {
        $case = CriminalCase::findOrFail($id);
        $takeover = $case->takeovers()->findOrFail($takeoverId);

        $isAdmin = $request->user()->group?->roles->contains('name', 'CID Institution Admin') ?? false;
        if ($request->user()->id !== $takeover->incoming_investigator_id && !$isAdmin) {
            abort(403, 'Only the incoming investigator can accept this takeover.');
        }

        if (!$takeover->outgoing_acknowledged_at) {
            return back()->withErrors(['takeover' => 'The outgoing officer must acknowledge before the incoming officer can accept.']);
        }

        $takeover->update(['incoming_accepted_at' => now()]);

        return redirect()->route('criminal-cases.takeovers', $case->id)
            ->with('success', 'Incoming acceptance recorded. Awaiting admin approval.');
    }

    public function approve(Request $request, $id, $takeoverId)
    {
        $case = CriminalCase::with(['assignment', 'occurrenceBook'])->findOrFail($id);
        $takeover = $case->takeovers()->findOrFail($takeoverId);

        $isAdmin = $request->user()->group?->roles->contains('name', 'CID Institution Admin') ?? false;
        if (!$isAdmin) {
            abort(403, 'Only an Institution Admin can approve a takeover.');
        }

        if (!$takeover->outgoing_acknowledged_at || !$takeover->incoming_accepted_at) {
            return back()->withErrors(['takeover' => 'Both outgoing acknowledgment and incoming acceptance are required before approval.']);
        }

        $takeover->update([
            'admin_approved_by' => $request->user()->id,
            'admin_approved_at' => now(),
        ]);

        $case->assignment?->update(['assigned_investigator_id' => $takeover->incoming_investigator_id]);
        $case->occurrenceBook?->update(['assigned_investigator_id' => $takeover->incoming_investigator_id]);

        $case->diaryEntries()->create([
            'entry_type'  => 'system',
            'action_type' => 'Investigation Takeover Approved',
            'description' => 'Case reassigned to ' . $takeover->incomingInvestigator->name,
            'user_id'     => $request->user()->id,
        ]);

        return redirect()->route('criminal-cases.takeovers', $case->id)
            ->with('success', 'Takeover approved. Case reassigned to ' . $takeover->incomingInvestigator->name . '.');
    }

    public function reject(Request $request, $id, $takeoverId)
    {
        $case = CriminalCase::findOrFail($id);
        $takeover = $case->takeovers()->findOrFail($takeoverId);

        $isAdmin = $request->user()->group?->roles->contains('name', 'CID Institution Admin') ?? false;
        if (!$isAdmin) {
            abort(403, 'Only an Institution Admin can reject a takeover.');
        }

        $takeover->update(['rejected_at' => now()]);

        return redirect()->route('criminal-cases.takeovers', $case->id)
            ->with('success', 'Takeover request rejected.');
    }
}
