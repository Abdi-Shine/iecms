<?php

namespace App\Http\Controllers;

use App\Models\CriminalCaseArrest;
use App\Models\CriminalCaseCourtAppearance;
use App\Models\CriminalCaseCustody;
use Illuminate\Http\Request;

class CriminalPeriodAlertsController extends Controller
{
    /**
     * Computed at request time from existing dates — remand expiry,
     * upcoming court appearances, and warrant expiry. Not a background
     * job/notification engine: this app has no scheduled-command
     * infrastructure yet (no app/Console/Commands, no withSchedule()
     * call anywhere), so there is nothing to push in-system/email/SMS
     * alerts from. Building that scheduler is a separate piece of work;
     * this view is what's honestly buildable without it.
     */
    public function index(Request $request)
    {
        $now = now();

        $remandDeadlines = CriminalCaseCustody::with('criminalCase')
            ->whereNotNull('legal_deadline')
            ->where('legal_deadline', '<=', $now->copy()->addHours(72))
            ->orderBy('legal_deadline')
            ->get()
            ->map(fn ($c) => [
                'case'     => $c->criminalCase,
                'label'    => 'Remand expiry',
                'due_at'   => $c->legal_deadline,
                'overdue'  => $c->legal_deadline->isPast(),
            ]);

        $upcomingCourt = CriminalCaseCourtAppearance::with('criminalCase')
            ->whereNull('outcome')
            ->where('appearance_date', '<=', $now->copy()->addHours(48))
            ->orderBy('appearance_date')
            ->get()
            ->map(fn ($a) => [
                'case'     => $a->criminalCase,
                'label'    => 'Court appearance — ' . $a->hearing_type . ' at ' . $a->court_name,
                'due_at'   => $a->appearance_date,
                'overdue'  => $a->appearance_date->isPast(),
            ]);

        $warrantExpiries = CriminalCaseArrest::with('criminalCase')
            ->whereNotNull('warrant_expiry_date')
            ->where('warrant_expiry_date', '<=', $now->copy()->addHours(72))
            ->orderBy('warrant_expiry_date')
            ->get()
            ->map(fn ($a) => [
                'case'     => $a->criminalCase,
                'label'    => 'Warrant expiry — ' . $a->warrant_number,
                'due_at'   => $a->warrant_expiry_date,
                'overdue'  => $a->warrant_expiry_date->isPast(),
            ]);

        $alerts = $remandDeadlines->concat($upcomingCourt)->concat($warrantExpiries)
            ->sortBy('due_at')->values();

        return view('cid.cases.period-alerts', compact('alerts'));
    }
}
