<?php

namespace App\Http\Controllers;

use App\Models\CivilCaseAppeal;
use App\Models\DistricCivilRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class AppealController extends Controller
{
    public function index(Request $request)
    {
        $query = DistricCivilRegistration::with(['court', 'appeal', 'judgments.receipts'])->orderByDesc('CRID');

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
            'total'    => DistricCivilRegistration::count(),
            'rafcaan'  => DistricCivilRegistration::where('Status', 'Rafcaan')->count(),
            'dhaqanGal'=> DistricCivilRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'   => DistricCivilRegistration::where('Status', 'Closed')->count(),
        ];

        return view('Courts.District_civil.registration.district_civil_view_appeal', compact('records', 'statuses', 'stats'));
    }

    public function form($caseId)
    {
        $case        = DistricCivilRegistration::with(['court', 'assignments.employee', 'judgments.receipts'])->findOrFail($caseId);
        $appeal      = CivilCaseAppeal::where('civil_case_id', $caseId)->latest()->first();
        $judge       = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? 'â€”';
        $caseStatus  = StatusProcess::where('name', 'Rafcaan')->get();
        $defaultStatus = 'Rafcaan';

        $judgment    = $case->judgments->sortByDesc('created_at')->first();
        $receipts    = $judgment?->receipts ?? collect();
        $lagaParties = $receipts->filter(fn($rc) => str_contains(strtolower($rc->judgment_outcome ?? ''), 'laga'))->values();

        return view('Courts.District_civil.registration.district_civil_add_appeal',
            compact('case', 'appeal', 'judge', 'caseStatus', 'defaultStatus', 'lagaParties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'civil_case_id'    => 'required|exists:distric_civil_registrations,CRID',
            'appeal_date'      => 'nullable|date',
            'appeal_type'      => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_civil_uploads/appeal-attachments', 'public');
        }

        $caseId  = $request->input('civil_case_id');
        $isDraft = $request->input('status') === 'Draft';

        CivilCaseAppeal::updateOrCreate(
            ['civil_case_id' => $caseId],
            [
                'appeal_type'       => $request->input('appeal_type'),
                'appeal_date'       => $request->input('appeal_date'),
                'appealing_parties' => $request->input('appealing_parties', []),
                'additional_notes'  => $request->input('additional_notes'),
                'attachment'        => $attachment ?? CivilCaseAppeal::where('civil_case_id', $caseId)->value('attachment'),
                'status'            => $request->input('status'),
                'created_by'        => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            DistricCivilRegistration::where('CRID', $caseId)->update(['Status' => 'Rafcaan']);
        }

        return $isDraft
            ? redirect()->route('appeal.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('appeal.index')->with('success', 'Rafcaanka si guul leh ayaa loo gudbiyay.');
    }
}

