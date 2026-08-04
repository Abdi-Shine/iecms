<?php

namespace App\Http\Controllers;

use App\Models\DistrictFamilyCloseCase;
use App\Models\DocumentSignature;
use App\Models\Employee;
use App\Models\DistrictFamilyJudgment;
use App\Models\DistrictFamilyRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistrictFamilyCloseCaseController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictFamilyRegistration::with('court')->orderByDesc('FCID');

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
            'total'   => DistrictFamilyRegistration::count(),
            'xukun'   => DistrictFamilyRegistration::where('Status', 'Xukun')->count(),
            'closed'  => DistrictFamilyRegistration::where('Status', 'Closed')->count(),
            'final'   => DistrictFamilyJudgment::where('status', 'Final')->count(),
        ];

        $familySubCases = \App\Models\CaseCategory::where('case_name', 'Qoyska')->pluck('sub_case');

        return view('Courts.District_family.Conclusion.district_family_view_Close', compact('records', 'statuses', 'stats', 'familySubCases'));
    }

    public function document($caseId)
    {
        $data = $this->closeCaseDocumentData($caseId);

        return view('Courts.District_family.Conclusion.district_family_document_Close', $data);
    }

    /**
     * Plain, read-only version — no sign button, no stamp-request button,
     * no sign/stamp modals. Used by list/table links that just need to show
     * the document.
     */
    public function documentReadOnly($caseId)
    {
        $data = $this->closeCaseDocumentData($caseId);

        return view('Courts.District_family.Conclusion.district_family_document_Close_readonly', $data);
    }

    public function documentPdf($caseId)
    {
        $data = $this->closeCaseDocumentData($caseId);

        return \App\Support\CourtDocumentPdf::stream(
            'Courts.District_family.Conclusion.district_family_document_Close',
            $data,
            'CloseCase-' . $data['case']->FileNo . '.pdf'
        );
    }

    private function closeCaseDocumentData($caseId): array
    {
        $case      = DistrictFamilyRegistration::with(['court', 'parties', 'lawyers.lawyer', 'assignments.employee'])->findOrFail($caseId);
        $closeCase = DistrictFamilyCloseCase::where('family_case_id', $caseId)->latest()->firstOrFail();
        $court     = $case->court;
        $judgment  = $closeCase;

        $chair = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'family_close_case')
            ->where('document_id', $closeCase->id)
            ->get()
            ->keyBy('role');

        $judgeSig = $signatures['judge'] ?? $signatures['chair'] ?? null;
        $clerkSig = $signatures['clerk'] ?? $signatures['kaaliye'] ?? null;

        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        $isComplete = $judgeSig && $clerkSig;

        $stampSigs        = DocumentSignature::where('document_type', 'family_close_case_stamp')
                                ->where('document_id', $closeCase->id)->get()->keyBy('role');
        $isStampRequested = $stampSigs->has('kaaliye');
        $isStampApproved  = $stampSigs->has('archive_officer');

        $myRole = null;
        $myAlreadySigned = false;
        $myStampRole = null;
        if ($myEmployee) {
            $myAlreadySigned = $signatures->contains('signer_id', $myEmployee->AID);
            if (!$myAlreadySigned) {
                $isJudge = $case->assignments
                    ->whereIn('panel_role', ['Chair', 'Judge', 'Xaakimka', 'Guddoomiye'])
                    ->first(fn($a) => (int) $a->employee_id === (int) $myEmployee->AID);
                $isClerk = $case->assignments
                    ->whereIn('panel_role', ['Clerk', 'Kaaliye'])
                    ->first(fn($a) => (int) $a->employee_id === (int) $myEmployee->AID);
                $myRole = $isJudge ? 'judge' : ($isClerk ? 'clerk' : null);
            }

            if (!$isStampRequested) {
                $isKaaliye = $case->assignments
                    ->whereIn('panel_role', ['Clerk', 'Kaaliye'])
                    ->first(fn($a) => (int) $a->employee_id === (int) $myEmployee->AID);
                if ($isKaaliye) $myStampRole = 'kaaliye';
            } elseif (!$isStampApproved) {
                $myStampRole = 'archive_officer';
            }
        }

        return compact('case', 'closeCase', 'court', 'judgment', 'chair', 'clerk',
            'judgeSig', 'clerkSig', 'myEmployee', 'myRole', 'myAlreadySigned',
            'isComplete', 'isStampApproved', 'isStampRequested', 'myStampRole');
    }

    public function form($caseId)
    {
        $case       = DistrictFamilyRegistration::with(['court', 'assignments.employee'])->findOrFail($caseId);
        $closeCase  = DistrictFamilyCloseCase::where('family_case_id', $caseId)->latest()->first();
        $judge      = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus = StatusProcess::where('name', 'Oodista')->get();

        return view('Courts.District_family.Conclusion.district_family_add_Close',
            compact('case', 'closeCase', 'judge', 'caseStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'family_case_id' => 'required|exists:district_family_registrations,FCID',
            'judgment_date'  => 'nullable|date',
            'judgment_type'  => 'nullable|string',
            'decision_body'  => 'nullable|string',
            'status'         => 'required|in:Draft,Submitted,Final',
        ]);

        $caseId  = $request->input('family_case_id');
        $isDraft = $request->input('status') === 'Draft';

        $closeCase = DistrictFamilyCloseCase::updateOrCreate(
            ['family_case_id' => $caseId],
            [
                'judgment_type' => $request->input('judgment_type'),
                'judgment_date' => $request->input('judgment_date'),
                'decision_body' => $request->input('decision_body'),
                'status'        => $request->input('status'),
                'created_by'    => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            DistrictFamilyRegistration::where('FCID', $caseId)->update(['Status' => 'Oodista']);
        }

        $redirect = $isDraft
            ? redirect()->route('family-close-case.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('family-close-case.index')->with('success', 'Kiiska xidhitaankiisu si guul leh ayaa loo gudbiyay.');

        return $redirect;
    }

    public function stampRequest($caseId)
    {
        $case      = DistrictFamilyRegistration::with(['court', 'parties', 'assignments.employee'])->findOrFail($caseId);
        $closeCase = DistrictFamilyCloseCase::where('family_case_id', $caseId)->latest()->firstOrFail();
        $clerk     = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $stampSigs  = DocumentSignature::with('signer')
                        ->where('document_type', 'family_close_case_stamp')
                        ->where('document_id', $closeCase->id)
                        ->get()->keyBy('role');

        $clerkSig   = $stampSigs['kaaliye'] ?? null;
        $archiveSig = $stampSigs['archive_officer'] ?? null;
        $isComplete = $clerkSig && $archiveSig;

        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        $iAmKaaliye = $myEmployee && (
            ($clerk?->employee && (int) $clerk->employee->AID === (int) $myEmployee->AID) ||
            ($clerkSig && (int) $clerkSig->signer_id === (int) $myEmployee->AID)
        );

        $isArchiveOfficer = auth()->user()->hasPermission('Archive', 'view');

        return view('Courts.District_family.Conclusion.district_family_close_stamp_document',
            compact('closeCase', 'case', 'clerk', 'clerkSig', 'archiveSig',
                    'isComplete', 'isArchiveOfficer'));
    }

    public function close($caseId)
    {
        $case = DistrictFamilyRegistration::findOrFail($caseId);
        $case->update(['Status' => 'Closed']);

        return back()->with('success', 'Dacwadda si guul leh ayaa loo xidhay.');
    }
}
