<?php

namespace App\Http\Controllers;

use App\Models\DistrictFamilyAssignment;
use App\Models\DistrictFamilyRegistration;
use App\Models\Employee;
use App\Models\StatusProcess;
use Illuminate\Http\Request;
use App\Mail\CaseAssignmentMail;
use Illuminate\Support\Facades\Mail;
use Auth;

class DistrictFamilyAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $courtId = auth()->user()->employee?->courtID;

        $baseQuery = DistrictFamilyRegistration::query();

        $stats = [
            'total'      => (clone $baseQuery)->count(),
            'assigned'   => (clone $baseQuery)->whereHas('assignments')->count(),
            'unassigned' => (clone $baseQuery)->whereDoesntHave('assignments')->count(),
        ];

        $query = (clone $baseQuery)->with(['assignments.employee', 'court'])->orderByDesc('FCID');

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
        $assignments    = DistrictFamilyAssignment::with(['case.court', 'employee'])->latest()->get();
        $employees      = Employee::where('status', 'Active')
                               ->when($courtId, fn($q) => $q->where('courtID', $courtId))
                               ->get();
        $familySubCases = \App\Models\CaseCategory::where('case_name', 'Qoyska')->pluck('sub_case');

        return view('Courts.District_family.assign.district_family_assign_view', compact('cases', 'assignments', 'employees', 'stats', 'familySubCases'));
    }

    public function addJudges($id)
    {
        $courtId     = auth()->user()->employee?->courtID;
        $case        = DistrictFamilyRegistration::with(['court', 'assignments.employee'])->findOrFail($id);
        $employees   = Employee::where('status', 'Active')
                               ->when($courtId, fn($q) => $q->where('courtID', $courtId))
                               ->get();
        $stageStatus = StatusProcess::where('name', 'Gal Ku Qoris')->first();
        return view('Courts.District_family.assign.district_family_add_judges', compact('case', 'employees', 'stageStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'family_case_id' => 'required|exists:district_family_registrations,FCID',
            'employee_id' => 'required|exists:employees,AID',
            'assignment_date' => 'required|date',
        ]);

        $case        = DistrictFamilyRegistration::findOrFail($request->input('family_case_id'));
        $stageStatus = StatusProcess::where('name', 'Gal Ku Qoris')->first();
        $newStatus   = $stageStatus?->name ?? 'Gal Ku Qoris';

        DistrictFamilyAssignment::create([
            'family_case_id'  => $request->input('family_case_id'),
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
        $assignment = DistrictFamilyAssignment::findOrFail($id);
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
        DistrictFamilyAssignment::findOrFail($id)->delete();
        return response()->json(['message' => 'Assignment deleted successfully']);
    }
}
