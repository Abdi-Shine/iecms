<?php

namespace App\Http\Controllers;

use App\Models\AttorneyCase;
use App\Models\AttorneyCaseEvidenceManagement;
use App\Models\AttorneyCaseExpertInterview;
use App\Models\AttorneyCaseSuspectInterview;
use App\Models\AttorneyCaseVictimInterview;
use App\Models\AttorneyCaseWitnessInterview;
use Illuminate\Http\Request;

class AttorneyEvidenceInterviewsController extends Controller
{
    public const RECORDING_METHOD_OPTIONS = ['Qoraal', 'Cod', 'Muuqaal', 'Dhammaan'];
    public const CREDIBILITY_OPTIONS      = ['Sarreeya', 'Dhexdhexaad', 'Hoose'];
    public const EVIDENCE_TYPE_OPTIONS    = ['Dukumeenti', 'Jireed', 'Dhijitaal', 'Markhaati', 'Sawir', 'Fiidiyow', 'Kale'];
    public const EVIDENCE_CONDITION_OPTIONS = ['Wanaagsan', 'Dhexdhexaad', 'Xun'];

    /**
     * Landing page listing the 5 forms required for this step.
     */
    public function show(Request $request, $id)
    {
        $case = AttorneyCase::with([
            'suspectInterview', 'witnessInterview', 'expertInterview', 'victimInterview', 'evidenceManagement',
        ])->findOrFail($id);

        $forms = [
            [
                'key'         => 'suspect-interviews',
                'title'       => 'Wareysiga Eedeysanaha',
                'description' => 'Wareysi eedeysanayaasha oo diiwaan geli bayaannadooda',
                'complete'    => (bool) $case->suspectInterview,
                'route'       => route('attorney-cases.workflow.evidence-interviews.suspect-interviews.form', $case->ACID),
            ],
            [
                'key'         => 'witness-interviews',
                'title'       => 'Wareysiga Markhaatiyaasha',
                'description' => 'Wareysi markhaatiyaasha oo ururi maragfurkooda',
                'complete'    => (bool) $case->witnessInterview,
                'route'       => route('attorney-cases.workflow.evidence-interviews.witness-interviews.form', $case->ACID),
            ],
            [
                'key'         => 'expert-interviews',
                'title'       => 'Wareysiga Khubarada',
                'description' => 'Wareysi markhaatiyaasha khubarada ah iyo takhasusleyaasha',
                'complete'    => (bool) $case->expertInterview,
                'route'       => route('attorney-cases.workflow.evidence-interviews.expert-interviews.form', $case->ACID),
            ],
            [
                'key'         => 'victim-interviews',
                'title'       => 'Wareysiga Dhibbanayaasha',
                'description' => 'Wareysi dhibbanayaasha oo diiwaan geli bayaannadooda',
                'complete'    => (bool) $case->victimInterview,
                'route'       => route('attorney-cases.workflow.evidence-interviews.victim-interviews.form', $case->ACID),
            ],
            [
                'key'         => 'evidence-management',
                'title'       => 'Maareynta Caddaynta',
                'description' => 'Diiwaan geli oo maarey ururinta caddaynta',
                'complete'    => (bool) $case->evidenceManagement,
                'route'       => route('attorney-cases.workflow.evidence-interviews.evidence-management.form', $case->ACID),
            ],
        ];

        return view('attorney.Conclusion.direct_complaint_evidence_interviews', [
            'case'  => $case,
            'forms' => $forms,
        ]);
    }

    // ── 1. Suspect Interviews ───────────────────────────────────────
    public function suspectInterviewsForm(Request $request, $id)
    {
        $case = AttorneyCase::with(['suspectInterview', 'accused'])->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_suspect_interviews_form', [
            'case'                   => $case,
            'recordingMethodOptions' => self::RECORDING_METHOD_OPTIONS,
        ]);
    }

    public function storeSuspectInterviews(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'suspect_name'           => 'nullable|string|max:150',
            'interview_date'         => 'required|date',
            'interview_time'         => 'nullable',
            'interview_location'     => 'nullable|string|max:255',
            'interviewing_officer'   => 'nullable|string|max:150',
            'interpreter_used'       => 'nullable|boolean',
            'interpreter_name'       => 'nullable|string|max:150',
            'legal_counsel_present'  => 'nullable|boolean',
            'counsel_name'           => 'nullable|string|max:150',
            'rights_informed'        => 'nullable|boolean',
            'recording_method'       => 'nullable|in:' . implode(',', self::RECORDING_METHOD_OPTIONS),
            'statement_summary'      => 'nullable|string',
            'statement_voluntary'    => 'nullable|boolean',
            'signature_obtained'     => 'nullable|boolean',
            'recording_file'         => 'nullable|file|max:20480',
            'notes'                  => 'nullable|string',
        ]);

        foreach (['interpreter_used', 'legal_counsel_present', 'rights_informed', 'statement_voluntary', 'signature_obtained'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        if ($request->hasFile('recording_file')) {
            $data['recording_file_path'] = $request->file('recording_file')->store(
                'uploads/attorney/suspect-interviews/' . $case->ACID,
                'public'
            );
        }
        unset($data['recording_file']);

        AttorneyCaseSuspectInterview::updateOrCreate(['attorney_case_id' => $case->ACID], $data);

        return redirect()->route('attorney-cases.workflow.evidence-interviews', $case->ACID)
            ->with('success', 'Wareysiga eedeysanaha waa la keydiyay.');
    }

    // ── 2. Witness Interviews ───────────────────────────────────────
    public function witnessInterviewsForm(Request $request, $id)
    {
        $case = AttorneyCase::with('witnessInterview')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_witness_interviews_form', [
            'case'                => $case,
            'credibilityOptions'  => self::CREDIBILITY_OPTIONS,
        ]);
    }

    public function storeWitnessInterviews(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'witness_name'            => 'nullable|string|max:150',
            'witness_contact'         => 'nullable|string|max:100',
            'relationship_to_case'    => 'nullable|string|max:150',
            'interview_date'          => 'required|date',
            'interview_location'      => 'nullable|string|max:255',
            'interviewing_officer'    => 'nullable|string|max:150',
            'testimony_summary'       => 'nullable|string',
            'credibility_assessment'  => 'nullable|in:' . implode(',', self::CREDIBILITY_OPTIONS),
            'follow_up_needed'        => 'nullable|boolean',
            'statement_file'          => 'nullable|file|max:20480',
            'notes'                   => 'nullable|string',
        ]);

        $data['follow_up_needed'] = $request->boolean('follow_up_needed');

        if ($request->hasFile('statement_file')) {
            $data['statement_file_path'] = $request->file('statement_file')->store(
                'uploads/attorney/witness-interviews/' . $case->ACID,
                'public'
            );
        }
        unset($data['statement_file']);

        AttorneyCaseWitnessInterview::updateOrCreate(['attorney_case_id' => $case->ACID], $data);

        return redirect()->route('attorney-cases.workflow.evidence-interviews', $case->ACID)
            ->with('success', 'Wareysiga markhaatiga waa la keydiyay.');
    }

    // ── 3. Expert Interviews ────────────────────────────────────────
    public function expertInterviewsForm(Request $request, $id)
    {
        $case = AttorneyCase::with('expertInterview')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_expert_interviews_form', [
            'case' => $case,
        ]);
    }

    public function storeExpertInterviews(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'expert_name'              => 'nullable|string|max:150',
            'specialization'           => 'nullable|string|max:150',
            'credentials'              => 'nullable|string|max:255',
            'interview_date'           => 'required|date',
            'interview_location'       => 'nullable|string|max:255',
            'interviewing_officer'     => 'nullable|string|max:150',
            'expert_opinion_summary'   => 'nullable|string',
            'report_attached'          => 'nullable|boolean',
            'fee_arrangement'          => 'nullable|string|max:150',
            'expert_report'            => 'nullable|file|max:20480',
            'notes'                    => 'nullable|string',
        ]);

        $data['report_attached'] = $request->boolean('report_attached');

        if ($request->hasFile('expert_report')) {
            $data['expert_report_path'] = $request->file('expert_report')->store(
                'uploads/attorney/expert-interviews/' . $case->ACID,
                'public'
            );
        }
        unset($data['expert_report']);

        AttorneyCaseExpertInterview::updateOrCreate(['attorney_case_id' => $case->ACID], $data);

        return redirect()->route('attorney-cases.workflow.evidence-interviews', $case->ACID)
            ->with('success', 'Wareysiga khabiirka waa la keydiyay.');
    }

    // ── 4. Victim Interviews ────────────────────────────────────────
    public function victimInterviewsForm(Request $request, $id)
    {
        $case = AttorneyCase::with(['victimInterview', 'victims'])->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_victim_interviews_form', [
            'case' => $case,
        ]);
    }

    public function storeVictimInterviews(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'victim_name'                  => 'nullable|string|max:150',
            'interview_date'                => 'required|date',
            'interview_time'                => 'nullable',
            'interview_location'            => 'nullable|string|max:255',
            'interviewing_officer'          => 'nullable|string|max:150',
            'support_person_present'        => 'nullable|boolean',
            'support_person_name'           => 'nullable|string|max:150',
            'victim_impact_summary'         => 'nullable|string',
            'medical_treatment_required'    => 'nullable|boolean',
            'protective_measures_needed'    => 'nullable|boolean',
            'protective_measures_notes'     => 'nullable|string',
            'statement_file'                => 'nullable|file|max:20480',
            'notes'                          => 'nullable|string',
        ]);

        foreach (['support_person_present', 'medical_treatment_required', 'protective_measures_needed'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        if ($request->hasFile('statement_file')) {
            $data['statement_file_path'] = $request->file('statement_file')->store(
                'uploads/attorney/victim-interviews/' . $case->ACID,
                'public'
            );
        }
        unset($data['statement_file']);

        AttorneyCaseVictimInterview::updateOrCreate(['attorney_case_id' => $case->ACID], $data);

        return redirect()->route('attorney-cases.workflow.evidence-interviews', $case->ACID)
            ->with('success', 'Wareysiga dhibbanaha waa la keydiyay.');
    }

    // ── 5. Evidence Management ──────────────────────────────────────
    public function evidenceManagementForm(Request $request, $id)
    {
        $case = AttorneyCase::with('evidenceManagement')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_evidence_management_form', [
            'case'                 => $case,
            'evidenceTypeOptions'  => self::EVIDENCE_TYPE_OPTIONS,
            'conditionOptions'     => self::EVIDENCE_CONDITION_OPTIONS,
        ]);
    }

    public function storeEvidenceManagement(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'evidence_description'      => 'nullable|string',
            'evidence_type'             => 'nullable|in:' . implode(',', self::EVIDENCE_TYPE_OPTIONS),
            'date_collected'            => 'required|date',
            'collected_by'              => 'nullable|string|max:150',
            'storage_location'          => 'nullable|string|max:255',
            'condition'                 => 'nullable|in:' . implode(',', self::EVIDENCE_CONDITION_OPTIONS),
            'chain_of_custody_notes'    => 'nullable|string',
            'catalogued'                => 'nullable|boolean',
            'evidence_file'             => 'nullable|file|max:20480',
            'notes'                     => 'nullable|string',
        ]);

        $data['catalogued'] = $request->boolean('catalogued');

        if ($request->hasFile('evidence_file')) {
            $data['evidence_file_path'] = $request->file('evidence_file')->store(
                'uploads/attorney/evidence-management/' . $case->ACID,
                'public'
            );
        }
        unset($data['evidence_file']);

        AttorneyCaseEvidenceManagement::updateOrCreate(['attorney_case_id' => $case->ACID], $data);

        return redirect()->route('attorney-cases.workflow.evidence-interviews', $case->ACID)
            ->with('success', 'Maareynta caddaynta waa la keydiyay.');
    }
}
