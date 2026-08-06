<?php

namespace App\Http\Controllers;

use App\Models\AppealCriminalEnforcement;
use App\Models\AppealCriminalRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class AppealCriminalEnforcementController extends Controller
{
    public function index(Request $request)
    {
        $query = AppealCriminalRegistration::with(['court', 'enforcement', 'judgments.receipts'])->orderByDesc('ACMID');

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
            'total'   => AppealCriminalRegistration::count(),
            'oodista' => AppealCriminalRegistration::where('Status', 'Oodista')->count(),
            'fulinta' => AppealCriminalRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'  => AppealCriminalRegistration::where('Status', 'Closed')->count(),
        ];

        return view('appeal_court.Appeal_criminal.Conclusion.appeal_criminal_view_Enforcement', compact('records', 'statuses', 'stats'));
    }

    public function form($caseId)
    {
        $case          = AppealCriminalRegistration::with(['court', 'assignments.employee'])->findOrFail($caseId);
        $enforcement   = AppealCriminalEnforcement::where('criminal_case_id', $caseId)->latest()->first();
        $judge         = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus    = StatusProcess::where('name', 'Dhaqan Gal')->get();
        $defaultStatus = 'Dhaqan Gal';

        return view('appeal_court.Appeal_criminal.Conclusion.appeal_criminal_add_Enforcement',
            compact('case', 'enforcement', 'judge', 'caseStatus', 'defaultStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'criminal_case_id' => 'required|exists:appeal_criminal_registrations,ACMID',
            'enforcement_date' => 'nullable|date',
            'enforcement_type' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('appeal_criminal_uploads/enforcement-attachments', 'public');
        }

        $caseId  = $request->input('criminal_case_id');
        $isDraft = $request->input('status') === 'Draft';

        AppealCriminalEnforcement::updateOrCreate(
            ['criminal_case_id' => $caseId],
            [
                'enforcement_type' => $request->input('enforcement_type'),
                'enforcement_date' => $request->input('enforcement_date'),
                'additional_notes' => $request->input('additional_notes'),
                'attachment'       => $attachment ?? AppealCriminalEnforcement::where('criminal_case_id', $caseId)->value('attachment'),
                'status'           => $request->input('status'),
                'created_by'       => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            AppealCriminalRegistration::where('ACMID', $caseId)->update(['Status' => 'Dhaqan Gal']);
        }

        return $isDraft
            ? redirect()->route('appeal-criminal-enforcement.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('appeal-criminal-enforcement.index')->with('success', 'Dhaqan Galka si guul leh ayaa loo gudbiyay.');
    }

    public function document($caseId)
    {
        $case        = AppealCriminalRegistration::with(['court', 'parties', 'assignments.employee'])->findOrFail($caseId);
        $enforcement = AppealCriminalEnforcement::where('criminal_case_id', $caseId)->latest()->firstOrFail();
        $court       = $case->court;

        return view('appeal_court.Appeal_criminal.Conclusion.appeal_criminal_document_Enforcement', compact('case', 'enforcement', 'court'));
    }
}
