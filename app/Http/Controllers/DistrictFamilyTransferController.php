<?php

namespace App\Http\Controllers;

use App\Models\DistrictFamilyTransfer;
use App\Models\Court;
use App\Models\DistrictFamilyRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;

class DistrictFamilyTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = DistrictFamilyRegistration::with(['court', 'appeal', 'transfer', 'judgments.receipts'])
                        ->whereIn('Status', ['Rafcaan', 'La Wareejiyay'])
                        ->orderByDesc('FCID');

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
            'total'      => DistrictFamilyRegistration::count(),
            'rafcaan'    => DistrictFamilyRegistration::where('Status', 'Rafcaan')->count(),
            'transferred'=> DistrictFamilyRegistration::where('Status', 'La Wareejiyay')->count(),
            'closed'     => DistrictFamilyRegistration::where('Status', 'Closed')->count(),
        ];

        return view('Courts.District_family.integration.district_family_transfer',
            compact('records', 'courts', 'stats', 'statuses', 'isKaaliyeSare'));
    }

    public function form($caseId)
    {
        $case     = DistrictFamilyRegistration::with(['court', 'assignments.employee', 'appeal'])->findOrFail($caseId);
        $transfer = DistrictFamilyTransfer::where('family_case_id', $caseId)->latest()->first();
        $courts   = Court::where('status', 'active')->orderBy('longName')->get();
        $judge    = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()?->employee?->EmpName ?? '—';

        return view('Courts.District_family.registration.district_family_add_transfer',
            compact('case', 'transfer', 'courts', 'judge'));
    }

    public function store(Request $request)
    {
        $isSubmitting = $request->input('status') === 'Submitted';

        $request->validate([
            'family_case_id' => 'required|exists:district_family_registrations,FCID',
            'to_court'       => 'required|exists:courts,courtcode',
            'transfer_date'  => $isSubmitting ? 'required|date' : 'nullable|date',
            'notes'          => 'nullable|string|max:1000',
            'status'         => 'required|in:Draft,Submitted',
        ], [
            'to_court.required'      => 'Fadlan dooro maxkamadda cusub.',
            'transfer_date.required' => 'Taariikhda wareejinta waa waajib.',
        ]);

        $caseId   = $request->input('family_case_id');
        $isDraft  = $request->input('status') === 'Draft';
        $case     = DistrictFamilyRegistration::findOrFail($caseId);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('district_family_uploads/transfer-attachments', 'public');
        }

        DistrictFamilyTransfer::updateOrCreate(
            ['family_case_id' => $caseId],
            [
                'from_court'    => $case->GradeCourt,
                'to_court'      => $request->input('to_court'),
                'transfer_date' => $request->input('transfer_date'),
                'notes'         => $request->input('notes'),
                'attachment'    => $attachment ?? DistrictFamilyTransfer::where('family_case_id', $caseId)->value('attachment'),
                'status'        => $request->input('status'),
                'created_by'    => auth()->user()->name ?? 'Admin',
            ]
        );

        return $isDraft
            ? redirect()->route('family-transfer.form', $caseId)->with('success', 'Muswaadda si guul leh ayaa lagu keydsaday.')
            : redirect()->route('family-transfer.index')->with('success', 'Codsiga wareejinta waxaa la gudbiyay. Sugaya ogolashada Kaaliyaha Sare.');
    }

    public function approve(Request $request, $transferId)
    {
        if (!auth()->user()->hasPermission('Transfer Approval', 'view')) {
            abort(403, 'Ogolashada wareejinta waxaa kaliya samayn kara Kaaliyaha Sare.');
        }

        $transfer = DistrictFamilyTransfer::findOrFail($transferId);

        if ($transfer->status !== 'Submitted') {
            return back()->with('error', 'Wareejintan horey ayaa loo ogolaaday ama muswaad ayay tahay.');
        }

        $transfer->update([
            'status'      => 'Approved',
            'approved_by' => auth()->user()->name,
            'approved_at' => now(),
        ]);

        $transfer->familyCase()->update(['Status' => 'La Wareejiyay']);

        return redirect()->route('family-transfer.index')
            ->with('success', 'Wareejinta waa la ogolaaday. Dacwadda waxaa la wareejiyay.');
    }
}
