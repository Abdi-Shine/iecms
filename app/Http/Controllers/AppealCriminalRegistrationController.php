<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppealCriminalRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\AppealCriminalRegistration::with('court')->orderByDesc('ACMID');

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

        $allRecords = \App\Models\AppealCriminalRegistration::with('court')->orderByDesc('ACMID');
        if ($userCourtID) $allRecords->where('GradeCourt', $userCourtID);
        $allRecords = $allRecords->get();

        $records          = $query->get();
        $courts           = \App\Models\Court::orderBy('longName')->get();
        $caseTypes        = \App\Models\CaseType::orderBy('case_name')->get();
        $statuses         = \App\Models\StatusProcess::orderBy('name')->get();
        $criminalSubCases = \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->pluck('sub_case');

        return view('appeal_court.Appeal_criminal.registration.appeal_criminal_registration', compact('records', 'allRecords', 'courts', 'caseTypes', 'statuses', 'userCourtID', 'criminalSubCases'));
    }

    public function show($id)
    {
        // Hearing/Judgment/Handover/Close/Enforcement/further-Appeal are not
        // built yet for Appeal Criminal — those sections are left out of
        // appeal_criminal_information.blade.php rather than wired to
        // routes/models that don't exist. The lower (District Criminal) case
        // is fully built already, so its full history is shown here.
        $case = \App\Models\AppealCriminalRegistration::with([
            'court', 'parties', 'documents', 'legalRepresentatives.party', 'lawyers.lawyer', 'lawyers.party',
            'assignments.employee',
        ])->findOrFail($id);

        $lowerCase        = null;
        $lowerHandover    = null;
        $lowerReturnFile  = null;
        $lowerScriptures  = collect();
        $lowerJudgments   = collect();
        $lowerEnforcement = null;
        $lowerAppeal      = null;

        if ($case->lower_case_no) {
            $lowerCase = \App\Models\DistrictCriminalRegistration::with(['court', 'parties', 'documents', 'legalRepresentatives.party', 'lawyers.lawyer', 'lawyers.party', 'assignments.employee', 'hearings'])
                ->whereRaw('LOWER(TRIM(FileNo)) = ?', [strtolower(trim($case->lower_case_no))])
                ->first();

            if ($lowerCase) {
                $lowerHandover    = \App\Models\DistrictCriminalHandover::where('criminal_case_id', $lowerCase->CMID)->latest()->first();
                $lowerReturnFile  = \App\Models\DistrictCriminalReturnFile::where('criminal_case_id', $lowerCase->CMID)->latest()->first();
                $lowerScriptures  = \App\Models\DistrictCriminalHearingScripture::with('hearing')->where('criminal_case_id', $lowerCase->CMID)->orderByDesc('created_at')->get();
                $lowerJudgments   = \App\Models\DistrictCriminalJudgment::with('receipts')->where('criminal_case_id', $lowerCase->CMID)->orderByDesc('created_at')->get();
                $lowerEnforcement = \App\Models\DistrictCriminalEnforcement::where('criminal_case_id', $lowerCase->CMID)->latest()->first();
                $lowerAppeal      = \App\Models\DistrictCriminalAppeal::where('criminal_case_id', $lowerCase->CMID)->latest()->first();
            }
        }

        return view('appeal_court.Appeal_criminal.registration.appeal_criminal_information', compact(
            'case', 'lowerCase', 'lowerHandover', 'lowerReturnFile', 'lowerScriptures', 'lowerJudgments', 'lowerEnforcement', 'lowerAppeal'
        ));
    }

    public function supporting($id)
    {
        $case = \App\Models\AppealCriminalRegistration::with(['court', 'parties', 'documents', 'legalRepresentatives'])->findOrFail($id);

        return view('appeal_court.Appeal_criminal.registration.appeal_criminal_add_supporting', compact('case'));
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

        \App\Models\AppealCriminalRegistration::create(array_merge(
            $request->only('RegisterNo','FileNo','GradeCourt','CaseType','SubCase','OpenDate','NumberLetter','LegalBasis','Orders_Requested','Remarks','Status','lower_court','lower_case_no'),
            [
                'institution_id' => auth()->user()->institution_id,
                'addedBy'   => auth()->user()->name ?? 'Admin',
                'addedDate' => now()->format('Y-m-d'),
            ]
        ));

        return response()->json(['success' => true, 'message' => 'Appeal criminal case registered successfully.']);
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

        $record = \App\Models\AppealCriminalRegistration::findOrFail($id);
        $record->update(array_merge(
            $request->only('RegisterNo','FileNo','GradeCourt','CaseType','SubCase','OpenDate','NumberLetter','LegalBasis','Orders_Requested','Remarks','Status','lower_court','lower_case_no'),
            [
                'updatedBy'   => auth()->user()->name ?? 'Admin',
                'updatedDate' => now()->format('Y-m-d'),
            ]
        ));

        return response()->json(['success' => true, 'message' => 'Appeal criminal case updated successfully.']);
    }

    public function destroy($id)
    {
        $record = \App\Models\AppealCriminalRegistration::findOrFail($id);
        $record->delete();

        return response()->json(['success' => true, 'message' => 'Appeal criminal case deleted successfully.']);
    }

    public function tracking(Request $request)
    {
        $baseQuery = \App\Models\AppealCriminalRegistration::query();

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
            'court', 'parties', 'documents', 'assignments.employee',
        ])->orderByDesc('ACMID');

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

        return view('appeal_court.Appeal_criminal.registration.appeal_criminal_tracking', compact('records', 'courts', 'stats', 'allStatuses'));
    }

    public function importLowerCourtParties($id)
    {
        $appealCase = \App\Models\AppealCriminalRegistration::findOrFail($id);

        if (!$appealCase->lower_court || !$appealCase->lower_case_no) {
            return back()->with('error', 'Dacwadda hoose lama xidhin. Fadlan horta xidh maxkamadda hoose iyo lambarka dacwadda.');
        }

        $lowerCase = \App\Models\DistrictCriminalRegistration::where('GradeCourt', $appealCase->lower_court)
            ->where('FileNo', $appealCase->lower_case_no)
            ->first();

        if (!$lowerCase) {
            return back()->with('error', 'Dacwadda hoose lama helin.');
        }

        $lowerParties = \App\Models\DistrictCriminalParty::where('criminal_case_id', $lowerCase->CMID)->get();

        if ($lowerParties->isEmpty()) {
            return back()->with('error', 'Dacwadda hoose dhinac ma lahan.');
        }

        $existing = \App\Models\AppealCriminalParty::where('criminal_case_id', $id)->count();
        if ($existing > 0) {
            return back()->with('error', 'Dhinacyada horaan loo diiwangeliyay. Ma awoodi doontid inaad mar labaad soo shubto.');
        }

        foreach ($lowerParties as $p) {
            \App\Models\AppealCriminalParty::create([
                'criminal_case_id' => $id,
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
        $cases = \App\Models\DistrictCriminalRegistration::where('GradeCourt', $courtcode)
            ->where('Status', 'Rafcaan')
            ->orderByDesc('CMID')
            ->get(['CMID', 'FileNo', 'RegisterNo', 'CaseType', 'SubCase', 'NumberLetter', 'LegalBasis', 'Orders_Requested', 'Remarks']);

        return response()->json($cases);
    }

    public function nextFileNo($courtcode)
    {
        $court    = \App\Models\Court::where('courtcode', $courtcode)->firstOrFail();
        $short    = $court->shortName;
        $currYear = date('Y');

        $last = \App\Models\AppealCriminalRegistration::where('GradeCourt', $courtcode)
            ->where('FileNo', 'like', "{$short}/DDL/%/{$currYear}")
            ->orderByDesc('ACMID')
            ->value('FileNo');

        $serial = 1;
        if ($last) {
            $parts = explode('/', $last);
            if (count($parts) >= 3) {
                $serial = intval($parts[2]) + 1;
            }
        }

        $fileNo = sprintf('%s/DDL/%d/%s', $short, $serial, $currYear);

        return response()->json(['fileNo' => $fileNo]);
    }
}
