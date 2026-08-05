<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use Illuminate\Http\Request;

class CriminalExhibitController extends Controller
{
    private function isAdmin(Request $request): bool
    {
        return $request->user()->group?->roles->contains('name', 'CID Institution Admin') ?? false;
    }

    public function index(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Exhibit Management is restricted to Institution Admin.');
        }

        $case = CriminalCase::with('exhibits')->findOrFail($id);

        return view('cid.cases.exhibits', compact('case'));
    }

    public function store(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Exhibit Management is restricted to Institution Admin.');
        }

        $case = CriminalCase::findOrFail($id);

        $data = $request->validate([
            'description'        => 'required|string|max:255',
            'receiving_officer'  => 'required|string|max:150',
            'storage_location'   => 'nullable|string|max:150',
            'condition'          => 'nullable|string|max:100',
        ]);

        $data['current_holder'] = $data['receiving_officer'];
        $data['added_by'] = $request->user()->name ?? 'Staff';

        $case->exhibits()->create($data);

        return redirect()->route('criminal-cases.exhibits.index', $case->id)->with('success', 'Exhibit logged.');
    }

    public function updateStatus(Request $request, $id, $exhibitId)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Exhibit Management is restricted to Institution Admin.');
        }

        $case = CriminalCase::findOrFail($id);
        $exhibit = $case->exhibits()->findOrFail($exhibitId);

        $data = $request->validate([
            'status'         => 'required|in:' . implode(',', \App\Models\CriminalCaseExhibit::STATUSES),
            'current_holder' => 'nullable|string|max:150',
        ]);

        $exhibit->update($data);

        return redirect()->route('criminal-cases.exhibits.index', $case->id)->with('success', 'Exhibit updated.');
    }
}
