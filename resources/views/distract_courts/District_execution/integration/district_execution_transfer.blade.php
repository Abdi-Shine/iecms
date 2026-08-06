@extends('admin.admin_master')
@section('page_title', 'Wareejinta Dacwadaha Fulinta')

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
    @if(session('error'))
        <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#991b1b;
                        border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="page-wrap">

        {{-- Page Header --}}
        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-box-arrow-right"></i></div>
                <div>
                    <h1 class="page-title">Wareejinta Dacwadaha</h1>
                    <p class="page-sub">Maamul iyo diiwaangelinta wareejinta dacwadaha madaniga</p>
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
                <div class="kpi-ico kpi-ico-gold"><i class="bi bi-arrow-up-circle"></i></div>
                <div>
                    <p class="kpi-lbl">Rafcaan</p>
                    <h3 class="kpi-val">{{ $stats['rafcaan'] }}</h3>
                    <p class="kpi-sub-gold">Diyaar u wareejinta</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-success"><i class="bi bi-arrow-left-right"></i></div>
                <div>
                    <p class="kpi-lbl">La Wareejiyay</p>
                    <h3 class="kpi-val">{{ $stats['transferred'] }}</h3>
                    <p class="kpi-sub-success">Wareejinta dhammaystiran</p>
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
            <form action="{{ route('execution-transfer.index') }}" method="GET" class="reg-filter">
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
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Raadi lambarka faylka ama nooca dacwada..."
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
                    <a href="{{ route('execution-transfer.index') }}" class="act-btn" title="Clear" style="width:auto;padding:0 .9rem;gap:.4rem;">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </form>

            {{-- Table header bar --}}
            <div class="reg-table-bar">
                <div class="reg-table-bar-left">
                    <i class="bi bi-table"></i>
                    <span class="reg-table-bar-title">Diiwaanka Dacwadaha Diyaarka Wareejinta</span>
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
                            <th>Maxkamadda Hadda</th>
                            <th>Maxkamadda Cusub</th>
                            <th>Taariikhda Wareejinta</th>
                            <th>Codsiga</th>
                            <th>Xaalada</th>
                            <th class="center" style="width:10rem">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $i => $r)
                            @php
                                $isClosed      = $r->Status === 'Closed';
                                $isTransferred = $r->Status === 'La Wareejiyay';
                                $isRafcaan     = $r->Status === 'Rafcaan';
                                $isGreen       = in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Dhageysi']);
                                $sBg    = $isClosed      ? 'rgba(239,68,68,.1)'
                                        : ($isTransferred ? 'rgba(34,197,94,.12)'
                                        : ($isRafcaan     ? 'rgba(240,180,60,.12)'
                                        : ($isGreen       ? 'rgba(34,197,94,.12)' : 'rgba(240,180,60,.12)')));
                                $sColor = $isClosed      ? '#b91c1c'
                                        : ($isTransferred ? '#15803d'
                                        : ($isRafcaan     ? '#C07E15'
                                        : ($isGreen       ? '#15803d' : '#C07E15')));
                                $trn        = $r->transfer;
                                $trnStatus  = $trn?->status ?? null;
                                $isPending  = $trnStatus === 'Submitted';
                                $isApproved = $trnStatus === 'Approved';
                                $isDraft    = $trnStatus === 'Draft';
                            @endphp

                            <tr>

                                <td><span class="td-num">{{ str_pad($records->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span></td>
                                <td><span class="badge-file">{{ $r->FileNo }}</span></td>
                                <td><span class="badge-type">{{ $r->CaseType }}</span></td>
                                <td class="td-text">{{ $r->court?->longName ?? $r->GradeCourt }}</td>

                                {{-- Maxkamadda Cusub --}}
                                <td class="td-text">
                                    @if($trn?->to_court)
                                        @php $toCourt = $courts->firstWhere('courtcode', $trn->to_court); @endphp
                                        <span style="font-weight:600;color:#0A284D">
                                            {{ $toCourt?->longName ?? $trn->to_court }}
                                        </span>
                                    @else
                                        <span style="color:#9ca3af">—</span>
                                    @endif
                                </td>

                                {{-- Taariikhda Wareejinta --}}
                                <td class="td-date">
                                    {{ $trn?->transfer_date ? \Carbon\Carbon::parse($trn->transfer_date)->format('d/m/Y') : '—' }}
                                </td>

                                {{-- Codsiga (transfer request status) --}}
                                <td>
                                    @if($isApproved)
                                        <span class="status-pill" style="background:rgba(34,197,94,.12);color:#15803d">
                                            <i class="bi bi-check-circle-fill" style="font-size:.65rem"></i> La Ogolaaday
                                        </span>
                                    @elseif($isPending)
                                        <span class="status-pill" style="background:rgba(240,180,60,.12);color:#C07E15">
                                            <i class="bi bi-hourglass-split" style="font-size:.65rem"></i> Sugaya
                                        </span>
                                    @elseif($isDraft)
                                        <span class="status-pill" style="background:rgba(156,163,175,.12);color:#6b7280">
                                            <i class="bi bi-pencil" style="font-size:.65rem"></i> Muswaad
                                        </span>
                                    @else
                                        <span style="color:#9ca3af">—</span>
                                    @endif
                                </td>

                                {{-- Xaalada (case status) --}}
                                <td>
                                    <span class="status-pill" style="background:{{ $sBg }};color:{{ $sColor }}">
                                        {{ $r->Status }}
                                    </span>
                                </td>

                                {{-- Ficilada --}}
                                <td>
                                    <div class="td-actions">

                                        {{-- Approve: Kaaliyaha Sare only, when transfer is Submitted --}}
                                        @if(($isKaaliyeSare ?? false) && $isPending)
                                            <form method="POST" action="{{ route('execution-transfer.approve', $trn->id) }}"
                                                  style="display:inline" class="approve-form">
                                                @csrf
                                                <button type="button" title="Ogolow Wareejinta" class="act-btn approve-btn"
                                                    style="background:rgba(34,197,94,.1);color:#15803d;border:1px solid rgba(34,197,94,.3)">
                                                    <i class="bi bi-check2-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Transfer form --}}
                                        @if($isClosed || $isTransferred || $isApproved)
                                            <span class="act-btn"
                                                title="{{ $isClosed ? 'Dacwaddu horey ayaa la xidhay' : 'Horey ayaa la wareejiyay' }}"
                                                style="opacity:.3;cursor:not-allowed;background:#f9fafb;border:1px solid #e5e7eb;">
                                                <i class="bi bi-arrow-left-right"></i>
                                            </span>
                                        @else
                                            <a href="{{ route('execution-transfer.form', $r->ECID) }}"
                                               title="Fur Foomka Wareejinta" class="act-btn"
                                               style="background:rgba(34,197,94,.1);color:#15803d;border:1px solid rgba(34,197,94,.3)">
                                                <i class="bi bi-arrow-left-right"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('execution-registration.show', $r->ECID) }}"
                                           title="Arag Dacwada Oo Dhan" class="act-btn act-btn-edit">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <div class="empty-ico"><i class="bi bi-arrow-left-right"></i></div>
                                        <p class="empty-msg">Dacwad fulinta diyaar u wareejinta lama helin.</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.approve-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const form = this.closest('.approve-form');
                    Swal.fire({
                        title: 'Ogolow Wareejinta',
                        text: 'Ma hubtaa inaad ogolaan lahayd wareejintaan?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#15803d',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="bi bi-check2-circle"></i> Haa, Ogolow',
                        cancelButtonText: 'Jooji',
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>

@endsection
