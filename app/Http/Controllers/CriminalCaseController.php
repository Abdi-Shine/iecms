<?php

namespace App\Http\Controllers;

use App\Models\CriminalCase;
use Illuminate\Http\Request;

class CriminalCaseController extends Controller
{
    public function index(Request $request)
    {
        $query = CriminalCase::query();

        if ($request->filled('search')) {
            $query->where('case_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('stage')) {
            $query->where('current_stage', $request->stage);
        }

        $cases = $query->with('arrest')->latest()->paginate(15)->withQueryString();

        return view('cid.cases.index', compact('cases'));
    }

    public function store(Request $request)
    {
        $case = CriminalCase::create([
            'priority' => $request->input('priority', 'Routine'),
            'added_by' => $request->user()->name ?? 'Staff',
        ]);

        return redirect()->route('criminal-cases.workflow.arrest.form', $case->id);
    }
}
