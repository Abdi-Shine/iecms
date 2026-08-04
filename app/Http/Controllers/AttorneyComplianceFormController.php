<?php

namespace App\Http\Controllers;

use App\Models\AttorneyCase;
use App\Models\AttorneyComplianceForm;
use App\Models\Court;
use App\Models\Employee;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AttorneyComplianceFormController extends Controller
{
    public const FORM_TYPES = [
        'non_disclosure' => [
            'code'  => 'XIGQ05',
            'label' => 'Non-Disclosure',
        ],
        'conflict_of_interest' => [
            'code'  => 'XIGQ03',
            'label' => 'Conflict of Interest',
        ],
    ];

    public function create(Request $request, $caseId, $type)
    {
        $this->validateType($type);
        $case     = AttorneyCase::findOrFail($caseId);
        $employee = $this->resolveEmployee($request);

        return view('attorney.Conclusion.direct_complaint_compliance_form', [
            'case'     => $case,
            'type'     => $type,
            'formMeta' => self::FORM_TYPES[$type],
            'employee' => $employee,
        ]);
    }

    public function store(Request $request, $caseId, $type)
    {
        $this->validateType($type);
        $case = AttorneyCase::findOrFail($caseId);

        $data = $request->validate([
            'confirm_details' => 'required',
            'signed_date'     => 'required|date',
        ], [
            'confirm_details.required' => 'Waa inaad xaqiijisaa faahfaahinta ka hor intaadan foomka gudbin.',
        ]);

        $employee = $this->resolveEmployee($request);
        if (!$employee) {
            return back()->withInput()->withErrors(['signature' => 'Akoonkaaga lama xidhiidhin diiwaanka shaqaalaha.']);
        }
        if (!$employee->signature) {
            return back()->withInput()->withErrors(['signature' => 'Fadlan marka hore saxiixa ka geli bogga xogta shaqaalaha, kaddibna ku soo laabo foomkan.']);
        }

        // Copy the employee's signature-on-file at the moment of signing, so
        // this record stays accurate even if they later replace their signature.
        $sourcePath = public_path($employee->signature);
        $sigDir     = public_path('uploads/attorney/compliance-signatures');
        if (!is_dir($sigDir)) {
            mkdir($sigDir, 0755, true);
        }
        $sigFilename = 'sig_' . $type . '_' . $case->ACID . '_' . time() . '.' . pathinfo($sourcePath, PATHINFO_EXTENSION);
        copy($sourcePath, $sigDir . '/' . $sigFilename);

        AttorneyComplianceForm::create([
            'attorney_case_id' => $case->ACID,
            'employee_id'      => $employee->AID,
            'form_type'        => $type,
            'form_code'        => self::FORM_TYPES[$type]['code'],
            'notes'            => self::FORM_TYPES[$type]['label'] . ' waa la xaqiijiyay.',
            'signature'        => 'uploads/attorney/compliance-signatures/' . $sigFilename,
            'signed_date'      => $data['signed_date'],
        ]);

        return redirect()->route('attorney-cases.workflow', $case->ACID)
            ->with('success', self::FORM_TYPES[$type]['label'] . ' waa la keydiyay.');
    }

    private function resolveEmployee(Request $request): ?Employee
    {
        return $request->user()->employee
            ?? Employee::where('EmpName', $request->user()->name)->first()
            ?? Employee::where('email', $request->user()->email)->first();
    }

    public function letter($recordId)
    {
        $data = $this->letterData($recordId);

        return view('attorney.Conclusion.compliance_letter', $data);
    }

    public function letterPdf($recordId)
    {
        $data = $this->letterData($recordId);

        return \App\Support\CourtDocumentPdf::stream(
            'attorney.Conclusion.compliance_letter',
            $data,
            $data['formMeta']['code'] . '-' . $data['case']->case_number . '.pdf'
        );
    }

    private function letterData($recordId): array
    {
        $record = AttorneyComplianceForm::with(['employee', 'attorneyCase.accused'])->findOrFail($recordId);

        $court = Court::where('longName', 'ATTORNEY GENERAL OFFICE')->first();

        // Generated server-side (not client-side JS) so it also renders inside
        // the Dompdf-produced PDF, which cannot execute JavaScript.
        $qrDataUri = (new Builder(
            writer: new PngWriter(),
            data: $record->attorneyCase->case_number . ' / ' . self::FORM_TYPES[$record->form_type]['code'] . ' / #' . $record->id,
            size: 150,
            margin: 0,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            foregroundColor: new Color(10, 40, 77),
            backgroundColor: new Color(255, 255, 255),
        ))->build()->getDataUri();

        return [
            'record'     => $record,
            'case'       => $record->attorneyCase,
            'type'       => $record->form_type,
            'formMeta'   => self::FORM_TYPES[$record->form_type],
            'court'      => $court,
            'qrDataUri'  => $qrDataUri,
        ];
    }

    private function validateType(string $type): void
    {
        if (!array_key_exists($type, self::FORM_TYPES)) {
            throw ValidationException::withMessages(['type' => 'Nooca foomka lama aqoonsanin.']);
        }
    }
}
