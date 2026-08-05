<?php

namespace App\Http\Controllers;

use App\Models\CriminalDetainee;
use Illuminate\Http\Request;

class CriminalRemandController extends Controller
{
    /**
     * Legal compliance dashboard: % of detainees within legal remand
     * limits, plus a 72/48/24-hour escalation list — computed live
     * from criminal_detainees.legal_deadline, same query-time approach
     * as Phase 2's Period Alerts (no scheduler infrastructure exists in
     * this app to push background notifications from).
     */
    public function index(Request $request)
    {
        $active = CriminalDetainee::whereNotIn('custody_status', ['Released', 'Granted Bail', 'Transferred', 'Deceased'])
            ->whereNotNull('legal_deadline')
            ->with('criminalCase')
            ->get();

        $withinLimit = $active->filter(fn ($d) => $d->legal_deadline->isFuture())->count();
        $total = $active->count();
        $compliancePercent = $total > 0 ? round(($withinLimit / $total) * 100) : 100;

        $escalations = $active->filter(fn ($d) => $d->legal_deadline->lte(now()->addHours(72)))
            ->sortBy('legal_deadline')
            ->values();

        return view('cid.cases.remand-dashboard', compact('active', 'compliancePercent', 'total', 'withinLimit', 'escalations'));
    }
}
