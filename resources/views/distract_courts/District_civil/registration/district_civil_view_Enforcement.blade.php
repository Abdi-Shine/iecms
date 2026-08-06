@extends('admin.admin_master')
@section('page_title', 'Dhaqan Galka Dacwadaha')

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
                <div class="page-hd-icon"><i class="bi bi-hammer"></i></div>
                <div>
                    <h1 class="page-title">Dhaqan Galka Dacwadaha</h1>
                    <p class="page-sub">Maamul iyo diiwaangelinta dhaqan galka xukunnada dacwadaha madaniga</p>
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
                    <p class="kpi-lbl">Oodista</p>
                    <h3 class="kpi-val">{{ $stats['oodista'] }}</h3>
                    <p class="kpi-sub-gold">Diyaar u ah dhaqan galka</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-success"><i class="bi bi-hammer"></i></div>
                <div>
                    <p class="kpi-lbl">La Dhaqan Galayaa</p>
                    <h3 class="kpi-val">{{ $stats['fulinta'] }}</h3>
                    <p class="kpi-sub-success">Dhaqan Galka socota</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-primary"><i class="bi bi-patch-check"></i></div>
                <div>
                    <p class="kpi-lbl">La Xidhay</p>
                    <h3 class="kpi-val">{{ $stats['closed'] }}</h3>
                    <p class="kpi-sub-primary">Dacwadaha dhammaystiran</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="data-card">

            {{-- Search & Filter --}}
            <form action="{{ route('enforcement.index') }}" method="GET" class="reg-filter">
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
                <select name="sub_case" onchange="this.form.submit()" class="reg-filter-sel">
                    <option value="">Nooca Dacwada</option>
                    @foreach($civilSubCases as $sub)
                        <option value="{{ $sub }}" {{ request('sub_case') == $sub ? 'selected' : '' }}>{{ $sub }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()" class="reg-filter-sel">
                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Dhammaan Xaaladaha</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->name }}" {{ request('status') == $s->name ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="act-btn act-btn-edit" title="Search" style="width:auto;padding:0 .9rem;gap:.4rem;">
                    <i class="bi bi-search"></i> Search
                </button>
                @if(request()->anyFilled(['search', 'status', 'sub_case']))
                    <a href="{{ route('enforcement.index') }}" class="act-btn" title="Clear" style="width:auto;padding:0 .9rem;gap:.4rem;">
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
                            <th>Gal Lambarka </th>
                            <th>Dacwada</th>
                            <th>Nooca Dacwada</th>
                            <th>Taariikhda Furitaanka</th>
                            <th>Lambarka Warqadda</th>
                            <th>Mudada 30-ka</th>
                            <th>Loo Xukumo</th>
                            <th>Xaalada</th>
                            <th class="center" style="width:10rem">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $i => $r)
                            @php
                                $isClosed     = $r->Status === 'Closed';
                                $isDhaqanGal  = $r->Status === 'Dhaqan Gal';
                                $isOodista    = $r->Status === 'Oodista';
                                $isGreen      = in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Dhageysi']);
                                $sBg    = $isClosed    ? 'rgba(239,68,68,.1)'
                                        : ($isDhaqanGal ? 'rgba(82,140,190,.12)'
                                        : ($isOodista   ? 'rgba(240,180,60,.12)'
                                        : ($isGreen     ? 'rgba(34,197,94,.12)' : 'rgba(240,180,60,.12)')));
                                $sColor = $isClosed    ? '#b91c1c'
                                        : ($isDhaqanGal ? '#0A284D'
                                        : ($isOodista   ? '#C07E15'
                                        : ($isGreen     ? '#15803d' : '#C07E15')));
                                $enf = $r->enforcement;
                            @endphp
                            <tr>

                                <td><span class="td-num">{{ str_pad($records->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span></td>
                                <td><span class="badge-file">{{ $r->FileNo }}</span></td>
                                <td><span class="badge-type">{{ $r->CaseType }}</span></td>
                                <td>
                                    @if($r->SubCase)
                                        <span class="badge-type">{{ $r->SubCase }}</span>
                                    @else
                                        <span class="text-neutral-400">—</span>
                                    @endif
                                </td>
                                <td class="td-date">{{ \Carbon\Carbon::parse($r->OpenDate)->format('d/m/Y') }}</td>
                                <td class="td-text">{{ $r->NumberLetter ?: '—' }}</td>

                                {{-- Mudada 30-ka: starts only after parties sign --}}
                                @php
                                    $judgment          = $r->judgments->sortByDesc('created_at')->first();
                                    $receipts          = $judgment?->receipts ?? collect();
                                    $latestReceiptDate = null;
                                    if ($judgment && $receipts->isNotEmpty()) {
                                        $latestReceiptDate = $receipts
                                            ->filter(fn($rc) => $rc->received_date)
                                            ->sortByDesc('received_date')
                                            ->first()?->received_date;
                                    }
                                @endphp
                                <td style="min-width:140px;padding:8px 12px;">
                                    @if($judgment && !$latestReceiptDate)
                                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:600;color:#9ca3af;">
                                            <i class="bi bi-pen" style="font-size:.75rem;"></i> Sugaya Saxiixa
                                        </span>
                                    @elseif($latestReceiptDate)
                                        @php
                                            $startDate = \Carbon\Carbon::parse($latestReceiptDate)->startOfDay();
                                            $elapsed   = max(0, $startDate->diffInDays(now()->startOfDay()));
                                            $remaining = max(0, 30 - $elapsed);
                                            $pct       = min(100, (int) round($elapsed / 30 * 100));
                                            $barColor  = $elapsed >= 30 ? '#ef4444' : ($elapsed >= 20 ? '#f59e0b' : '#22c55e');
                                            $textColor = $elapsed >= 30 ? '#b91c1c' : ($elapsed >= 20 ? '#d97706' : '#15803d');
                                        @endphp
                                        <div style="font-size:12px;">
                                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                                                <span style="font-weight:700;color:{{ $textColor }}">
                                                    {{ $remaining }} Maalmaha
                                                </span>
                                                <span style="color:#6b7280;font-size:11px;">{{ $pct }}%</span>
                                            </div>
                                            <div style="background:#e5e7eb;border-radius:99px;height:5px;overflow:hidden;">
                                                <div style="width:{{ $pct }}%;height:100%;background:{{ $barColor }};border-radius:99px;transition:width .3s;"></div>
                                            </div>
                                            <div style="color:#6b7280;font-size:11px;margin-top:4px;">{{ $elapsed }} / 30 maalmood</div>
                                        </div>
                                    @else
                                        <span style="color:#9ca3af;font-size:12px;">—</span>
                                    @endif
                                </td>

                                {{-- Loo Xukumo --}}
                                @php
                                    $receipts   = $judgment?->receipts ?? collect();
                                    $looParties = $receipts->filter(fn($rc) => str_contains(strtolower($rc->judgment_outcome ?? ''), 'loo'))->values();
                                @endphp
                                <td style="padding:8px 12px;">
                                    @forelse($looParties as $rc)
                                        <div style="font-size:.8rem;font-weight:600;color:#0A284D;line-height:1.5">
                                            {{ $rc->party_name }}
                                        </div>
                                    @empty
                                        <span style="color:#9ca3af;font-size:12px;">—</span>
                                    @endforelse
                                </td>

                                <td>
                                    <span class="status-pill" style="background:{{ $sBg }};color:{{ $sColor }}">
                                        {{ $r->Status }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="td-actions">

                                        {{-- Enforcement form --}}
                                        @if($isClosed)
                                            <span class="act-btn" title="Dacwaddu horey ayaa la xidhay"
                                                style="opacity:.3;cursor:not-allowed;background:#f9fafb;border:1px solid #e5e7eb;">
                                                <i class="bi bi-hammer"></i>
                                            </span>
                                        @else
                                            <a href="{{ route('enforcement.form', $r->CRID) }}" title="Fur Foomka Dhaqan Galka"
                                                class="act-btn"
                                                style="background:rgba(82,140,190,.1);color:#0A284D;border:1px solid rgba(82,140,190,.25)">
                                                <i class="bi bi-hammer"></i>
                                            </a>
                                        @endif

                                        {{-- View case --}}
                                        <a href="{{ route('civil-registration.show', $r->CRID) }}" title="Arag Dacwada Oo Dhan"
                                            class="act-btn act-btn-edit">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <div class="empty-ico"><i class="bi bi-hammer"></i></div>
                                        <p class="empty-msg">Dacwad dhaqan gal ah lama helin.</p>
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
