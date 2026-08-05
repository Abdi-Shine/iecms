<?php

namespace App\Http\Controllers;

use App\Models\AttorneyCase;
use App\Models\AttorneyCaseInvestigationDecision;
use App\Models\AttorneyCaseInvestigationExtension;
use Illuminate\Http\Request;

class AttorneyCaseWorkflowController extends Controller
{
    public const INVESTIGATION_DECISIONS = ['Baaritaan Loo Baahan Yahay', 'Baaritaan Looma Baahna'];

    public const EVIDENCE_QUALITY_OPTIONS      = ['Adag', 'Dhexdhexaad', 'Daciif', 'Aan Ku Filnayn'];
    public const EVIDENCE_COMPLETENESS_OPTIONS = ['Dhammaystiran', 'Qayb Ahaan Dhammaystiran', 'Aan Dhammaysnayn'];
    public const LEGAL_SUFFICIENCY_OPTIONS     = ['Ku Filan', 'Aan Ku Filnayn', 'U Baahan Dib U Eegis'];
    public const LEGAL_BASIS_OPTIONS           = ['Haa', 'Maya', 'Qayb Ahaan'];
    public const RISK_LEVEL_OPTIONS            = ['Hooseeya', 'Dhexdhexaad', 'Sarreeya', 'Halis Weyn'];
    public const RISK_FACTOR_OPTIONS           = [
        'Markhaati Faragelin',
        'Baabi\'inta Caddaynta',
        'Halista Carar',
        'Ammaanka Dadweynaha',
        'Kale',
    ];

    public const INVESTIGATION_STATUSES = ['Ongoing', 'Completed', 'Referred to CID', 'Suspended'];

    public const INVESTIGATION_EVIDENCE_TYPE_OPTIONS = ['Dukumeenti', 'Jireed', 'Dhijitaal', 'Markhaati', 'Sawir', 'Fiidiyow', 'Kale'];
    public const SEX_OPTIONS                          = ['Male' => 'Lab', 'Female' => 'Dheddig'];
    public const VICTIM_TYPE_OPTIONS                  = ['Shakhsi' => 'Shakhsi (Individual)', 'Qaranka' => 'Qaranka (State)'];

    public const APPROVAL_STATUS_OPTIONS = ['Sugaya', 'La Ansixiyay', 'La Diiday'];

    public const EXTENSION_PERIOD_OPTIONS = ['07 days', '15 days', '30 days', '60 days', '90 days', 'Other'];

    public function show(Request $request, $id)
    {
        $case = AttorneyCase::with([
            'investigationDecision', 'investigation', 'complianceForms.employee', 'complainants',
            'arrestDecision', 'arrestWithoutWarrant', 'warrantOfArrest', 'searchAndSeizure', 'assetRecovery',
            'suspectInterview', 'witnessInterview', 'expertInterview', 'victimInterview', 'evidenceManagement',
            'investigationExtension',
        ])->findOrFail($id);

        $steps = [
            [
                'key'         => 'investigation-decision',
                'title'       => 'Investigation Decision',
                'description' => 'Prosecutors decide: Is investigation needed?',
                'formsCount'  => 1,
                'enabled'     => true,
                'route'       => route('attorney-cases.workflow.investigation-decision', $case->ACID),
                'complete'    => (bool) $case->investigationDecision,
            ],
            [
                'key'         => 'investigation',
                'title'       => 'Investigation',
                'description' => 'AGO conducts investigation or refers to CID',
                'formsCount'  => 1,
                'enabled'     => true,
                'route'       => route('attorney-cases.workflow.investigation', $case->ACID),
                'complete'    => (bool) $case->investigation,
            ],
            [
                'key'         => 'arrest-decision',
                'title'       => 'Arrest Decision',
                'description' => 'Decide if arrest is required',
                'formsCount'  => 5,
                'enabled'     => true,
                'route'       => route('attorney-cases.workflow.arrest-decision', $case->ACID),
                'complete'    => (bool) $case->arrestDecision
                    && (bool) $case->arrestWithoutWarrant
                    && (bool) $case->warrantOfArrest
                    && (bool) $case->searchAndSeizure
                    && (bool) $case->assetRecovery,
            ],
            [
                'key'         => 'evidence-interviews',
                'title'       => 'Evidence & Interviews',
                'description' => 'Conduct interviews and manage evidence',
                'formsCount'  => 5,
                'enabled'     => true,
                'route'       => route('attorney-cases.workflow.evidence-interviews', $case->ACID),
                'complete'    => (bool) $case->suspectInterview
                    && (bool) $case->witnessInterview
                    && (bool) $case->expertInterview
                    && (bool) $case->victimInterview
                    && (bool) $case->evidenceManagement,
            ],
            [
                'key'         => 'investigation-extension',
                'title'       => 'Investigation Extension',
                'description' => 'Request extension if investigation time expires',
                'formsCount'  => 1,
                'enabled'     => true,
                'route'       => route('attorney-cases.workflow.investigation-extension', $case->ACID),
                'complete'    => (bool) $case->investigationExtension,
            ],
            [
                'key'         => 'investigation-report',
                'title'       => 'Investigation Report',
                'description' => 'Compile completed investigation reports',
                'formsCount'  => 1,
                'enabled'     => false,
            ],
            [
                'key'         => 'pir',
                'title'       => 'Prosecutor Investigation Report (PIR)',
                'description' => 'Prosecutors create PIR for AG/Deputy AG approval',
                'formsCount'  => 1,
                'enabled'     => false,
            ],
            [
                'key'         => 'review-charging-decision',
                'title'       => 'Review & Charging Decision',
                'description' => 'Review evidence and make charging decisions',
                'formsCount'  => 1,
                'enabled'     => false,
            ],
            [
                'key'         => 'charge-sheet-management',
                'title'       => 'Charge Sheet Management',
                'description' => 'Prepare and manage charge sheets',
                'formsCount'  => 3,
                'enabled'     => false,
                'note'        => 'Prosecutors create forms here',
            ],
            [
                'key'         => 'court-appearance',
                'title'       => 'Court Appearance',
                'description' => 'Submit formal request for court appearance',
                'formsCount'  => 1,
                'enabled'     => false,
            ],
            [
                'key'         => 'case-closure',
                'title'       => 'Case Closure',
                'description' => 'Complete and close the case',
                'formsCount'  => 1,
                'enabled'     => false,
            ],
        ];

        $totalSteps     = count($steps);
        $completedSteps = count(array_filter($steps, fn ($s) => $s['complete'] ?? false));
        $currentStep    = null;
        foreach ($steps as $index => $step) {
            if (!($step['complete'] ?? false)) {
                $currentStep = ['position' => $index + 1, 'title' => $step['title']];
                break;
            }
        }

        $complianceByType = [];
        foreach (\App\Http\Controllers\AttorneyComplianceFormController::FORM_TYPES as $type => $meta) {
            $complianceByType[$type] = [
                'meta'    => $meta,
                'records' => $case->complianceForms->where('form_type', $type)->values(),
            ];
        }

        return view('attorney.Conclusion.direct_complaint_workflow', [
            'case'             => $case,
            'steps'            => $steps,
            'totalSteps'       => $totalSteps,
            'completedSteps'   => $completedSteps,
            'currentStep'      => $currentStep,
            'complianceByType' => $complianceByType,
        ]);
    }

    public function investigation(Request $request, $id)
    {
        $case = AttorneyCase::with('investigation.updates')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_investigation', [
            'case' => $case,
        ]);
    }

    public function storeInvestigationUpdate(Request $request, $id)
    {
        $case = AttorneyCase::with('investigation')->findOrFail($id);

        if (!$case->investigation) {
            return back()->withErrors(['note' => 'Fadlan marka hore bilow baaritaanka ka hor intaadan isbeddel darin.']);
        }

        $data = $request->validate([
            'note' => 'required|string',
        ]);

        $case->investigation->updates()->create([
            'note'     => $data['note'],
            'added_by' => $request->user()->name,
        ]);

        return redirect()->route('attorney-cases.workflow.investigation', $case->ACID)
            ->with('success', 'Isbeddelka waa la daray.');
    }

    public function investigationForm(Request $request, $id)
    {
        $case = AttorneyCase::with(['investigation', 'accused', 'evidenceItems', 'legalProvisions'])->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_investigation_form', [
            'case'                 => $case,
            'offenceCategories'    => \App\Models\CaseCategory::where('case_name', 'Ciqaabta')
                ->whereNotNull('sub_case')->distinct()->orderBy('sub_case')->pluck('sub_case'),
            'legalProvisionOptions' => \App\Models\CaseCategory::where('case_name', 'Ciqaabta')
                ->whereNotNull('rule')->orderByRaw('CAST(SUBSTRING_INDEX(rule, " ", -1) AS UNSIGNED)')->get(),
            'evidenceTypeOptions'  => self::INVESTIGATION_EVIDENCE_TYPE_OPTIONS,
            'sexOptions'           => self::SEX_OPTIONS,
            'victimTypeOptions'    => self::VICTIM_TYPE_OPTIONS,
        ]);
    }

    public function storeInvestigation(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'commencement_date'   => 'required|date',

            'accused'                      => 'nullable|array',
            'accused.*.full_name'          => 'required_with:accused|string|max:150',
            'accused.*.mother_name'        => 'nullable|string|max:150',
            'accused.*.gender'             => 'nullable|string|max:20',
            'accused.*.address'            => 'nullable|string',
            'accused.*.id_number'          => 'nullable|string|max:50',

            'offence_category'    => 'required|string|max:255',
            'specific_offence'    => 'nullable|string|max:255',
            'legal_provisions'                 => 'nullable|array',
            'legal_provisions.*.provision'     => 'nullable|string|max:255',

            'victim_type'         => 'required|in:' . implode(',', array_keys(self::VICTIM_TYPE_OPTIONS)),

            'evidence'                      => 'nullable|array',
            'evidence.*.existing_id'        => 'nullable|integer',
            'evidence.*.evidence_type'      => 'nullable|string|max:50',
            'evidence.*.description'        => 'nullable|string',

            'case_description'           => 'nullable|string',
            'location_of_offence'        => 'nullable|string|max:255',
            'approximate_time_of_offence' => 'nullable|string|max:100',

            'prosecutor_name'       => 'required|string|max:150',
            'prosecutor_department' => 'nullable|string|max:150',
            'prosecutor_signature'  => 'required|string|max:150',
            'signature_date'        => 'required|date',

            'initial_evidence_file'      => 'nullable|file|max:10240',
            'supporting_documents_file'  => 'nullable|file|max:10240',
        ]);

        $case->update([
            'offense_type'      => $data['offence_category'],
            'summary'           => $data['case_description'] ?? $case->summary,
            'incident_location' => $data['location_of_offence'] ?? $case->incident_location,
            'incident_time'     => $data['approximate_time_of_offence'] ?? $case->incident_time,
        ]);

        $case->accused()->delete();
        foreach ($data['accused'] ?? [] as $row) {
            \App\Models\AttorneyCaseAccused::create(array_merge(['attorney_case_id' => $case->ACID], $row));
        }

        $case->legalProvisions()->delete();
        foreach ($data['legal_provisions'] ?? [] as $row) {
            if (empty($row['provision'])) {
                continue;
            }
            \App\Models\AttorneyCaseLegalProvision::create([
                'attorney_case_id' => $case->ACID,
                'provision'        => $row['provision'],
            ]);
        }

        $keptEvidenceIds = [];
        foreach ($request->input('evidence', []) as $index => $row) {
            $existingId = $row['existing_id'] ?? null;
            $item       = $existingId ? $case->evidenceItems()->find($existingId) : null;

            $attributes = [
                'evidence_type' => $row['evidence_type'] ?? 'Kale',
                'description'   => $row['description'] ?? null,
            ];

            $item = $item ? tap($item)->update($attributes)
                : \App\Models\AttorneyCaseEvidenceItem::create(array_merge(['attorney_case_id' => $case->ACID], $attributes));

            $keptEvidenceIds[] = $item->id;
        }
        $case->evidenceItems()->whereNotIn('id', $keptEvidenceIds ?: [0])->delete();

        $investigationData = [
            'start_date'             => $data['commencement_date'],
            'specific_offence'       => $data['specific_offence'] ?? null,
            'victim_type'            => $data['victim_type'],
            'prosecutor_name'        => $data['prosecutor_name'],
            'prosecutor_department'  => $data['prosecutor_department'] ?? null,
            'prosecutor_signature'   => $data['prosecutor_signature'],
            'signature_date'         => $data['signature_date'],
            'created_by'             => $request->user()->name,
        ];

        if ($request->hasFile('initial_evidence_file')) {
            $investigationData['initial_evidence_file'] = $request->file('initial_evidence_file')->store(
                'uploads/attorney/investigations/' . $case->ACID,
                'public'
            );
        }

        if ($request->hasFile('supporting_documents_file')) {
            $investigationData['supporting_documents_file'] = $request->file('supporting_documents_file')->store(
                'uploads/attorney/investigations/' . $case->ACID,
                'public'
            );
        }

        \App\Models\AttorneyInvestigation::updateOrCreate(
            ['attorney_case_id' => $case->ACID],
            $investigationData
        );

        return redirect()->route('attorney-cases.workflow', $case->ACID)
            ->with('success', 'Baaritaanka waa la bilaabay.');
    }

    public function sendToCourt(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);
        $case->update(['status' => 'Ku Jira Maxkamadda']);
        $case->activities()->create([
            'user_id'     => $request->user()->id,
            'type'        => 'sent_to_court',
            'description' => "{$request->user()->name} wuxuu dacwadan u diray Maxkamadda.",
        ]);

        return redirect()->route('attorney-cases.workflow', $case->ACID)
            ->with('success', 'Dacwadda waa loo diray Maxkamadda.');
    }

    public function investigationDecision(Request $request, $id)
    {
        $case = AttorneyCase::with('investigationDecision')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_investigation_decision', [
            'case'      => $case,
            'decisions' => self::INVESTIGATION_DECISIONS,
        ]);
    }

    public function investigationDecisionForm(Request $request, $id)
    {
        $case = AttorneyCase::with('investigationDecision')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_investigation_decision_form', [
            'case'                     => $case,
            'decisions'                => self::INVESTIGATION_DECISIONS,
            'evidenceQualityOptions'   => self::EVIDENCE_QUALITY_OPTIONS,
            'evidenceCompletenessOptions' => self::EVIDENCE_COMPLETENESS_OPTIONS,
            'legalSufficiencyOptions'  => self::LEGAL_SUFFICIENCY_OPTIONS,
            'legalBasisOptions'        => self::LEGAL_BASIS_OPTIONS,
            'riskLevelOptions'         => self::RISK_LEVEL_OPTIONS,
            'riskFactorOptions'        => self::RISK_FACTOR_OPTIONS,
        ]);
    }

    public function storeInvestigationDecision(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            // CID Investigation Summary
            'investigation_summary'           => 'required|string',

            // Evidence Assessment
            'evidence_quality'                => 'nullable|in:' . implode(',', self::EVIDENCE_QUALITY_OPTIONS),
            'evidence_completeness'            => 'nullable|in:' . implode(',', self::EVIDENCE_COMPLETENESS_OPTIONS),
            'evidence_assessment_notes'        => 'nullable|string',

            // Witness Interviews
            'witnesses_interviewed'            => 'nullable|string|max:50',
            'witness_interview_notes'          => 'nullable|string',

            // Legal Assessment
            'legal_sufficiency'                => 'nullable|in:' . implode(',', self::LEGAL_SUFFICIENCY_OPTIONS),
            'legal_basis_identified'           => 'nullable|in:' . implode(',', self::LEGAL_BASIS_OPTIONS),
            'legal_assessment_notes'           => 'nullable|string',

            // Investigation Decision
            'decision'                         => 'required|in:' . implode(',', self::INVESTIGATION_DECISIONS),
            'reasoning'                         => 'nullable|string',
            'decision_date'                     => 'required|date',
            'next_steps'                        => 'nullable|string',

            // Resource Requirements
            'additional_investigation_needed'   => 'nullable|boolean',
            'estimated_completion_time'         => 'nullable|string|max:100',
            'resource_requirements'             => 'nullable|string',

            // Risk Assessment
            'overall_risk_level'                => 'nullable|in:' . implode(',', self::RISK_LEVEL_OPTIONS),
            'risk_factors'                       => 'nullable|array',
            'risk_factors.*'                     => 'in:' . implode(',', self::RISK_FACTOR_OPTIONS),
            'risk_mitigation_strategies'         => 'nullable|string',

            // Supporting Documentation
            'cid_investigation_report'          => 'nullable|file|max:10240',
            'evidence_photographs'              => 'nullable|file|max:10240',
            'witness_statements'                => 'nullable|file|max:10240',
            'other_documents'                   => 'nullable|file|max:10240',

            // Approval
            'recommended_by'                    => 'nullable|string|max:255',
            'recommended_date'                  => 'nullable|date',
            'approved_by'                        => 'nullable|string|max:255',
            'approved_date'                      => 'nullable|date',
        ]);

        $data['decided_by']                       = $request->user()->name;
        $data['additional_investigation_needed']  = $request->boolean('additional_investigation_needed');

        $uploadDir = 'uploads/attorney/investigation-decisions/' . $case->ACID;
        foreach ([
            'cid_investigation_report' => 'cid_investigation_report_path',
            'evidence_photographs'     => 'evidence_photographs_path',
            'witness_statements'       => 'witness_statements_path',
            'other_documents'          => 'other_documents_path',
        ] as $fileField => $pathColumn) {
            if ($request->hasFile($fileField)) {
                $data[$pathColumn] = $request->file($fileField)->store($uploadDir, 'public');
            }
            unset($data[$fileField]);
        }

        AttorneyCaseInvestigationDecision::updateOrCreate(
            ['attorney_case_id' => $case->ACID],
            $data
        );

        return redirect()->route('attorney-cases.workflow', $case->ACID)
            ->with('success', 'Go\'aanka baaritaanka waa la keydiyay.');
    }

    // ── Investigation Extension ─────────────────────────────────────
    public function investigationExtension(Request $request, $id)
    {
        $case = AttorneyCase::with('investigationExtension.accused')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_investigation_extension', [
            'case' => $case,
        ]);
    }

    public function investigationExtensionForm(Request $request, $id)
    {
        $case = AttorneyCase::with(['investigationExtension.accused', 'accused'])->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_investigation_extension_form', [
            'case'                   => $case,
            'courts'                 => \App\Models\Court::orderBy('longName')->get(),
            'extensionPeriodOptions' => self::EXTENSION_PERIOD_OPTIONS,
            'sexOptions'             => self::SEX_OPTIONS,
        ]);
    }

    public function storeInvestigationExtension(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'date_registered_investigation'         => 'required|date',
            'incident_type'                          => 'required|string|max:255',
            'legal_articles'                          => 'required|string',
            'date_offence_occurred'                   => 'required|date',
            'offence_location'                        => 'required|string|max:255',
            'date_investigation_commenced'             => 'required|date',
            'court_name'                               => 'required|string|max:150',
            'court_case_reference'                     => 'required|string|max:100',

            'accused'                      => 'required|array|min:1',
            'accused.*.full_name'          => 'required|string|max:150',
            'accused.*.mother_name'        => 'required|string|max:150',
            'accused.*.sex'                => 'required|string|max:20',
            'accused.*.residence'          => 'required|string|max:255',

            'reason_ongoing_investigation'              => 'nullable|boolean',
            'reason_awaiting_scan_results'               => 'nullable|boolean',
            'reason_awaiting_institutional_experts'      => 'nullable|boolean',
            'reason_awaiting_witness_statements'         => 'nullable|boolean',
            'reason_other'                                => 'nullable|boolean',
            'reason_other_specify'                        => 'nullable|string',

            'extension_period'       => 'required|in:' . implode(',', self::EXTENSION_PERIOD_OPTIONS),
            'extension_period_other' => 'nullable|string|max:100',

            'prosecutor_name'  => 'required|string|max:150',
            'prosecutor_title' => 'required|string|max:255',
        ]);

        $accused = $data['accused'];
        unset($data['accused']);

        foreach (['reason_ongoing_investigation', 'reason_awaiting_scan_results', 'reason_awaiting_institutional_experts', 'reason_awaiting_witness_statements', 'reason_other'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $extension = AttorneyCaseInvestigationExtension::updateOrCreate(
            ['attorney_case_id' => $case->ACID],
            $data
        );

        $extension->accused()->delete();
        foreach ($accused as $row) {
            $extension->accused()->create($row);
        }

        return redirect()->route('attorney-cases.workflow.investigation-extension', $case->ACID)
            ->with('success', 'Codsiga kordhinta baaritaanka waa la keydiyay.');
    }

    public function approveInvestigationExtension(Request $request, $id)
    {
        $case = AttorneyCase::with('investigationExtension')->findOrFail($id);

        $data = $request->validate([
            'decision' => 'required|in:La Ansixiyay,La Diiday',
            'reason'   => 'nullable|string',
        ]);

        $case->investigationExtension()->firstOrFail()->update([
            'status'           => $data['decision'],
            'approval_reason'  => $data['reason'] ?? null,
            'approved_by'      => $request->user()->name,
            'approved_date'    => now()->format('Y-m-d'),
        ]);

        return redirect()->route('attorney-cases.workflow.investigation-extension', $case->ACID)
            ->with('success', 'Codsiga waa la ' . ($data['decision'] === 'La Ansixiyay' ? 'ansixiyay' : 'diiday') . '.');
    }
}
