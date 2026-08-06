@extends('admin.admin_master')
@section('page_title', 'Diiwanka Dacwadaha — Maxkamadda Rafcaanka')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-wrap" x-data="civilRegManager">

        {{-- Page Header --}}
        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-file-earmark-ruled"></i></div>
                <div>
                    <h1 class="page-title">Diiwanka Dacwadaha</h1>
                    <p class="page-sub">Maxkamadda Rafcaanka — Diiwaangelinta dacwadaha madaniga</p>
                </div>
            </div>
            <button @click="openModal()" class="btn-submit" style="display:inline-flex;align-items:center;gap:.4rem">
                <i class="bi bi-plus-lg"></i> Kudar Dacwad Cusub
            </button>
        </div>

        {{-- KPI Cards --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-primary"><i class="bi bi-file-earmark-ruled"></i></div>
                <div>
                    <p class="kpi-lbl">Wadarta Guud</p>
                    <h3 class="kpi-val">{{ $allRecords->count() }}</h3>
                    <p class="kpi-sub-primary">Dhamaan Wadarta Diiwanka</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-gold"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <p class="kpi-lbl">Bishan</p>
                    <h3 class="kpi-val">
                        {{ $allRecords->filter(fn($r) => date('Y-m', strtotime($r->OpenDate ?? '')) == date('Y-m'))->count() }}
                    </h3>
                    <p class="kpi-sub-gold">{{ date('F Y') }}</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-primary"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <p class="kpi-lbl">Dacwadaha Socda</p>
                    <h3 class="kpi-val">
                        {{ $allRecords->filter(fn($r) => in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay']))->count() }}
                    </h3>
                    <p class="kpi-sub-primary">Socda</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-gold"><i class="bi bi-check2-all"></i></div>
                <div>
                    <p class="kpi-lbl">Dacwadaha Xiran</p>
                    <h3 class="kpi-val">{{ $allRecords->filter(fn($r) => $r->Status === 'Closed')->count() }}</h3>
                    <p class="kpi-sub-gold">Xiran</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="data-card">

            {{-- Search & Filter --}}
            <div class="reg-filter">
                <form action="{{ route('appeal-civil-registration.index') }}" method="GET"
                      style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:center;width:100%">
                    <div class="reg-search-wrap" style="flex:1;min-width:200px">
                        <i class="bi bi-search reg-search-ico"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Raadi lambarka faylka, diiwanka..."
                               class="reg-search-inp">
                    </div>
                    <div class="inp-wrap" style="min-width:160px">
                        <select name="status" onchange="this.form.submit()" class="reg-filter-sel">
                            <option value="">Dhammaan Xaaladaha</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st->name }}" {{ request('status') == $st->name ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="inp-wrap" style="min-width:160px">
                        <select name="case_type" onchange="this.form.submit()" class="reg-filter-sel">
                            <option value="">Dhammaan Noocyada</option>
                            @foreach($caseTypes as $ct)
                                <option value="{{ $ct->case_name }}" {{ request('case_type') == $ct->case_name ? 'selected' : '' }}>
                                    {{ $ct->case_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-submit" style="display:inline-flex;align-items:center;gap:.4rem">
                        <i class="bi bi-search"></i> Raadi
                    </button>
                    @if(request()->anyFilled(['search', 'status', 'case_type']))
                        <a href="{{ route('appeal-civil-registration.index') }}" class="btn-cancel">
                            <i class="bi bi-x-circle"></i> Nadiifi
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table bar --}}
            <div class="reg-table-bar">
                <div class="reg-table-bar-left">
                    <i class="bi bi-table"></i>
                    <span class="reg-table-bar-title">Diiwaanka Dacwadaha</span>
                </div>
                <span class="reg-table-count">{{ $records->count() }} diiwaan</span>
            </div>

            {{-- Table --}}
            <div class="data-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:3.5rem">T.T</th>
                            <th>Gal Lambar</th>
                            <th>Nooca Dacwada</th>
                            <th>Hoosaadka</th>
                            <th>Taariikhda</th>
                            <th>Warqadaha</th>
                            <th>Nuxurka</th>
                            <th class="center">Xaalada</th>
                            <th class="center" style="width:9rem">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $i => $r)
                            @php
                                $isActive = in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                $isClosed = $r->Status === 'Closed';
                                $sBg    = $isActive ? 'rgba(34,197,94,.12)'  : ($isClosed ? 'rgba(239,68,68,.1)'  : 'rgba(240,180,60,.12)');
                                $sColor = $isActive ? '#15803d'              : ($isClosed ? '#b91c1c'             : '#C07E15');
                            @endphp
                            <tr>
                                <td><span class="td-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span></td>
                                <td><span class="badge-file">{{ $r->FileNo }}</span></td>
                                <td><span class="badge-type">{{ $r->CaseType }}</span></td>
                                <td class="td-text">{{ $r->SubCase ?: '—' }}</td>
                                <td class="td-date">{{ \Carbon\Carbon::parse($r->OpenDate)->format('d/m/Y') }}</td>
                                <td class="td-text">{{ $r->NumberLetter ?: '—' }}</td>
                                <td class="td-text">{{ Str::limit($r->Remarks ?? '—', 45) }}</td>
                                <td class="center">
                                    <span class="status-pill" style="background:{{ $sBg }};color:{{ $sColor }}">
                                        {{ $r->Status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="td-actions">
                                        <a href="{{ route('appeal-civil-registration.supporting', $r->ACID) }}"
                                           title="Supporting Details" class="act-btn act-btn-edit">
                                            <i class="bi bi-card-list"></i>
                                        </a>
                                        <a href="{{ route('appeal-civil-registration.show', $r->ACID) }}"
                                           title="Arag Dacwada" class="act-btn act-btn-edit">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button
                                            @click="openModal({{ $r->ACID }},'{{ addslashes($r->RegisterNo) }}','{{ addslashes($r->FileNo) }}','{{ addslashes($r->GradeCourt) }}','{{ addslashes($r->CaseType) }}','{{ addslashes($r->SubCase ?? '') }}','{{ $r->OpenDate }}','{{ addslashes($r->NumberLetter ?? '') }}','{{ addslashes($r->LegalBasis ?? '') }}','{{ addslashes($r->Orders_Requested ?? '') }}','{{ addslashes($r->Remarks ?? '') }}','{{ addslashes($r->Status) }}','{{ addslashes($r->lower_court ?? '') }}','{{ addslashes($r->lower_case_no ?? '') }}')"
                                            title="Wax ka Badal" class="act-btn act-btn-edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button @click="deleteRecord({{ $r->ACID }})"
                                            title="Tirtir" class="act-btn act-btn-del">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <div class="empty-ico"><i class="bi bi-file-earmark-ruled"></i></div>
                                        <p class="empty-msg">Dacwad lama helin.</p>
                                        <button @click="openModal()" class="btn-submit" style="margin-top:.5rem">
                                            Diiwangeli Dacwad Cusub
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="data-card-ft">
                <p>Muujinaya <span class="font-semibold">{{ $records->count() }}</span> diiwaan</p>
            </div>
        </div>

        {{-- Add / Edit Modal --}}
        <div x-show="isModalOpen" style="display:none"
             class="fixed inset-0 z-[70] flex items-center justify-center p-4"
             style="background:rgba(10,40,77,.6);backdrop-filter:blur(6px)"
             @keydown.escape.window="closeModal()" @click.self="closeModal()">
            <div class="bg-white flex flex-col overflow-hidden w-full"
                 style="max-width:780px;max-height:94vh;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between flex-shrink-0"
                     :style="isEditMode ? 'background:#F0B43C;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0'
                                        : 'background:#528CBE;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0'">
                    <div class="flex items-center gap-3">
                        <div style="width:40px;height:40px;background:rgba(255,255,255,.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                            <i :class="isEditMode ? 'bi bi-pencil-square' : 'bi bi-file-earmark-plus'"
                               style="color:white;font-size:1.1rem"></i>
                        </div>
                        <div>
                            <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0"
                                x-text="isEditMode ? 'Wax ka Badal Dacwada' : 'Diiwangeli Dacwad Cusub'"></h2>
                            <p style="color:rgba(255,255,255,.8);font-size:.75rem;margin:0">Buuxi dhammaan goobaha loo baahdo</p>
                        </div>
                    </div>
                    <button @click="closeModal()"
                            style="width:34px;height:34px;background:rgba(255,255,255,.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center"
                            onmouseover="this.style.background='rgba(255,255,255,.22)'"
                            onmouseout="this.style.background='rgba(255,255,255,.12)'">
                        <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="overflow-y-auto flex-1" style="padding:1.5rem 1.75rem">
                    <form @submit.prevent="submitForm">

                        {{-- Row 1: Maxkamadda Hoose | Lambarka Dacwadda Hoose --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.1rem">
                            <div>
                                <label class="form-lbl">Maxkamadda Hoose</label>
                                <div class="inp-wrap">
                                    <select x-model="f.lower_court" class="form-sel">
                                        <option value="">— Dooro Maxkamadda Hoose —</option>
                                        @foreach($courts as $c)
                                            <option value="{{ $c->courtcode }}">{{ $c->longName }}</option>
                                        @endforeach
                                    </select>
                                    <i class="bi bi-chevron-down inp-ico-xs"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl">Lambarka Dacwadda Hoose</label>
                                <div class="inp-wrap" style="position:relative">
                                    <template x-if="rafcaanCasesList.length > 0">
                                        <select x-model="f.lower_case_no" class="form-sel">
                                            <option value="">— Dooro Lambarka Dacwadda —</option>
                                            <template x-for="c in rafcaanCasesList" :key="c.CRID">
                                                <option :value="c.FileNo" x-text="c.FileNo"></option>
                                            </template>
                                        </select>
                                    </template>
                                    <template x-if="rafcaanCasesList.length === 0">
                                        <input type="text" x-model="f.lower_case_no"
                                               :placeholder="rafcaanLoading ? 'Waa la rarayo...' : (f.lower_court ? 'Dacwad Rafcaan ah lama helin' : 'tusaale: MRGB/DML/5/2025')"
                                               class="form-inp">
                                    </template>
                                    <i x-show="rafcaanLoading" class="bi bi-arrow-repeat inp-ico" style="animation:spin .8s linear infinite;display:none"></i>
                                    <i x-show="!rafcaanLoading && rafcaanCasesList.length > 0" class="bi bi-chevron-down inp-ico-xs" style="display:none"></i>
                                    <i x-show="!rafcaanLoading && rafcaanCasesList.length === 0" class="bi bi-file-earmark-text inp-ico" style="display:none"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Row 2: B.G Lambar | Maxkamadda | Gal Lambar --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.1rem">
                            <div>
                                <label class="form-lbl">B.G Lambar <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="f.RegisterNo" required placeholder="tusaale: BG-2024-001" class="form-inp">
                                    <i class="bi bi-hash inp-ico"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl">Magaca Maxkamadda <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <select x-model="f.GradeCourt" required class="form-sel"
                                            :disabled="!isEditMode && '{{ $userCourtID }}' !== ''">
                                        <option value="">— Dooro Maxkamadda —</option>
                                        @foreach($courts as $c)
                                            <option value="{{ $c->courtcode }}">{{ $c->longName }}</option>
                                        @endforeach
                                    </select>
                                    <i class="bi bi-chevron-down inp-ico-xs"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl">Gal Lambar <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="f.FileNo" required readonly placeholder="Xulo Maxkamadda..."
                                           class="form-inp" style="background:#f9fafb;cursor:not-allowed;color:#9ca3af">
                                    <i class="bi bi-lock inp-ico"></i>
                                </div>
                            </div>
                        </div>

                        {{-- File No Preview --}}
                        <div x-show="f.FileNo && !isEditMode" style="display:none;margin-bottom:1.1rem">
                            <div style="background:linear-gradient(135deg,#eef4fb,#e8f0f9);border:1.5px solid #c3d9f0;border-radius:.875rem;padding:.9rem 1.1rem;display:flex;align-items:center;gap:.85rem">
                                <div style="width:34px;height:34px;background:#528CBE;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="bi bi-info-lg" style="color:white;font-size:1rem"></i>
                                </div>
                                <div>
                                    <p style="margin:0;font-size:.82rem;color:#0A284D;font-weight:600">
                                        Lambarka Dacwada: &nbsp;<span x-text="f.FileNo" style="font-size:1rem;font-weight:800;color:#0A284D"></span>
                                    </p>
                                    <p style="margin:.2rem 0 0;font-size:.72rem;color:#528CBE">Lambarka la siin doono marka foomka la gudbinayo.</p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" x-model="f.CaseType">

                        {{-- Hoosaadka Dacwadda --}}
                        <div style="margin-bottom:1.1rem">
                            <label class="form-lbl">Nooca Hoosaadka Dacwadda</label>
                            <div class="inp-wrap">
                                <select x-model="f.SubCase" class="form-sel">
                                    <option value="">— Dooro Nooca Hoosaadka —</option>
                                    @foreach($civilSubCases as $sub)
                                        <option value="{{ $sub }}">{{ $sub }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down inp-ico-xs"></i>
                            </div>
                        </div>

                        {{-- Row 2: Taariikhda | Xaalada | Lambarka Warqadda --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.1rem">
                            <div>
                                <label class="form-lbl">Taariikhda Furitaanka <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <input type="date" x-model="f.OpenDate" required class="form-inp">
                                    <i class="bi bi-calendar3 inp-ico"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl">Xaaladda</label>
                                <div class="inp-wrap">
                                    <select x-model="f.Status" class="form-sel" style="pointer-events:none;background:#f9fafb">
                                        <option value="Diwaan Galin">Diwaan Galin</option>
                                    </select>
                                    <i class="bi bi-chevron-down inp-ico-xs"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl">Lambarka Warqadda</label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="f.NumberLetter" placeholder="tusaale: WRQ-001" class="form-inp">
                                    <i class="bi bi-hash inp-ico"></i>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" x-model="f.LegalBasis">

                        {{-- Codsiga Dacwoodaha --}}
                        <div style="margin-bottom:1.1rem">
                            <label class="form-lbl">Codsiga Dacwoodaha</label>
                            <textarea x-model="f.Orders_Requested" rows="3" class="form-textarea"
                                      placeholder="Sharax amarrada la codsanayo..."></textarea>
                        </div>

                        {{-- Mooduca Dacwadda --}}
                        <div style="margin-bottom:1.5rem">
                            <label class="form-lbl">Mooduca Dacwadda</label>
                            <textarea x-model="f.Remarks" rows="3" class="form-textarea"
                                      placeholder="Faallo dheeraad ah..."></textarea>
                        </div>

                        {{-- Modal Footer --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                            <button type="button" @click="closeModal()" class="btn-cancel">Jooji</button>
                            <button type="submit" :disabled="isSubmitting" class="btn-submit"
                                    :style="isEditMode ? 'background:#F0B43C' : 'background:#528CBE'"
                                    :class="{'opacity-70 cursor-not-allowed': isSubmitting}">
                                <i class="bi bi-check-circle-fill" x-show="!isSubmitting"></i>
                                <i class="bi bi-arrow-repeat" x-show="isSubmitting" style="display:none"></i>
                                <span x-text="isEditMode ? 'Kaydi Isbeddelada' : 'Kaydi'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('civilRegManager', () => ({
                isModalOpen: false, isEditMode: false, isSubmitting: false, currentId: null,
                rafcaanCasesList: [], rafcaanLoading: false,
                f: {
                    RegisterNo: '', FileNo: '', GradeCourt: '{{ $userCourtID ?? "" }}',
                    CaseType: 'Madani', SubCase: '', OpenDate: '{{ date("Y-m-d") }}',
                    NumberLetter: '', LegalBasis: 'Dacwadda Madaniga', Orders_Requested: '',
                    Remarks: '', Status: 'Diwaan Galin',
                    lower_court: '', lower_case_no: ''
                },
                init() {
                    this.$watch('f.GradeCourt', (courtcode) => {
                        if (courtcode && !this.isEditMode) this.generateFileNo(courtcode);
                    });
                    this.$watch('f.lower_court', (courtcode) => {
                        this.f.lower_case_no = '';
                        this.rafcaanCasesList = [];
                        if (courtcode) this.fetchRafcaanCases(courtcode);
                    });
                    this.$watch('f.lower_case_no', (fileNo) => {
                        if (!fileNo) return;
                        const c = this.rafcaanCasesList.find(x => x.FileNo === fileNo);
                        if (!c) return;
                        if (c.SubCase)          this.f.SubCase          = c.SubCase;
                        if (c.NumberLetter)     this.f.NumberLetter     = c.NumberLetter;
                        if (c.LegalBasis)       this.f.LegalBasis       = c.LegalBasis;
                        if (c.Orders_Requested) this.f.Orders_Requested = c.Orders_Requested;
                        if (c.Remarks)          this.f.Remarks          = c.Remarks;
                    });
                },
                async fetchRafcaanCases(courtcode) {
                    this.rafcaanLoading = true;
                    try {
                        const res  = await fetch(`/appeal-civil-registration/rafcaan-cases/${encodeURIComponent(courtcode)}`, { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        this.rafcaanCasesList = Array.isArray(data) ? data : [];
                    } catch (e) { this.rafcaanCasesList = []; }
                    finally { this.rafcaanLoading = false; }
                },
                openModal(id = null, RegisterNo = '', FileNo = '', GradeCourt = '{{ $userCourtID ?? "" }}',
                          CaseType = 'Madani', SubCase = '', OpenDate = '{{ date("Y-m-d") }}',
                          NumberLetter = '', LegalBasis = 'Dacwadda Madaniga',
                          Orders_Requested = '', Remarks = '', Status = 'Diwaan Galin',
                          lower_court = '', lower_case_no = '') {
                    this.isEditMode = !!id; this.currentId = id;
                    this.rafcaanCasesList = [];
                    this.f = { RegisterNo, FileNo, GradeCourt, CaseType, SubCase, OpenDate, NumberLetter, LegalBasis, Orders_Requested, Remarks, Status, lower_court, lower_case_no };
                    this.isModalOpen = true;
                    if (this.f.GradeCourt) this.generateFileNo(this.f.GradeCourt);
                    if (lower_court) this.fetchRafcaanCases(lower_court);
                },
                closeModal() {
                    this.isModalOpen = false; this.currentId = null;
                    this.rafcaanCasesList = []; this.rafcaanLoading = false;
                    this.f = { RegisterNo: '', FileNo: '', GradeCourt: '{{ $userCourtID ?? "" }}', CaseType: 'Madani', SubCase: '', OpenDate: '{{ date("Y-m-d") }}', NumberLetter: '', LegalBasis: 'Dacwadda Madaniga', Orders_Requested: '', Remarks: '', Status: 'Diwaan Galin', lower_court: '', lower_case_no: '' };
                },
                async generateFileNo(courtcode) {
                    if (!courtcode || this.isEditMode) return;
                    this.f.FileNo = '';
                    try {
                        const res = await fetch(`/appeal-civil-registration/next-fileno/${encodeURIComponent(courtcode)}`, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) return;
                        const data = await res.json();
                        if (data.fileNo) this.f.FileNo = data.fileNo;
                    } catch (e) { console.error('generateFileNo failed:', e); }
                },
                async submitForm() {
                    this.isSubmitting = true;
                    try {
                        const url    = this.isEditMode ? `/appeal-civil-registration/${this.currentId}` : '/appeal-civil-registration';
                        const method = this.isEditMode ? 'PUT' : 'POST';
                        const res    = await fetch(url, {
                            method,
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: JSON.stringify(this.f)
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.closeModal();
                            Swal.fire({ title: 'Guul!', text: data.message, icon: 'success', confirmButtonText: 'Hagaag', confirmButtonColor: '#528CBE' }).then(() => window.location.reload());
                        } else throw new Error(data.message || 'Xaqiijintu waa fashilantay.');
                    } catch (e) {
                        Swal.fire({ title: 'Khalad!', text: e.message, icon: 'error', confirmButtonText: 'Hagaag', confirmButtonColor: '#DC2626' });
                    } finally { this.isSubmitting = false; }
                },
                deleteRecord(id) {
                    Swal.fire({
                        title: 'Ma hubtaa?', text: 'Ma awoodi doontid inaad dib u celiso!',
                        icon: 'warning', showCancelButton: true,
                        confirmButtonColor: '#DC2626', cancelButtonColor: '#528CBE',
                        confirmButtonText: 'Haa, tirtir!', cancelButtonText: 'Jooji'
                    }).then(async r => {
                        if (r.isConfirmed) {
                            try {
                                const res  = await fetch(`/appeal-civil-registration/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
                                const data = await res.json();
                                if (res.ok && data.success) {
                                    Swal.fire({ title: 'La tirtiray!', text: data.message, icon: 'success', confirmButtonText: 'Hagaag', confirmButtonColor: '#528CBE' }).then(() => window.location.reload());
                                } else throw new Error(data.message || 'Tirtirku wuu fashilmay.');
                            } catch (e) {
                                Swal.fire({ title: 'Khalad!', text: e.message, icon: 'error', confirmButtonText: 'Hagaag', confirmButtonColor: '#DC2626' });
                            }
                        }
                    });
                }
            }));
        });
    </script>

@endsection
