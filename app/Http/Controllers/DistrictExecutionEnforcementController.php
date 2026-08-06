<?php

namespace App\Http\Controllers;

use App\Models\DistrictExecutionEnforcement;
use App\Models\DistrictExecutionRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class DistrictExecutionEnforcementController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictExecutionRegistration::with(['court', 'enforcement', 'judgments.receipts'])->orderByDesc('ECID');

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

        if ($request->filled('sub_case')) {
            $query->where('SubCase', $request->sub_case);
        }

        $perPage = $this->resolvePerPage($request);

        $records  = $query->paginate($perPage)->withQueryString();
        $statuses = StatusProcess::orderBy('name')->get();

        $stats = [
            'total'     => DistrictExecutionRegistration::count(),
            'oodista'   => DistrictExecutionRegistration::where('Status', 'Oodista')->count(),
            'fulinta'   => DistrictExecutionRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'    => DistrictExecutionRegistration::where('Status', 'Closed')->count(),
        ];

        $executionSubCases = \App\Models\CaseCategory::where('case_name', 'Fulinta')->pluck('sub_case');

        return view('distract_courts.District_execution.registration.district_execution_view_Enforcement', compact('records', 'statuses', 'stats', 'executionSubCases'));
    }

    public function form($caseId)
    {
        $case        = DistrictExecutionRegistration::with(['court', 'assignments.employee'])->findOrFail($caseId);
        $enforcement = DistrictExecutionEnforcement::where('execution_case_id', $caseId)->latest()->first();
        $judge       = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus  = StatusProcess::where('name', 'Dhaqan Gal')->get();
        $defaultStatus = 'Dhaqan Gal';

        return view('distract_courts.District_execution.registration.district_execution_add_Enforcement',
            compact('case', 'enforcement', 'judge', 'caseStatus', 'defaultStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'execution_case_id'   => 'required|exists:district_execution_registrations,ECID',
            'enforcement_date' => 'nullable|date',
            'enforcement_type' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_execution_uploads/enforcement-attachments', 'public');
        }

        $caseId  = $request->input('execution_case_id');
        $isDraft = $request->input('status') === 'Draft';

        DistrictExecutionEnforcement::updateOrCreate(
            ['execution_case_id' => $caseId],
            [
                'enforcement_type' => $request->input('enforcement_type'),
                'enforcement_date' => $request->input('enforcement_date'),
                'additional_notes' => $request->input('additional_notes'),
                'attachment'       => $attachment ?? DistrictExecutionEnforcement::where('execution_case_id', $caseId)->value('attachment'),
                'status'           => $request->input('status'),
                'created_by'       => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            DistrictExecutionRegistration::where('ECID', $caseId)->update(['Status' => 'Dhaqan Gal']);
        }

        return $isDraft
            ? redirect()->route('execution-enforcement.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('execution-enforcement.index')->with('success', 'Dhaqan Galka si guul leh ayaa loo gudbiyay.');
    }

    public function document($caseId)
    {
        $case        = DistrictExecutionRegistration::with(['court', 'parties', 'lawyers.lawyer', 'assignments.employee'])->findOrFail($caseId);
        $enforcement = DistrictExecutionEnforcement::where('execution_case_id', $caseId)->latest()->firstOrFail();
        $court       = $case->court;

        return view('distract_courts.District_execution.registration.district_execution_document_Enforcement', compact('case', 'enforcement', 'court'));
    }
}
