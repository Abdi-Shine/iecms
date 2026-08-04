<?php

namespace App\Http\Controllers;

use App\Models\CivilCaseDocument;
use App\Models\DistricCivilRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CivilCaseDocumentController extends Controller
{
    public function index(Request $request)
    {
        $caseId = $request->query('case_id');
        $case = DistricCivilRegistration::findOrFail($caseId);
        $docTypes = \App\Models\DocumentAttachment::orderBy('Aname')->get();
        return view('Courts.District_civil.registration.district_civil_document_support', compact('case', 'docTypes'));
    }

    public function getDocumentsByCase($caseId)
    {
        $docs = CivilCaseDocument::where('civil_case_id', $caseId)->get();
        return response()->json($docs);
    }

    public function store(Request $request)
    {
        $caseId = $request->case_id;
        $docData = $request->documents ?? [];

        // Track IDs to keep
        $currentDIDs = collect($docData)->pluck('DID')->filter()->toArray();
        
        // Delete documents not in the list
        $toDelete = CivilCaseDocument::where('civil_case_id', $caseId)->whereNotIn('DID', $currentDIDs)->get();
        foreach ($toDelete as $oldDoc) {
            if ($oldDoc->file_path) Storage::disk('public')->delete($oldDoc->file_path);
            $oldDoc->delete();
        }

        foreach ($docData as $index => $data) {
            $did = $data['DID'] ?? null;
            $fields = [
                'civil_case_id' => $caseId,
                'document_name' => $data['document_name'],
                'document_date' => $data['document_date'] ?? null,
                'description'   => $data['description'] ?? null,
            ];

            if ($request->hasFile("documents.$index.file")) {
                // Delete old file if updating
                if ($did) {
                    $existing = CivilCaseDocument::find($did);
                    if ($existing && $existing->file_path) Storage::disk('public')->delete($existing->file_path);
                }
                $fields['file_path'] = $request->file("documents.$index.file")->store('district_civil_uploads/Evidence_Documents', 'public');
            }

            if ($did) {
                $fields['updatedBy']   = auth()->user()->name ?? 'Admin';
                $fields['updatedDate'] = now()->format('Y-m-d');
                CivilCaseDocument::where('DID', $did)->update($fields);
            } else {
                $fields['addedBy']   = auth()->user()->name ?? 'Admin';
                $fields['addedDate'] = now()->format('Y-m-d');
                CivilCaseDocument::create($fields);
            }
        }

        return response()->json(['success' => true, 'message' => 'Documents saved successfully.']);
    }

    public function destroy($id)
    {
        $doc = CivilCaseDocument::findOrFail($id);
        if ($doc->file_path) Storage::disk('public')->delete($doc->file_path);
        $doc->delete();
        return response()->json(['success' => true, 'message' => 'Document deleted successfully.']);
    }
}

