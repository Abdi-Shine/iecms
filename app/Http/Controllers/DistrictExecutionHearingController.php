<?php

namespace App\Http\Controllers;

use App\Models\DistrictExecutionHearing;
use App\Models\DistrictExecutionHearingScripture;
use App\Models\DistrictExecutionRegistration;
use App\Models\Court;
use App\Models\DocumentSignature;
use App\Models\Employee;
use App\Models\StatusProcess;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DistrictExecutionHearingController extends Controller
{
    public function hearingCases(Request $request)
    {
        $query = DistrictExecutionRegistration::with('handover', 'court', 'hearings')->orderByDesc('ECID');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('FileNo', 'like', "%$s%")
                  ->orWhere('CaseType', 'like', "%$s%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('Status', $request->status);
        }

        if ($request->filled('sub_case')) {
            $query->where('SubCase', $request->sub_case);
        }

        $perPage = $this->resolvePerPage($request);

        $records        = $query->paginate($perPage)->withQueryString();
        $statuses       = StatusProcess::orderBy('name')->get();
        $court          = Court::first();
        $executionSubCases = \App\Models\CaseCategory::where('case_name', 'Fulinta')->pluck('sub_case');

        $hearings = DistrictExecutionHearing::all();
        $hearingStats = [
            'total'     => $hearings->count(),
            'scheduled' => $hearings->where('status', 'Scheduled')->count(),
            'completed' => $hearings->where('status', 'Completed')->count(),
            'thisMonth' => $hearings->filter(fn($h) => \Carbon\Carbon::parse($h->hearing_date)->format('Y-m') === date('Y-m'))->count(),
        ];

        return view('distract_courts.District_execution.hearing.district_execution_view_hearing_Cases', compact('records', 'statuses', 'court', 'hearingStats', 'executionSubCases'));
    }

    public function hearingScripture(Request $request)
    {
        $query = DistrictExecutionRegistration::with('handover', 'court')->orderByDesc('ECID');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('FileNo', 'like', "%$s%")
                  ->orWhere('CaseType', 'like', "%$s%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('Status', $request->status);
        }

        if ($request->filled('sub_case')) {
            $query->where('SubCase', $request->sub_case);
        }

        $perPage = $this->resolvePerPage($request);

        $records  = $query->paginate($perPage)->withQueryString();
        $statuses = StatusProcess::orderBy('name')->get();

        $hearings = DistrictExecutionHearing::all();
        $hearingStats = [
            'total'     => $hearings->count(),
            'scheduled' => $hearings->where('status', 'Scheduled')->count(),
            'completed' => $hearings->where('status', 'Completed')->count(),
            'thisMonth' => $hearings->filter(fn($h) => \Carbon\Carbon::parse($h->hearing_date)->format('Y-m') === date('Y-m'))->count(),
        ];
        $executionSubCases = \App\Models\CaseCategory::where('case_name', 'Fulinta')->pluck('sub_case');

        return view('distract_courts.District_execution.hearing.district_execution_view_hearing_scripture', compact('records', 'statuses', 'hearingStats', 'executionSubCases'));
    }

    public function index()
    {
        $hearings = DistrictExecutionHearing::with('executionCase.court')->orderByDesc('hearing_date')->orderByDesc('hearing_time')->get();
        $cases    = DistrictExecutionRegistration::orderByDesc('ECID')->get(['ECID', 'FileNo', 'CaseType', 'GradeCourt']);
        $courts   = Court::orderBy('longName')->get();

        $calendarEvents = $hearings->map(function ($h) {
            $color = match($h->status) {
                'Completed'  => '#10B981',
                'Cancelled'  => '#DC2626',
                'Postponed'  => '#F0B43C',
                default      => '#528CBE',
            };
            return [
                'id'    => $h->id,
                'title' => ($h->executionCase->FileNo ?? '—'),
                'start' => $h->hearing_date->format('Y-m-d') . 'T' . $h->hearing_time,
                'color' => $color,
                'extendedProps' => [
                    'purpose'  => $h->hearing_purpose,
                    'status'   => $h->status,
                    'courtroom'=> $h->courtroom,
                    'caseId'   => $h->execution_case_id,
                ],
            ];
        })->values();

        $stats = [
            'total'     => $hearings->count(),
            'scheduled' => $hearings->where('status', 'Scheduled')->count(),
            'completed' => $hearings->where('status', 'Completed')->count(),
            'cancelled' => $hearings->where('status', 'Cancelled')->count(),
            'postponed' => $hearings->where('status', 'Postponed')->count(),
        ];

        return view('distract_courts.District_execution.hearing.district_execution_view_schedule', compact('hearings', 'cases', 'courts', 'calendarEvents', 'stats'));
    }

    public function viewIndex()
    {
        $hearings = DistrictExecutionHearing::with(['executionCase.court', 'executionCase.parties', 'executionCase.assignments.employee'])->orderBy('hearing_date')->orderBy('hearing_time')->get();
        $stats = [
            'total'     => $hearings->count(),
            'scheduled' => $hearings->where('status', 'Scheduled')->count(),
            'completed' => $hearings->where('status', 'Completed')->count(),
            'postponed' => $hearings->where('status', 'Postponed')->count(),
            'cancelled' => $hearings->where('status', 'Cancelled')->count(),
            'thisMonth' => $hearings->filter(fn($h) => \Carbon\Carbon::parse($h->hearing_date)->format('Y-m') === date('Y-m'))->count(),
        ];
        $executionSubCases = \App\Models\CaseCategory::where('case_name', 'Fulinta')->pluck('sub_case');
        return view('distract_courts.District_execution.hearing.district_execution_calendar_view', compact('hearings', 'stats', 'executionSubCases'));
    }

    public function create($caseId = null)
    {
        $cases        = DistrictExecutionRegistration::orderByDesc('ECID')->get(['ECID', 'FileNo', 'CaseType', 'GradeCourt']);
        $courts       = Court::orderBy('longName')->get();
        $statuses     = StatusProcess::orderBy('name')->get();
        $selectedCase = $caseId ? DistrictExecutionRegistration::with('court')->find($caseId) : null;
        $hearing      = null;
        $caseHearings = $caseId ? DistrictExecutionHearing::where('execution_case_id', $caseId)->orderBy('hearing_date')->get() : collect();
        return view('distract_courts.District_execution.hearing.district_execution_hearing_scheduling', compact('cases', 'courts', 'statuses', 'selectedCase', 'hearing', 'caseHearings'));
    }

    public function edit($id)
    {
        $hearing      = DistrictExecutionHearing::with('executionCase.court')->findOrFail($id);
        $cases        = DistrictExecutionRegistration::orderByDesc('ECID')->get(['ECID', 'FileNo', 'CaseType', 'GradeCourt']);
        $courts       = Court::orderBy('longName')->get();
        $statuses     = StatusProcess::orderBy('name')->get();
        $selectedCase = $hearing->executionCase;
        $caseHearings = DistrictExecutionHearing::where('execution_case_id', $hearing->execution_case_id)->orderBy('hearing_date')->get();
        return view('distract_courts.District_execution.hearing.district_execution_hearing_scheduling', compact('cases', 'courts', 'statuses', 'selectedCase', 'hearing', 'caseHearings'));
    }

    public function store(Request $request)
    {
        $isDraft = $request->input('action') === 'draft';

        $request->validate([
            'execution_case_id'  => 'required|exists:district_execution_registrations,ECID',
            'hearing_date'    => 'required|date',
            'hearing_time'    => 'required',
            'duration'        => 'nullable|string|max:10',
            'courtroom'       => 'nullable|string|max:50',
            'hearing_purpose' => 'nullable|string',
            'status'          => $isDraft ? 'nullable|string' : 'required|in:Scheduled,Completed,Cancelled,Postponed',
        ]);

        $data = $request->only('execution_case_id', 'hearing_date', 'hearing_time', 'duration', 'courtroom', 'hearing_purpose');
        $data['status']     = $isDraft ? 'Draft' : $request->input('status');
        $data['created_by'] = auth()->user()->name ?? 'Admin';

        $hearing = DistrictExecutionHearing::create($data);

        if ($isDraft) {
            return redirect()->route('execution-hearings.edit', $hearing->id)
                ->with('success', 'Mudeynta Draft ahaan ayaa lagu keydsaday. Waxaad dib u eegi kartaa.');
        }

        $stageStatus = StatusProcess::where('name', 'Mudeyn')->first();
        $newStatus   = $stageStatus?->name ?? 'Mudeyn';
        DistrictExecutionRegistration::where('ECID', $request->input('execution_case_id'))
            ->update(['Status' => $newStatus]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Hearing scheduled successfully.']);
        }
        return redirect()->route('execution.hearing.cases')->with('success', 'Hearing scheduled successfully.');
    }

    public function update(Request $request, $id)
    {
        $isDraft = $request->input('action') === 'draft';

        $request->validate([
            'execution_case_id'  => 'required|exists:district_execution_registrations,ECID',
            'hearing_date'    => 'required|date',
            'hearing_time'    => 'required',
            'duration'        => 'nullable|string|max:10',
            'courtroom'       => 'nullable|string|max:50',
            'hearing_purpose' => 'nullable|string',
            'status'          => $isDraft ? 'nullable|string' : 'required|in:Scheduled,Completed,Cancelled,Postponed',
        ]);

        $hearing = DistrictExecutionHearing::findOrFail($id);
        $data = $request->only('execution_case_id', 'hearing_date', 'hearing_time', 'duration', 'courtroom', 'hearing_purpose');
        $data['status'] = $isDraft ? 'Draft' : $request->input('status');

        $hearing->update($data);

        if ($isDraft) {
            return redirect()->route('execution-hearings.edit', $hearing->id)
                ->with('success', 'Mudeynta Draft ahaan ayaa lagu cusboonaysiiyay. Waxaad dib u eegi kartaa.');
        }

        $stageStatus = StatusProcess::where('name', 'Mudeyn')->first();
        $newStatus   = $stageStatus?->name ?? 'Mudeyn';
        DistrictExecutionRegistration::where('ECID', $request->input('execution_case_id'))
            ->update(['Status' => $newStatus]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Hearing updated successfully.']);
        }
        return redirect()->route('execution.hearing.cases')->with('success', 'Hearing updated successfully.');
    }

    public function documentByCase($caseId)
    {
        /** @var DistrictExecutionHearing $hearing */
        $hearing = DistrictExecutionHearing::with([
            'executionCase.court',
            'executionCase.parties',
            'executionCase.lawyers.lawyer',
            'executionCase.lawyers.party',
            'executionCase.assignments.employee',
        ])->where('execution_case_id', $caseId)
          ->orderByDesc('hearing_date')
          ->orderByDesc('hearing_time')
          ->firstOrFail();

        return view('distract_courts.District_execution.hearing.district_execution_hearing_document',
            array_merge(compact('hearing'), $this->hearingSignVars($hearing)));
    }

    public function createScripture($caseId)
    {
        $case      = DistrictExecutionRegistration::with('court')->findOrFail($caseId);
        $hearings  = DistrictExecutionHearing::where('execution_case_id', $caseId)
                        ->whereIn('status', ['Scheduled', 'Submitted', 'Confirmed'])
                        ->orderByDesc('hearing_date')->get();
        $scripture = null;
        return view('distract_courts.District_execution.hearing.district_execution_add_hearing_scripture', compact('case', 'hearings', 'scripture'));
    }

    public function storeScripture(Request $request)
    {
        $isDraft = $request->input('status', 'Draft') === 'Draft';

        if ($request->input('hearing_time')) {
            $request->merge(['hearing_time' => substr($request->input('hearing_time'), 0, 5)]);
        }
        if ($request->input('hearing_id') === '') {
            $request->merge(['hearing_id' => null]);
        }

        $request->validate([
            'execution_case_id' => 'required|integer',
            'hearing_id'     => $isDraft ? 'nullable' : 'required|exists:district_execution_hearings,id',
            'hearing_date'   => 'nullable|date',
            'hearing_time'   => 'nullable|date_format:H:i',
        ]);

        $scripture = DistrictExecutionHearingScripture::create([
            ...$request->only('execution_case_id','hearing_id','session_number','hearing_date','hearing_time',
                'courtroom','hearing_type','body_content'),
            'status'     => $isDraft ? 'Draft' : 'Submitted',
            'created_by' => auth()->user()->name ?? null,
        ]);

        if ($isDraft) {
            return redirect()->route('execution-hearings.scripture.edit', $scripture->id)
                ->with('success', 'Qoraalka ayaa Draft ahaan lagu keydsaday.');
        }

        $newStatus = StatusProcess::where('name', 'Dhageysi')->value('name') ?? 'Dhageysi';
        DistrictExecutionRegistration::where('ECID', $scripture->execution_case_id)
            ->update(['Status' => $newStatus]);

        return redirect()->route('execution-hearings.scripture')
            ->with('success', 'Garmaqalka si guul leh ayaa loo gudbiiyay.');
    }

    public function editScripture($id)
    {
        $scripture = DistrictExecutionHearingScripture::findOrFail($id);
        $case      = DistrictExecutionRegistration::with('court')->findOrFail($scripture->execution_case_id);
        $hearings  = DistrictExecutionHearing::where('execution_case_id', $scripture->execution_case_id)
                        ->whereIn('status', ['Scheduled', 'Submitted', 'Confirmed'])
                        ->orderByDesc('hearing_date')->get();
        return view('distract_courts.District_execution.hearing.district_execution_add_hearing_scripture', compact('case', 'hearings', 'scripture'));
    }

    public function updateScripture(Request $request, $id)
    {
        $isDraft = $request->input('status', 'Draft') === 'Draft';

        if ($request->input('hearing_time')) {
            $request->merge(['hearing_time' => substr($request->input('hearing_time'), 0, 5)]);
        }
        if ($request->input('hearing_id') === '') {
            $request->merge(['hearing_id' => null]);
        }

        $request->validate([
            'hearing_id'   => $isDraft ? 'nullable' : 'required|exists:district_execution_hearings,id',
            'hearing_date' => 'nullable|date',
            'hearing_time' => 'nullable|date_format:H:i',
        ]);

        $scripture = DistrictExecutionHearingScripture::findOrFail($id);
        $scripture->update([
            ...$request->only('hearing_id','session_number','hearing_date','hearing_time',
                'courtroom','hearing_type','body_content'),
            'status' => $isDraft ? 'Draft' : 'Submitted',
        ]);

        if ($isDraft) {
            return redirect()->route('execution-hearings.scripture.edit', $scripture->id)
                ->with('success', 'Qoraalka ayaa Draft ahaan lagu cusboonaysiiyay.');
        }

        $newStatus = StatusProcess::where('name', 'Dhageysi')->value('name') ?? 'Dhageysi';
        DistrictExecutionRegistration::where('ECID', $scripture->execution_case_id)
            ->update(['Status' => $newStatus]);

        return redirect()->route('execution-hearings.scripture')
            ->with('success', 'Garmaqalka si guul leh ayaa loo cusboonaysiiyay.');
    }

    public function scriptureDocument($id)
    {
        $scripture = DistrictExecutionHearingScripture::with([
            'executionCase.court',
            'executionCase.parties',
            'executionCase.lawyers.lawyer',
            'executionCase.lawyers.party',
            'executionCase.legalRepresentatives',
            'executionCase.assignments.employee',
            'hearing',
        ])->findOrFail($id);

        $case  = $scripture->executionCase;
        $court = $case->court;

        $judge = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()
              ?? $case->assignments->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        // Load digital signatures for this scripture
        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'execution_scripture')
            ->where('document_id', $scripture->id)
            ->get()
            ->keyBy('role');

        $judgeSig   = $signatures['judge'] ?? null;
        $clerkSig   = $signatures['clerk'] ?? null;
        $isComplete = $judgeSig && $clerkSig;

        // Determine current user's signing role
        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        $iAmJudge = $myEmployee && $judge?->employee &&
                    (int) $judge->employee->AID === (int) $myEmployee->AID;
        $iAmClerk = $myEmployee && $clerk?->employee &&
                    (int) $clerk->employee->AID === (int) $myEmployee->AID;

        // Fallback: match via assignment employee_id if direct relationship comparison failed
        if (!$iAmJudge && !$iAmClerk && $myEmployee) {
            $myAssignment = $case->assignments
                ->whereIn('panel_role', ['Chair', 'Guddoomiye', 'Clerk', 'Kaaliye'])
                ->first(fn($a) => (int) $a->employee_id === (int) $myEmployee->AID);
            if ($myAssignment) {
                $iAmJudge = in_array($myAssignment->panel_role, ['Chair', 'Guddoomiye']);
                $iAmClerk = !$iAmJudge;
            }
        }

        $myRole = match (true) {
            $iAmJudge => 'judge',
            $iAmClerk => 'clerk',
            default   => null,
        };

        $myAlreadySigned = match (true) {
            $iAmJudge => (bool) $judgeSig,
            $iAmClerk => (bool) $clerkSig,
            default   => false,
        };

        return view('distract_courts.District_execution.hearing.district_execution_document_hearing_scripture',
            compact('scripture', 'case', 'court', 'judge', 'clerk',
                    'signatures', 'judgeSig', 'clerkSig', 'isComplete',
                    'myRole', 'myAlreadySigned'));
    }

    public function document($id)
    {
        $hearing = DistrictExecutionHearing::with([
            'executionCase.court',
            'executionCase.parties',
            'executionCase.lawyers.lawyer',
            'executionCase.lawyers.party',
            'executionCase.assignments.employee',
        ])->findOrFail($id);

        return view('distract_courts.District_execution.hearing.district_execution_hearing_document',
            array_merge(compact('hearing'), $this->hearingSignVars($hearing)));
    }

    public function documentPdf($id)
    {
        $hearing = DistrictExecutionHearing::with([
            'executionCase.court',
            'executionCase.parties',
            'executionCase.lawyers.lawyer',
            'executionCase.lawyers.party',
            'executionCase.assignments.employee',
        ])->findOrFail($id);

        $data = array_merge(compact('hearing'), $this->hearingSignVars($hearing));

        return \App\Support\CourtDocumentPdf::stream(
            'distract_courts.District_execution.hearing.district_execution_hearing_document',
            $data,
            'Hearing-' . $hearing->executionCase->FileNo . '.pdf'
        );
    }

    public function approvalStampDocument($id)
    {
        $hearing = DistrictExecutionHearing::with([
            'executionCase.court',
            'executionCase.parties',
            'executionCase.assignments.employee',
        ])->findOrFail($id);

        $case  = $hearing->executionCase;
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'execution_hearing_stamp')
            ->where('document_id', $hearing->id)
            ->get()
            ->keyBy('role');

        $clerkSig   = $signatures['kaaliye'] ?? null;
        $archiveSig = $signatures['archive_officer'] ?? null;

        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        // Is the current user the Kaaliye? Check via assignment OR via who actually signed
        $iAmKaaliye = $myEmployee && (
            ($clerk && $clerk->employee && (int) $clerk->employee->AID === (int) $myEmployee->AID) ||
            ($clerkSig && (int) $clerkSig->signer_id === (int) $myEmployee->AID)
        );

        // Role: kaaliye if they are the clerk; archive_officer for everyone else (until approved)
        if ($iAmKaaliye) {
            $myRole = 'kaaliye';
        } elseif (!$archiveSig) {
            $myRole = 'archive_officer';
        } else {
            $myRole = null;
        }

        $myAlreadySigned = $iAmKaaliye
            ? (bool) $clerkSig
            : (bool) $archiveSig;

        return view('distract_courts.Archive.district_execution_approval_stamp_document',
            compact('hearing', 'case', 'clerk', 'signatures',
                    'clerkSig', 'archiveSig',
                    'myEmployee', 'myRole', 'myAlreadySigned'));
    }

    private function hearingSignVars(DistrictExecutionHearing $hearing): array
    {
        $case  = $hearing->executionCase;
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'execution_hearing')
            ->where('document_id', $hearing->id)
            ->get()
            ->keyBy('role');

        $clerkSig = $signatures['clerk'] ?? $signatures['signer'] ?? null;

        // Stamp only appears after archive officer has fully approved
        $approvalSigs   = DocumentSignature::where('document_type', 'execution_hearing_stamp')
            ->where('document_id', $hearing->id)
            ->pluck('role');
        $stampRequested = $approvalSigs->contains('kaaliye');
        $stampApproved  = $stampRequested && $approvalSigs->contains('archive_officer');

        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        $canSign         = $myEmployee && $myEmployee->signature;
        $myAlreadySigned = $canSign && $signatures->contains('signer_id', $myEmployee->AID);

        $myRole = null;
        if ($myEmployee) {
            if ($clerk && $clerk->employee && $clerk->employee->AID === $myEmployee->AID) {
                $myRole = 'clerk';
            } elseif ($myEmployee->EmpName === $hearing->created_by) {
                $myRole = 'registrar';
            } else {
                $myRole = 'signer';
            }
        }

        // Generated server-side (not client-side JS) so it also renders inside
        // the Dompdf-produced PDF, which cannot execute JavaScript.
        $qrDataUri = (new Builder(
            writer: new PngWriter(),
            data: $case->FileNo,
            size: 150,
            margin: 0,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            foregroundColor: new Color(10, 40, 77),
            backgroundColor: new Color(255, 255, 255),
        ))->build()->getDataUri();

        return compact('clerk', 'signatures', 'clerkSig', 'stampApproved', 'stampRequested', 'myEmployee', 'myRole', 'canSign', 'myAlreadySigned', 'qrDataUri');
    }

    public function hearingsByCase($caseId)
    {
        $hearings = DistrictExecutionHearing::where('execution_case_id', $caseId)
            ->orderBy('hearing_date')
            ->get(['id','hearing_date','hearing_time','duration','hearing_purpose','status']);
        return response()->json($hearings);
    }

    public function destroy($id)
    {
        DistrictExecutionHearing::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Hearing deleted successfully.']);
    }
}
