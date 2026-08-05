<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use Illuminate\Http\Request;

class CriminalBiometricController extends Controller
{
    private function assertInvestigatorOrAdmin(Request $request): void
    {
        $roleNames = $request->user()->group?->roles->pluck('name') ?? collect();
        if (!$roleNames->intersect(['CID Institution Admin', 'Investigator'])->count()) {
            abort(403, 'Biometrics access is restricted to Investigator and Institution Admin roles.');
        }
    }

    public function index(Request $request, $id)
    {
        $this->assertInvestigatorOrAdmin($request);

        $case = CriminalCase::with('biometrics')->findOrFail($id);

        return view('cid.cases.biometrics', compact('case'));
    }

    public function store(Request $request, $id)
    {
        $this->assertInvestigatorOrAdmin($request);

        $case = CriminalCase::findOrFail($id);

        $data = $request->validate([
            'person_name'           => 'required|string|max:150',
            'person_role'           => 'required|in:suspect,person_of_interest',
            'fingerprint_reference' => 'nullable|string|max:100',
            'facial_photo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'dna_reference'         => 'nullable|string|max:100',
            'iris_scan_reference'   => 'nullable|string|max:100',
            'captured_by'           => 'required|string|max:150',
            'captured_date'         => 'required|date',
        ]);

        if ($request->hasFile('facial_photo')) {
            $file = $request->file('facial_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path("uploads/cid/biometrics/{$case->id}"), $filename);
            $data['facial_photo_path'] = "uploads/cid/biometrics/{$case->id}/{$filename}";
        }
        unset($data['facial_photo']);

        $data['added_by'] = $request->user()->name ?? 'Staff';

        $case->biometrics()->create($data);

        return redirect()->route('criminal-cases.biometrics.index', $case->id)
            ->with('success', 'Biometric record added.');
    }

    public function updateMatch(Request $request, $id, $biometricId)
    {
        $this->assertInvestigatorOrAdmin($request);

        $case = CriminalCase::findOrFail($id);
        $biometric = $case->biometrics()->findOrFail($biometricId);

        $data = $request->validate([
            'match_status'    => 'required|in:' . implode(',', \App\Models\CriminalCaseBiometric::MATCH_STATUSES),
            'match_reference' => 'nullable|string|max:150',
        ]);

        $biometric->update($data);

        return redirect()->route('criminal-cases.biometrics.index', $case->id)
            ->with('success', 'Match result updated.');
    }
}
