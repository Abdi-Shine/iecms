<?php

namespace App\Http\Controllers;

use App\Models\DistrictFamilyPayment;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class DistrictFamilyRegistrationController extends Controller
{
    public function familyPaymentReceipt($id)
    {
        return view('distract_courts.District_civil.finance.payment_receipt', $this->familyPaymentReceiptData($id));
    }

    public function familyPaymentReceiptPdf($id)
    {
        $data = $this->familyPaymentReceiptData($id);

        return \App\Support\CourtDocumentPdf::stream(
            'distract_courts.District_civil.finance.payment_receipt',
            $data,
            'Rasiid-' . str_pad($data['payment']->id, 4, '0', STR_PAD_LEFT) . '.pdf'
        );
    }

    private function familyPaymentReceiptData($id): array
    {
        $payment = DistrictFamilyPayment::with(['court', 'tariff', 'cashier', 'approver'])->findOrFail($id);
        $court   = $payment->court;

        $qrDataUri = (new Builder(
            writer: new PngWriter(),
            data: route('family-registration.payments.receipt', $payment->id),
            size: 220,
            margin: 0,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            foregroundColor: new Color(10, 40, 77),
            backgroundColor: new Color(255, 255, 255),
        ))->build()->getDataUri();

        return compact('payment', 'court', 'qrDataUri');
    }

    public function paymentRequestForm($id)
    {
        $case = \App\Models\DistrictFamilyRegistration::with(['court', 'parties', 'payments'])->findOrFail($id);

        $knownApplicants = \App\Models\Payment::select('payer_name', 'payer_phone', 'payer_email')
            ->whereNotNull('payer_name')
            ->orderByDesc('created_at')
            ->get()
            ->unique('payer_name')
            ->values();

        return view('distract_courts.District_family.registration.district_family_add_payment', compact('case', 'knownApplicants'));
    }

    public function storePaymentRequest(Request $request)
    {
        // Case-specific launch only: the form only collects the applicant's identity.
        // Saved exclusively to district_family_payments (not the shared payments table)
        // so it shows up in "Codsiyada Lacag Bixinta" on the case page.
        $validated = $request->validate([
            'family_case_id' => 'required|exists:district_family_registrations,FCID',
            'payer_name'     => 'required|string|max:255',
            'payer_phone'    => 'required|string|max:20',
            'payer_email'    => 'required|email|max:255',
        ]);

        // Reuse the case's existing open payment request (still Pending/Awaiting
        // Approval) instead of creating a duplicate row every time this form is
        // submitted for the same case.
        $payment = DistrictFamilyPayment::where('family_case_id', $validated['family_case_id'])
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
        // district_family_payments, matching what's shown in their payment history.
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
            DistrictFamilyPayment::create(array_merge([
                'family_case_id' => $validated['family_case_id'],
                'amount'         => 0,
                'currency'       => 'USD',
                'status'         => 'Pending',
            ], $fields));
            $message = 'Codsiga lacag bixinta si guul leh ayaa loo diray.';
        }

        return redirect()->route('family-registration.supporting', $validated['family_case_id'])
            ->with('success', $message);
    }

    public function index(Request $request)
    {
        $query = \App\Models\DistrictFamilyRegistration::with('court')->orderByDesc('FCID');

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

        $allRecords = \App\Models\DistrictFamilyRegistration::with('court')->orderByDesc('FCID');
        if ($userCourtID) $allRecords->where('GradeCourt', $userCourtID);
        $allRecords = $allRecords->get();

        $perPage = $this->resolvePerPage($request);

        $records         = $query->paginate($perPage)->withQueryString();
        $courts          = \App\Models\Court::orderBy('longName')->get();
        $caseTypes       = \App\Models\CaseType::orderBy('case_name')->get();
        $statuses        = \App\Models\StatusProcess::orderBy('name')->get();
        $familySubCases  = \App\Models\CaseCategory::where('case_name', 'Qoyska')->pluck('sub_case');
        return view('distract_courts.District_family.registration.district_family_registration', compact('records', 'allRecords', 'courts', 'caseTypes', 'statuses', 'userCourtID', 'familySubCases'));
    }

    public function create()
    {
        $courts       = \App\Models\Court::orderBy('longName')->get();
        $userCourtID  = (auth()->check() && auth()->user()->position !== 'admin')
                        ? (auth()->user()->employee->courtID ?? null)
                        : null;

        return view('distract_courts.District_family.registration.district_family_registration_form', compact('courts', 'userCourtID'));
    }

    public function edit($id)
    {
        $record      = \App\Models\DistrictFamilyRegistration::findOrFail($id);
        $courts      = \App\Models\Court::orderBy('longName')->get();
        $userCourtID = (auth()->check() && auth()->user()->position !== 'admin')
                        ? (auth()->user()->employee->courtID ?? null)
                        : null;

        return view('distract_courts.District_family.registration.district_family_registration_form', compact('record', 'courts', 'userCourtID'));
    }

    public function show($id)
    {
        $case        = \App\Models\DistrictFamilyRegistration::with(['court', 'parties', 'documents', 'lawyers.lawyer', 'lawyers.party', 'legalRepresentatives.party', 'assignments.employee', 'hearings', 'payments.tariff'])->findOrFail($id);
        $handover    = \App\Models\DistrictFamilyHandover::where('family_case_id', $id)->latest()->first();
        $returnFile  = \App\Models\DistrictFamilyReturnFile::where('family_case_id', $id)->latest()->first();
        $scriptures  = \App\Models\DistrictFamilyHearingScripture::with('hearing')->where('family_case_id', $id)->orderBy('created_at')->get();
        $judgments   = \App\Models\DistrictFamilyJudgment::with('receipts')->where('family_case_id', $id)->orderByDesc('created_at')->get();
        $enforcement = \App\Models\DistrictFamilyEnforcement::where('family_case_id', $id)->latest()->first();
        $appeal      = \App\Models\DistrictFamilyAppeal::where('family_case_id', $id)->latest()->first();
        return view('distract_courts.District_family.registration.district_family_information', compact('case', 'handover', 'returnFile', 'scriptures', 'judgments', 'enforcement', 'appeal'));
    }

    public function supporting($id)
    {
        $case       = \App\Models\DistrictFamilyRegistration::with(['court', 'parties', 'documents', 'lawyers.lawyer', 'lawyers.party', 'assignments.employee', 'hearings', 'payments.tariff'])->findOrFail($id);
        $handover   = \App\Models\DistrictFamilyHandover::where('family_case_id', $id)->latest()->first();
        $scriptures = \App\Models\DistrictFamilyHearingScripture::with('hearing')->where('family_case_id', $id)->orderBy('created_at')->get();
        return view('distract_courts.District_family.registration.district_family_add_supporting', compact('case', 'handover', 'scriptures'));
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

        \App\Models\DistrictFamilyRegistration::create(array_merge(
            $request->only('RegisterNo','FileNo','GradeCourt','CaseType','SubCase','OpenDate','NumberLetter','LegalBasis','Orders_Requested','Remarks','Status'),
            [
                'institution_id' => auth()->user()->institution_id,
                'addedBy'   => auth()->user()->name ?? 'Admin',
                'addedDate' => now()->format('Y-m-d'),
            ]
        ));

        return redirect()->route('family-registration.index')
            ->with('success', 'Family case registered successfully.');
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

        $record = \App\Models\DistrictFamilyRegistration::findOrFail($id);
        $record->update(array_merge(
            $request->only('RegisterNo','FileNo','GradeCourt','CaseType','SubCase','OpenDate','NumberLetter','LegalBasis','Orders_Requested','Remarks','Status'),
            [
                'updatedBy'   => auth()->user()->name ?? 'Admin',
                'updatedDate' => now()->format('Y-m-d'),
            ]
        ));

        return redirect()->route('family-registration.index')
            ->with('success', 'Family case updated successfully.');
    }

    public function destroy($id)
    {
        $record = \App\Models\DistrictFamilyRegistration::findOrFail($id);
        $record->delete();
        return response()->json(['success' => true, 'message' => 'Family case deleted successfully.']);
    }

    public function tracking(Request $request)
    {
        $baseQuery = \App\Models\DistrictFamilyRegistration::query();

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
        ])->orderByDesc('FCID');

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

        $records        = $query->paginate($perPage)->withQueryString();
        $courts         = \App\Models\Court::orderBy('longName')->get();
        $familySubCases = \App\Models\CaseCategory::where('case_name', 'Qoyska')->pluck('sub_case');

        return view('distract_courts.District_family.registration.district_family_tracking', compact('records', 'courts', 'stats', 'allStatuses', 'familySubCases'));
    }

    public function handover(Request $request)
    {
        $baseQuery = \App\Models\DistrictFamilyRegistration::query();

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

        $query = (clone $baseQuery)->with(['court', 'handover'])->orderByDesc('FCID');

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

        $records        = $query->paginate($perPage)->withQueryString();
        $statuses       = \App\Models\StatusProcess::orderBy('name')->get();
        $familySubCases = \App\Models\CaseCategory::where('case_name', 'Qoyska')->pluck('sub_case');

        return view('distract_courts.District_family.registration.district_family_handover', compact('records', 'statuses', 'stats', 'familySubCases'));
    }

    public function nextFileNo($courtcode)
    {
        $court = \App\Models\Court::where('courtcode', $courtcode)->firstOrFail();
        $short = $court->shortName;
        $currYear = date('Y');

        $last = \App\Models\DistrictFamilyRegistration::where('GradeCourt', $courtcode)
            ->where('FileNo', 'like', "{$short}/DQL/%/{$currYear}")
            ->orderByDesc('FCID')
            ->value('FileNo');

        $serial = 1;
        if ($last) {
            // Format: SHORT/DQL/SERIAL/YEAR
            $parts = explode('/', $last);
            if (count($parts) >= 3) {
                $serial = intval($parts[2]) + 1;
            }
        }

        $fileNo = sprintf('%s/DQL/%02d/%s', $short, $serial, $currYear);

        return response()->json(['fileNo' => $fileNo]);
    }
}
