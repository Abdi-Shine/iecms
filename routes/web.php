<?php

use App\Http\Controllers\BackupRestoreController;
use App\Http\Controllers\LawyerController;
use App\Http\Controllers\ReturnCivilFileController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\CaseCategoryController;
use App\Http\Controllers\CaseTypeController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\DistricCivilRegistrationController;
use App\Http\Controllers\CivilCaseHandoverController;
use App\Http\Controllers\CivilCaseDocumentController;
use App\Http\Controllers\CivilCaseAssignmentController;
use App\Http\Controllers\DocumentAttachmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LockScreenController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicInstitutionController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\StateRegionController;
use App\Http\Controllers\StatusProcessController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/brand-guidelines', function () {
    return view('brand-guidelines');
});

// ── Dashboard ────────────────────────────────────────────────────────────────
Route::get('/dashboard', function () {
    $employees = \App\Models\Employee::all();
    $hearings = \App\Models\Hearing::with('civilCase.parties', 'civilCase.assignments.employee')->get();

    $calendarEvents = $hearings->map(function ($h) {
        $color = match($h->status) {
            'Completed' => '#10B981',
            'Cancelled' => '#DC2626',
            'Postponed' => '#F0B43C',
            default     => '#528CBE',
        };

        $case = $h->civilCase;
        $plaintiff = $case->parties->where('party_type', 'Plaintiff')->first()->party_name ?? '—';

        $judges = $case->assignments->whereIn('panel_role', ['Chair', 'Member'])->pluck('employee.EmpName')->implode(' and ') ?: '—';
        $clerk  = $case->assignments->where('panel_role', 'Clerk')->first()->employee->EmpName ?? '—';

        return [
            'id'    => $h->id,
            'title' => ($case->FileNo ?? '—'),
            'start' => $h->hearing_date->format('Y-m-d'),
            'color' => $color,
            'extendedProps' => [
                'fileNo'    => $case->FileNo ?? '—',
                'caseType'  => $case->CaseType ?? '—',
                'caseStatus'=> $case->Status ?? 'Pending',
                'plaintiff' => $plaintiff,
                'judges'    => $judges,
                'clerk'     => $clerk,
                'date'      => $h->hearing_date->format('Y-m-d'),
                'time'      => $h->hearing_time,
                'courtroom' => $h->courtroom ?? '—',
                'status'    => $h->status ?? '—',
            ],
        ];
    })->values();

    $currentYear = now()->year;

    $monthlyFor = fn($model, $dateCol = 'OpenDate') => array_values(array_map(
        fn($m) => (int) ($model::selectRaw("MONTH($dateCol) as month, COUNT(*) as total")
            ->whereYear($dateCol, $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')[$m] ?? 0),
        range(1, 12)
    ));

    $civilMonthly      = $monthlyFor(\App\Models\DistricCivilRegistration::class);
    $familyMonthly     = $monthlyFor(\App\Models\DistrictFamilyRegistration::class);
    $enforcementMonthly = $monthlyFor(\App\Models\DistrictExecutionRegistration::class);
    $criminalMonthly   = $monthlyFor(\App\Models\DistrictCriminalRegistration::class);

    $closedStatuses = ['La Xidhay', 'Closed', 'Oodista', 'Xukun', 'Qaraar', "Go'aan", 'Dhaqan Gal', 'Rafcaan', 'Gal Celin'];

    $civilReg    = \App\Models\DistricCivilRegistration::count();
    $civilClosed = \App\Models\DistricCivilRegistration::whereIn('Status', $closedStatuses)->count();

    $criminalReg    = \App\Models\DistrictCriminalRegistration::count();
    $criminalClosed = \App\Models\DistrictCriminalRegistration::whereIn('Status', $closedStatuses)->count();

    $caseStats = [
        ['name' => 'Dacwadaha Madaniga',   'reg' => $civilReg,    'closed' => $civilClosed, 'color' => '#528CBE', 'icon' => 'bi-briefcase-fill', 'monthly' => $civilMonthly],
        ['name' => 'Dacwadaha Ciqaabta',  'reg' => $criminalReg, 'closed' => $criminalClosed, 'color' => '#DC2626', 'icon' => 'bi-shield-fill-exclamation', 'monthly' => $criminalMonthly],
        ['name' => 'Dacwadaha Fulinta',    'reg' => 52,           'closed' => 35,           'color' => '#16A34A', 'icon' => 'bi-hammer', 'monthly' => $enforcementMonthly],
        ['name' => 'Dacwadaha Qoyska',     'reg' => 42,           'closed' => 31,           'color' => '#F0B43C', 'icon' => 'bi-people-fill', 'monthly' => $familyMonthly],
    ];

    return view('admin.index', [
        'totalEmployees'    => $employees->count(),
        'totalJudges'       => $employees->filter(fn($e) => stripos($e->getAttribute('Position'), 'Judge') !== false)->count(),
        'activeCases'       => \App\Models\DistricCivilRegistration::whereIn('Status', ['Active', 'Gal Ku Qoris'])->count(),
        'scheduledHearings' => $hearings->where('status', 'Scheduled')->count(),
        'calendarEvents'    => $calendarEvents,
        'caseStats'         => $caseStats,
    ]);
})->middleware(['auth', 'verified', 'permission:Dashboard,view'])->name('dashboard');

// ── Positions ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Positions,view'])->group(function () {
    Route::resource('position', PositionController::class)->only(['index', 'store', 'update', 'destroy']);
});

// ── State & Region ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:State & Region,view'])->group(function () {
    Route::resource('state-region', StateRegionController::class)->only(['index', 'store', 'update', 'destroy']);
});

// ── City ──────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:City,view'])->group(function () {
    Route::resource('city', CityController::class)->only(['index', 'store', 'update', 'destroy']);
});

// ── Public Institutions ──────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Public Institutions,view'])->group(function () {
    Route::resource('public-institution', PublicInstitutionController::class)->only(['index', 'store', 'update', 'destroy']);
});

// ── Case Types ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Case Types,view'])->group(function () {
    Route::resource('case-type', CaseTypeController::class)->only(['index', 'store', 'update', 'destroy']);
});

// ── Case Categories ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Case Categories,view'])->group(function () {
    Route::resource('case-category', CaseCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
});

// ── Status Process ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Status Process,view'])->group(function () {
    Route::get('/status-process/export', [StatusProcessController::class, 'export'])->name('status-process.export');
    Route::post('/status-process/import', [StatusProcessController::class, 'import'])->name('status-process.import');
    Route::resource('status-process', StatusProcessController::class)->only(['index', 'store', 'update', 'destroy']);
});

// ── Document Attachment ───────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Document Attachment,view'])->group(function () {
    Route::get('/document-attachment/export', [DocumentAttachmentController::class, 'export'])->name('document-attachment.export');
    Route::post('/document-attachment/import', [DocumentAttachmentController::class, 'import'])->name('document-attachment.import');
    Route::resource('document-attachment', DocumentAttachmentController::class)->only(['index', 'store', 'update', 'destroy']);
});

// ── Civil Case Registration (+ Parties, Documents, Lawyers sub-resources) ─────
Route::middleware(['auth', 'permission:Civil Case Registration,view'])->group(function () {
    Route::get('civil-registration/next-fileno/{courtcode}', [DistricCivilRegistrationController::class, 'nextFileNo']);
    Route::get('civil-case-tracking', [DistricCivilRegistrationController::class, 'tracking'])->name('civil-case-tracking.index');
    Route::get('civil-registration/{id}/supporting', [DistricCivilRegistrationController::class, 'supporting'])->name('civil-registration.supporting');
    Route::resource('civil-registration', DistricCivilRegistrationController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Case Parties
    Route::resource('civil-case-parties', \App\Http\Controllers\CivilCasePartyController::class)->only(['index', 'store', 'destroy']);
    Route::get('civil-case-parties/case/{caseId}', [\App\Http\Controllers\CivilCasePartyController::class, 'getPartiesByCase']);
    Route::post('civil-case-parties/notify/{caseId}', [\App\Http\Controllers\CivilCasePartyController::class, 'sendNotifications'])->name('civil-case-parties.notify');

    // Legal Representatives (district_civil_legal_representatives)
    Route::get('civil-legal-reps/case/{caseId}', [\App\Http\Controllers\CivilLegalRepresentativeController::class, 'getByCase']);
    Route::post('civil-legal-reps', [\App\Http\Controllers\CivilLegalRepresentativeController::class, 'store']);
    Route::delete('civil-legal-reps/{id}', [\App\Http\Controllers\CivilLegalRepresentativeController::class, 'destroy']);

    // Case Documents
    Route::get('/civil-case-documents', [CivilCaseDocumentController::class, 'index'])->name('civil-case-documents.index');
    Route::post('/civil-case-documents', [CivilCaseDocumentController::class, 'store'])->name('civil-case-documents.store');
    Route::get('/civil-case-documents/case/{id}', [CivilCaseDocumentController::class, 'getDocumentsByCase']);
    Route::delete('/civil-case-documents/{id}', [CivilCaseDocumentController::class, 'destroy']);

    // Case Lawyers
    Route::get('/civil-case-lawyers', [\App\Http\Controllers\CivilCaseLawyerController::class, 'index'])->name('civil-case-lawyers.index');
    Route::post('/civil-case-lawyers', [\App\Http\Controllers\CivilCaseLawyerController::class, 'store'])->name('civil-case-lawyers.store');
    Route::put('/civil-case-lawyers/{id}', [\App\Http\Controllers\CivilCaseLawyerController::class, 'update']);
    Route::get('/civil-case-lawyers/case/{id}', [\App\Http\Controllers\CivilCaseLawyerController::class, 'getAssignmentsByCase']);
    Route::delete('/civil-case-lawyers/{id}', [\App\Http\Controllers\CivilCaseLawyerController::class, 'destroy']);

    // Applicant Payment Request (Foomka Codsiga Lacag Bixinta) — case-specific launch
    Route::get('civil-registration/{id}/payment-request', [DistricCivilRegistrationController::class, 'paymentRequestForm'])->name('civil-registration.payment-request');
});

// Applicant Payment Request — standalone launch (case picked inside the form) and the
// shared store endpoint, reachable by either Civil Case Registration or Finance staff.
Route::middleware(['auth', 'permission:Civil Case Registration|Finance,view'])->group(function () {
    Route::get('finance-applicant-request', [DistricCivilRegistrationController::class, 'paymentRequestForm'])->name('finance.applicant-request');
    Route::post('finance-applicant-request', [DistricCivilRegistrationController::class, 'storePaymentRequest'])->name('civil-registration.payment-request.store');
    Route::get('finance-applicant-request/payment-history', [\App\Http\Controllers\FinanceController::class, 'applicantPaymentHistory'])->name('finance.applicant-request.payment-history');
    Route::get('civil-registration-payments/{id}/receipt', [DistricCivilRegistrationController::class, 'districtPaymentReceipt'])->name('civil-registration.payments.receipt');
    Route::get('civil-registration-payments/{id}/receipt-pdf', [DistricCivilRegistrationController::class, 'districtPaymentReceiptPdf'])->name('civil-registration.payments.receipt-pdf');
});

// Viewing a single case's full info is also reached from the Hearings
// tracking pages (e.g. Kaaliye has "Hearings" access but not "Civil Case
// Registration"), so accept either permission here rather than only the
// latter — otherwise that link 403s for anyone who only has Hearings access.
Route::middleware(['auth', 'permission:Civil Case Registration|Hearings,view'])->group(function () {
    Route::get('civil-registration/{civil_registration}', [DistricCivilRegistrationController::class, 'show'])->name('civil-registration.show');
});

// ── Case Handover ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Case Handover,view'])->group(function () {
    Route::get('civil-registration-handover', [DistricCivilRegistrationController::class, 'handover'])->name('civil-registration.handover');
});

// Handover Approval — its own permission module, separate from Case Handover,
// so it can be granted/restricted independently in the Role & Permission Matrix.
Route::middleware(['auth', 'permission:Case Handover Approval,view'])->group(function () {
    Route::post('civil-case-handover/{id}/approve', [CivilCaseHandoverController::class, 'approve'])->name('civil-case-handover.approve');
    Route::post('civil-case-handover/{id}/reject', [CivilCaseHandoverController::class, 'reject'])->name('civil-case-handover.reject');
    Route::get('civil-case-handover-approval', [CivilCaseHandoverController::class, 'approvalIndex'])->name('civil-case-handover.approval');
});

// Content-modifying handover actions — Kaaliye (Clerk) only has "view" on Case
// Handover (by design, per the Role & Permission Matrix), so this keeps them
// from editing the handover; only Senior Clerk / roles with "create" can.
Route::middleware(['auth', 'permission:Case Handover,create'])->group(function () {
    Route::get('civil-case-handover/{id}', [CivilCaseHandoverController::class, 'create'])->name('civil-case-handover.create');
    Route::post('civil-case-handover', [CivilCaseHandoverController::class, 'store'])->name('civil-case-handover.store');
});

// Viewable by District Case Handover staff OR Appeal Court staff (for lower-court case file access)
Route::middleware(['auth', 'permission:Case Handover|Appeal Case Handover,view'])->group(function () {
    Route::get('civil-case-handover/{id}/document', [CivilCaseHandoverController::class, 'document'])->name('civil-case-handover.document');
    Route::get('civil-case-handover/{id}/document-pdf', [CivilCaseHandoverController::class, 'documentPdf'])->name('civil-case-handover.document-pdf');
});

// ── Archive ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Archive,view'])->group(function () {
    Route::get('/archive',                                   [\App\Http\Controllers\ArchiveController::class, 'index'])          ->name('archive.index');
    Route::get('/archive/judgments',                         [\App\Http\Controllers\ArchiveController::class, 'judgmentsIndex'])   ->name('archive.judgments');
    Route::get('/archive/judgments/{id}/stamp-request',      [\App\Http\Controllers\ArchiveController::class, 'judgmentStampDocument'])->name('archive.judgment.stamp-request');
    Route::post('/archive/hearings/{id}/approve-stamp',      [\App\Http\Controllers\ArchiveController::class, 'approveHearingStamp'])   ->name('archive.hearing.approve-stamp');
    Route::post('/archive/hearings/{id}/approve-doc-stamp',  [\App\Http\Controllers\ArchiveController::class, 'approveHearingDocStamp'])->name('archive.hearing.approve-doc-stamp');
});

// Stamp request document pages — accessible by Kaaliye and Archive Officer
Route::middleware(['auth'])->group(function () {
    Route::get('/hearings/{id}/approval-stamp',  [\App\Http\Controllers\HearingController::class,  'approvalStampDocument']) ->name('hearings.approval.stamp');
    Route::get('/judgments/{id}/stamp-request',  [\App\Http\Controllers\JudgmentController::class, 'stampRequestDocument'])  ->name('judgments.stamp-request');
});

// ── Judicial Units ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Judicial Units,view'])->group(function () {
    Route::get('/court/export', [CourtController::class, 'export'])->name('court.export');
    Route::post('/court/import', [CourtController::class, 'import'])->name('court.import');
    Route::resource('court', CourtController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

// ── Staff Registry ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Staff Registry,view'])->group(function () {
    Route::get('/employee/export', [EmployeeController::class, 'export'])->name('employee.export');
    Route::post('/employee/import', [EmployeeController::class, 'import'])->name('employee.import');
    Route::resource('employee', EmployeeController::class)->names([
        'index'   => 'employee.index',
        'create'  => 'employee.create',
        'store'   => 'employee.store',
        'show'    => 'employee.show',
        'edit'    => 'employee.edit',
        'update'  => 'employee.update',
        'destroy' => 'employee.destroy',
    ]);
});

// ── Access Login ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Access Login,view'])->group(function () {
    Route::get('/access-login', [EmployeeController::class, 'accessLogin'])->name('employee.access-login');
    Route::get('/access-login/{id}/create', [EmployeeController::class, 'accessLoginCreate'])->name('employee.access-login.create');
    Route::post('/access-login/{id}/store', [EmployeeController::class, 'accessLoginStore'])->name('employee.access-login.store');
    Route::get('/access-login/{id}/edit', [EmployeeController::class, 'accessLoginEdit'])->name('employee.access-login.edit');
    Route::put('/access-login/{id}/update', [EmployeeController::class, 'accessLoginUpdate'])->name('employee.access-login.update');
});

// ── Role & Permission ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Role & Permission,view'])->group(function () {
    Route::get('/roles',          [RolePermissionController::class, 'rolesIndex']) ->name('roles.index');
    Route::get('/roles/export',   [RolePermissionController::class, 'exportRoles'])->name('roles.export');
    Route::post('/roles/import',  [RolePermissionController::class, 'importRoles'])->name('roles.import');
    Route::post('/roles',         [RolePermissionController::class, 'storeRole'])  ->name('roles.store');
    Route::put('/roles/{id}',     [RolePermissionController::class, 'updateRole']) ->name('roles.update');
    Route::delete('/roles/{id}',  [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');
    Route::get('/role-permission',  [RolePermissionController::class, 'index']) ->name('role-permission.index');
    Route::post('/role-permission', [RolePermissionController::class, 'update'])->name('role-permission.update');
    Route::get('/groups',           [\App\Http\Controllers\GroupController::class, 'index'])  ->name('groups.index');
    Route::post('/groups',          [\App\Http\Controllers\GroupController::class, 'store'])  ->name('groups.store');
    Route::put('/groups/{id}',      [\App\Http\Controllers\GroupController::class, 'update']) ->name('groups.update');
    Route::delete('/groups/{id}',   [\App\Http\Controllers\GroupController::class, 'destroy'])->name('groups.destroy');
});

// ── Case Assignment ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Case Assignment,view'])->group(function () {
    Route::get('/civil-case-assign', [CivilCaseAssignmentController::class, 'index'])->name('civil-case-assign.index');
    Route::get('/civil-case-assign/add/{id}', [CivilCaseAssignmentController::class, 'addJudges'])->name('civil-case-assign.add');
    Route::post('/civil-case-assign', [CivilCaseAssignmentController::class, 'store'])->name('civil-case-assign.store');
    Route::delete('/civil-case-assign/{id}', [CivilCaseAssignmentController::class, 'destroy'])->name('civil-case-assign.destroy');
    Route::put('/civil-case-assign/{id}', [CivilCaseAssignmentController::class, 'update'])->name('civil-case-assign.update');
});

// ── Finance ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Finance,view'])->group(function () {
    Route::get('/finance-dashboard', [\App\Http\Controllers\FinanceController::class, 'dashboard'])->name('finance.dashboard');
    Route::get('/finance-payments',  [\App\Http\Controllers\FinanceController::class, 'paymentsIndex'])->name('finance.payments');
    Route::get('/finance-tariffs',   [\App\Http\Controllers\FinanceController::class, 'tariffsIndex'])->name('finance.tariffs');
    Route::get('/finance-applicant-requests', [\App\Http\Controllers\FinanceController::class, 'applicantRequestsIndex'])->name('finance.applicant-requests');
    Route::get('/finance-payments/{id}/receipt', [\App\Http\Controllers\FinanceController::class, 'paymentReceipt'])->name('finance.payments.receipt');
    Route::get('/finance-payments/{id}/receipt-pdf', [\App\Http\Controllers\FinanceController::class, 'paymentReceiptPdf'])->name('finance.payments.receipt-pdf');
});

Route::middleware(['auth', 'permission:Finance,create'])->group(function () {
    Route::post('/finance-tariffs', [\App\Http\Controllers\FinanceController::class, 'storeTariff'])->name('finance.tariffs.store');
});

Route::middleware(['auth', 'permission:Finance,edit'])->group(function () {
    Route::put('/finance-tariffs/{id}', [\App\Http\Controllers\FinanceController::class, 'updateTariff'])->name('finance.tariffs.update');
    Route::put('/finance-payments/{id}', [\App\Http\Controllers\FinanceController::class, 'updatePaymentRequest'])->name('finance.payments.update');
    Route::post('/finance-payments/{id}/approve', [\App\Http\Controllers\FinanceController::class, 'approvePaymentRequest'])->name('finance.payments.approve');
});

// ── Hearings ──────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Hearings,view'])->group(function () {
    Route::get('/civil-hearing-cases',                   [HearingController::class, 'hearingCases'])->name('civil.hearing.cases');
    Route::get('/hearing-scripture',                     [HearingController::class, 'hearingScripture'])->name('hearings.scripture');
    Route::get('/hearing-scripture/{caseId}/create',     [HearingController::class, 'createScripture'])->name('hearings.scripture.create');
    Route::post('/hearing-scripture',                    [HearingController::class, 'storeScripture'])->name('hearings.scripture.store');
    Route::get('/hearing-scripture/{id}/edit',           [HearingController::class, 'editScripture'])->name('hearings.scripture.edit');
    Route::put('/hearing-scripture/{id}',                [HearingController::class, 'updateScripture'])->name('hearings.scripture.update');
    Route::get('/hearings',                  [HearingController::class, 'index'])    ->name('hearings.index');
    Route::get('/hearings-view',             [HearingController::class, 'viewIndex'])->name('hearings.view');
    Route::get('/hearings/create/{caseId?}', [HearingController::class, 'create'])  ->name('hearings.create');
    Route::post('/hearings',                 [HearingController::class, 'store'])   ->name('hearings.store');
    Route::get('/hearings/{id}/edit',        [HearingController::class, 'edit'])    ->name('hearings.edit');
    Route::get('/hearings/case/{caseId}/json',         [HearingController::class, 'hearingsByCase'])->name('hearings.by.case');
    Route::put('/hearings/{id}',                [HearingController::class, 'update'])            ->name('hearings.update');
    Route::delete('/hearings/{id}',             [HearingController::class, 'destroy'])           ->name('hearings.destroy');
    Route::post('/hearings/{id}/request-stamp', [HearingController::class, 'requestHearingStamp'])->name('hearings.request-stamp');
});

// Viewable by District Hearings staff OR Appeal Court staff (for lower-court case file access)
Route::middleware(['auth', 'permission:Hearings|Appeal Hearings,view'])->group(function () {
    Route::get('/hearing-scripture/{id}/document',   [HearingController::class, 'scriptureDocument']) ->name('hearings.scripture.document');
    Route::get('/hearings/{id}/document',            [HearingController::class, 'document'])          ->name('hearings.document');
    Route::get('/hearings/{id}/document-pdf',        [HearingController::class, 'documentPdf'])       ->name('hearings.document-pdf');
    Route::get('/hearings/case/{caseId}/document',   [HearingController::class, 'documentByCase'])     ->name('hearings.document.case');
});

// ── Judgments ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Judgments,view'])->group(function () {
    Route::get('/judgments',               [\App\Http\Controllers\JudgmentController::class, 'index']) ->name('judgments.index');
    Route::get('/judgments/{caseId}/create', [\App\Http\Controllers\JudgmentController::class, 'create'])->name('judgments.create');
    Route::post('/judgments',              [\App\Http\Controllers\JudgmentController::class, 'store']) ->name('judgments.store');
    Route::get('/judgments/{id}/edit',     [\App\Http\Controllers\JudgmentController::class, 'edit'])    ->name('judgments.edit');
    Route::get('/judgments/{id}/document', [\App\Http\Controllers\JudgmentController::class, 'document'])->name('judgments.document');
    Route::get('/judgments/{id}/document-readonly', [\App\Http\Controllers\JudgmentController::class, 'documentReadOnly'])->name('judgments.document.readonly');
    Route::put('/judgments/{id}',          [\App\Http\Controllers\JudgmentController::class, 'update'])  ->name('judgments.update');
});

// Judgment Receipts — its own permission module, separate from Judgments,
// so it can be granted/restricted independently in the Role & Permission Matrix.
Route::middleware(['auth', 'permission:Judgment Receipts,view'])->group(function () {
    Route::get('/judgment-receipts',       [\App\Http\Controllers\JudgmentController::class, 'receiptsIndex'])->name('judgments.receipts');
    Route::post('/judgment-receipts/{judgment}/{party}', [\App\Http\Controllers\JudgmentController::class, 'confirmReceipt'])->name('judgments.receipt.confirm');
});

// ── Return Civil File ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Return Civil File,view'])->group(function () {
    Route::get('/return-civil-file',               [ReturnCivilFileController::class, 'index'])   ->name('ReturnCivilFile.index');
    Route::get('/return-civil-file/{id}/create',   [ReturnCivilFileController::class, 'create'])  ->name('ReturnCivilFile.create');
    Route::post('/return-civil-file',              [ReturnCivilFileController::class, 'store'])   ->name('ReturnCivilFile.store');
});

// Viewable by District Return File staff OR Appeal Court staff (for lower-court case file access)
Route::middleware(['auth', 'permission:Return Civil File|Appeal Return File,view'])->group(function () {
    Route::get('/return-civil-file/{id}/document', [ReturnCivilFileController::class, 'document'])->name('ReturnCivilFile.document');
    Route::get('/return-civil-file/{id}/document-readonly', [ReturnCivilFileController::class, 'documentReadOnly'])->name('ReturnCivilFile.document.readonly');
    Route::get('/return-civil-file/{id}/document-pdf', [ReturnCivilFileController::class, 'documentPdf'])->name('ReturnCivilFile.document-pdf');
});

// ── Close Case ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Close Case,view'])->group(function () {
    Route::get('/close-cases',              [\App\Http\Controllers\CloseCaseController::class, 'index'])   ->name('close_case.index');
    Route::get('/close-cases/{id}/form',    [\App\Http\Controllers\CloseCaseController::class, 'form'])    ->name('close_case.form');
    Route::post('/close-cases/store',       [\App\Http\Controllers\CloseCaseController::class, 'store'])   ->name('close_case.store');
    Route::get('/close-cases/{id}/stamp-request', [\App\Http\Controllers\CloseCaseController::class, 'stampRequest']) ->name('close_case.stamp-request');
    Route::post('/close-cases/{id}/close',        [\App\Http\Controllers\CloseCaseController::class, 'close'])        ->name('close_case.close');
});

// Viewable by District Close Case staff OR Appeal Court staff (for lower-court case file access)
Route::middleware(['auth', 'permission:Close Case|Appeal Close Case,view'])->group(function () {
    Route::get('/close-cases/{id}/document', [\App\Http\Controllers\CloseCaseController::class, 'document'])->name('close_case.document');
    Route::get('/close-cases/{id}/document-readonly', [\App\Http\Controllers\CloseCaseController::class, 'documentReadOnly'])->name('close_case.document.readonly');
    Route::get('/close-cases/{id}/document-pdf', [\App\Http\Controllers\CloseCaseController::class, 'documentPdf'])->name('close_case.document-pdf');
});

// ── Enforcement ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Enforcement,view'])->group(function () {
    Route::get('/enforcement',               [\App\Http\Controllers\EnforcementController::class, 'index'])   ->name('enforcement.index');
    Route::get('/enforcement/{id}/form',     [\App\Http\Controllers\EnforcementController::class, 'form'])    ->name('enforcement.form');
    Route::post('/enforcement/store',        [\App\Http\Controllers\EnforcementController::class, 'store'])   ->name('enforcement.store');
    Route::get('/enforcement/{id}/document', [\App\Http\Controllers\EnforcementController::class, 'document'])->name('enforcement.document');
});

// ── Appeal ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal,view'])->group(function () {
    Route::get('/appeal',           [\App\Http\Controllers\AppealController::class, 'index'])->name('appeal.index');
    Route::get('/appeal/{id}/form', [\App\Http\Controllers\AppealController::class, 'form']) ->name('appeal.form');
    Route::post('/appeal/store',    [\App\Http\Controllers\AppealController::class, 'store'])->name('appeal.store');

    Route::get('/transfer',                     [\App\Http\Controllers\TransferController::class, 'index'])  ->name('transfer.index');
    Route::get('/transfer/{id}/form',           [\App\Http\Controllers\TransferController::class, 'form'])   ->name('transfer.form');
    Route::post('/transfer/store',              [\App\Http\Controllers\TransferController::class, 'store'])  ->name('transfer.store');
    Route::post('/transfer/{transfer}/approve', [\App\Http\Controllers\TransferController::class, 'approve'])->name('transfer.approve');
});

// ══════════════════════════════════════════════════════════════════════════════
// APPEAL CIVIL CASE ROUTES (Banadir Regional Appeal Court)
// ══════════════════════════════════════════════════════════════════════════════

// ── Appeal Civil Registration ─────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal Civil Registration,view'])->group(function () {
    Route::get('appeal-civil-registration/next-fileno/{courtcode}', [\App\Http\Controllers\AppealCivilRegistrationController::class, 'nextFileNo'])->name('appeal-civil-registration.next-fileno');
    Route::get('appeal-civil-registration/rafcaan-cases/{courtcode}', [\App\Http\Controllers\AppealCivilRegistrationController::class, 'rafcaanCases'])->name('appeal-civil-registration.rafcaan-cases');
    Route::get('appeal-civil-tracking', [\App\Http\Controllers\AppealCivilRegistrationController::class, 'tracking'])->name('appeal-civil-tracking.index');
    Route::get('appeal-civil-registration/{id}/supporting', [\App\Http\Controllers\AppealCivilRegistrationController::class, 'supporting'])->name('appeal-civil-registration.supporting');
    Route::resource('appeal-civil-registration', \App\Http\Controllers\AppealCivilRegistrationController::class)->only(['index', 'store', 'update', 'destroy']);

    // Parties
    Route::get('appeal-civil-parties', [\App\Http\Controllers\AppealCivilPartyController::class, 'index'])->name('appeal-civil-parties.index');
    Route::post('appeal-civil-parties', [\App\Http\Controllers\AppealCivilPartyController::class, 'store'])->name('appeal-civil-parties.store');
    Route::delete('appeal-civil-parties/{id}', [\App\Http\Controllers\AppealCivilPartyController::class, 'destroy'])->name('appeal-civil-parties.destroy');
    Route::get('appeal-civil-parties/case/{caseId}', [\App\Http\Controllers\AppealCivilPartyController::class, 'getPartiesByCase'])->name('appeal-civil-parties.by-case');
    Route::post('appeal-civil-parties/notify/{caseId}', [\App\Http\Controllers\AppealCivilPartyController::class, 'sendNotifications'])->name('appeal-civil-parties.notify');
    Route::post('appeal-civil-registration/{id}/import-parties', [\App\Http\Controllers\AppealCivilRegistrationController::class, 'importLowerCourtParties'])->name('appeal-civil-registration.import-parties');

    // Documents
    Route::get('appeal-civil-documents', [\App\Http\Controllers\AppealCivilDocumentController::class, 'index'])->name('appeal-civil-documents.index');
    Route::post('appeal-civil-documents', [\App\Http\Controllers\AppealCivilDocumentController::class, 'store'])->name('appeal-civil-documents.store');
    Route::delete('appeal-civil-documents/{id}', [\App\Http\Controllers\AppealCivilDocumentController::class, 'destroy'])->name('appeal-civil-documents.destroy');
    Route::get('appeal-civil-documents/case/{caseId}', [\App\Http\Controllers\AppealCivilDocumentController::class, 'getDocumentsByCase'])->name('appeal-civil-documents.by-case');

    // Legal Representatives
    Route::get('appeal-civil-legal-reps/case/{caseId}', [\App\Http\Controllers\AppealCivilLegalRepresentativeController::class, 'getByCase'])->name('appeal-civil-legal-reps.by-case');
    Route::post('appeal-civil-legal-reps', [\App\Http\Controllers\AppealCivilLegalRepresentativeController::class, 'store'])->name('appeal-civil-legal-reps.store');
    Route::delete('appeal-civil-legal-reps/{id}', [\App\Http\Controllers\AppealCivilLegalRepresentativeController::class, 'destroy'])->name('appeal-civil-legal-reps.destroy');
});

// Viewing a single case's full info is also reached from the Appeal Hearings
// tracking pages (e.g. Kaaliye Rafcaanka has "Appeal Hearings" access but not
// "Appeal Civil Registration"), so accept either permission here rather than
// only the latter — otherwise that link 403s for anyone who only has Appeal
// Hearings access.
Route::middleware(['auth', 'permission:Appeal Civil Registration|Appeal Hearings,view'])->group(function () {
    Route::get('appeal-civil-registration/{appeal_civil_registration}', [\App\Http\Controllers\AppealCivilRegistrationController::class, 'show'])->name('appeal-civil-registration.show');
});

// ── Appeal Civil Case Lawyers (Lawyer Assignment) ──────────────────────────────
Route::middleware(['auth', 'permission:Appeal Case Lawyers,view'])->group(function () {
    Route::get('appeal-civil-lawyers', [\App\Http\Controllers\AppealCivilLawyerController::class, 'index'])->name('appeal-civil-lawyers.index');
    Route::post('appeal-civil-lawyers', [\App\Http\Controllers\AppealCivilLawyerController::class, 'store'])->name('appeal-civil-lawyers.store');
    Route::put('appeal-civil-lawyers/{id}', [\App\Http\Controllers\AppealCivilLawyerController::class, 'update'])->name('appeal-civil-lawyers.update');
    Route::delete('appeal-civil-lawyers/{id}', [\App\Http\Controllers\AppealCivilLawyerController::class, 'destroy'])->name('appeal-civil-lawyers.destroy');
    Route::get('appeal-civil-lawyers/case/{caseId}', [\App\Http\Controllers\AppealCivilLawyerController::class, 'getAssignmentsByCase'])->name('appeal-civil-lawyers.by-case');
});

// ── Appeal Civil Handover ─────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal Case Handover,view'])->group(function () {
    Route::get('appeal-civil-handover/{id}/document', [\App\Http\Controllers\AppealCivilHandoverController::class, 'document'])->name('appeal-civil-handover.document');
    Route::get('appeal-civil-handover/{id}/document-pdf', [\App\Http\Controllers\AppealCivilHandoverController::class, 'documentPdf'])->name('appeal-civil-handover.document-pdf');
});

// Handover Approval — its own permission module, separate from Appeal Case
// Handover, so it can be granted/restricted independently in the Matrix.
Route::middleware(['auth', 'permission:Appeal Case Handover Approval,view'])->group(function () {
    Route::post('appeal-civil-handover/{id}/approve', [\App\Http\Controllers\AppealCivilHandoverController::class, 'approve'])->name('appeal-civil-handover.approve');
    Route::post('appeal-civil-handover/{id}/reject', [\App\Http\Controllers\AppealCivilHandoverController::class, 'reject'])->name('appeal-civil-handover.reject');
    Route::get('appeal-civil-handover-approval', [\App\Http\Controllers\AppealCivilHandoverController::class, 'approvalIndex'])->name('appeal-civil-handover.approval');
});

// Content-modifying handover actions — Appeal Clerk (Kaaliye Rafcaanka) only
// has "view" on Appeal Case Handover, so this keeps them from editing it.
Route::middleware(['auth', 'permission:Appeal Case Handover,create'])->group(function () {
    Route::get('appeal-civil-handover/{id}', [\App\Http\Controllers\AppealCivilHandoverController::class, 'create'])->name('appeal-civil-handover.create');
    Route::post('appeal-civil-handover', [\App\Http\Controllers\AppealCivilHandoverController::class, 'store'])->name('appeal-civil-handover.store');
});

// ── Appeal Civil Assignment ───────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal Case Assignment,view'])->group(function () {
    Route::get('/appeal-civil-assign', [\App\Http\Controllers\AppealCivilAssignmentController::class, 'index'])->name('appeal-civil-assign.index');
    Route::get('/appeal-civil-assign/add/{id}', [\App\Http\Controllers\AppealCivilAssignmentController::class, 'addJudges'])->name('appeal-civil-assign.add');
    Route::post('/appeal-civil-assign', [\App\Http\Controllers\AppealCivilAssignmentController::class, 'store'])->name('appeal-civil-assign.store');
    Route::put('/appeal-civil-assign/{id}', [\App\Http\Controllers\AppealCivilAssignmentController::class, 'update'])->name('appeal-civil-assign.update');
    Route::delete('/appeal-civil-assign/{id}', [\App\Http\Controllers\AppealCivilAssignmentController::class, 'destroy'])->name('appeal-civil-assign.destroy');
});

// ── Appeal Civil Hearings ─────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal Hearings,view'])->group(function () {
    Route::get('/appeal-civil-hearing-cases',                  [\App\Http\Controllers\AppealCivilHearingController::class, 'hearingCases'])  ->name('appeal-civil.hearing.cases');
    Route::get('/appeal-hearing-scripture',                    [\App\Http\Controllers\AppealCivilHearingController::class, 'hearingScripture'])->name('appeal-hearings.scripture');
    Route::get('/appeal-hearing-scripture/{caseId}/create',    [\App\Http\Controllers\AppealCivilHearingController::class, 'createScripture'])->name('appeal-hearings.scripture.create');
    Route::post('/appeal-hearing-scripture',                   [\App\Http\Controllers\AppealCivilHearingController::class, 'storeScripture']) ->name('appeal-hearings.scripture.store');
    Route::get('/appeal-hearing-scripture/{id}/edit',          [\App\Http\Controllers\AppealCivilHearingController::class, 'editScripture'])  ->name('appeal-hearings.scripture.edit');
    Route::put('/appeal-hearing-scripture/{id}',               [\App\Http\Controllers\AppealCivilHearingController::class, 'updateScripture'])->name('appeal-hearings.scripture.update');
    Route::get('/appeal-hearing-scripture/{id}/document',      [\App\Http\Controllers\AppealCivilHearingController::class, 'scriptureDocument'])->name('appeal-hearings.scripture.document');
    Route::get('/appeal-hearings',                  [\App\Http\Controllers\AppealCivilHearingController::class, 'index'])    ->name('appeal-hearings.index');
    Route::get('/appeal-hearings-view',             [\App\Http\Controllers\AppealCivilHearingController::class, 'viewIndex'])->name('appeal-hearings.view');
    Route::get('/appeal-hearings/create/{caseId?}', [\App\Http\Controllers\AppealCivilHearingController::class, 'create'])  ->name('appeal-hearings.create');
    Route::post('/appeal-hearings',                 [\App\Http\Controllers\AppealCivilHearingController::class, 'store'])   ->name('appeal-hearings.store');
    Route::get('/appeal-hearings/{id}/edit',        [\App\Http\Controllers\AppealCivilHearingController::class, 'edit'])    ->name('appeal-hearings.edit');
    Route::get('/appeal-hearings/{id}/document',    [\App\Http\Controllers\AppealCivilHearingController::class, 'document'])->name('appeal-hearings.document');
    Route::get('/appeal-hearings/{id}/document-pdf', [\App\Http\Controllers\AppealCivilHearingController::class, 'documentPdf'])->name('appeal-hearings.document-pdf');
    Route::put('/appeal-hearings/{id}',             [\App\Http\Controllers\AppealCivilHearingController::class, 'update'])  ->name('appeal-hearings.update');
    Route::delete('/appeal-hearings/{id}',          [\App\Http\Controllers\AppealCivilHearingController::class, 'destroy']) ->name('appeal-hearings.destroy');
    Route::get('/appeal-hearings/case/{caseId}/json', [\App\Http\Controllers\AppealCivilHearingController::class, 'hearingsByCase'])->name('appeal-hearings.by.case');
});

// ── Appeal Criminal Hearings ─────────────────────────────────────────────────
// Reuses the shared "Appeal Hearings" module. Route names are
// appeal-criminal-hearings.* rather than bare appeal-hearings.* — Appeal
// Civil's route names for this stage aren't case-type-prefixed, so this
// avoids a name collision.
Route::middleware(['auth', 'permission:Appeal Hearings,view'])->group(function () {
    Route::get('/appeal-criminal-hearing-cases',                  [\App\Http\Controllers\AppealCriminalHearingController::class, 'hearingCases'])  ->name('appeal-criminal.hearing.cases');
    Route::get('/appeal-criminal-hearing-scripture',                    [\App\Http\Controllers\AppealCriminalHearingController::class, 'hearingScripture'])->name('appeal-criminal-hearings.scripture');
    Route::get('/appeal-criminal-hearing-scripture/{caseId}/create',    [\App\Http\Controllers\AppealCriminalHearingController::class, 'createScripture'])->name('appeal-criminal-hearings.scripture.create');
    Route::post('/appeal-criminal-hearing-scripture',                   [\App\Http\Controllers\AppealCriminalHearingController::class, 'storeScripture']) ->name('appeal-criminal-hearings.scripture.store');
    Route::get('/appeal-criminal-hearing-scripture/{id}/edit',          [\App\Http\Controllers\AppealCriminalHearingController::class, 'editScripture'])  ->name('appeal-criminal-hearings.scripture.edit');
    Route::put('/appeal-criminal-hearing-scripture/{id}',               [\App\Http\Controllers\AppealCriminalHearingController::class, 'updateScripture'])->name('appeal-criminal-hearings.scripture.update');
    Route::get('/appeal-criminal-hearing-scripture/{id}/document',      [\App\Http\Controllers\AppealCriminalHearingController::class, 'scriptureDocument'])->name('appeal-criminal-hearings.scripture.document');
    Route::get('/appeal-criminal-hearings',                  [\App\Http\Controllers\AppealCriminalHearingController::class, 'index'])    ->name('appeal-criminal-hearings.index');
    Route::get('/appeal-criminal-hearings-view',             [\App\Http\Controllers\AppealCriminalHearingController::class, 'viewIndex'])->name('appeal-criminal-hearings.view');
    Route::get('/appeal-criminal-hearings/create/{caseId?}', [\App\Http\Controllers\AppealCriminalHearingController::class, 'create'])  ->name('appeal-criminal-hearings.create');
    Route::post('/appeal-criminal-hearings',                 [\App\Http\Controllers\AppealCriminalHearingController::class, 'store'])   ->name('appeal-criminal-hearings.store');
    Route::get('/appeal-criminal-hearings/{id}/edit',        [\App\Http\Controllers\AppealCriminalHearingController::class, 'edit'])    ->name('appeal-criminal-hearings.edit');
    Route::get('/appeal-criminal-hearings/{id}/document',    [\App\Http\Controllers\AppealCriminalHearingController::class, 'document'])->name('appeal-criminal-hearings.document');
    Route::get('/appeal-criminal-hearings/{id}/document-pdf', [\App\Http\Controllers\AppealCriminalHearingController::class, 'documentPdf'])->name('appeal-criminal-hearings.document-pdf');
    Route::put('/appeal-criminal-hearings/{id}',             [\App\Http\Controllers\AppealCriminalHearingController::class, 'update'])  ->name('appeal-criminal-hearings.update');
    Route::delete('/appeal-criminal-hearings/{id}',          [\App\Http\Controllers\AppealCriminalHearingController::class, 'destroy']) ->name('appeal-criminal-hearings.destroy');
    Route::get('/appeal-criminal-hearings/case/{caseId}/json', [\App\Http\Controllers\AppealCriminalHearingController::class, 'hearingsByCase'])->name('appeal-criminal-hearings.by.case');
});

// ── Appeal Civil Judgments ────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal Judgments,view'])->group(function () {
    Route::get('/appeal-judgments',                 [\App\Http\Controllers\AppealCivilJudgmentController::class, 'index'])       ->name('appeal-judgments.index');
    Route::get('/appeal-judgments/{caseId}/create', [\App\Http\Controllers\AppealCivilJudgmentController::class, 'create'])      ->name('appeal-judgments.create');
    Route::post('/appeal-judgments',                [\App\Http\Controllers\AppealCivilJudgmentController::class, 'store'])       ->name('appeal-judgments.store');
    Route::get('/appeal-judgments/{id}/edit',       [\App\Http\Controllers\AppealCivilJudgmentController::class, 'edit'])        ->name('appeal-judgments.edit');
    Route::put('/appeal-judgments/{id}',            [\App\Http\Controllers\AppealCivilJudgmentController::class, 'update'])      ->name('appeal-judgments.update');
    Route::get('/appeal-judgments/{id}/document',   [\App\Http\Controllers\AppealCivilJudgmentController::class, 'document'])    ->name('appeal-judgments.document');
});

// Appeal Judgment Receipts — its own permission module, separate from
// Appeal Judgments, so it can be granted/restricted independently in the Matrix.
Route::middleware(['auth', 'permission:Appeal Judgment Receipts,view'])->group(function () {
    Route::get('/appeal-judgment-receipts',         [\App\Http\Controllers\AppealCivilJudgmentController::class, 'receiptsIndex'])->name('appeal-judgments.receipts');
    Route::post('/appeal-judgment-receipts/{judgment}/{party}', [\App\Http\Controllers\AppealCivilJudgmentController::class, 'confirmReceipt'])->name('appeal-judgments.receipt.confirm');
});

// ── Appeal Civil Return File ──────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal Return File,view'])->group(function () {
    Route::get('/appeal-return-file',               [\App\Http\Controllers\AppealCivilReturnFileController::class, 'index'])   ->name('appeal-return-file.index');
    Route::get('/appeal-return-file/{id}/create',   [\App\Http\Controllers\AppealCivilReturnFileController::class, 'create'])  ->name('appeal-return-file.create');
    Route::post('/appeal-return-file',              [\App\Http\Controllers\AppealCivilReturnFileController::class, 'store'])   ->name('appeal-return-file.store');
    Route::get('/appeal-return-file/{id}/document', [\App\Http\Controllers\AppealCivilReturnFileController::class, 'document'])->name('appeal-return-file.document');
    Route::get('/appeal-return-file/{id}/document-pdf', [\App\Http\Controllers\AppealCivilReturnFileController::class, 'documentPdf'])->name('appeal-return-file.document-pdf');
});

// ── Appeal Civil Close Case ───────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal Close Case,view'])->group(function () {
    Route::get('/appeal-close-cases',              [\App\Http\Controllers\AppealCivilCloseCaseController::class, 'index'])   ->name('appeal-close-case.index');
    Route::get('/appeal-close-cases/{id}/form',    [\App\Http\Controllers\AppealCivilCloseCaseController::class, 'form'])    ->name('appeal-close-case.form');
    Route::post('/appeal-close-cases/store',       [\App\Http\Controllers\AppealCivilCloseCaseController::class, 'store'])   ->name('appeal-close-case.store');
    Route::get('/appeal-close-cases/{id}/document', [\App\Http\Controllers\AppealCivilCloseCaseController::class, 'document'])->name('appeal-close-case.document');
    Route::post('/appeal-close-cases/{id}/close',   [\App\Http\Controllers\AppealCivilCloseCaseController::class, 'close'])  ->name('appeal-close-case.close');
});

// ── Appeal Civil Enforcement ──────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal Enforcement,view'])->group(function () {
    Route::get('/appeal-enforcement',               [\App\Http\Controllers\AppealCivilEnforcementController::class, 'index'])   ->name('appeal-enforcement.index');
    Route::get('/appeal-enforcement/{id}/form',     [\App\Http\Controllers\AppealCivilEnforcementController::class, 'form'])    ->name('appeal-enforcement.form');
    Route::post('/appeal-enforcement/store',        [\App\Http\Controllers\AppealCivilEnforcementController::class, 'store'])   ->name('appeal-enforcement.store');
    Route::get('/appeal-enforcement/{id}/document', [\App\Http\Controllers\AppealCivilEnforcementController::class, 'document'])->name('appeal-enforcement.document');
});

// ── Appeal Civil Appeal & Transfer ───────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal Cases,view'])->group(function () {
    Route::get('/appeal-civil-appeal',           [\App\Http\Controllers\AppealCivilAppealController::class, 'index'])->name('appeal-civil-appeal.index');
    Route::get('/appeal-civil-appeal/{id}/form', [\App\Http\Controllers\AppealCivilAppealController::class, 'form']) ->name('appeal-civil-appeal.form');
    Route::post('/appeal-civil-appeal/store',    [\App\Http\Controllers\AppealCivilAppealController::class, 'store'])->name('appeal-civil-appeal.store');

    Route::get('/appeal-transfer',                     [\App\Http\Controllers\AppealCivilTransferController::class, 'index'])  ->name('appeal-transfer.index');
    Route::get('/appeal-transfer/{id}/form',           [\App\Http\Controllers\AppealCivilTransferController::class, 'form'])   ->name('appeal-transfer.form');
    Route::post('/appeal-transfer/store',              [\App\Http\Controllers\AppealCivilTransferController::class, 'store'])  ->name('appeal-transfer.store');
    Route::post('/appeal-transfer/{transfer}/approve', [\App\Http\Controllers\AppealCivilTransferController::class, 'approve'])->name('appeal-transfer.approve');
});

// ══════════════════════════════════════════════════════════════════════════════
// APPEAL CRIMINAL CASE ROUTES (Banadir Regional Appeal Court)
// Registration stage only for now (Assign/Hearing/Conclusion/Integration are
// separate phases) — mirrors Appeal Civil's structure and the criminal-domain
// fields already established by District Criminal.
// ══════════════════════════════════════════════════════════════════════════════

// ── Appeal Criminal Registration ────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Appeal Criminal Registration,view'])->group(function () {
    Route::get('appeal-criminal-registration/next-fileno/{courtcode}', [\App\Http\Controllers\AppealCriminalRegistrationController::class, 'nextFileNo'])->name('appeal-criminal-registration.next-fileno');
    Route::get('appeal-criminal-registration/rafcaan-cases/{courtcode}', [\App\Http\Controllers\AppealCriminalRegistrationController::class, 'rafcaanCases'])->name('appeal-criminal-registration.rafcaan-cases');
    Route::get('appeal-criminal-tracking', [\App\Http\Controllers\AppealCriminalRegistrationController::class, 'tracking'])->name('appeal-criminal-tracking.index');
    Route::get('appeal-criminal-registration/{id}/supporting', [\App\Http\Controllers\AppealCriminalRegistrationController::class, 'supporting'])->name('appeal-criminal-registration.supporting');
    Route::get('appeal-criminal-registration/{appeal_criminal_registration}', [\App\Http\Controllers\AppealCriminalRegistrationController::class, 'show'])->name('appeal-criminal-registration.show');
    Route::resource('appeal-criminal-registration', \App\Http\Controllers\AppealCriminalRegistrationController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('appeal-criminal-registration/{id}/import-parties', [\App\Http\Controllers\AppealCriminalRegistrationController::class, 'importLowerCourtParties'])->name('appeal-criminal-registration.import-parties');

    // Parties
    Route::get('appeal-criminal-parties', [\App\Http\Controllers\AppealCriminalPartyController::class, 'index'])->name('appeal-criminal-parties.index');
    Route::post('appeal-criminal-parties', [\App\Http\Controllers\AppealCriminalPartyController::class, 'store'])->name('appeal-criminal-parties.store');
    Route::delete('appeal-criminal-parties/{id}', [\App\Http\Controllers\AppealCriminalPartyController::class, 'destroy'])->name('appeal-criminal-parties.destroy');
    Route::get('appeal-criminal-parties/case/{caseId}', [\App\Http\Controllers\AppealCriminalPartyController::class, 'getPartiesByCase'])->name('appeal-criminal-parties.by-case');
    Route::post('appeal-criminal-parties/notify/{caseId}', [\App\Http\Controllers\AppealCriminalPartyController::class, 'sendNotifications'])->name('appeal-criminal-parties.notify');

    // Documents
    Route::get('appeal-criminal-documents', [\App\Http\Controllers\AppealCriminalDocumentController::class, 'index'])->name('appeal-criminal-documents.index');
    Route::post('appeal-criminal-documents', [\App\Http\Controllers\AppealCriminalDocumentController::class, 'store'])->name('appeal-criminal-documents.store');
    Route::delete('appeal-criminal-documents/{id}', [\App\Http\Controllers\AppealCriminalDocumentController::class, 'destroy'])->name('appeal-criminal-documents.destroy');
    Route::get('appeal-criminal-documents/case/{caseId}', [\App\Http\Controllers\AppealCriminalDocumentController::class, 'getDocumentsByCase'])->name('appeal-criminal-documents.by-case');

    // Legal Representatives
    Route::get('appeal-criminal-legal-reps/case/{caseId}', [\App\Http\Controllers\AppealCriminalLegalRepresentativeController::class, 'getByCase'])->name('appeal-criminal-legal-reps.by-case');
    Route::post('appeal-criminal-legal-reps', [\App\Http\Controllers\AppealCriminalLegalRepresentativeController::class, 'store'])->name('appeal-criminal-legal-reps.store');
    Route::delete('appeal-criminal-legal-reps/{id}', [\App\Http\Controllers\AppealCriminalLegalRepresentativeController::class, 'destroy'])->name('appeal-criminal-legal-reps.destroy');
});

// ── Appeal Criminal Case Lawyers (Lawyer Assignment) ────────────────────────────
// Reuses the "Appeal Case Lawyers" module shared across all Appeal case types,
// same as Appeal Civil's lawyer routes below.
Route::middleware(['auth', 'permission:Appeal Case Lawyers,view'])->group(function () {
    Route::get('appeal-criminal-lawyers', [\App\Http\Controllers\AppealCriminalLawyerController::class, 'index'])->name('appeal-criminal-lawyers.index');
    Route::post('appeal-criminal-lawyers', [\App\Http\Controllers\AppealCriminalLawyerController::class, 'store'])->name('appeal-criminal-lawyers.store');
    Route::put('appeal-criminal-lawyers/{id}', [\App\Http\Controllers\AppealCriminalLawyerController::class, 'update'])->name('appeal-criminal-lawyers.update');
    Route::delete('appeal-criminal-lawyers/{id}', [\App\Http\Controllers\AppealCriminalLawyerController::class, 'destroy'])->name('appeal-criminal-lawyers.destroy');
    Route::get('appeal-criminal-lawyers/case/{caseId}', [\App\Http\Controllers\AppealCriminalLawyerController::class, 'getAssignmentsByCase'])->name('appeal-criminal-lawyers.by-case');
});

// ── Appeal Criminal Case Assignment (Panel of Judges) ───────────────────────────
// Reuses the "Appeal Case Assignment" module shared across all Appeal case
// types, same as Appeal Civil's assignment routes below.
Route::middleware(['auth', 'permission:Appeal Case Assignment,view'])->group(function () {
    Route::get('/appeal-criminal-assign', [\App\Http\Controllers\AppealCriminalAssignmentController::class, 'index'])->name('appeal-criminal-assign.index');
    Route::get('/appeal-criminal-assign/add/{id}', [\App\Http\Controllers\AppealCriminalAssignmentController::class, 'addJudges'])->name('appeal-criminal-assign.add');
    Route::post('/appeal-criminal-assign', [\App\Http\Controllers\AppealCriminalAssignmentController::class, 'store'])->name('appeal-criminal-assign.store');
    Route::put('/appeal-criminal-assign/{id}', [\App\Http\Controllers\AppealCriminalAssignmentController::class, 'update'])->name('appeal-criminal-assign.update');
    Route::delete('/appeal-criminal-assign/{id}', [\App\Http\Controllers\AppealCriminalAssignmentController::class, 'destroy'])->name('appeal-criminal-assign.destroy');
});

// ══════════════════════════════════════════════════════════════════════════════

// ── District Family Case Registration (+ Parties, Documents, Lawyers sub-resources) ──
Route::middleware(['auth', 'permission:Family Case Registration,view'])->group(function () {
    Route::get('family-registration/next-fileno/{courtcode}', [\App\Http\Controllers\DistrictFamilyRegistrationController::class, 'nextFileNo']);
    Route::get('family-case-tracking', [\App\Http\Controllers\DistrictFamilyRegistrationController::class, 'tracking'])->name('family-case-tracking.index');
    Route::get('family-registration/{id}/supporting', [\App\Http\Controllers\DistrictFamilyRegistrationController::class, 'supporting'])->name('family-registration.supporting');
    Route::resource('family-registration', \App\Http\Controllers\DistrictFamilyRegistrationController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

// Shown from either "Family Case Registration" or "Family Hearings" (case detail page
// is also reachable from the hearings flow), so accept either permission here rather
// than only the latter — otherwise that link 403s for anyone who only has Family
// Hearings access.
Route::middleware(['auth', 'permission:Family Case Registration|Family Hearings,view'])->group(function () {
    Route::get('family-registration/{family_registration}', [\App\Http\Controllers\DistrictFamilyRegistrationController::class, 'show'])->name('family-registration.show');
});

// ── District Family Case Handover ────────────────────────────────────────────
Route::middleware(['auth', 'permission:Family Case Handover,view'])->group(function () {
    Route::get('family-registration-handover', [\App\Http\Controllers\DistrictFamilyRegistrationController::class, 'handover'])->name('family-registration.handover');
});

// ── District Family Case Parties / Documents / Legal Reps / Lawyers ─────────
Route::middleware(['auth', 'permission:Family Case Parties,view'])->group(function () {
    Route::get('family-case-parties', [\App\Http\Controllers\DistrictFamilyPartyController::class, 'index'])->name('family-case-parties.index');
    Route::post('family-case-parties', [\App\Http\Controllers\DistrictFamilyPartyController::class, 'store'])->name('family-case-parties.store');
    Route::get('family-case-parties/case/{caseId}', [\App\Http\Controllers\DistrictFamilyPartyController::class, 'getPartiesByCase']);
    Route::post('family-case-parties/notify/{caseId}', [\App\Http\Controllers\DistrictFamilyPartyController::class, 'sendNotifications'])->name('family-case-parties.notify');
    Route::delete('family-case-parties/{id}', [\App\Http\Controllers\DistrictFamilyPartyController::class, 'destroy']);
});

Route::middleware(['auth', 'permission:Family Case Documents,view'])->group(function () {
    Route::get('/family-case-documents', [\App\Http\Controllers\DistrictFamilyDocumentController::class, 'index'])->name('family-case-documents.index');
    Route::post('/family-case-documents', [\App\Http\Controllers\DistrictFamilyDocumentController::class, 'store'])->name('family-case-documents.store');
    Route::get('/family-case-documents/case/{id}', [\App\Http\Controllers\DistrictFamilyDocumentController::class, 'getDocumentsByCase']);
    Route::delete('/family-case-documents/{id}', [\App\Http\Controllers\DistrictFamilyDocumentController::class, 'destroy']);
});

Route::middleware(['auth', 'permission:Family Case Lawyers,view'])->group(function () {
    Route::get('/family-case-lawyers', [\App\Http\Controllers\DistrictFamilyLawyerController::class, 'index'])->name('family-case-lawyers.index');
    Route::post('/family-case-lawyers', [\App\Http\Controllers\DistrictFamilyLawyerController::class, 'store'])->name('family-case-lawyers.store');
    Route::put('/family-case-lawyers/{id}', [\App\Http\Controllers\DistrictFamilyLawyerController::class, 'update']);
    Route::get('/family-case-lawyers/case/{id}', [\App\Http\Controllers\DistrictFamilyLawyerController::class, 'getAssignmentsByCase']);
    Route::delete('/family-case-lawyers/{id}', [\App\Http\Controllers\DistrictFamilyLawyerController::class, 'destroy']);
});

Route::middleware(['auth', 'permission:Family Case Registration,view'])->group(function () {
    Route::get('family-legal-reps/case/{caseId}', [\App\Http\Controllers\DistrictFamilyLegalRepresentativeController::class, 'getByCase']);
    Route::post('family-legal-reps', [\App\Http\Controllers\DistrictFamilyLegalRepresentativeController::class, 'store']);
    Route::delete('family-legal-reps/{id}', [\App\Http\Controllers\DistrictFamilyLegalRepresentativeController::class, 'destroy']);
});

// ── District Execution Case Parties / Documents / Legal Reps / Lawyers ─────────
Route::middleware(['auth', 'permission:Execution Case Parties,view'])->group(function () {
    Route::get('execution-case-parties', [\App\Http\Controllers\DistrictExecutionPartyController::class, 'index'])->name('execution-case-parties.index');
    Route::post('execution-case-parties', [\App\Http\Controllers\DistrictExecutionPartyController::class, 'store'])->name('execution-case-parties.store');
    Route::get('execution-case-parties/case/{caseId}', [\App\Http\Controllers\DistrictExecutionPartyController::class, 'getPartiesByCase']);
    Route::post('execution-case-parties/notify/{caseId}', [\App\Http\Controllers\DistrictExecutionPartyController::class, 'sendNotifications'])->name('execution-case-parties.notify');
    Route::delete('execution-case-parties/{id}', [\App\Http\Controllers\DistrictExecutionPartyController::class, 'destroy']);
});

Route::middleware(['auth', 'permission:Execution Case Documents,view'])->group(function () {
    Route::get('/execution-case-documents', [\App\Http\Controllers\DistrictExecutionDocumentController::class, 'index'])->name('execution-case-documents.index');
    Route::post('/execution-case-documents', [\App\Http\Controllers\DistrictExecutionDocumentController::class, 'store'])->name('execution-case-documents.store');
    Route::get('/execution-case-documents/case/{id}', [\App\Http\Controllers\DistrictExecutionDocumentController::class, 'getDocumentsByCase']);
    Route::delete('/execution-case-documents/{id}', [\App\Http\Controllers\DistrictExecutionDocumentController::class, 'destroy']);
});

Route::middleware(['auth', 'permission:Execution Case Lawyers,view'])->group(function () {
    Route::get('/execution-case-lawyers', [\App\Http\Controllers\DistrictExecutionLawyerController::class, 'index'])->name('execution-case-lawyers.index');
    Route::post('/execution-case-lawyers', [\App\Http\Controllers\DistrictExecutionLawyerController::class, 'store'])->name('execution-case-lawyers.store');
    Route::put('/execution-case-lawyers/{id}', [\App\Http\Controllers\DistrictExecutionLawyerController::class, 'update']);
    Route::get('/execution-case-lawyers/case/{id}', [\App\Http\Controllers\DistrictExecutionLawyerController::class, 'getAssignmentsByCase']);
    Route::delete('/execution-case-lawyers/{id}', [\App\Http\Controllers\DistrictExecutionLawyerController::class, 'destroy']);
});

Route::middleware(['auth', 'permission:Execution Case Registration,view'])->group(function () {
    Route::get('execution-legal-reps/case/{caseId}', [\App\Http\Controllers\DistrictExecutionLegalRepresentativeController::class, 'getByCase']);
    Route::post('execution-legal-reps', [\App\Http\Controllers\DistrictExecutionLegalRepresentativeController::class, 'store']);
    Route::delete('execution-legal-reps/{id}', [\App\Http\Controllers\DistrictExecutionLegalRepresentativeController::class, 'destroy']);
});

// ── District Criminal Case Parties / Documents / Legal Reps / Lawyers ─────────
Route::middleware(['auth', 'permission:Criminal Case Parties,view'])->group(function () {
    Route::get('criminal-case-parties', [\App\Http\Controllers\DistrictCriminalPartyController::class, 'index'])->name('criminal-case-parties.index');
    Route::post('criminal-case-parties', [\App\Http\Controllers\DistrictCriminalPartyController::class, 'store'])->name('criminal-case-parties.store');
    Route::get('criminal-case-parties/case/{caseId}', [\App\Http\Controllers\DistrictCriminalPartyController::class, 'getPartiesByCase']);
    Route::post('criminal-case-parties/notify/{caseId}', [\App\Http\Controllers\DistrictCriminalPartyController::class, 'sendNotifications'])->name('criminal-case-parties.notify');
    Route::delete('criminal-case-parties/{id}', [\App\Http\Controllers\DistrictCriminalPartyController::class, 'destroy']);
});

Route::middleware(['auth', 'permission:Criminal Case Documents,view'])->group(function () {
    Route::get('/criminal-case-documents', [\App\Http\Controllers\DistrictCriminalDocumentController::class, 'index'])->name('criminal-case-documents.index');
    Route::post('/criminal-case-documents', [\App\Http\Controllers\DistrictCriminalDocumentController::class, 'store'])->name('criminal-case-documents.store');
    Route::get('/criminal-case-documents/case/{id}', [\App\Http\Controllers\DistrictCriminalDocumentController::class, 'getDocumentsByCase']);
    Route::delete('/criminal-case-documents/{id}', [\App\Http\Controllers\DistrictCriminalDocumentController::class, 'destroy']);
});

Route::middleware(['auth', 'permission:Criminal Case Lawyers,view'])->group(function () {
    Route::get('/criminal-case-lawyers', [\App\Http\Controllers\DistrictCriminalLawyerController::class, 'index'])->name('criminal-case-lawyers.index');
    Route::post('/criminal-case-lawyers', [\App\Http\Controllers\DistrictCriminalLawyerController::class, 'store'])->name('criminal-case-lawyers.store');
    Route::put('/criminal-case-lawyers/{id}', [\App\Http\Controllers\DistrictCriminalLawyerController::class, 'update']);
    Route::get('/criminal-case-lawyers/case/{id}', [\App\Http\Controllers\DistrictCriminalLawyerController::class, 'getAssignmentsByCase']);
    Route::delete('/criminal-case-lawyers/{id}', [\App\Http\Controllers\DistrictCriminalLawyerController::class, 'destroy']);
});

Route::middleware(['auth', 'permission:Criminal Case Registration,view'])->group(function () {
    Route::get('criminal-legal-reps/case/{caseId}', [\App\Http\Controllers\DistrictCriminalLegalRepresentativeController::class, 'getByCase']);
    Route::post('criminal-legal-reps', [\App\Http\Controllers\DistrictCriminalLegalRepresentativeController::class, 'store']);
    Route::delete('criminal-legal-reps/{id}', [\App\Http\Controllers\DistrictCriminalLegalRepresentativeController::class, 'destroy']);
});

// ── District Execution Case Assignment ──────────────────────────────────────────
Route::middleware(['auth', 'permission:Execution Case Assignment,view'])->group(function () {
    Route::get('/execution-case-assign', [\App\Http\Controllers\DistrictExecutionAssignmentController::class, 'index'])->name('execution-case-assign.index');
    Route::get('/execution-case-assign/add/{id}', [\App\Http\Controllers\DistrictExecutionAssignmentController::class, 'addJudges'])->name('execution-case-assign.add');
    Route::post('/execution-case-assign', [\App\Http\Controllers\DistrictExecutionAssignmentController::class, 'store'])->name('execution-case-assign.store');
    Route::delete('/execution-case-assign/{id}', [\App\Http\Controllers\DistrictExecutionAssignmentController::class, 'destroy'])->name('execution-case-assign.destroy');
    Route::put('/execution-case-assign/{id}', [\App\Http\Controllers\DistrictExecutionAssignmentController::class, 'update'])->name('execution-case-assign.update');
});

// ── District Execution Case Handover Approval — own permission module ──────────
Route::middleware(['auth', 'permission:Execution Case Handover Approval,view'])->group(function () {
    Route::post('execution-case-handover/{id}/approve', [\App\Http\Controllers\DistrictExecutionHandoverController::class, 'approve'])->name('execution-case-handover.approve');
    Route::post('execution-case-handover/{id}/reject', [\App\Http\Controllers\DistrictExecutionHandoverController::class, 'reject'])->name('execution-case-handover.reject');
    Route::get('execution-case-handover-approval', [\App\Http\Controllers\DistrictExecutionHandoverController::class, 'approvalIndex'])->name('execution-case-handover.approval');
});

// Content-modifying handover actions — only roles with "create" may edit.
Route::middleware(['auth', 'permission:Execution Case Handover,create'])->group(function () {
    Route::get('execution-case-handover/{id}', [\App\Http\Controllers\DistrictExecutionHandoverController::class, 'create'])->name('execution-case-handover.create');
    Route::post('execution-case-handover', [\App\Http\Controllers\DistrictExecutionHandoverController::class, 'store'])->name('execution-case-handover.store');
});

Route::middleware(['auth', 'permission:Execution Case Handover,view'])->group(function () {
    Route::get('execution-case-handover/{id}/document', [\App\Http\Controllers\DistrictExecutionHandoverController::class, 'document'])->name('execution-case-handover.document');
    Route::get('execution-case-handover/{id}/document-pdf', [\App\Http\Controllers\DistrictExecutionHandoverController::class, 'documentPdf'])->name('execution-case-handover.document-pdf');
});

// ── District Criminal Case Assignment ──────────────────────────────────────────
Route::middleware(['auth', 'permission:Criminal Case Assignment,view'])->group(function () {
    Route::get('/criminal-case-assign', [\App\Http\Controllers\DistrictCriminalAssignmentController::class, 'index'])->name('criminal-case-assign.index');
    Route::get('/criminal-case-assign/add/{id}', [\App\Http\Controllers\DistrictCriminalAssignmentController::class, 'addJudges'])->name('criminal-case-assign.add');
    Route::post('/criminal-case-assign', [\App\Http\Controllers\DistrictCriminalAssignmentController::class, 'store'])->name('criminal-case-assign.store');
    Route::delete('/criminal-case-assign/{id}', [\App\Http\Controllers\DistrictCriminalAssignmentController::class, 'destroy'])->name('criminal-case-assign.destroy');
    Route::put('/criminal-case-assign/{id}', [\App\Http\Controllers\DistrictCriminalAssignmentController::class, 'update'])->name('criminal-case-assign.update');
});

// ── District Criminal Case Handover Approval — own permission module ──────────
Route::middleware(['auth', 'permission:Criminal Case Handover Approval,view'])->group(function () {
    Route::post('criminal-case-handover/{id}/approve', [\App\Http\Controllers\DistrictCriminalHandoverController::class, 'approve'])->name('criminal-case-handover.approve');
    Route::post('criminal-case-handover/{id}/reject', [\App\Http\Controllers\DistrictCriminalHandoverController::class, 'reject'])->name('criminal-case-handover.reject');
    Route::get('criminal-case-handover-approval', [\App\Http\Controllers\DistrictCriminalHandoverController::class, 'approvalIndex'])->name('criminal-case-handover.approval');
});

// Content-modifying handover actions — only roles with "create" may edit.
Route::middleware(['auth', 'permission:Criminal Case Handover,create'])->group(function () {
    Route::get('criminal-case-handover/{id}', [\App\Http\Controllers\DistrictCriminalHandoverController::class, 'create'])->name('criminal-case-handover.create');
    Route::post('criminal-case-handover', [\App\Http\Controllers\DistrictCriminalHandoverController::class, 'store'])->name('criminal-case-handover.store');
});

Route::middleware(['auth', 'permission:Criminal Case Handover,view'])->group(function () {
    Route::get('criminal-case-handover/{id}/document', [\App\Http\Controllers\DistrictCriminalHandoverController::class, 'document'])->name('criminal-case-handover.document');
    Route::get('criminal-case-handover/{id}/document-pdf', [\App\Http\Controllers\DistrictCriminalHandoverController::class, 'documentPdf'])->name('criminal-case-handover.document-pdf');
});

// ── District Family Case Assignment ──────────────────────────────────────────
Route::middleware(['auth', 'permission:Family Case Assignment,view'])->group(function () {
    Route::get('/family-case-assign', [\App\Http\Controllers\DistrictFamilyAssignmentController::class, 'index'])->name('family-case-assign.index');
    Route::get('/family-case-assign/add/{id}', [\App\Http\Controllers\DistrictFamilyAssignmentController::class, 'addJudges'])->name('family-case-assign.add');
    Route::post('/family-case-assign', [\App\Http\Controllers\DistrictFamilyAssignmentController::class, 'store'])->name('family-case-assign.store');
    Route::delete('/family-case-assign/{id}', [\App\Http\Controllers\DistrictFamilyAssignmentController::class, 'destroy'])->name('family-case-assign.destroy');
    Route::put('/family-case-assign/{id}', [\App\Http\Controllers\DistrictFamilyAssignmentController::class, 'update'])->name('family-case-assign.update');
});

// ── District Family Case Handover Approval — own permission module ──────────
Route::middleware(['auth', 'permission:Family Case Handover Approval,view'])->group(function () {
    Route::post('family-case-handover/{id}/approve', [\App\Http\Controllers\DistrictFamilyHandoverController::class, 'approve'])->name('family-case-handover.approve');
    Route::post('family-case-handover/{id}/reject', [\App\Http\Controllers\DistrictFamilyHandoverController::class, 'reject'])->name('family-case-handover.reject');
    Route::get('family-case-handover-approval', [\App\Http\Controllers\DistrictFamilyHandoverController::class, 'approvalIndex'])->name('family-case-handover.approval');
});

// Content-modifying handover actions — only roles with "create" may edit.
Route::middleware(['auth', 'permission:Family Case Handover,create'])->group(function () {
    Route::get('family-case-handover/{id}', [\App\Http\Controllers\DistrictFamilyHandoverController::class, 'create'])->name('family-case-handover.create');
    Route::post('family-case-handover', [\App\Http\Controllers\DistrictFamilyHandoverController::class, 'store'])->name('family-case-handover.store');
});

Route::middleware(['auth', 'permission:Family Case Handover,view'])->group(function () {
    Route::get('family-case-handover/{id}/document', [\App\Http\Controllers\DistrictFamilyHandoverController::class, 'document'])->name('family-case-handover.document');
    Route::get('family-case-handover/{id}/document-pdf', [\App\Http\Controllers\DistrictFamilyHandoverController::class, 'documentPdf'])->name('family-case-handover.document-pdf');
});

// ── District Family Hearings ─────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Family Hearings,view'])->group(function () {
    Route::get('/family-hearing-cases',                      [\App\Http\Controllers\DistrictFamilyHearingController::class, 'hearingCases'])->name('family.hearing.cases');
    Route::get('/family-hearing-scripture',                  [\App\Http\Controllers\DistrictFamilyHearingController::class, 'hearingScripture'])->name('family-hearings.scripture');
    Route::get('/family-hearing-scripture/{caseId}/create',  [\App\Http\Controllers\DistrictFamilyHearingController::class, 'createScripture'])->name('family-hearings.scripture.create');
    Route::post('/family-hearing-scripture',                 [\App\Http\Controllers\DistrictFamilyHearingController::class, 'storeScripture'])->name('family-hearings.scripture.store');
    Route::get('/family-hearing-scripture/{id}/edit',        [\App\Http\Controllers\DistrictFamilyHearingController::class, 'editScripture'])->name('family-hearings.scripture.edit');
    Route::put('/family-hearing-scripture/{id}',              [\App\Http\Controllers\DistrictFamilyHearingController::class, 'updateScripture'])->name('family-hearings.scripture.update');
    Route::get('/family-hearings',                  [\App\Http\Controllers\DistrictFamilyHearingController::class, 'index'])    ->name('family-hearings.index');
    Route::get('/family-hearings-view',             [\App\Http\Controllers\DistrictFamilyHearingController::class, 'viewIndex'])->name('family-hearings.view');
    Route::get('/family-hearings/create/{caseId?}', [\App\Http\Controllers\DistrictFamilyHearingController::class, 'create'])  ->name('family-hearings.create');
    Route::post('/family-hearings',                 [\App\Http\Controllers\DistrictFamilyHearingController::class, 'store'])   ->name('family-hearings.store');
    Route::get('/family-hearings/{id}/edit',        [\App\Http\Controllers\DistrictFamilyHearingController::class, 'edit'])    ->name('family-hearings.edit');
    Route::get('/family-hearings/case/{caseId}/json', [\App\Http\Controllers\DistrictFamilyHearingController::class, 'hearingsByCase'])->name('family-hearings.by.case');
    Route::put('/family-hearings/{id}',             [\App\Http\Controllers\DistrictFamilyHearingController::class, 'update']) ->name('family-hearings.update');
    Route::delete('/family-hearings/{id}',          [\App\Http\Controllers\DistrictFamilyHearingController::class, 'destroy'])->name('family-hearings.destroy');
});

Route::middleware(['auth', 'permission:Family Hearings,view'])->group(function () {
    Route::get('/family-hearing-scripture/{id}/document', [\App\Http\Controllers\DistrictFamilyHearingController::class, 'scriptureDocument']) ->name('family-hearings.scripture.document');
    Route::get('/family-hearings/{id}/document',           [\App\Http\Controllers\DistrictFamilyHearingController::class, 'document'])          ->name('family-hearings.document');
    Route::get('/family-hearings/{id}/document-pdf',       [\App\Http\Controllers\DistrictFamilyHearingController::class, 'documentPdf'])       ->name('family-hearings.document-pdf');
    Route::get('/family-hearings/case/{caseId}/document',  [\App\Http\Controllers\DistrictFamilyHearingController::class, 'documentByCase'])     ->name('family-hearings.document.case');
});

// Stamp request document page — accessible by Kaaliye and Archive Officer, same pattern as District Civil's
Route::middleware(['auth'])->group(function () {
    Route::get('/family-hearings/{id}/approval-stamp', [\App\Http\Controllers\DistrictFamilyHearingController::class, 'approvalStampDocument'])->name('family-hearings.approval.stamp');
});

// ── District Execution Hearings ─────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Execution Hearings,view'])->group(function () {
    Route::get('/execution-hearing-cases',                      [\App\Http\Controllers\DistrictExecutionHearingController::class, 'hearingCases'])->name('execution.hearing.cases');
    Route::get('/execution-hearing-scripture',                  [\App\Http\Controllers\DistrictExecutionHearingController::class, 'hearingScripture'])->name('execution-hearings.scripture');
    Route::get('/execution-hearing-scripture/{caseId}/create',  [\App\Http\Controllers\DistrictExecutionHearingController::class, 'createScripture'])->name('execution-hearings.scripture.create');
    Route::post('/execution-hearing-scripture',                 [\App\Http\Controllers\DistrictExecutionHearingController::class, 'storeScripture'])->name('execution-hearings.scripture.store');
    Route::get('/execution-hearing-scripture/{id}/edit',        [\App\Http\Controllers\DistrictExecutionHearingController::class, 'editScripture'])->name('execution-hearings.scripture.edit');
    Route::put('/execution-hearing-scripture/{id}',              [\App\Http\Controllers\DistrictExecutionHearingController::class, 'updateScripture'])->name('execution-hearings.scripture.update');
    Route::get('/execution-hearings',                  [\App\Http\Controllers\DistrictExecutionHearingController::class, 'index'])    ->name('execution-hearings.index');
    Route::get('/execution-hearings-view',             [\App\Http\Controllers\DistrictExecutionHearingController::class, 'viewIndex'])->name('execution-hearings.view');
    Route::get('/execution-hearings/create/{caseId?}', [\App\Http\Controllers\DistrictExecutionHearingController::class, 'create'])  ->name('execution-hearings.create');
    Route::post('/execution-hearings',                 [\App\Http\Controllers\DistrictExecutionHearingController::class, 'store'])   ->name('execution-hearings.store');
    Route::get('/execution-hearings/{id}/edit',        [\App\Http\Controllers\DistrictExecutionHearingController::class, 'edit'])    ->name('execution-hearings.edit');
    Route::get('/execution-hearings/case/{caseId}/json', [\App\Http\Controllers\DistrictExecutionHearingController::class, 'hearingsByCase'])->name('execution-hearings.by.case');
    Route::put('/execution-hearings/{id}',             [\App\Http\Controllers\DistrictExecutionHearingController::class, 'update']) ->name('execution-hearings.update');
    Route::delete('/execution-hearings/{id}',          [\App\Http\Controllers\DistrictExecutionHearingController::class, 'destroy'])->name('execution-hearings.destroy');
});

Route::middleware(['auth', 'permission:Execution Hearings,view'])->group(function () {
    Route::get('/execution-hearing-scripture/{id}/document', [\App\Http\Controllers\DistrictExecutionHearingController::class, 'scriptureDocument']) ->name('execution-hearings.scripture.document');
    Route::get('/execution-hearings/{id}/document',           [\App\Http\Controllers\DistrictExecutionHearingController::class, 'document'])          ->name('execution-hearings.document');
    Route::get('/execution-hearings/{id}/document-pdf',       [\App\Http\Controllers\DistrictExecutionHearingController::class, 'documentPdf'])       ->name('execution-hearings.document-pdf');
    Route::get('/execution-hearings/case/{caseId}/document',  [\App\Http\Controllers\DistrictExecutionHearingController::class, 'documentByCase'])     ->name('execution-hearings.document.case');
});

// Stamp request document page — accessible by Kaaliye and Archive Officer, same pattern as District Civil's
Route::middleware(['auth'])->group(function () {
    Route::get('/execution-hearings/{id}/approval-stamp', [\App\Http\Controllers\DistrictExecutionHearingController::class, 'approvalStampDocument'])->name('execution-hearings.approval.stamp');
});

// ── District Criminal Hearings ─────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Criminal Hearings,view'])->group(function () {
    Route::get('/criminal-hearing-cases',                      [\App\Http\Controllers\DistrictCriminalHearingController::class, 'hearingCases'])->name('criminal.hearing.cases');
    Route::get('/criminal-hearing-scripture',                  [\App\Http\Controllers\DistrictCriminalHearingController::class, 'hearingScripture'])->name('criminal-hearings.scripture');
    Route::get('/criminal-hearing-scripture/{caseId}/create',  [\App\Http\Controllers\DistrictCriminalHearingController::class, 'createScripture'])->name('criminal-hearings.scripture.create');
    Route::post('/criminal-hearing-scripture',                 [\App\Http\Controllers\DistrictCriminalHearingController::class, 'storeScripture'])->name('criminal-hearings.scripture.store');
    Route::get('/criminal-hearing-scripture/{id}/edit',        [\App\Http\Controllers\DistrictCriminalHearingController::class, 'editScripture'])->name('criminal-hearings.scripture.edit');
    Route::put('/criminal-hearing-scripture/{id}',              [\App\Http\Controllers\DistrictCriminalHearingController::class, 'updateScripture'])->name('criminal-hearings.scripture.update');
    Route::get('/criminal-hearings',                  [\App\Http\Controllers\DistrictCriminalHearingController::class, 'index'])    ->name('criminal-hearings.index');
    Route::get('/criminal-hearings-view',             [\App\Http\Controllers\DistrictCriminalHearingController::class, 'viewIndex'])->name('criminal-hearings.view');
    Route::get('/criminal-hearings/create/{caseId?}', [\App\Http\Controllers\DistrictCriminalHearingController::class, 'create'])  ->name('criminal-hearings.create');
    Route::post('/criminal-hearings',                 [\App\Http\Controllers\DistrictCriminalHearingController::class, 'store'])   ->name('criminal-hearings.store');
    Route::get('/criminal-hearings/{id}/edit',        [\App\Http\Controllers\DistrictCriminalHearingController::class, 'edit'])    ->name('criminal-hearings.edit');
    Route::get('/criminal-hearings/case/{caseId}/json', [\App\Http\Controllers\DistrictCriminalHearingController::class, 'hearingsByCase'])->name('criminal-hearings.by.case');
    Route::put('/criminal-hearings/{id}',             [\App\Http\Controllers\DistrictCriminalHearingController::class, 'update']) ->name('criminal-hearings.update');
    Route::delete('/criminal-hearings/{id}',          [\App\Http\Controllers\DistrictCriminalHearingController::class, 'destroy'])->name('criminal-hearings.destroy');
});

Route::middleware(['auth', 'permission:Criminal Hearings,view'])->group(function () {
    Route::get('/criminal-hearing-scripture/{id}/document', [\App\Http\Controllers\DistrictCriminalHearingController::class, 'scriptureDocument']) ->name('criminal-hearings.scripture.document');
    Route::get('/criminal-hearings/{id}/document',           [\App\Http\Controllers\DistrictCriminalHearingController::class, 'document'])          ->name('criminal-hearings.document');
    Route::get('/criminal-hearings/{id}/document-pdf',       [\App\Http\Controllers\DistrictCriminalHearingController::class, 'documentPdf'])       ->name('criminal-hearings.document-pdf');
    Route::get('/criminal-hearings/case/{caseId}/document',  [\App\Http\Controllers\DistrictCriminalHearingController::class, 'documentByCase'])     ->name('criminal-hearings.document.case');
});

// Stamp request document page — accessible by Kaaliye and Archive Officer, same pattern as District Civil's
Route::middleware(['auth'])->group(function () {
    Route::get('/criminal-hearings/{id}/approval-stamp', [\App\Http\Controllers\DistrictCriminalHearingController::class, 'approvalStampDocument'])->name('criminal-hearings.approval.stamp');
});

// ── District Family Judgments ────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Family Judgments,view'])->group(function () {
    Route::get('/family-judgments',               [\App\Http\Controllers\DistrictFamilyJudgmentController::class, 'index']) ->name('family-judgments.index');
    Route::get('/family-judgments/{caseId}/create', [\App\Http\Controllers\DistrictFamilyJudgmentController::class, 'create'])->name('family-judgments.create');
    Route::post('/family-judgments',              [\App\Http\Controllers\DistrictFamilyJudgmentController::class, 'store']) ->name('family-judgments.store');
    Route::get('/family-judgments/{id}/edit',     [\App\Http\Controllers\DistrictFamilyJudgmentController::class, 'edit'])    ->name('family-judgments.edit');
    Route::get('/family-judgments/{id}/document', [\App\Http\Controllers\DistrictFamilyJudgmentController::class, 'document'])->name('family-judgments.document');
    Route::get('/family-judgments/{id}/document-readonly', [\App\Http\Controllers\DistrictFamilyJudgmentController::class, 'documentReadOnly'])->name('family-judgments.document.readonly');
    Route::put('/family-judgments/{id}',          [\App\Http\Controllers\DistrictFamilyJudgmentController::class, 'update'])  ->name('family-judgments.update');
});

Route::middleware(['auth', 'permission:Family Judgment Receipts,view'])->group(function () {
    Route::get('/family-judgment-receipts',       [\App\Http\Controllers\DistrictFamilyJudgmentController::class, 'receiptsIndex'])->name('family-judgments.receipts');
    Route::post('/family-judgment-receipts/{judgment}/{party}', [\App\Http\Controllers\DistrictFamilyJudgmentController::class, 'confirmReceipt'])->name('family-judgments.receipt.confirm');
});

// Stamp request document page — accessible by Kaaliye and Archive Officer
Route::middleware(['auth'])->group(function () {
    Route::get('/family-judgments/{id}/stamp-request', [\App\Http\Controllers\DistrictFamilyJudgmentController::class, 'stampRequestDocument'])->name('family-judgments.stamp-request');
});

// ── District Family Return File ──────────────────────────────────────────────
Route::middleware(['auth', 'permission:Family Return File,view'])->group(function () {
    Route::get('/family-return-file',              [\App\Http\Controllers\DistrictFamilyReturnFileController::class, 'index'])  ->name('family-return-file.index');
    Route::get('/family-return-file/{id}/create',  [\App\Http\Controllers\DistrictFamilyReturnFileController::class, 'create']) ->name('family-return-file.create');
    Route::post('/family-return-file',             [\App\Http\Controllers\DistrictFamilyReturnFileController::class, 'store'])  ->name('family-return-file.store');
    Route::get('/family-return-file/{id}/document', [\App\Http\Controllers\DistrictFamilyReturnFileController::class, 'document'])->name('family-return-file.document');
    Route::get('/family-return-file/{id}/document-readonly', [\App\Http\Controllers\DistrictFamilyReturnFileController::class, 'documentReadOnly'])->name('family-return-file.document.readonly');
    Route::get('/family-return-file/{id}/document-pdf', [\App\Http\Controllers\DistrictFamilyReturnFileController::class, 'documentPdf'])->name('family-return-file.document-pdf');
});

// ── District Family Close Case ───────────────────────────────────────────────
Route::middleware(['auth', 'permission:Family Close Case,view'])->group(function () {
    Route::get('/family-close-case',              [\App\Http\Controllers\DistrictFamilyCloseCaseController::class, 'index'])   ->name('family-close-case.index');
    Route::get('/family-close-case/{id}/form',    [\App\Http\Controllers\DistrictFamilyCloseCaseController::class, 'form'])    ->name('family-close-case.form');
    Route::post('/family-close-case/store',       [\App\Http\Controllers\DistrictFamilyCloseCaseController::class, 'store'])   ->name('family-close-case.store');
    Route::get('/family-close-case/{id}/stamp-request', [\App\Http\Controllers\DistrictFamilyCloseCaseController::class, 'stampRequest']) ->name('family-close-case.stamp-request');
    Route::post('/family-close-case/{id}/close',        [\App\Http\Controllers\DistrictFamilyCloseCaseController::class, 'close'])        ->name('family-close-case.close');
    Route::get('/family-close-case/{id}/document', [\App\Http\Controllers\DistrictFamilyCloseCaseController::class, 'document'])->name('family-close-case.document');
    Route::get('/family-close-case/{id}/document-readonly', [\App\Http\Controllers\DistrictFamilyCloseCaseController::class, 'documentReadOnly'])->name('family-close-case.document.readonly');
    Route::get('/family-close-case/{id}/document-pdf', [\App\Http\Controllers\DistrictFamilyCloseCaseController::class, 'documentPdf'])->name('family-close-case.document-pdf');
});

// ── District Execution Judgments ────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Execution Judgments,view'])->group(function () {
    Route::get('/execution-judgments',               [\App\Http\Controllers\DistrictExecutionJudgmentController::class, 'index']) ->name('execution-judgments.index');
    Route::get('/execution-judgments/{caseId}/create', [\App\Http\Controllers\DistrictExecutionJudgmentController::class, 'create'])->name('execution-judgments.create');
    Route::post('/execution-judgments',              [\App\Http\Controllers\DistrictExecutionJudgmentController::class, 'store']) ->name('execution-judgments.store');
    Route::get('/execution-judgments/{id}/edit',     [\App\Http\Controllers\DistrictExecutionJudgmentController::class, 'edit'])    ->name('execution-judgments.edit');
    Route::get('/execution-judgments/{id}/document', [\App\Http\Controllers\DistrictExecutionJudgmentController::class, 'document'])->name('execution-judgments.document');
    Route::get('/execution-judgments/{id}/document-readonly', [\App\Http\Controllers\DistrictExecutionJudgmentController::class, 'documentReadOnly'])->name('execution-judgments.document.readonly');
    Route::put('/execution-judgments/{id}',          [\App\Http\Controllers\DistrictExecutionJudgmentController::class, 'update'])  ->name('execution-judgments.update');
});

Route::middleware(['auth', 'permission:Execution Judgment Receipts,view'])->group(function () {
    Route::get('/execution-judgment-receipts',       [\App\Http\Controllers\DistrictExecutionJudgmentController::class, 'receiptsIndex'])->name('execution-judgments.receipts');
    Route::post('/execution-judgment-receipts/{judgment}/{party}', [\App\Http\Controllers\DistrictExecutionJudgmentController::class, 'confirmReceipt'])->name('execution-judgments.receipt.confirm');
});

// Stamp request document page — accessible by Kaaliye and Archive Officer
Route::middleware(['auth'])->group(function () {
    Route::get('/execution-judgments/{id}/stamp-request', [\App\Http\Controllers\DistrictExecutionJudgmentController::class, 'stampRequestDocument'])->name('execution-judgments.stamp-request');
});

// ── District Execution Return File ──────────────────────────────────────────────
Route::middleware(['auth', 'permission:Execution Return File,view'])->group(function () {
    Route::get('/execution-return-file',              [\App\Http\Controllers\DistrictExecutionReturnFileController::class, 'index'])  ->name('execution-return-file.index');
    Route::get('/execution-return-file/{id}/create',  [\App\Http\Controllers\DistrictExecutionReturnFileController::class, 'create']) ->name('execution-return-file.create');
    Route::post('/execution-return-file',             [\App\Http\Controllers\DistrictExecutionReturnFileController::class, 'store'])  ->name('execution-return-file.store');
    Route::get('/execution-return-file/{id}/document', [\App\Http\Controllers\DistrictExecutionReturnFileController::class, 'document'])->name('execution-return-file.document');
    Route::get('/execution-return-file/{id}/document-readonly', [\App\Http\Controllers\DistrictExecutionReturnFileController::class, 'documentReadOnly'])->name('execution-return-file.document.readonly');
    Route::get('/execution-return-file/{id}/document-pdf', [\App\Http\Controllers\DistrictExecutionReturnFileController::class, 'documentPdf'])->name('execution-return-file.document-pdf');
});

// ── District Execution Close Case ───────────────────────────────────────────────
Route::middleware(['auth', 'permission:Execution Close Case,view'])->group(function () {
    Route::get('/execution-close-case',              [\App\Http\Controllers\DistrictExecutionCloseCaseController::class, 'index'])   ->name('execution-close-case.index');
    Route::get('/execution-close-case/{id}/form',    [\App\Http\Controllers\DistrictExecutionCloseCaseController::class, 'form'])    ->name('execution-close-case.form');
    Route::post('/execution-close-case/store',       [\App\Http\Controllers\DistrictExecutionCloseCaseController::class, 'store'])   ->name('execution-close-case.store');
    Route::get('/execution-close-case/{id}/stamp-request', [\App\Http\Controllers\DistrictExecutionCloseCaseController::class, 'stampRequest']) ->name('execution-close-case.stamp-request');
    Route::post('/execution-close-case/{id}/close',        [\App\Http\Controllers\DistrictExecutionCloseCaseController::class, 'close'])        ->name('execution-close-case.close');
    Route::get('/execution-close-case/{id}/document', [\App\Http\Controllers\DistrictExecutionCloseCaseController::class, 'document'])->name('execution-close-case.document');
    Route::get('/execution-close-case/{id}/document-readonly', [\App\Http\Controllers\DistrictExecutionCloseCaseController::class, 'documentReadOnly'])->name('execution-close-case.document.readonly');
    Route::get('/execution-close-case/{id}/document-pdf', [\App\Http\Controllers\DistrictExecutionCloseCaseController::class, 'documentPdf'])->name('execution-close-case.document-pdf');
});

// ── District Criminal Judgments ────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Criminal Judgments,view'])->group(function () {
    Route::get('/criminal-judgments',               [\App\Http\Controllers\DistrictCriminalJudgmentController::class, 'index']) ->name('criminal-judgments.index');
    Route::get('/criminal-judgments/{caseId}/create', [\App\Http\Controllers\DistrictCriminalJudgmentController::class, 'create'])->name('criminal-judgments.create');
    Route::post('/criminal-judgments',              [\App\Http\Controllers\DistrictCriminalJudgmentController::class, 'store']) ->name('criminal-judgments.store');
    Route::get('/criminal-judgments/{id}/edit',     [\App\Http\Controllers\DistrictCriminalJudgmentController::class, 'edit'])    ->name('criminal-judgments.edit');
    Route::get('/criminal-judgments/{id}/document', [\App\Http\Controllers\DistrictCriminalJudgmentController::class, 'document'])->name('criminal-judgments.document');
    Route::get('/criminal-judgments/{id}/document-readonly', [\App\Http\Controllers\DistrictCriminalJudgmentController::class, 'documentReadOnly'])->name('criminal-judgments.document.readonly');
    Route::put('/criminal-judgments/{id}',          [\App\Http\Controllers\DistrictCriminalJudgmentController::class, 'update'])  ->name('criminal-judgments.update');
});

Route::middleware(['auth', 'permission:Criminal Judgment Receipts,view'])->group(function () {
    Route::get('/criminal-judgment-receipts',       [\App\Http\Controllers\DistrictCriminalJudgmentController::class, 'receiptsIndex'])->name('criminal-judgments.receipts');
    Route::post('/criminal-judgment-receipts/{judgment}/{party}', [\App\Http\Controllers\DistrictCriminalJudgmentController::class, 'confirmReceipt'])->name('criminal-judgments.receipt.confirm');
});

// Stamp request document page — accessible by Kaaliye and Archive Officer
Route::middleware(['auth'])->group(function () {
    Route::get('/criminal-judgments/{id}/stamp-request', [\App\Http\Controllers\DistrictCriminalJudgmentController::class, 'stampRequestDocument'])->name('criminal-judgments.stamp-request');
});

// ── District Criminal Return File ──────────────────────────────────────────────
Route::middleware(['auth', 'permission:Criminal Return File,view'])->group(function () {
    Route::get('/criminal-return-file',              [\App\Http\Controllers\DistrictCriminalReturnFileController::class, 'index'])  ->name('criminal-return-file.index');
    Route::get('/criminal-return-file/{id}/create',  [\App\Http\Controllers\DistrictCriminalReturnFileController::class, 'create']) ->name('criminal-return-file.create');
    Route::post('/criminal-return-file',             [\App\Http\Controllers\DistrictCriminalReturnFileController::class, 'store'])  ->name('criminal-return-file.store');
    Route::get('/criminal-return-file/{id}/document', [\App\Http\Controllers\DistrictCriminalReturnFileController::class, 'document'])->name('criminal-return-file.document');
    Route::get('/criminal-return-file/{id}/document-readonly', [\App\Http\Controllers\DistrictCriminalReturnFileController::class, 'documentReadOnly'])->name('criminal-return-file.document.readonly');
    Route::get('/criminal-return-file/{id}/document-pdf', [\App\Http\Controllers\DistrictCriminalReturnFileController::class, 'documentPdf'])->name('criminal-return-file.document-pdf');
});

// ── District Criminal Close Case ───────────────────────────────────────────────
Route::middleware(['auth', 'permission:Criminal Close Case,view'])->group(function () {
    Route::get('/criminal-close-case',              [\App\Http\Controllers\DistrictCriminalCloseCaseController::class, 'index'])   ->name('criminal-close-case.index');
    Route::get('/criminal-close-case/{id}/form',    [\App\Http\Controllers\DistrictCriminalCloseCaseController::class, 'form'])    ->name('criminal-close-case.form');
    Route::post('/criminal-close-case/store',       [\App\Http\Controllers\DistrictCriminalCloseCaseController::class, 'store'])   ->name('criminal-close-case.store');
    Route::get('/criminal-close-case/{id}/stamp-request', [\App\Http\Controllers\DistrictCriminalCloseCaseController::class, 'stampRequest']) ->name('criminal-close-case.stamp-request');
    Route::post('/criminal-close-case/{id}/close',        [\App\Http\Controllers\DistrictCriminalCloseCaseController::class, 'close'])        ->name('criminal-close-case.close');
    Route::get('/criminal-close-case/{id}/document', [\App\Http\Controllers\DistrictCriminalCloseCaseController::class, 'document'])->name('criminal-close-case.document');
    Route::get('/criminal-close-case/{id}/document-readonly', [\App\Http\Controllers\DistrictCriminalCloseCaseController::class, 'documentReadOnly'])->name('criminal-close-case.document.readonly');
    Route::get('/criminal-close-case/{id}/document-pdf', [\App\Http\Controllers\DistrictCriminalCloseCaseController::class, 'documentPdf'])->name('criminal-close-case.document-pdf');
});

// ── District Family Enforcement ──────────────────────────────────────────────
Route::middleware(['auth', 'permission:Family Enforcement,view'])->group(function () {
    Route::get('/family-enforcement',               [\App\Http\Controllers\DistrictFamilyEnforcementController::class, 'index'])   ->name('family-enforcement.index');
    Route::get('/family-enforcement/{id}/form',     [\App\Http\Controllers\DistrictFamilyEnforcementController::class, 'form'])    ->name('family-enforcement.form');
    Route::post('/family-enforcement/store',        [\App\Http\Controllers\DistrictFamilyEnforcementController::class, 'store'])   ->name('family-enforcement.store');
    Route::get('/family-enforcement/{id}/document', [\App\Http\Controllers\DistrictFamilyEnforcementController::class, 'document'])->name('family-enforcement.document');
});

// ── District Family Cases (escalate to higher court) + Transfer ─────────────
// Mirrors District Civil's "Appeal" group exactly: transfer.approve sits under
// the same coarse gate as the rest — the fine-grained "Transfer Approval" check
// (a role-capability, not a case-type module; the same human approves transfers
// regardless of case type) happens inside DistrictFamilyTransferController::approve().
Route::middleware(['auth', 'permission:Family Cases,view'])->group(function () {
    Route::get('/family-case-appeal',           [\App\Http\Controllers\DistrictFamilyAppealController::class, 'index'])->name('family-case-appeal.index');
    Route::get('/family-case-appeal/{id}/form', [\App\Http\Controllers\DistrictFamilyAppealController::class, 'form']) ->name('family-case-appeal.form');
    Route::post('/family-case-appeal/store',    [\App\Http\Controllers\DistrictFamilyAppealController::class, 'store'])->name('family-case-appeal.store');

    Route::get('/family-transfer',                     [\App\Http\Controllers\DistrictFamilyTransferController::class, 'index'])  ->name('family-transfer.index');
    Route::get('/family-transfer/{id}/form',           [\App\Http\Controllers\DistrictFamilyTransferController::class, 'form'])   ->name('family-transfer.form');
    Route::post('/family-transfer/store',              [\App\Http\Controllers\DistrictFamilyTransferController::class, 'store'])  ->name('family-transfer.store');
    Route::post('/family-transfer/{transfer}/approve', [\App\Http\Controllers\DistrictFamilyTransferController::class, 'approve'])->name('family-transfer.approve');
});

// ── District Execution Enforcement ──────────────────────────────────────────────
Route::middleware(['auth', 'permission:Execution Enforcement,view'])->group(function () {
    Route::get('/execution-enforcement',               [\App\Http\Controllers\DistrictExecutionEnforcementController::class, 'index'])   ->name('execution-enforcement.index');
    Route::get('/execution-enforcement/{id}/form',     [\App\Http\Controllers\DistrictExecutionEnforcementController::class, 'form'])    ->name('execution-enforcement.form');
    Route::post('/execution-enforcement/store',        [\App\Http\Controllers\DistrictExecutionEnforcementController::class, 'store'])   ->name('execution-enforcement.store');
    Route::get('/execution-enforcement/{id}/document', [\App\Http\Controllers\DistrictExecutionEnforcementController::class, 'document'])->name('execution-enforcement.document');
});

// ── District Execution Cases (escalate to higher court) + Transfer ─────────────
// Mirrors District Family's "Family Cases" group exactly: transfer.approve sits
// under the same coarse gate as the rest — the fine-grained "Transfer Approval"
// check (a role-capability, not a case-type module; the same human approves
// transfers regardless of case type) happens inside DistrictExecutionTransferController::approve().
Route::middleware(['auth', 'permission:Execution Cases,view'])->group(function () {
    Route::get('/execution-case-appeal',           [\App\Http\Controllers\DistrictExecutionAppealController::class, 'index'])->name('execution-case-appeal.index');
    Route::get('/execution-case-appeal/{id}/form', [\App\Http\Controllers\DistrictExecutionAppealController::class, 'form']) ->name('execution-case-appeal.form');
    Route::post('/execution-case-appeal/store',    [\App\Http\Controllers\DistrictExecutionAppealController::class, 'store'])->name('execution-case-appeal.store');

    Route::get('/execution-transfer',                     [\App\Http\Controllers\DistrictExecutionTransferController::class, 'index'])  ->name('execution-transfer.index');
    Route::get('/execution-transfer/{id}/form',           [\App\Http\Controllers\DistrictExecutionTransferController::class, 'form'])   ->name('execution-transfer.form');
    Route::post('/execution-transfer/store',              [\App\Http\Controllers\DistrictExecutionTransferController::class, 'store'])  ->name('execution-transfer.store');
    Route::post('/execution-transfer/{transfer}/approve', [\App\Http\Controllers\DistrictExecutionTransferController::class, 'approve'])->name('execution-transfer.approve');
});

// ── District Criminal Enforcement ──────────────────────────────────────────────
Route::middleware(['auth', 'permission:Criminal Enforcement,view'])->group(function () {
    Route::get('/criminal-enforcement',               [\App\Http\Controllers\DistrictCriminalEnforcementController::class, 'index'])   ->name('criminal-enforcement.index');
    Route::get('/criminal-enforcement/{id}/form',     [\App\Http\Controllers\DistrictCriminalEnforcementController::class, 'form'])    ->name('criminal-enforcement.form');
    Route::post('/criminal-enforcement/store',        [\App\Http\Controllers\DistrictCriminalEnforcementController::class, 'store'])   ->name('criminal-enforcement.store');
    Route::get('/criminal-enforcement/{id}/document', [\App\Http\Controllers\DistrictCriminalEnforcementController::class, 'document'])->name('criminal-enforcement.document');
});

// ── District Criminal Cases (escalate to higher court) + Transfer ─────────────
// Mirrors District Execution's "Execution Cases" group exactly: transfer.approve sits
// under the same coarse gate as the rest — the fine-grained "Transfer Approval"
// check (a role-capability, not a case-type module; the same human approves
// transfers regardless of case type) happens inside DistrictCriminalTransferController::approve().
Route::middleware(['auth', 'permission:Criminal Cases,view'])->group(function () {
    Route::get('/criminal-case-appeal',           [\App\Http\Controllers\DistrictCriminalAppealController::class, 'index'])->name('criminal-case-appeal.index');
    Route::get('/criminal-case-appeal/{id}/form', [\App\Http\Controllers\DistrictCriminalAppealController::class, 'form']) ->name('criminal-case-appeal.form');
    Route::post('/criminal-case-appeal/store',    [\App\Http\Controllers\DistrictCriminalAppealController::class, 'store'])->name('criminal-case-appeal.store');

    Route::get('/criminal-transfer',                     [\App\Http\Controllers\DistrictCriminalTransferController::class, 'index'])  ->name('criminal-transfer.index');
    Route::get('/criminal-transfer/{id}/form',           [\App\Http\Controllers\DistrictCriminalTransferController::class, 'form'])   ->name('criminal-transfer.form');
    Route::post('/criminal-transfer/store',              [\App\Http\Controllers\DistrictCriminalTransferController::class, 'store'])  ->name('criminal-transfer.store');
    Route::post('/criminal-transfer/{transfer}/approve', [\App\Http\Controllers\DistrictCriminalTransferController::class, 'approve'])->name('criminal-transfer.approve');
});

// ── District Family Payments (Foomka Codsiga Lacag Bixinta) ─────────────────
// Case-scoped only — reuses payment_receipt.blade.php and FinanceController unchanged.
Route::middleware(['auth', 'permission:Family Case Registration|Finance,view'])->group(function () {
    Route::get('family-registration/{id}/payment-request', [\App\Http\Controllers\DistrictFamilyRegistrationController::class, 'paymentRequestForm'])->name('family-registration.payment-request');
    Route::post('family-registration/payment-request', [\App\Http\Controllers\DistrictFamilyRegistrationController::class, 'storePaymentRequest'])->name('family-registration.payment-request.store');
    Route::get('family-registration-payments/{id}/receipt', [\App\Http\Controllers\DistrictFamilyRegistrationController::class, 'familyPaymentReceipt'])->name('family-registration.payments.receipt');
    Route::get('family-registration-payments/{id}/receipt-pdf', [\App\Http\Controllers\DistrictFamilyRegistrationController::class, 'familyPaymentReceiptPdf'])->name('family-registration.payments.receipt-pdf');
});

// ── District Execution Payments (Foomka Codsiga Lacag Bixinta) ─────────────────
// Case-scoped only — reuses payment_receipt.blade.php and FinanceController unchanged.
Route::middleware(['auth', 'permission:Execution Case Registration|Finance,view'])->group(function () {
    Route::get('execution-registration/{id}/payment-request', [\App\Http\Controllers\DistrictExecutionRegistrationController::class, 'paymentRequestForm'])->name('execution-registration.payment-request');
    Route::post('execution-registration/payment-request', [\App\Http\Controllers\DistrictExecutionRegistrationController::class, 'storePaymentRequest'])->name('execution-registration.payment-request.store');
    Route::get('execution-registration-payments/{id}/receipt', [\App\Http\Controllers\DistrictExecutionRegistrationController::class, 'executionPaymentReceipt'])->name('execution-registration.payments.receipt');
    Route::get('execution-registration-payments/{id}/receipt-pdf', [\App\Http\Controllers\DistrictExecutionRegistrationController::class, 'executionPaymentReceiptPdf'])->name('execution-registration.payments.receipt-pdf');
});

// ── District Criminal Payments (Foomka Codsiga Lacag Bixinta) ─────────────────
// Case-scoped only — reuses payment_receipt.blade.php and FinanceController unchanged.
Route::middleware(['auth', 'permission:Criminal Case Registration|Finance,view'])->group(function () {
    Route::get('criminal-registration/{id}/payment-request', [\App\Http\Controllers\DistrictCriminalRegistrationController::class, 'paymentRequestForm'])->name('criminal-registration.payment-request');
    Route::post('criminal-registration/payment-request', [\App\Http\Controllers\DistrictCriminalRegistrationController::class, 'storePaymentRequest'])->name('criminal-registration.payment-request.store');
    Route::get('criminal-registration-payments/{id}/receipt', [\App\Http\Controllers\DistrictCriminalRegistrationController::class, 'criminalPaymentReceipt'])->name('criminal-registration.payments.receipt');
    Route::get('criminal-registration-payments/{id}/receipt-pdf', [\App\Http\Controllers\DistrictCriminalRegistrationController::class, 'criminalPaymentReceiptPdf'])->name('criminal-registration.payments.receipt-pdf');
});

// ══════════════════════════════════════════════════════════════════════════════

// ── District Execution Case Registration (+ Parties, Documents, Lawyers sub-resources) ──
Route::middleware(['auth', 'permission:Execution Case Registration,view'])->group(function () {
    Route::get('execution-registration/next-fileno/{courtcode}', [\App\Http\Controllers\DistrictExecutionRegistrationController::class, 'nextFileNo']);
    Route::get('execution-case-tracking', [\App\Http\Controllers\DistrictExecutionRegistrationController::class, 'tracking'])->name('execution-case-tracking.index');
    Route::get('execution-registration/{id}/supporting', [\App\Http\Controllers\DistrictExecutionRegistrationController::class, 'supporting'])->name('execution-registration.supporting');
    Route::resource('execution-registration', \App\Http\Controllers\DistrictExecutionRegistrationController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

// Shown from either "Execution Case Registration" or "Execution Hearings" (case detail page
// is also reachable from the hearings flow), so accept either permission here rather
// than only the latter — otherwise that link 403s for anyone who only has Execution
// Hearings access.
Route::middleware(['auth', 'permission:Execution Case Registration|Execution Hearings,view'])->group(function () {
    Route::get('execution-registration/{execution_registration}', [\App\Http\Controllers\DistrictExecutionRegistrationController::class, 'show'])->name('execution-registration.show');
});

// ── District Execution Case Handover ────────────────────────────────────────────
Route::middleware(['auth', 'permission:Execution Case Handover,view'])->group(function () {
    Route::get('execution-registration-handover', [\App\Http\Controllers\DistrictExecutionRegistrationController::class, 'handover'])->name('execution-registration.handover');
});

// ══════════════════════════════════════════════════════════════════════════════

// ── District Criminal Case Registration (+ Parties, Documents, Lawyers sub-resources) ──
Route::middleware(['auth', 'permission:Criminal Case Registration,view'])->group(function () {
    Route::get('criminal-registration/next-fileno/{courtcode}', [\App\Http\Controllers\DistrictCriminalRegistrationController::class, 'nextFileNo']);
    Route::get('criminal-case-tracking', [\App\Http\Controllers\DistrictCriminalRegistrationController::class, 'tracking'])->name('criminal-case-tracking.index');
    Route::get('criminal-registration/{id}/supporting', [\App\Http\Controllers\DistrictCriminalRegistrationController::class, 'supporting'])->name('criminal-registration.supporting');
    Route::resource('criminal-registration', \App\Http\Controllers\DistrictCriminalRegistrationController::class)->only(['index', 'store', 'update', 'destroy']);
});

// Shown from either "Criminal Case Registration" or "Criminal Hearings" (case detail page
// is also reachable from the hearings flow), so accept either permission here rather
// than only the latter — otherwise that link 403s for anyone who only has Criminal
// Hearings access.
Route::middleware(['auth', 'permission:Criminal Case Registration|Criminal Hearings,view'])->group(function () {
    Route::get('criminal-registration/{criminal_registration}', [\App\Http\Controllers\DistrictCriminalRegistrationController::class, 'show'])->name('criminal-registration.show');
});

// ── District Criminal Case Handover ────────────────────────────────────────────
Route::middleware(['auth', 'permission:Criminal Case Handover,view'])->group(function () {
    Route::get('criminal-registration-handover', [\App\Http\Controllers\DistrictCriminalRegistrationController::class, 'handover'])->name('criminal-registration.handover');
});

// ══════════════════════════════════════════════════════════════════════════════

// ── Courts Integration ────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Courts Integration,view'])->group(function () {
    Route::get('/courts-integration/transfer', [\App\Http\Controllers\CourtsIntegrationController::class, 'transfer'])->name('courtsintergration.transfer');
    Route::get('/courts-integration/received', [\App\Http\Controllers\CourtsIntegrationController::class, 'received'])->name('courtsintergration.recived');
});

// ── Lawyer Registry ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Lawyer Registry,view'])->group(function () {
    Route::get('/lawyer/export', [LawyerController::class, 'export'])->name('lawyer.export');
    Route::post('/lawyer/import', [LawyerController::class, 'import'])->name('lawyer.import');
    Route::resource('lawyer', LawyerController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
});

// ── Backup & Restore ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Backup & Restore,view'])->prefix('backup')->name('backup.')->group(function () {
    Route::get('/',               [BackupRestoreController::class, 'index'])           ->name('index');
    Route::post('/create',        [BackupRestoreController::class, 'create'])          ->name('create');
    Route::get('/download/{id}',  [BackupRestoreController::class, 'download'])        ->name('download');
    Route::post('/restore/{id}',  [BackupRestoreController::class, 'restore'])         ->name('restore');
    Route::delete('/delete/{id}', [BackupRestoreController::class, 'delete'])          ->name('delete');
    Route::post('/settings',      [BackupRestoreController::class, 'updateSettings'])  ->name('settings.update');
    Route::post('/scheduled',     [BackupRestoreController::class, 'scheduledTrigger'])->name('scheduled.trigger');
    Route::post('/gmail',         [BackupRestoreController::class, 'gmail'])           ->name('gmail');
});

// ── Lock Screen ───────────────────────────────────────────────────────────────
Route::get('/lock-screen', [LockScreenController::class, 'show'])->name('lock-screen.show');
Route::post('/lock-screen/unlock', [LockScreenController::class, 'unlock'])->name('lock-screen.unlock');
Route::middleware('auth')->post('/lock-screen/lock', [LockScreenController::class, 'lock'])->name('lock-screen.lock');

// ── Document Signatures ───────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/document-sign/{type}/{id}', [\App\Http\Controllers\DocumentSignatureController::class, 'sign'])->name('document.sign');
});

// ── Notifications ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/notifications',              [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/all',          [\App\Http\Controllers\NotificationController::class, 'page'])->name('notifications.page');
    Route::post('/notifications/{id}/read',   [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all',    [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

// ── Support Tickets (Help Desk) ─────────────────────────────────────────────────
// Open to all authenticated users; Super Admin (no group assigned) sees every ticket and can manage status/assignment.
Route::middleware('auth')->group(function () {
    Route::get('/support-tickets',             [\App\Http\Controllers\SupportTicketController::class, 'index'])->name('support-tickets.index');
    Route::get('/support-tickets/create',       [\App\Http\Controllers\SupportTicketController::class, 'create'])->name('support-tickets.create');
    Route::post('/support-tickets',             [\App\Http\Controllers\SupportTicketController::class, 'store'])->name('support-tickets.store');
    Route::get('/support-tickets/{id}',         [\App\Http\Controllers\SupportTicketController::class, 'show'])->name('support-tickets.show');
    Route::post('/support-tickets/{id}/reply',  [\App\Http\Controllers\SupportTicketController::class, 'reply'])->name('support-tickets.reply');
    Route::post('/support-tickets/{id}/status', [\App\Http\Controllers\SupportTicketController::class, 'updateStatus'])->name('support-tickets.update-status');
    Route::get('/support-tickets/attachments/{id}/download', [\App\Http\Controllers\SupportTicketController::class, 'downloadAttachment'])->name('support-tickets.attachments.download');
});

// ── Profile ───────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/system-preferences', [ProfileController::class, 'updateSystemPreferences'])->name('profile.system-preferences.update');
    Route::post('/profile/appearance', [ProfileController::class, 'updateAppearance'])->name('profile.appearance.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/security/two-factor', [\App\Http\Controllers\TwoFactorSettingsController::class, 'show'])->name('two-factor.show');
    Route::post('/security/two-factor/confirm', [\App\Http\Controllers\TwoFactorSettingsController::class, 'confirm'])->name('two-factor.confirm');
    Route::delete('/security/two-factor', [\App\Http\Controllers\TwoFactorSettingsController::class, 'destroy'])->name('two-factor.destroy');
});

// ══════════════════════════════════════════════════════════════════════════════
// Attorney General Case Management System (AGCMS)
// ══════════════════════════════════════════════════════════════════════════════

// ── AGCMS Dashboard ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Attorney Dashboard,view'])->group(function () {
    Route::get('attorney-dashboard', [\App\Http\Controllers\AttorneyDashboardController::class, 'index'])->name('attorney-dashboard.index');
});

// ── AGCMS Case Registration ─────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Attorney Case Registration,view'])->group(function () {
    Route::get('attorney-cases', [\App\Http\Controllers\AttorneyCaseController::class, 'index'])->name('attorney-cases.index');
    Route::get('attorney-cases/create', [\App\Http\Controllers\AttorneyCaseController::class, 'create'])->name('attorney-cases.create');
    Route::post('attorney-cases', [\App\Http\Controllers\AttorneyCaseController::class, 'store'])->name('attorney-cases.store');
    Route::post('attorney-cases/relationship-types', [\App\Http\Controllers\AttorneyCaseController::class, 'storeRelationshipType'])->name('attorney-cases.relationship-types.store');
    Route::post('attorney-cases/source-types', [\App\Http\Controllers\AttorneyCaseController::class, 'storeSourceType'])->name('attorney-cases.source-types.store');
    Route::get('attorney-cases/tracking', [\App\Http\Controllers\AttorneyCaseController::class, 'tracking'])->name('attorney-cases.tracking');
    Route::get('attorney-cases/calendar', [\App\Http\Controllers\AttorneyCaseController::class, 'calendar'])->name('attorney-cases.calendar');
    Route::get('attorney-cases/assign', [\App\Http\Controllers\AttorneyCaseController::class, 'assign'])->name('attorney-cases.assign');
    Route::get('attorney-cases/socodsinta', [\App\Http\Controllers\AttorneyCaseController::class, 'socodsinta'])->name('attorney-cases.socodsinta');
    Route::get('attorney-cases/{id}', [\App\Http\Controllers\AttorneyCaseController::class, 'show'])->name('attorney-cases.show');
    Route::get('attorney-cases/{id}/edit', [\App\Http\Controllers\AttorneyCaseController::class, 'edit'])->name('attorney-cases.edit');
    Route::put('attorney-cases/{id}', [\App\Http\Controllers\AttorneyCaseController::class, 'update'])->name('attorney-cases.update');
    Route::delete('attorney-cases/{id}', [\App\Http\Controllers\AttorneyCaseController::class, 'destroy'])->name('attorney-cases.destroy');
    Route::post('attorney-cases/{id}/parties', [\App\Http\Controllers\AttorneyCaseController::class, 'addParty'])->name('attorney-cases.parties.store');
    Route::delete('attorney-cases/{id}/parties/{partyId}', [\App\Http\Controllers\AttorneyCaseController::class, 'removeParty'])->name('attorney-cases.parties.destroy');
    Route::post('attorney-cases/{id}/documents', [\App\Http\Controllers\AttorneyCaseController::class, 'addDocument'])->name('attorney-cases.documents.store');
    Route::delete('attorney-cases/{id}/documents/{documentId}', [\App\Http\Controllers\AttorneyCaseController::class, 'removeDocument'])->name('attorney-cases.documents.destroy');

    Route::get('attorney-cases/{id}/workflow', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'show'])->name('attorney-cases.workflow');
    Route::get('attorney-cases/{id}/workflow/investigation-decision', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'investigationDecision'])->name('attorney-cases.workflow.investigation-decision');
    Route::get('attorney-cases/{id}/workflow/investigation-decision/form', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'investigationDecisionForm'])->name('attorney-cases.workflow.investigation-decision.form');
    Route::post('attorney-cases/{id}/workflow/investigation-decision', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'storeInvestigationDecision'])->name('attorney-cases.workflow.investigation-decision.store');
    Route::get('attorney-cases/{id}/workflow/investigation', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'investigation'])->name('attorney-cases.workflow.investigation');
    Route::get('attorney-cases/{id}/workflow/investigation/form', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'investigationForm'])->name('attorney-cases.workflow.investigation.form');
    Route::post('attorney-cases/{id}/workflow/investigation', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'storeInvestigation'])->name('attorney-cases.workflow.investigation.store');
    Route::post('attorney-cases/{id}/workflow/investigation/updates', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'storeInvestigationUpdate'])->name('attorney-cases.workflow.investigation.updates.store');
    Route::post('attorney-cases/{id}/workflow/send-to-court', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'sendToCourt'])->name('attorney-cases.workflow.send-to-court');

    Route::get('attorney-cases/{id}/workflow/arrest-decision', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'show'])->name('attorney-cases.workflow.arrest-decision');
    Route::post('attorney-cases/{id}/workflow/arrest-decision/approve', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'approve'])->name('attorney-cases.workflow.arrest-decision.approve');

    Route::get('attorney-cases/{id}/workflow/arrest-decision/arrest-decision/form', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'arrestDecisionForm'])->name('attorney-cases.workflow.arrest-decision.arrest-decision.form');
    Route::post('attorney-cases/{id}/workflow/arrest-decision/arrest-decision', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'storeArrestDecision'])->name('attorney-cases.workflow.arrest-decision.arrest-decision.store');

    Route::get('attorney-cases/{id}/workflow/arrest-decision/arrest-without-warrant/form', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'arrestWithoutWarrantForm'])->name('attorney-cases.workflow.arrest-decision.arrest-without-warrant.form');
    Route::post('attorney-cases/{id}/workflow/arrest-decision/arrest-without-warrant', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'storeArrestWithoutWarrant'])->name('attorney-cases.workflow.arrest-decision.arrest-without-warrant.store');

    Route::get('attorney-cases/{id}/workflow/arrest-decision/warrant-of-arrest/form', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'warrantOfArrestForm'])->name('attorney-cases.workflow.arrest-decision.warrant-of-arrest.form');
    Route::post('attorney-cases/{id}/workflow/arrest-decision/warrant-of-arrest', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'storeWarrantOfArrest'])->name('attorney-cases.workflow.arrest-decision.warrant-of-arrest.store');

    Route::get('attorney-cases/{id}/workflow/arrest-decision/search-and-seizure/form', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'searchAndSeizureForm'])->name('attorney-cases.workflow.arrest-decision.search-and-seizure.form');
    Route::post('attorney-cases/{id}/workflow/arrest-decision/search-and-seizure', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'storeSearchAndSeizure'])->name('attorney-cases.workflow.arrest-decision.search-and-seizure.store');

    Route::get('attorney-cases/{id}/workflow/arrest-decision/asset-recovery/form', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'assetRecoveryForm'])->name('attorney-cases.workflow.arrest-decision.asset-recovery.form');
    Route::post('attorney-cases/{id}/workflow/arrest-decision/asset-recovery', [\App\Http\Controllers\AttorneyArrestDecisionController::class, 'storeAssetRecovery'])->name('attorney-cases.workflow.arrest-decision.asset-recovery.store');

    Route::get('attorney-cases/{id}/workflow/evidence-interviews', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'show'])->name('attorney-cases.workflow.evidence-interviews');

    Route::get('attorney-cases/{id}/workflow/evidence-interviews/suspect-interviews/form', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'suspectInterviewsForm'])->name('attorney-cases.workflow.evidence-interviews.suspect-interviews.form');
    Route::post('attorney-cases/{id}/workflow/evidence-interviews/suspect-interviews', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'storeSuspectInterviews'])->name('attorney-cases.workflow.evidence-interviews.suspect-interviews.store');

    Route::get('attorney-cases/{id}/workflow/evidence-interviews/witness-interviews/form', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'witnessInterviewsForm'])->name('attorney-cases.workflow.evidence-interviews.witness-interviews.form');
    Route::post('attorney-cases/{id}/workflow/evidence-interviews/witness-interviews', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'storeWitnessInterviews'])->name('attorney-cases.workflow.evidence-interviews.witness-interviews.store');

    Route::get('attorney-cases/{id}/workflow/evidence-interviews/expert-interviews/form', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'expertInterviewsForm'])->name('attorney-cases.workflow.evidence-interviews.expert-interviews.form');
    Route::post('attorney-cases/{id}/workflow/evidence-interviews/expert-interviews', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'storeExpertInterviews'])->name('attorney-cases.workflow.evidence-interviews.expert-interviews.store');

    Route::get('attorney-cases/{id}/workflow/evidence-interviews/victim-interviews/form', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'victimInterviewsForm'])->name('attorney-cases.workflow.evidence-interviews.victim-interviews.form');
    Route::post('attorney-cases/{id}/workflow/evidence-interviews/victim-interviews', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'storeVictimInterviews'])->name('attorney-cases.workflow.evidence-interviews.victim-interviews.store');

    Route::get('attorney-cases/{id}/workflow/evidence-interviews/evidence-management/form', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'evidenceManagementForm'])->name('attorney-cases.workflow.evidence-interviews.evidence-management.form');
    Route::post('attorney-cases/{id}/workflow/evidence-interviews/evidence-management', [\App\Http\Controllers\AttorneyEvidenceInterviewsController::class, 'storeEvidenceManagement'])->name('attorney-cases.workflow.evidence-interviews.evidence-management.store');

    Route::get('attorney-cases/{id}/workflow/investigation-extension', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'investigationExtension'])->name('attorney-cases.workflow.investigation-extension');
    Route::get('attorney-cases/{id}/workflow/investigation-extension/form', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'investigationExtensionForm'])->name('attorney-cases.workflow.investigation-extension.form');
    Route::post('attorney-cases/{id}/workflow/investigation-extension', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'storeInvestigationExtension'])->name('attorney-cases.workflow.investigation-extension.store');
    Route::post('attorney-cases/{id}/workflow/investigation-extension/approve', [\App\Http\Controllers\AttorneyCaseWorkflowController::class, 'approveInvestigationExtension'])->name('attorney-cases.workflow.investigation-extension.approve');

    Route::get('attorney-cases/{id}/compliance/{type}', [\App\Http\Controllers\AttorneyComplianceFormController::class, 'create'])->name('attorney-cases.compliance.create');
    Route::post('attorney-cases/{id}/compliance/{type}', [\App\Http\Controllers\AttorneyComplianceFormController::class, 'store'])->name('attorney-cases.compliance.store');
    Route::get('attorney-cases/compliance/{record}/letter', [\App\Http\Controllers\AttorneyComplianceFormController::class, 'letter'])->name('attorney-cases.compliance.letter');
    Route::get('attorney-cases/compliance/{record}/letter-pdf', [\App\Http\Controllers\AttorneyComplianceFormController::class, 'letterPdf'])->name('attorney-cases.compliance.letter-pdf');
});

// ── AGCMS Case Reviews (Hubinta Cabashada) ──────────────────────────────────────
Route::middleware(['auth', 'permission:Attorney Case Reviews,view'])->group(function () {
    Route::get('attorney-case-reviews', [\App\Http\Controllers\AttorneyCaseReviewController::class, 'index'])->name('attorney-case-reviews.index');
    Route::get('attorney-case-reviews/{id}', [\App\Http\Controllers\AttorneyCaseReviewController::class, 'show'])->name('attorney-case-reviews.show');
    Route::post('attorney-case-reviews', [\App\Http\Controllers\AttorneyCaseReviewController::class, 'store'])->name('attorney-case-reviews.store');
    Route::put('attorney-case-reviews/{id}', [\App\Http\Controllers\AttorneyCaseReviewController::class, 'update'])->name('attorney-case-reviews.update');
    Route::delete('attorney-case-reviews/{id}', [\App\Http\Controllers\AttorneyCaseReviewController::class, 'destroy'])->name('attorney-case-reviews.destroy');
});

// ── AGCMS Prosecutor Assignments (Xilsaarista Xeer Ilaaliyaha) ──────────────────
Route::middleware(['auth', 'permission:Attorney Prosecutor Assignments,view'])->group(function () {
    Route::get('attorney-prosecutor-assignments', [\App\Http\Controllers\AttorneyProsecutorAssignmentController::class, 'index'])->name('attorney-prosecutor-assignments.index');
    Route::get('attorney-prosecutor-assignments/{id}/add', [\App\Http\Controllers\AttorneyProsecutorAssignmentController::class, 'add'])->name('attorney-prosecutor-assignments.add');
    Route::post('attorney-prosecutor-assignments', [\App\Http\Controllers\AttorneyProsecutorAssignmentController::class, 'store'])->name('attorney-prosecutor-assignments.store');
    Route::put('attorney-prosecutor-assignments/{id}', [\App\Http\Controllers\AttorneyProsecutorAssignmentController::class, 'update'])->name('attorney-prosecutor-assignments.update');
    Route::delete('attorney-prosecutor-assignments/{id}', [\App\Http\Controllers\AttorneyProsecutorAssignmentController::class, 'destroy'])->name('attorney-prosecutor-assignments.destroy');
});

// ── Attorney Departments ────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:Attorney Departments,view'])->group(function () {
    Route::resource('attorney-departments', \App\Http\Controllers\AttorneyDepartmentController::class)->only(['index', 'store', 'update', 'destroy']);
});

// ══════════════════════════════════════════════════════════════════════════════
// Criminal Investigation Department (CID)
// ══════════════════════════════════════════════════════════════════════════════

// ── CID Dashboard ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:CID Dashboard,view'])->group(function () {
    Route::get('cid-dashboard', [\App\Http\Controllers\CidDashboardController::class, 'index'])->name('cid-dashboard.index');
});

// ── CID Investigation Workflow ───────────────────────────────────────────────
Route::middleware(['auth', 'permission:CID Investigation Workflow,view'])->group(function () {
    Route::get('criminal-cases', [\App\Http\Controllers\CriminalCaseController::class, 'index'])->name('criminal-cases.index');
    Route::post('criminal-cases', [\App\Http\Controllers\CriminalCaseController::class, 'store'])->name('criminal-cases.store');

    Route::get('criminal-cases/{id}/workflow', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'show'])->name('criminal-cases.workflow');
    Route::get('criminal-cases/{id}/workflow/arrest', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'arrestForm'])->name('criminal-cases.workflow.arrest.form');
    Route::post('criminal-cases/{id}/workflow/arrest', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'storeArrest'])->name('criminal-cases.workflow.arrest.store');

    Route::get('criminal-cases/{id}/workflow/occurrence-book', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'obForm'])->name('criminal-cases.workflow.ob.form');
    Route::post('criminal-cases/{id}/workflow/occurrence-book', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'storeOb'])->name('criminal-cases.workflow.ob.store');
    Route::post('criminal-cases/{id}/workflow/occurrence-book/acknowledge', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'acknowledgeOb'])->name('criminal-cases.workflow.ob.acknowledge');

    Route::get('criminal-cases/{id}/workflow/assignment', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'assignmentForm'])->name('criminal-cases.workflow.assignment.form');
    Route::post('criminal-cases/{id}/workflow/assignment', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'storeAssignment'])->name('criminal-cases.workflow.assignment.store');

    Route::get('criminal-cases/{id}/workflow/evidence', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'evidenceIndex'])->name('criminal-cases.workflow.evidence.index');
    Route::post('criminal-cases/{id}/workflow/evidence', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'storeEvidenceItem'])->name('criminal-cases.workflow.evidence.store');
    Route::post('criminal-cases/{id}/workflow/evidence/{itemId}/status', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'updateEvidenceStatus'])->name('criminal-cases.workflow.evidence.status');
    Route::post('criminal-cases/{id}/workflow/evidence/{itemId}/custody', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'transferEvidenceCustody'])->name('criminal-cases.workflow.evidence.custody');

    Route::get('criminal-cases/{id}/workflow/custody', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'custodyForm'])->name('criminal-cases.workflow.custody.form');
    Route::post('criminal-cases/{id}/workflow/custody', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'storeCustody'])->name('criminal-cases.workflow.custody.store');
    Route::post('criminal-cases/{id}/workflow/court-appearances', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'storeCourtAppearance'])->name('criminal-cases.workflow.court-appearances.store');
    Route::post('criminal-cases/{id}/workflow/court-appearances/{appearanceId}/outcome', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'recordCourtOutcome'])->name('criminal-cases.workflow.court-appearances.outcome');

    Route::get('criminal-cases/{id}/workflow/report', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'reportForm'])->name('criminal-cases.workflow.report.form');
    Route::post('criminal-cases/{id}/workflow/report', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'storeReport'])->name('criminal-cases.workflow.report.store');
    Route::post('criminal-cases/{id}/workflow/report/endorse', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'endorseReport'])->name('criminal-cases.workflow.report.endorse');
    Route::post('criminal-cases/{id}/workflow/report/submit-to-ago', [\App\Http\Controllers\CriminalCaseWorkflowController::class, 'submitToAgo'])->name('criminal-cases.workflow.report.submit-to-ago');
});

// ── CID Case Management ──────────────────────────────────────────────────────
// Diary and Takeovers are reached from within a case (workflow view) as well as
// managed as their own registries, so either permission grants access.
Route::middleware(['auth', 'permission:CID Investigation Workflow|CID Case Management,view'])->group(function () {
    Route::get('criminal-cases/export', [\App\Http\Controllers\CriminalCaseController::class, 'export'])->name('criminal-cases.export');
    Route::post('criminal-cases/bulk-reassign', [\App\Http\Controllers\CriminalCaseController::class, 'bulkReassign'])->name('criminal-cases.bulk-reassign');
    Route::post('criminal-cases/bulk-close', [\App\Http\Controllers\CriminalCaseController::class, 'bulkClose'])->name('criminal-cases.bulk-close');
    Route::get('criminal-cases/{id}/diary', [\App\Http\Controllers\CriminalCaseController::class, 'diaryIndex'])->name('criminal-cases.diary');
    Route::post('criminal-cases/{id}/diary', [\App\Http\Controllers\CriminalCaseController::class, 'storeDiaryEntry'])->name('criminal-cases.diary.store');

    Route::get('criminal-cases/{id}/takeovers', [\App\Http\Controllers\CriminalCaseTakeoverController::class, 'index'])->name('criminal-cases.takeovers');
    Route::post('criminal-cases/{id}/takeovers', [\App\Http\Controllers\CriminalCaseTakeoverController::class, 'store'])->name('criminal-cases.takeovers.store');
    Route::post('criminal-cases/{id}/takeovers/{takeoverId}/acknowledge-outgoing', [\App\Http\Controllers\CriminalCaseTakeoverController::class, 'acknowledgeOutgoing'])->name('criminal-cases.takeovers.acknowledge-outgoing');
    Route::post('criminal-cases/{id}/takeovers/{takeoverId}/accept-incoming', [\App\Http\Controllers\CriminalCaseTakeoverController::class, 'acceptIncoming'])->name('criminal-cases.takeovers.accept-incoming');
    Route::post('criminal-cases/{id}/takeovers/{takeoverId}/approve', [\App\Http\Controllers\CriminalCaseTakeoverController::class, 'approve'])->name('criminal-cases.takeovers.approve');
    Route::post('criminal-cases/{id}/takeovers/{takeoverId}/reject', [\App\Http\Controllers\CriminalCaseTakeoverController::class, 'reject'])->name('criminal-cases.takeovers.reject');
});

Route::middleware(['auth', 'permission:CID Case Management,view'])->group(function () {
    Route::get('cid-occurrence-books', [\App\Http\Controllers\CriminalObController::class, 'index'])->name('cid-occurrence-books.index');
    Route::get('cid-internal-ob', [\App\Http\Controllers\CriminalObController::class, 'internal'])->name('cid-internal-ob.index');
    Route::get('cid-ob-archive', [\App\Http\Controllers\CriminalObController::class, 'archive'])->name('cid-ob-archive.index');
    Route::get('cid-court-calendar', [\App\Http\Controllers\CriminalCourtCalendarController::class, 'index'])->name('cid-court-calendar.index');
    Route::get('cid-period-alerts', [\App\Http\Controllers\CriminalPeriodAlertsController::class, 'index'])->name('cid-period-alerts.index');
});

// ── CID Settings ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:CID Settings,view'])->group(function () {
    Route::get('cid-number-formats', [\App\Http\Controllers\CriminalNumberFormatController::class, 'index'])->name('cid-number-formats.index');
    Route::post('cid-number-formats/{key}', [\App\Http\Controllers\CriminalNumberFormatController::class, 'update'])->name('cid-number-formats.update');
});

// ── CID Evidence & Documentation ─────────────────────────────────────────────
Route::middleware(['auth', 'permission:CID Evidence & Documentation,view'])->group(function () {
    Route::get('cid-evidence-registry', [\App\Http\Controllers\CriminalEvidenceController::class, 'index'])->name('cid-evidence-registry.index');

    Route::get('criminal-cases/{id}/biometrics', [\App\Http\Controllers\CriminalBiometricController::class, 'index'])->name('criminal-cases.biometrics.index');
    Route::post('criminal-cases/{id}/biometrics', [\App\Http\Controllers\CriminalBiometricController::class, 'store'])->name('criminal-cases.biometrics.store');
    Route::post('criminal-cases/{id}/biometrics/{biometricId}/match', [\App\Http\Controllers\CriminalBiometricController::class, 'updateMatch'])->name('criminal-cases.biometrics.match');

    Route::get('criminal-cases/{id}/interviews', [\App\Http\Controllers\CriminalInterviewController::class, 'index'])->name('criminal-cases.interviews.index');
    Route::post('criminal-cases/{id}/interviews', [\App\Http\Controllers\CriminalInterviewController::class, 'store'])->name('criminal-cases.interviews.store');
    Route::post('criminal-cases/{id}/interviews/{interviewId}/sign-off', [\App\Http\Controllers\CriminalInterviewController::class, 'signOff'])->name('criminal-cases.interviews.sign-off');

    Route::get('criminal-cases/{id}/investigation-reports', [\App\Http\Controllers\CriminalInvestigationReportController::class, 'index'])->name('criminal-cases.investigation-reports.index');
    Route::post('criminal-cases/{id}/investigation-reports', [\App\Http\Controllers\CriminalInvestigationReportController::class, 'store'])->name('criminal-cases.investigation-reports.store');
    Route::post('criminal-cases/{id}/investigation-reports/{reportId}/submit-for-review', [\App\Http\Controllers\CriminalInvestigationReportController::class, 'submitForReview'])->name('criminal-cases.investigation-reports.submit-for-review');
    Route::post('criminal-cases/{id}/investigation-reports/{reportId}/approve', [\App\Http\Controllers\CriminalInvestigationReportController::class, 'approve'])->name('criminal-cases.investigation-reports.approve');
    Route::post('criminal-cases/{id}/investigation-reports/{reportId}/submit', [\App\Http\Controllers\CriminalInvestigationReportController::class, 'submit'])->name('criminal-cases.investigation-reports.submit');

    Route::get('cid-conclusion-reports', [\App\Http\Controllers\CriminalConclusionReportController::class, 'index'])->name('cid-conclusion-reports.index');
});

// ── CID Detention Center ──────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:CID Detention Center,view'])->group(function () {
    Route::get('cid-detainees', [\App\Http\Controllers\CriminalDetaineeController::class, 'index'])->name('cid-detainees.index');
    Route::get('cid-detainees/{id}', [\App\Http\Controllers\CriminalDetaineeController::class, 'show'])->name('cid-detainees.show');
    Route::post('cid-detainees/{id}/status', [\App\Http\Controllers\CriminalDetaineeController::class, 'updateStatus'])->name('cid-detainees.status');
    Route::post('cid-detainees/{id}/transfer', [\App\Http\Controllers\CriminalDetaineeController::class, 'storeTransfer'])->name('cid-detainees.transfer');
    Route::post('cid-detainees/{id}/release', [\App\Http\Controllers\CriminalDetaineeController::class, 'storeRelease'])->name('cid-detainees.release');
    Route::get('criminal-cases/{id}/admission', [\App\Http\Controllers\CriminalDetaineeController::class, 'admissionForm'])->name('cid-detainees.admission-form');
    Route::post('criminal-cases/{id}/admission', [\App\Http\Controllers\CriminalDetaineeController::class, 'admit'])->name('cid-detainees.admit');
    Route::post('cid-detainees/{id}/remand', [\App\Http\Controllers\CriminalDetaineeController::class, 'storeRemandOrder'])->name('cid-detainees.remand');
    Route::get('cid-detainees/{id}/medical', [\App\Http\Controllers\CriminalDetaineeController::class, 'medicalRecords'])->name('cid-detainees.medical');
    Route::post('cid-detainees/{id}/medical', [\App\Http\Controllers\CriminalDetaineeController::class, 'storeMedicalRecord'])->name('cid-detainees.medical.store');

    Route::get('cid-remand-management', [\App\Http\Controllers\CriminalRemandController::class, 'index'])->name('cid-remand-management.index');

    Route::get('criminal-cases/{id}/exhibits', [\App\Http\Controllers\CriminalExhibitController::class, 'index'])->name('criminal-cases.exhibits.index');
    Route::post('criminal-cases/{id}/exhibits', [\App\Http\Controllers\CriminalExhibitController::class, 'store'])->name('criminal-cases.exhibits.store');
    Route::post('criminal-cases/{id}/exhibits/{exhibitId}/status', [\App\Http\Controllers\CriminalExhibitController::class, 'updateStatus'])->name('criminal-cases.exhibits.status');
});

// ── CID Legal Process ────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:CID Legal Process,view'])->group(function () {
    Route::get('cid-legal-process/{slug}', [\App\Http\Controllers\CriminalLegalProcessController::class, 'index'])->name('cid-legal-process.index');
    Route::get('criminal-cases/{id}/legal-process/{slug}', [\App\Http\Controllers\CriminalLegalProcessController::class, 'form'])->name('cid-legal-process.form');
    Route::post('criminal-cases/{id}/legal-process/{slug}', [\App\Http\Controllers\CriminalLegalProcessController::class, 'store'])->name('cid-legal-process.store');
    Route::post('criminal-cases/{id}/legal-process/{slug}/{requestId}/status', [\App\Http\Controllers\CriminalLegalProcessController::class, 'updateStatus'])->name('cid-legal-process.status');
    Route::post('criminal-cases/{id}/legal-process/{slug}/{requestId}/execution', [\App\Http\Controllers\CriminalLegalProcessController::class, 'recordExecution'])->name('cid-legal-process.execution');

    Route::get('cid-arrest-warrants', [\App\Http\Controllers\CriminalArrestRegistryController::class, 'warrants'])->name('cid-arrest-warrants.index');
    Route::get('cid-arrests-without-warrant', [\App\Http\Controllers\CriminalArrestRegistryController::class, 'withoutWarrant'])->name('cid-arrests-without-warrant.index');

    Route::get('cid-received-warrants', [\App\Http\Controllers\CriminalReceivedWarrantController::class, 'index'])->name('cid-received-warrants.index');
    Route::post('cid-received-warrants', [\App\Http\Controllers\CriminalReceivedWarrantController::class, 'store'])->name('cid-received-warrants.store');
    Route::post('cid-received-warrants/{id}/assign', [\App\Http\Controllers\CriminalReceivedWarrantController::class, 'assign'])->name('cid-received-warrants.assign');
    Route::post('cid-received-warrants/{id}/status', [\App\Http\Controllers\CriminalReceivedWarrantController::class, 'updateStatus'])->name('cid-received-warrants.status');

    Route::get('criminal-cases/{id}/court-forms', [\App\Http\Controllers\CriminalCourtFormController::class, 'index'])->name('criminal-cases.court-forms.index');
    Route::post('criminal-cases/{id}/court-forms', [\App\Http\Controllers\CriminalCourtFormController::class, 'store'])->name('criminal-cases.court-forms.store');
    Route::post('criminal-cases/{id}/court-forms/{formId}/status', [\App\Http\Controllers\CriminalCourtFormController::class, 'updateStatus'])->name('criminal-cases.court-forms.status');

    Route::get('cid-bulk-arrests', [\App\Http\Controllers\CriminalBulkArrestController::class, 'index'])->name('cid-bulk-arrests.index');
    Route::post('cid-bulk-arrests', [\App\Http\Controllers\CriminalBulkArrestController::class, 'store'])->name('cid-bulk-arrests.store');
    Route::get('cid-bulk-arrests/{id}', [\App\Http\Controllers\CriminalBulkArrestController::class, 'show'])->name('cid-bulk-arrests.show');
    Route::post('cid-bulk-arrests/{id}/members', [\App\Http\Controllers\CriminalBulkArrestController::class, 'addMember'])->name('cid-bulk-arrests.members.store');
    Route::post('cid-bulk-arrests/{id}/assign', [\App\Http\Controllers\CriminalBulkArrestController::class, 'assignInvestigator'])->name('cid-bulk-arrests.assign');
    Route::post('cid-bulk-arrests/{id}/members/{memberId}/generate-case', [\App\Http\Controllers\CriminalBulkArrestController::class, 'generateCase'])->name('cid-bulk-arrests.generate-case');
});

// ══════════════════════════════════════════════════════════════════════════════
// Platform Administration (Super Admin only)
// ══════════════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'permission:Platform Administration,view'])->group(function () {
    Route::get('platform/dashboard', [\App\Http\Controllers\PlatformDashboardController::class, 'index'])->name('platform.dashboard');
    Route::get('institutions', [\App\Http\Controllers\InstitutionController::class, 'index'])->name('institutions.index');
    Route::get('audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
});

Route::middleware(['auth', 'permission:Platform Administration,manage-institutions'])->group(function () {
    Route::post('institutions', [\App\Http\Controllers\InstitutionController::class, 'store'])->name('institutions.store');
    Route::put('institutions/{institution}', [\App\Http\Controllers\InstitutionController::class, 'update'])->name('institutions.update');
});

Route::middleware(['auth', 'permission:Platform Administration,manage-institution-admins'])->group(function () {
    Route::get('institutions/{institution}/admin/create', [\App\Http\Controllers\InstitutionController::class, 'createAdmin'])->name('institutions.admin.create');
    Route::post('institutions/{institution}/admin', [\App\Http\Controllers\InstitutionController::class, 'storeAdmin'])->name('institutions.admin.store');
});

require __DIR__.'/auth.php';
