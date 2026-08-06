<?php

namespace App\Http\Controllers;

use App\Models\AppealCriminalJudgment;
use App\Models\AppealCriminalJudgmentReceipt;
use App\Models\AppealCriminalRegistration;
use App\Models\DocumentSignature;
use App\Models\Employee;
use App\Models\StatusProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AppealCriminalJudgmentController extends Controller
{
    public function index(Request $request)
    {
        $query = AppealCriminalRegistration::with([
            'court',
            'judgments' => fn($q) => $q->with('receipts')->orderByDesc('created_at')->limit(1),
        ])->orderByDesc('ACMID');

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
            'total'     => AppealCriminalJudgment::count(),
            'draft'     => AppealCriminalJudgment::where('status', 'Draft')->count(),
            'submitted' => AppealCriminalJudgment::where('status', 'Submitted')->count(),
            'final'     => AppealCriminalJudgment::where('status', 'Final')->count(),
        ];
        return view('appeal_court.Appeal_criminal.Conclusion.appeal_criminal_view_Judgment', compact('records', 'statuses', 'stats'));
    }

    public function create($caseId)
    {
        $case     = AppealCriminalRegistration::with('court', 'assignments.employee')->findOrFail($caseId);
        $judgment = null;
        $judge    = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        return view('appeal_court.Appeal_criminal.Conclusion.appeal_criminal_add_Judgment', compact('case', 'judgment', 'judge'));
    }

    public function store(Request $request)
    {
        $isDraft = $request->input('status', 'Draft') === 'Draft';

        if ($request->input('judgment_time')) {
            $request->merge(['judgment_time' => \Carbon\Carbon::parse($request->input('judgment_time'))->format('H:i')]);
        }

        $request->validate([
            'criminal_case_id' => 'required|integer',
            'judgment_date'    => 'nullable|date',
            'judgment_time'    => 'nullable|date_format:H:i',
            'judgment_type'    => 'nullable|string',
        ]);

        $judgment = AppealCriminalJudgment::create([
            ...$request->only('criminal_case_id', 'judgment_date', 'judgment_time', 'judgment_type'),
            'status'     => $isDraft ? 'Draft' : 'Submitted',
            'created_by' => auth()->user()->name ?? null,
        ]);

        if (!$isDraft) {
            $submitStatus = $request->input('case_status_on_submit', 'Xukun');
            $newStatus    = StatusProcess::where('name', $submitStatus)->value('name') ?? $submitStatus;
            AppealCriminalRegistration::where('ACMID', $request->criminal_case_id)
                ->update(['Status' => $newStatus]);
        }

        if ($isDraft) {
            return redirect()->route('appeal-criminal-judgments.edit', $judgment->id)
                ->with('success', 'Xukunku Draft ahaan ayaa lagu keydsaday.');
        }
        return redirect()->route('appeal-criminal-judgments.index')
            ->with('success', 'Xukunku si guul leh ayaa loo gudbiiyay.');
    }

    public function edit($id)
    {
        $judgment = AppealCriminalJudgment::findOrFail($id);
        $case     = AppealCriminalRegistration::with('court', 'assignments.employee')->findOrFail($judgment->criminal_case_id);
        $judge    = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';
        return view('appeal_court.Appeal_criminal.Conclusion.appeal_criminal_add_Judgment', compact('case', 'judgment', 'judge'));
    }

    public function document($id)
    {
        $judgment = AppealCriminalJudgment::with('receipts')->findOrFail($id);
        $case     = AppealCriminalRegistration::with('court', 'parties', 'legalRepresentatives.party', 'assignments.employee')
                        ->findOrFail($judgment->criminal_case_id);
        $court    = $case->court;

        $chair = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'appeal_criminal_judgment')
            ->where('document_id', $judgment->id)
            ->get()
            ->keyBy('role');

        $judgeSig   = $signatures['judge'] ?? null;
        $clerkSig   = $signatures['clerk'] ?? null;
        $isComplete = $judgeSig && $clerkSig;

        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        $myAssignment = $myEmployee
            ? $case->assignments
                ->whereIn('panel_role', ['Chair', 'Guddoomiye', 'Clerk', 'Kaaliye'])
                ->first(fn($a) => (int) $a->employee_id === (int) $myEmployee->AID)
            : null;

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

        $isStampRequested = false;
        $isStampApproved  = false;

        return view('appeal_court.Appeal_criminal.Conclusion.appeal_criminal_document_Judgment',
            compact('judgment', 'case', 'court', 'chair', 'clerk',
                    'signatures', 'judgeSig', 'clerkSig', 'isComplete',
                    'myRole', 'myAlreadySigned', 'isStampRequested', 'isStampApproved'));
    }

    public function receiptsIndex(Request $request)
    {
        $query = AppealCriminalRegistration::with([
            'parties',
            'legalRepresentatives',
            'judgments' => fn($q) => $q->with('receipts')->orderByDesc('created_at'),
        ])->orderByDesc('ACMID');

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
            'total'          => AppealCriminalJudgment::count(),
            'fully_received' => AppealCriminalJudgment::whereHas('receipts', fn($q) => $q->groupBy('judgment_id'))->count(),
            'partial'        => 0,
            'not_received'   => AppealCriminalJudgment::doesntHave('receipts')->count(),
        ];

        return view('appeal_court.Appeal_criminal.Conclusion.appeal_criminal_view_Judgment_taking_parties',
            compact('records', 'statuses', 'stats'));
    }

    public function confirmReceipt(Request $request, $judgmentId, $partyId)
    {
        $judgment = AppealCriminalJudgment::with('criminalCase.parties')->findOrFail($judgmentId);
        $party    = $judgment->criminalCase->parties->where('PID', $partyId)->firstOrFail();

        $signaturePath = AppealCriminalJudgmentReceipt::where('judgment_id', $judgmentId)
            ->where('party_id', $partyId)->value('signature');

        if ($request->filled('signature_data')) {
            $base64  = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('signature_data'));
            $decoded = base64_decode($base64);
            if ($decoded) {
                $filename = 'appeal_criminal_uploads/judgment-signatures/' . uniqid('sig_', true) . '.png';
                Storage::disk('public')->put($filename, $decoded);
                $signaturePath = $filename;
            }
        }

        $repSignaturePath = AppealCriminalJudgmentReceipt::where('judgment_id', $judgmentId)
            ->where('party_id', $partyId)->value('rep_signature');

        if ($request->filled('rep_signature_data')) {
            $base64  = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('rep_signature_data'));
            $decoded = base64_decode($base64);
            if ($decoded) {
                $filename = 'appeal_criminal_uploads/judgment-signatures/' . uniqid('rep_', true) . '.png';
                Storage::disk('public')->put($filename, $decoded);
                $repSignaturePath = $filename;
            }
        }

        AppealCriminalJudgmentReceipt::updateOrCreate(
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
        ]);

        $judgment = AppealCriminalJudgment::findOrFail($id);
        $judgment->update([
            ...$request->only('judgment_date', 'judgment_time', 'judgment_type'),
            'status' => $isDraft ? 'Draft' : 'Submitted',
        ]);

        if (!$isDraft) {
            $newStatus = StatusProcess::where('name', 'Xukun')->value('name') ?? 'Xukun';
            AppealCriminalRegistration::where('ACMID', $judgment->criminal_case_id)
                ->update(['Status' => $newStatus]);
        }

        if ($isDraft) {
            return redirect()->route('appeal-criminal-judgments.edit', $judgment->id)
                ->with('success', 'Xukunku Draft ahaan ayaa lagu cusboonaysiiyay.');
        }
        return redirect()->route('appeal-criminal-judgments.index')
            ->with('success', 'Xukunku si guul leh ayaa loo cusboonaysiiyay.');
    }
}
