<?php

namespace App\Http\Controllers;

use App\Models\AppealCivilAssignment;
use App\Models\AppealCivilRegistration;
use App\Models\Employee;
use App\Models\StatusProcess;
use Illuminate\Http\Request;
use App\Mail\CaseAssignmentMail;
use Illuminate\Support\Facades\Mail;
use Auth;

class AppealCivilAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $courtId = auth()->user()->employee?->courtID;

        $baseQuery = AppealCivilRegistration::query();

        $stats = [
            'total'      => (clone $baseQuery)->count(),
            'assigned'   => (clone $baseQuery)->whereHas('assignments')->count(),
            'unassigned' => (clone $baseQuery)->whereDoesntHave('assignments')->count(),
        ];

        $query = (clone $baseQuery)->with(['assignments.employee', 'court'])->orderByDesc('ACID');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('FileNo', 'like', "%$s%")
                  ->orWhere('RegisterNo', 'like', "%$s%")
                  ->orWhere('CaseType', 'like', "%$s%")
                  ->orWhereHas('court', fn($cq) => $cq->where('longName', 'like', "%$s%"));
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'assigned') {
                $query->where('Status', 'Gal Ku Qoris');
            } elseif ($request->status === 'pending') {
                $query->where('Status', '!=', 'Gal Ku Qoris');
            }
        }

        $perPage = $this->resolvePerPage($request);

        $cases       = $query->paginate($perPage)->withQueryString();
        $assignments = AppealCivilAssignment::with(['case.court', 'employee'])->latest()->get();
        $employees   = Employee::where('status', 'Active')
                               ->when($courtId, fn($q) => $q->where('courtID', $courtId))
                               ->get();

        return view('appeal_court.Appeal_civil.assign.appeal_civil_assign_view', compact('cases', 'assignments', 'employees', 'stats'));
    }

    public function addJudges($id)
    {
        $courtId     = auth()->user()->employee?->courtID;
        $case        = AppealCivilRegistration::with(['court', 'assignments.employee'])->findOrFail($id);
        $employees   = Employee::where('status', 'Active')
                               ->when($courtId, fn($q) => $q->where('courtID', $courtId))
                               ->get();
        $stageStatus = StatusProcess::where('name', 'Gal Ku Qoris')->first();
        return view('appeal_court.Appeal_civil.assign.appeal_civil_add_judges', compact('case', 'employees', 'stageStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'civil_case_id'   => 'required|exists:appeal_civil_registrations,ACID',
            'employee_id'     => 'required|exists:employees,AID',
            'assignment_date' => 'required|date',
        ]);

        $case        = AppealCivilRegistration::findOrFail($request->input('civil_case_id'));
        $stageStatus = StatusProcess::where('name', 'Gal Ku Qoris')->first();
        $newStatus   = $stageStatus?->name ?? 'Gal Ku Qoris';

        AppealCivilAssignment::create([
            'civil_case_id'   => $request->input('civil_case_id'),
            'employee_id'     => $request->input('employee_id'),
            'panel_role'      => $request->input('panel_role'),
            'assignment_date' => $request->input('assignment_date'),
            'assigned_by'     => Auth::user()->name,
            'status'          => $newStatus,
            'notes'           => $request->input('notes'),
        ]);

        $case->update(['Status' => $newStatus]);

        try {
            $employee = Employee::find($request->input('employee_id'));
            if ($employee && $employee->email) {
                Mail::to($employee->email)->send(new CaseAssignmentMail($case, $employee, $request->input('panel_role')));
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send appeal case assignment email: " . $e->getMessage());
        }

        return response()->json(['message' => 'Case assigned and notification sent successfully']);
    }

    public function update(Request $request, $id)
    {
        $assignment = AppealCivilAssignment::findOrFail($id);
        $assignment->update([
            'employee_id'     => $request->input('employee_id'),
            'panel_role'      => $request->input('panel_role'),
            'assignment_date' => $request->input('assignment_date'),
            'notes'           => $request->input('notes'),
        ]);
        return response()->json(['message' => 'Assignment updated successfully']);
    }

    public function destroy($id)
    {
        AppealCivilAssignment::findOrFail($id)->delete();
        return response()->json(['message' => 'Assignment deleted successfully']);
    }
}
