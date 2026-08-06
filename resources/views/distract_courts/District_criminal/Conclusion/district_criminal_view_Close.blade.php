@extends('admin.admin_master')
@section('page_title', 'Xidhitaanka Dacwadaha')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(session('success'))
        <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#065f46;
                            border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="page-wrap">

        {{-- Page Header --}}
        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-folder-x"></i></div>
                <div>
                    <h1 class="page-title">Xidhitaanka Dacwadaha</h1>
                    <p class="page-sub">Maamul iyo diiwaangelinta dacwadaha la xidhay ama diyaar u ah in la xidho</p>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-primary"><i class="bi bi-folder2-open"></i></div>
                <div>
                    <p class="kpi-lbl">Wadarta Dacwadaha</p>
                    <h3 class="kpi-val">{{ $stats['total'] }}</h3>
                    <p class="kpi-sub-primary">Diiwaanka taariikhiga</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-gold"><i class="bi bi-gavel"></i></div>
                <div>
                    <p class="kpi-lbl">Go,aanno</p>
                    <h3 class="kpi-val">{{ $stats['xukun'] }}</h3>
                    <p class="kpi-sub-gold">Diyaar u ah xidhitaan</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-success"><i class="bi bi-folder-check"></i></div>
                <div>
                    <p class="kpi-lbl">La Xidhay</p>
                    <h3 class="kpi-val">{{ $stats['closed'] }}</h3>
                    <p class="kpi-sub-success">Dacwadaha dhammaystiran</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-primary"><i class="bi bi-patch-check"></i></div>
                <div>
                    <p class="kpi-lbl">Xukun Dhamaystiran</p>
                    <h3 class="kpi-val">{{ $stats['final'] }}</h3>
                    <p class="kpi-sub-primary">Xukunnada la xushmeeyo</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="data-card">

            {{-- Search & Filter --}}
            <form action="{{ route('criminal-close-case.index') }}" method="GET" class="reg-filter">
                {{-- Page size --}}
                <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                    <span>Show</span>
                    <select name="per_page" onchange="this.form.submit()"
                        class="px-3 py-1.5 text-sm font-semibold border border-neutral-300 rounded-full bg-white text-neutral-700
                               focus:outline-none focus:border-[#528CBE] cursor-pointer">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="reg-search-wrap">
                    <i class="bi bi-search reg-search-ico"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Raadi lambarka faylka ama nooca dacwada..."
                        class="reg-search-inp">
                </div>
                <select name="status" onchange="this.form.submit()" class="reg-filter-sel">
                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Dhammaan Xaaladaha</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->name }}" {{ request('status') == $s->name ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="act-btn act-btn-edit" title="Search" style="width:auto;padding:0 .9rem;gap:.4rem;">
                    <i class="bi bi-search"></i> Search
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('criminal-close-case.index') }}" class="act-btn" title="Clear" style="width:auto;padding:0 .9rem;gap:.4rem;">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </form>

            {{-- Table header bar --}}
            <div class="reg-table-bar">
                <div class="reg-table-bar-left">
                    <i class="bi bi-table"></i>
                    <span class="reg-table-bar-title">Diiwaanka Dacwadaha</span>
                </div>
                <span class="reg-table-count">{{ $records->total() }} dacwadood</span>
            </div>

            {{-- Table --}}
            <div class="data-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:3.5rem">T.T</th>
                            <th>Lambarka Faylka</th>
                            <th>Dacwada</th>
                            <th>Taariikhda Furitaanka</th>
                            <th>Lambarka Warqadda</th>
                            <th>Faallo</th>
                            <th>Xaalada</th>
                            <th class="center" style="width:10rem">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $i => $r)
                            @php
                                $isGreen = in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Dhageysi']);
                                $isClosed = $r->Status === 'Closed';
                                $isXukun = $r->Status === 'Xukun';
                                $sBg = $isClosed ? 'rgba(239,68,68,.1)' : ($isXukun ? 'rgba(82,140,190,.12)' : ($isGreen ? 'rgba(34,197,94,.12)' : 'rgba(240,180,60,.12)'));
                                $sColor = $isClosed ? '#b91c1c' : ($isXukun ? '#0A284D' : ($isGreen ? '#15803d' : '#C07E15'));
                                $latestJudgment = \App\Models\DistrictCriminalJudgment::where('criminal_case_id', $r->CMID)->orderByDesc('created_at')->first();

                            @endphp
                            <tr>

                                <td><span class="td-num">{{ str_pad($records->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span></td>
                                <td><span class="badge-file">{{ $r->FileNo }}</span></td>
                                <td><span class="badge-type">{{ $r->CaseType }}</span></td>
                                <td class="td-date">{{ \Carbon\Carbon::parse($r->OpenDate)->format('d/m/Y') }}</td>
                                <td class="td-text">{{ $r->NumberLetter ?: '—' }}</td>
                                <td class="td-muted">{{ Str::limit($r->Remarks ?? '—', 45) }}</td>

                                <td>
                                    <span class="status-pill" style="background:{{ $sBg }};color:{{ $sColor }}">
                                        {{ $r->Status }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="td-actions">

                                        {{-- Open close-case form --}}
                                        @if($isClosed)
                                            <span class="act-btn" title="Dacwaddu horey ayaa la xidhay"
                                                style="opacity:.3;cursor:not-allowed;background:#f9fafb;border:1px solid #e5e7eb;">
                                                <i class="bi bi-folder-x"></i>
                                            </span>
                                        @else
                                            <a href="{{ route('criminal-close-case.form', $r->CMID) }}" title="Fur Foomka Xidhitaanka"
                                                class="act-btn"
                                                style="background:rgba(239,68,68,.08);color:#b91c1c;border:1px solid rgba(239,68,68,.2)">
                                                <i class="bi bi-folder-x"></i>
                                            </a>
                                        @endif

                                        {{-- Close case document --}}
                                        @if($latestJudgment)
                                            <a href="{{ route('criminal-close-case.document', $r->CMID) }}" title="Dukuumintiga Xidhitaanka"
                                                class="act-btn"
                                                style="background:rgba(10,40,77,.07);color:#0A284D;border:1px solid rgba(10,40,77,.18)">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        @else
                                            <span class="act-btn" title="Xukun ma jiro"
                                                style="opacity:.3;cursor:default;background:#f9fafb;border:1px solid #e5e7eb">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </span>
                                        @endif

                                        {{-- View case --}}
                                        <a href="{{ route('criminal-registration.show', $r->CMID) }}" title="Arag Dacwada Oo Dhan"
                                            class="act-btn act-btn-edit">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <div class="empty-ico"><i class="bi bi-folder-x"></i></div>
                                        <p class="empty-msg">Dacwad ciqaabta ah la xidhay lama helin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="data-card-ft">
                <p>Muujinaya <span>{{ $records->count() }}</span> diiwaan ({{ $records->total() }} wadarta)</p>
            </div>

            <div class="px-6 py-4 border-t border-neutral-100">
                {{ $records->links() }}
            </div>

        </div>

    </div>

@endsection