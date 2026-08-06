<?php

namespace App\Http\Controllers;

use App\Models\CivilCaseEnforcement;
use App\Models\DistricCivilRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class EnforcementController extends Controller
{
    public function index(Request $request)
    {
        $query = DistricCivilRegistration::with(['court', 'enforcement', 'judgments.receipts'])->orderByDesc('CRID');

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
            'total'     => DistricCivilRegistration::count(),
            'oodista'   => DistricCivilRegistration::where('Status', 'Oodista')->count(),
            'fulinta'   => DistricCivilRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'    => DistricCivilRegistration::where('Status', 'Closed')->count(),
        ];

        $civilSubCases = \App\Models\CaseCategory::where('case_name', 'Madani')->pluck('sub_case');

        return view('distract_courts.District_civil.registration.district_civil_view_Enforcement', compact('records', 'statuses', 'stats', 'civilSubCases'));
    }

    public function form($caseId)
    {
        $case        = DistricCivilRegistration::with(['court', 'assignments.employee'])->findOrFail($caseId);
        $enforcement = CivilCaseEnforcement::where('civil_case_id', $caseId)->latest()->first();
        $judge       = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? 'â€”';
        $caseStatus  = StatusProcess::where('name', 'Dhaqan Gal')->get();
        $defaultStatus = 'Dhaqan Gal';

        return view('distract_courts.District_civil.registration.district_civil_add_Enforcement',
            compact('case', 'enforcement', 'judge', 'caseStatus', 'defaultStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'civil_case_id'    => 'required|exists:distric_civil_registrations,CRID',
            'enforcement_date' => 'nullable|date',
            'enforcement_type' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_civil_uploads/enforcement-attachments', 'public');
        }

        $caseId  = $request->input('civil_case_id');
        $isDraft = $request->input('status') === 'Draft';

        CivilCaseEnforcement::updateOrCreate(
            ['civil_case_id' => $caseId],
            [
                'enforcement_type' => $request->input('enforcement_type'),
                'enforcement_date' => $request->input('enforcement_date'),
                'additional_notes' => $request->input('additional_notes'),
                'attachment'       => $attachment ?? CivilCaseEnforcement::where('civil_case_id', $caseId)->value('attachment'),
                'status'           => $request->input('status'),
                'created_by'       => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            DistricCivilRegistration::where('CRID', $caseId)->update(['Status' => 'Dhaqan Gal']);
        }

        return $isDraft
            ? redirect()->route('enforcement.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('enforcement.index')->with('success', 'Dhaqan Galka si guul leh ayaa loo gudbiyay.');
    }

    public function document($caseId)
    {
        $case        = DistricCivilRegistration::with(['court', 'parties', 'lawyers.lawyer', 'assignments.employee'])->findOrFail($caseId);
        $enforcement = CivilCaseEnforcement::where('civil_case_id', $caseId)->latest()->firstOrFail();
        $court       = $case->court;

        return view('distract_courts.District_civil.registration.district_civil_document_Enforcement', compact('case', 'enforcement', 'court'));
    }
}

