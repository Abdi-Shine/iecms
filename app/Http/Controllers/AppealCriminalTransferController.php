<?php

namespace App\Http\Controllers;

use App\Models\AppealCriminalTransfer;
use App\Models\AppealCriminalRegistration;
use App\Models\Court;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class AppealCriminalTransferController extends Controller
{
    public function index(Request $request)
    {
        // 'appeal' (further-appeal-to-Supreme) isn't built yet — omitted from
        // the eager-load rather than left referencing a model that doesn't exist.
        $query = AppealCriminalRegistration::with(['court', 'transfer', 'judgments.receipts'])
                        ->whereIn('Status', ['Rafcaan', 'La Wareejiyay'])
                        ->orderByDesc('ACMID');

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

        $records       = $query->paginate($perPage)->withQueryString();
        $statuses      = StatusProcess::orderBy('name')->get();
        $courts        = Court::where('status', 'active')->orderBy('longName')->get();
        $isKaaliyeSare = auth()->user()->hasPermission('Transfer Approval', 'view');

        $stats = [
            'total'       => AppealCriminalRegistration::count(),
            'rafcaan'     => AppealCriminalRegistration::where('Status', 'Rafcaan')->count(),
            'transferred' => AppealCriminalRegistration::where('Status', 'La Wareejiyay')->count(),
            'closed'      => AppealCriminalRegistration::where('Status', 'Closed')->count(),
        ];

        return view('appeal_court.Appeal_criminal.integration.appeal_criminal_transfer',
            compact('records', 'courts', 'stats', 'statuses', 'isKaaliyeSare'));
    }

    public function form($caseId)
    {
        $case     = AppealCriminalRegistration::with(['court', 'assignments.employee'])->findOrFail($caseId);
        $transfer = AppealCriminalTransfer::where('criminal_case_id', $caseId)->latest()->first();
        $courts   = Court::where('status', 'active')->orderBy('longName')->get();
        $judge    = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';

        return view('appeal_court.Appeal_criminal.Conclusion.appeal_criminal_add_transfer',
            compact('case', 'transfer', 'courts', 'judge'));
    }

    public function store(Request $request)
    {
        $isSubmitting = $request->input('status') === 'Submitted';

        $request->validate([
            'criminal_case_id' => 'required|exists:appeal_criminal_registrations,ACMID',
            'to_court'         => 'required|exists:courts,courtcode',
            'transfer_date'    => $isSubmitting ? 'required|date' : 'nullable|date',
            'notes'            => 'nullable|string|max:1000',
            'status'           => 'required|in:Draft,Submitted',
        ], [
            'to_court.required'      => 'Fadlan dooro maxkamadda cusub.',
            'transfer_date.required' => 'Taariikhda wareejinta waa waajib.',
        ]);

        $caseId     = $request->input('criminal_case_id');
        $isDraft    = $request->input('status') === 'Draft';
        $case       = AppealCriminalRegistration::findOrFail($caseId);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('appeal_criminal_uploads/transfer-attachments', 'public');
        }

        AppealCriminalTransfer::updateOrCreate(
            ['criminal_case_id' => $caseId],
            [
                'from_court'    => $case->GradeCourt,
                'to_court'      => $request->input('to_court'),
                'transfer_date' => $request->input('transfer_date'),
                'notes'         => $request->input('notes'),
                'attachment'    => $attachment ?? AppealCriminalTransfer::where('criminal_case_id', $caseId)->value('attachment'),
                'status'        => $request->input('status'),
                'created_by'    => auth()->user()->name ?? 'Admin',
            ]
        );

        return $isDraft
            ? redirect()->route('appeal-criminal-transfer.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('appeal-criminal-transfer.index')->with('success', 'Codsiga wareejinta waxaa la gudbiyay. Sugaya ogolashada Kaaliyaha Sare.');
    }

    public function approve(Request $request, $transferId)
    {
        if (!auth()->user()->hasPermission('Transfer Approval', 'view')) {
            abort(403, 'Ogolashada wareejinta waxaa kaliya samayn kara Kaaliyaha Sare.');
        }

        $transfer = AppealCriminalTransfer::findOrFail($transferId);

        if ($transfer->status !== 'Submitted') {
            return back()->with('error', 'Wareejintan horey ayaa loo ogolaaday ama muswaad ayay tahay.');
        }

        $transfer->update([
            'status'      => 'Approved',
            'approved_by' => auth()->user()->name,
            'approved_at' => now(),
        ]);

        $transfer->criminalCase()->update(['Status' => 'La Wareejiyay']);

        return redirect()->route('appeal-criminal-transfer.index')
            ->with('success', 'Wareejinta waa la ogolaaday. Dacwadda waxaa la wareejiyay.');
    }
}
