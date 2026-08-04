<?php

namespace App\Http\Controllers;

use App\Models\AttorneyCase;
use App\Models\AttorneyCaseInvestigationDecision;
use Illuminate\Http\Request;

class AttorneyCaseWorkflowController extends Controller
{
    public const INVESTIGATION_DECISIONS = ['Baaritaan Loo Baahan Yahay', 'Baaritaan Looma Baahna'];

    public function show(Request $request, $id)
    {
        $case = AttorneyCase::with(['investigationDecision', 'complianceForms.employee', 'complainants'])->findOrFail($id);

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
                'enabled'     => false,
            ],
            [
                'key'         => 'arrest-decision',
                'title'       => 'Arrest Decision',
                'description' => 'Decide if arrest is required',
                'formsCount'  => 5,
                'enabled'     => false,
            ],
            [
                'key'         => 'evidence-interviews',
                'title'       => 'Evidence & Interviews',
                'description' => 'Conduct interviews and manage evidence',
                'formsCount'  => 5,
                'enabled'     => false,
            ],
            [
                'key'         => 'investigation-extension',
                'title'       => 'Investigation Extension',
                'description' => 'Request extension if investigation time expires',
                'formsCount'  => 1,
                'enabled'     => false,
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

    public function storeInvestigationDecision(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'decision'      => 'required|in:' . implode(',', self::INVESTIGATION_DECISIONS),
            'reasoning'     => 'nullable|string',
            'decision_date' => 'required|date',
        ]);

        $data['decided_by'] = $request->user()->name;

        AttorneyCaseInvestigationDecision::updateOrCreate(
            ['attorney_case_id' => $case->ACID],
            $data
        );

        return redirect()->route('attorney-cases.workflow', $case->ACID)
            ->with('success', 'Go\'aanka baaritaanka waa la keydiyay.');
    }
}
