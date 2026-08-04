<?php

namespace App\Http\Controllers;

use App\Models\AttorneyCase;
use App\Models\AttorneyCaseAccused;
use App\Models\AttorneyCaseComplainant;
use App\Models\AttorneyCaseDocument;
use App\Models\AttorneyCaseEvidenceItem;
use App\Models\AttorneyCaseLegalProvision;
use App\Models\AttorneyCaseParty;
use App\Models\AttorneyCaseSourceType;
use App\Models\AttorneyCaseVictim;
use App\Models\AttorneyCaseWitness;
use App\Models\AttorneyVictimRelationshipType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttorneyCaseController extends Controller
{
    public const STATUSES = ['Diiwaan', 'Gal Ku Qoris', 'Baaritaan Socda', 'Hubin', 'La Eedeeyay', 'Ku Jira Maxkamadda', 'La Xukumay', 'La Sii Daayay', 'La Diiday', 'La Xiray'];
    public const PRIORITIES = ['Hoose', 'Dhexdhexaad', 'Sarreeya', 'Degdeg ah'];
    public const PARTY_ROLES = ['Eedeysane', 'Dhibbane', 'Markhaati'];
    public const CASE_TYPES = ['Shaqsi', 'Shirkad', "Hay'ad", 'Dawladeed', 'Qaraan'];

    /**
     * Maps each Ciqaabta sub-case name to its matching "Qodobka N – title"
     * legal-provision string, since both live on the same case_categories
     * row (sub_case + rule + description). Lets the create form
     * auto-select the corresponding article when a sub-case is chosen.
     */
    protected function offenseSubcaseArticleMap()
    {
        return \App\Models\CaseCategory::where('case_name', 'Ciqaabta')
            ->whereNotNull('sub_case')
            ->whereNotNull('rule')
            ->get(['sub_case', 'rule', 'description'])
            ->mapWithKeys(fn ($c) => [$c->sub_case => $c->rule . ' – ' . $c->description]);
    }

    public function index(Request $request)
    {
        $base = AttorneyCase::query();

        $stats = [
            'open'                => (clone $base)->where('status', 'Diiwaan')->count(),
            'under_investigation' => (clone $base)->where('status', 'Baaritaan Socda')->count(),
            'complete'            => (clone $base)->whereIn('status', ['La Eedeeyay', 'Ku Jira Maxkamadda', 'La Xukumay', 'La Sii Daayay'])->count(),
            'closed'              => (clone $base)->whereIn('status', ['La Diiday', 'La Xiray'])->count(),
        ];

        $query = (clone $base)->with('complainants')->orderByDesc('ACID');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('case_type') && $request->case_type !== 'all') {
            $query->where('case_type', $request->case_type);
        }

        $perPage = $this->resolvePerPage($request);
        $cases   = $query->paginate($perPage)->withQueryString();

        return view('attorney.Direct_Complaint.direct_complaint_registration', [
            'cases'      => $cases,
            'stats'      => $stats,
            'statuses'   => self::STATUSES,
            'priorities' => self::PRIORITIES,
            'caseTypes'  => self::CASE_TYPES,
        ]);
    }

    public function tracking(Request $request)
    {
        $baseQuery = AttorneyCase::query();

        $activeStatuses = ['Diiwaan', 'Baaritaan Socda', 'La Eedeeyay', 'Ku Jira Maxkamadda'];
        $doneStatuses   = ['La Xukumay', 'La Sii Daayay'];
        $closedStatuses = ['La Diiday', 'La Xiray'];

        $stats = [
            'total'   => (clone $baseQuery)->count(),
            'active'  => (clone $baseQuery)->whereIn('status', $activeStatuses)->count(),
            'pending' => (clone $baseQuery)->where('status', 'Baaritaan Socda')->count(),
            'done'    => (clone $baseQuery)->whereIn('status', $doneStatuses)->count(),
            'closed'  => (clone $baseQuery)->whereIn('status', $closedStatuses)->count(),
        ];

        $query = (clone $baseQuery)->with(['complainants', 'prosecutorAssignments.prosecutor'])->orderByDesc('ACID');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('complainants', function ($cq) use ($search) {
                      $cq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('victims', function ($vq) use ($search) {
                      $vq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('mother_name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('accused', function ($aq) use ($search) {
                      $aq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('mother_name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('witnesses', function ($wq) use ($search) {
                      $wq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('mother_name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = $this->resolvePerPage($request);
        $records = $query->paginate($perPage)->withQueryString();

        return view('attorney.Direct_Complaint.direct_complaint_tracking', [
            'records'     => $records,
            'stats'       => $stats,
            'allStatuses' => self::STATUSES,
        ]);
    }

    public function calendar(Request $request)
    {
        $proceedings = \App\Models\AttorneyProceeding::with('attorneyCase')
            ->whereHas('attorneyCase')
            ->orderBy('hearing_date')
            ->get();

        $calendarEvents = $proceedings->map(function ($p) {
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
                'url'   => route('attorney-cases.show', $p->attorney_case_id),
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

        $stats = [
            'total'       => $proceedings->count(),
            'thisMonth'   => $proceedings->filter(fn ($p) => $p->hearing_date->isSameMonth(now()))->count(),
            'scheduled'   => $proceedings->where('status', 'Scheduled')->count(),
            'completed'   => $proceedings->where('status', 'Completed')->count(),
            'postponed'   => $proceedings->whereIn('status', ['Postponed', 'Cancelled'])->count(),
        ];

        $upcoming = $proceedings->filter(fn ($p) => $p->hearing_date->isToday() || $p->hearing_date->isFuture())
            ->sortBy('hearing_date')
            ->take(10)
            ->values();

        return view('attorney.Direct_Complaint.direct_complaint_calendar', [
            'calendarEvents' => $calendarEvents,
            'stats'          => $stats,
            'upcoming'       => $upcoming,
        ]);
    }

    public function assign(Request $request)
    {
        return view('attorney.Direct_Complaint.direct_complaint_assign');
    }

    public function socodsinta(Request $request)
    {
        $baseQuery = AttorneyCase::query();

        $stats = [
            'total'      => (clone $baseQuery)->count(),
            'assigned'   => (clone $baseQuery)->whereHas('prosecutorAssignments')->count(),
            'unassigned' => (clone $baseQuery)->whereDoesntHave('prosecutorAssignments')->count(),
        ];

        $query = (clone $baseQuery)->with(['complainants', 'prosecutorAssignments.prosecutor'])->orderByDesc('ACID');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('complainants', function ($cq) use ($search) {
                      $cq->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('assign_status')) {
            if ($request->assign_status === 'assigned') {
                $query->whereHas('prosecutorAssignments');
            } elseif ($request->assign_status === 'unassigned') {
                $query->whereDoesntHave('prosecutorAssignments');
            }
        }

        if ($request->filled('case_type')) {
            $query->where('case_type', $request->case_type);
        }

        $perPage = $this->resolvePerPage($request);
        $cases   = $query->paginate($perPage)->withQueryString();

        return view('attorney.Conclusion.direct_complaint_socodsinta', [
            'cases'     => $cases,
            'stats'     => $stats,
            'caseTypes' => self::CASE_TYPES,
        ]);
    }

    public function create(Request $request)
    {
        return view('attorney.Direct_Complaint.direct_complaint_create', [
            'priorities'          => self::PRIORITIES,
            'caseTypes'           => self::CASE_TYPES,
            'institutions'        => \App\Models\PublicInstitution::orderBy('name')->get(),
            'relationshipTypes'   => AttorneyVictimRelationshipType::orderBy('name')->get(),
            'sourceTypes'         => AttorneyCaseSourceType::orderBy('name')->get(),
            'regions'             => \App\Models\StateRegion::with(['cities' => function ($q) {
                $q->orderBy('city_name');
            }])->orderBy('state_name')->get(),
            'articles'            => \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->whereNotNull('rule')->orderByRaw('CAST(SUBSTRING_INDEX(rule, " ", -1) AS UNSIGNED)')->get(),
            'offenseSubcases'     => \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->whereNotNull('sub_case')->distinct()->orderBy('sub_case')->pluck('sub_case'),
            'offenseSubcaseArticles' => $this->offenseSubcaseArticleMap(),
            'currentEmployee'     => $request->user()->employee ?? null,
            'nextCaseNumber'      => AttorneyCase::nextCaseNumber(),
            'nextCaseNumbers'     => AttorneyCase::nextCaseNumberPreviewMap(),
        ]);
    }

    public function storeSourceType(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150|unique:attorney_case_source_types,name',
            'description' => 'nullable|string',
        ]);

        $value = \Illuminate\Support\Str::slug($data['name']);
        $base  = $value;
        $n     = 1;
        while (AttorneyCaseSourceType::where('value', $value)->exists()) {
            $value = $base . '-' . (++$n);
        }

        $type = AttorneyCaseSourceType::create([
            'name'        => $data['name'],
            'value'       => $value,
            'description' => $data['description'] ?? null,
        ]);

        return response()->json($type);
    }

    public function storeRelationshipType(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150|unique:attorney_victim_relationship_types,name',
            'description' => 'nullable|string',
        ]);

        $value = \Illuminate\Support\Str::slug($data['name']);
        $base  = $value;
        $n     = 1;
        while (AttorneyVictimRelationshipType::where('value', $value)->exists()) {
            $value = $base . '-' . (++$n);
        }

        $type = AttorneyVictimRelationshipType::create([
            'name'        => $data['name'],
            'value'       => $value,
            'description' => $data['description'] ?? null,
        ]);

        return response()->json($type);
    }

    protected function complaintValidationRules(): array
    {
        return [
            'source_of_case'    => 'nullable|string|max:50',
            'case_type'         => 'nullable|in:' . implode(',', self::CASE_TYPES),
            'title'             => 'nullable|string|max:255',
            'offense_type'      => 'required|string',
            'reporting_agency'  => 'nullable|string|max:150',
            'priority'          => 'required|in:' . implode(',', self::PRIORITIES),
            'summary'           => 'required|string',
            'incident_date'     => 'required|date',
            'incident_time'     => 'nullable|string|max:20',
            'incident_location' => 'required|string|max:255',
            'relief_sought'     => 'required|string',
            'declaration_accepted'      => 'required|accepted',
            'declaration_signed_by'     => 'required|string|max:150',
            'declaration_date'          => 'required|date',
            'declaration_signature_data' => 'nullable|string',

            'complainants'                          => 'nullable|array',
            'complainants.*.type'                   => 'nullable|string',
            'complainants.*.full_name'              => 'required_with:complainants|string|max:150',
            'complainants.*.phone_number'           => 'nullable|string|max:30',
            'complainants.*.email'                  => 'nullable|email|max:150',
            'complainants.*.date_of_birth'          => 'nullable|date',
            'complainants.*.nationality'            => 'nullable|string|max:100',
            'complainants.*.occupation'             => 'nullable|string|max:100',
            'complainants.*.address'                => 'nullable|string',
            'complainants.*.license_number'         => 'nullable|string|max:50',
            'complainants.*.representative_name'    => 'nullable|string|max:150',
            'complainants.*.representative_title'   => 'nullable|string|max:100',
            'complainants.*.lawyer_name'            => 'nullable|string|max:150',
            'complainants.*.lawyer_license_no'      => 'nullable|string|max:50',

            'victims'                          => 'nullable|array',
            'victims.*.type'                   => 'required_with:victims|string|in:Shakhsi,Qaranka',
            'victims.*.full_name'              => 'required_with:victims|string|max:150',
            'victims.*.mother_name'            => 'nullable|string|max:150',
            'victims.*.phone_number'           => 'nullable|string|max:30',
            'victims.*.date_of_birth'          => 'nullable|date',
            'victims.*.gender'                 => 'nullable|string|max:20',
            'victims.*.place_of_birth'         => 'nullable|string|max:150',
            'victims.*.district'               => 'nullable|string|max:100',
            'victims.*.region'                 => 'nullable|string|max:100',
            'victims.*.address'                => 'nullable|string',
            'victims.*.id_number'              => 'nullable|string|max:50',
            'victims.*.impact_description'     => 'nullable|string',
            'victims.*.custodian_full_name'    => 'nullable|string|max:150',
            'victims.*.custodian_relationship' => 'nullable|string|max:100',
            'victims.*.custodian_id_number'    => 'nullable|string|max:50',
            'victims.*.custodian_phone_number' => 'nullable|string|max:30',
            'victims.*.custodian_address'      => 'nullable|string',
            'victims.*.custodian_email'        => 'nullable|email|max:150',

            'accused'                       => 'nullable|array',
            'accused.*.full_name'           => 'required_with:accused|string|max:150',
            'accused.*.mother_name'         => 'nullable|string|max:150',
            'accused.*.phone_number'        => 'nullable|string|max:30',
            'accused.*.date_of_birth'       => 'nullable|date',
            'accused.*.gender'              => 'nullable|string|max:20',
            'accused.*.place_of_birth'      => 'nullable|string|max:150',
            'accused.*.district'            => 'nullable|string|max:100',
            'accused.*.region'              => 'nullable|string|max:100',
            'accused.*.address'             => 'nullable|string',
            'accused.*.id_number'           => 'nullable|string|max:50',
            'accused.*.additional_details'  => 'nullable|string',
            'accused.*.custodian_full_name'    => 'nullable|string|max:150',
            'accused.*.custodian_relationship' => 'nullable|string|max:100',
            'accused.*.custodian_id_number'    => 'nullable|string|max:50',
            'accused.*.custodian_phone_number' => 'nullable|string|max:30',
            'accused.*.custodian_address'      => 'nullable|string',
            'accused.*.custodian_email'        => 'nullable|email|max:150',

            'evidence'                   => 'nullable|array',
            'evidence.*.existing_id'     => 'nullable|integer',
            'evidence.*.evidence_type'   => 'nullable|string|max:50',
            'evidence.*.description'     => 'nullable|string',
            'evidence.*.collected_date'  => 'nullable|date',
            'evidence.*.file'            => 'nullable|file|max:10240',

            'witnesses'                  => 'nullable|array',
            'witnesses.*.full_name'      => 'required_with:witnesses|string|max:150',
            'witnesses.*.mother_name'    => 'nullable|string|max:150',
            'witnesses.*.date_of_birth'  => 'nullable|date',
            'witnesses.*.gender'         => 'nullable|string|max:20',
            'witnesses.*.place_of_birth' => 'nullable|string|max:150',
            'witnesses.*.district'       => 'nullable|string|max:100',
            'witnesses.*.region'         => 'nullable|string|max:100',
            'witnesses.*.id_number'      => 'nullable|string|max:50',
            'witnesses.*.nationality'    => 'nullable|string|max:100',
            'witnesses.*.occupation'     => 'nullable|string|max:100',
            'witnesses.*.phone_number'   => 'nullable|string|max:30',
            'witnesses.*.address'        => 'nullable|string',
            'witnesses.*.statement'      => 'nullable|string',

            'legal_provisions'              => 'nullable|array',
            'legal_provisions.*.provision'  => 'nullable|string|max:255',
        ];
    }

    protected function applyCustodianMinorRule($validator, Request $request): void
    {
        $validator->after(function ($validator) use ($request) {
            $requireCustodianForMinors = function ($field) use ($validator, $request) {
                foreach ($request->input($field, []) as $i => $row) {
                    if (($row['type'] ?? null) === 'Qaranka') {
                        continue;
                    }

                    $dob = $row['date_of_birth'] ?? null;
                    if (!$dob) {
                        continue;
                    }

                    try {
                        $isMinor = \Carbon\Carbon::parse($dob)->age < 18;
                    } catch (\Exception $e) {
                        continue;
                    }

                    if (!$isMinor) {
                        continue;
                    }

                    if (empty($row['custodian_full_name'])) {
                        $validator->errors()->add("{$field}.{$i}.custodian_full_name", 'Magaca masuulka/waalidka waa lagama maarmaan haddii uu ilmo yahay.');
                    }
                    if (empty($row['custodian_relationship'])) {
                        $validator->errors()->add("{$field}.{$i}.custodian_relationship", 'Xiriirka masuulka waa lagama maarmaan haddii uu ilmo yahay.');
                    }
                    if (empty($row['custodian_phone_number'])) {
                        $validator->errors()->add("{$field}.{$i}.custodian_phone_number", 'Lambarka taleefanka masuulka waa lagama maarmaan haddii uu ilmo yahay.');
                    }
                }
            };

            $requireCustodianForMinors('victims');
            $requireCustodianForMinors('accused');
        });
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $this->complaintValidationRules());
        $this->applyCustodianMinorRule($validator, $request);

        $data = $validator->validate();

        $user = $request->user();

        $signaturePath = null;
        if ($request->filled('declaration_signature_data')) {
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('declaration_signature_data'));
            $decoded = base64_decode($base64);
            if ($decoded) {
                $signaturePath = 'attorney_uploads/declaration-signatures/' . uniqid('sig_', true) . '.png';
                Storage::disk('public')->put($signaturePath, $decoded);
            }
        }

        $case = AttorneyCase::create([
            'institution_id'        => $user->institution_id,
            'title'                 => $data['title'] ?? $data['offense_type'],
            'offense_type'          => $data['offense_type'],
            'date_reported'         => now()->toDateString(),
            'source_of_case'        => $data['source_of_case'] ?? null,
            'case_type'             => $data['case_type'] ?? null,
            'reporting_agency'      => $data['reporting_agency'] ?? null,
            'priority'              => $data['priority'],
            'summary'               => $data['summary'],
            'incident_date'         => $data['incident_date'],
            'incident_time'         => $data['incident_time'] ?? null,
            'incident_location'     => $data['incident_location'],
            'relief_sought'         => $data['relief_sought'],
            'declaration_accepted'  => true,
            'declaration_signed_by' => $data['declaration_signed_by'],
            'declaration_date'      => $data['declaration_date'],
            'declaration_signature' => $signaturePath,
            'status'                => 'Diiwaan',
            'added_by'              => $user->name ?? 'Admin',
        ]);

        foreach ($data['complainants'] ?? [] as $row) {
            AttorneyCaseComplainant::create(array_merge(['attorney_case_id' => $case->ACID], $row));
        }

        foreach ($data['victims'] ?? [] as $row) {
            AttorneyCaseVictim::create(array_merge(['attorney_case_id' => $case->ACID], $row));
        }

        foreach ($data['accused'] ?? [] as $row) {
            AttorneyCaseAccused::create(array_merge(['attorney_case_id' => $case->ACID], $row));
        }

        foreach ($request->input('evidence', []) as $index => $row) {
            $filePath = null;
            if ($request->hasFile("evidence.{$index}.file")) {
                $filePath = $request->file("evidence.{$index}.file")->store('attorney/evidence', 'public');
            }

            AttorneyCaseEvidenceItem::create([
                'attorney_case_id' => $case->ACID,
                'evidence_type'    => $row['evidence_type'] ?? 'Document',
                'description'      => $row['description'] ?? null,
                'collected_date'   => $row['collected_date'] ?? null,
                'file_path'        => $filePath,
            ]);
        }

        foreach ($data['witnesses'] ?? [] as $row) {
            AttorneyCaseWitness::create(array_merge(['attorney_case_id' => $case->ACID], $row));
        }

        foreach ($data['legal_provisions'] ?? [] as $row) {
            if (empty($row['provision'])) {
                continue;
            }

            AttorneyCaseLegalProvision::create([
                'attorney_case_id' => $case->ACID,
                'provision'        => $row['provision'],
            ]);
        }

        $case->activities()->create([
            'user_id'     => $user->id,
            'type'        => 'created',
            'description' => "{$user->name} diiwaan geliyay dacwadan.",
        ]);

        return redirect()->route('attorney-cases.show', $case->ACID)->with('success', 'Dacwadda si guul leh ayaa loo diiwaan geliyay.');
    }

    public function show(Request $request, $id)
    {
        $case = AttorneyCase::with(['complainants', 'victims', 'accused', 'parties', 'documents', 'activities.user', 'legalProvisions', 'evidenceItems', 'witnesses', 'reviews.department', 'reviews.user', 'prosecutorAssignments.prosecutor'])->findOrFail($id);

        return view('attorney.Direct_Complaint.direct_complaint_information', [
            'case'     => $case,
            'statuses' => self::STATUSES,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $case = AttorneyCase::with([
            'complainants', 'victims', 'accused', 'evidenceItems', 'witnesses', 'legalProvisions',
        ])->findOrFail($id);

        return view('attorney.Direct_Complaint.direct_complaint_create', [
            'case'                => $case,
            'priorities'          => self::PRIORITIES,
            'statuses'            => self::STATUSES,
            'caseTypes'           => self::CASE_TYPES,
            'institutions'        => \App\Models\PublicInstitution::orderBy('name')->get(),
            'relationshipTypes'   => AttorneyVictimRelationshipType::orderBy('name')->get(),
            'sourceTypes'         => AttorneyCaseSourceType::orderBy('name')->get(),
            'regions'             => \App\Models\StateRegion::with(['cities' => function ($q) {
                $q->orderBy('city_name');
            }])->orderBy('state_name')->get(),
            'articles'            => \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->whereNotNull('rule')->orderByRaw('CAST(SUBSTRING_INDEX(rule, " ", -1) AS UNSIGNED)')->get(),
            'offenseSubcases'     => \App\Models\CaseCategory::where('case_name', 'Ciqaabta')->whereNotNull('sub_case')->distinct()->orderBy('sub_case')->pluck('sub_case'),
            'offenseSubcaseArticles' => $this->offenseSubcaseArticleMap(),
            'currentEmployee'     => $request->user()->employee ?? null,
            'nextCaseNumber'      => $case->case_number,
        ]);
    }

    public function update(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $rules             = $this->complaintValidationRules();
        $rules['status']   = 'required|in:' . implode(',', self::STATUSES);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
        $this->applyCustodianMinorRule($validator, $request);

        $data = $validator->validate();

        $signaturePath = $case->declaration_signature;
        if ($request->filled('declaration_signature_data')) {
            $base64  = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('declaration_signature_data'));
            $decoded = base64_decode($base64);
            if ($decoded) {
                $signaturePath = 'attorney_uploads/declaration-signatures/' . uniqid('sig_', true) . '.png';
                Storage::disk('public')->put($signaturePath, $decoded);
            }
        }

        $oldStatus = $case->status;

        $case->update([
            'title'                 => $data['title'] ?? $data['offense_type'],
            'offense_type'          => $data['offense_type'],
            'source_of_case'        => $data['source_of_case'] ?? null,
            'case_type'             => $data['case_type'] ?? null,
            'reporting_agency'      => $data['reporting_agency'] ?? null,
            'priority'              => $data['priority'],
            'status'                => $data['status'],
            'summary'               => $data['summary'],
            'incident_date'         => $data['incident_date'],
            'incident_time'         => $data['incident_time'] ?? null,
            'incident_location'     => $data['incident_location'],
            'relief_sought'         => $data['relief_sought'],
            'declaration_signed_by' => $data['declaration_signed_by'],
            'declaration_date'      => $data['declaration_date'],
            'declaration_signature' => $signaturePath,
        ]);

        $case->complainants()->delete();
        foreach ($data['complainants'] ?? [] as $row) {
            AttorneyCaseComplainant::create(array_merge(['attorney_case_id' => $case->ACID], $row));
        }

        $case->victims()->delete();
        foreach ($data['victims'] ?? [] as $row) {
            AttorneyCaseVictim::create(array_merge(['attorney_case_id' => $case->ACID], $row));
        }

        $case->accused()->delete();
        foreach ($data['accused'] ?? [] as $row) {
            AttorneyCaseAccused::create(array_merge(['attorney_case_id' => $case->ACID], $row));
        }

        $case->witnesses()->delete();
        foreach ($data['witnesses'] ?? [] as $row) {
            AttorneyCaseWitness::create(array_merge(['attorney_case_id' => $case->ACID], $row));
        }

        $case->legalProvisions()->delete();
        foreach ($data['legal_provisions'] ?? [] as $row) {
            if (empty($row['provision'])) {
                continue;
            }

            AttorneyCaseLegalProvision::create([
                'attorney_case_id' => $case->ACID,
                'provision'        => $row['provision'],
            ]);
        }

        $keptEvidenceIds = [];
        foreach ($request->input('evidence', []) as $index => $row) {
            $existingId = $row['existing_id'] ?? null;
            $filePath   = null;

            if ($request->hasFile("evidence.{$index}.file")) {
                $filePath = $request->file("evidence.{$index}.file")->store('attorney/evidence', 'public');
            }

            $item = $existingId ? $case->evidenceItems()->find($existingId) : null;

            if ($item) {
                $item->update([
                    'evidence_type'  => $row['evidence_type'] ?? 'Document',
                    'description'    => $row['description'] ?? null,
                    'collected_date' => $row['collected_date'] ?? null,
                    'file_path'      => $filePath ?: $item->file_path,
                ]);
            } else {
                $item = AttorneyCaseEvidenceItem::create([
                    'attorney_case_id' => $case->ACID,
                    'evidence_type'    => $row['evidence_type'] ?? 'Document',
                    'description'      => $row['description'] ?? null,
                    'collected_date'   => $row['collected_date'] ?? null,
                    'file_path'        => $filePath,
                ]);
            }

            $keptEvidenceIds[] = $item->id;
        }
        $case->evidenceItems()->whereNotIn('id', $keptEvidenceIds ?: [0])->delete();

        if ($oldStatus !== $data['status']) {
            $case->activities()->create([
                'user_id'     => $request->user()->id,
                'type'        => 'status_changed',
                'description' => "Xaaladda dacwadda waxaa laga beddelay \"{$oldStatus}\" una beddelay \"{$data['status']}\".",
            ]);
        }

        $case->activities()->create([
            'user_id'     => $request->user()->id,
            'type'        => 'updated',
            'description' => "{$request->user()->name} wax ka beddelay faahfaahinta dacwadan.",
        ]);

        return redirect()->route('attorney-cases.show', $case->ACID)->with('success', 'Dacwadda si guul leh ayaa loo cusboonaysiiyay.');
    }

    public function destroy($id)
    {
        $case = AttorneyCase::findOrFail($id);
        $case->delete();

        return redirect()->route('attorney-cases.index')->with('success', 'Dacwadda waa la tirtiray.');
    }

    public function addParty(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'party_role'     => 'required|string',
            'full_name'      => 'required|string|max:150',
            'contact_number' => 'nullable|string|max:30',
            'national_id'    => 'nullable|string|max:50',
            'address'        => 'nullable|string',
        ]);

        $case->parties()->create($data);

        return back()->with('success', 'Dhinaca waa lagu daray dacwadda.');
    }

    public function removeParty($id, $partyId)
    {
        $case = AttorneyCase::findOrFail($id);
        $case->parties()->where('id', $partyId)->delete();

        return back()->with('success', 'Dhinaca waa laga saaray dacwadda.');
    }

    public function addDocument(Request $request, $id)
    {
        $case = AttorneyCase::findOrFail($id);

        $data = $request->validate([
            'document_name'  => 'required|string|max:150',
            'document_date'  => 'nullable|date',
            'file'           => 'required|file|max:10240',
        ]);

        $path = $request->file('file')->store('attorney/documents', 'public');

        AttorneyCaseDocument::create([
            'attorney_case_id' => $case->ACID,
            'document_name'    => $data['document_name'],
            'document_date'    => $data['document_date'] ?? null,
            'file_path'        => $path,
            'uploaded_by'      => $request->user()->name ?? 'Admin',
        ]);

        return back()->with('success', 'Dukumeentiga waa lagu daray dacwadda.');
    }

    public function removeDocument($id, $documentId)
    {
        $case = AttorneyCase::findOrFail($id);
        $document = $case->documents()->where('id', $documentId)->first();

        if ($document) {
            Storage::disk('public')->delete($document->file_path);
            $document->delete();
        }

        return back()->with('success', 'Dukumeentiga waa laga saaray dacwadda.');
    }
}
