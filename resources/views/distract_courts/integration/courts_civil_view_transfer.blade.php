@extends('admin.admin_master')
@section('page_title', 'Wareejinta Dacwadaha')

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

    <div class="page-wrap" x-data="{ search: '', status: 'all' }" x-init="$watch('search', () => $nextTick(() => filterRows()));
                          $watch('status',  () => $nextTick(() => filterRows()));">

        {{-- Page Header --}}
        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-arrow-left-right"></i></div>
                <div>
                    <h1 class="page-title">Wareejinta Dacwadaha</h1>
                    <p class="page-sub">Wareejinta dacwadaha ka maxkamad u maxkamad ka dib rafcaanka</p>
                </div>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div style="display:flex;gap:0;border-bottom:2px solid #e5e7eb;margin-bottom:1.5rem">
            <a href="{{ route('transfer.index') }}"
               style="display:inline-flex;align-items:center;gap:.45rem;padding:.65rem 1.25rem;font-size:.82rem;font-weight:700;text-decoration:none;border-bottom:2px solid #528CBE;margin-bottom:-2px;color:#528CBE">
                <i class="bi bi-arrow-left-right"></i> Diyaarka Wareejinta
            </a>
            <a href="{{ route('courtsintergration.transfer') }}"
               style="display:inline-flex;align-items:center;gap:.45rem;padding:.65rem 1.25rem;font-size:.82rem;font-weight:700;text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-2px;color:#6b7280"
               onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#6b7280'">
                <i class="bi bi-box-arrow-right"></i> La Wareejiyay
            </a>
            <a href="{{ route('courtsintergration.recived') }}"
               style="display:inline-flex;align-items:center;gap:.45rem;padding:.65rem 1.25rem;font-size:.82rem;font-weight:700;text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-2px;color:#6b7280"
               onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#6b7280'">
                <i class="bi bi-box-arrow-in-left"></i> La Helay
            </a>
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
            <div class="reg-filter">
                <div class="reg-search-wrap">
                    <i class="bi bi-search reg-search-ico"></i>
                    <input x-model="search" type="text" placeholder="Raadi lambarka faylka ama nooca dacwada..."
                        class="reg-search-inp">
                </div>
                <select x-model="status" class="reg-filter-sel">
                    <option value="all">Dhammaan Xaaladaha</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->name }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Table header bar --}}
            <div class="reg-table-bar">
                <div class="reg-table-bar-left">
                    <i class="bi bi-table"></i>
                    <span class="reg-table-bar-title">Diiwaanka Dacwadaha Diyaarka Wareejinta</span>
                </div>
                <span id="total-count" class="reg-table-count">{{ count($records) }} dacwadood</span>
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
                                $trn = $r->transfer;

                                // Transfer request status
                                $trnStatus   = $trn?->status ?? null;
                                $isPending   = $trnStatus === 'Submitted';
                                $isApproved  = $trnStatus === 'Approved';
                                $isDraft     = $trnStatus === 'Draft';
                            @endphp
                            <tr data-row data-fileno="{{ strtolower($r->FileNo) }}"
                                data-casetype="{{ strtolower($r->CaseType) }}" data-status="{{ $r->Status }}">

                                <td><span class="td-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span></td>
                                <td><span class="badge-file">{{ $r->FileNo }}</span></td>
                                <td><span class="badge-type">{{ $r->CaseType }}</span></td>

                                {{-- Maxkamadda Hadda --}}
                                <td class="td-text">{{ $r->court?->longName ?? $r->GradeCourt }}</td>

                                {{-- Maxkamadda Cusub (to court) --}}
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

                                {{-- Transfer request status badge --}}
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

                                {{-- Case status --}}
                                <td>
                                    <span class="status-pill" style="background:{{ $sBg }};color:{{ $sColor }}">
                                        {{ $r->Status }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="td-actions">

                                        {{-- Approve button: Kaaliyaha Sare only, transfer must be Submitted --}}
                                        @if($isKaaliyeSare && $isPending)
                                            <form method="POST" action="{{ route('transfer.approve', $trn->id) }}"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Ma hubtaa inaad ogolaan lahayd wareejintaan?')">
                                                @csrf
                                                <button type="submit" title="Ogolow Wareejinta"
                                                    class="act-btn"
                                                    style="background:rgba(34,197,94,.1);color:#15803d;border:1px solid rgba(34,197,94,.3)">
                                                    <i class="bi bi-check2-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Transfer form button: only if not yet approved/closed --}}
                                        @if($isClosed || $isTransferred || $isApproved)
                                            <span class="act-btn" title="{{ $isClosed ? 'Dacwaddu horey ayaa la xidhay' : 'Horey ayaa la wareejiyay' }}"
                                                style="opacity:.3;cursor:not-allowed;background:#f9fafb;border:1px solid #e5e7eb;">
                                                <i class="bi bi-arrow-left-right"></i>
                                            </span>
                                        @else
                                            <a href="{{ route('transfer.form', $r->CRID) }}" title="Fur Foomka Wareejinta"
                                                class="act-btn"
                                                style="background:rgba(34,197,94,.1);color:#15803d;border:1px solid rgba(34,197,94,.3)">
                                                <i class="bi bi-arrow-left-right"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('civil-registration.show', $r->CRID) }}" title="Arag Dacwada Oo Dhan"
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
                                        <div class="empty-ico"><i class="bi bi-arrow-left-right"></i></div>
                                        <p class="empty-msg">Dacwad diyaar u wareejinta lama helin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="no-results-row" style="display:none">
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-ico"><i class="bi bi-search"></i></div>
                                    <p class="empty-msg">Dacwad u dhigma raadintaada lama helin.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="data-card-ft">
                <p>Muujinaya <span id="showing-count">{{ count($records) }}</span> diiwaan</p>
                <span id="record-count" class="reg-table-count">{{ count($records) }} wadarta</span>
            </div>

        </div>

    </div>

    <script>
        function filterRows() {
            const search = document.querySelector('[x-model="search"]')?.value?.toLowerCase() ?? '';
            const status = document.querySelector('[x-model="status"]')?.value ?? 'all';
            const rows   = document.querySelectorAll('[data-row]');
            let visible  = 0;

            rows.forEach(row => {
                const matchSearch = !search || row.dataset.fileno.includes(search) || row.dataset.casetype.includes(search);
                const matchStatus = status === 'all' || row.dataset.status === status;
                row.style.display = (matchSearch && matchStatus) ? '' : 'none';
                if (matchSearch && matchStatus) visible++;
            });

            const noResults = document.getElementById('no-results-row');
            if (noResults) noResults.style.display = visible === 0 ? '' : 'none';

            document.getElementById('total-count').textContent   = visible + ' dacwadood';
            document.getElementById('showing-count').textContent = visible;
            document.getElementById('record-count').textContent  = visible + ' wadarta';
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('[x-model="search"]')?.addEventListener('input', filterRows);
            document.querySelector('[x-model="status"]')?.addEventListener('change', filterRows);
        });
    </script>

@endsection
