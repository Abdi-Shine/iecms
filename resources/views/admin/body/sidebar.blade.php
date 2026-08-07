<!-- SIDEBAR -->
<aside id="sidebar" x-data="{
       activeMenu: localStorage.getItem('sidebarActiveMenu') || '',
       toggleMenu(menu) {
           if (this.activeMenu === menu) {
               this.activeMenu = '';
           } else if (this.activeMenu.startsWith(menu.split('_')[0] + '_') && !menu.includes('_')) {
                this.activeMenu = menu;
           } else {
               this.activeMenu = menu;
           }
           localStorage.setItem('sidebarActiveMenu', this.activeMenu);
       },
       isOpen(menu) {
           return this.activeMenu === menu || this.activeMenu.startsWith(menu + '_');
       }
   }"
    class="fixed top-0 left-0 w-[260px] h-screen bg-primary z-50 transition-transform duration-300 lg:translate-x-0 -translate-x-full overflow-y-auto scrollbar-hide shadow-2xl border-r border-white/5">

    <!-- Brand -->
    @php
        $sidebarLogoUrl = null;
        try {
            $sidebarAuthUser = auth()->user();
            $sidebarEmployee = $sidebarAuthUser?->employee
                ?? \App\Models\Employee::where('email', $sidebarAuthUser?->email)->first()
                ?? \App\Models\Employee::where('EmpName', $sidebarAuthUser?->name)->first();
            $sidebarCourt = $sidebarEmployee?->court;
            if ($sidebarCourt?->logo) {
                $sidebarLogoUrl = asset('storage/' . $sidebarCourt->logo);
            }
        } catch (\Throwable $e) {
            // DB not ready or migration pending — fall back to the default logo
        }
    @endphp
    <div class="sticky top-0 bg-primary/95 backdrop-blur-sm z-10 px-6 py-3 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-14 h-14 rounded-full overflow-hidden flex-shrink-0 shadow-lg bg-white p-1">
                <img src="{{ $sidebarLogoUrl ?? asset('images/logo.png') }}"
                    alt="{{ $sidebarCourt->shortName ?? 'IECMS' }} Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h4 class="text-white text-lg font-bold tracking-tight">IECMS Portal</h4>
                <p class="text-accent text-[10px] font-bold uppercase tracking-widest">
                    Judiciary System
                </p>
            </div>
        </div>
    </div>

    @php
        $u = auth()->user();
        $canDashboard = $u->hasPermission('Dashboard');
        $canCivil = $u->hasPermission('Civil Case Registration');
        $canHandover = $u->hasPermission('Case Handover');
        $canHandoverApproval = $u->hasPermission('Case Handover Approval');
        $canEnforcement = $u->hasPermission('Enforcement');
        $canAppeal = $u->hasPermission('Appeal');
        $canTransfer = $u->hasPermission('Appeal');
        $canAssignment = $u->hasPermission('Case Assignment');
        $canHearings = $u->hasPermission('Hearings');
        $canJudgments = $u->hasPermission('Judgments');
        $canCloseCase = $u->hasPermission('Close Case');
        $canReturnFile = $u->hasPermission('Return Civil File');
        $canReceiveJudgmentParties = $u->hasPermission('Judgment Receipts');

        $canAppealCivil = $u->hasPermission('Appeal Civil Registration');
        $canAppealHandover = $u->hasPermission('Appeal Case Handover');
        $canAppealHandoverApproval = $u->hasPermission('Appeal Case Handover Approval');
        $canAppealEnforcement = $u->hasPermission('Appeal Enforcement');
        $canAppealCases = $u->hasPermission('Appeal Cases');
        $canAppealAssignment = $u->hasPermission('Appeal Case Assignment');
        $canAppealHearings = $u->hasPermission('Appeal Hearings');
        $canAppealJudgments = $u->hasPermission('Appeal Judgments');
        $canAppealCloseCase = $u->hasPermission('Appeal Close Case');
        $canAppealReturnFile = $u->hasPermission('Appeal Return File');
        $canAppealReceiveJudgmentParties = $u->hasPermission('Appeal Judgment Receipts');

        $canAppealCriminal = $u->hasPermission('Appeal Criminal Registration');
        $canAppealFamily = $u->hasPermission('Appeal Family Registration');

        $canFamily = $u->hasPermission('Family Case Registration');
        $canFamilyHandover = $u->hasPermission('Family Case Handover');
        $canFamilyHandoverApproval = $u->hasPermission('Family Case Handover Approval');
        $canFamilyEnforcement = $u->hasPermission('Family Enforcement');
        $canFamilyCases = $u->hasPermission('Family Cases');
        $canFamilyAssignment = $u->hasPermission('Family Case Assignment');
        $canFamilyHearings = $u->hasPermission('Family Hearings');
        $canFamilyJudgments = $u->hasPermission('Family Judgments');
        $canFamilyCloseCase = $u->hasPermission('Family Close Case');
        $canFamilyReturnFile = $u->hasPermission('Family Return File');
        $canFamilyReceiveJudgmentParties = $u->hasPermission('Family Judgment Receipts');

        $familyPermSet = $canFamily || $canFamilyHandover || $canFamilyHandoverApproval || $canFamilyEnforcement
            || $canFamilyCases || $canFamilyAssignment || $canFamilyHearings || $canFamilyJudgments
            || $canFamilyCloseCase || $canFamilyReturnFile || $canFamilyReceiveJudgmentParties;
        $hasFamilySection = $familyPermSet;

        $canExecution = $u->hasPermission('Execution Case Registration');
        $canExecutionHandover = $u->hasPermission('Execution Case Handover');
        $canExecutionHandoverApproval = $u->hasPermission('Execution Case Handover Approval');
        $canExecutionEnforcement = $u->hasPermission('Execution Enforcement');
        $canExecutionCases = $u->hasPermission('Execution Cases');
        $canExecutionAssignment = $u->hasPermission('Execution Case Assignment');
        $canExecutionHearings = $u->hasPermission('Execution Hearings');
        $canExecutionJudgments = $u->hasPermission('Execution Judgments');
        $canExecutionCloseCase = $u->hasPermission('Execution Close Case');
        $canExecutionReturnFile = $u->hasPermission('Execution Return File');
        $canExecutionReceiveJudgmentParties = $u->hasPermission('Execution Judgment Receipts');

        $executionPermSet = $canExecution || $canExecutionHandover || $canExecutionHandoverApproval || $canExecutionEnforcement
            || $canExecutionCases || $canExecutionAssignment || $canExecutionHearings || $canExecutionJudgments
            || $canExecutionCloseCase || $canExecutionReturnFile || $canExecutionReceiveJudgmentParties;
        $hasExecutionSection = $executionPermSet;

        $canCriminal = $u->hasPermission('Criminal Case Registration');
        $canCriminalHandover = $u->hasPermission('Criminal Case Handover');
        $canCriminalHandoverApproval = $u->hasPermission('Criminal Case Handover Approval');
        $canCriminalEnforcement = $u->hasPermission('Criminal Enforcement');
        $canCriminalCases = $u->hasPermission('Criminal Cases');
        $canCriminalAssignment = $u->hasPermission('Criminal Case Assignment');
        $canCriminalHearings = $u->hasPermission('Criminal Hearings');
        $canCriminalJudgments = $u->hasPermission('Criminal Judgments');
        $canCriminalCloseCase = $u->hasPermission('Criminal Close Case');
        $canCriminalReturnFile = $u->hasPermission('Criminal Return File');
        $canCriminalReceiveJudgmentParties = $u->hasPermission('Criminal Judgment Receipts');

        $criminalPermSet = $canCriminal || $canCriminalHandover || $canCriminalHandoverApproval || $canCriminalEnforcement
            || $canCriminalCases || $canCriminalAssignment || $canCriminalHearings || $canCriminalJudgments
            || $canCriminalCloseCase || $canCriminalReturnFile || $canCriminalReceiveJudgmentParties;
        $hasCriminalSection = $criminalPermSet;

        $canAttorneyDashboard = $u->hasPermission('Attorney Dashboard');
        $canAttorneyCases = $u->hasPermission('Attorney Case Registration');
        $canAttorneyDepartments = $u->hasPermission('Attorney Departments');
        $canAttorneyCaseReviews = $u->hasPermission('Attorney Case Reviews');
        $canAttorneyProsecutorAssignments = $u->hasPermission('Attorney Prosecutor Assignments');

        $canCidDashboard = $u->hasPermission('CID Dashboard');
        $canCidInvestigation = $u->hasPermission('CID Investigation Workflow');
        $canCidCaseManagement = $u->hasPermission('CID Case Management');
        $canCidEvidence = $u->hasPermission('CID Evidence & Documentation');
        $canCidLegalProcess = $u->hasPermission('CID Legal Process');
        $canCidDetention = $u->hasPermission('CID Detention Center');
        $canCidSettings = $u->hasPermission('CID Settings');

        $canPlatformAdmin = $u->hasPermission('Platform Administration');

        $canFinance = $u->hasPermission('Finance');
        $canLawyer = $u->hasPermission('Lawyer Registry');
        $canarchive = $u->hasPermission('Archive');
        $canStaff = $u->hasPermission('Staff Registry');
        $canCourt = $u->hasPermission('Judicial Units');
        $canStateRegion = $u->hasPermission('State & Region');
        $canCity = $u->hasPermission('City');
        $canPublicInstitution = $u->hasPermission('Public Institutions');
        $canCaseType = $u->hasPermission('Case Types');
        $canCaseCategory = $u->hasPermission('Case Categories');
        $canStatusProc = $u->hasPermission('Status Process');
        $canDocAttach = $u->hasPermission('Document Attachment');
        $canRoles = $u->hasPermission('Role & Permission');
        $canBackup = $u->hasPermission('Backup & Restore');
        $canAccessLogin = $u->hasPermission('Access Login');
        $canAudit = $u->hasPermission('Audit Logs');
        $cancourtintergration = $u->hasPermission('Courts Integration');

        $hasCivilSection = $canCivil || $canHandover || $canEnforcement || $canAppeal || $canTransfer;
        $hasConclusionSection = $canHandover || $canHearings || $canJudgments || $canCloseCase || $canReturnFile || $canReceiveJudgmentParties;
        $hasAdminSection = $canStaff || $canCourt || $canStateRegion || $canCity || $canPublicInstitution || $canCaseType || $canCaseCategory
            || $canStatusProc || $canDocAttach || $canRoles || $canBackup || $canAudit || $canAccessLogin || $canAttorneyDepartments;
        $civilPermSet = $canCivil || $canHandover || $canEnforcement || $canAppeal
            || $canTransfer || $canAssignment || $canHearings || $canJudgments || $canCloseCase
            || $canReturnFile || $canReceiveJudgmentParties;

        // courts.type (dropdown-driven from court_types) is the real tier
        // field — District Court | Regional Court | Appellate Court |
        // High Court | Supreme Court, backfilled in
        // 2026_08_06_000003_backfill_court_type_and_retire_legacy_court_types.
        // Civil/Criminal/Family/Execution modules are shared between
        // District and Regional tiers (same permissions, same views), so
        // this only relabels the shared section — it doesn't gate it.
        $userCourtRecord = $u->employee?->court ?? null;
        $courtTier = match ($userCourtRecord?->type) {
            'District Court'  => 'district',
            'Regional Court'  => 'regional',
            'Appellate Court' => 'appeal',
            'High Court'      => 'high',
            'Supreme Court'   => 'supreme',
            default           => null,
        };
        $sharedTierLabel = match ($courtTier) {
            'regional' => ' (Gobolka)',
            'district' => ' (Degmada)',
            default    => '',
        };
        $appealCivilPermSet = $canAppealCivil || $canAppealHandover || $canAppealEnforcement || $canAppealCases
            || $canAppealAssignment || $canAppealHearings || $canAppealJudgments || $canAppealCloseCase
            || $canAppealReturnFile || $canAppealReceiveJudgmentParties;

        // Section visibility still follows permissions, not court tier —
        // an employee's role, not just their court record, decides what
        // they can see. $courtTier only relabels the shared sections.
        $hasDistrictAndRegionalCivilSection = $civilPermSet;
        $hasAppealCivilSection = $appealCivilPermSet;
        $hasAppealCriminalSection = $canAppealCriminal;
        $hasAppealFamilySection = $canAppealFamily;
    @endphp

    <!-- Menu Sections -->
    <div class="px-4 py-4 space-y-1">

        <!-- 1. Dashboard (suppressed for institutions that have their own
             dashboard — e.g. CID/AGO — since the generic Dashboard here
             surfaces court-wide civil/criminal case statistics that don't
             belong in a non-court institution admin's sidebar) -->
        @if($canDashboard && !$canCidDashboard && !$canAttorneyDashboard)
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] transition-all duration-200 w-full {{ Route::currentRouteName() == 'dashboard' ? 'bg-white/5 text-white' : '' }}">
                <i class="bi bi-speedometer2 text-lg"></i>
                <span>Dashboard</span>
            </a>
        @endif

        @if($canAttorneyDashboard)
            <a href="{{ route('attorney-dashboard.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] transition-all duration-200 w-full {{ Route::is('attorney-dashboard.*') ? 'bg-white/5 text-white' : '' }}">
                <i class="bi bi-speedometer2 text-lg"></i>
                <span>Guriga</span>
            </a>
        @endif

        @if($canAttorneyCases)
            <div class="space-y-1">
                <button @click="toggleMenu('attorney-section')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-shield-lock-fill text-lg text-ago-100"></i>
                    <span class="text-left leading-tight">Dacwadaha Cabasha</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('attorney-section') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('attorney-section')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('attorney-cases.tracking') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('attorney-cases.tracking') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Lasocodka Dacwadaha</a>
                    <a href="{{ route('attorney-cases.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('attorney-cases.*') && !Route::is('attorney-cases.tracking') && !Route::is('attorney-cases.calendar') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Diiwaanka Cabashada</a>
                </div>
            </div>
        @endif

        @if($canAttorneyCaseReviews)
            <div class="space-y-1">
                <button @click="toggleMenu('attorney-hubinta')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-shield-check text-lg text-ago-100"></i>
                    <span class="text-left leading-tight">Hubinta Dacwadaha</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('attorney-hubinta') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('attorney-hubinta')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('attorney-case-reviews.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('attorney-case-reviews.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Hubinta Cabashada</a>
                </div>
            </div>
        @endif

        @if($canAttorneyProsecutorAssignments)
            <div class="space-y-1">
                <button @click="toggleMenu('attorney-galqoris')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-journal-plus text-lg text-ago-100"></i>
                    <span class="text-left leading-tight">Gal Qoris Dacwadaha</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('attorney-galqoris') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('attorney-galqoris')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('attorney-prosecutor-assignments.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('attorney-prosecutor-assignments.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Gal Qorista Cabashada</a>
                </div>
            </div>
        @endif

        @if($canAttorneyCases)
            <div class="space-y-1">
                <button @click="toggleMenu('attorney-socodsinta')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-signpost-split-fill text-lg text-ago-100"></i>
                    <span class="text-left leading-tight">Socodsinta Dacwadaha</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('attorney-socodsinta') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('attorney-socodsinta')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('attorney-cases.socodsinta') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('attorney-cases.socodsinta') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Socodsinta Cabashada</a>
                </div>
            </div>
        @endif

        @if($canAttorneyCases)
            <div class="space-y-1">
                <button @click="toggleMenu('attorney-mudeynta')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-calendar2-week-fill text-lg text-ago-100"></i>
                    <span class="text-left leading-tight">Jadwalka Dacwadaha</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('attorney-mudeynta') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('attorney-mudeynta')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('attorney-cases.calendar') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('attorney-cases.calendar') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Jadwalka Maxkamadaha</a>
                </div>
            </div>
        @endif

        <!-- CID: Criminal Investigation Department -->
        @if($canCidDashboard)
            <a href="{{ route('cid-dashboard.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] transition-all duration-200 w-full {{ Route::is('cid-dashboard.*') ? 'bg-white/5 text-white' : '' }}">
                <i class="bi bi-speedometer2 text-lg"></i>
                <span>CID Dashboard</span>
            </a>
        @endif

        @if($canCidInvestigation)
            <a href="{{ route('criminal-cases.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] transition-all duration-200 w-full {{ Route::is('criminal-cases.*') ? 'bg-white/5 text-white' : '' }}">
                <i class="bi bi-search text-lg"></i>
                <span>Investigation Workflow</span>
            </a>
        @endif

        @if($canCidCaseManagement)
            <div class="space-y-1">
                <button @click="toggleMenu('cid-case-management')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full">
                    <i class="bi bi-folder2-open text-lg"></i>
                    <span class="text-left leading-tight">Case Management</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('cid-case-management') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('cid-case-management')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('cid-occurrence-books.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-occurrence-books.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-journal-bookmark text-lg"></i> Occurrence Books</a>
                    <a href="{{ route('cid-internal-ob.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-internal-ob.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-journal-lock text-lg"></i> Internal OB</a>
                    <a href="{{ route('cid-ob-archive.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-ob-archive.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-archive text-lg"></i> OB Archive</a>
                    <a href="{{ route('cid-court-calendar.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-court-calendar.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-calendar-event text-lg"></i> Court Appearance</a>
                    <a href="{{ route('cid-period-alerts.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-period-alerts.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-bell text-lg"></i> Period Alerts</a>
                </div>
            </div>
        @endif

        @if($canCidEvidence)
            <div class="space-y-1">
                <button @click="toggleMenu('cid-evidence')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full">
                    <i class="bi bi-archive-fill text-lg"></i>
                    <span class="text-left leading-tight">Evidence &amp; Documentation</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('cid-evidence') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('cid-evidence')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('cid-evidence-registry.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-evidence-registry.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-box-seam text-lg"></i> Evidence</a>
                    <a href="{{ route('cid-conclusion-reports.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-conclusion-reports.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-file-earmark-check text-lg"></i> Conclusion Reports</a>
                </div>
            </div>
        @endif

        @if($canCidLegalProcess)
            <div class="space-y-1">
                <button @click="toggleMenu('cid-legal-process')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full">
                    <i class="bi bi-file-earmark-lock2-fill text-lg"></i>
                    <span class="text-left leading-tight">Legal Process</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('cid-legal-process') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('cid-legal-process')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[700px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[700px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('cid-legal-process.index', 'arrest-without-warrant-ago') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ request()->is('cid-legal-process/arrest-without-warrant-ago') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-send text-lg"></i> Arrest Without Warrant (AGO)</a>
                    <a href="{{ route('cid-legal-process.index', 'warrant-of-arrest-ago') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ request()->is('cid-legal-process/warrant-of-arrest-ago') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-send text-lg"></i> Warrant of Arrest (AGO)</a>
                    <a href="{{ route('cid-legal-process.index', 'search-seizure-ago') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ request()->is('cid-legal-process/search-seizure-ago') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-send text-lg"></i> Search &amp; Seizure (AGO)</a>
                    <a href="{{ route('cid-legal-process.index', 'asset-recovery-ago') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ request()->is('cid-legal-process/asset-recovery-ago') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-send text-lg"></i> Asset Recovery (AGO)</a>
                    <a href="{{ route('cid-legal-process.index', 'search-warrants') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ request()->is('cid-legal-process/search-warrants') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-file-earmark-text text-lg"></i> Search Warrants</a>
                    <a href="{{ route('cid-arrest-warrants.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-arrest-warrants.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-shield-lock text-lg"></i> Arrest Warrants</a>
                    <a href="{{ route('cid-arrests-without-warrant.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-arrests-without-warrant.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-shield-exclamation text-lg"></i> Arrests Without Warrant</a>
                    <a href="{{ route('cid-received-warrants.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-received-warrants.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-inbox text-lg"></i> Received Arrest Warrants</a>
                    <a href="{{ route('cid-bulk-arrests.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-bulk-arrests.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-people text-lg"></i> Bulk Arrest Management</a>
                </div>
            </div>
        @endif

        @if($canCidDetention)
            <div class="space-y-1">
                <button @click="toggleMenu('cid-detention')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full">
                    <i class="bi bi-shield-lock-fill text-lg"></i>
                    <span class="text-left leading-tight">Detention Center</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('cid-detention') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('cid-detention')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('cid-detainees.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-detainees.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-people text-lg"></i> Registry</a>
                    <a href="{{ route('cid-remand-management.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-remand-management.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-hourglass-split text-lg"></i> Remand Management</a>
                    <a href="{{ route('cid-court-calendar.index') }}"
                        class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('cid-court-calendar.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-calendar-event text-lg"></i> Court Schedule</a>
                </div>
            </div>
        @endif

        @if($canCidSettings)
            <a href="{{ route('cid-number-formats.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] transition-all duration-200 w-full {{ Route::is('cid-number-formats.*') ? 'bg-white/5 text-white' : '' }}">
                <i class="bi bi-gear-fill text-lg"></i>
                <span>CID Settings</span>
            </a>
        @endif

        <!-- 2. Civil Cases for District and Regional Court-->
        @if($hasDistrictAndRegionalCivilSection)
            <div class="space-y-1">
                <button @click="toggleMenu('case-registration')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-briefcase-fill text-lg"></i>
                    <span class="text-left leading-tight">Dacwadaha Madaniga{{ $sharedTierLabel }}</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('case-registration') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('case-registration')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    @if($canCivil)
                        <a href="{{ route('civil-case-tracking.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('civil-case-tracking.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Lasocadka Dacwadaha </a>
                        <a href="{{ route('civil-registration.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('civil-registration.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Diiwaanka Dacwadaha</a>
                    @endif
                    @if($canHandover)
                        <a href="{{ route('civil-registration.handover') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('civil-registration.handover') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhaqdhaqaqa Dacwada</a>
                    @endif
                    @if($canEnforcement)
                        <a href="{{ route('enforcement.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('enforcement.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>DhaqanGalka Dacwadaha</a>
                    @endif
                    @if($canAppeal)
                        <a href="{{ route('appeal.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Rafcaanka Dacwadaha</a>
                    @endif
                    @if($canAssignment)
                        <a href="{{ route('civil-case-assign.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('civil-case-assign.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Gal Ku Qoris Madani</a>
                    @endif
                    @if($canHearings)
                        <a href="{{ route('hearings.view') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('hearings.view') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Jadwalka Dacwadaha</a>
                    @endif
                    @if($canCloseCase)
                        <a href="{{ route('close_case.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('close_case.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Gal Xeris Dacwadaha</a>
                    @endif
                    @if($canHandoverApproval)
                        <a href="{{ route('civil-case-handover.approval') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('civil-case-handover.approval') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhaqaaqa Dacwadaha
                            <span data-notif-badge="handover_submitted"
                                style="display:none;min-width:18px;height:18px;background:#ef4444;color:white;border-radius:999px;font-size:10px;font-weight:800;align-items:center;justify-content:center;padding:0 5px;line-height:1;margin-left:auto"></span>
                        </a>
                    @endif
                    @if($canHearings)
                        <a href="{{ route('civil.hearing.cases') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('civil.hearing.cases') || Route::is('hearings.edit') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Mudeynta Dacwadaha</a>
                        <a href="{{ route('hearings.scripture') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('hearings.scripture') || Route::is('hearings.scripture.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhegeysiga Dacwadaha</a>
                    @endif
                    @if($canJudgments)
                        <a href="{{ route('judgments.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('judgments.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Xukunka Dacwadaha</a>
                    @endif

                    @if($canReceiveJudgmentParties)
                        <a href="{{ route('judgments.receipts') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('judgments.receipts') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Qatay Xukunka Dacwada</a>
                    @endif
                    @if($canReturnFile)
                        <a href="{{ route('ReturnCivilFile.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('ReturnCivilFile.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Celinta Dacwadaha</a>
                    @endif
                </div>
            </div>
        @endif

        <!-- 2. Civil Cases for Banadir Regional Appeal Court-->
        @if($hasAppealCivilSection)
            <div class="space-y-1">
                <button @click="toggleMenu('appeal-registration')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-arrow-repeat text-lg"></i>
                    <span class="text-left leading-tight">Dacwadaha Rafcaanka</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('appeal-registration') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('appeal-registration')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    @if($canAppealCivil)
                        <a href="{{ route('appeal-civil-tracking.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-civil-tracking.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Lasocadka Dacwada </a>
                        <a href="{{ route('appeal-civil-registration.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-civil-registration.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Diiwaanka Dacwada </a>
                    @endif
                    @if($canAppealHandoverApproval)
                        <a href="{{ route('appeal-civil-handover.approval') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-civil-handover.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhaqdhaqaqa Dacwada
                            <span data-notif-badge="appeal_handover_submitted"
                                style="display:none;min-width:18px;height:18px;background:#ef4444;color:white;border-radius:999px;font-size:10px;font-weight:800;align-items:center;justify-content:center;padding:0 5px;line-height:1;margin-left:auto"></span>
                        </a>
                    @endif
                    @if($canAppealEnforcement)
                        <a href="{{ route('appeal-enforcement.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-enforcement.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhaqangalka Dacwada</a>
                    @endif
                    @if($canAppealCases)
                        <a href="{{ route('appeal-civil-appeal.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-civil-appeal.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Rafcaanka Dacwada</a>
                    @endif
                    @if($canAppealAssignment)
                        <a href="{{ route('appeal-civil-assign.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-civil-assign.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Gal Ku Qoris Madani</a>
                    @endif
                    @if($canAppealHearings)
                        <a href="{{ route('appeal-hearings.view') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-hearings.view') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Jadwalka Dacwada</a>
                    @endif
                    @if($canAppealCloseCase)
                        <a href="{{ route('appeal-close-case.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-close-case.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Gal Xeris Dacwada</a>
                    @endif
                    @if($canAppealHearings)
                        <a href="{{ route('appeal-civil.hearing.cases') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-civil.hearing.cases') || Route::is('appeal-hearings.edit') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Mudeymaha Dacwada</a>
                        <a href="{{ route('appeal-hearings.scripture') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-hearings.scripture') || Route::is('appeal-hearings.scripture.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhegeysiga Dacwada</a>
                    @endif
                    @if($canAppealJudgments)
                        <a href="{{ route('appeal-judgments.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-judgments.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Xukunka Dacwada</a>
                    @endif
                    @if($canAppealReceiveJudgmentParties)
                        <a href="{{ route('appeal-judgments.receipts') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-judgments.receipts') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Qatay Xukunka Dacwada</a>
                    @endif
                    @if($canAppealReturnFile)
                        <a href="{{ route('appeal-return-file.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-return-file.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Celinta Dacwada</a>
                    @endif
                </div>
            </div>
        @endif

        <!-- 2b. Criminal Cases for Banadir Regional Appeal Court (Registration stage only so far) -->
        @if($hasAppealCriminalSection)
            <div class="space-y-1">
                <button @click="toggleMenu('appeal-criminal-registration')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-arrow-repeat text-lg"></i>
                    <span class="text-left leading-tight">Dacwadaha Rafcaanka - Ciqaabta</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('appeal-criminal-registration') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('appeal-criminal-registration')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('appeal-criminal-tracking.index') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-tracking.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Lasocadka Dacwada </a>
                    <a href="{{ route('appeal-criminal-registration.index') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-registration.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Diiwaanka Dacwada </a>
                    @if($canAppealAssignment)
                        <a href="{{ route('appeal-criminal-assign.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-assign.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Gal Ku Qoris Ciqaabta</a>
                    @endif
                    @if($canAppealHearings)
                        <a href="{{ route('appeal-criminal-hearings.view') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-hearings.view') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Jadwalka Ciqaabta</a>
                        <a href="{{ route('appeal-criminal.hearing.cases') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal.hearing.cases') || Route::is('appeal-criminal-hearings.edit') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Mudeymaha Ciqaabta</a>
                        <a href="{{ route('appeal-criminal-hearings.scripture') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-hearings.scripture') || Route::is('appeal-criminal-hearings.scripture.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Dhegeysiga Ciqaabta</a>
                    @endif
                    @if($canAppealHandoverApproval)
                        <a href="{{ route('appeal-criminal-handover.approval') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-handover.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Dhaqdhaqaqa Ciqaabta</a>
                    @endif
                    @if($canAppealJudgments)
                        <a href="{{ route('appeal-criminal-judgments.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-judgments.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Xukunka Ciqaabta</a>
                    @endif
                    @if($canAppealReceiveJudgmentParties)
                        <a href="{{ route('appeal-criminal-judgments.receipts') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-judgments.receipts') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Qatay Xukunka Ciqaabta</a>
                    @endif
                    @if($canAppealCloseCase)
                        <a href="{{ route('appeal-criminal-close-case.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-close-case.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Gal Xeris Ciqaabta</a>
                    @endif
                    @if($canAppealReturnFile)
                        <a href="{{ route('appeal-criminal-return-file.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-return-file.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Celinta Ciqaabta</a>
                    @endif
                    @if($canAppealEnforcement)
                        <a href="{{ route('appeal-criminal-enforcement.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-criminal-enforcement.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Dhaqangalka Ciqaabta</a>
                    @endif
                </div>
            </div>
        @endif

        <!-- 2c. Family Cases for Banadir Regional Appeal Court (Registration through Conclusion; Transfer remains) -->
        @if($hasAppealFamilySection)
            <div class="space-y-1">
                <button @click="toggleMenu('appeal-family-registration')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-arrow-repeat text-lg"></i>
                    <span class="text-left leading-tight">Dacwadaha Rafcaanka - Qoyska</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('appeal-family-registration') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('appeal-family-registration')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('appeal-family-tracking.index') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-tracking.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Lasocadka Dacwada </a>
                    <a href="{{ route('appeal-family-registration.index') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-registration.*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Diiwaanka Dacwada </a>
                    @if($canAppealAssignment)
                        <a href="{{ route('appeal-family-assign.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-assign.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Gal Ku Qoris Qoyska</a>
                    @endif
                    @if($canAppealHearings)
                        <a href="{{ route('appeal-family-hearings.view') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-hearings.view') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Jadwalka Qoyska</a>
                        <a href="{{ route('appeal-family.hearing.cases') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family.hearing.cases') || Route::is('appeal-family-hearings.edit') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Mudeymaha Qoyska</a>
                        <a href="{{ route('appeal-family-hearings.scripture') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-hearings.scripture') || Route::is('appeal-family-hearings.scripture.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Dhegeysiga Qoyska</a>
                    @endif
                    @if($canAppealHandoverApproval)
                        <a href="{{ route('appeal-family-handover.approval') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-handover.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Dhaqdhaqaqa Qoyska</a>
                    @endif
                    @if($canAppealJudgments)
                        <a href="{{ route('appeal-family-judgments.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-judgments.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Xukunka Qoyska</a>
                    @endif
                    @if($canAppealReceiveJudgmentParties)
                        <a href="{{ route('appeal-family-judgments.receipts') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-judgments.receipts') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Qatay Xukunka Qoyska</a>
                    @endif
                    @if($canAppealCloseCase)
                        <a href="{{ route('appeal-family-close-case.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-close-case.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Gal Xeris Qoyska</a>
                    @endif
                    @if($canAppealReturnFile)
                        <a href="{{ route('appeal-family-return-file.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-return-file.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Celinta Qoyska</a>
                    @endif
                    @if($canAppealEnforcement)
                        <a href="{{ route('appeal-family-enforcement.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('appeal-family-enforcement.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Dhaqangalka Qoyska</a>
                    @endif
                </div>
            </div>
        @endif

        <!-- 3. Family Cases for District Court -->
        @if($hasFamilySection)
            <div class="space-y-1">
                <button @click="toggleMenu('family-registration')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-people text-lg"></i>
                    <span class="text-left leading-tight">Dacwadaha Qoyska{{ $sharedTierLabel }}</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('family-registration') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('family-registration')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    @if($canFamily)
                        <a href="{{ route('family-case-tracking.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-case-tracking.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Lasocadka Dacwadaha </a>
                        <a href="{{ route('family-registration.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-registration.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Diiwaanka Dacwadaha</a>
                    @endif
                    @if($canFamilyHandover)
                        <a href="{{ route('family-registration.handover') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-registration.handover') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhaqdhaqaqa Dacwada</a>
                    @endif
                    @if($canFamilyEnforcement)
                        <a href="{{ route('family-enforcement.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-enforcement.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>DhaqanGalka Dacwadaha</a>
                    @endif
                    @if($canFamilyCases)
                        <a href="{{ route('family-case-appeal.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-case-appeal.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Rafcaanka Dacwadaha</a>
                        <a href="{{ route('family-transfer.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-transfer.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Wareejinta Dacwadaha</a>
                    @endif
                    @if($canFamilyAssignment)
                        <a href="{{ route('family-case-assign.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-case-assign.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Gal Ku Qoris Qoyska</a>
                    @endif
                    @if($canFamilyHearings)
                        <a href="{{ route('family-hearings.view') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-hearings.view') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Jadwalka Dacwadaha</a>
                    @endif
                    @if($canFamilyCloseCase)
                        <a href="{{ route('family-close-case.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-close-case.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Gal Xeris Dacwadaha</a>
                    @endif
                    @if($canFamilyHandoverApproval)
                        <a href="{{ route('family-case-handover.approval') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-case-handover.approval') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhaqaaqa Dacwadaha
                            <span data-notif-badge="family_handover_submitted"
                                style="display:none;min-width:18px;height:18px;background:#ef4444;color:white;border-radius:999px;font-size:10px;font-weight:800;align-items:center;justify-content:center;padding:0 5px;line-height:1;margin-left:auto"></span>
                        </a>
                    @endif
                    @if($canFamilyHearings)
                        <a href="{{ route('family.hearing.cases') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family.hearing.cases') || Route::is('family-hearings.edit') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Mudeynta Dacwadaha</a>
                        <a href="{{ route('family-hearings.scripture') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-hearings.scripture') || Route::is('family-hearings.scripture.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhegeysiga Dacwadaha</a>
                    @endif
                    @if($canFamilyJudgments)
                        <a href="{{ route('family-judgments.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-judgments.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Xukunka Dacwadaha</a>
                    @endif

                    @if($canFamilyReceiveJudgmentParties)
                        <a href="{{ route('family-judgments.receipts') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-judgments.receipts') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Qatay Xukunka Dacwada</a>
                    @endif
                    @if($canFamilyReturnFile)
                        <a href="{{ route('family-return-file.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('family-return-file.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Celinta Dacwadaha</a>
                    @endif
                </div>
            </div>
        @endif

        <!-- 4. Execution Cases for District Court -->
        @if($hasExecutionSection)
            <div class="space-y-1">
                <button @click="toggleMenu('execution-registration')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-hammer text-lg"></i>
                    <span class="text-left leading-tight">Dacwadaha Fulinta{{ $sharedTierLabel }}</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('execution-registration') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('execution-registration')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    @if($canExecution)
                        <a href="{{ route('execution-case-tracking.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-case-tracking.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Lasocadka Dacwadaha </a>
                        <a href="{{ route('execution-registration.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-registration.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Diiwaanka Dacwadaha</a>
                    @endif
                    @if($canExecutionHandover)
                        <a href="{{ route('execution-registration.handover') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-registration.handover') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhaqdhaqaqa Dacwada</a>
                    @endif
                    @if($canExecutionEnforcement)
                        <a href="{{ route('execution-enforcement.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-enforcement.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>DhaqanGalka Dacwadaha</a>
                    @endif
                    @if($canExecutionCases)
                        <a href="{{ route('execution-case-appeal.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-case-appeal.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Rafcaanka Dacwadaha</a>
                        <a href="{{ route('execution-transfer.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-transfer.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Wareejinta Dacwadaha</a>
                    @endif
                    @if($canExecutionAssignment)
                        <a href="{{ route('execution-case-assign.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-case-assign.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Gal Ku Qoris Fulinta</a>
                    @endif
                    @if($canExecutionHearings)
                        <a href="{{ route('execution-hearings.view') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-hearings.view') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Jadwalka Dacwadaha</a>
                    @endif
                    @if($canExecutionCloseCase)
                        <a href="{{ route('execution-close-case.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-close-case.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Gal Xeris Dacwadaha</a>
                    @endif
                    @if($canExecutionHandoverApproval)
                        <a href="{{ route('execution-case-handover.approval') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-case-handover.approval') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhaqaaqa Dacwadaha
                            <span data-notif-badge="execution_handover_submitted"
                                style="display:none;min-width:18px;height:18px;background:#ef4444;color:white;border-radius:999px;font-size:10px;font-weight:800;align-items:center;justify-content:center;padding:0 5px;line-height:1;margin-left:auto"></span>
                        </a>
                    @endif
                    @if($canExecutionHearings)
                        <a href="{{ route('execution.hearing.cases') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution.hearing.cases') || Route::is('execution-hearings.edit') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Mudeynta Dacwadaha</a>
                        <a href="{{ route('execution-hearings.scripture') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-hearings.scripture') || Route::is('execution-hearings.scripture.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhegeysiga Dacwadaha</a>
                    @endif
                    @if($canExecutionJudgments)
                        <a href="{{ route('execution-judgments.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-judgments.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Xukunka Dacwadaha</a>
                    @endif

                    @if($canExecutionReceiveJudgmentParties)
                        <a href="{{ route('execution-judgments.receipts') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-judgments.receipts') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Qatay Xukunka Dacwada</a>
                    @endif
                    @if($canExecutionReturnFile)
                        <a href="{{ route('execution-return-file.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('execution-return-file.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Celinta Dacwadaha</a>
                    @endif
                </div>
            </div>
        @endif

        @if($hasCriminalSection)
            <div class="space-y-1">
                <button @click="toggleMenu('criminal-registration')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-shield-fill-exclamation text-lg"></i>
                    <span class="text-left leading-tight">Dacwadaha Ciqaabta{{ $sharedTierLabel }}</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('criminal-registration') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('criminal-registration')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    @if($canCriminal)
                        <a href="{{ route('criminal-case-tracking.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-case-tracking.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Lasocadka Dacwadaha </a>
                        <a href="{{ route('criminal-registration.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-registration.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Diiwaanka Dacwadaha</a>
                    @endif
                    @if($canCriminalHandover)
                        <a href="{{ route('criminal-registration.handover') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-registration.handover') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhaqdhaqaqa Dacwada</a>
                    @endif
                    @if($canCriminalEnforcement)
                        <a href="{{ route('criminal-enforcement.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-enforcement.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>DhaqanGalka Dacwadaha</a>
                    @endif
                    @if($canCriminalCases)
                        <a href="{{ route('criminal-case-appeal.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-case-appeal.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Rafcaanka Dacwadaha</a>
                        <a href="{{ route('criminal-transfer.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-transfer.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Wareejinta Dacwadaha</a>
                    @endif
                    @if($canCriminalAssignment)
                        <a href="{{ route('criminal-case-assign.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-case-assign.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Gal Ku Qoris Ciqaabta</a>
                    @endif
                    @if($canCriminalHearings)
                        <a href="{{ route('criminal-hearings.view') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-hearings.view') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Jadwalka Dacwadaha</a>
                    @endif
                    @if($canCriminalCloseCase)
                        <a href="{{ route('criminal-close-case.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-close-case.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Gal Xeris Dacwadaha</a>
                    @endif
                    @if($canCriminalHandoverApproval)
                        <a href="{{ route('criminal-case-handover.approval') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-case-handover.approval') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhaqaaqa Dacwadaha
                            <span data-notif-badge="criminal_handover_submitted"
                                style="display:none;min-width:18px;height:18px;background:#ef4444;color:white;border-radius:999px;font-size:10px;font-weight:800;align-items:center;justify-content:center;padding:0 5px;line-height:1;margin-left:auto"></span>
                        </a>
                    @endif
                    @if($canCriminalHearings)
                        <a href="{{ route('criminal.hearing.cases') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal.hearing.cases') || Route::is('criminal-hearings.edit') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Mudeynta Dacwadaha</a>
                        <a href="{{ route('criminal-hearings.scripture') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-hearings.scripture') || Route::is('criminal-hearings.scripture.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Dhegeysiga Dacwadaha</a>
                    @endif
                    @if($canCriminalJudgments)
                        <a href="{{ route('criminal-judgments.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-judgments.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Xukunka Dacwadaha</a>
                    @endif

                    @if($canCriminalReceiveJudgmentParties)
                        <a href="{{ route('criminal-judgments.receipts') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-judgments.receipts') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Qatay Xukunka Dacwada</a>
                    @endif
                    @if($canCriminalReturnFile)
                        <a href="{{ route('criminal-return-file.index') }}"
                            class="flex items-center gap-3 pl-4 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('criminal-return-file.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i>Celinta Dacwadaha</a>
                    @endif
                </div>
            </div>
        @endif

        @if($cancourtintergration)
            <!-- courts integration -->
            <div class="space-y-1">
                <button @click="toggleMenu('courtsintegration')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-archive text-lg"></i>
                    <span class="text-left leading-tight">Courts Integration</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('archive') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('courtsintegration')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('courtsintergration.transfer') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('courtsintergration.transfer') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-mic text-lg"></i> Transfer Cases</a>
                    <a href="{{ route('courtsintergration.recived') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('courtsintergration.recived') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-gavel text-lg"></i> Receive Cases</a>
                </div>
            </div>
        @endif
        @if($canFinance)
            <!-- Finance -->
            <div class="space-y-1">
                <button @click="toggleMenu('finance-payment')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-cash-coin text-lg"></i>
                    <span class="text-left leading-tight">finance payment</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('finance-payment') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('finance-payment')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('finance.dashboard') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('finance.dashboard') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Dashboard</a>
                    <a href="{{ route('finance.payments') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('finance.payments') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Payment</a>
                    <a href="{{ route('finance.tariffs') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('finance.tariffs') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Tariffs</a>
                    <a href="{{ route('finance.applicant-requests') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('finance.applicant-requests') || Route::is('finance.applicant-request') || Route::is('civil-registration.payment-request*') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Applicant Request</a>
                </div>
            </div>
        @endif

        @if($canLawyer)
            <!-- Lawyers -->
            <div class="space-y-1">
                <button @click="toggleMenu('lawyer')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-person-badge-fill text-lg"></i>
                    <span class="text-left leading-tight">lawyer management</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('lawyer') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('lawyer')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('lawyer.index') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('lawyer.index') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Lawyers </a>
                </div>
            </div>
        @endif
        @if($canarchive)
            <!-- archive -->
            <div class="space-y-1">
                <button @click="toggleMenu('archive')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-archive text-lg"></i>
                    <span class="text-left leading-tight">Archive</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('archive') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('archive')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    <a href="{{ route('archive.index') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('archive.index') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-mic text-lg"></i> Shaabadda Mudeymaha</a>
                    <a href="{{ route('archive.judgments') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('archive.judgments') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-gavel text-lg"></i> Shaabadda Xukunnada</a>
                </div>
            </div>
        @endif
        @if($hasAdminSection)
            <!-- System Administration -->
            <div class="space-y-1">
                <button @click="toggleMenu('admin')"
                    class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                    >
                    <i class="bi bi-gear text-lg"></i>
                    <span class="text-left leading-tight">System Admin</span>
                    <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                        :class="isOpen('admin') ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="isOpen('admin')" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                    class="space-y-1 overflow-hidden transition-all duration-300">
                    @if($canStaff)
                        <a href="{{ route('employee.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('employee.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Staff Registry</a>
                    @endif
                    @if($canStateRegion)
                        <a href="{{ route('state-region.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('state-region.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> State / Region</a>
                    @endif
                    @if($canCity)
                        <a href="{{ route('city.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('city.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> City</a>
                    @endif
                    @if($canPublicInstitution)
                        <a href="{{ route('public-institution.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('public-institution.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Public Institutions</a>
                    @endif
                    @if($canCaseType)
                        <a href="{{ route('case-type.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('case-type.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Case Types</a>
                    @endif
                    @if($canCaseCategory)
                        <a href="{{ route('case-category.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('case-category.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Case Category</a>
                    @endif
                    @if($canAttorneyDepartments)
                        <a href="{{ route('attorney-departments.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('attorney-departments.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Attorney Departments</a>
                    @endif
                    @if($canStatusProc)
                        <a href="{{ route('status-process.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('status-process.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Status Process</a>
                    @endif
                    @if($canDocAttach)
                        <a href="{{ route('document-attachment.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('document-attachment.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Document Attachment</a>
                    @endif
                    @if($canRoles)
                        <a href="{{ route('roles.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('roles.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Roles</a>
                    @endif
                    @if($canCourt)
                        <a href="{{ route('court.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('court.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Judiciary Registration</a>
                    @endif
                    @if($canAccessLogin)
                        <a href="{{ route('employee.access-login') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('employee.access-login') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Access login</a>
                    @endif
                    @if($canRoles)
                        <a href="{{ route('role-permission.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('role-permission.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Role & Permission</a>
                        <a href="{{ route('groups.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('groups.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Group Role</a>
                    @endif
                    @if($canPlatformAdmin)
                        <a href="{{ route('platform.dashboard') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('platform.dashboard') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Global Dashboard</a>
                        <a href="{{ route('institutions.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('institutions.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Institution Management</a>
                        <a href="{{ route('audit-logs.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('audit-logs.*') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Audit Logs</a>
                    @endif
                    @if($canBackup)
                        <a href="{{ route('backup.index') }}"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('backup.index') ? 'text-white bg-white/5' : '' }}">
                            <i class="bi bi-plus text-lg"></i> Backup & Restore</a>
                    @endif
                    @if($canAudit)
                        <a href="#"
                            class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200">
                            <i class="bi bi-plus text-lg"></i> Audit Logs</a>
                    @endif
                    <a href="{{ route('notifications.page') }}"
                        class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('notifications.page') ? 'text-white bg-white/5' : '' }}">
                        <i class="bi bi-plus text-lg"></i> Notifications</a>
                </div>
            </div>
        @endif

        <!-- Support -->
        <div class="space-y-1">
            <button @click="toggleMenu('support')"
                class="flex items-center gap-2 px-3 py-3 text-white/70 hover:bg-white/5 hover:text-white rounded-xl font-semibold text-[14px] text-left transition-all duration-200 w-full"
                >
                <i class="bi bi-headset text-lg"></i>
                <span>Support</span>
                <i class="bi bi-chevron-down ml-auto text-[10px] transition-transform duration-200"
                    :class="isOpen('support') ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="isOpen('support')" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0"
                class="space-y-1 overflow-hidden transition-all duration-300">
                <a href="{{ route('support-tickets.index') }}"
                    class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('support-tickets.index') ? 'text-white bg-white/5' : '' }}">
                    <i class="bi bi-headset text-lg"></i> My Tickets</a>
                <a href="{{ route('support-tickets.create') }}"
                    class="flex items-center gap-3 pl-12 pr-4 py-2 text-white/50 hover:text-white text-[13px] font-medium transition-all duration-200 {{ Route::is('support-tickets.create') ? 'text-white bg-white/5' : '' }}">
                    <i class="bi bi-plus-circle text-lg"></i> Create Ticket</a>
            </div>
        </div>

    </div>

    </div>

    <!-- Logout -->
    <div class="px-4 py-4 border-t border-white/5 bg-primary/50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-2 text-accent hover:text-white hover:bg-white/5 rounded-xl font-bold text-[14px] transition-all duration-200 cursor-pointer">
                <i class="bi bi-power text-lg"></i>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>

<script>
    (function () {
        const badges = document.querySelectorAll('[data-notif-badge]');
        if (!badges.length) return;

        function refreshSidebarBadges() {
            fetch('{{ route("notifications.index") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    const counts = {};
                    (data.notifications || []).forEach(n => {
                        if (n.read_at) return;
                        const type = n.data?.type;
                        if (!type) return;
                        counts[type] = (counts[type] || 0) + 1;
                    });
                    badges.forEach(el => {
                        const count = counts[el.dataset.notifBadge] || 0;
                        el.textContent = count > 9 ? '9+' : count;
                        el.style.display = count > 0 ? 'flex' : 'none';
                    });
                }).catch(() => { });
        }

        refreshSidebarBadges();
        setInterval(refreshSidebarBadges, 60000);
    })();
</script>