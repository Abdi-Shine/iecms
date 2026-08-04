<?php

namespace App\Http\Controllers;

use App\Models\DistrictCivilPayment;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class DistricCivilRegistrationController extends Controller
{
    public function districtPaymentReceipt($id)
    {
        return view('Courts.District_civil.finance.payment_receipt', $this->districtPaymentReceiptData($id));
    }

    public function districtPaymentReceiptPdf($id)
    {
        $data = $this->districtPaymentReceiptData($id);

        return \App\Support\CourtDocumentPdf::stream(
            'Courts.District_civil.finance.payment_receipt',
            $data,
            'Rasiid-' . str_pad($data['payment']->id, 4, '0', STR_PAD_LEFT) . '.pdf'
        );
    }

    private function districtPaymentReceiptData($id): array
    {
        $payment = DistrictCivilPayment::with(['court', 'tariff', 'cashier', 'approver'])->findOrFail($id);
        $court   = $payment->court;

        $qrDataUri = (new Builder(
            writer: new PngWriter(),
            data: route('civil-registration.payments.receipt', $payment->id),
            size: 220,
            margin: 0,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            foregroundColor: new Color(10, 40, 77),
            backgroundColor: new Color(255, 255, 255),
        ))->build()->getDataUri();

        return compact('payment', 'court', 'qrDataUri');
    }

    public function index(Request $request)
    {
        $query = \App\Models\DistricCivilRegistration::with('court')->orderByDesc('CRID');

        // Data isolation: Non-admins only see records from their assigned court
        if (auth()->check() && auth()->user()->position !== 'admin') {
            $userCourt = auth()->user()->employee->courtID ?? null;
            if ($userCourt) {
                $query->where('GradeCourt', $userCourt);
            } else {
                $query->where('GradeCourt', 'NONE');
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('FileNo', 'like', "%$s%")
                  ->orWhere('RegisterNo', 'like', "%$s%")
                  ->orWhere('Remarks', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->filled('sub_case')) {
            $query->where('SubCase', $request->sub_case);
        }

        $userCourtID = (auth()->check() && auth()->user()->position !== 'admin')
                        ? (auth()->user()->employee->courtID ?? null)
                        : null;

        $allRecords = \App\Models\DistricCivilRegistration::with('court')->orderByDesc('CRID');
        if ($userCourtID) $allRecords->where('GradeCourt', $userCourtID);
        $allRecords = $allRecords->get();

        $perPage = $this->resolvePerPage($request);

        $records        = $query->paginate($perPage)->withQueryString();
        $courts         = \App\Models\Court::orderBy('longName')->get();
        $caseTypes      = \App\Models\CaseType::orderBy('case_name')->get();
        $statuses       = \App\Models\StatusProcess::orderBy('name')->get();
        $civilSubCases  = \App\Models\CaseCategory::where('case_name', 'Madani')->pluck('sub_case');
        return view('Courts.District_civil.registration.district_civil_registration', compact('records', 'allRecords', 'courts', 'caseTypes', 'statuses', 'userCourtID', 'civilSubCases'));
    }

    public function create()
    {
        $courts        = \App\Models\Court::orderBy('longName')->get();
        $civilSubCases = \App\Models\CaseCategory::where('case_name', 'Madani')->pluck('sub_case');
        $userCourtID   = (auth()->check() && auth()->user()->position !== 'admin')
                        ? (auth()->user()->employee->courtID ?? null)
                        : null;

        return view('Courts.District_civil.registration.district_civil_registration_form', compact('courts', 'civilSubCases', 'userCourtID'));
    }

    public function edit($id)
    {
        $record        = \App\Models\DistricCivilRegistration::findOrFail($id);
        $courts        = \App\Models\Court::orderBy('longName')->get();
        $civilSubCases = \App\Models\CaseCategory::where('case_name', 'Madani')->pluck('sub_case');
        $userCourtID   = (auth()->check() && auth()->user()->position !== 'admin')
                        ? (auth()->user()->employee->courtID ?? null)
                        : null;

        return view('Courts.District_civil.registration.district_civil_registration_form', compact('record', 'courts', 'civilSubCases', 'userCourtID'));
    }

    public function show($id)
    {
        $case        = \App\Models\DistricCivilRegistration::with(['court', 'parties', 'documents', 'lawyers.lawyer', 'lawyers.party', 'legalRepresentatives.party', 'assignments.employee', 'hearings', 'districtCivilPayments.tariff'])->findOrFail($id);
        $handover    = \App\Models\CivilCaseHandover::where('civil_case_id', $id)->latest()->first();
        $returnFile  = \App\Models\CivilCaseReturnFile::where('civil_case_id', $id)->latest()->first();
        $scriptures  = \App\Models\HearingScripture::with('hearing')->where('civil_case_id', $id)->orderBy('created_at')->get();
        $judgments   = \App\Models\Judgment::with('receipts')->where('civil_case_id', $id)->orderByDesc('created_at')->get();
        $enforcement = \App\Models\CivilCaseEnforcement::where('civil_case_id', $id)->latest()->first();
        $appeal      = \App\Models\CivilCaseAppeal::where('civil_case_id', $id)->latest()->first();
        return view('Courts.District_civil.registration.district_civil_information', compact('case', 'handover', 'returnFile', 'scriptures', 'judgments', 'enforcement', 'appeal'));
    }

    public function supporting($id)
    {
        $case       = \App\Models\DistricCivilRegistration::with(['court', 'parties', 'documents', 'lawyers.lawyer', 'lawyers.party', 'assignments.employee', 'hearings', 'districtCivilPayments.tariff'])->findOrFail($id);
        $handover   = \App\Models\CivilCaseHandover::where('civil_case_id', $id)->latest()->first();
        $scriptures = \App\Models\HearingScripture::with('hearing')->where('civil_case_id', $id)->orderBy('created_at')->get();
        return view('Courts.District_civil.registration.district_civil_add_supporting', compact('case', 'handover', 'scriptures'));
    }

    public function paymentRequestForm($id = null)
    {
        $case = $id ? \App\Models\DistricCivilRegistration::with(['court', 'parties', 'payments'])->findOrFail($id) : null;

        $knownApplicants = \App\Models\Payment::select('payer_name', 'payer_phone', 'payer_email')
            ->whereNotNull('payer_name')
            ->orderByDesc('created_at')
            ->get()
            ->unique('payer_name')
            ->values();

        // Only needed for the standalone (no case) launch, which still creates a full
        // Payment record and requires a court/service/amount to be picked.
        $courts  = \App\Models\Court::orderBy('longName')->get();
        $tariffs = \App\Models\Tariff::where('status', 'Active')->orderBy('name_so')->get();

        return view('Courts.District_civil.registration.district_civil_add_payment', compact('case', 'knownApplicants', 'courts', 'tariffs'));
    }

    public function storePaymentRequest(Request $request)
    {
        // Case-specific launch: the form only collects the applicant's identity now
        // (court/service/amount fields were removed). Saved exclusively to
        // district_civil_payments (not the shared payments table) so it shows up in
        // "Codsiyada Lacag Bixinta" on the case page.
        if ($request->filled('civil_case_id')) {
            $validated = $request->validate([
                'civil_case_id' => 'required|exists:distric_civil_registrations,CRID',
                'payer_name'    => 'required|string|max:255',
                'payer_phone'   => 'required|string|max:20',
                'payer_email'   => 'required|email|max:255',
            ]);

            // Reuse the case's existing open payment request (still Pending/Awaiting
            // Approval) instead of creating a duplicate row every time this form is
            // submitted for the same case.
            $payment = \App\Models\DistrictCivilPayment::where('civil_case_id', $validated['civil_case_id'])
                ->whereIn('status', ['Pending', 'Awaiting Approval'])
                ->latest()
                ->first();

            $fields = [
                'payer_name'  => $validated['payer_name'],
                'payer_phone' => $validated['payer_phone'],
                'payer_email' => $validated['payer_email'],
            ];

            // Pull the applicant's most recent payment record (court/service/amount/
            // date/status) so the full record — not just their identity — lands in
            // district_civil_payments, matching what's shown in their payment history.
            $sourcePayment = \App\Models\Payment::where('payer_phone', $validated['payer_phone'])
                ->orWhere('payer_email', $validated['payer_email'])
                ->orderByDesc('created_at')
                ->first();

            if ($sourcePayment) {
                $fields['court_id']     = $sourcePayment->court_id;
                $fields['tariff_id']    = $sourcePayment->tariff_id;
                $fields['amount']       = $sourcePayment->amount;
                $fields['currency']     = $sourcePayment->currency;
                $fields['payment_date'] = $sourcePayment->payment_date;
                $fields['status']       = $sourcePayment->status;
            }

            if ($payment) {
                $payment->update($fields);
                $message = 'Macluumaadka codsiga lacag bixinta si guul leh ayaa loo cusboonaysiiyay.';
            } else {
                \App\Models\DistrictCivilPayment::create(array_merge([
                    'civil_case_id' => $validated['civil_case_id'],
                    'amount'        => 0,
                    'currency'      => 'USD',
                    'status'        => 'Pending',
                ], $fields));
                $message = 'Codsiga lacag bixinta si guul leh ayaa loo diray.';
            }

            return redirect()->route('civil-registration.supporting', $validated['civil_case_id'])
                ->with('success', $message);
        }

        // Standalone launch (no case): still creates a full Payment record.
        $validated = $request->validate([
            'payer_name'    => 'required|string|max:255',
            'payer_phone'   => 'required|string|max:20',
            'payer_email'   => 'required|email|max:255',
            'court_id'      => 'required|exists:courts,CAI',
            'tariff_id'     => 'required|exists:tariffs,id',
            'amount'        => 'required|numeric|min:0',
            'payment_date'  => 'required|date',
            'cashier_id'    => 'nullable|exists:employees,AID',
        ]);

        \App\Models\Payment::create([
            'payer_name'    => $validated['payer_name'],
            'payer_phone'   => $validated['payer_phone'],
            'payer_email'   => $validated['payer_email'],
            'court_id'      => $validated['court_id'],
            'tariff_id'     => $validated['tariff_id'],
            'amount'        => $validated['amount'],
            'currency'      => 'USD',
            'payment_date'  => $validated['payment_date'],
            'cashier_id'    => $validated['cashier_id'] ?? null,
            'status'        => 'Pending',
        ]);

        return redirect()->route('finance.applicant-requests')
            ->with('success', 'Codsiga lacag bixinta si guul leh ayaa loo diray.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'RegisterNo'       => 'required|string|max:50',
            'FileNo'           => 'required|string|max:50',
            'GradeCourt'       => 'required|string|max:50',
            'CaseType'         => 'required|string|max:50',
            'SubCase'          => 'nullable|string|max:100',
            'OpenDate'         => 'required|date',
            'NumberLetter'     => 'nullable|string|max:50',
            'LegalBasis'       => 'nullable|string',
            'Orders_Requested' => 'nullable|string',
            'Remarks'          => 'nullable|string',
            'Status'           => 'required|string|max:50',
        ]);

        \App\Models\DistricCivilRegistration::create(array_merge(
            $request->only('RegisterNo','FileNo','GradeCourt','CaseType','SubCase','OpenDate','NumberLetter','LegalBasis','Orders_Requested','Remarks','Status'),
            [
                'institution_id' => auth()->user()->institution_id,
                'addedBy'   => auth()->user()->name ?? 'Admin',
                'addedDate' => now()->format('Y-m-d'),
            ]
        ));

        return redirect()->route('civil-registration.index')
            ->with('success', 'Civil case registered successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'RegisterNo'       => 'required|string|max:50',
            'FileNo'           => 'required|string|max:50',
            'GradeCourt'       => 'required|string|max:50',
            'CaseType'         => 'required|string|max:50',
            'SubCase'          => 'nullable|string|max:100',
            'OpenDate'         => 'required|date',
            'NumberLetter'     => 'nullable|string|max:50',
            'LegalBasis'       => 'nullable|string',
            'Orders_Requested' => 'nullable|string',
            'Remarks'          => 'nullable|string',
            'Status'           => 'required|string|max:50',
        ]);

        $record = \App\Models\DistricCivilRegistration::findOrFail($id);
        $record->update(array_merge(
            $request->only('RegisterNo','FileNo','GradeCourt','CaseType','SubCase','OpenDate','NumberLetter','LegalBasis','Orders_Requested','Remarks','Status'),
            [
                'updatedBy'   => auth()->user()->name ?? 'Admin',
                'updatedDate' => now()->format('Y-m-d'),
            ]
        ));

        return redirect()->route('civil-registration.index')
            ->with('success', 'Civil case updated successfully.');
    }

    public function destroy($id)
    {
        $record = \App\Models\DistricCivilRegistration::findOrFail($id);
        $record->delete();
        return response()->json(['success' => true, 'message' => 'Civil case deleted successfully.']);
    }

    public function tracking(Request $request)
    {
        $baseQuery = \App\Models\DistricCivilRegistration::query();

        if (auth()->check() && auth()->user()->position !== 'admin') {
            $userCourt = auth()->user()->employee->courtID ?? null;
            if ($userCourt) {
                $baseQuery->where('GradeCourt', $userCourt);
            } else {
                $baseQuery->where('GradeCourt', 'NONE');
            }
        }

        $stats = [
            'total'   => (clone $baseQuery)->count(),
            'active'  => (clone $baseQuery)->whereIn('Status', ['Active', 'Gal Ku Qoris'])->count(),
            'pending' => (clone $baseQuery)->where('Status', 'Sug Qaatay')->count(),
            'done'    => (clone $baseQuery)->where('Status', 'Qaatay')->count(),
            'closed'  => (clone $baseQuery)->where('Status', 'Closed')->count(),
        ];

        $allStatuses = (clone $baseQuery)->whereNotNull('Status')->distinct()->orderBy('Status')->pluck('Status');

        $query = (clone $baseQuery)->with([
            'court', 'handover', 'parties', 'documents',
            'assignments.employee', 'lawyers.lawyer',
        ])->orderByDesc('CRID');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('FileNo', 'like', "%$s%")
                  ->orWhere('RegisterNo', 'like', "%$s%")
                  ->orWhereHas('court', fn($cq) => $cq->where('longName', 'like', "%$s%"))
                  ->orWhereHas('parties', function ($pq) use ($s) {
                      $pq->where('full_name', 'like', "%$s%")
                         ->orWhere('mother_name', 'like', "%$s%")
                         ->orWhere('contact_number', 'like', "%$s%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('Status', $request->status);
        }

        if ($request->filled('sub_case')) {
            $query->where('SubCase', $request->sub_case);
        }

        $perPage = $this->resolvePerPage($request);

        $records       = $query->paginate($perPage)->withQueryString();
        $courts        = \App\Models\Court::orderBy('longName')->get();
        $civilSubCases = \App\Models\CaseCategory::where('case_name', 'Madani')->pluck('sub_case');

        return view('Courts.District_civil.registration.district_civil_tracking', compact('records', 'courts', 'stats', 'allStatuses', 'civilSubCases'));
    }

    public function handover(Request $request)
    {
        $baseQuery = \App\Models\DistricCivilRegistration::query();

        if (auth()->check() && auth()->user()->position !== 'admin') {
            $userCourt = auth()->user()->employee->courtID ?? null;
            if ($userCourt) {
                $baseQuery->where('GradeCourt', $userCourt);
            } else {
                $baseQuery->where('GradeCourt', 'NONE');
            }
        }

        $stats = [
            'total'      => (clone $baseQuery)->count(),
            'galKuQoris' => (clone $baseQuery)->where('Status', 'Gal Ku Qoris')->count(),
            'diwaanGalin'=> (clone $baseQuery)->where('Status', 'Diwaan Galin')->count(),
            'thisMonth'  => (clone $baseQuery)->whereRaw("DATE_FORMAT(OpenDate, '%Y-%m') = ?", [date('Y-m')])->count(),
        ];

        $query = (clone $baseQuery)->with(['court', 'handover'])->orderByDesc('CRID');

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

        if ($request->filled('sub_case')) {
            $query->where('SubCase', $request->sub_case);
        }

        $perPage = $this->resolvePerPage($request);

        $records       = $query->paginate($perPage)->withQueryString();
        $statuses      = \App\Models\StatusProcess::orderBy('name')->get();
        $civilSubCases = \App\Models\CaseCategory::where('case_name', 'Madani')->pluck('sub_case');

        return view('Courts.District_civil.registration.district_civil_handover', compact('records', 'statuses', 'stats', 'civilSubCases'));
    }

    public function nextFileNo($courtcode)
    {
        $court = \App\Models\Court::where('courtcode', $courtcode)->firstOrFail();
        $short = $court->shortName;
        $currYear = date('Y');

        $last = \App\Models\DistricCivilRegistration::where('GradeCourt', $courtcode)
            ->where('FileNo', 'like', "{$short}/DML/%/{$currYear}")
            ->orderByDesc('CRID')
            ->value('FileNo');

        $serial = 1;
        if ($last) {
            // Format: SHORT/DML/SERIAL/YEAR
            $parts = explode('/', $last);
            if (count($parts) >= 3) {
                $serial = intval($parts[2]) + 1;
            }
        }

        $fileNo = sprintf('%s/DML/%02d/%s', $short, $serial, $currYear);

        return response()->json(['fileNo' => $fileNo]);
    }
}

