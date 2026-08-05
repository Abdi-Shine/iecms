<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use Illuminate\Http\Request;

class CriminalCourtFormController extends Controller
{
    public function index(Request $request, $id)
    {
        $case = CriminalCase::with(['courtAppearanceForms.courtAppearance', 'courtAppearances'])->findOrFail($id);

        return view('cid.cases.court-forms', compact('case'));
    }

    public function store(Request $request, $id)
    {
        $case = CriminalCase::findOrFail($id);

        $data = $request->validate([
            'form_type'           => 'required|in:' . implode(',', array_keys(\App\Models\CriminalCourtAppearanceForm::TYPES)),
            'recipient_name'      => 'required|string|max:150',
            'recipient_role'      => 'nullable|string|max:50',
            'court_appearance_id' => 'nullable|exists:criminal_case_court_appearances,id',
        ]);

        $data['added_by'] = $request->user()->name ?? 'Staff';

        $case->courtAppearanceForms()->create($data);

        return redirect()->route('criminal-cases.court-forms.index', $case->id)
            ->with('success', 'Form generated.');
    }

    public function updateStatus(Request $request, $id, $formId)
    {
        $case = CriminalCase::findOrFail($id);
        $form = $case->courtAppearanceForms()->findOrFail($formId);

        $data = $request->validate([
            'status'      => 'required|in:' . implode(',', \App\Models\CriminalCourtAppearanceForm::STATUSES),
            'served_date' => 'nullable|date',
            'served_by'   => 'nullable|string|max:150',
        ]);

        $form->update($data);

        return redirect()->route('criminal-cases.court-forms.index', $case->id)
            ->with('success', 'Form status updated.');
    }
}
