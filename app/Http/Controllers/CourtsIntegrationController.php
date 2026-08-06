<?php

namespace App\Http\Controllers;

use App\Models\CivilCaseTransfer;
use App\Models\Court;
use App\Models\DistricCivilRegistration;
use App\Models\StatusProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourtsIntegrationController extends Controller
{
    public function transfer(Request $request)
    {
        $query = DistricCivilRegistration::with(['court', 'appeal', 'transfer', 'judgments.receipts'])
            ->whereIn('Status', ['Rafcaan', 'La Wareejiyay'])
            ->orderByDesc('CRID');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('FileNo', 'like', "%$s%")
                  ->orWhere('CaseType', 'like', "%$s%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('Status', $request->status);
        }

        $perPage = $this->resolvePerPage($request);

        $records = $query->paginate($perPage)->withQueryString();

        $courts        = Court::where('status', 'active')->orderBy('longName')->get();
        $statuses      = StatusProcess::orderBy('name')->get();
        $isKaaliyeSare = auth()->user()->hasPermission('Transfer Approval', 'view');

        $stats = [
            'total'       => DistricCivilRegistration::count(),
            'rafcaan'     => DistricCivilRegistration::where('Status', 'Rafcaan')->count(),
            'transferred' => DistricCivilRegistration::where('Status', 'La Wareejiyay')->count(),
            'closed'      => DistricCivilRegistration::where('Status', 'Closed')->count(),
        ];

        return view('distract_courts.integration.courts_integration_transfer',
            compact('records', 'courts', 'stats', 'statuses', 'isKaaliyeSare'));
    }

    public function received(Request $request)
    {
        $query = CivilCaseTransfer::with(['civilCase.court', 'toCourt', 'fromCourt'])
            ->where('status', 'Submitted')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('civilCase', function ($q) use ($s) {
                $q->where('FileNo', 'like', "%$s%");
            });
        }

        $perPage = $this->resolvePerPage($request);

        $records = $query->paginate($perPage)->withQueryString();

        $courts = Court::where('status', 'active')->orderBy('longName')->get();

        return view('distract_courts.integration.courts_integration_received',
            compact('records', 'courts'));
    }
}

