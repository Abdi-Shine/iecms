<?php

namespace App\Http\Controllers;

use App\Mail\LawyerAssignmentNotification;
use App\Models\DistrictExecutionLawyer;
use App\Models\DistrictExecutionRegistration;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DistrictExecutionLawyerController extends Controller
{
    public function index(Request $request)
    {
        $caseId = $request->query('case_id');
        $case = DistrictExecutionRegistration::with('parties')->findOrFail($caseId);
        $lawyers = Lawyer::where('status', 'active')->where('Grade', 'Darajada Koobaad')->orderBy('LawyerName')->get();
        return view('Courts.District_execution.registration.district_execution_lawyer_assign', compact('case', 'lawyers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'execution_case_id'  => 'required|exists:district_execution_registrations,ECID',
            'lawyer_id'       => 'required|exists:lawyers,LRID',
            'party_id'        => 'nullable|exists:district_execution_parties,PID',
            'party_role'      => 'required|string',
            'assignment_date' => 'required|date',
        ]);

        DistrictExecutionLawyer::create([
            'execution_case_id'  => $request->execution_case_id,
            'lawyer_id'       => $request->lawyer_id,
            'party_id'        => $request->party_id ?: null,
            'party_role'      => $request->party_role,
            'assignment_date' => $request->assignment_date,
            'reason'          => $request->reason,
            'addedBy'         => auth()->user()->name ?? 'Admin',
            'addedDate'       => now(),
        ]);

        $this->sendLawyerEmail($request->lawyer_id, $request->execution_case_id, $request->party_role, $request->assignment_date);

        return response()->json(['success' => true, 'message' => 'Lawyer assigned successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lawyer_id'       => 'required|exists:lawyers,LRID',
            'party_id'        => 'nullable|exists:district_execution_parties,PID',
            'party_role'      => 'required|string',
            'assignment_date' => 'required|date',
        ]);

        $assignment = DistrictExecutionLawyer::findOrFail($id);
        $assignment->update([
            'lawyer_id'       => $request->lawyer_id,
            'party_id'        => $request->party_id ?: null,
            'party_role'      => $request->party_role,
            'assignment_date' => $request->assignment_date,
            'reason'          => $request->reason,
        ]);

        $this->sendLawyerEmail($request->lawyer_id, $assignment->execution_case_id, $request->party_role, $request->assignment_date);

        return response()->json(['success' => true, 'message' => 'Assignment updated successfully.']);
    }

    public function destroy($id)
    {
        $assignment = DistrictExecutionLawyer::findOrFail($id);
        $assignment->delete();
        return response()->json(['success' => true, 'message' => 'Assignment removed successfully.']);
    }

    public function getAssignmentsByCase($caseId)
    {
        $assignments = DistrictExecutionLawyer::with(['lawyer', 'party'])
            ->where('execution_case_id', $caseId)
            ->get();
        return response()->json($assignments);
    }

    private function sendLawyerEmail($lawyerId, $caseId, $partyRole, $assignmentDate): void
    {
        try {
            $lawyer = Lawyer::find($lawyerId);
            $case   = DistrictExecutionRegistration::with('court')->find($caseId);

            if (!$lawyer || !$case || empty($lawyer->Email)) {
                return;
            }

            Mail::to($lawyer->Email)->send(new LawyerAssignmentNotification(
                lawyerName:     $lawyer->LawyerName,
                fileNo:         $case->FileNo,
                courtName:      $case->court?->longName ?? 'Maxkamadda',
                caseType:       $case->CaseType,
                partyRole:      $partyRole,
                assignmentDate: \Carbon\Carbon::parse($assignmentDate)->format('d/m/Y'),
            ));
        } catch (\Throwable $e) {
            \Log::error('LawyerAssignmentNotification failed: ' . $e->getMessage());
        }
    }
}
