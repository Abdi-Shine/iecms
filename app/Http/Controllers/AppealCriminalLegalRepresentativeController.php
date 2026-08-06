<?php

namespace App\Http\Controllers;

use App\Models\AppealCriminalLegalRepresentative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppealCriminalLegalRepresentativeController extends Controller
{
    public function getByCase($caseId)
    {
        $reps = AppealCriminalLegalRepresentative::with('party')
            ->where('criminal_case_id', $caseId)
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
            'criminal_case_id' => 'required|exists:appeal_criminal_registrations,ACMID',
            'party_id'         => 'nullable|integer',
            'party_role'       => 'nullable|string|max:50',
            'rep_name'         => 'required|string|max:150',
            'rep_doc_number'   => 'nullable|string|max:100',
            'rep_doc'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $id     = $request->input('id');
        $fields = [
            'criminal_case_id' => $request->criminal_case_id,
            'party_id'         => $request->party_id ?: null,
            'party_role'       => $request->party_role,
            'rep_name'         => $request->rep_name,
            'rep_doc_number'   => $request->rep_doc_number,
        ];

        if ($request->hasFile('rep_doc')) {
            if ($id) {
                $existing = AppealCriminalLegalRepresentative::find($id);
                if ($existing?->rep_doc) Storage::disk('public')->delete($existing->rep_doc);
            }
            $fields['rep_doc'] = $request->file('rep_doc')->store('appeal_criminal_uploads/legal_reps', 'public');
        }

        if ($id) {
            $fields['addedBy'] = auth()->user()->name ?? 'Admin';
            AppealCriminalLegalRepresentative::where('id', $id)->update($fields);
            $rep = AppealCriminalLegalRepresentative::find($id);
        } else {
            $fields['addedBy']   = auth()->user()->name ?? 'Admin';
            $fields['addedDate'] = now()->toDateString();
            $rep = AppealCriminalLegalRepresentative::create($fields);
        }

        return response()->json(['success' => true, 'message' => 'Wakiilka waa la keydsaday.', 'rep' => $rep]);
    }

    public function destroy($id)
    {
        $rep = AppealCriminalLegalRepresentative::findOrFail($id);
        if ($rep->rep_doc) Storage::disk('public')->delete($rep->rep_doc);
        $rep->delete();
        return response()->json(['success' => true, 'message' => 'Wakiilka waa laga saaray.']);
    }
}
