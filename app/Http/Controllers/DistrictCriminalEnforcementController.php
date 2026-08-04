<?php

namespace App\Http\Controllers;

use App\Models\DistrictCriminalEnforcement;
use App\Models\DistrictCriminalRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class DistrictCriminalEnforcementController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictCriminalRegistration::with(['court', 'enforcement', 'judgments.receipts'])->orderByDesc('CMID');

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
            'total'     => DistrictCriminalRegistration::count(),
            'oodista'   => DistrictCriminalRegistration::where('Status', 'Oodista')->count(),
            'fulinta'   => DistrictCriminalRegistration::where('Status', 'Dhaqan Gal')->count(),
            'closed'    => DistrictCriminalRegistration::where('Status', 'Closed')->count(),
        ];

        $criminalSubCases = \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->pluck('sub_case');

        return view('Courts.District_criminal.registration.district_criminal_view_Enforcement', compact('records', 'statuses', 'stats', 'criminalSubCases'));
    }

    public function form($caseId)
    {
        $case        = DistrictCriminalRegistration::with(['court', 'assignments.employee'])->findOrFail($caseId);
        $enforcement = DistrictCriminalEnforcement::where('criminal_case_id', $caseId)->latest()->first();
        $judge       = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus  = StatusProcess::where('name', 'Dhaqan Gal')->get();
        $defaultStatus = 'Dhaqan Gal';

        return view('Courts.District_criminal.registration.district_criminal_add_Enforcement',
            compact('case', 'enforcement', 'judge', 'caseStatus', 'defaultStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'criminal_case_id'   => 'required|exists:district_criminal_registrations,CMID',
            'enforcement_date' => 'nullable|date',
            'enforcement_type' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status'           => 'required|in:Draft,Submitted,Final',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_criminal_uploads/enforcement-attachments', 'public');
        }

        $caseId  = $request->input('criminal_case_id');
        $isDraft = $request->input('status') === 'Draft';

        DistrictCriminalEnforcement::updateOrCreate(
            ['criminal_case_id' => $caseId],
            [
                'enforcement_type' => $request->input('enforcement_type'),
                'enforcement_date' => $request->input('enforcement_date'),
                'additional_notes' => $request->input('additional_notes'),
                'attachment'       => $attachment ?? DistrictCriminalEnforcement::where('criminal_case_id', $caseId)->value('attachment'),
                'status'           => $request->input('status'),
                'created_by'       => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            DistrictCriminalRegistration::where('CMID', $caseId)->update(['Status' => 'Dhaqan Gal']);
        }

        return $isDraft
            ? redirect()->route('criminal-enforcement.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('criminal-enforcement.index')->with('success', 'Dhaqan Galka si guul leh ayaa loo gudbiyay.');
    }

    public function document($caseId)
    {
        $case        = DistrictCriminalRegistration::with(['court', 'parties', 'lawyers.lawyer', 'assignments.employee'])->findOrFail($caseId);
        $enforcement = DistrictCriminalEnforcement::where('criminal_case_id', $caseId)->latest()->firstOrFail();
        $court       = $case->court;

        return view('Courts.District_criminal.registration.district_criminal_document_Enforcement', compact('case', 'enforcement', 'court'));
    }
}
