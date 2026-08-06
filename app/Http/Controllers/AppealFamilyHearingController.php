<?php

namespace App\Http\Controllers;

use App\Models\AppealFamilyHearing;
use App\Models\AppealFamilyHearingScripture;
use App\Models\AppealFamilyRegistration;
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

class AppealFamilyHearingController extends Controller
{
    public function hearingCases(Request $request)
    {
        $query = AppealFamilyRegistration::with('parties', 'court', 'hearings')->orderByDesc('AFCID');

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

        $perPage = $this->resolvePerPage($request);

        $records  = $query->paginate($perPage)->withQueryString();
        $statuses = StatusProcess::orderBy('name')->get();
        $court    = Court::first();

        $hearings     = AppealFamilyHearing::all();
        $hearingStats = [
            'total'     => $hearings->count(),
            'scheduled' => $hearings->where('status', 'Scheduled')->count(),
            'completed' => $hearings->where('status', 'Completed')->count(),
            'thisMonth' => $hearings->filter(fn($h) => \Carbon\Carbon::parse($h->hearing_date)->format('Y-m') === date('Y-m'))->count(),
        ];

        return view('appeal_court.Appeal_family.hearing.appeal_family_view_hearing_Cases', compact('records', 'statuses', 'court', 'hearingStats'));
    }

    public function hearingScripture(Request $request)
    {
        $query = AppealFamilyRegistration::with('court')->orderByDesc('AFCID');

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

        $perPage = $this->resolvePerPage($request);

        $records  = $query->paginate($perPage)->withQueryString();
        $statuses = StatusProcess::orderBy('name')->get();

        $hearings     = AppealFamilyHearing::all();
        $hearingStats = [
            'total'     => $hearings->count(),
            'scheduled' => $hearings->where('status', 'Scheduled')->count(),
            'completed' => $hearings->where('status', 'Completed')->count(),
            'thisMonth' => $hearings->filter(fn($h) => \Carbon\Carbon::parse($h->hearing_date)->format('Y-m') === date('Y-m'))->count(),
        ];

        return view('appeal_court.Appeal_family.hearing.appeal_family_view_hearing_scripture', compact('records', 'statuses', 'hearingStats'));
    }

    public function index()
    {
        $hearings = AppealFamilyHearing::with('familyCase.court')->orderByDesc('hearing_date')->orderByDesc('hearing_time')->get();
        $cases    = AppealFamilyRegistration::orderByDesc('AFCID')->get(['AFCID', 'FileNo', 'CaseType', 'GradeCourt']);
        $courts   = Court::orderBy('longName')->get();

        $calendarEvents = $hearings->map(function ($h) {
            $color = match($h->status) {
                'Completed' => '#10B981',
                'Cancelled' => '#DC2626',
                'Postponed' => '#F0B43C',
                default     => '#528CBE',
            };
            return [
                'id'    => $h->id,
                'title' => ($h->familyCase->FileNo ?? '—'),
                'start' => $h->hearing_date->format('Y-m-d') . 'T' . $h->hearing_time,
                'color' => $color,
                'extendedProps' => [
                    'purpose'   => $h->hearing_purpose,
                    'status'    => $h->status,
                    'courtroom' => $h->courtroom,
                    'caseId'    => $h->family_case_id,
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

        return view('appeal_court.Appeal_family.hearing.appeal_family_view_schedule', compact('hearings', 'cases', 'courts', 'calendarEvents', 'stats'));
    }

    public function viewIndex()
    {
        $hearings = AppealFamilyHearing::with(['familyCase.court', 'familyCase.parties', 'familyCase.assignments.employee'])->orderBy('hearing_date')->orderBy('hearing_time')->get();
        $stats = [
            'total'     => $hearings->count(),
            'scheduled' => $hearings->where('status', 'Scheduled')->count(),
            'completed' => $hearings->where('status', 'Completed')->count(),
            'postponed' => $hearings->where('status', 'Postponed')->count(),
            'cancelled' => $hearings->where('status', 'Cancelled')->count(),
            'thisMonth' => $hearings->filter(fn($h) => \Carbon\Carbon::parse($h->hearing_date)->format('Y-m') === date('Y-m'))->count(),
        ];
        return view('appeal_court.Appeal_family.hearing.appeal_family_calendar_view', compact('hearings', 'stats'));
    }

    public function create($caseId = null)
    {
        $cases        = AppealFamilyRegistration::orderByDesc('AFCID')->get(['AFCID', 'FileNo', 'CaseType', 'GradeCourt']);
        $courts       = Court::orderBy('longName')->get();
        $statuses     = StatusProcess::orderBy('name')->get();
        $selectedCase = $caseId ? AppealFamilyRegistration::with('court')->find($caseId) : null;
        $hearing      = null;
        $caseHearings = $caseId ? AppealFamilyHearing::where('family_case_id', $caseId)->orderBy('hearing_date')->get() : collect();
        return view('appeal_court.Appeal_family.hearing.appeal_family_hearing_scheduling', compact('cases', 'courts', 'statuses', 'selectedCase', 'hearing', 'caseHearings'));
    }

    public function edit($id)
    {
        $hearing      = AppealFamilyHearing::with('familyCase.court')->findOrFail($id);
        $cases        = AppealFamilyRegistration::orderByDesc('AFCID')->get(['AFCID', 'FileNo', 'CaseType', 'GradeCourt']);
        $courts       = Court::orderBy('longName')->get();
        $statuses     = StatusProcess::orderBy('name')->get();
        $selectedCase = $hearing->familyCase;
        $caseHearings = AppealFamilyHearing::where('family_case_id', $hearing->family_case_id)->orderBy('hearing_date')->get();
        return view('appeal_court.Appeal_family.hearing.appeal_family_hearing_scheduling', compact('cases', 'courts', 'statuses', 'selectedCase', 'hearing', 'caseHearings'));
    }

    public function store(Request $request)
    {
        $isDraft = $request->input('action') === 'draft';

        $request->validate([
            'family_case_id'  => 'required|exists:appeal_family_registrations,AFCID',
            'hearing_date'    => 'required|date',
            'hearing_time'    => 'required',
            'duration'        => 'nullable|string|max:10',
            'courtroom'       => 'nullable|string|max:50',
            'hearing_purpose' => 'nullable|string',
            'status'          => $isDraft ? 'nullable|string' : 'required|in:Scheduled,Completed,Cancelled,Postponed',
        ]);

        $data = $request->only('family_case_id', 'hearing_date', 'hearing_time', 'duration', 'courtroom', 'hearing_purpose');
        $data['status']     = $isDraft ? 'Draft' : $request->input('status');
        $data['created_by'] = auth()->user()->name ?? 'Admin';

        $hearing = AppealFamilyHearing::create($data);

        if ($isDraft) {
            return redirect()->route('appeal-family-hearings.edit', $hearing->id)
                ->with('success', 'Mudeynta Draft ahaan ayaa lagu keydsaday. Waxaad dib u eegi kartaa.');
        }

        $stageStatus = StatusProcess::where('name', 'Mudeyn')->first();
        $newStatus   = $stageStatus?->name ?? 'Mudeyn';
        AppealFamilyRegistration::where('AFCID', $request->input('family_case_id'))
            ->update(['Status' => $newStatus]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Hearing scheduled successfully.']);
        }
        return redirect()->route('appeal-family-hearings.index')->with('success', 'Hearing scheduled successfully.');
    }

    public function update(Request $request, $id)
    {
        $isDraft = $request->input('action') === 'draft';

        $request->validate([
            'family_case_id'  => 'required|exists:appeal_family_registrations,AFCID',
            'hearing_date'    => 'required|date',
            'hearing_time'    => 'required',
            'duration'        => 'nullable|string|max:10',
            'courtroom'       => 'nullable|string|max:50',
            'hearing_purpose' => 'nullable|string',
            'status'          => $isDraft ? 'nullable|string' : 'required|in:Scheduled,Completed,Cancelled,Postponed',
        ]);

        $hearing = AppealFamilyHearing::findOrFail($id);
        $data    = $request->only('family_case_id', 'hearing_date', 'hearing_time', 'duration', 'courtroom', 'hearing_purpose');
        $data['status'] = $isDraft ? 'Draft' : $request->input('status');
        $hearing->update($data);

        if ($isDraft) {
            return redirect()->route('appeal-family-hearings.edit', $hearing->id)
                ->with('success', 'Mudeynta Draft ahaan ayaa lagu cusboonaysiiyay. Waxaad dib u eegi kartaa.');
        }

        $stageStatus = StatusProcess::where('name', 'Mudeyn')->first();
        $newStatus   = $stageStatus?->name ?? 'Mudeyn';
        AppealFamilyRegistration::where('AFCID', $request->input('family_case_id'))
            ->update(['Status' => $newStatus]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Hearing updated successfully.']);
        }
        return redirect()->route('appeal-family-hearings.index')->with('success', 'Hearing updated successfully.');
    }

    public function createScripture($caseId)
    {
        $case      = AppealFamilyRegistration::with('court')->findOrFail($caseId);
        $hearings  = AppealFamilyHearing::where('family_case_id', $caseId)
                        ->whereIn('status', ['Scheduled', 'Submitted', 'Confirmed'])
                        ->orderByDesc('hearing_date')->get();
        $scripture = null;
        return view('appeal_court.Appeal_family.hearing.appeal_family_add_hearing_scripture', compact('case', 'hearings', 'scripture'));
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
            'family_case_id' => 'required|integer',
            'hearing_id'     => $isDraft ? 'nullable' : 'required|exists:appeal_family_hearings,id',
            'hearing_date'   => 'nullable|date',
            'hearing_time'   => 'nullable|date_format:H:i',
        ]);

        $scripture = AppealFamilyHearingScripture::create([
            ...$request->only('family_case_id','hearing_id','session_number','hearing_date','hearing_time',
                'courtroom','hearing_type','body_content'),
            'status'     => $isDraft ? 'Draft' : 'Submitted',
            'created_by' => auth()->user()->name ?? null,
        ]);

        if ($isDraft) {
            return redirect()->route('appeal-family-hearings.scripture.edit', $scripture->id)
                ->with('success', 'Qoraalka ayaa Draft ahaan lagu keydsaday.');
        }

        $newStatus = StatusProcess::where('name', 'Dhageysi')->value('name') ?? 'Dhageysi';
        AppealFamilyRegistration::where('AFCID', $scripture->family_case_id)
            ->update(['Status' => $newStatus]);

        return redirect()->route('appeal-family-hearings.scripture')
            ->with('success', 'Garmaqalka si guul leh ayaa loo gudbiiyay.');
    }

    public function editScripture($id)
    {
        $scripture = AppealFamilyHearingScripture::findOrFail($id);
        $case      = AppealFamilyRegistration::with('court')->findOrFail($scripture->family_case_id);
        $hearings  = AppealFamilyHearing::where('family_case_id', $scripture->family_case_id)
                        ->whereIn('status', ['Scheduled', 'Submitted', 'Confirmed'])
                        ->orderByDesc('hearing_date')->get();
        return view('appeal_court.Appeal_family.hearing.appeal_family_add_hearing_scripture', compact('case', 'hearings', 'scripture'));
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
            'hearing_id'   => $isDraft ? 'nullable' : 'required|exists:appeal_family_hearings,id',
            'hearing_date' => 'nullable|date',
            'hearing_time' => 'nullable|date_format:H:i',
        ]);

        $scripture = AppealFamilyHearingScripture::findOrFail($id);
        $scripture->update([
            ...$request->only('hearing_id','session_number','hearing_date','hearing_time',
                'courtroom','hearing_type','body_content'),
            'status' => $isDraft ? 'Draft' : 'Submitted',
        ]);

        if ($isDraft) {
            return redirect()->route('appeal-family-hearings.scripture.edit', $scripture->id)
                ->with('success', 'Qoraalka ayaa Draft ahaan lagu cusboonaysiiyay.');
        }

        $newStatus = StatusProcess::where('name', 'Dhageysi')->value('name') ?? 'Dhageysi';
        AppealFamilyRegistration::where('AFCID', $scripture->family_case_id)
            ->update(['Status' => $newStatus]);

        return redirect()->route('appeal-family-hearings.scripture')
            ->with('success', 'Garmaqalka si guul leh ayaa loo cusboonaysiiyay.');
    }

    public function document($id)
    {
        $data = $this->hearingDocumentData($id);

        return view('appeal_court.Appeal_family.hearing.appeal_family_hearing_document', $data);
    }

    public function documentPdf($id)
    {
        $data = $this->hearingDocumentData($id);

        return \App\Support\CourtDocumentPdf::stream(
            'appeal_court.Appeal_family.hearing.appeal_family_hearing_document',
            $data,
            'Appeal-Family-Hearing-' . $data['hearing']->familyCase->FileNo . '.pdf'
        );
    }

    private function hearingDocumentData($id): array
    {
        $hearing = AppealFamilyHearing::with([
            'familyCase.court',
            'familyCase.parties',
            'familyCase.legalRepresentatives',
            'familyCase.lawyers.lawyer',
            'familyCase.assignments.employee',
        ])->findOrFail($id);

        $case  = $hearing->familyCase;
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'appeal_family_hearing')
            ->where('document_id', $hearing->id)
            ->get()
            ->keyBy('role');

        $clerkSig = $signatures['clerk'] ?? $signatures['signer'] ?? null;

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

        return compact('hearing', 'clerk', 'signatures', 'clerkSig', 'myEmployee', 'myRole', 'canSign', 'myAlreadySigned', 'qrDataUri');
    }

    public function scriptureDocument($id)
    {
        $scripture = AppealFamilyHearingScripture::with([
            'familyCase.court',
            'familyCase.parties',
            'familyCase.legalRepresentatives',
            'familyCase.assignments.employee',
            'hearing',
        ])->findOrFail($id);

        $case  = $scripture->familyCase;
        $court = $case->court;

        $judge = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()
              ?? $case->assignments->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'appeal_family_scripture')
            ->where('document_id', $scripture->id)
            ->get()
            ->keyBy('role');

        $judgeSig   = $signatures['judge'] ?? null;
        $clerkSig   = $signatures['clerk'] ?? null;
        $isComplete = $judgeSig && $clerkSig;

        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        $iAmJudge = $myEmployee && $judge?->employee &&
                    (int) $judge->employee->AID === (int) $myEmployee->AID;
        $iAmClerk = $myEmployee && $clerk?->employee &&
                    (int) $clerk->employee->AID === (int) $myEmployee->AID;

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

        return view('appeal_court.Appeal_family.hearing.appeal_family_document_hearing_scripture',
            compact('scripture', 'case', 'court', 'judge', 'clerk',
                    'signatures', 'judgeSig', 'clerkSig', 'isComplete',
                    'myRole', 'myAlreadySigned'));
    }

    public function hearingsByCase($caseId)
    {
        $hearings = AppealFamilyHearing::where('family_case_id', $caseId)
            ->orderBy('hearing_date')
            ->get(['id','hearing_date','hearing_time','duration','hearing_purpose','status']);
        return response()->json($hearings);
    }

    public function destroy($id)
    {
        AppealFamilyHearing::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Hearing deleted successfully.']);
    }
}
