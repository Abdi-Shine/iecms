<?php

namespace App\Http\Controllers;

use App\Mail\CivilCasePartyNotification;
use App\Models\AppealCivilParty;
use App\Models\AppealCivilRegistration;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AppealCivilPartyController extends Controller
{
    public function index(Request $request)
    {
        $caseId = $request->query('case_id');
        if (!$caseId) {
            return redirect()->route('appeal-civil-registration.index')->with('error', 'Case ID is required.');
        }
        $case    = AppealCivilRegistration::findOrFail($caseId);
        $lawyers = Lawyer::where('status', 'active')->orderBy('LawyerName')->get();
        return view('Courts.Appeal_civil.registration.appeal_civil_Parties_information', compact('case', 'lawyers'));
    }

    public function getPartiesByCase($caseId)
    {
        $parties = AppealCivilParty::where('civil_case_id', $caseId)->get();
        return response()->json($parties);
    }

    public function store(Request $request)
    {
        $request->validate([
            'case_id' => 'required|exists:appeal_civil_registrations,ACID',
            'parties' => 'required|array',
            'parties.*.party_role' => 'required|string',
            'parties.*.full_name'  => 'required|string',
            'parties.*.dob'        => 'nullable|date',
        ]);

        $caseId    = $request->case_id;
        $partyData = $request->input('parties', []);

        $currentPIDs = collect($partyData)->pluck('PID')->filter()->toArray();
        AppealCivilParty::where('civil_case_id', $caseId)->whereNotIn('PID', $currentPIDs)->delete();

        foreach ($partyData as $index => $data) {
            $pid = $data['PID'] ?? null;

            $fields = [
                'civil_case_id'   => $caseId,
                'party_role'      => $data['party_role'],
                'full_name'       => $data['full_name'],
                'mother_name'     => $data['mother_name'] ?? null,
                'sex'             => $data['sex'] ?? null,
                'dob'             => !empty($data['dob']) ? $data['dob'] : null,
                'contact_number'  => $data['contact_number'] ?? null,
                'email'           => $data['email'] ?? null,
                'district'        => $data['district'] ?? null,
                'national_id'     => $data['national_id'] ?? null,
                'passport_number' => $data['passport_number'] ?? null,
            ];

            if ($request->hasFile("parties.$index.passport_doc")) {
                $fields['passport_doc'] = $request->file("parties.$index.passport_doc")->store('appeal_civil_uploads/Parties_documents', 'public');
            }
            if ($request->hasFile("parties.$index.power_of_attorney")) {
                $fields['power_of_attorney'] = $request->file("parties.$index.power_of_attorney")->store('appeal_civil_uploads/Parties_documents', 'public');
            }

            if ($pid) {
                $fields['updatedBy']   = auth()->user()->name ?? 'Admin';
                $fields['updatedDate'] = now()->format('Y-m-d');
                AppealCivilParty::where('PID', $pid)->update($fields);
            } else {
                $fields['addedBy']   = auth()->user()->name ?? 'Admin';
                $fields['addedDate'] = now()->format('Y-m-d');
                AppealCivilParty::create($fields);
            }
        }

        return response()->json(['success' => true, 'message' => 'Parties information saved successfully.']);
    }

    public function destroy($id)
    {
        $party = AppealCivilParty::findOrFail($id);
        if ($party->passport_doc) Storage::disk('public')->delete($party->passport_doc);
        if ($party->power_of_attorney) Storage::disk('public')->delete($party->power_of_attorney);
        $party->delete();
        return response()->json(['success' => true, 'message' => 'Party deleted successfully.']);
    }

    public function sendNotifications($caseId)
    {
        $case    = AppealCivilRegistration::with('court')->findOrFail($caseId);
        $parties = AppealCivilParty::where('civil_case_id', $caseId)->where('party_role', 'Plaintiff')->whereNotNull('email')->where('email', '!=', '')->get();

        if ($parties->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Dhinac walba oo xogta e-mail ah leh kuma jiro.'], 422);
        }

        $sent  = 0;
        $fails = [];
        $courtName = $case->court->longName ?? 'Maxkamadda';
        $openDate  = $case->OpenDate ? \Carbon\Carbon::parse($case->OpenDate)->format('d/m/Y') : date('d/m/Y');

        foreach ($parties as $party) {
            try {
                Mail::to($party->email)->send(new CivilCasePartyNotification(
                    partyName: $party->full_name,
                    fileNo:    $case->FileNo,
                    courtName: $courtName,
                    caseType:  $case->CaseType,
                    openDate:  $openDate,
                ));
                $sent++;
            } catch (\Throwable $e) {
                $fails[] = $party->full_name . ' (' . $party->email . ')';
            }
        }

        if ($sent === 0) {
            return response()->json(['success' => false, 'message' => 'Warbaahinta e-mailka waa fashilantay. Xaqiiji dejinta SMTP-ga.'], 500);
        }

        $msg = "E-mail si guul leh ayaa loogu diray $sent dhinac.";
        if ($fails) {
            $msg .= ' Kuwan fashilmay: ' . implode(', ', $fails);
        }

        return response()->json(['success' => true, 'message' => $msg, 'sent' => $sent, 'failed' => $fails]);
    }
}
