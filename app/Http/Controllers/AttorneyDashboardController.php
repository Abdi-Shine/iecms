<?php

namespace App\Http\Controllers;

use App\Models\AttorneyCase;
use App\Models\AttorneyProceeding;
use Illuminate\Http\Request;

class AttorneyDashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'totalCases'        => AttorneyCase::count(),
            'activeCases'       => AttorneyCase::whereNotIn('status', ['La Xukumay', 'La Sii Daayay', 'La Diiday', 'La Xiray'])->count(),
            'unassignedCases'   => AttorneyCase::whereDoesntHave('prosecutorAssignments')->count(),
            'directComplaints'  => AttorneyCase::where(function ($q) {
                $q->whereNull('reporting_agency')->orWhere('reporting_agency', '');
            })->count(),
            'cidReferrals'      => AttorneyCase::where('reporting_agency', 'like', '%CID%')->count(),
        ];

        $calendarEvents = AttorneyProceeding::with('attorneyCase')->get()->map(function ($p) {
            $color = match ($p->status) {
                'Completed' => '#10B981',
                'Cancelled' => '#DC2626',
                'Postponed' => '#F0B43C',
                default     => '#528CBE',
            };

            return [
                'id'    => $p->id,
                'title' => $p->attorneyCase->case_number ?? '—',
                'start' => $p->hearing_date->format('Y-m-d'),
                'color' => $color,
                'extendedProps' => [
                    'caseNumber'     => $p->attorneyCase->case_number ?? '—',
                    'caseTitle'      => $p->attorneyCase->title ?? '—',
                    'proceedingType' => $p->proceeding_type,
                    'status'         => $p->status,
                    'date'           => $p->hearing_date->format('Y-m-d'),
                    'time'           => $p->hearing_time ?? '—',
                ],
            ];
        })->values();

        $currentYear = now()->year;
        $monthlyByPriority = fn (string $priority) => array_values(array_map(
            fn ($m) => (int) (AttorneyCase::selectRaw('MONTH(date_reported) as month, COUNT(*) as total')
                ->where('priority', $priority)
                ->whereYear('date_reported', $currentYear)
                ->groupBy('month')
                ->pluck('total', 'month')[$m] ?? 0),
            range(1, 12)
        ));

        $filingActivity = [
            ['name' => 'Hoose',      'color' => '#16A34A', 'monthly' => $monthlyByPriority('Hoose')],
            ['name' => 'Dhexe',      'color' => '#528CBE', 'monthly' => $monthlyByPriority('Dhexdhexaad')],
            ['name' => 'Sare',       'color' => '#F0B43C', 'monthly' => $monthlyByPriority('Sarreeya')],
            ['name' => 'Degdeg ah',  'color' => '#DC2626', 'monthly' => $monthlyByPriority('Degdeg ah')],
        ];

        return view('attorney.dashboard.index', compact('stats', 'calendarEvents', 'filingActivity'));
    }
}
