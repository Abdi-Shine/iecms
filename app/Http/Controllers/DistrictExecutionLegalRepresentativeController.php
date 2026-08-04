<?php

namespace App\Http\Controllers;

use App\Models\DistrictExecutionLegalRepresentative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DistrictExecutionLegalRepresentativeController extends Controller
{
    public function getByCase($caseId)
    {
        $reps = DistrictExecutionLegalRepresentative::with('party')
            ->where('execution_case_id', $caseId)
            ->get()
            ->map(function ($r) {
                $data = $r->toArray();
                $data['party_full_name']   = $r->party?->full_name   ?? null;
                $data['party_mother_name'] = $r->party?->mother_name ?? null;
                return $data;
            });
        return response()->json($reps);
    }

    public function store(Request $request)
    {
        $request->validate([
            'execution_case_id' => 'required|exists:district_execution_registrations,ECID',
            'party_id'       => 'nullable|integer',
            'party_role'     => 'nullable|string|max:50',
            'rep_name'       => 'required|string|max:150',
            'rep_doc_number' => 'nullable|string|max:100',
            'rep_doc'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $id     = $request->input('id');
        $fields = [
            'execution_case_id' => $request->execution_case_id,
            'party_id'       => $request->party_id ?: null,
            'party_role'     => $request->party_role,
            'rep_name'       => $request->rep_name,
            'rep_doc_number' => $request->rep_doc_number,
        ];

        if ($request->hasFile('rep_doc')) {
            if ($id) {
                $existing = DistrictExecutionLegalRepresentative::find($id);
                if ($existing?->rep_doc) Storage::disk('public')->delete($existing->rep_doc);
            }
            $fields['rep_doc'] = $request->file('rep_doc')->store('district_execution_uploads/legal_reps', 'public');
        }

        if ($id) {
            $fields['addedBy']   = auth()->user()->name ?? 'Admin';
            DistrictExecutionLegalRepresentative::where('id', $id)->update($fields);
            $rep = DistrictExecutionLegalRepresentative::find($id);
        } else {
            $fields['addedBy']   = auth()->user()->name ?? 'Admin';
            $fields['addedDate'] = now()->toDateString();
            $rep = DistrictExecutionLegalRepresentative::create($fields);
        }

        return response()->json(['success' => true, 'message' => 'Wakiilka waa la keydsaday.', 'rep' => $rep]);
    }

    public function destroy($id)
    {
        $rep = DistrictExecutionLegalRepresentative::findOrFail($id);
        if ($rep->rep_doc) Storage::disk('public')->delete($rep->rep_doc);
        $rep->delete();
        return response()->json(['success' => true, 'message' => 'Wakiilka waa laga saaray.']);
    }
}
