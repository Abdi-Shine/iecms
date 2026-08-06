<?php

namespace App\Http\Controllers;

use App\Models\DistrictExecutionAppeal;
use App\Models\DistrictExecutionRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class DistrictExecutionAppealController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictExecutionRegistration::with(['court', 'appeal', 'judgments.receipts'])->orderByDesc('ECID');

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
            'total'    => DistrictExecutionRegistration::count(),
            'rafcaan'  => DistrictExecutionRegistration::where('Status', 'Rafcaan')->count(),
            'dhaqanGal'=> DistrictExecutionRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'   => DistrictExecutionRegistration::where('Status', 'Closed')->count(),
        ];

        return view('distract_courts.District_execution.registration.district_execution_view_appeal', compact('records', 'statuses', 'stats'));
    }

    public function form($caseId)
    {
        $case        = DistrictExecutionRegistration::with(['court', 'assignments.employee', 'judgments.receipts'])->findOrFail($caseId);
        $appeal      = DistrictExecutionAppeal::where('execution_case_id', $caseId)->latest()->first();
        $judge       = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus  = StatusProcess::where('name', 'Rafcaan')->get();
        $defaultStatus = 'Rafcaan';

        $judgment    = $case->judgments->sortByDesc('created_at')->first();
        $receipts    = $judgment?->receipts ?? collect();
        $lagaParties = $receipts->filter(fn($rc) => str_contains(strtolower($rc->judgment_outcome ?? ''), 'laga'))->values();

        return view('distract_courts.District_execution.registration.district_execution_add_appeal',
            compact('case', 'appeal', 'judge', 'caseStatus', 'defaultStatus', 'lagaParties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'execution_case_id'   => 'required|exists:district_execution_registrations,ECID',
            'appeal_date'      => 'nullable|date',
            'appeal_type'      => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_execution_uploads/appeal-attachments', 'public');
        }

        $caseId  = $request->input('execution_case_id');
        $isDraft = $request->input('status') === 'Draft';

        DistrictExecutionAppeal::updateOrCreate(
            ['execution_case_id' => $caseId],
            [
                'appeal_type'       => $request->input('appeal_type'),
                'appeal_date'       => $request->input('appeal_date'),
                'appealing_parties' => $request->input('appealing_parties', []),
                'additional_notes'  => $request->input('additional_notes'),
                'attachment'        => $attachment ?? DistrictExecutionAppeal::where('execution_case_id', $caseId)->value('attachment'),
                'status'            => $request->input('status'),
                'created_by'        => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            DistrictExecutionRegistration::where('ECID', $caseId)->update(['Status' => 'Rafcaan']);
        }

        return $isDraft
            ? redirect()->route('execution-case-appeal.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('execution-case-appeal.index')->with('success', 'Rafcaanka si guul leh ayaa loo gudbiyay.');
    }
}
