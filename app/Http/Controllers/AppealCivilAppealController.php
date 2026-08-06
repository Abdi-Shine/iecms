<?php

namespace App\Http\Controllers;

use App\Models\AppealCivilAppeal;
use App\Models\AppealCivilRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class AppealCivilAppealController extends Controller
{
    public function index(Request $request)
    {
        $query = AppealCivilRegistration::with(['court', 'appeal', 'judgments.receipts'])->orderByDesc('ACID');

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
            'total'     => AppealCivilRegistration::count(),
            'rafcaan'   => AppealCivilRegistration::where('Status', 'Rafcaan')->count(),
            'dhaqanGal' => AppealCivilRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'    => AppealCivilRegistration::where('Status', 'Closed')->count(),
        ];

        return view('appeal_court.Appeal_civil.registration.appeal_civil_view_appeal', compact('records', 'statuses', 'stats'));
    }

    public function form($caseId)
    {
        $case          = AppealCivilRegistration::with(['court', 'assignments.employee', 'judgments.receipts'])->findOrFail($caseId);
        $appeal        = AppealCivilAppeal::where('civil_case_id', $caseId)->latest()->first();
        $judge         = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus    = StatusProcess::where('name', 'Rafcaan')->get();
        $defaultStatus = 'Rafcaan';

        $judgment    = $case->judgments->sortByDesc('created_at')->first();
        $receipts    = $judgment?->receipts ?? collect();
        $lagaParties = $receipts->filter(fn($rc) => str_contains(strtolower($rc->judgment_outcome ?? ''), 'laga'))->values();

        return view('appeal_court.Appeal_civil.registration.appeal_civil_add_appeal',
            compact('case', 'appeal', 'judge', 'caseStatus', 'defaultStatus', 'lagaParties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'civil_case_id'    => 'required|exists:appeal_civil_registrations,ACID',
            'appeal_date'      => 'nullable|date',
            'appeal_type'      => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('appeal_civil_uploads/appeal-attachments', 'public');
        }

        $caseId  = $request->input('civil_case_id');
        $isDraft = $request->input('status') === 'Draft';

        AppealCivilAppeal::updateOrCreate(
            ['civil_case_id' => $caseId],
            [
                'appeal_type'       => $request->input('appeal_type'),
                'appeal_date'       => $request->input('appeal_date'),
                'appealing_parties' => $request->input('appealing_parties', []),
                'additional_notes'  => $request->input('additional_notes'),
                'attachment'        => $attachment ?? AppealCivilAppeal::where('civil_case_id', $caseId)->value('attachment'),
                'status'            => $request->input('status'),
                'created_by'        => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            AppealCivilRegistration::where('ACID', $caseId)->update(['Status' => 'Rafcaan']);
        }

        return $isDraft
            ? redirect()->route('appeal-civil-appeal.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('appeal-civil-appeal.index')->with('success', 'Rafcaanka si guul leh ayaa loo gudbiyay.');
    }
}
