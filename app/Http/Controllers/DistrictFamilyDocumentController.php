<?php

namespace App\Http\Controllers;

use App\Models\DistrictFamilyDocument;
use App\Models\DistrictFamilyRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DistrictFamilyDocumentController extends Controller
{
    public function index(Request $request)
    {
        $caseId = $request->query('case_id');
        $case = DistrictFamilyRegistration::findOrFail($caseId);
        $docTypes = \App\Models\DocumentAttachment::orderBy('Aname')->get();
        return view('Courts.District_family.registration.district_family_document_support', compact('case', 'docTypes'));
    }

    public function getDocumentsByCase($caseId)
    {
        $docs = DistrictFamilyDocument::where('family_case_id', $caseId)->get();
        return response()->json($docs);
    }

    public function store(Request $request)
    {
        $caseId = $request->case_id;
        $docData = $request->documents ?? [];

        $currentDIDs = collect($docData)->pluck('DID')->filter()->toArray();

        $toDelete = DistrictFamilyDocument::where('family_case_id', $caseId)->whereNotIn('DID', $currentDIDs)->get();
        foreach ($toDelete as $oldDoc) {
            if ($oldDoc->file_path) Storage::disk('public')->delete($oldDoc->file_path);
            $oldDoc->delete();
        }

        foreach ($docData as $index => $data) {
            $did = $data['DID'] ?? null;
            $fields = [
                'family_case_id' => $caseId,
                'document_name'  => $data['document_name'],
                'document_date'  => $data['document_date'] ?? null,
                'description'    => $data['description'] ?? null,
            ];

            if ($request->hasFile("documents.$index.file")) {
                if ($did) {
                    $existing = DistrictFamilyDocument::find($did);
                    if ($existing && $existing->file_path) Storage::disk('public')->delete($existing->file_path);
                }
                $fields['file_path'] = $request->file("documents.$index.file")->store('district_family_uploads/Evidence_Documents', 'public');
            }

            if ($did) {
                $fields['updatedBy']   = auth()->user()->name ?? 'Admin';
                $fields['updatedDate'] = now()->format('Y-m-d');
                DistrictFamilyDocument::where('DID', $did)->update($fields);
            } else {
                $fields['addedBy']   = auth()->user()->name ?? 'Admin';
                $fields['addedDate'] = now()->format('Y-m-d');
                DistrictFamilyDocument::create($fields);
            }
        }

        return response()->json(['success' => true, 'message' => 'Documents saved successfully.']);
    }

    public function destroy($id)
    {
        $doc = DistrictFamilyDocument::findOrFail($id);
        if ($doc->file_path) Storage::disk('public')->delete($doc->file_path);
        $doc->delete();
        return response()->json(['success' => true, 'message' => 'Document deleted successfully.']);
    }
}
