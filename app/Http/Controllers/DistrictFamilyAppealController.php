<?php

namespace App\Http\Controllers;

use App\Models\DistrictFamilyAppeal;
use App\Models\DistrictFamilyRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class DistrictFamilyAppealController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictFamilyRegistration::with(['court', 'appeal', 'judgments.receipts'])->orderByDesc('FCID');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('FileNo', 'like', "%$s%")
                  ->orWhere('CaseType', 'like', "%$s%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('Status', $request->status);
        }

        $perPage = $this->resolvePerPage($request);

        $records  = $query->paginate($perPage)->withQueryString();
        $statuses = StatusProcess::orderBy('name')->get();

        $stats = [
            'total'    => DistrictFamilyRegistration::count(),
            'rafcaan'  => DistrictFamilyRegistration::where('Status', 'Rafcaan')->count(),
            'dhaqanGal'=> DistrictFamilyRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'   => DistrictFamilyRegistration::where('Status', 'Closed')->count(),
        ];

        return view('Courts.District_family.registration.district_family_view_appeal', compact('records', 'statuses', 'stats'));
    }

    public function form($caseId)
    {
        $case        = DistrictFamilyRegistration::with(['court', 'assignments.employee', 'judgments.receipts'])->findOrFail($caseId);
        $appeal      = DistrictFamilyAppeal::where('family_case_id', $caseId)->latest()->first();
        $judge       = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus  = StatusProcess::where('name', 'Rafcaan')->get();
        $defaultStatus = 'Rafcaan';

        $judgment    = $case->judgments->sortByDesc('created_at')->first();
        $receipts    = $judgment?->receipts ?? collect();
        $lagaParties = $receipts->filter(fn($rc) => str_contains(strtolower($rc->judgment_outcome ?? ''), 'laga'))->values();

        return view('Courts.District_family.registration.district_family_add_appeal',
            compact('case', 'appeal', 'judge', 'caseStatus', 'defaultStatus', 'lagaParties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'family_case_id'   => 'required|exists:district_family_registrations,FCID',
            'appeal_date'      => 'nullable|date',
            'appeal_type'      => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_family_uploads/appeal-attachments', 'public');
        }

        $caseId  = $request->input('family_case_id');
        $isDraft = $request->input('status') === 'Draft';

        DistrictFamilyAppeal::updateOrCreate(
            ['family_case_id' => $caseId],
            [
                'appeal_type'       => $request->input('appeal_type'),
                'appeal_date'       => $request->input('appeal_date'),
                'appealing_parties' => $request->input('appealing_parties', []),
                'additional_notes'  => $request->input('additional_notes'),
                'attachment'        => $attachment ?? DistrictFamilyAppeal::where('family_case_id', $caseId)->value('attachment'),
                'status'            => $request->input('status'),
                'created_by'        => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            DistrictFamilyRegistration::where('FCID', $caseId)->update(['Status' => 'Rafcaan']);
        }

        return $isDraft
            ? redirect()->route('family-case-appeal.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('family-case-appeal.index')->with('success', 'Rafcaanka si guul leh ayaa loo gudbiyay.');
    }
}
