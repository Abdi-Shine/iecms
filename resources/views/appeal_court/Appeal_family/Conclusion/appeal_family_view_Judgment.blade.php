@extends('admin.admin_master')
@section('page_title', 'Xukunka Dacwadaha')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-wrap">

        {{-- Page Header --}}
        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-gavel"></i></div>
                <div>
                    <h1 class="page-title">Xukunka Dacwadaha</h1>
                    <p class="page-sub">Maamul iyo diiwaangelinta xukunnada dacwadaha qoyska</p>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-primary"><i class="bi bi-gavel"></i></div>
                <div>
                    <p class="kpi-lbl">Wadarta Xukunnada</p>
                    <h3 class="kpi-val">{{ $stats['total'] }}</h3>
                    <p class="kpi-sub-primary">Diiwaanka taariikhiga</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-gold"><i class="bi bi-pencil-square"></i></div>
                <div>
                    <p class="kpi-lbl">Muswaaddada</p>
                    <h3 class="kpi-val">{{ $stats['draft'] }}</h3>
                    <p class="kpi-sub-gold">Aan la gudbinin</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-success"><i class="bi bi-check2-all"></i></div>
                <div>
                    <p class="kpi-lbl">La Gudbiiyay</p>
                    <h3 class="kpi-val">{{ $stats['submitted'] }}</h3>
                    <p class="kpi-sub-success">Xukunnada la gudbiyay</p>
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
            <form action="{{ route('appeal-family-judgments.index') }}" method="GET" class="reg-filter">
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
                    <a href="{{ route('appeal-family-judgments.index') }}" class="act-btn" title="Clear" style="width:auto;padding:0 .9rem;gap:.4rem;">
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
                            <th style="width:11rem">Mudada 30-ka</th>
                            <th class="center" style="width:9rem">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $i => $r)
                            @php
                                $isGreen = in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Dhageysi']);
                                $isClosed = $r->Status === 'Closed';
                                $sBg = $isGreen ? 'rgba(34,197,94,.12)' : ($isClosed ? 'rgba(239,68,68,.1)' : 'rgba(240,180,60,.12)');
                                $sColor = $isGreen ? '#15803d' : ($isClosed ? '#b91c1c' : '#C07E15');
                                $sDot = $isGreen ? '#16a34a' : ($isClosed ? '#dc2626' : '#F0B43C');
                                $latestJudgment = $r->judgments->first();

                                // 30-day period: ONLY starts after parties sign (latest receipt date)
                                $latestReceiptDate = null;
                                if ($latestJudgment && $latestJudgment->receipts->isNotEmpty()) {
                                    $latestReceiptDate = $latestJudgment->receipts
                                        ->filter(fn($rec) => $rec->received_date)
                                        ->sortByDesc('received_date')
                                        ->first()?->received_date;
                                }
                                $startDate   = $latestReceiptDate ? \Carbon\Carbon::parse($latestReceiptDate) : null;
                                $daysElapsed = $startDate ? max(0, $startDate->startOfDay()->diffInDays(now()->startOfDay())) : null;
                                $daysLeft    = $daysElapsed !== null ? max(0, 30 - $daysElapsed) : null;
                                $pct         = $daysElapsed !== null ? min(100, (int) round($daysElapsed / 30 * 100)) : 0;
                                $barColor    = $pct < 50 ? '#16a34a' : ($pct < 84 ? '#C07E15' : '#dc2626');
                                $barTrack    = $pct < 50 ? 'rgba(34,197,94,.12)' : ($pct < 84 ? 'rgba(240,180,60,.15)' : 'rgba(239,68,68,.1)');
                                $labelColor  = $pct < 50 ? '#15803d' : ($pct < 84 ? '#92600a' : '#b91c1c');
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

                                {{-- 30-day progress column --}}
                                <td style="padding:10px 14px;">
                                    @if($latestJudgment && !$startDate)
                                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:600;color:#9ca3af;">
                                            <i class="bi bi-pen" style="font-size:.75rem;"></i> Sugaya Saxiixa
                                        </span>
                                    @elseif($startDate)
                                        <div style="display:flex;flex-direction:column;gap:4px;">
                                            {{-- Bar track --}}
                                            <div style="height:6px;border-radius:99px;background:{{ $barTrack }};overflow:hidden;">
                                                <div
                                                    style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;transition:width .3s;">
                                                </div>
                                            </div>
                                            {{-- Label --}}
                                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                                @if($daysElapsed <= 30)
                                                    <span style="font-size:.72rem;font-weight:700;color:{{ $labelColor }};">
                                                        {{ $daysLeft }} Maalmaha
                                                    </span>
                                                @else
                                                    <span style="font-size:.72rem;font-weight:700;color:#b91c1c;">
                                                        <i class="bi bi-exclamation-circle-fill" style="font-size:.7rem;"></i>
                                                        {{ $daysElapsed - 30 }} Maalmo Ka Dhaafay
                                                    </span>
                                                @endif
                                                <span style="font-size:.7rem;color:#9ca3af;">{{ $pct }}%</span>
                                            </div>
                                            {{-- Day count --}}
                                            <span style="font-size:.7rem;color:#6b7280;">
                                                {{ min($daysElapsed, 30) }} / 30 maalmood
                                            </span>
                                        </div>
                                    @else
                                        <span style="font-size:.8rem;color:#d1d5db;">—</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="td-actions">
                                        @if($latestJudgment && in_array($latestJudgment->status, ['Submitted', 'Final']))
                                            <span class="act-btn" title="Xukun horey ayaa loo gudbiyay"
                                                style="opacity:.3;cursor:not-allowed;background:#f9fafb;border:1px solid #e5e7eb;">
                                                <i class="bi bi-gavel"></i>
                                            </span>
                                        @else
                                            <a href="{{ route('appeal-family-judgments.create', $r->AFCID) }}" title="Ku Dar Xukun"
                                                class="act-btn act-btn-poa">
                                                <i class="bi bi-gavel"></i>
                                            </a>
                                        @endif
                                        @if($latestJudgment && $latestJudgment->status === 'Draft')
                                            <a href="{{ route('appeal-family-judgments.edit', $latestJudgment->id) }}"
                                                title="Wax ka Badal Draft-ka" class="act-btn"
                                                style="background:rgba(240,180,60,.12);color:#C07E15;border:1px solid rgba(240,180,60,.3)">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @else
                                            <span class="act-btn" title="Draft ma jirto"
                                                style="opacity:.3;cursor:default;background:#f9fafb;border:1px solid #e5e7eb">
                                                <i class="bi bi-pencil-square"></i>
                                            </span>
                                        @endif
                                        @if($latestJudgment)
                                            <a href="{{ route('appeal-family-judgments.document', $latestJudgment->id) }}"
                                                title="Dukuumintiga Xukunku" class="act-btn"
                                                style="background:rgba(10,40,77,.07);color:#0A284D;border:1px solid rgba(10,40,77,.18)">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        @else
                                            <span class="act-btn" title="Xukun ma jiro"
                                                style="opacity:.3;cursor:default;background:#f9fafb;border:1px solid #e5e7eb">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </span>
                                        @endif
                                        <a href="{{ route('appeal-family-registration.show', $r->AFCID) }}" title="Arag Dacwada Oo Dhan"
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
                                        <div class="empty-ico"><i class="bi bi-gavel"></i></div>
                                        <p class="empty-msg">Dacwad qoyska ah lama helin.</p>
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