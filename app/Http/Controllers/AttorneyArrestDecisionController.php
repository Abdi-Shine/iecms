<?php

namespace App\Http\Controllers;

use App\Models\AttorneyCase;
use App\Models\AttorneyCaseArrestDecision;
use App\Models\AttorneyCaseArrestWithoutWarrant;
use App\Models\AttorneyCaseAssetRecovery;
use App\Models\AttorneyCaseSearchAndSeizure;
use App\Models\AttorneyCaseWarrantOfArrest;
use Illuminate\Http\Request;

class AttorneyArrestDecisionController extends Controller
{
    public const ARREST_DECISION_OPTIONS = ['Loo Baahan Yahay Xidhitaan', 'Looma Baahna Xidhitaan'];
    public const URGENCY_LEVEL_OPTIONS   = ['Hoose', 'Dhexdhexaad', 'Sarreeya', 'Degdeg Ah'];
    public const NEXT_ACTION_OPTIONS     = ['Xidhitaan Aan Waaran Lahayn', 'Codsiga Waaran Xidhitaan', 'Wax Kale'];

    public const WARRANTLESS_GROUNDS_OPTIONS = ['Falka Hore', 'Halis Degdeg Ah', 'Cadaymo Baabi\'in Kara', 'Kale'];

    public const WARRANT_STATUS_OPTIONS = ['Sugaya', 'La Ansixiyay', 'La Diiday'];
    public const SEIZURE_STATUS_OPTIONS = ['Sugaya', 'La Qabtay', 'La Diiday'];

    /**
     * Landing page listing the 5 forms required for this step.
     */
    public function show(Request $request, $id)
    {
        $case = AttorneyCase::with([
            'arrestDecision', 'arrestWithoutWarrant', 'warrantOfArrest', 'searchAndSeizure', 'assetRecovery',
        ])->findOrFail($id);

        $forms = [
            [
                'key'         => 'arrest-decision',
                'title'       => 'Go\'aanka Xidhitaanka',
                'description' => 'Go\'aami haddii xidhitaan loo baahan yahay',
                'complete'    => (bool) $case->arrestDecision,
                'route'       => route('attorney-cases.workflow.arrest-decision.arrest-decision.form', $case->ACID),
            ],
            [
                'key'         => 'arrest-without-warrant',
                'title'       => 'Xidhitaan Aan Waaran Lahayn',
                'description' => 'Dukumeentiga xidhitaanka aan waaran lahayn',
                'complete'    => (bool) $case->arrestWithoutWarrant,
                'route'       => route('attorney-cases.workflow.arrest-decision.arrest-without-warrant.form', $case->ACID),
            ],
            [
                'key'         => 'warrant-of-arrest',
                'title'       => 'Waaran Xidhitaan',
                'description' => 'Codsiga iyo dukumeentiga waaranka xidhitaanka',
                'complete'    => (bool) $case->warrantOfArrest,
                'route'       => route('attorney-cases.workflow.arrest-decision.warrant-of-arrest.form', $case->ACID),
            ],
            [
                'key'         => 'search-and-seizure',
                'title'       => 'Baaritaan Iyo Qabashada',
                'description' => 'Codso waaran baaritaan iyo qabasho',
                'complete'    => (bool) $case->searchAndSeizure,
                'route'       => route('attorney-cases.workflow.arrest-decision.search-and-seizure.form', $case->ACID),
            ],
            [
                'key'         => 'asset-recovery',
                'title'       => 'Soo Celinta Hantida',
                'description' => 'Codso qabashada iyo la wareegga hantida',
                'complete'    => (bool) $case->assetRecovery,
                'route'       => route('attorney-cases.workflow.arrest-decision.asset-recovery.form', $case->ACID),
            ],
        ];

        return view('attorney.Conclusion.direct_complaint_arrest_decision', [
            'case'  => $case,
            'forms' => $forms,
        ]);
    }

    // ── 1. Arrest Decision ──────────────────────────────────────────
    public function arrestDecisionForm(Request $request, $id)
    {
        $case = AttorneyCase::with(['arrestDecision', 'accused'])->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_arrest_decision_form', [
            'case'             => $case,
            'decisionOptions'  => self::ARREST_DECISION_OPTIONS,
            'urgencyOptions'   => self::URGENCY_LEVEL_OPTIONS,
            'nextActionOptions' => self::NEXT_ACTION_OPTIONS,
        ]);
    }

    public function storeArrestDecision(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'suspect_name'        => 'nullable|string|max:150',
            'decision'            => 'required|in:' . implode(',', self::ARREST_DECISION_OPTIONS),
            'legal_grounds'       => 'nullable|string',
            'urgency_level'       => 'nullable|in:' . implode(',', self::URGENCY_LEVEL_OPTIONS),
            'flight_risk'         => 'nullable|boolean',
            'public_safety_risk'  => 'nullable|boolean',
            'reasoning'           => 'nullable|string',
            'next_action'         => 'nullable|in:' . implode(',', self::NEXT_ACTION_OPTIONS),
            'decision_date'       => 'required|date',
        ]);

        $data['flight_risk']        = $request->boolean('flight_risk');
        $data['public_safety_risk'] = $request->boolean('public_safety_risk');
        $data['decided_by']         = $request->user()->name;

        AttorneyCaseArrestDecision::updateOrCreate(['attorney_case_id' => $case->ACID], $data);

        return redirect()->route('attorney-cases.workflow.arrest-decision', $case->ACID)
            ->with('success', 'Go\'aanka xidhitaanka waa la keydiyay.');
    }

    // ── 2. Arrest Without Warrant ───────────────────────────────────
    public function arrestWithoutWarrantForm(Request $request, $id)
    {
        $case = AttorneyCase::with('arrestWithoutWarrant')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_arrest_without_warrant_form', [
            'case'           => $case,
            'groundsOptions' => self::WARRANTLESS_GROUNDS_OPTIONS,
        ]);
    }

    public function storeArrestWithoutWarrant(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'suspect_name'                    => 'nullable|string|max:150',
            'arrest_date'                      => 'required|date',
            'arrest_time'                      => 'nullable',
            'arrest_location'                  => 'nullable|string|max:255',
            'arresting_officer_name'           => 'nullable|string|max:150',
            'arresting_officer_rank'           => 'nullable|string|max:100',
            'grounds_for_warrantless_arrest'   => 'nullable|in:' . implode(',', self::WARRANTLESS_GROUNDS_OPTIONS),
            'circumstances'                    => 'nullable|string',
            'force_used'                       => 'nullable|boolean',
            'force_description'                => 'nullable|string',
            'rights_informed'                  => 'nullable|boolean',
            'witnesses_present'                => 'nullable|string|max:255',
            'ob_reference'                      => 'nullable|string|max:100',
            'reporting_officer'                => 'nullable|string|max:150',
            'report_date'                       => 'nullable|date',
        ]);

        $data['force_used']      = $request->boolean('force_used');
        $data['rights_informed'] = $request->boolean('rights_informed');

        AttorneyCaseArrestWithoutWarrant::updateOrCreate(['attorney_case_id' => $case->ACID], $data);

        return redirect()->route('attorney-cases.workflow.arrest-decision', $case->ACID)
            ->with('success', 'Dukumeentiga xidhitaanka aan waaran lahayn waa la keydiyay.');
    }

    // ── 3. Warrant Of Arrest ────────────────────────────────────────
    public function warrantOfArrestForm(Request $request, $id)
    {
        $case = AttorneyCase::with('warrantOfArrest')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_warrant_of_arrest_form', [
            'case'           => $case,
            'statusOptions'  => self::WARRANT_STATUS_OPTIONS,
        ]);
    }

    public function storeWarrantOfArrest(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'suspect_name'                 => 'nullable|string|max:150',
            'suspect_address'              => 'nullable|string|max:255',
            'offence_alleged'              => 'nullable|string',
            'legal_provision'              => 'nullable|string|max:255',
            'supporting_evidence_summary'  => 'nullable|string',
            'application_date'             => 'required|date',
            'applying_prosecutor'          => 'nullable|string|max:150',
            'court_name'                   => 'nullable|string|max:150',
            'judge_name'                   => 'nullable|string|max:150',
            'warrant_status'               => 'nullable|in:' . implode(',', self::WARRANT_STATUS_OPTIONS),
            'warrant_number'               => 'nullable|string|max:100',
            'issue_date'                   => 'nullable|date',
            'expiry_date'                  => 'nullable|date',
            'special_conditions'           => 'nullable|string',
            'warrant_document'             => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('warrant_document')) {
            $data['warrant_document_path'] = $request->file('warrant_document')->store(
                'uploads/attorney/warrant-of-arrest/' . $case->ACID,
                'public'
            );
        }
        unset($data['warrant_document']);

        AttorneyCaseWarrantOfArrest::updateOrCreate(['attorney_case_id' => $case->ACID], $data);

        return redirect()->route('attorney-cases.workflow.arrest-decision', $case->ACID)
            ->with('success', 'Waaranka xidhitaanka waa la keydiyay.');
    }

    // ── 4. Search And Seizure ───────────────────────────────────────
    public function searchAndSeizureForm(Request $request, $id)
    {
        $case = AttorneyCase::with('searchAndSeizure')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_search_and_seizure_form', [
            'case'          => $case,
            'statusOptions' => self::WARRANT_STATUS_OPTIONS,
        ]);
    }

    public function storeSearchAndSeizure(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'location_to_search'        => 'nullable|string|max:255',
            'items_sought'              => 'nullable|string',
            'grounds_for_search'        => 'nullable|string',
            'application_date'          => 'required|date',
            'applying_officer'          => 'nullable|string|max:150',
            'warrant_status'            => 'nullable|in:' . implode(',', self::WARRANT_STATUS_OPTIONS),
            'warrant_number'            => 'nullable|string|max:100',
            'search_conducted_date'     => 'nullable|date',
            'items_seized'              => 'nullable|string',
            'search_conducted_by'       => 'nullable|string|max:150',
            'witnesses_present'         => 'nullable|string|max:255',
            'property_receipt_issued'   => 'nullable|boolean',
            'search_report'             => 'nullable|file|max:10240',
        ]);

        $data['property_receipt_issued'] = $request->boolean('property_receipt_issued');

        if ($request->hasFile('search_report')) {
            $data['search_report_path'] = $request->file('search_report')->store(
                'uploads/attorney/search-and-seizure/' . $case->ACID,
                'public'
            );
        }
        unset($data['search_report']);

        AttorneyCaseSearchAndSeizure::updateOrCreate(['attorney_case_id' => $case->ACID], $data);

        return redirect()->route('attorney-cases.workflow.arrest-decision', $case->ACID)
            ->with('success', 'Codsiga baaritaanka iyo qabashada waa la keydiyay.');
    }

    // ── 5. Asset Recovery ───────────────────────────────────────────
    public function assetRecoveryForm(Request $request, $id)
    {
        $case = AttorneyCase::with('assetRecovery')->findOrFail($id);

        return view('attorney.Conclusion.direct_complaint_asset_recovery_form', [
            'case'          => $case,
            'statusOptions' => self::SEIZURE_STATUS_OPTIONS,
        ]);
    }

    public function storeAssetRecovery(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'asset_description'         => 'nullable|string',
            'estimated_value'           => 'nullable|string|max:100',
            'asset_location'            => 'nullable|string|max:255',
            'ownership_details'         => 'nullable|string',
            'legal_basis_for_seizure'   => 'nullable|string',
            'application_date'          => 'required|date',
            'requesting_officer'        => 'nullable|string|max:150',
            'court_order_reference'     => 'nullable|string|max:100',
            'seizure_status'            => 'nullable|in:' . implode(',', self::SEIZURE_STATUS_OPTIONS),
            'seizure_date'              => 'nullable|date',
            'custody_location'          => 'nullable|string|max:255',
            'disposition_notes'         => 'nullable|string',
            'supporting_document'       => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('supporting_document')) {
            $data['supporting_document_path'] = $request->file('supporting_document')->store(
                'uploads/attorney/asset-recovery/' . $case->ACID,
                'public'
            );
        }
        unset($data['supporting_document']);

        AttorneyCaseAssetRecovery::updateOrCreate(['attorney_case_id' => $case->ACID], $data);

        return redirect()->route('attorney-cases.workflow.arrest-decision', $case->ACID)
            ->with('success', 'Codsiga soo celinta hantida waa la keydiyay.');
    }
}
