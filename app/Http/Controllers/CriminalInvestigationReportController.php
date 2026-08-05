<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use Illuminate\Http\Request;

class CriminalInvestigationReportController extends Controller
{
    public function index(Request $request, $id)
    {
        $case = CriminalCase::with('investigationReports.author', 'investigationReports.reviewer')->findOrFail($id);

        return view('cid.cases.investigation-reports', compact('case'));
    }

    public function store(Request $request, $id)
    {
        $case = CriminalCase::findOrFail($id);

        $data = $request->validate([
            'report_type' => 'required|in:' . implode(',', \App\Models\CriminalCaseInvestigationReport::TYPES),
            'content'     => 'required|string',
        ]);

        $data['author_id'] = $request->user()->id;
        $data['status']    = 'Draft';
        $data['added_by']  = $request->user()->name ?? 'Staff';

        $case->investigationReports()->create($data);

        return redirect()->route('criminal-cases.investigation-reports.index', $case->id)
            ->with('success', 'Report saved as draft.');
    }

    public function submitForReview(Request $request, $id, $reportId)
    {
        $case = CriminalCase::findOrFail($id);
        $report = $case->investigationReports()->findOrFail($reportId);

        if ($report->status !== 'Draft') {
            return back()->withErrors(['report' => 'Only draft reports can be submitted for review.']);
        }

        $report->update(['status' => 'Supervisor Review']);

        return redirect()->route('criminal-cases.investigation-reports.index', $case->id)
            ->with('success', 'Report submitted for supervisor review.');
    }

    public function approve(Request $request, $id, $reportId)
    {
        $case = CriminalCase::findOrFail($id);
        $report = $case->investigationReports()->findOrFail($reportId);

        $roleNames = $request->user()->group?->roles->pluck('name') ?? collect();
        if (!$roleNames->intersect(['CID Institution Admin', 'Investigator'])->count()) {
            abort(403, 'Only an Investigator or Institution Admin can approve a report.');
        }

        if ($report->status !== 'Supervisor Review') {
            return back()->withErrors(['report' => 'Only reports awaiting review can be approved.']);
        }

        $report->update([
            'status'      => 'Approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('criminal-cases.investigation-reports.index', $case->id)
            ->with('success', 'Report approved.');
    }

    public function submit(Request $request, $id, $reportId)
    {
        $case = CriminalCase::findOrFail($id);
        $report = $case->investigationReports()->findOrFail($reportId);

        if ($report->status !== 'Approved') {
            return back()->withErrors(['report' => 'Only approved reports can be marked submitted.']);
        }

        $report->update(['status' => 'Submitted']);

        return redirect()->route('criminal-cases.investigation-reports.index', $case->id)
            ->with('success', 'Report marked as submitted.');
    }
}
