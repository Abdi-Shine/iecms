<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use App\Models\CriminalLegalProcessRequest;
use App\Models\Institution;
use Illuminate\Http\Request;

class CriminalLegalProcessController extends Controller
{
    public const SLUG_TO_TYPE = [
        'warrant-of-arrest-ago'      => 'warrant_of_arrest_ago',
        'search-seizure-ago'         => 'search_seizure_ago',
        'asset-recovery-ago'         => 'asset_recovery_ago',
        'arrest-without-warrant-ago' => 'arrest_without_warrant_ago',
        'search-warrants'            => 'search_warrant_court',
    ];

    public function index(Request $request, string $slug)
    {
        $type = self::SLUG_TO_TYPE[$slug] ?? abort(404);

        $query = CriminalLegalProcessRequest::with('criminalCase')->where('request_type', $type);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('case')) {
            $query->whereHas('criminalCase', fn ($q) => $q->where('case_number', 'like', '%' . $request->case . '%'));
        }

        $requests = $query->latest()->paginate(20)->withQueryString();

        return view('cid.cases.legal-process-registry', [
            'requests' => $requests,
            'slug'     => $slug,
            'type'     => $type,
            'typeLabel' => CriminalLegalProcessRequest::TYPES[$type],
        ]);
    }

    public function form(Request $request, $id, string $slug)
    {
        $type = self::SLUG_TO_TYPE[$slug] ?? abort(404);
        $case = CriminalCase::with('legalProcessRequests')->findOrFail($id);

        return view('cid.cases.legal-process-form', [
            'case'      => $case,
            'slug'      => $slug,
            'type'      => $type,
            'typeLabel' => CriminalLegalProcessRequest::TYPES[$type],
            'existing'  => $case->legalProcessRequests->where('request_type', $type),
        ]);
    }

    public function store(Request $request, $id, string $slug)
    {
        $type = self::SLUG_TO_TYPE[$slug] ?? abort(404);
        $case = CriminalCase::findOrFail($id);

        $data = $request->validate([
            'requesting_officer' => 'required|string|max:150',
            'urgency_level'      => 'nullable|string|max:20',
            'grounds'            => 'nullable|string',
            'details'            => 'nullable|string',
            'estimated_value'    => 'nullable|numeric',
        ]);

        $data['request_type'] = $type;
        $data['added_by']     = $request->user()->name ?? 'Staff';

        $legalRequest = $case->legalProcessRequests()->create($data);

        if (in_array($type, CriminalLegalProcessRequest::AGO_TYPES)) {
            $agoInstitutionId = Institution::where('type', 'ago')->value('id');

            $attorneyCase = \App\Models\AttorneyCase::create([
                'institution_id'   => $agoInstitutionId,
                'title'            => CriminalLegalProcessRequest::TYPES[$type] . ' - ' . $case->case_number,
                'offense_type'     => $case->arrest->alleged_offence ?? 'Unspecified',
                'date_reported'    => now()->toDateString(),
                'reporting_agency' => 'CID - ' . $case->case_number,
                'priority'         => $case->priority,
                'summary'          => $data['grounds'] ?? $data['details'] ?? null,
                'added_by'         => $request->user()->name ?? 'CID',
            ]);

            $legalRequest->update(['attorney_case_id' => $attorneyCase->ACID]);
        }

        $case->diaryEntries()->create([
            'entry_type'  => 'system',
            'action_type' => CriminalLegalProcessRequest::TYPES[$type] . ' Requested',
            'description' => $data['requesting_officer'],
            'user_id'     => $request->user()->id,
        ]);

        return redirect()->route('cid-legal-process.form', [$case->id, $slug])
            ->with('success', 'Request submitted.');
    }

    public function updateStatus(Request $request, $id, string $slug, $requestId)
    {
        $type = self::SLUG_TO_TYPE[$slug] ?? abort(404);
        $case = CriminalCase::findOrFail($id);
        $legalRequest = $case->legalProcessRequests()->where('request_type', $type)->findOrFail($requestId);

        $data = $request->validate([
            'status'            => 'required|in:' . implode(',', CriminalLegalProcessRequest::STATUSES),
            'issuing_authority' => 'nullable|string|max:150',
            'issue_date'        => 'nullable|date',
            'expiry_date'       => 'nullable|date',
        ]);

        $legalRequest->update($data);

        return redirect()->route('cid-legal-process.form', [$case->id, $slug])
            ->with('success', 'Status updated.');
    }

    public function recordExecution(Request $request, $id, string $slug, $requestId)
    {
        $type = self::SLUG_TO_TYPE[$slug] ?? abort(404);
        $case = CriminalCase::findOrFail($id);
        $legalRequest = $case->legalProcessRequests()->where('request_type', $type)->findOrFail($requestId);

        $data = $request->validate([
            'execution_outcome' => 'required|string',
            'execution_date'    => 'required|date',
            'items_seized'      => 'nullable|string',
        ]);

        $data['status'] = 'Executed';
        $legalRequest->update($data);

        return redirect()->route('cid-legal-process.form', [$case->id, $slug])
            ->with('success', 'Execution recorded.');
    }
}
