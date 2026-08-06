<?php

namespace App\Http\Controllers;

use App\Models\DistrictFamilyEnforcement;
use App\Models\DistrictFamilyRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class DistrictFamilyEnforcementController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictFamilyRegistration::with(['court', 'enforcement', 'judgments.receipts'])->orderByDesc('FCID');

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
            'total'     => DistrictFamilyRegistration::count(),
            'oodista'   => DistrictFamilyRegistration::where('Status', 'Oodista')->count(),
            'fulinta'   => DistrictFamilyRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'    => DistrictFamilyRegistration::where('Status', 'Closed')->count(),
        ];

        $familySubCases = \App\Models\CaseCategory::where('case_name', 'Qoyska')->pluck('sub_case');

        return view('distract_courts.District_family.registration.district_family_view_Enforcement', compact('records', 'statuses', 'stats', 'familySubCases'));
    }

    public function form($caseId)
    {
        $case        = DistrictFamilyRegistration::with(['court', 'assignments.employee'])->findOrFail($caseId);
        $enforcement = DistrictFamilyEnforcement::where('family_case_id', $caseId)->latest()->first();
        $judge       = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus  = StatusProcess::where('name', 'Dhaqan Gal')->get();
        $defaultStatus = 'Dhaqan Gal';

        return view('distract_courts.District_family.registration.district_family_add_Enforcement',
            compact('case', 'enforcement', 'judge', 'caseStatus', 'defaultStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'family_case_id'   => 'required|exists:district_family_registrations,FCID',
            'enforcement_date' => 'nullable|date',
            'enforcement_type' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_family_uploads/enforcement-attachments', 'public');
        }

        $caseId  = $request->input('family_case_id');
        $isDraft = $request->input('status') === 'Draft';

        DistrictFamilyEnforcement::updateOrCreate(
            ['family_case_id' => $caseId],
            [
                'enforcement_type' => $request->input('enforcement_type'),
                'enforcement_date' => $request->input('enforcement_date'),
                'additional_notes' => $request->input('additional_notes'),
                'attachment'       => $attachment ?? DistrictFamilyEnforcement::where('family_case_id', $caseId)->value('attachment'),
                'status'           => $request->input('status'),
                'created_by'       => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            DistrictFamilyRegistration::where('FCID', $caseId)->update(['Status' => 'Dhaqan Gal']);
        }

        return $isDraft
            ? redirect()->route('family-enforcement.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('family-enforcement.index')->with('success', 'Dhaqan Galka si guul leh ayaa loo gudbiyay.');
    }

    public function document($caseId)
    {
        $case        = DistrictFamilyRegistration::with(['court', 'parties', 'lawyers.lawyer', 'assignments.employee'])->findOrFail($caseId);
        $enforcement = DistrictFamilyEnforcement::where('family_case_id', $caseId)->latest()->firstOrFail();
        $court       = $case->court;

        return view('distract_courts.District_family.registration.district_family_document_Enforcement', compact('case', 'enforcement', 'court'));
    }
}
