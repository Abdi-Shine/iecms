<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use App\Models\CriminalDetainee;
use Illuminate\Http\Request;

class CriminalDetaineeController extends Controller
{
    private function isAdmin(Request $request): bool
    {
        return $request->user()->group?->roles->contains('name', 'CID Institution Admin') ?? false;
    }

    /**
     * Investigators may only admit a detainee for a case they are the
     * assigned investigator on. Everything else in this controller
     * beyond store() is Institution Admin only, checked by the caller.
     */
    private function canAdmit(Request $request, CriminalCase $case): bool
    {
        if ($this->isAdmin($request)) {
            return true;
        }

        $roleNames = $request->user()->group?->roles->pluck('name') ?? collect();
        if (!$roleNames->contains('Investigator')) {
            return false;
        }

        $case->loadMissing(['assignment', 'occurrenceBook']);
        $investigatorId = $case->assignment->assigned_investigator_id ?? $case->occurrenceBook?->assigned_investigator_id;

        return $investigatorId === $request->user()->id;
    }

    public function index(Request $request)
    {
        $query = CriminalDetainee::with('criminalCase');

        if ($request->filled('status')) {
            $query->where('custody_status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('detainee_name', 'like', '%' . $request->search . '%');
        }

        $detainees = $query->latest('admission_datetime')->paginate(20)->withQueryString();

        $pendingCount = CriminalDetainee::where('custody_status', 'Newly Admitted')
            ->where(fn ($q) => $q->where('property_receipt_signed', false)->orWhere('medical_screening_referred', false))
            ->count();

        return view('cid.cases.detainee-registry', compact('detainees', 'pendingCount'));
    }

    public function admissionForm(Request $request, $id)
    {
        $case = CriminalCase::with(['arrest', 'detainee'])->findOrFail($id);

        if (!$this->canAdmit($request, $case)) {
            abort(403, 'You can only admit a detainee for a case you are the assigned investigator on.');
        }

        if (!$case->arrest) {
            return redirect()->route('criminal-cases.workflow.arrest.form', $case->id)
                ->with('error', 'Complete the Arrest stage before Detention Center admission.');
        }

        return view('cid.cases.detainee-admission-form', compact('case'));
    }

    public function admit(Request $request, $id)
    {
        $case = CriminalCase::with('arrest')->findOrFail($id);

        if (!$this->canAdmit($request, $case)) {
            abort(403, 'You can only admit a detainee for a case you are the assigned investigator on.');
        }

        if ($case->detainee()->exists()) {
            return back()->withErrors(['detainee' => 'This case already has a detainee admission record.']);
        }

        $data = $request->validate([
            'admission_datetime'         => 'required|date',
            'admitting_officer'          => 'required|string|max:150',
            'cell_unit_reference'        => 'nullable|string|max:100',
            'legal_deadline'             => 'nullable|date',
            'court_order_reference'      => 'nullable|string|max:150',
            'initial_health_declaration' => 'nullable|string',
            'property_receipt_signed'    => 'nullable|boolean',
            'medical_screening_referred' => 'nullable|boolean',
        ]);

        $data['detainee_name'] = $case->arrest->arrestee_name;
        $data['property_receipt_signed'] = $request->boolean('property_receipt_signed');
        $data['medical_screening_referred'] = $request->boolean('medical_screening_referred');
        $data['added_by'] = $request->user()->name ?? 'Staff';

        $detainee = $case->detainee()->create($data);

        foreach ($request->input('property_items', []) as $item) {
            if (!empty($item)) {
                $detainee->propertyItems()->create(['item_description' => $item]);
            }
        }

        $case->diaryEntries()->create([
            'entry_type'  => 'system',
            'action_type' => 'Detention Admission',
            'description' => $detainee->detainee_name,
            'user_id'     => $request->user()->id,
        ]);

        return redirect()->route('cid-detainees.show', $detainee->id)->with('success', 'Detainee admitted.');
    }

    public function show(Request $request, $id)
    {
        $detainee = CriminalDetainee::with(['criminalCase', 'propertyItems', 'transfers', 'release', 'remandOrders'])->findOrFail($id);

        return view('cid.cases.detainee-show', ['detainee' => $detainee, 'isAdmin' => $this->isAdmin($request)]);
    }

    public function updateStatus(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Only an Institution Admin can update custody status directly.');
        }

        $detainee = CriminalDetainee::findOrFail($id);

        $data = $request->validate([
            'custody_status' => 'required|in:' . implode(',', CriminalDetainee::STATUSES),
        ]);

        $detainee->update($data);

        return redirect()->route('cid-detainees.show', $detainee->id)->with('success', 'Custody status updated.');
    }

    public function storeTransfer(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Only an Institution Admin can record a transfer.');
        }

        $detainee = CriminalDetainee::findOrFail($id);

        $data = $request->validate([
            'from_facility'      => 'required|string|max:150',
            'to_facility'        => 'required|string|max:150',
            'transfer_datetime'  => 'required|date',
            'reason'             => 'nullable|string',
            'escorting_officer'  => 'required|string|max:150',
        ]);

        $data['added_by'] = $request->user()->name ?? 'Staff';

        $detainee->transfers()->create($data);
        $detainee->update(['custody_status' => 'Transferred', 'cell_unit_reference' => $data['to_facility']]);

        return redirect()->route('cid-detainees.show', $detainee->id)->with('success', 'Transfer recorded.');
    }

    public function storeRelease(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Only an Institution Admin can authorize a release.');
        }

        $detainee = CriminalDetainee::with('propertyItems')->findOrFail($id);

        if ($detainee->release) {
            return back()->withErrors(['release' => 'This detainee has already been released.']);
        }

        $data = $request->validate([
            'release_type'                => 'required|in:' . implode(',', \App\Models\CriminalDetaineeRelease::TYPES),
            'authorizing_officer'         => 'required|string|max:150',
            'release_document_reference'  => 'nullable|string|max:150',
            'release_conditions'          => 'nullable|string',
            'receiving_party'             => 'nullable|string|max:150',
            'property_returned_confirmed' => 'nullable|boolean',
            'released_at'                 => 'required|date',
        ]);

        $data['property_returned_confirmed'] = $request->boolean('property_returned_confirmed');
        $data['added_by'] = $request->user()->name ?? 'Staff';

        $detainee->release()->create($data);

        if ($data['property_returned_confirmed']) {
            $detainee->propertyItems()->update(['returned' => true, 'returned_at' => now()]);
        }

        $newStatus = $data['release_type'] === 'Bail Granted' ? 'Granted Bail' : 'Released';
        $detainee->update(['custody_status' => $newStatus]);

        $detainee->criminalCase->diaryEntries()->create([
            'entry_type'  => 'system',
            'action_type' => 'Detainee Released',
            'description' => $data['release_type'] . ' — authorized by ' . $data['authorizing_officer'],
            'user_id'     => $request->user()->id,
        ]);

        return redirect()->route('cid-detainees.show', $detainee->id)->with('success', 'Release recorded.');
    }

    public function storeRemandOrder(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Only an Institution Admin can record a remand order.');
        }

        $detainee = CriminalDetainee::findOrFail($id);

        $data = $request->validate([
            'court_reference'    => 'required|string|max:150',
            'judge'              => 'nullable|string|max:150',
            'remand_period'      => 'required|string|max:50',
            'remand_start_date'  => 'required|date',
            'expiry_date'        => 'required|date|after_or_equal:remand_start_date',
            'renewal_of'         => 'nullable|exists:criminal_detainee_remand_orders,id',
        ]);

        $data['added_by'] = $request->user()->name ?? 'Staff';

        $detainee->remandOrders()->create($data);
        $detainee->update(['legal_deadline' => $data['expiry_date'], 'custody_status' => 'Remanded']);

        return redirect()->route('cid-detainees.show', $detainee->id)->with('success', 'Remand order recorded.');
    }

    /**
     * Medical Records — restricted per spec to "medical staff, detention
     * admin, institution admin only". This app has no dedicated medical
     * staff role, so access is Institution Admin only.
     */
    public function medicalRecords(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Medical Records are restricted to Institution Admin.');
        }

        $detainee = CriminalDetainee::with('medicalRecords')->findOrFail($id);

        return view('cid.cases.detainee-medical', compact('detainee'));
    }

    public function storeMedicalRecord(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Medical Records are restricted to Institution Admin.');
        }

        $detainee = CriminalDetainee::findOrFail($id);

        $data = $request->validate([
            'visit_date'          => 'required|date',
            'visited_by'          => 'required|string|max:150',
            'screening_notes'     => 'nullable|string',
            'ongoing_conditions'  => 'nullable|string',
            'medications'         => 'nullable|string',
            'referral_to'         => 'nullable|string|max:150',
            'is_emergency'        => 'nullable|boolean',
        ]);

        $data['is_emergency'] = $request->boolean('is_emergency');
        $data['added_by'] = $request->user()->name ?? 'Staff';

        $detainee->medicalRecords()->create($data);

        return redirect()->route('cid-detainees.medical', $detainee->id)->with('success', 'Medical record added.');
    }
}
