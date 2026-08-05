<?php

namespace App\Http\Controllers;

use App\Models\CriminalCaseFinalReport;
use Illuminate\Http\Request;

class CriminalConclusionReportController extends Controller
{
    /**
     * Read-only cross-case registry over criminal_case_final_reports
     * (Stage 5's Final Investigation Report) rather than a separate
     * "Investigation Conclusion Report" table — the spec's conclusion
     * report and the Stage 5 final report are the same document
     * (case summary, evidence-backed recommendation, supervisor
     * endorsement, locked once submitted); building a second parallel
     * workflow would just duplicate it.
     */
    public function index(Request $request)
    {
        $query = CriminalCaseFinalReport::with(['criminalCase', 'supervisor']);

        if ($request->filled('recommendation')) {
            $query->where('recommendation', $request->recommendation);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $reports = $query->latest()->paginate(20)->withQueryString();

        return view('cid.cases.conclusion-reports', compact('reports'));
    }
}
