<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use DB;

class FinanceController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_payments' => Payment::count(),
            'approved_payments' => Payment::where('status', 'Approved')->count(),
            'total_revenue' => Payment::where('status', 'Approved')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'Approved')
                ->whereMonth('payment_date', now()->month)
                ->sum('amount'),
            'today_revenue' => Payment::where('status', 'Approved')
                ->whereDate('payment_date', now()->today())
                ->sum('amount'),
            'yearly_revenue' => Payment::where('status', 'Approved')
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
        ];

        $status_breakdown = Payment::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $recent_payments = Payment::orderBy('created_at', 'desc')->take(10)->get();

        return view('Courts.District_civil.finance.finance_Dashboard', compact('stats', 'status_breakdown', 'recent_payments'));
    }

    public function paymentsIndex(Request $request)
    {
        $query = Payment::with('tariff');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payer_name', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stats = [
            'total'     => Payment::count(),
            'approved'  => Payment::where('status', 'Approved')->count(),
            'pending'   => Payment::whereIn('status', ['Pending', 'Awaiting Approval'])->count(),
            'collected' => Payment::where('status', 'Approved')->sum('amount'),
        ];

        $perPage = $this->resolvePerPage($request);

        $payments = $query->orderByDesc('payment_date')->paginate($perPage)->withQueryString();
        $courts   = \App\Models\Court::orderBy('longName')->get();
        $tariffs  = \App\Models\Tariff::where('status', 'Active')->orderBy('name_so')->get();

        return view('Courts.District_civil.finance.Payment', compact('payments', 'stats', 'courts', 'tariffs'));
    }

    public function applicantRequestsIndex(Request $request)
    {
        $query = Payment::with(['court', 'tariff', 'case', 'cashier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payer_name', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stats = [
            'pending'           => Payment::where('status', 'Pending')->count(),
            'awaiting_approval' => Payment::count(),
            'today'             => Payment::whereDate('created_at', now()->today())->count(),
            'amount'            => Payment::sum('amount'),
        ];

        $perPage = $this->resolvePerPage($request);

        $requests = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();
        $courts   = \App\Models\Court::orderBy('longName')->get();
        $tariffs  = \App\Models\Tariff::where('status', 'Active')->orderBy('name_so')->get();

        return view('Courts.District_civil.finance.applicant_requests', compact('requests', 'stats', 'courts', 'tariffs'));
    }

    public function updatePaymentRequest(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'payer_name'   => 'required|string|max:255',
            'payer_phone'  => 'required|string|max:20',
            'payer_email'  => 'required|email|max:255',
            'court_id'     => 'required|exists:courts,CAI',
            'tariff_id'    => 'required|exists:tariffs,id',
            'amount'       => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'status'       => 'required|in:Pending,Awaiting Approval,Approved,Failed',
        ]);

        if ($validated['status'] === 'Approved' && $payment->status !== 'Approved') {
            $approver = \App\Models\Employee::where('EmpName', auth()->user()->name)->first()
                ?? \App\Models\Employee::where('email', auth()->user()->email)->first();

            $validated['approved_by'] = $approver?->AID;
            $validated['approved_at'] = now();
        }

        $payment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Codsiga lacag bixinta ee "' . $payment->payer_name . '" si guul leh ayaa wax looga beddelay.',
        ]);
    }

    public function approvePaymentRequest($id)
    {
        $payment = Payment::findOrFail($id);

        if (!in_array($payment->status, ['Pending', 'Awaiting Approval'])) {
            return response()->json([
                'success' => false,
                'message' => 'Codsigan horeba loo qaaday tallaabo — ma sugayo ansaxin.',
            ], 422);
        }

        $approver = \App\Models\Employee::where('EmpName', auth()->user()->name)->first()
            ?? \App\Models\Employee::where('email', auth()->user()->email)->first();

        $payment->update([
            'status'      => 'Approved',
            'approved_by' => $approver?->AID,
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Codsiga lacag bixinta ee "' . $payment->payer_name . '" waa la ansixiyay. Codsadaha wuu sii wadi karaa lacag bixinta.',
        ]);
    }

    public function applicantPaymentHistory(Request $request)
    {
        $name  = trim((string) $request->query('name'));
        $phone = trim((string) $request->query('phone'));
        $email = trim((string) $request->query('email'));

        if ($phone === '' && $email === '' && mb_strlen($name) < 3) {
            return response()->json([]);
        }

        $query = Payment::with(['court', 'tariff']);

        if ($phone !== '' || $email !== '') {
            $query->where(function ($q) use ($phone, $email) {
                if ($phone !== '') {
                    $q->orWhere('payer_phone', $phone);
                }
                if ($email !== '') {
                    $q->orWhere('payer_email', $email);
                }
            });
        } else {
            $query->where('payer_name', 'like', "%{$name}%");
        }

        $payments = $query->orderByDesc('created_at')->limit(10)->get()->map(function ($p) {
            return [
                'id'          => $p->id,
                'payer_name'  => $p->payer_name,
                'payer_phone' => $p->payer_phone,
                'court_name'  => $p->court->longName ?? '—',
                'service'     => $p->tariff->name_so ?? 'Tarifad Guud',
                'amount'      => (float) $p->amount,
                'currency'    => $p->currency,
                'status'      => $p->status,
                'payment_date'=> $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') : '—',
            ];
        });

        return response()->json($payments);
    }

    public function paymentReceipt($id)
    {
        return view('Courts.District_civil.finance.payment_receipt', $this->paymentReceiptData($id));
    }

    public function paymentReceiptPdf($id)
    {
        $data = $this->paymentReceiptData($id);

        return \App\Support\CourtDocumentPdf::stream(
            'Courts.District_civil.finance.payment_receipt',
            $data,
            'Rasiid-' . str_pad($data['payment']->id, 4, '0', STR_PAD_LEFT) . '.pdf'
        );
    }

    private function paymentReceiptData($id): array
    {
        $payment = Payment::with(['court', 'tariff', 'case', 'cashier', 'approver'])->findOrFail($id);
        $court   = $payment->court;

        $qrDataUri = (new Builder(
            writer: new PngWriter(),
            data: route('finance.payments.receipt', $payment->id),
            size: 220,
            margin: 0,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            foregroundColor: new Color(10, 40, 77),
            backgroundColor: new Color(255, 255, 255),
        ))->build()->getDataUri();

        return compact('payment', 'court', 'qrDataUri');
    }

    public function tariffsIndex(Request $request)
    {
        $query = \App\Models\Tariff::with('court');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_so', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stats = [
            'total'    => \App\Models\Tariff::count(),
            'active'   => \App\Models\Tariff::where('status', 'Active')->count(),
            'fixed'    => \App\Models\Tariff::where('type', 'Fixed')->count(),
            'variable' => \App\Models\Tariff::where('type', 'Variable')->count(),
        ];

        $perPage = $this->resolvePerPage($request);

        $tariffs = $query->orderBy('name_so')->paginate($perPage)->withQueryString();
        $courts  = \App\Models\Court::orderBy('longName')->get();

        return view('Courts.District_civil.finance.Tariffs', compact('tariffs', 'stats', 'courts'));
    }

    public function storeTariff(Request $request)
    {
        $validated = $this->validateTariff($request);
        $validated['type'] = 'Fixed';

        $tariff = \App\Models\Tariff::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarifadka "' . $tariff->name_so . '" si guul leh ayaa loo abuuray.',
        ]);
    }

    public function updateTariff(Request $request, $id)
    {
        $tariff = \App\Models\Tariff::findOrFail($id);
        $validated = $this->validateTariff($request, $id);

        $tariff->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarifadka "' . $tariff->name_so . '" si guul leh ayaa wax looga beddelay.',
        ]);
    }

    private function validateTariff(Request $request, $ignoreId = null): array
    {
        $validated = $request->validate([
            'court_id'    => 'required|exists:courts,CAI',
            'tariff_code' => ['required', 'string', 'max:100', \Illuminate\Validation\Rule::unique('tariffs', 'tariff_code')->ignore($ignoreId)],
            'name_so'     => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'status'      => 'required|in:Active,Inactive',
            'description' => 'nullable|string',
        ]);

        $validated['currency'] = 'USD';

        return $validated;
    }
}

