<?php

namespace App\Http\Controllers;

use App\Models\CriminalCaseCourtAppearance;
use Illuminate\Http\Request;

class CriminalCourtCalendarController extends Controller
{
    public function index(Request $request)
    {
        $query = CriminalCaseCourtAppearance::with('criminalCase');

        if ($request->filled('court')) {
            $query->where('court_name', 'like', '%' . $request->court . '%');
        }
        if ($request->filled('officer')) {
            $query->where('responsible_officer', 'like', '%' . $request->officer . '%');
        }
        if ($request->filled('from')) {
            $query->whereDate('appearance_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('appearance_date', '<=', $request->to);
        }
        if ($request->filled('status') && $request->status === 'pending') {
            $query->whereNull('outcome');
        }

        $appearances = $query->orderBy('appearance_date')->paginate(20)->withQueryString();

        return view('cid.cases.court-calendar', compact('appearances'));
    }
}
