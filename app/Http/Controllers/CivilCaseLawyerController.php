<?php

namespace App\Http\Controllers;

use App\Mail\LawyerAssignmentNotification;
use App\Models\CivilCaseLawyer;
use App\Models\DistricCivilRegistration;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CivilCaseLawyerController extends Controller
{
    public function index(Request $request)
    {
        $caseId = $request->query('case_id');
        $case = DistricCivilRegistration::with('parties')->findOrFail($caseId);
        $lawyers = Lawyer::where('status', 'active')->where('Grade', 'Darajada Koobaad')->orderBy('LawyerName')->get();
        return view('Courts.District_civil.registration.district_civil_lawyer_assign', compact('case', 'lawyers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'civil_case_id'   => 'required|exists:distric_civil_registrations,CRID',
            'lawyer_id'       => 'required|exists:lawyers,LRID',
            'party_id'        => 'nullable|exists:district_civil_parties,PID',
            'party_role'      => 'required|string',
            'assignment_date' => 'required|date',
        ]);

        CivilCaseLawyer::create([
            'civil_case_id'   => $request->civil_case_id,
            'lawyer_id'       => $request->lawyer_id,
            'party_id'        => $request->party_id ?: null,
            'party_role'      => $request->party_role,
            'assignment_date' => $request->assignment_date,
            'reason'          => $request->reason,
            'addedBy'         => auth()->user()->name ?? 'Admin',
            'addedDate'       => now(),
        ]);

        $this->sendLawyerEmail($request->lawyer_id, $request->civil_case_id, $request->party_role, $request->assignment_date);

        return response()->json(['success' => true, 'message' => 'Lawyer assigned successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lawyer_id'       => 'required|exists:lawyers,LRID',
            'party_id'        => 'nullable|exists:district_civil_parties,PID',
            'party_role'      => 'required|string',
            'assignment_date' => 'required|date',
        ]);

        $assignment = CivilCaseLawyer::findOrFail($id);
        $assignment->update([
            'lawyer_id'       => $request->lawyer_id,
            'party_id'        => $request->party_id ?: null,
            'party_role'      => $request->party_role,
            'assignment_date' => $request->assignment_date,
            'reason'          => $request->reason,
        ]);

        $this->sendLawyerEmail($request->lawyer_id, $assignment->civil_case_id, $request->party_role, $request->assignment_date);

        return response()->json(['success' => true, 'message' => 'Assignment updated successfully.']);
    }

    public function destroy($id)
    {
        $assignment = CivilCaseLawyer::findOrFail($id);
        $assignment->delete();
        return response()->json(['success' => true, 'message' => 'Assignment removed successfully.']);
    }

    public function getAssignmentsByCase($caseId)
    {
        $assignments = CivilCaseLawyer::with(['lawyer', 'party'])
            ->where('civil_case_id', $caseId)
            ->get();
        return response()->json($assignments);
    }

    private function sendLawyerEmail($lawyerId, $caseId, $partyRole, $assignmentDate): void
    {
        try {
            $lawyer = Lawyer::find($lawyerId);
            $case   = DistricCivilRegistration::with('court')->find($caseId);

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

