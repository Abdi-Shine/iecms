<?php

namespace App\Http\Controllers;

use App\Models\DistrictExecutionTransfer;
use App\Models\Court;
use App\Models\DistrictExecutionRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class DistrictExecutionTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictExecutionRegistration::with(['court', 'appeal', 'transfer', 'judgments.receipts'])
                        ->whereIn('Status', ['Rafcaan', 'La Wareejiyay'])
                        ->orderByDesc('ECID');

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
        $statuses       = StatusProcess::orderBy('name')->get();
        $courts         = Court::where('status', 'active')->orderBy('longName')->get();
        $isKaaliyeSare  = auth()->user()->hasPermission('Transfer Approval', 'view');

        $stats = [
            'total'      => DistrictExecutionRegistration::count(),
            'rafcaan'    => DistrictExecutionRegistration::where('Status', 'Rafcaan')->count(),
            'transferred'=> DistrictExecutionRegistration::where('Status', 'La Wareejiyay')->count(),
            'closed'     => DistrictExecutionRegistration::where('Status', 'Closed')->count(),
        ];

        return view('Courts.District_execution.integration.district_execution_transfer',
            compact('records', 'courts', 'stats', 'statuses', 'isKaaliyeSare'));
    }

    public function form($caseId)
    {
        $case     = DistrictExecutionRegistration::with(['court', 'assignments.employee', 'appeal'])->findOrFail($caseId);
        $transfer = DistrictExecutionTransfer::where('execution_case_id', $caseId)->latest()->first();
        $courts   = Court::where('status', 'active')->orderBy('longName')->get();
        $judge    = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';

        return view('Courts.District_execution.registration.district_execution_add_transfer',
            compact('case', 'transfer', 'courts', 'judge'));
    }

    public function store(Request $request)
    {
        $isSubmitting = $request->input('status') === 'Submitted';

        $request->validate([
            'execution_case_id' => 'required|exists:district_execution_registrations,ECID',
            'to_court'       => 'required|exists:courts,courtcode',
            'transfer_date'  => $isSubmitting ? 'required|date' : 'nullable|date',
            'notes'          => 'nullable|string|max:1000',
            'status'         => 'required|in:Draft,Submitted',
        ], [
            'to_court.required'      => 'Fadlan dooro maxkamadda cusub.',
            'transfer_date.required' => 'Taariikhda wareejinta waa waajib.',
        ]);

        $caseId   = $request->input('execution_case_id');
        $isDraft  = $request->input('status') === 'Draft';
        $case     = DistrictExecutionRegistration::findOrFail($caseId);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_execution_uploads/transfer-attachments', 'public');
        }

        DistrictExecutionTransfer::updateOrCreate(
            ['execution_case_id' => $caseId],
            [
                'from_court'    => $case->GradeCourt,
                'to_court'      => $request->input('to_court'),
                'transfer_date' => $request->input('transfer_date'),
                'notes'         => $request->input('notes'),
                'attachment'    => $attachment ?? DistrictExecutionTransfer::where('execution_case_id', $caseId)->value('attachment'),
                'status'        => $request->input('status'),
                'created_by'    => auth()->user()->name ?? 'Admin',
            ]
        );

        return $isDraft
            ? redirect()->route('execution-transfer.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('execution-transfer.index')->with('success', 'Codsiga wareejinta waxaa la gudbiyay. Sugaya ogolashada Kaaliyaha Sare.');
    }

    public function approve(Request $request, $transferId)
    {
        if (!auth()->user()->hasPermission('Transfer Approval', 'view')) {
            abort(403, 'Ogolashada wareejinta waxaa kaliya samayn kara Kaaliyaha Sare.');
        }

        $transfer = DistrictExecutionTransfer::findOrFail($transferId);

        if ($transfer->status !== 'Submitted') {
            return back()->with('error', 'Wareejintan horey ayaa loo ogolaaday ama muswaad ayay tahay.');
        }

        $transfer->update([
            'status'      => 'Approved',
            'approved_by' => auth()->user()->name,
            'approved_at' => now(),
        ]);

        $transfer->executionCase()->update(['Status' => 'La Wareejiyay']);

        return redirect()->route('execution-transfer.index')
            ->with('success', 'Wareejinta waa la ogolaaday. Dacwadda waxaa la wareejiyay.');
    }
}
