<?php

namespace App\Http\Controllers;

use App\Mail\LawyerAssignmentNotification;
use App\Models\AppealCriminalLawyer;
use App\Models\AppealCriminalRegistration;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppealCriminalLawyerController extends Controller
{
    public function index(Request $request)
    {
        $caseId = $request->query('case_id');
        $case = AppealCriminalRegistration::with('parties')->findOrFail($caseId);
        $lawyers = Lawyer::where('status', 'active')->orderBy('LawyerName')->get();
        return view('appeal_court.Appeal_criminal.registration.appeal_criminal_lawyer_assign', compact('case', 'lawyers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'criminal_case_id' => 'required|exists:appeal_criminal_registrations,ACMID',
            'lawyer_id'        => 'required|exists:lawyers,LRID',
            'party_id'         => 'nullable|exists:appeal_criminal_parties,PID',
            'party_role'       => 'required|string',
            'assignment_date'  => 'required|date',
        ]);

        AppealCriminalLawyer::create([
            'criminal_case_id' => $request->criminal_case_id,
            'lawyer_id'        => $request->lawyer_id,
            'party_id'         => $request->party_id ?: null,
            'party_role'       => $request->party_role,
            'assignment_date'  => $request->assignment_date,
            'reason'           => $request->reason,
            'addedBy'          => auth()->user()->name ?? 'Admin',
            'addedDate'        => now(),
        ]);

        $this->sendLawyerEmail($request->lawyer_id, $request->criminal_case_id, $request->party_role, $request->assignment_date);

        return response()->json(['success' => true, 'message' => 'Lawyer assigned successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lawyer_id'       => 'required|exists:lawyers,LRID',
            'party_id'        => 'nullable|exists:appeal_criminal_parties,PID',
            'party_role'      => 'required|string',
            'assignment_date' => 'required|date',
        ]);

        $assignment = AppealCriminalLawyer::findOrFail($id);
        $assignment->update([
            'lawyer_id'       => $request->lawyer_id,
            'party_id'        => $request->party_id ?: null,
            'party_role'      => $request->party_role,
            'assignment_date' => $request->assignment_date,
            'reason'          => $request->reason,
        ]);

        $this->sendLawyerEmail($request->lawyer_id, $assignment->criminal_case_id, $request->party_role, $request->assignment_date);

        return response()->json(['success' => true, 'message' => 'Assignment updated successfully.']);
    }

    public function destroy($id)
    {
        $assignment = AppealCriminalLawyer::findOrFail($id);
        $assignment->delete();
        return response()->json(['success' => true, 'message' => 'Assignment removed successfully.']);
    }

    public function getAssignmentsByCase($caseId)
    {
        $assignments = AppealCriminalLawyer::with(['lawyer', 'party'])
            ->where('criminal_case_id', $caseId)
            ->get();
        return response()->json($assignments);
    }

    private function sendLawyerEmail($lawyerId, $caseId, $partyRole, $assignmentDate): void
    {
        try {
            $lawyer = Lawyer::find($lawyerId);
            $case   = AppealCriminalRegistration::with('court')->find($caseId);

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
