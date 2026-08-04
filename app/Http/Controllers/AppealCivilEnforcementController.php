<?php

namespace App\Http\Controllers;

use App\Models\AppealCivilEnforcement;
use App\Models\AppealCivilRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class AppealCivilEnforcementController extends Controller
{
    public function index(Request $request)
    {
        $query = AppealCivilRegistration::with(['court', 'enforcement', 'judgments.receipts'])->orderByDesc('ACID');

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
            'total'   => AppealCivilRegistration::count(),
            'oodista' => AppealCivilRegistration::where('Status', 'Oodista')->count(),
            'fulinta' => AppealCivilRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'  => AppealCivilRegistration::where('Status', 'Closed')->count(),
        ];

        return view('Courts.Appeal_civil.registration.appeal_civil_view_Enforcement', compact('records', 'statuses', 'stats'));
    }

    public function form($caseId)
    {
        $case          = AppealCivilRegistration::with(['court', 'assignments.employee'])->findOrFail($caseId);
        $enforcement   = AppealCivilEnforcement::where('civil_case_id', $caseId)->latest()->first();
        $judge         = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus    = StatusProcess::where('name', 'Dhaqan Gal')->get();
        $defaultStatus = 'Dhaqan Gal';

        return view('Courts.Appeal_civil.registration.appeal_civil_add_Enforcement',
            compact('case', 'enforcement', 'judge', 'caseStatus', 'defaultStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'civil_case_id'    => 'required|exists:appeal_civil_registrations,ACID',
            'enforcement_date' => 'nullable|date',
            'enforcement_type' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('appeal_civil_uploads/enforcement-attachments', 'public');
        }

        $caseId  = $request->input('civil_case_id');
        $isDraft = $request->input('status') === 'Draft';

        AppealCivilEnforcement::updateOrCreate(
            ['civil_case_id' => $caseId],
            [
                'enforcement_type' => $request->input('enforcement_type'),
                'enforcement_date' => $request->input('enforcement_date'),
                'additional_notes' => $request->input('additional_notes'),
                'attachment'       => $attachment ?? AppealCivilEnforcement::where('civil_case_id', $caseId)->value('attachment'),
                'status'           => $request->input('status'),
                'created_by'       => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            AppealCivilRegistration::where('ACID', $caseId)->update(['Status' => 'Dhaqan Gal']);
        }

        return $isDraft
            ? redirect()->route('appeal-enforcement.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('appeal-enforcement.index')->with('success', 'Dhaqan Galka si guul leh ayaa loo gudbiyay.');
    }

    public function document($caseId)
    {
        $case        = AppealCivilRegistration::with(['court', 'parties', 'assignments.employee'])->findOrFail($caseId);
        $enforcement = AppealCivilEnforcement::where('civil_case_id', $caseId)->latest()->firstOrFail();
        $court       = $case->court;

        return view('Courts.Appeal_civil.registration.appeal_civil_document_Enforcement', compact('case', 'enforcement', 'court'));
    }
}
