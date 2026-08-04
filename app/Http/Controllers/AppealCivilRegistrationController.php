<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppealCivilRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\AppealCivilRegistration::with('court')->orderByDesc('ACID');

        if (auth()->check() && auth()->user()->position !== 'admin') {
            $userCourt = auth()->user()->employee->courtID ?? null;
            if ($userCourt) {
                $query->where('GradeCourt', $userCourt);
            } else {
                $query->where('GradeCourt', 'NONE');
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('FileNo', 'like', "%$s%")
                  ->orWhere('RegisterNo', 'like', "%$s%")
                  ->orWhere('Remarks', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->filled('case_type')) {
            $query->where('CaseType', $request->case_type);
        }

        $userCourtID = (auth()->check() && auth()->user()->position !== 'admin')
                        ? (auth()->user()->employee->courtID ?? null)
                        : null;

        $allRecords = \App\Models\AppealCivilRegistration::with('court')->orderByDesc('ACID');
        if ($userCourtID) $allRecords->where('GradeCourt', $userCourtID);
        $allRecords = $allRecords->get();

        $records       = $query->get();
        $courts        = \App\Models\Court::orderBy('longName')->get();
        $caseTypes     = \App\Models\CaseType::orderBy('case_name')->get();
        $statuses      = \App\Models\StatusProcess::orderBy('name')->get();
        $civilSubCases = \App\Models\CaseCategory::where('case_name', 'Madani')->pluck('sub_case');

        return view('Courts.Appeal_civil.registration.appeal_civil_registration', compact('records', 'allRecords', 'courts', 'caseTypes', 'statuses', 'userCourtID', 'civilSubCases'));
    }

    public function show($id)
    {
        $case        = \App\Models\AppealCivilRegistration::with(['court', 'parties', 'documents', 'legalRepresentatives.party', 'lawyers.lawyer', 'lawyers.party', 'assignments.employee', 'hearings'])->findOrFail($id);
        $handover    = \App\Models\AppealCivilHandover::where('civil_case_id', $id)->latest()->first();
        $returnFile  = \App\Models\AppealCivilReturnFile::where('civil_case_id', $id)->latest()->first();
        $scriptures  = \App\Models\AppealCivilHearingScripture::with('hearing')->where('civil_case_id', $id)->orderByDesc('created_at')->get();
        $judgments   = \App\Models\AppealCivilJudgment::with('receipts')->where('civil_case_id', $id)->orderByDesc('created_at')->get();
        $enforcement = \App\Models\AppealCivilEnforcement::where('civil_case_id', $id)->latest()->first();
        $appeal      = \App\Models\AppealCivilAppeal::where('civil_case_id', $id)->latest()->first();

        $lowerCase          = null;
        $lowerHandover       = null;
        $lowerReturnFile     = null;
        $lowerScriptures     = collect();
        $lowerJudgments      = collect();
        $lowerEnforcement    = null;
        $lowerAppeal         = null;

        if ($case->lower_case_no) {
            $lowerCase = \App\Models\DistricCivilRegistration::with(['court', 'parties', 'documents', 'legalRepresentatives.party', 'lawyers.lawyer', 'lawyers.party', 'assignments.employee', 'hearings'])
                ->whereRaw('LOWER(TRIM(FileNo)) = ?', [strtolower(trim($case->lower_case_no))])
                ->first();

            if ($lowerCase) {
                $lowerHandover    = \App\Models\CivilCaseHandover::where('civil_case_id', $lowerCase->CRID)->latest()->first();
                $lowerReturnFile  = \App\Models\CivilCaseReturnFile::where('civil_case_id', $lowerCase->CRID)->latest()->first();
                $lowerScriptures  = \App\Models\HearingScripture::with('hearing')->where('civil_case_id', $lowerCase->CRID)->orderByDesc('created_at')->get();
                $lowerJudgments   = \App\Models\Judgment::with('receipts')->where('civil_case_id', $lowerCase->CRID)->orderByDesc('created_at')->get();
                $lowerEnforcement = \App\Models\CivilCaseEnforcement::where('civil_case_id', $lowerCase->CRID)->latest()->first();
                $lowerAppeal      = \App\Models\CivilCaseAppeal::where('civil_case_id', $lowerCase->CRID)->latest()->first();
            }
        }

        return view('Courts.Appeal_civil.registration.appeal_civil_information', compact(
            'case', 'handover', 'returnFile', 'scriptures', 'judgments', 'enforcement', 'appeal',
            'lowerCase', 'lowerHandover', 'lowerReturnFile', 'lowerScriptures', 'lowerJudgments', 'lowerEnforcement', 'lowerAppeal'
        ));
    }

    public function supporting($id)
    {
        $case       = \App\Models\AppealCivilRegistration::with(['court', 'parties', 'documents', 'assignments.employee', 'hearings', 'legalRepresentatives'])->findOrFail($id);
        $handover   = \App\Models\AppealCivilHandover::where('civil_case_id', $id)->latest()->first();
        $scriptures = \App\Models\AppealCivilHearingScripture::with('hearing')->where('civil_case_id', $id)->orderByDesc('created_at')->get();

        return view('Courts.Appeal_civil.registration.appeal_civil_add_supporting', compact('case', 'handover', 'scriptures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'RegisterNo'       => 'required|string|max:50',
            'FileNo'           => 'required|string|max:50',
            'GradeCourt'       => 'required|string|max:50',
            'CaseType'         => 'required|string|max:50',
            'SubCase'          => 'nullable|string|max:100',
            'OpenDate'         => 'required|date',
            'NumberLetter'     => 'nullable|string|max:50',
            'LegalBasis'       => 'nullable|string',
            'Orders_Requested' => 'nullable|string',
            'Remarks'          => 'nullable|string',
            'Status'           => 'required|string|max:50',
            'lower_court'      => 'nullable|string|max:50',
            'lower_case_no'    => 'nullable|string|max:100',
        ]);

        \App\Models\AppealCivilRegistration::create(array_merge(
            $request->only('RegisterNo','FileNo','GradeCourt','CaseType','SubCase','OpenDate','NumberLetter','LegalBasis','Orders_Requested','Remarks','Status','lower_court','lower_case_no'),
            [
                'addedBy'   => auth()->user()->name ?? 'Admin',
                'addedDate' => now()->format('Y-m-d'),
            ]
        ));

        return response()->json(['success' => true, 'message' => 'Appeal civil case registered successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'RegisterNo'       => 'required|string|max:50',
            'FileNo'           => 'required|string|max:50',
            'GradeCourt'       => 'required|string|max:50',
            'CaseType'         => 'required|string|max:50',
            'SubCase'          => 'nullable|string|max:100',
            'OpenDate'         => 'required|date',
            'NumberLetter'     => 'nullable|string|max:50',
            'LegalBasis'       => 'nullable|string',
            'Orders_Requested' => 'nullable|string',
            'Remarks'          => 'nullable|string',
            'Status'           => 'required|string|max:50',
            'lower_court'      => 'nullable|string|max:50',
            'lower_case_no'    => 'nullable|string|max:100',
        ]);

        $record = \App\Models\AppealCivilRegistration::findOrFail($id);
        $record->update(array_merge(
            $request->only('RegisterNo','FileNo','GradeCourt','CaseType','SubCase','OpenDate','NumberLetter','LegalBasis','Orders_Requested','Remarks','Status','lower_court','lower_case_no'),
            [
                'updatedBy'   => auth()->user()->name ?? 'Admin',
                'updatedDate' => now()->format('Y-m-d'),
            ]
        ));

        return response()->json(['success' => true, 'message' => 'Appeal civil case updated successfully.']);
    }

    public function destroy($id)
    {
        $record = \App\Models\AppealCivilRegistration::findOrFail($id);
        $record->delete();

        return response()->json(['success' => true, 'message' => 'Appeal civil case deleted successfully.']);
    }

    public function tracking(Request $request)
    {
        $baseQuery = \App\Models\AppealCivilRegistration::query();

        if (auth()->check() && auth()->user()->position !== 'admin') {
            $userCourt = auth()->user()->employee->courtID ?? null;
            if ($userCourt) {
                $baseQuery->where('GradeCourt', $userCourt);
            } else {
                $baseQuery->where('GradeCourt', 'NONE');
            }
        }

        $stats = [
            'total'   => (clone $baseQuery)->count(),
            'active'  => (clone $baseQuery)->whereIn('Status', ['Active', 'Gal Ku Qoris'])->count(),
            'pending' => (clone $baseQuery)->where('Status', 'Sug Qaatay')->count(),
            'done'    => (clone $baseQuery)->where('Status', 'Qaatay')->count(),
            'closed'  => (clone $baseQuery)->where('Status', 'Closed')->count(),
        ];

        $allStatuses = (clone $baseQuery)->whereNotNull('Status')->distinct()->orderBy('Status')->pluck('Status');

        $query = (clone $baseQuery)->with([
            'court', 'handover', 'parties', 'documents',
            'assignments.employee',
        ])->orderByDesc('ACID');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('FileNo', 'like', "%$s%")
                  ->orWhere('RegisterNo', 'like', "%$s%")
                  ->orWhereHas('court', fn($cq) => $cq->where('longName', 'like', "%$s%"))
                  ->orWhereHas('parties', function ($pq) use ($s) {
                      $pq->where('full_name', 'like', "%$s%")
                         ->orWhere('mother_name', 'like', "%$s%")
                         ->orWhere('contact_number', 'like', "%$s%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('Status', $request->status);
        }

        $perPage = $this->resolvePerPage($request);

        $records = $query->paginate($perPage)->withQueryString();
        $courts  = \App\Models\Court::orderBy('longName')->get();

        return view('Courts.Appeal_civil.registration.appeal_civil_tracking', compact('records', 'courts', 'stats', 'allStatuses'));
    }

    public function importLowerCourtParties($id)
    {
        $appealCase = \App\Models\AppealCivilRegistration::findOrFail($id);

        if (!$appealCase->lower_court || !$appealCase->lower_case_no) {
            return back()->with('error', 'Dacwadda hoose lama xidhin. Fadlan horta xidh maxkamadda hoose iyo lambarka dacwadda.');
        }

        $lowerCase = \App\Models\DistricCivilRegistration::where('GradeCourt', $appealCase->lower_court)
            ->where('FileNo', $appealCase->lower_case_no)
            ->first();

        if (!$lowerCase) {
            return back()->with('error', 'Dacwadda hoose lama helin.');
        }

        $lowerParties = \App\Models\CivilCaseParty::where('civil_case_id', $lowerCase->CRID)->get();

        if ($lowerParties->isEmpty()) {
            return back()->with('error', 'Dacwadda hoose dhinac ma lahan.');
        }

        $existing = \App\Models\AppealCivilParty::where('civil_case_id', $id)->count();
        if ($existing > 0) {
            return back()->with('error', 'Dhinacyada horaan loo diiwangeliyay. Ma awoodi doontid inaad mar labaad soo shubto.');
        }

        foreach ($lowerParties as $p) {
            \App\Models\AppealCivilParty::create([
                'civil_case_id'    => $id,
                'party_role'       => $p->party_role,
                'full_name'        => $p->full_name,
                'mother_name'      => $p->mother_name,
                'sex'              => $p->sex,
                'dob'              => $p->dob,
                'contact_number'   => $p->contact_number,
                'email'            => $p->email,
                'district'         => $p->district,
                'national_id'      => $p->national_id,
                'passport_number'  => $p->passport_number,
                'addedBy'          => auth()->user()->name ?? 'System',
                'addedDate'        => now()->format('Y-m-d'),
            ]);
        }

        return back()->with('success', $lowerParties->count() . ' dhinac si guul leh ayaa looga soo shubay dacwadda hoose.');
    }

    public function rafcaanCases($courtcode)
    {
        $cases = \App\Models\DistricCivilRegistration::where('GradeCourt', $courtcode)
            ->where('Status', 'Rafcaan')
            ->orderByDesc('CRID')
            ->get(['CRID', 'FileNo', 'RegisterNo', 'CaseType', 'SubCase', 'NumberLetter', 'LegalBasis', 'Orders_Requested', 'Remarks']);

        return response()->json($cases);
    }

    public function nextFileNo($courtcode)
    {
        $court    = \App\Models\Court::where('courtcode', $courtcode)->firstOrFail();
        $short    = $court->shortName;
        $currYear = date('Y');

        $last = \App\Models\AppealCivilRegistration::where('GradeCourt', $courtcode)
            ->where('FileNo', 'like', "{$short}/DML/%/{$currYear}")
            ->orderByDesc('ACID')
            ->value('FileNo');

        $serial = 1;
        if ($last) {
            $parts = explode('/', $last);
            if (count($parts) >= 3) {
                $serial = intval($parts[2]) + 1;
            }
        }

        $fileNo = sprintf('%s/DML/%d/%s', $short, $serial, $currYear);

        return response()->json(['fileNo' => $fileNo]);
    }
}
