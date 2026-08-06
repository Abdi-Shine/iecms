<?php

namespace App\Http\Controllers;

use App\Models\AppealCivilDocument;
use App\Models\AppealCivilHandover;
use App\Models\AppealCivilReturnFile;
use App\Models\AppealCivilRegistration;
use App\Models\DocumentSignature;
use App\Models\StatusProcess;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppealCivilHandoverController extends Controller
{
    public function create($id)
    {
        $case  = AppealCivilRegistration::with(['court', 'assignments.employee'])->findOrFail($id);
        $judge = $case->assignments->whereIn('panel_role', ['Chair', 'Judge', 'Xaakimka', 'Guddoomiye'])->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $existing = AppealCivilHandover::where('civil_case_id', $id)->latest()->first();

        $caseDocuments = AppealCivilDocument::where('civil_case_id', $id)
            ->get()
            ->map(function ($doc) {
                $pages = 0;
                if ($doc->file_path) {
                    $path = storage_path('app/public/' . $doc->file_path);
                    if (file_exists($path)) {
                        $content = @file_get_contents($path);
                        if ($content !== false) {
                            preg_match_all('/\/Type\s*\/Page[\s\/]/', $content, $m);
                            $pages = \count($m[0]);
                            if ($pages === 0 && preg_match('/\/Count\s+(\d+)/', $content, $cm)) {
                                $pages = (int) $cm[1];
                            }
                        }
                    }
                }
                return ['name' => $doc->document_name, 'pages' => $pages > 0 ? (string) $pages : ''];
            })
            ->values();

        $existingReturnFile  = AppealCivilReturnFile::where('civil_case_id', $id)->latest()->first();
        $returnFileDocuments = $existingReturnFile?->documents ?? $caseDocuments->toArray();

        return view('appeal_court.Appeal_civil.registration.appeal_civil_add_handover',
            compact('case', 'judge', 'clerk', 'existing', 'caseDocuments', 'existingReturnFile', 'returnFileDocuments'));
    }

    public function document($id)
    {
        $data = $this->handoverDocumentData($id);

        return view('appeal_court.Appeal_civil.registration.appeal_civil_handover_document', $data);
    }

    public function documentPdf($id)
    {
        $data = $this->handoverDocumentData($id);

        return \App\Support\CourtDocumentPdf::stream(
            'appeal_court.Appeal_civil.registration.appeal_civil_handover_document',
            $data,
            'Handover-' . $data['case']->FileNo . '.pdf'
        );
    }

    private function handoverDocumentData($id): array
    {
        $case     = AppealCivilRegistration::with(['court', 'assignments.employee'])->findOrFail($id);
        $handover = AppealCivilHandover::where('civil_case_id', $id)->latest()->first();

        $judge = $case->assignments->whereIn('panel_role', ['Chair', 'Judge', 'Xaakimka', 'Guddoomiye'])->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();

        $signatures = $handover
            ? DocumentSignature::with('signer')
                ->where('document_type', 'handover')
                ->where('document_id', $handover->id)
                ->get()
                ->keyBy('role')
            : collect();

        $regSig   = $signatures['registrar'] ?? $signatures['signer'] ?? null;
        $clerkSig = $signatures['clerk'] ?? null;

        $myEmployee = Auth::user()->employee
            ?? \App\Models\Employee::where('EmpName', Auth::user()->name)->first();

        $canSign         = $myEmployee && $handover;
        $myAlreadySigned = $canSign && $signatures->contains('signer_id', $myEmployee->AID);

        $myRole = null;
        if ($myEmployee && $handover) {
            if ($myEmployee->EmpName === $handover->created_by) {
                $myRole = 'registrar';
            } elseif ($clerk && $clerk->employee && $clerk->employee->AID === $myEmployee->AID) {
                $myRole = 'clerk';
            } else {
                $myRole = 'signer';
            }
        }

        // Generated server-side (not client-side JS) so it also renders inside
        // the Dompdf-produced PDF, which cannot execute JavaScript.
        $qrDataUri = (new Builder(
            writer: new PngWriter(),
            data: $case->FileNo,
            size: 150,
            margin: 0,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            foregroundColor: new Color(10, 40, 77),
            backgroundColor: new Color(255, 255, 255),
        ))->build()->getDataUri();

        return compact('case', 'handover', 'judge', 'clerk',
            'regSig', 'clerkSig', 'qrDataUri',
            'signatures', 'myEmployee', 'myRole', 'canSign', 'myAlreadySigned');
    }

    public function store(Request $request)
    {
        $request->validate([
            'civil_case_id'        => 'required|exists:appeal_civil_registrations,ACID',
            'documents'            => 'nullable|array',
            'documents.*.name'     => 'nullable|string|max:255',
            'documents.*.pages'    => 'nullable|string|max:30',
            'special_instructions' => 'nullable|string',
            'additional_notes'     => 'nullable|string',
            'status'               => 'required|in:Draft,Sug Qaatay,Qaatay',
        ]);

        $documents = collect($request->input('documents', []))
            ->filter(fn($d) => !empty($d['name']))
            ->values()
            ->toArray();

        $caseId       = $request->input('civil_case_id');
        $newStatus    = $request->input('status');
        $wasSubmitted = AppealCivilHandover::where('civil_case_id', $caseId)->value('status') === 'Sug Qaatay';

        $handover = AppealCivilHandover::updateOrCreate(
            ['civil_case_id' => $caseId],
            [
                'documents'            => $documents,
                'special_instructions' => $request->input('special_instructions'),
                'additional_notes'     => $request->input('additional_notes'),
                'status'               => $newStatus,
                'created_by'           => Auth::user()->name ?? 'Admin',
            ]
        );

        // Notify Clerk staff only when this transitions INTO "Sug Qaatay"
        // (Senior Clerk submitting/re-submitting), not on every draft save.
        if ($newStatus === 'Sug Qaatay' && !$wasSubmitted) {
            $this->notifyHandoverApprovers($handover, $caseId);
        }

        return response()->json(['success' => true, 'message' => 'Handover saved successfully.']);
    }

    private function notifyHandoverApprovers(AppealCivilHandover $handover, $caseId): void
    {
        try {
            $case = AppealCivilRegistration::find($caseId);
            if (!$case) return;

            $approvers = \App\Models\User::whereHas('group.roles.permissions', function ($q) {
                $q->where('module', 'Appeal Case Handover Approval')->where('action', 'view');
            })->orWhereNull('group_id')->get();

            foreach ($approvers as $user) {
                $user->notify(new \App\Notifications\AppealHandoverSubmittedNotification(
                    $handover,
                    $case->FileNo,
                    Auth::user()->name ?? 'Admin',
                ));
            }
        } catch (\Throwable $e) {
            \Log::error('AppealHandoverSubmittedNotification failed: ' . $e->getMessage());
        }
    }

    public function approvalIndex(Request $request)
    {
        $baseQuery = AppealCivilHandover::query();

        $pending  = (clone $baseQuery)->where('status', 'Sug Qaatay')->count();
        $approved = (clone $baseQuery)->where('status', 'Qaatay')->count();
        $rejected = (clone $baseQuery)->where('status', 'Rejected')->count();
        $draft    = (clone $baseQuery)->where('status', 'Draft')->count();

        $caseTypes = AppealCivilRegistration::whereNotNull('CaseType')
            ->whereIn('ACID', (clone $baseQuery)->pluck('civil_case_id'))
            ->distinct()
            ->orderBy('CaseType')
            ->pluck('CaseType');

        $query = (clone $baseQuery)->with('case')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('created_by', 'like', "%$s%")
                  ->orWhereHas('case', function ($cq) use ($s) {
                      $cq->where('FileNo', 'like', "%$s%")
                         ->orWhereHas('court', fn($crq) => $crq->where('longName', 'like', "%$s%"));
                  });
            });
        }

        if ($request->filled('casetype') && $request->casetype !== 'all') {
            $ct = $request->casetype;
            $query->whereHas('case', fn($cq) => $cq->whereRaw('LOWER(CaseType) = ?', [strtolower($ct)]));
        }

        $status = $request->has('status') ? $request->get('status') : 'Sug Qaatay';
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $perPage = $this->resolvePerPage($request);

        $handovers = $query->paginate($perPage)->withQueryString();

        return view('appeal_court.Appeal_civil.Conclusion.appeal_civil_handover_approval', compact('handovers', 'pending', 'approved', 'rejected', 'draft', 'caseTypes'));
    }

    /**
     * Clerk acknowledges receipt. Real digital signature, not a bare status
     * flip: requires password confirmation, and is recorded the same way as
     * the "Saxiix Dukuumintiga" button (document_type=handover, role=clerk).
     */
    public function approve(Request $request, $id)
    {
        $request->validate(['password' => 'required|string']);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['success' => false, 'message' => 'Erayga sirta ah waa khalad.'], 422);
        }

        $employee = auth()->user()->employee
            ?? \App\Models\Employee::where('EmpName', auth()->user()->name)->first()
            ?? \App\Models\Employee::where('email', auth()->user()->email)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Magacaagu kuma jiro diiwaanka shaqaalaha.'], 422);
        }

        $stageStatus = StatusProcess::where('name', 'Qaatay')->first();
        $newStatus   = $stageStatus?->name ?? 'Qaatay';

        $handover = AppealCivilHandover::where('civil_case_id', $id)->latest()->firstOrFail();

        \App\Models\DocumentSignature::updateOrCreate(
            [
                'document_type' => 'handover',
                'document_id'   => $handover->id,
                'signer_id'     => $employee->AID,
            ],
            [
                'role'         => 'clerk',
                'signed_at'    => now(),
                'ip_address'   => $request->ip(),
                'content_hash' => hash('sha256', 'handover|' . $handover->id . '|' . $handover->civil_case_id . '|' . $newStatus),
            ]
        );

        $handover->update(['status' => $newStatus]);
        AppealCivilRegistration::where('ACID', $id)->update(['Status' => $newStatus]);

        return response()->json(['success' => true, 'message' => 'Handover approved and digitally signed.']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['password' => 'required|string']);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['success' => false, 'message' => 'Erayga sirta ah waa khalad.'], 422);
        }

        $handover = AppealCivilHandover::where('civil_case_id', $id)->latest()->firstOrFail();
        $reason   = $request->input('reason');
        $handover->update([
            'status'           => 'Rejected',
            'additional_notes' => $reason ? '[Rejected] ' . $reason : $handover->additional_notes,
        ]);
        return response()->json(['success' => true, 'message' => 'Handover rejected.']);
    }
}
