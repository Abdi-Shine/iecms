<?php

namespace App\Http\Controllers;

use App\Models\DistrictCriminalAssignment;
use App\Models\DistrictCriminalRegistration;
use App\Models\Employee;
use App\Models\StatusProcess;
use Illuminate\Http\Request;
use App\Mail\CaseAssignmentMail;
use Illuminate\Support\Facades\Mail;
use Auth;

class DistrictCriminalAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $courtId = auth()->user()->employee?->courtID;

        $baseQuery = DistrictCriminalRegistration::query();

        $stats = [
            'total'      => (clone $baseQuery)->count(),
            'assigned'   => (clone $baseQuery)->whereHas('assignments')->count(),
            'unassigned' => (clone $baseQuery)->whereDoesntHave('assignments')->count(),
        ];

        $query = (clone $baseQuery)->with(['assignments.employee', 'court'])->orderByDesc('CMID');

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

        if ($request->filled('sub_case')) {
            $query->where('SubCase', $request->sub_case);
        }

        $perPage = $this->resolvePerPage($request);

        $cases          = $query->paginate($perPage)->withQueryString();
        $assignments    = DistrictCriminalAssignment::with(['case.court', 'employee'])->latest()->get();
        $employees      = Employee::where('status', 'Active')
                               ->when($courtId, fn($q) => $q->where('courtID', $courtId))
                               ->get();
        $criminalSubCases = \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->pluck('sub_case');

        return view('Courts.District_criminal.assign.district_criminal_assign_view', compact('cases', 'assignments', 'employees', 'stats', 'criminalSubCases'));
    }

    public function addJudges($id)
    {
        $courtId     = auth()->user()->employee?->courtID;
        $case        = DistrictCriminalRegistration::with(['court', 'assignments.employee'])->findOrFail($id);
        $employees   = Employee::where('status', 'Active')
                               ->when($courtId, fn($q) => $q->where('courtID', $courtId))
                               ->get();
        $stageStatus = StatusProcess::where('name', 'Gal Ku Qoris')->first();
        return view('Courts.District_criminal.assign.district_criminal_add_judges', compact('case', 'employees', 'stageStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'criminal_case_id' => 'required|exists:district_criminal_registrations,CMID',
            'employee_id' => 'required|exists:employees,AID',
            'assignment_date' => 'required|date',
        ]);

        $case        = DistrictCriminalRegistration::findOrFail($request->input('criminal_case_id'));
        $stageStatus = StatusProcess::where('name', 'Gal Ku Qoris')->first();
        $newStatus   = $stageStatus?->name ?? 'Gal Ku Qoris';

        DistrictCriminalAssignment::create([
            'criminal_case_id'  => $request->input('criminal_case_id'),
            'employee_id'     => $request->input('employee_id'),
            'panel_role'      => $request->input('panel_role'),
            'assignment_date' => $request->input('assignment_date'),
            'assigned_by'     => Auth::user()->name,
            'status'          => $newStatus,
            'notes'           => $request->input('notes'),
        ]);

        // Advance the case to the assignment workflow stage
        $case->update(['Status' => $newStatus]);

        // Send Email Notification
        try {
            $employee = Employee::find($request->input('employee_id'));
            if ($employee && $employee->email) {
                Mail::to($employee->email)->send(new CaseAssignmentMail($case, $employee, $request->input('panel_role')));
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send case assignment email: " . $e->getMessage());
        }

        return response()->json(['message' => 'Case assigned and notification sent successfully']);
    }

    public function update(Request $request, $id)
    {
        $assignment = DistrictCriminalAssignment::findOrFail($id);
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
        DistrictCriminalAssignment::findOrFail($id)->delete();
        return response()->json(['message' => 'Assignment deleted successfully']);
    }
}
