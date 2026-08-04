<?php

namespace App\Http\Controllers;

use App\Mail\ProsecutorAssignmentNotification;
use App\Models\AttorneyCase;
use App\Models\AttorneyCaseProsecutor;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AttorneyProsecutorAssignmentController extends Controller
{
    public const ROLES = ['Lead', 'Assistant', 'Support'];
    public const ROLE_LABELS = ['Lead' => 'Hogaamiye', 'Assistant' => 'Kaaliye', 'Support' => 'Taageere'];

    public function index(Request $request)
    {
        $baseQuery = AttorneyCase::query();

        $stats = [
            'total'      => (clone $baseQuery)->count(),
            'assigned'   => (clone $baseQuery)->whereHas('prosecutorAssignments')->count(),
            'unassigned' => (clone $baseQuery)->whereDoesntHave('prosecutorAssignments')->count(),
        ];

        $query = (clone $baseQuery)->with(['complainants', 'prosecutorAssignments.prosecutor'])->orderByDesc('ACID');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('complainants', function ($cq) use ($search) {
                      $cq->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('assign_status')) {
            if ($request->assign_status === 'assigned') {
                $query->whereHas('prosecutorAssignments');
            } elseif ($request->assign_status === 'unassigned') {
                $query->whereDoesntHave('prosecutorAssignments');
            }
        }

        if ($request->filled('case_type')) {
            $query->where('case_type', $request->case_type);
        }

        $perPage = $this->resolvePerPage($request);
        $cases   = $query->paginate($perPage)->withQueryString();

        return view('attorney.Assigned.Direct_complaint_view_prosecutor_assigned', [
            'cases'     => $cases,
            'stats'     => $stats,
            'caseTypes' => AttorneyCaseController::CASE_TYPES,
        ]);
    }

    public function add(Request $request, $id)
    {
        $case = AttorneyCase::with('prosecutorAssignments.prosecutor')->findOrFail($id);
        $prosecutors = Employee::where('status', 'Active')
            ->whereHas('court', function ($q) {
                $q->where('longName', 'ATTORNEY GENERAL OFFICE');
            })
            ->orderBy('EmpName')->get();

        return view('attorney.Assigned.Direct_complaint_add_prosecutor_assigned', [
            'case'        => $case,
            'prosecutors' => $prosecutors,
            'roles'       => self::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'attorney_case_id' => 'required|exists:attorney_cases,ACID',
            'prosecutor_id'    => 'required|exists:employees,AID',
            'role'             => 'required|in:' . implode(',', self::ROLES),
            'assigned_date'    => 'required|date',
        ]);

        $assignment = AttorneyCaseProsecutor::create($data);

        $case       = AttorneyCase::find($data['attorney_case_id']);
        $prosecutor = Employee::find($data['prosecutor_id']);
        $case?->update(['status' => 'Gal Ku Qoris']);
        $case?->activities()->create([
            'user_id'     => $request->user()->id,
            'type'        => 'prosecutor_assigned',
            'description' => "{$request->user()->name} wuxuu u xilsaaray {$prosecutor?->EmpName} dacwadan ({$data['role']}).",
        ]);

        $emailSent = $this->sendAssignmentEmail($assignment);

        $message = 'Xeer ilaaliyaha waa loo xilsaaray dacwadda.';
        $message .= $emailSent
            ? ' Ogeysiis email ah ayaa loo diray.'
            : ' Laakiin ogeysiiska emailka lama diri karin — fadlan si kale ula soco xeer ilaaliyaha.';

        return response()->json([
            'message'    => $message,
            'email_sent' => $emailSent,
            'assignment' => $assignment->load('prosecutor'),
        ]);
    }

    private function sendAssignmentEmail(AttorneyCaseProsecutor $assignment): bool
    {
        $assignment->loadMissing(['prosecutor', 'attorneyCase']);
        $prosecutor = $assignment->prosecutor;
        $case       = $assignment->attorneyCase;

        if (!$prosecutor || !$case || !$prosecutor->email) {
            return false;
        }

        try {
            Mail::to($prosecutor->email)->send(new ProsecutorAssignmentNotification(
                prosecutorName: $prosecutor->EmpName,
                caseNumber:     $case->case_number,
                caseTitle:      $case->title,
                roleLabel:      self::ROLE_LABELS[$assignment->role] ?? $assignment->role,
                assignedDate:   \Carbon\Carbon::parse($assignment->assigned_date)->format('d/m/Y'),
            ));
            return true;
        } catch (\Throwable $e) {
            \Log::error('Failed to send ProsecutorAssignmentNotification to ' . $prosecutor->email . ': ' . $e->getMessage());
            return false;
        }
    }

    public function update(Request $request, $id)
    {
        $assignment = AttorneyCaseProsecutor::findOrFail($id);

        $data = $request->validate([
            'prosecutor_id' => 'required|exists:employees,AID',
            'role'          => 'required|in:' . implode(',', self::ROLES),
            'assigned_date' => 'required|date',
        ]);

        $assignment->update($data);

        return response()->json(['message' => 'Xilsaarista waa la cusboonaysiiyay.', 'assignment' => $assignment->load('prosecutor')]);
    }

    public function destroy($id)
    {
        $assignment = AttorneyCaseProsecutor::findOrFail($id);
        $assignment->delete();

        return response()->json(['message' => 'Xilsaaristii waa la tirtiray.']);
    }
}
