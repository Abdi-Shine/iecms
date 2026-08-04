<?php

namespace App\Http\Controllers;

use App\Models\DistrictCriminalCloseCase;
use App\Models\DocumentSignature;
use App\Models\Employee;
use App\Models\DistrictCriminalJudgment;
use App\Models\DistrictCriminalRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistrictCriminalCloseCaseController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictCriminalRegistration::with('court')->orderByDesc('CMID');

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
            'total'   => DistrictCriminalRegistration::count(),
            'xukun'   => DistrictCriminalRegistration::where('Status', 'Xukun')->count(),
            'closed'  => DistrictCriminalRegistration::where('Status', 'Closed')->count(),
            'final'   => DistrictCriminalJudgment::where('status', 'Final')->count(),
        ];

        $criminalSubCases = \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->pluck('sub_case');

        return view('Courts.District_criminal.Conclusion.district_criminal_view_Close', compact('records', 'statuses', 'stats', 'criminalSubCases'));
    }

    public function document($caseId)
    {
        $data = $this->closeCaseDocumentData($caseId);

        return view('Courts.District_criminal.Conclusion.district_criminal_document_Close', $data);
    }

    /**
     * Plain, read-only version — no sign button, no stamp-request button,
     * no sign/stamp modals. Used by list/table links that just need to show
     * the document.
     */
    public function documentReadOnly($caseId)
    {
        $data = $this->closeCaseDocumentData($caseId);

        return view('Courts.District_criminal.Conclusion.district_criminal_document_Close_readonly', $data);
    }

    public function documentPdf($caseId)
    {
        $data = $this->closeCaseDocumentData($caseId);

        return \App\Support\CourtDocumentPdf::stream(
            'Courts.District_criminal.Conclusion.district_criminal_document_Close',
            $data,
            'CloseCase-' . $data['case']->FileNo . '.pdf'
        );
    }

    private function closeCaseDocumentData($caseId): array
    {
        $case      = DistrictCriminalRegistration::with(['court', 'parties', 'lawyers.lawyer', 'assignments.employee'])->findOrFail($caseId);
        $closeCase = DistrictCriminalCloseCase::where('criminal_case_id', $caseId)->latest()->firstOrFail();
        $court     = $case->court;
        $judgment  = $closeCase;

        $chair = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'criminal_close_case')
            ->where('document_id', $closeCase->id)
            ->get()
            ->keyBy('role');

        $judgeSig = $signatures['judge'] ?? $signatures['chair'] ?? null;
        $clerkSig = $signatures['clerk'] ?? $signatures['kaaliye'] ?? null;

        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        $isComplete = $judgeSig && $clerkSig;

        $stampSigs        = DocumentSignature::where('document_type', 'criminal_close_case_stamp')
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
        $case       = DistrictCriminalRegistration::with(['court', 'assignments.employee'])->findOrFail($caseId);
        $closeCase  = DistrictCriminalCloseCase::where('criminal_case_id', $caseId)->latest()->first();
        $judge      = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus = StatusProcess::where('name', 'Oodista')->get();

        return view('Courts.District_criminal.Conclusion.district_criminal_add_Close',
            compact('case', 'closeCase', 'judge', 'caseStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'criminal_case_id' => 'required|exists:district_criminal_registrations,CMID',
            'judgment_date'  => 'nullable|date',
            'judgment_type'  => 'nullable|string',
            'decision_body'  => 'nullable|string',
            'status'         => 'required|in:Draft,Submitted,Final',
        ]);

        $caseId  = $request->input('criminal_case_id');
        $isDraft = $request->input('status') === 'Draft';

        $closeCase = DistrictCriminalCloseCase::updateOrCreate(
            ['criminal_case_id' => $caseId],
            [
                'judgment_type' => $request->input('judgment_type'),
                'judgment_date' => $request->input('judgment_date'),
                'decision_body' => $request->input('decision_body'),
                'status'        => $request->input('status'),
                'created_by'    => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            DistrictCriminalRegistration::where('CMID', $caseId)->update(['Status' => 'Oodista']);
        }

        $redirect = $isDraft
            ? redirect()->route('criminal-close-case.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('criminal-close-case.index')->with('success', 'Kiiska xidhitaankiisu si guul leh ayaa loo gudbiyay.');

        return $redirect;
    }

    public function stampRequest($caseId)
    {
        $case      = DistrictCriminalRegistration::with(['court', 'parties', 'assignments.employee'])->findOrFail($caseId);
        $closeCase = DistrictCriminalCloseCase::where('criminal_case_id', $caseId)->latest()->firstOrFail();
        $clerk     = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $stampSigs  = DocumentSignature::with('signer')
                        ->where('document_type', 'criminal_close_case_stamp')
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

        return view('Courts.District_criminal.Conclusion.district_criminal_close_stamp_document',
            compact('closeCase', 'case', 'clerk', 'clerkSig', 'archiveSig',
                    'isComplete', 'isArchiveOfficer'));
    }

    public function close($caseId)
    {
        $case = DistrictCriminalRegistration::findOrFail($caseId);
        $case->update(['Status' => 'Closed']);

        return back()->with('success', 'Dacwadda si guul leh ayaa loo xidhay.');
    }
}
