<?php

namespace App\Http\Controllers;

use App\Models\DistrictCriminalReturnFile;
use App\Models\DistrictCriminalRegistration;
use App\Models\DocumentSignature;
use App\Models\Employee;
use App\Models\StatusProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistrictCriminalReturnFileController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = DistrictCriminalRegistration::query();

        $stats = [
            'total'     => (clone $baseQuery)->count(),
            'returned'  => (clone $baseQuery)->whereHas('returnFile', fn($q) => $q->where('status', 'Qaatay'))->count(),
            'pending'   => (clone $baseQuery)->whereHas('returnFile', fn($q) => $q->where('status', 'Sug Qaatay'))->count(),
            'thisMonth' => (clone $baseQuery)->whereRaw("DATE_FORMAT(OpenDate, '%Y-%m') = ?", [date('Y-m')])->count(),
        ];

        $query = (clone $baseQuery)->with(['court', 'returnFile', 'judgments' => fn($q) => $q->with('receipts')->orderByDesc('created_at')->limit(1)])->orderByDesc('CMID');

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
        $statuses       = StatusProcess::orderBy('name')->get();
        $criminalSubCases = \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->pluck('sub_case');

        return view('Courts.District_criminal.Conclusion.district_criminal_view_return_file',
            compact('records', 'statuses', 'stats', 'criminalSubCases'));
    }

    public function create($id)
    {
        $case = DistrictCriminalRegistration::with(['court', 'assignments.employee', 'documents'])->findOrFail($id);
        $judge = $case->assignments->whereIn('panel_role', ['Chair', 'Judge', 'Xaakimka', 'Guddoomiye'])->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();
        $existing = DistrictCriminalReturnFile::where('criminal_case_id', $id)->latest()->first();

        $caseDocuments = $case->documents->map(function ($doc) {
            $pages = 0;
            if ($doc->file_path) {
                $path = storage_path('app/public/' . $doc->file_path);
                if (file_exists($path)) {
                    $content = @file_get_contents($path);
                    if ($content !== false) {
                        preg_match_all('/\/Type\s*\/Page[\s\/]/', $content, $m);
                        $pages = count($m[0]);
                        if ($pages === 0 && preg_match('/\/Count\s+(\d+)/', $content, $cm)) {
                            $pages = (int) $cm[1];
                        }
                    }
                }
            }
            return ['name' => $doc->document_name, 'pages' => $pages > 0 ? (string) $pages : ''];
        })->values()->toArray();

        return view('Courts.District_criminal.Conclusion.district_criminal_add_return_file',
            compact('case', 'judge', 'clerk', 'existing', 'caseDocuments'));
    }

    public function document($id)
    {
        $data = $this->returnFileDocumentData($id);

        return view('Courts.District_criminal.Conclusion.district_criminal_document_return_file', $data);
    }

    /**
     * Plain, read-only version — no sign button, no sign modal. Used by
     * list/table links that just need to show the document.
     */
    public function documentReadOnly($id)
    {
        $data = $this->returnFileDocumentData($id);

        return view('Courts.District_criminal.Conclusion.district_criminal_document_return_file_readonly', $data);
    }

    public function documentPdf($id)
    {
        $data = $this->returnFileDocumentData($id);

        return \App\Support\CourtDocumentPdf::stream(
            'Courts.District_criminal.Conclusion.district_criminal_document_return_file',
            $data,
            'ReturnFile-' . $data['case']->FileNo . '.pdf'
        );
    }

    private function returnFileDocumentData($id): array
    {
        $case       = DistrictCriminalRegistration::with(['court', 'assignments.employee'])->findOrFail($id);
        $returnFile = DistrictCriminalReturnFile::where('criminal_case_id', $id)->latest()->first();
        $clerk      = $case->assignments->first(fn($a) => in_array($a->panel_role, ['Kaaliye', 'Clerk']));

        $signatures = $returnFile
            ? DocumentSignature::with('signer')
                ->where('document_type', 'criminal_return_file')
                ->where('document_id', $returnFile->id)
                ->get()
                ->keyBy('role')
            : collect();

        $clerkSig = $signatures['clerk'] ?? $signatures['kaaliye'] ?? null;

        $myEmployee = Auth::user()->employee
            ?? Employee::where('EmpName', Auth::user()->name)->first()
            ?? Employee::where('email', Auth::user()->email)->first();

        $canSign         = $myEmployee && $myEmployee->signature && $returnFile;
        $myAlreadySigned = $canSign && $signatures->contains('signer_id', $myEmployee->AID);

        $myRole = null;
        if ($myEmployee && $returnFile && !$myAlreadySigned) {
            $assignment = $case->assignments
                ->whereIn('panel_role', ['Clerk', 'Kaaliye'])
                ->first(fn($a) => (int) $a->employee_id === (int) $myEmployee->AID);
            $myRole = $assignment ? 'clerk' : 'signer';
        }

        return compact('case', 'returnFile', 'clerk', 'clerkSig', 'myEmployee', 'myRole', 'canSign', 'myAlreadySigned');
    }

    public function store(Request $request)
    {
        $request->validate([
            'criminal_case_id'       => 'required|exists:district_criminal_registrations,CMID',
            'documents'            => 'nullable|array',
            'documents.*.name'     => 'nullable|string|max:255',
            'documents.*.pages'    => 'nullable|integer|min:0',
            'special_instructions' => 'nullable|string',
            'additional_notes'     => 'nullable|string',
            'status'               => 'nullable|in:Draft,Qaatay',
        ]);

        $documents = collect($request->input('documents', []))
            ->filter(fn($d) => !empty($d['name']))
            ->values()
            ->toArray();

        $caseId = $request->input('criminal_case_id');

        $status = $request->input('status', 'Draft');

        DistrictCriminalReturnFile::updateOrCreate(
            ['criminal_case_id' => $caseId],
            [
                'documents'            => $documents,
                'special_instructions' => $request->input('special_instructions'),
                'additional_notes'     => $request->input('additional_notes'),
                'status'               => $status,
                'created_by'           => Auth::user()->name ?? 'Admin',
            ]
        );

        if ($status === 'Qaatay') {
            $newStatus = StatusProcess::where('name', 'Gal Celin')->value('name') ?? 'Gal Celin';
            DistrictCriminalRegistration::where('CMID', $caseId)->update(['Status' => $newStatus]);
        }

        $message = $status === 'Qaatay'
            ? 'Soo celinta faylka si guul leh ayaa loo gudbiyay.'
            : 'Qoraalka si guul leh ayaa loo keydsaday.';

        return response()->json(['success' => true, 'message' => $message]);
    }
}
