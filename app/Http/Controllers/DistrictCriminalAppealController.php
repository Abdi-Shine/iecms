<?php

namespace App\Http\Controllers;

use App\Models\DistrictCriminalAppeal;
use App\Models\DistrictCriminalRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class DistrictCriminalAppealController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictCriminalRegistration::with(['court', 'appeal', 'judgments.receipts'])->orderByDesc('CMID');

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
            'total'    => DistrictCriminalRegistration::count(),
            'rafcaan'  => DistrictCriminalRegistration::where('Status', 'Rafcaan')->count(),
            'dhaqanGal'=> DistrictCriminalRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'   => DistrictCriminalRegistration::where('Status', 'Closed')->count(),
        ];

        return view('distract_courts.District_criminal.registration.district_criminal_view_appeal', compact('records', 'statuses', 'stats'));
    }

    public function form($caseId)
    {
        $case        = DistrictCriminalRegistration::with(['court', 'assignments.employee', 'judgments.receipts'])->findOrFail($caseId);
        $appeal      = DistrictCriminalAppeal::where('criminal_case_id', $caseId)->latest()->first();
        $judge       = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus  = StatusProcess::where('name', 'Rafcaan')->get();
        $defaultStatus = 'Rafcaan';

        $judgment    = $case->judgments->sortByDesc('created_at')->first();
        $receipts    = $judgment?->receipts ?? collect();
        $lagaParties = $receipts->filter(fn($rc) => str_contains(strtolower($rc->judgment_outcome ?? ''), 'laga'))->values();

        return view('distract_courts.District_criminal.registration.district_criminal_add_appeal',
            compact('case', 'appeal', 'judge', 'caseStatus', 'defaultStatus', 'lagaParties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'criminal_case_id'   => 'required|exists:district_criminal_registrations,CMID',
            'appeal_date'      => 'nullable|date',
            'appeal_type'      => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_criminal_uploads/appeal-attachments', 'public');
        }

        $caseId  = $request->input('criminal_case_id');
        $isDraft = $request->input('status') === 'Draft';

        DistrictCriminalAppeal::updateOrCreate(
            ['criminal_case_id' => $caseId],
            [
                'appeal_type'       => $request->input('appeal_type'),
                'appeal_date'       => $request->input('appeal_date'),
                'appealing_parties' => $request->input('appealing_parties', []),
                'additional_notes'  => $request->input('additional_notes'),
                'attachment'        => $attachment ?? DistrictCriminalAppeal::where('criminal_case_id', $caseId)->value('attachment'),
                'status'            => $request->input('status'),
                'created_by'        => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            DistrictCriminalRegistration::where('CMID', $caseId)->update(['Status' => 'Rafcaan']);
        }

        return $isDraft
            ? redirect()->route('criminal-case-appeal.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('criminal-case-appeal.index')->with('success', 'Rafcaanka si guul leh ayaa loo gudbiyay.');
    }
}
