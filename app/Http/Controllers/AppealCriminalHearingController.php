<?php

namespace App\Http\Controllers;

use App\Models\AppealCriminalHearing;
use App\Models\AppealCriminalHearingScripture;
use App\Models\AppealCriminalRegistration;
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

class AppealCriminalHearingController extends Controller
{
    public function hearingCases(Request $request)
    {
        $query = AppealCriminalRegistration::with('parties', 'court', 'hearings')->orderByDesc('ACMID');

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

        $hearings     = AppealCriminalHearing::all();
        $hearingStats = [
            'total'     => $hearings->count(),
            'scheduled' => $hearings->where('status', 'Scheduled')->count(),
            'completed' => $hearings->where('status', 'Completed')->count(),
            'thisMonth' => $hearings->filter(fn($h) => \Carbon\Carbon::parse($h->hearing_date)->format('Y-m') === date('Y-m'))->count(),
        ];

        return view('appeal_court.Appeal_criminal.hearing.appeal_criminal_view_hearing_Cases', compact('records', 'statuses', 'court', 'hearingStats'));
    }

    public function hearingScripture(Request $request)
    {
        $query = AppealCriminalRegistration::with('court')->orderByDesc('ACMID');

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

        $hearings     = AppealCriminalHearing::all();
        $hearingStats = [
            'total'     => $hearings->count(),
            'scheduled' => $hearings->where('status', 'Scheduled')->count(),
            'completed' => $hearings->where('status', 'Completed')->count(),
            'thisMonth' => $hearings->filter(fn($h) => \Carbon\Carbon::parse($h->hearing_date)->format('Y-m') === date('Y-m'))->count(),
        ];

        return view('appeal_court.Appeal_criminal.hearing.appeal_criminal_view_hearing_scripture', compact('records', 'statuses', 'hearingStats'));
    }

    public function index()
    {
        $hearings = AppealCriminalHearing::with('criminalCase.court')->orderByDesc('hearing_date')->orderByDesc('hearing_time')->get();
        $cases    = AppealCriminalRegistration::orderByDesc('ACMID')->get(['ACMID', 'FileNo', 'CaseType', 'GradeCourt']);
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
                'title' => ($h->criminalCase->FileNo ?? '—'),
                'start' => $h->hearing_date->format('Y-m-d') . 'T' . $h->hearing_time,
                'color' => $color,
                'extendedProps' => [
                    'purpose'   => $h->hearing_purpose,
                    'status'    => $h->status,
                    'courtroom' => $h->courtroom,
                    'caseId'    => $h->criminal_case_id,
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

        return view('appeal_court.Appeal_criminal.hearing.appeal_criminal_view_schedule', compact('hearings', 'cases', 'courts', 'calendarEvents', 'stats'));
    }

    public function viewIndex()
    {
        $hearings = AppealCriminalHearing::with(['criminalCase.court', 'criminalCase.parties', 'criminalCase.assignments.employee'])->orderBy('hearing_date')->orderBy('hearing_time')->get();
        $stats = [
            'total'     => $hearings->count(),
            'scheduled' => $hearings->where('status', 'Scheduled')->count(),
            'completed' => $hearings->where('status', 'Completed')->count(),
            'postponed' => $hearings->where('status', 'Postponed')->count(),
            'cancelled' => $hearings->where('status', 'Cancelled')->count(),
            'thisMonth' => $hearings->filter(fn($h) => \Carbon\Carbon::parse($h->hearing_date)->format('Y-m') === date('Y-m'))->count(),
        ];
        return view('appeal_court.Appeal_criminal.hearing.appeal_criminal_calendar_view', compact('hearings', 'stats'));
    }

    public function create($caseId = null)
    {
        $cases        = AppealCriminalRegistration::orderByDesc('ACMID')->get(['ACMID', 'FileNo', 'CaseType', 'GradeCourt']);
        $courts       = Court::orderBy('longName')->get();
        $statuses     = StatusProcess::orderBy('name')->get();
        $selectedCase = $caseId ? AppealCriminalRegistration::with('court')->find($caseId) : null;
        $hearing      = null;
        $caseHearings = $caseId ? AppealCriminalHearing::where('criminal_case_id', $caseId)->orderBy('hearing_date')->get() : collect();
        return view('appeal_court.Appeal_criminal.hearing.appeal_criminal_hearing_scheduling', compact('cases', 'courts', 'statuses', 'selectedCase', 'hearing', 'caseHearings'));
    }

    public function edit($id)
    {
        $hearing      = AppealCriminalHearing::with('criminalCase.court')->findOrFail($id);
        $cases        = AppealCriminalRegistration::orderByDesc('ACMID')->get(['ACMID', 'FileNo', 'CaseType', 'GradeCourt']);
        $courts       = Court::orderBy('longName')->get();
        $statuses     = StatusProcess::orderBy('name')->get();
        $selectedCase = $hearing->criminalCase;
        $caseHearings = AppealCriminalHearing::where('criminal_case_id', $hearing->criminal_case_id)->orderBy('hearing_date')->get();
        return view('appeal_court.Appeal_criminal.hearing.appeal_criminal_hearing_scheduling', compact('cases', 'courts', 'statuses', 'selectedCase', 'hearing', 'caseHearings'));
    }

    public function store(Request $request)
    {
        $isDraft = $request->input('action') === 'draft';

        $request->validate([
            'criminal_case_id' => 'required|exists:appeal_criminal_registrations,ACMID',
            'hearing_date'     => 'required|date',
            'hearing_time'     => 'required',
            'duration'         => 'nullable|string|max:10',
            'courtroom'        => 'nullable|string|max:50',
            'hearing_purpose'  => 'nullable|string',
            'status'           => $isDraft ? 'nullable|string' : 'required|in:Scheduled,Completed,Cancelled,Postponed',
        ]);

        $data = $request->only('criminal_case_id', 'hearing_date', 'hearing_time', 'duration', 'courtroom', 'hearing_purpose');
        $data['status']     = $isDraft ? 'Draft' : $request->input('status');
        $data['created_by'] = auth()->user()->name ?? 'Admin';

        $hearing = AppealCriminalHearing::create($data);

        if ($isDraft) {
            return redirect()->route('appeal-criminal-hearings.edit', $hearing->id)
                ->with('success', 'Mudeynta Draft ahaan ayaa lagu keydsaday. Waxaad dib u eegi kartaa.');
        }

        $stageStatus = StatusProcess::where('name', 'Mudeyn')->first();
        $newStatus   = $stageStatus?->name ?? 'Mudeyn';
        AppealCriminalRegistration::where('ACMID', $request->input('criminal_case_id'))
            ->update(['Status' => $newStatus]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Hearing scheduled successfully.']);
        }
        return redirect()->route('appeal-criminal-hearings.index')->with('success', 'Hearing scheduled successfully.');
    }

    public function update(Request $request, $id)
    {
        $isDraft = $request->input('action') === 'draft';

        $request->validate([
            'criminal_case_id' => 'required|exists:appeal_criminal_registrations,ACMID',
            'hearing_date'     => 'required|date',
            'hearing_time'     => 'required',
            'duration'         => 'nullable|string|max:10',
            'courtroom'        => 'nullable|string|max:50',
            'hearing_purpose'  => 'nullable|string',
            'status'           => $isDraft ? 'nullable|string' : 'required|in:Scheduled,Completed,Cancelled,Postponed',
        ]);

        $hearing = AppealCriminalHearing::findOrFail($id);
        $data    = $request->only('criminal_case_id', 'hearing_date', 'hearing_time', 'duration', 'courtroom', 'hearing_purpose');
        $data['status'] = $isDraft ? 'Draft' : $request->input('status');
        $hearing->update($data);

        if ($isDraft) {
            return redirect()->route('appeal-criminal-hearings.edit', $hearing->id)
                ->with('success', 'Mudeynta Draft ahaan ayaa lagu cusboonaysiiyay. Waxaad dib u eegi kartaa.');
        }

        $stageStatus = StatusProcess::where('name', 'Mudeyn')->first();
        $newStatus   = $stageStatus?->name ?? 'Mudeyn';
        AppealCriminalRegistration::where('ACMID', $request->input('criminal_case_id'))
            ->update(['Status' => $newStatus]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Hearing updated successfully.']);
        }
        return redirect()->route('appeal-criminal-hearings.index')->with('success', 'Hearing updated successfully.');
    }

    public function createScripture($caseId)
    {
        $case      = AppealCriminalRegistration::with('court')->findOrFail($caseId);
        $hearings  = AppealCriminalHearing::where('criminal_case_id', $caseId)
                        ->whereIn('status', ['Scheduled', 'Submitted', 'Confirmed'])
                        ->orderByDesc('hearing_date')->get();
        $scripture = null;
        return view('appeal_court.Appeal_criminal.hearing.appeal_criminal_add_hearing_scripture', compact('case', 'hearings', 'scripture'));
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
            'criminal_case_id' => 'required|integer',
            'hearing_id'       => $isDraft ? 'nullable' : 'required|exists:appeal_criminal_hearings,id',
            'hearing_date'     => 'nullable|date',
            'hearing_time'     => 'nullable|date_format:H:i',
        ]);

        $scripture = AppealCriminalHearingScripture::create([
            ...$request->only('criminal_case_id','hearing_id','session_number','hearing_date','hearing_time',
                'courtroom','hearing_type','body_content'),
            'status'     => $isDraft ? 'Draft' : 'Submitted',
            'created_by' => auth()->user()->name ?? null,
        ]);

        if ($isDraft) {
            return redirect()->route('appeal-criminal-hearings.scripture.edit', $scripture->id)
                ->with('success', 'Qoraalka ayaa Draft ahaan lagu keydsaday.');
        }

        $newStatus = StatusProcess::where('name', 'Dhageysi')->value('name') ?? 'Dhageysi';
        AppealCriminalRegistration::where('ACMID', $scripture->criminal_case_id)
            ->update(['Status' => $newStatus]);

        return redirect()->route('appeal-criminal-hearings.scripture')
            ->with('success', 'Garmaqalka si guul leh ayaa loo gudbiiyay.');
    }

    public function editScripture($id)
    {
        $scripture = AppealCriminalHearingScripture::findOrFail($id);
        $case      = AppealCriminalRegistration::with('court')->findOrFail($scripture->criminal_case_id);
        $hearings  = AppealCriminalHearing::where('criminal_case_id', $scripture->criminal_case_id)
                        ->whereIn('status', ['Scheduled', 'Submitted', 'Confirmed'])
                        ->orderByDesc('hearing_date')->get();
        return view('appeal_court.Appeal_criminal.hearing.appeal_criminal_add_hearing_scripture', compact('case', 'hearings', 'scripture'));
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
            'hearing_id'   => $isDraft ? 'nullable' : 'required|exists:appeal_criminal_hearings,id',
            'hearing_date' => 'nullable|date',
            'hearing_time' => 'nullable|date_format:H:i',
        ]);

        $scripture = AppealCriminalHearingScripture::findOrFail($id);
        $scripture->update([
            ...$request->only('hearing_id','session_number','hearing_date','hearing_time',
                'courtroom','hearing_type','body_content'),
            'status' => $isDraft ? 'Draft' : 'Submitted',
        ]);

        if ($isDraft) {
            return redirect()->route('appeal-criminal-hearings.scripture.edit', $scripture->id)
                ->with('success', 'Qoraalka ayaa Draft ahaan lagu cusboonaysiiyay.');
        }

        $newStatus = StatusProcess::where('name', 'Dhageysi')->value('name') ?? 'Dhageysi';
        AppealCriminalRegistration::where('ACMID', $scripture->criminal_case_id)
            ->update(['Status' => $newStatus]);

        return redirect()->route('appeal-criminal-hearings.scripture')
            ->with('success', 'Garmaqalka si guul leh ayaa loo cusboonaysiiyay.');
    }

    public function document($id)
    {
        $data = $this->hearingDocumentData($id);

        return view('appeal_court.Appeal_criminal.hearing.appeal_criminal_hearing_document', $data);
    }

    public function documentPdf($id)
    {
        $data = $this->hearingDocumentData($id);

        return \App\Support\CourtDocumentPdf::stream(
            'appeal_court.Appeal_criminal.hearing.appeal_criminal_hearing_document',
            $data,
            'Appeal-Criminal-Hearing-' . $data['hearing']->criminalCase->FileNo . '.pdf'
        );
    }

    private function hearingDocumentData($id): array
    {
        $hearing = AppealCriminalHearing::with([
            'criminalCase.court',
            'criminalCase.parties',
            'criminalCase.legalRepresentatives',
            'criminalCase.lawyers.lawyer',
            'criminalCase.assignments.employee',
        ])->findOrFail($id);

        $case  = $hearing->criminalCase;
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'appeal_criminal_hearing')
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
        $scripture = AppealCriminalHearingScripture::with([
            'criminalCase.court',
            'criminalCase.parties',
            'criminalCase.legalRepresentatives',
            'criminalCase.assignments.employee',
            'hearing',
        ])->findOrFail($id);

        $case  = $scripture->criminalCase;
        $court = $case->court;

        $judge = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first()
              ?? $case->assignments->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = DocumentSignature::with('signer')
            ->where('document_type', 'appeal_criminal_scripture')
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

        return view('appeal_court.Appeal_criminal.hearing.appeal_criminal_document_hearing_scripture',
            compact('scripture', 'case', 'court', 'judge', 'clerk',
                    'signatures', 'judgeSig', 'clerkSig', 'isComplete',
                    'myRole', 'myAlreadySigned'));
    }

    public function hearingsByCase($caseId)
    {
        $hearings = AppealCriminalHearing::where('criminal_case_id', $caseId)
            ->orderBy('hearing_date')
            ->get(['id','hearing_date','hearing_time','duration','hearing_purpose','status']);
        return response()->json($hearings);
    }

    public function destroy($id)
    {
        AppealCriminalHearing::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Hearing deleted successfully.']);
    }
}
