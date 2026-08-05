<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use Illuminate\Http\Request;

class CriminalInterviewController extends Controller
{
    private function assertInvestigatorOrAdmin(Request $request): void
    {
        $roleNames = $request->user()->group?->roles->pluck('name') ?? collect();
        if (!$roleNames->intersect(['CID Institution Admin', 'Investigator'])->count()) {
            abort(403, 'Interview content is restricted to Investigator and Institution Admin roles.');
        }
    }

    public function index(Request $request, $id)
    {
        $this->assertInvestigatorOrAdmin($request);

        $case = CriminalCase::with('interviews.signedOffBy')->findOrFail($id);

        return view('cid.cases.interviews', compact('case'));
    }

    public function store(Request $request, $id)
    {
        $this->assertInvestigatorOrAdmin($request);

        $case = CriminalCase::findOrFail($id);

        $data = $request->validate([
            'interviewee_name'       => 'required|string|max:150',
            'interviewee_id'         => 'nullable|string|max:100',
            'interviewee_role'       => 'required|in:' . implode(',', \App\Models\CriminalCaseInterview::ROLES),
            'interview_date'         => 'required|date',
            'interview_time'         => 'nullable',
            'interview_location'     => 'nullable|string|max:255',
            'interviewing_officer'   => 'required|string|max:150',
            'second_officer_witness' => 'nullable|string|max:150',
            'format'                 => 'required|in:' . implode(',', \App\Models\CriminalCaseInterview::FORMATS),
            'statement_summary'      => 'nullable|string',
        ]);

        $data['added_by'] = $request->user()->name ?? 'Staff';

        $case->interviews()->create($data);

        return redirect()->route('criminal-cases.interviews.index', $case->id)
            ->with('success', 'Interview recorded.');
    }

    public function signOff(Request $request, $id, $interviewId)
    {
        $this->assertInvestigatorOrAdmin($request);

        $case = CriminalCase::findOrFail($id);
        $interview = $case->interviews()->findOrFail($interviewId);

        $interview->update([
            'signed_off_by' => $request->user()->id,
            'signed_off_at' => now(),
        ]);

        return redirect()->route('criminal-cases.interviews.index', $case->id)
            ->with('success', 'Statement signed off.');
    }
}
