<?php

namespace App\Http\Controllers;

use App\Models\CriminalReceivedWarrant;
use Illuminate\Http\Request;

class CriminalReceivedWarrantController extends Controller
{
    public function index(Request $request)
    {
        $query = CriminalReceivedWarrant::with(['assignedOfficer', 'criminalCase']);

        if ($request->filled('status')) {
            $query->where('execution_status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(fn ($q) => $q->where('warrant_number', 'like', '%' . $request->search . '%')
                ->orWhere('suspect_name', 'like', '%' . $request->search . '%'));
        }

        $warrants = $query->latest('received_date')->paginate(20)->withQueryString();

        $officers = \App\Models\User::whereHas('group', function ($q) {
            $q->whereHas('roles', fn ($r) => $r->whereIn('name', ['Investigator', 'Officer']));
        })->orderBy('name')->get();

        return view('cid.cases.received-warrants', compact('warrants', 'officers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warrant_number'      => 'required|string|max:100',
            'issuing_authority'   => 'required|string|max:150',
            'suspect_name'        => 'required|string|max:150',
            'suspect_details'     => 'nullable|string',
            'offence'             => 'required|string|max:255',
            'received_date'       => 'required|date',
            'warrant_expiry_date' => 'nullable|date',
        ]);

        $data['added_by'] = $request->user()->name ?? 'Staff';

        CriminalReceivedWarrant::create($data);

        return redirect()->route('cid-received-warrants.index')->with('success', 'Received warrant logged.');
    }

    public function assign(Request $request, $id)
    {
        $warrant = CriminalReceivedWarrant::findOrFail($id);

        $data = $request->validate([
            'assigned_officer_id' => 'required|exists:users,id',
        ]);

        $data['execution_status'] = 'Assigned';
        $warrant->update($data);

        return redirect()->route('cid-received-warrants.index')->with('success', 'Warrant assigned.');
    }

    public function updateStatus(Request $request, $id)
    {
        $warrant = CriminalReceivedWarrant::findOrFail($id);

        $data = $request->validate([
            'execution_status' => 'required|in:' . implode(',', CriminalReceivedWarrant::STATUSES),
        ]);

        $warrant->update($data);

        return redirect()->route('cid-received-warrants.index')->with('success', 'Status updated.');
    }
}
