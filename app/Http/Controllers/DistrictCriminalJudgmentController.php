<?php

namespace App\Http\Controllers;

use App\Models\DocumentSignature;
use App\Models\Employee;
use App\Models\JudgmentDocumentSignature;
use App\Models\DistrictCriminalJudgment;
use App\Models\DistrictCriminalJudgmentReceipt;
use App\Models\DistrictCriminalRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DistrictCriminalJudgmentController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictCriminalRegistration::with([
            'court',
            'judgments' => fn($q) => $q->with('receipts')->orderByDesc('created_at')->limit(1),
        ])->orderByDesc('CMID');

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
            'total'     => DistrictCriminalJudgment::count(),
            'draft'     => DistrictCriminalJudgment::where('status', 'Draft')->count(),
            'submitted' => DistrictCriminalJudgment::where('status', 'Submitted')->count(),
            'final'     => DistrictCriminalJudgment::where('status', 'Final')->count(),
        ];
        $criminalSubCases = \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->pluck('sub_case');

        return view('distract_courts.District_criminal.Conclusion.district_criminal_view_Judgment', compact('records', 'statuses', 'stats', 'criminalSubCases'));
    }

    public function create($caseId)
    {
        $case     = DistrictCriminalRegistration::with('court', 'assignments.employee')->findOrFail($caseId);
        $judgment = null;
        $judge    = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        return view('distract_courts.District_criminal.Conclusion.district_criminal_add_Judgment', compact('case', 'judgment', 'judge'));
    }

    public function store(Request $request)
    {
        $isDraft = $request->input('status', 'Draft') === 'Draft';

        if ($request->input('judgment_time')) {
            $request->merge(['judgment_time' => \Carbon\Carbon::parse($request->input('judgment_time'))->format('H:i')]);
        }

        $request->validate([
            'criminal_case_id' => 'required|integer',
            'judgment_date'  => 'nullable|date',
            'judgment_time'  => 'nullable|date_format:H:i',
            'judgment_type'  => 'nullable|string',
            'verdict'        => 'nullable|string',
        ]);

        $judgment = DistrictCriminalJudgment::create([
            ...$request->only('criminal_case_id', 'judgment_date', 'judgment_time', 'judgment_type'),
            'judgment_body' => $request->input('verdict'),
            'status'        => $isDraft ? 'Draft' : 'Submitted',
            'created_by'    => auth()->user()->name ?? null,
        ]);

        if (!$isDraft) {
            $submitStatus = $request->input('case_status_on_submit', 'Xukun');
            $newStatus    = StatusProcess::where('name', $submitStatus)->value('name') ?? $submitStatus;
            DistrictCriminalRegistration::where('CMID', $request->criminal_case_id)
                ->update(['Status' => $newStatus]);
        }

        if ($isDraft) {
            return redirect()->route('criminal-judgments.edit', $judgment->id)
                ->with('success', 'Xukunku Draft ahaan ayaa lagu keydsaday.');
        }
        return redirect()->route('criminal-judgments.index')
            ->with('success', 'Xukunku si guul leh ayaa loo gudbiiyay.');
    }

    public function edit($id)
    {
        $judgment = DistrictCriminalJudgment::findOrFail($id);
        $case     = DistrictCriminalRegistration::with('court', 'assignments.employee')->findOrFail($judgment->criminal_case_id);
        $judge    = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        return view('distract_courts.District_criminal.Conclusion.district_criminal_add_Judgment', compact('case', 'judgment', 'judge'));
    }

    public function document($id)
    {
        $judgment = DistrictCriminalJudgment::with('receipts')->findOrFail($id);
        $case     = DistrictCriminalRegistration::with('court', 'parties', 'lawyers.lawyer', 'legalRepresentatives.party', 'assignments.employee')
                        ->findOrFail($judgment->criminal_case_id);
        $court    = $case->court;

        $chair = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        // Load judgment signatures
        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'criminal_judgment')
            ->where('document_id', $judgment->id)
            ->get()
            ->keyBy('role');

        $judgeSig   = $signatures['judge'] ?? null;
        $clerkSig   = $signatures['clerk'] ?? null;
        $isComplete = $judgeSig && $clerkSig;

        // Load stamp signatures to drive toolbar button state
        $stampSigs       = JudgmentDocumentSignature::where('document_type', 'criminal_judgment_stamp')
            ->where('document_id', $judgment->id)->pluck('role');
        $isStampRequested = $stampSigs->contains('kaaliye');
        $isStampApproved  = $isStampRequested && $stampSigs->contains('archive_officer');

        // Determine current user's role
        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        // Match by employee_id on the assignment directly (avoids fragile nested relationship comparison)
        $myAssignment = $myEmployee
            ? $case->assignments
                ->whereIn('panel_role', ['Chair', 'Guddoomiye', 'Clerk', 'Kaaliye'])
                ->first(fn($a) => (int) $a->employee_id === (int) $myEmployee->AID)
            : null;

        // Fallback: if no matched assignment, allow any employee to fill whichever slot is still open
        if (!$myAssignment && $myEmployee) {
            if (!$judgeSig) {
                $myRole          = 'judge';
                $myAlreadySigned = false;
            } elseif (!$clerkSig) {
                $myRole          = 'clerk';
                $myAlreadySigned = false;
            } else {
                $myRole          = null;
                $myAlreadySigned = true;
            }
        } elseif ($myAssignment) {
            $isChairRole     = in_array($myAssignment->panel_role, ['Chair', 'Guddoomiye']);
            $myRole          = $isChairRole ? 'judge' : 'clerk';
            $myAlreadySigned = $isChairRole ? (bool) $judgeSig : (bool) $clerkSig;
        } else {
            $myRole          = null;
            $myAlreadySigned = false;
        }

        return view('distract_courts.District_criminal.Conclusion.district_criminal_document_Judgment',
            compact('judgment', 'case', 'court', 'chair', 'clerk',
                    'signatures', 'judgeSig', 'clerkSig', 'isComplete',
                    'myRole', 'myAlreadySigned', 'isStampRequested', 'isStampApproved'));
    }

    /**
     * Plain, read-only version of the judgment document — no sign button,
     * no stamp-request button, no sign modal. Used by list/table links that
     * just need to show the document, not offer the signing workflow.
     */
    public function documentReadOnly($id)
    {
        $judgment = DistrictCriminalJudgment::with('receipts')->findOrFail($id);
        $case     = DistrictCriminalRegistration::with('court', 'parties', 'lawyers.lawyer', 'legalRepresentatives.party', 'assignments.employee')
                        ->findOrFail($judgment->criminal_case_id);
        $court    = $case->court;

        $chair = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'criminal_judgment')
            ->where('document_id', $judgment->id)
            ->get()
            ->keyBy('role');

        $judgeSig = $signatures['judge'] ?? null;
        $clerkSig = $signatures['clerk'] ?? null;

        $stampSigs        = JudgmentDocumentSignature::where('document_type', 'criminal_judgment_stamp')
            ->where('document_id', $judgment->id)->pluck('role');
        $isStampApproved   = $stampSigs->contains('kaaliye') && $stampSigs->contains('archive_officer');

        return view('distract_courts.District_criminal.Conclusion.district_criminal_document_Judgment_readonly',
            compact('judgment', 'case', 'court', 'chair', 'clerk', 'judgeSig', 'clerkSig', 'isStampApproved'));
    }

    public function receiptsIndex(Request $request)
    {
        $query = DistrictCriminalRegistration::with([
            'parties',
            'legalRepresentatives',
            'judgments' => fn($q) => $q->with('receipts')->orderByDesc('created_at'),
        ])->orderByDesc('CMID');

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

        $records = $query->paginate($perPage)->withQueryString();

        $statuses = StatusProcess::orderBy('name')->get();

        $stats = [
            'total'           => DistrictCriminalJudgment::count(),
            'fully_received'  => DistrictCriminalJudgment::whereHas('receipts', fn($q) => $q->groupBy('judgment_id'))->count(),
            'partial'         => 0,
            'not_received'    => DistrictCriminalJudgment::doesntHave('receipts')->count(),
        ];

        $criminalSubCases = \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->pluck('sub_case');

        return view('distract_courts.District_criminal.Conclusion.district_criminal_view_Judgment_taking_parties',
            compact('records', 'statuses', 'stats', 'criminalSubCases'));
    }

    public function confirmReceipt(Request $request, $judgmentId, $partyId)
    {
        $judgment = DistrictCriminalJudgment::with('criminalCase.parties')->findOrFail($judgmentId);
        $party    = $judgment->criminalCase->parties->where('PID', $partyId)->firstOrFail();

        $signaturePath = DistrictCriminalJudgmentReceipt::where('judgment_id', $judgmentId)
            ->where('party_id', $partyId)->value('signature');

        if ($request->filled('signature_data')) {
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('signature_data'));
            $decoded = base64_decode($base64);
            if ($decoded) {
                $filename = 'district_criminal_uploads/judgment-signatures/' . uniqid('sig_', true) . '.png';
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $decoded);
                $signaturePath = $filename;
            }
        }

        // Handle wakiil (representative) signature
        $repSignaturePath = DistrictCriminalJudgmentReceipt::where('judgment_id', $judgmentId)
            ->where('party_id', $partyId)->value('rep_signature');

        if ($request->filled('rep_signature_data')) {
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('rep_signature_data'));
            $decoded = base64_decode($base64);
            if ($decoded) {
                $filename = 'district_criminal_uploads/judgment-signatures/' . uniqid('rep_', true) . '.png';
                Storage::disk('public')->put($filename, $decoded);
                $repSignaturePath = $filename;
            }
        }

        DistrictCriminalJudgmentReceipt::updateOrCreate(
            ['judgment_id' => $judgmentId, 'party_id' => $partyId],
            [
                'party_name'       => $party->full_name,
                'party_role'       => $request->input('party_role') ?: $party->party_role,
                'judgment_outcome' => $request->input('judgment_outcome'),
                'received_date'    => $request->input('received_date') ?: now()->toDateString(),
                'signature'        => $signaturePath,
                'rep_name'         => $request->input('rep_name') ?: null,
                'rep_signature'    => $repSignaturePath,
                'received_at'      => now(),
                'received_by'      => auth()->user()->name ?? 'Admin',
                'notes'            => $request->input('notes'),
            ]
        );

        return response()->json([
            'ok'               => true,
            'received_at'      => now()->format('d/m/Y H:i'),
            'received_date'    => $request->input('received_date')
                                    ? \Carbon\Carbon::parse($request->input('received_date'))->format('d/m/Y')
                                    : now()->format('d/m/Y'),
            'judgment_outcome' => $request->input('judgment_outcome'),
            'notes'            => $request->input('notes'),
        ]);
    }

    public function stampRequestDocument($id)
    {
        $judgment = DistrictCriminalJudgment::with([
            'criminalCase.court',
            'criminalCase.parties',
            'criminalCase.assignments.employee',
        ])->findOrFail($id);

        $case  = $judgment->criminalCase;
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = JudgmentDocumentSignature::with('signer')
            ->where('document_type', 'criminal_judgment_stamp')
            ->where('document_id', $judgment->id)
            ->get()->keyBy('role');

        $clerkSig   = $signatures['kaaliye'] ?? null;
        $archiveSig = $signatures['archive_officer'] ?? null;
        $isComplete = $clerkSig && $archiveSig;

        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first();

        $iAmKaaliye = $myEmployee && (
            ($clerk?->employee && (int) $clerk->employee->AID === (int) $myEmployee->AID) ||
            ($clerkSig && (int) $clerkSig->signer_id === (int) $myEmployee->AID)
        );

        if ($iAmKaaliye) {
            $myRole = 'kaaliye';
        } elseif (!$isComplete) {
            $myRole = 'archive_officer';
        } else {
            $myRole = null;
        }

        $myAlreadyActed = match ($myRole) {
            'kaaliye'         => (bool) $clerkSig,
            'archive_officer' => (bool) $archiveSig,
            default           => true,
        };

        return view('distract_courts.Archive.district_criminal_judgment_stamp_document',
            compact('judgment', 'case', 'clerk', 'myRole', 'myAlreadyActed',
                    'clerkSig', 'archiveSig', 'isComplete'));
    }

    public function update(Request $request, $id)
    {
        $isDraft = $request->input('status', 'Draft') === 'Draft';

        if ($request->input('judgment_time')) {
            $request->merge(['judgment_time' => \Carbon\Carbon::parse($request->input('judgment_time'))->format('H:i')]);
        }

        $request->validate([
            'judgment_date' => 'nullable|date',
            'judgment_time' => 'nullable|date_format:H:i',
            'judgment_type' => 'nullable|string',
            'verdict'       => 'nullable|string',
        ]);

        $judgment = DistrictCriminalJudgment::findOrFail($id);

        $judgment->update([
            ...$request->only('judgment_date', 'judgment_time', 'judgment_type'),
            'judgment_body' => $request->input('verdict'),
            'status'        => $isDraft ? 'Draft' : 'Submitted',
        ]);

        if (!$isDraft) {
            $newStatus = StatusProcess::where('name', 'Xukun')->value('name') ?? 'Xukun';
            DistrictCriminalRegistration::where('CMID', $judgment->criminal_case_id)
                ->update(['Status' => $newStatus]);
        }

        if ($isDraft) {
            return redirect()->route('criminal-judgments.edit', $judgment->id)
                ->with('success', 'Xukunku Draft ahaan ayaa lagu cusboonaysiiyay.');
        }
        return redirect()->route('criminal-judgments.index')
            ->with('success', 'Xukunku si guul leh ayaa loo cusboonaysiiyay.');
    }

}
