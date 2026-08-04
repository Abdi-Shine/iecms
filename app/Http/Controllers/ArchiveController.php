<?php

namespace App\Http\Controllers;

use App\Models\CivilCaseCloseCase;
use App\Models\DocumentSignature;
use App\Models\Hearing;
use App\Models\Judgment;
use App\Models\JudgmentDocumentSignature;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index()
    {
        $stampSigs = DocumentSignature::with('signer')
            ->where('document_type', 'hearing_stamp')
            ->get()
            ->groupBy('document_id');

        $requestedIds = $stampSigs->keys();

        $hearings = Hearing::with(['civilCase.court', 'civilCase.parties'])
            ->whereIn('id', $requestedIds)
            ->orderByDesc('hearing_date')
            ->orderByDesc('hearing_time')
            ->get();

        return view('Courts.Archive.district_civil_approval_stamp',
            compact('hearings', 'stampSigs'));
    }

    public function judgmentsIndex()
    {
        $stampSigs = JudgmentDocumentSignature::with('signer')
            ->where('document_type', 'judgment_stamp')
            ->get()
            ->groupBy('document_id');

        $requestedIds = $stampSigs->keys();

        $judgments = Judgment::with(['civilCase.court'])
            ->whereIn('id', $requestedIds)
            ->orderByDesc('id')
            ->get();

        $closeStampSigs = DocumentSignature::with('signer')
            ->where('document_type', 'close_case_stamp')
            ->get()
            ->groupBy('document_id');

        $closeRequestedIds = $closeStampSigs->keys();

        $closeCases = CivilCaseCloseCase::with(['case.court'])
            ->whereIn('id', $closeRequestedIds)
            ->orderByDesc('id')
            ->get();

        return view('Courts.Archive.district_civil_judgment_approvals',
            compact('judgments', 'stampSigs', 'closeCases', 'closeStampSigs'));
    }

    public function judgmentStampDocument($id)
    {
        $judgment = Judgment::with(['civilCase.court', 'civilCase.parties'])->findOrFail($id);

        return view('Courts.Archive.district_civil_judgment_stamp_document', compact('judgment'));
    }

}
