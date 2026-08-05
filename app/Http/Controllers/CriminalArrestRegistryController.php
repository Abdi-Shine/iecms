<?php

namespace App\Http\Controllers;

use App\Models\CriminalCaseArrest;
use Illuminate\Http\Request;

class CriminalArrestRegistryController extends Controller
{
    /**
     * Both read-only registries reuse Stage 1's criminal_case_arrests
     * data (same pattern as CriminalConclusionReportController) rather
     * than duplicating arrest records into new tables.
     */
    public function warrants(Request $request)
    {
        $query = CriminalCaseArrest::with('criminalCase')->where('arrest_type', 'with_warrant');

        if ($request->filled('status')) {
            $now = now()->toDateString();
            if ($request->status === 'active') {
                $query->where(fn ($q) => $q->whereNull('warrant_expiry_date')->orWhere('warrant_expiry_date', '>=', $now));
            } elseif ($request->status === 'expired') {
                $query->whereNotNull('warrant_expiry_date')->where('warrant_expiry_date', '<', $now);
            }
        }
        if ($request->filled('case')) {
            $query->whereHas('criminalCase', fn ($q) => $q->where('case_number', 'like', '%' . $request->case . '%'));
        }

        $arrests = $query->latest('arrest_date')->paginate(20)->withQueryString();

        return view('cid.cases.arrest-warrants-registry', compact('arrests'));
    }

    public function withoutWarrant(Request $request)
    {
        $query = CriminalCaseArrest::with(['criminalCase.legalProcessRequests'])->where('arrest_type', 'without_warrant');

        if ($request->filled('case')) {
            $query->whereHas('criminalCase', fn ($q) => $q->where('case_number', 'like', '%' . $request->case . '%'));
        }

        $arrests = $query->latest('arrest_date')->paginate(20)->withQueryString();

        return view('cid.cases.arrests-without-warrant-registry', compact('arrests'));
    }
}
