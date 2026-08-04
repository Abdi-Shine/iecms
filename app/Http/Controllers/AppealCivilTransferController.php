<?php

namespace App\Http\Controllers;

use App\Models\AppealCivilTransfer;
use App\Models\AppealCivilRegistration;
use App\Models\Court;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class AppealCivilTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = AppealCivilRegistration::with(['court', 'appeal', 'transfer', 'judgments.receipts'])
                        ->whereIn('Status', ['Rafcaan', 'La Wareejiyay'])
                        ->orderByDesc('ACID');

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
            'total'       => AppealCivilRegistration::count(),
            'rafcaan'     => AppealCivilRegistration::where('Status', 'Rafcaan')->count(),
            'transferred' => AppealCivilRegistration::where('Status', 'La Wareejiyay')->count(),
            'closed'      => AppealCivilRegistration::where('Status', 'Closed')->count(),
        ];

        return view('Courts.Appeal_civil.integration.appeal_civil_transfer',
            compact('records', 'courts', 'stats', 'statuses', 'isKaaliyeSare'));
    }

    public function form($caseId)
    {
        $case     = AppealCivilRegistration::with(['court', 'assignments.employee', 'appeal'])->findOrFail($caseId);
        $transfer = AppealCivilTransfer::where('civil_case_id', $caseId)->latest()->first();
        $courts   = Court::where('status', 'active')->orderBy('longName')->get();
        $judge    = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';

        return view('Courts.Appeal_civil.registration.appeal_civil_add_transfer',
            compact('case', 'transfer', 'courts', 'judge'));
    }

    public function store(Request $request)
    {
        $isSubmitting = $request->input('status') === 'Submitted';

        $request->validate([
            'civil_case_id' => 'required|exists:appeal_civil_registrations,ACID',
            'to_court'      => 'required|exists:courts,courtcode',
            'transfer_date' => $isSubmitting ? 'required|date' : 'nullable|date',
            'notes'         => 'nullable|string|max:1000',
            'status'        => 'required|in:Draft,Submitted',
        ], [
            'to_court.required'      => 'Fadlan dooro maxkamadda cusub.',
            'transfer_date.required' => 'Taariikhda wareejinta waa waajib.',
        ]);

        $caseId     = $request->input('civil_case_id');
        $isDraft    = $request->input('status') === 'Draft';
        $case       = AppealCivilRegistration::findOrFail($caseId);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('appeal_civil_uploads/transfer-attachments', 'public');
        }

        AppealCivilTransfer::updateOrCreate(
            ['civil_case_id' => $caseId],
            [
                'from_court'    => $case->GradeCourt,
                'to_court'      => $request->input('to_court'),
                'transfer_date' => $request->input('transfer_date'),
                'notes'         => $request->input('notes'),
                'attachment'    => $attachment ?? AppealCivilTransfer::where('civil_case_id', $caseId)->value('attachment'),
                'status'        => $request->input('status'),
                'created_by'    => auth()->user()->name ?? 'Admin',
            ]
        );

        return $isDraft
            ? redirect()->route('appeal-transfer.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('appeal-transfer.index')->with('success', 'Codsiga wareejinta waxaa la gudbiyay. Sugaya ogolashada Kaaliyaha Sare.');
    }

    public function approve(Request $request, $transferId)
    {
        if (!auth()->user()->hasPermission('Transfer Approval', 'view')) {
            abort(403, 'Ogolashada wareejinta waxaa kaliya samayn kara Kaaliyaha Sare.');
        }

        $transfer = AppealCivilTransfer::findOrFail($transferId);

        if ($transfer->status !== 'Submitted') {
            return back()->with('error', 'Wareejintan horey ayaa loo ogolaaday ama muswaad ayay tahay.');
        }

        $transfer->update([
            'status'      => 'Approved',
            'approved_by' => auth()->user()->name,
            'approved_at' => now(),
        ]);

        $transfer->civilCase()->update(['Status' => 'La Wareejiyay']);

        return redirect()->route('appeal-transfer.index')
            ->with('success', 'Wareejinta waa la ogolaaday. Dacwadda waxaa la wareejiyay.');
    }
}
