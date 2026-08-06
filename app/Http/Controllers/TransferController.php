<?php

namespace App\Http\Controllers;

use App\Models\CivilCaseTransfer;
use App\Models\Court;
use App\Models\DistricCivilRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index()
    {
        $records  = DistricCivilRegistration::with(['court', 'appeal', 'transfer', 'judgments.receipts'])
                        ->whereIn('Status', ['Rafcaan', 'La Wareejiyay'])
                        ->orderByDesc('CRID')
                        ->get();
        $statuses       = StatusProcess::orderBy('name')->get();
        $courts         = Court::where('status', 'active')->orderBy('longName')->get();
        $isKaaliyeSare  = auth()->user()->hasPermission('Transfer Approval', 'view');

        $stats = [
            'total'      => DistricCivilRegistration::count(),
            'rafcaan'    => DistricCivilRegistration::where('Status', 'Rafcaan')->count(),
            'transferred'=> DistricCivilRegistration::where('Status', 'La Wareejiyay')->count(),
            'closed'     => DistricCivilRegistration::where('Status', 'Closed')->count(),
        ];

        return view('distract_courts.integration.courts_integration_transfer',
            compact('records', 'courts', 'stats', 'statuses', 'isKaaliyeSare'));
    }

    public function form($caseId)
    {
        $case     = DistricCivilRegistration::with(['court', 'assignments.employee', 'appeal'])->findOrFail($caseId);
        $transfer = CivilCaseTransfer::where('civil_case_id', $caseId)->latest()->first();
        $courts   = Court::where('status', 'active')->orderBy('longName')->get();
        $judge    = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? 'â€”';

        return view('distract_courts.District_civil.registration.district_civil_add_transfer',
            compact('case', 'transfer', 'courts', 'judge'));
    }

    public function store(Request $request)
    {
        $isSubmitting = $request->input('status') === 'Submitted';

        $request->validate([
            'civil_case_id' => 'required|exists:distric_civil_registrations,CRID',
            'to_court'      => 'required|exists:courts,courtcode',
            'transfer_date' => $isSubmitting ? 'required|date' : 'nullable|date',
            'notes'         => 'nullable|string|max:1000',
            'status'        => 'required|in:Draft,Submitted',
        ], [
            'to_court.required'      => 'Fadlan dooro maxkamadda cusub.',
            'transfer_date.required' => 'Taariikhda wareejinta waa waajib.',
        ]);

        $caseId   = $request->input('civil_case_id');
        $isDraft  = $request->input('status') === 'Draft';
        $case     = DistricCivilRegistration::findOrFail($caseId);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_civil_uploads/transfer-attachments', 'public');
        }

        CivilCaseTransfer::updateOrCreate(
            ['civil_case_id' => $caseId],
            [
                'from_court'    => $case->GradeCourt,
                'to_court'      => $request->input('to_court'),
                'transfer_date' => $request->input('transfer_date'),
                'notes'         => $request->input('notes'),
                'attachment'    => $attachment ?? CivilCaseTransfer::where('civil_case_id', $caseId)->value('attachment'),
                'status'        => $request->input('status'),
                'created_by'    => auth()->user()->name ?? 'Admin',
            ]
        );

        return $isDraft
            ? redirect()->route('transfer.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('transfer.index')->with('success', 'Codsiga wareejinta waxaa la gudbiyay. Sugaya ogolashada Kaaliyaha Sare.');
    }

    public function approve(Request $request, $transferId)
    {
        if (!auth()->user()->hasPermission('Transfer Approval', 'view')) {
            abort(403, 'Ogolashada wareejinta waxaa kaliya samayn kara Kaaliyaha Sare.');
        }

        $transfer = CivilCaseTransfer::findOrFail($transferId);

        if ($transfer->status !== 'Submitted') {
            return back()->with('error', 'Wareejintan horey ayaa loo ogolaaday ama muswaad ayay tahay.');
        }

        $transfer->update([
            'status'      => 'Approved',
            'approved_by' => auth()->user()->name,
            'approved_at' => now(),
        ]);

        $transfer->civilCase()->update(['Status' => 'La Wareejiyay']);

        return redirect()->route('transfer.index')
            ->with('success', 'Wareejinta waa la ogolaaday. Dacwadda waxaa la wareejiyay.');
    }
}

