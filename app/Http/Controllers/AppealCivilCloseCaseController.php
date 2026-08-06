<?php

namespace App\Http\Controllers;

use App\Models\AppealCivilCloseCase;
use App\Models\AppealCivilJudgment;
use App\Models\AppealCivilRegistration;
use App\Models\DocumentSignature;
use App\Models\Employee;
use App\Models\StatusProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppealCivilCloseCaseController extends Controller
{
    public function index(Request $request)
    {
        $query = AppealCivilRegistration::with('court')->orderByDesc('ACID');

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
            'total'  => AppealCivilRegistration::count(),
            'xukun'  => AppealCivilRegistration::where('Status', 'Xukun')->count(),
            'closed' => AppealCivilRegistration::where('Status', 'Closed')->count(),
            'final'  => AppealCivilJudgment::where('status', 'Final')->count(),
        ];

        return view('appeal_court.Appeal_civil.Conclusion.appeal_civil_view_Close', compact('records', 'statuses', 'stats'));
    }

    public function document($caseId)
    {
        $case      = AppealCivilRegistration::with(['court', 'parties', 'assignments.employee'])->findOrFail($caseId);
        $closeCase = AppealCivilCloseCase::where('civil_case_id', $caseId)->latest()->firstOrFail();
        $court     = $case->court;
        $judgment  = $closeCase;

        $chair = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'close_case')
            ->where('document_id', $closeCase->id)
            ->get()
            ->keyBy('role');

        $judgeSig = $signatures['judge'] ?? $signatures['chair'] ?? null;
        $clerkSig = $signatures['clerk'] ?? $signatures['kaaliye'] ?? null;

        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        $isComplete = $judgeSig && $clerkSig;

        $stampSigs        = DocumentSignature::where('document_type', 'close_case_stamp')
                                ->where('document_id', $closeCase->id)->get()->keyBy('role');
        $isStampRequested = $stampSigs->has('kaaliye');
        $isStampApproved  = $stampSigs->has('archive_officer');

        $myRole          = null;
        $myAlreadySigned = false;
        $myStampRole     = null;
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

        return view('appeal_court.Appeal_civil.Conclusion.appeal_civil_document_Close',
            compact('case', 'closeCase', 'court', 'judgment', 'chair', 'clerk',
                    'judgeSig', 'clerkSig', 'myEmployee', 'myRole', 'myAlreadySigned',
                    'isComplete', 'isStampApproved', 'isStampRequested', 'myStampRole'));
    }

    public function form($caseId)
    {
        $case       = AppealCivilRegistration::with(['court', 'assignments.employee'])->findOrFail($caseId);
        $closeCase  = AppealCivilCloseCase::where('civil_case_id', $caseId)->latest()->first();
        $judge      = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        $caseStatus = StatusProcess::where('name', 'Oodista')->get();

        return view('appeal_court.Appeal_civil.Conclusion.appeal_civil_add_Close',
            compact('case', 'closeCase', 'judge', 'caseStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'civil_case_id' => 'required|exists:appeal_civil_registrations,ACID',
            'judgment_date' => 'nullable|date',
            'judgment_type' => 'nullable|string',
            'decision_body' => 'nullable|string',
            'status'        => 'required|in:Draft,Submitted,Final',
        ]);

        $caseId  = $request->input('civil_case_id');
        $isDraft = $request->input('status') === 'Draft';

        AppealCivilCloseCase::updateOrCreate(
            ['civil_case_id' => $caseId],
            [
                'judgment_type' => $request->input('judgment_type'),
                'judgment_date' => $request->input('judgment_date'),
                'decision_body' => $request->input('decision_body'),
                'status'        => $request->input('status'),
                'created_by'    => auth()->user()->name ?? 'Admin',
            ]
        );

        if (!$isDraft) {
            AppealCivilRegistration::where('ACID', $caseId)->update(['Status' => 'Oodista']);
        }

        return $isDraft
            ? redirect()->route('appeal-close-case.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('appeal-close-case.index')->with('success', 'Kiiska xidhitaankiisu si guul leh ayaa loo gudbiyay.');
    }

    public function close($caseId)
    {
        $case = AppealCivilRegistration::findOrFail($caseId);
        $case->update(['Status' => 'Closed']);
        return back()->with('success', 'Dacwadda si guul leh ayaa loo xidhay.');
    }
}
