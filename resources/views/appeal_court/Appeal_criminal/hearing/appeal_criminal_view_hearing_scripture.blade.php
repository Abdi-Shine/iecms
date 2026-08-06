@extends('admin.admin_master')
@section('page_title', 'Garmaqalka Dhageysiga')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-wrap">

        {{-- Page Header --}}
        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-journal-text"></i></div>
                <div>
                    <h1 class="page-title">Dhageysiga Dacwadaha</h1>
                    <p class="page-sub">Maamul iyo fulinta dhageysiyada dacwadaha ciqaabta</p>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-primary"><i class="bi bi-megaphone"></i></div>
                <div>
                    <p class="kpi-lbl">Wadarta Dhageysiyada</p>
                    <h3 class="kpi-val">{{ $hearingStats['total'] }}</h3>
                    <p class="kpi-sub-primary">Diiwaanka taariikhiga</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-gold"><i class="bi bi-calendar-event"></i></div>
                <div>
                    <p class="kpi-lbl">Jadwalka</p>
                    <h3 class="kpi-val">{{ $hearingStats['scheduled'] }}</h3>
                    <p class="kpi-sub-gold">Fadhiyada soo socda</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-success"><i class="bi bi-check2-all"></i></div>
                <div>
                    <p class="kpi-lbl">La Dhammeeyay</p>
                    <h3 class="kpi-val">{{ $hearingStats['completed'] }}</h3>
                    <p class="kpi-sub-success">Dacwaduhu dhammaatay</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-primary"><i class="bi bi-calendar-month"></i></div>
                <div>
                    <p class="kpi-lbl">Bishaan</p>
                    <h3 class="kpi-val">{{ $hearingStats['thisMonth'] }}</h3>
                    <p class="kpi-sub-primary">Xilligaan socda</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="data-card">

            {{-- Search & Filter --}}
            <form action="{{ route('appeal-criminal-hearings.scripture') }}" method="GET" class="reg-filter">
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
                    <a href="{{ route('appeal-criminal-hearings.scripture') }}" class="act-btn" title="Clear" style="width:auto;padding:0 .9rem;gap:.4rem;">
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
                            <th>Nooca Dacwada</th>
                            <th>Taariikhda Furitaanka</th>
                            <th>Lambarka Warqadda</th>
                            <th>Faallo</th>
                            <th>Xaalada</th>
                            <th class="center" style="width:11rem">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $i => $r)
                            @php
                                $isGreen = in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                $isClosed = $r->Status === 'Closed';
                                $sBg = $isGreen ? 'rgba(34,197,94,.12)' : ($isClosed ? 'rgba(239,68,68,.1)' : 'rgba(240,180,60,.12)');
                                $sColor = $isGreen ? '#15803d' : ($isClosed ? '#b91c1c' : '#C07E15');
                                $sDot = $isGreen ? '#16a34a' : ($isClosed ? '#dc2626' : '#F0B43C');
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

                                @php
                                    $latestScripture = \App\Models\HearingScripture::where('criminal_case_id', $r->ACMID)->orderByDesc('created_at')->first();
                                @endphp
                                <td>
                                    <div class="td-actions">
                                        <a href="{{ route('appeal-criminal-hearings.scripture.create', $r->ACMID) }}" title="Ku Dar Garmaqal"
                                            class="act-btn act-btn-poa">
                                            <i class="bi bi-journal-plus"></i>
                                        </a>
                                        @if($latestScripture && $latestScripture->status === 'Draft')
                                            <a href="{{ route('appeal-criminal-hearings.scripture.edit', $latestScripture->id) }}"
                                                title="Wax ka Badal Draft-ka" class="act-btn"
                                                style="background:rgba(240,180,60,.12);color:#C07E15;border:1px solid rgba(240,180,60,.3)">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @else
                                            <span class="act-btn"
                                                style="opacity:.3;cursor:default;background:#f9fafb;border:1px solid #e5e7eb"
                                                title="Draft ma jirto">
                                                <i class="bi bi-pencil-square"></i>
                                            </span>
                                        @endif
                                        @if($latestScripture)
                                            <a href="{{ route('appeal-criminal-hearings.scripture.document', $latestScripture->id) }}"
                                                title="Arag Dukuumintiga Garmaqalka" class="act-btn act-btn-edit">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        @else
                                            <span class="act-btn act-btn-edit" style="opacity:.35;cursor:default"
                                                title="Garmaqal ma jiro">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </span>
                                        @endif
                                        <a href="{{ route('appeal-criminal-registration.show', $r->ACMID) }}" title="Arag Dacwada Oo Dhan"
                                            class="act-btn act-btn-edit">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-ico"><i class="bi bi-journal-x"></i></div>
                                        <p class="empty-msg">Dacwad ciqaab ah lama helin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="data-card-ft">
                <p>Muujinaya <span>{{ $records->count() }}</span> diiwaан ({{ $records->total() }} wadarta)</p>
            </div>

            <div class="px-6 py-4 border-t border-neutral-100">
                {{ $records->links() }}
            </div>

        </div>

    </div>

@endsection