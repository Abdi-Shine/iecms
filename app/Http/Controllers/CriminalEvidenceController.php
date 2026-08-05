<?php

namespace App\Http\Controllers;

use App\Models\CriminalCaseEvidenceItem;
use Illuminate\Http\Request;

class CriminalEvidenceController extends Controller
{
    public function index(Request $request)
    {
        $query = CriminalCaseEvidenceItem::with(['criminalCase', 'custodyLogs']);

        if ($request->filled('evidence_id')) {
            $query->where('id', $request->evidence_id);
        }
        if ($request->filled('case')) {
            $query->whereHas('criminalCase', fn ($q) => $q->where('case_number', 'like', '%' . $request->case . '%'));
        }
        if ($request->filled('type')) {
            $query->where('evidence_type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('officer')) {
            $query->where('collected_by', 'like', '%' . $request->officer . '%');
        }
        if ($request->filled('from')) {
            $query->whereDate('collection_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('collection_date', '<=', $request->to);
        }

        $items = $query->latest('collection_date')->paginate(20)->withQueryString();

        return view('cid.cases.evidence-registry', compact('items'));
    }
}
