@extends('admin.admin_master')
@section('page_title', 'Qaadashada Xukunku — Dhinacyada')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
    <style>
        /* ── Receipt Modal ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(10,40,77,.55); z-index: 1000;
            align-items: center; justify-content: center; padding: 16px;
        }
        .modal-overlay.open { display: flex; }

        .modal-box {
            background: #f8fafc;
            border-radius: 1rem;
            width: 100%; max-width: 800px;
            max-height: 92vh; overflow-y: auto;
            box-shadow: 0 25px 60px rgba(10,40,77,.3);
        }

        /* sticky header */
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 24px;
            background: #fff;
            border-bottom: 1px solid #f3f4f6;
            border-radius: 1rem 1rem 0 0;
            position: sticky; top: 0; z-index: 10;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .modal-header-left { display: flex; align-items: center; gap: 10px; }
        .modal-header-icon {
            width: 38px; height: 38px; border-radius: .75rem;
            background: #528CBE;
            display: flex; align-items: center; justify-content: center;
        }
        .modal-header-icon i { color: #fff; font-size: 1rem; }
        .modal-title { font-size: .95rem; font-weight: 800; color: #0A284D; margin: 0; letter-spacing: -.01em; }
        .modal-sub   { font-size: .74rem; color: #6b7280; margin: 2px 0 0; font-weight: 600; }
        .modal-close {
            width: 32px; height: 32px; border-radius: .5rem;
            background: #f3f4f6; border: none; cursor: pointer;
            font-size: .85rem; color: #6b7280;
            display: flex; align-items: center; justify-content: center; transition: .15s;
        }
        .modal-close:hover { background: #e5e7eb; color: #374151; }

        .modal-body { padding: 20px 24px 28px; display: flex; flex-direction: column; gap: 14px; }

        /* ── Pending form card — matches entry-card style ── */
        .receipt-section {
            background: #fff;
            border: 1px solid #f3f4f6;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }

        /* Section header — matches card-hd with gradient */
        .receipt-section-hd {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 20px;
            background: linear-gradient(135deg, #528CBE 0%, #3D78AB 100%);
        }
        .receipt-section-hd-left { display: flex; align-items: center; gap: 10px; }
        .section-num {
            width: 28px; height: 28px; border-radius: 50%;
            background: rgba(255,255,255,.2); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .78rem; font-weight: 800; flex-shrink: 0;
            border: 1.5px solid rgba(255,255,255,.35);
        }
        .section-party-name { font-weight: 700; font-size: .9rem; color: #fff; }
        .section-role-badge {
            display: inline-flex; align-items: center;
            padding: 2px 10px; border-radius: 99px; font-size: .7rem; font-weight: 700;
            background: rgba(255,255,255,.2); color: #fff;
            border: 1px solid rgba(255,255,255,.3);
        }

        /* 4-column form grid */
        .form-grid-4 {
            display: grid; grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 1.25rem; padding: 1.25rem 1.5rem 0;
        }
        .form-field label {
            display: block; font-size: .68rem; font-weight: 800;
            color: #374151; text-transform: uppercase; letter-spacing: .07em;
            margin-bottom: .5rem;
        }
        .form-field label .req { color: #ef4444; }

        /* reuse court-forms .form-inp & .form-sel styles */
        .modal-inp {
            width: 100%; padding: .6rem .9rem;
            border: 1.5px solid #d1d5db; border-radius: .625rem;
            font-size: .82rem; color: #111827;
            background: #fff; outline: none; font-family: inherit;
            transition: border-color .15s, box-shadow .15s; box-sizing: border-box;
        }
        .modal-inp:focus { border-color: #528CBE; box-shadow: 0 0 0 3px rgba(82,140,190,.15); }
        .modal-inp.readonly-val {
            background: #EBF4FB; color: #528CBE;
            font-weight: 700; border-color: #A8D0EC; cursor: default;
        }

        /* file upload — matches upload-box-field */
        .file-label {
            display: flex; align-items: center; gap: .5rem;
            padding: .6rem .9rem; border: 1.5px dashed #d1d5db;
            border-radius: .625rem; cursor: pointer; font-size: .78rem;
            color: #9ca3af; background: #fafafa; transition: .15s;
            width: 100%; box-sizing: border-box;
        }
        .file-label:hover { border-color: #528CBE; background: rgba(82,140,190,.04); color: #528CBE; }

        /* faallo full-width section */
        .form-faallo { padding: 1rem 1.5rem 0; }

        /* action row — matches form-actions */
        .form-actions-modal {
            display: flex; justify-content: flex-end;
            padding: 1rem 1.5rem 1.25rem; gap: .75rem;
        }
        .btn-modal-save {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .65rem 1.75rem; background: #528CBE; color: #fff;
            border: none; border-radius: .875rem; font-size: .85rem; font-weight: 700;
            cursor: pointer; box-shadow: 0 4px 14px rgba(82,140,190,.4); transition: opacity .15s;
        }
        .btn-modal-save:hover:not(:disabled) { opacity: .88; }
        .btn-modal-save:disabled { opacity: .65; cursor: not-allowed; }

        /* ── Confirmed parties table — matches data-card ── */
        .confirmed-table-wrap {
            background: #fff; border: 1px solid #f3f4f6;
            border-radius: 1rem; overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .confirmed-table-hd {
            display: flex; align-items: center; justify-content: space-between;
            padding: .875rem 1.25rem; border-bottom: 1px solid #f3f4f6;
        }
        .confirmed-table-hd-title {
            display: flex; align-items: center; gap: .5rem;
            font-size: .7rem; font-weight: 800; color: #6b7280;
            text-transform: uppercase; letter-spacing: .1em;
        }
        .confirmed-table-hd-title i { color: #528CBE; font-size: .85rem; }
        .conf-tbl { width: 100%; border-collapse: collapse; font-size: .82rem; }
        .conf-tbl th {
            padding: .625rem 1.25rem;
            background: rgba(82,140,190,.06);
            font-size: .68rem; font-weight: 700; color: #6b7280;
            text-transform: uppercase; letter-spacing: .06em;
            border-bottom: 1px solid #f3f4f6; text-align: left;
        }
        .conf-tbl td { padding: .875rem 1.25rem; border-bottom: 1px solid #f8fafc; color: #374151; }
        .conf-tbl tbody tr:hover td { background: #f9fafb; }
        .conf-tbl tr:last-child td { border-bottom: none; }
        .outcome-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 9px; border-radius: 99px; font-size: .72rem; font-weight: 700;
        }

        /* ── Receipt badge in main table ── */
        .receipt-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 99px; font-size: .72rem; font-weight: 600;
        }
        .receipt-badge.all  { background: #DCFCE7; color: #15803D; }
        .receipt-badge.part { background: #FEF3C7; color: #C07E15; }
        .receipt-badge.none { background: #F3F4F6; color: #9CA3AF; }

        .btn-confirm.pending {
            background: #0A284D; color: #fff;
        }
        .btn-confirm.pending:hover { background: #0d3566; }
        .btn-confirm.loading { opacity: .6; cursor: default; }

        /* ── Receipt badge in table ── */
        .receipt-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 99px; font-size: .72rem; font-weight: 600;
        }
        .receipt-badge.all  { background: #dcfce7; color: #15803d; }
        .receipt-badge.part { background: #fef9c3; color: #92600a; }
        .receipt-badge.none { background: #f3f4f6; color: #9ca3af; }

        /* ── Signature Pad ── */
        .sig-pad-wrap {
            border: 1.5px solid #d1d5db; border-radius: .625rem;
            background: #fff; overflow: hidden;
            display: flex; flex-direction: column;
        }
        .sig-pad-wrap canvas {
            width: 100%; height: 90px; cursor: crosshair; display: block;
            touch-action: none;
        }
        .sig-pad-actions {
            display: flex; justify-content: space-between; align-items: center;
            padding: 4px 8px; border-top: 1px solid #f3f4f6; background: #fafafa;
        }
        .sig-pad-hint { font-size: .65rem; color: #9ca3af; }
        .sig-clear-btn {
            font-size: .68rem; color: #ef4444; background: none; border: none;
            cursor: pointer; font-weight: 600; padding: 2px 6px;
            border-radius: 4px; transition: background .15s;
        }
        .sig-clear-btn:hover { background: #fee2e2; }
        .sig-pad-wrap.has-sig { border-color: #528CBE; }
    </style>
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-wrap">

        {{-- Page Header --}}
        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-person-check"></i></div>
                <div>
                    <h1 class="page-title">Qaadashada Xukunku</h1>
                    <p class="page-sub">Xaqiijinta in dhinacyada dacwadu qaataan nuqulka xukunku</p>
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
                <div class="kpi-ico kpi-ico-success"><i class="bi bi-person-check-fill"></i></div>
                <div>
                    <p class="kpi-lbl">Dhamaan Qaatay</p>
                    <h3 class="kpi-val">{{ $stats['fully_received'] }}</h3>
                    <p class="kpi-sub-success">Dhammaan dhinacyadu qaatay</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-gold"><i class="bi bi-person-dash"></i></div>
                <div>
                    <p class="kpi-lbl">Ma Qaatin</p>
                    <h3 class="kpi-val">{{ $stats['not_received'] }}</h3>
                    <p class="kpi-sub-gold">Xukun aan la qaadin</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-ico kpi-ico-primary"><i class="bi bi-list-check"></i></div>
                <div>
                    <p class="kpi-lbl">Wadarta Kiisaska</p>
                    <h3 class="kpi-val">{{ $records->total() }}</h3>
                    <p class="kpi-sub-primary">Kiisaska la socda</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="data-card">

            {{-- Search & Filter --}}
            <form action="{{ route('appeal-judgments.receipts') }}" method="GET" class="reg-filter">
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
                    <a href="{{ route('appeal-judgments.receipts') }}" class="act-btn" title="Clear" style="width:auto;padding:0 .9rem;gap:.4rem;">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </form>

            {{-- Table header bar --}}
            <div class="reg-table-bar">
                <div class="reg-table-bar-left">
                    <i class="bi bi-table"></i>
                    <span class="reg-table-bar-title">Diiwaanka Xukunnada</span>
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
                            <th>Taariikhda Xukunku</th>
                            <th>Dhinacyada</th>
                            <th>Qaadashada Nuqulka</th>
                            <th>Xaalada</th>
                            <th class="center" style="width:7rem">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $i => $r)
                            @php
                                $judgment    = $r->judgments->first();
                                $parties     = $r->parties->sortBy('PID')->values();
                                $receipts    = $judgment ? $judgment->receipts->keyBy('party_id') : collect();
                                $totalP      = $parties->count();
                                $receivedP   = $receipts->count();

                                $isGreen  = in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Dhageysi', 'Xukun']);
                                $isClosed = $r->Status === 'Closed';
                                $sBg    = $isGreen  ? 'rgba(34,197,94,.12)' : ($isClosed ? 'rgba(239,68,68,.1)' : 'rgba(240,180,60,.12)');
                                $sColor = $isGreen  ? '#15803d'             : ($isClosed ? '#b91c1c' : '#C07E15');
                            @endphp
                            <tr>

                                <td><span class="td-num">{{ str_pad($records->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span></td>
                                <td><span class="badge-file">{{ $r->FileNo }}</span></td>
                                <td><span class="badge-type">{{ $r->CaseType }}</span></td>
                                <td class="td-date">
                                    {{ $judgment?->judgment_date ? \Carbon\Carbon::parse($judgment->judgment_date)->format('d/m/Y') : '—' }}
                                </td>

                                {{-- Parties summary --}}
                                <td>
                                    <div style="display:flex;flex-direction:column;gap:3px;">
                                        @foreach($parties->take(2) as $p)
                                            <span style="font-size:.75rem;color:#374151;">
                                                <span style="font-size:.7rem;color:{{ $p->party_role === 'Plaintiff' ? '#0A284D' : '#dc2626' }};font-weight:600;">
                                                    {{ $p->party_role === 'Plaintiff' ? 'D' : 'Dw' }}
                                                </span>
                                                {{ Str::limit($p->full_name, 22) }}
                                            </span>
                                        @endforeach
                                        @if($parties->count() > 2)
                                            <span style="font-size:.7rem;color:#9ca3af;">+{{ $parties->count() - 2 }} kale</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Receipt status --}}
                                <td id="receipt-cell-{{ $judgment?->id }}"
                                    data-total="{{ $totalP }}" data-received="{{ $receivedP }}">
                                    @if(!$judgment)
                                        <span class="receipt-badge none"><i class="bi bi-dash-circle"></i> Xukun ma jiro</span>
                                    @elseif($totalP === 0)
                                        <span class="receipt-badge none"><i class="bi bi-people"></i> Dhinac ma jiro</span>
                                    @elseif($receivedP >= $totalP)
                                        <span class="receipt-badge all"><i class="bi bi-check-circle-fill"></i> Dhamaan Qaatay ({{ $receivedP }}/{{ $totalP }})</span>
                                    @elseif($receivedP > 0)
                                        <span class="receipt-badge part"><i class="bi bi-hourglass-split"></i> Qayb Qaatay ({{ $receivedP }}/{{ $totalP }})</span>
                                    @else
                                        <span class="receipt-badge none"><i class="bi bi-clock"></i> Wali ma Qaatin (0/{{ $totalP }})</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="status-pill" style="background:{{ $sBg }};color:{{ $sColor }}">
                                        {{ $r->Status }}
                                    </span>
                                </td>

                                <td>
                                    <div class="td-actions">
                                        @if($judgment)
                                            @php $legalReps = $r->legalRepresentatives->keyBy('party_id'); @endphp
                                            <button type="button" title="Xaqiiji Qaadashada"
                                                class="act-btn act-btn-poa"
                                                onclick="openReceiptModal({{ $judgment->id }}, '{{ addslashes($r->FileNo) }}', {{ $parties->toJson() }}, {{ $receipts->toJson() }}, {{ $legalReps->toJson() }})">
                                                <i class="bi bi-person-check"></i>
                                            </button>
                                            <button type="button" title="Wax ka Bedel Qaadashada"
                                                class="act-btn act-btn-poa"
                                                onclick="openReceiptModal({{ $judgment->id }}, '{{ addslashes($r->FileNo) }}', {{ $parties->toJson() }}, {{ $receipts->toJson() }}, {{ $legalReps->toJson() }})">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        @else
                                            <span class="act-btn" style="opacity:.3;cursor:default;background:#f9fafb;border:1px solid #e5e7eb" title="Xukun ma jiro">
                                                <i class="bi bi-person-check"></i>
                                            </span>
                                            <span class="act-btn" style="opacity:.3;cursor:default;background:#f9fafb;border:1px solid #e5e7eb" title="Xukun ma jiro">
                                                <i class="bi bi-pencil-square"></i>
                                            </span>
                                        @endif
                                        <a href="{{ route('appeal-civil-registration.show', $r->ACID) }}" title="Arag Dacwada"
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
                                        <div class="empty-ico"><i class="bi bi-person-check"></i></div>
                                        <p class="empty-msg">Xukun lama helin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="data-card-ft">
                <p>Muujinaya <span>{{ $records->count() }}</span> diiwaaan ({{ $records->total() }} wadarta)</p>
            </div>

            <div class="px-6 py-4 border-t border-neutral-100">
                {{ $records->links() }}
            </div>
        </div>
    </div>

    {{-- ══ Receipt Modal ══ --}}
    <div class="modal-overlay" id="receipt-modal" onclick="closeOnBackdrop(event)">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-left">
                    <div class="modal-header-icon"><i class="bi bi-person-check"></i></div>
                    <div>
                        <p class="modal-title">Xaqiijinta Qaadashada Xukunku</p>
                        <p class="modal-sub" id="modal-fileno"></p>
                    </div>
                </div>
                <button class="modal-close" onclick="closeReceiptModal()">&#x2715;</button>
            </div>
            <div class="modal-body" id="modal-party-list">
                {{-- Filled by JS --}}
            </div>
        </div>
    </div>

    <script>
        let currentJudgmentId = null;
        let pendingQueue      = [];
        let modalToday        = '';
        let currentLegalReps  = {};

        function openReceiptModal(judgmentId, fileNo, parties, receipts, legalReps) {
            currentJudgmentId = judgmentId;
            currentLegalReps  = legalReps || {};
            modalToday        = new Date().toISOString().substring(0, 10);
            document.getElementById('modal-fileno').textContent = 'Dacwadda: ' + fileNo;
            const container = document.getElementById('modal-party-list');
            container.innerHTML = '';

            if (!parties || parties.length === 0) {
                container.innerHTML = '<p style="color:#9ca3af;text-align:center;padding:36px 0;font-size:.88rem;"><i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;color:#d1d5db"></i>Dhinac lama helin kiiskan.</p>';
                document.getElementById('receipt-modal').classList.add('open');
                return;
            }

            pendingQueue      = parties.filter(p => !receipts[p.PID]);
            const confirmed   = parties.filter(p =>  receipts[p.PID]);

            // Show only the FIRST pending party form
            if (pendingQueue.length > 0) buildPendingCard(container);

            // ── Confirmed parties table ──
            if (confirmed.length > 0) {
                const wrap = document.createElement('div');
                wrap.id = 'modal-confirmed-section';
                wrap.className = 'confirmed-table-wrap';

                let rows = '';
                confirmed.forEach(function(p, idx) {
                    const r         = receipts[p.PID];
                    const pid       = p.PID;
                    const isWin     = r.judgment_outcome === 'Loo Xukume';
                    const outBg     = isWin ? '#dcfce7' : '#fee2e2';
                    const outClr    = isWin ? '#15803d' : '#b91c1c';
                    const outIco    = isWin ? 'bi-trophy-fill' : 'bi-x-circle-fill';
                    const dateStr   = r.received_date ? r.received_date.substring(0,10) : (r.received_at||'').substring(0,10);
                    const isPlain   = (p.party_role||'').toLowerCase() === 'plaintiff';
                    const roleColor = isPlain ? '#0A284D' : '#b91c1c';
                    const roleBg    = isPlain ? 'rgba(10,40,77,.08)' : 'rgba(220,38,38,.08)';
                    const roleLbl   = isPlain ? 'Dacwoode' : 'Dacweysane';

                    rows += buildConfirmedRow(
                        pid, idx, p.full_name||'—', roleLbl, roleColor, roleBg,
                        r.judgment_outcome||'', outBg, outClr, outIco,
                        dateStr, r.notes||'', judgmentId
                    );
                });

                wrap.innerHTML =
                    '<div class="confirmed-table-hd">' +
                        '<div class="confirmed-table-hd-title">' +
                            '<i class="bi bi-people-fill" style="color:#15803d;font-size:.85rem;"></i>' +
                            'Dhinacyada Qaatay' +
                        '</div>' +
                        '<span id="modal-confirmed-count" style="font-size:.72rem;color:#9ca3af;">' + confirmed.length + ' qaatay</span>' +
                    '</div>' +
                    '<table class="conf-tbl">' +
                        '<thead><tr>' +
                            '<th style="width:3rem">T.T</th>' +
                            '<th>MAGACA</th>' +
                            '<th>XUKUNKA DHACAY</th>' +
                            '<th>TAARIIKHDA</th>' +
                            '<th>FAALLO</th>' +
                            '<th style="width:3rem"></th>' +
                        '</tr></thead>' +
                        '<tbody id="modal-confirmed-tbody">' + rows + '</tbody>' +
                    '</table>';

                container.appendChild(wrap);
            }

            document.getElementById('receipt-modal').classList.add('open');
        }

        function buildPendingCard(container) {
            // Remove existing pending card
            const old = document.getElementById('pending-parties-card');
            if (old) old.remove();
            if (pendingQueue.length === 0) return;

            const p         = pendingQueue[0];
            const pid       = p.PID;
            const isPlain   = (p.party_role || '').toLowerCase() === 'plaintiff';
            const roleLabel = isPlain ? 'Dacwoode' : 'Dacweysane';
            const roleColor = isPlain ? '#0A284D'  : '#b91c1c';
            const roleBg    = isPlain ? 'rgba(10,40,77,.09)' : 'rgba(220,38,38,.09)';

            const card = document.createElement('div');
            card.id = 'pending-parties-card';
            card.className = 'receipt-section';
            card.innerHTML =
                '<div class="receipt-section-hd">' +
                    '<div class="receipt-section-hd-left">' +
                        '<div class="section-num"><i class="bi bi-people-fill" style="font-size:.75rem;"></i></div>' +
                        '<span class="section-party-name">Dhinacyada Qaadanaya Xukunku</span>' +
                    '</div>' +
                    '<span class="section-role-badge" id="pending-count-badge">' + pendingQueue.length + ' dhinac</span>' +
                '</div>' +

                // Single party sub-header
                '<div id="party-row-' + pid + '" data-name="' + (p.full_name||'').replace(/"/g,'&quot;') + '" data-role="' + (p.party_role||'') + '">' +
                    '<div style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px 0;">' +
                        '<div style="display:flex;align-items:center;gap:8px;">' +
                            '<div style="width:24px;height:24px;border-radius:50%;background:rgba(82,140,190,.12);color:#528CBE;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:800;">1</div>' +
                            '<span style="font-weight:700;font-size:.88rem;color:#0A284D;">' + (p.full_name||'—') + '</span>' +
                        '</div>' +
                        '<span style="font-size:.7rem;font-weight:700;padding:2px 10px;border-radius:99px;background:' + roleBg + ';color:' + roleColor + ';">' + roleLabel + '</span>' +
                    '</div>' +

                    '<div style="padding:0 1.5rem 0;">' +
                        '<div class="form-field">' +
                            '<label>MAGACA DHINACA</label>' +
                            '<input type="text" value="' + (p.full_name||'').replace(/"/g,'&quot;') + '" class="modal-inp readonly-val" readonly>' +
                        '</div>' +
                    '</div>' +

                    '<div class="form-grid-4">' +
                        '<div class="form-field">' +
                            '<label>XAALADA DACWADDA</label>' +
                            '<select id="role_' + pid + '" class="modal-inp">' +
                                '<option value="Dacwoode"'   + (isPlain  ? ' selected' : '') + '>Dacwoode</option>' +
                                '<option value="Dacweysane"' + (!isPlain ? ' selected' : '') + '>Dacweysane</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="form-field">' +
                            '<label>XUKUNKA DHACAY <span class="req">*</span></label>' +
                            '<select id="outcome_' + pid + '" class="modal-inp" onchange="highlightOutcome(' + pid + ')">' +
                                '<option value="">— Dooro —</option>' +
                                '<option value="Loo Xukume">✓ Loo Xukume</option>' +
                                '<option value="Laga Xukume">✗ Laga Xukume</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="form-field">' +
                            '<label>SAXIIXGA <span style="color:#9ca3af;font-weight:400;text-transform:none;">(ikhtiyaari)</span></label>' +
                            '<div class="sig-pad-wrap" id="sig-wrap-' + pid + '">' +
                                '<canvas id="sig-canvas-' + pid + '" height="90"></canvas>' +
                                '<div class="sig-pad-actions">' +
                                    '<span class="sig-pad-hint"><i class="bi bi-pencil-fill"></i> Saxiix halkan</span>' +
                                    '<button type="button" class="sig-clear-btn" onclick="clearSig(' + pid + ')"><i class="bi bi-trash3"></i> Nadiifi</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-field">' +
                            '<label>TAARIIKHDA QAADASHADA</label>' +
                            '<input type="date" id="date_' + pid + '" value="' + modalToday + '" class="modal-inp">' +
                        '</div>' +
                    '</div>' +

                    '<div style="padding:0 1.5rem 0;">' +
                        '<div class="form-field">' +
                            '<label>FAALLO <span style="color:#9ca3af;font-weight:400;text-transform:none;">(ikhtiyaari)</span></label>' +
                            '<textarea id="note_' + pid + '" rows="2" placeholder="Faallo gaar ah..." class="modal-inp" style="resize:vertical;"></textarea>' +
                        '</div>' +
                    '</div>' +

                    // ── Wakiil (representative) section — only if party has a rep ──
                    (currentLegalReps[pid] ? (
                    '<div style="margin:12px 0 0;">' +

                        // Section divider header
                        '<div style="background:linear-gradient(135deg,#0A284D 0%,#1a3d6e 100%);padding:10px 20px;display:flex;align-items:center;gap:8px;">' +
                            '<div style="width:24px;height:24px;border-radius:6px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                                '<i class="bi bi-person-badge-fill" style="color:#F0B43C;font-size:.75rem;"></i>' +
                            '</div>' +
                            '<span style="font-size:.76rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.08em;">Saxiixa Wakiilka</span>' +
                            '<span style="margin-left:auto;font-size:.65rem;color:rgba(255,255,255,.55);font-weight:600;">Wakiilka Sharciga</span>' +
                        '</div>' +

                        // Wakiil form fields
                        '<div style="background:#f0f7ff;padding:14px 20px 16px;">' +
                            '<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">' +
                                '<div class="form-field">' +
                                    '<label>MAGACA WAKIILKA</label>' +
                                    '<input type="text" id="rep-name-' + pid + '" value="' + (currentLegalReps[pid].rep_name||'').replace(/"/g,'&quot;') + '" class="modal-inp readonly-val" readonly>' +
                                '</div>' +
                                '<div class="form-field">' +
                                    '<label>SAXIIXGA WAKIILKA <span style="color:#9ca3af;font-weight:400;text-transform:none;">(ikhtiyaari)</span></label>' +
                                    '<div class="sig-pad-wrap" id="rep-sig-wrap-' + pid + '" style="background:#fff;">' +
                                        '<canvas id="rep-sig-canvas-' + pid + '" height="80"></canvas>' +
                                        '<div class="sig-pad-actions">' +
                                            '<span class="sig-pad-hint"><i class="bi bi-pencil-fill"></i> Saxiix halkan</span>' +
                                            '<button type="button" class="sig-clear-btn" onclick="clearRepSig(' + pid + ')"><i class="bi bi-trash3"></i> Nadiifi</button>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +

                    '</div>'
                    ) : '') +

                    '<div class="form-actions-modal">' +
                        '<button id="btn-' + pid + '" class="btn-modal-save" onclick="confirmReceipt(' + currentJudgmentId + ',' + pid + ')">' +
                            '<i class="bi bi-cloud-arrow-up-fill"></i> Keydi' +
                        '</button>' +
                    '</div>' +
                '</div>';

            // Insert before confirmed section if it already exists
            const confSection = document.getElementById('modal-confirmed-section');
            if (confSection) {
                container.insertBefore(card, confSection);
            } else {
                container.appendChild(card);
            }

            initPadsForPid(pid);
            if (currentLegalReps[pid]) {
                setTimeout(() => initSigPad('rep-sig-canvas-' + pid, 'rep-sig-wrap-' + pid), 50);
            }
        }

        /* ══ Signature Pad Logic ══ */
        const sigPads = {};

        function initSigPad(canvasId, wrapId) {
            const canvas = document.getElementById(canvasId);
            if (!canvas || canvas._sigInit) return;
            canvas._sigInit = true;
            const ctx = canvas.getContext('2d');
            ctx.strokeStyle = '#1a4fa8';
            ctx.lineWidth   = 2;
            ctx.lineCap     = 'round';
            ctx.lineJoin    = 'round';
            let drawing = false, lastX = 0, lastY = 0;

            function pos(e) {
                const r = canvas.getBoundingClientRect();
                const scaleX = canvas.width  / r.width;
                const scaleY = canvas.height / r.height;
                const src = e.touches ? e.touches[0] : e;
                return [(src.clientX - r.left) * scaleX, (src.clientY - r.top) * scaleY];
            }
            function start(e) { e.preventDefault(); drawing = true; [lastX, lastY] = pos(e); }
            function draw(e)  {
                if (!drawing) return; e.preventDefault();
                const [x, y] = pos(e);
                ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(x, y); ctx.stroke();
                [lastX, lastY] = [x, y];
                document.getElementById(wrapId)?.classList.add('has-sig');
            }
            function stop() { drawing = false; }

            canvas.addEventListener('mousedown',  start);
            canvas.addEventListener('mousemove',  draw);
            canvas.addEventListener('mouseup',    stop);
            canvas.addEventListener('mouseleave', stop);
            canvas.addEventListener('touchstart', start, { passive: false });
            canvas.addEventListener('touchmove',  draw,  { passive: false });
            canvas.addEventListener('touchend',   stop);

            // Fix canvas pixel size to match display size
            const rect = canvas.getBoundingClientRect();
            canvas.width  = rect.width  || 300;
            canvas.height = 90;
            ctx.strokeStyle = '#1a4fa8'; ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.lineJoin = 'round';

            sigPads[canvasId] = { canvas, ctx };
        }

        function getSigData(canvasId) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return null;
            // Check if canvas has any drawing
            const blank = document.createElement('canvas');
            blank.width = canvas.width; blank.height = canvas.height;
            if (canvas.toDataURL() === blank.toDataURL()) return null;
            return canvas.toDataURL('image/png');
        }

        function clearSig(pid) {
            const canvas = document.getElementById('sig-canvas-' + pid);
            if (!canvas) return;
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('sig-wrap-' + pid)?.classList.remove('has-sig');
        }

        function clearEditSig(pid) {
            const canvas = document.getElementById('edit-sig-canvas-' + pid);
            if (!canvas) return;
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('edit-sig-wrap-' + pid)?.classList.remove('has-sig');
        }

        function clearRepSig(pid) {
            const canvas = document.getElementById('rep-sig-canvas-' + pid);
            if (!canvas) return;
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('rep-sig-wrap-' + pid)?.classList.remove('has-sig');
        }

        // Init pads after DOM is built (called after buildPendingCard / showEditForm)
        function initPadsForPid(pid) {
            setTimeout(() => {
                initSigPad('sig-canvas-' + pid, 'sig-wrap-' + pid);
            }, 50);
        }
        function initEditPadsForPid(pid) {
            setTimeout(() => {
                initSigPad('edit-sig-canvas-' + pid, 'edit-sig-wrap-' + pid);
            }, 50);
        }

        function highlightOutcome(pid) {
            const sel = document.getElementById('outcome_' + pid);
            if (sel.value === 'Loo Xukume') {
                sel.style.borderColor = '#15803d'; sel.style.color = '#15803d';
            } else if (sel.value === 'Laga Xukume') {
                sel.style.borderColor = '#dc2626'; sel.style.color = '#dc2626';
            } else {
                sel.style.borderColor = '#d1d5db'; sel.style.color = '#111827';
            }
        }


        function closeReceiptModal() {
            document.getElementById('receipt-modal').classList.remove('open');
        }

        function closeOnBackdrop(e) {
            if (e.target === document.getElementById('receipt-modal')) closeReceiptModal();
        }

        function confirmReceipt(judgmentId, partyId) {
            const btn     = document.getElementById('btn-' + partyId);
            const outcome  = document.getElementById('outcome_' + partyId)?.value || '';
            const role     = document.getElementById('role_'   + partyId)?.value || '';
            const date     = document.getElementById('date_' + partyId)?.value || '';
            const sigData  = getSigData('sig-canvas-' + partyId);
            const note     = document.getElementById('note_' + partyId)?.value || '';

            if (!outcome) {
                const sel = document.getElementById('outcome_' + partyId);
                if (sel) { sel.style.borderColor='#ef4444'; setTimeout(()=>{ sel.style.borderColor='#d1d5db'; },1500); }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Keydinaya...';

            const fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            fd.append('judgment_outcome', outcome);
            fd.append('party_role', role);
            fd.append('received_date', date);
            fd.append('notes', note);
            if (sigData) fd.append('signature_data', sigData);
            // Wakiil (representative)
            const repName    = document.getElementById('rep-name-' + partyId)?.value || '';
            const repSigData = getSigData('rep-sig-canvas-' + partyId);
            if (repName)    fd.append('rep_name', repName);
            if (repSigData) fd.append('rep_signature_data', repSigData);

            fetch('/judgment-receipts/' + judgmentId + '/' + partyId, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: fd
            })
            .then(r => r.json())
            .then(function(data) {
                if (data.ok) {
                    // Capture name/role before shifting queue
                    const partyName = pendingQueue[0] ? pendingQueue[0].full_name : '';
                    const partyRole = document.getElementById('role_' + partyId)?.value
                                   || (pendingQueue[0] ? pendingQueue[0].party_role : '');

                    // Advance queue and show next party form (or hide card)
                    pendingQueue.shift();
                    buildPendingCard(document.getElementById('modal-party-list'));

                    // Add saved party to confirmed table inside modal
                    addToConfirmedTable(partyName, partyRole, data, partyId);

                    // Update the main table row instantly
                    updateMainTableRow(currentJudgmentId);

                    // When all parties are done, reload page to refresh KPI stats
                    if (pendingQueue.length === 0) {
                        setTimeout(() => location.reload(), 1800);
                    }
                }
            })
            .catch(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-cloud-arrow-up-fill"></i> Keydi';
            });
        }

        function updateMainTableRow(judgmentId) {
            const cell = document.getElementById('receipt-cell-' + judgmentId);
            if (!cell) return;
            const total    = parseInt(cell.dataset.total)    || 0;
            let   received = parseInt(cell.dataset.received) || 0;
            received++;
            cell.dataset.received = received;

            let html;
            if (received >= total) {
                html = '<span class="receipt-badge all"><i class="bi bi-check-circle-fill"></i> Dhamaan Qaatay (' + received + '/' + total + ')</span>';
            } else if (received > 0) {
                html = '<span class="receipt-badge part"><i class="bi bi-hourglass-split"></i> Qayb Qaatay (' + received + '/' + total + ')</span>';
            } else {
                html = '<span class="receipt-badge none"><i class="bi bi-clock"></i> Wali ma Qaatin (0/' + total + ')</span>';
            }
            cell.innerHTML = html;
        }

        function addToConfirmedTable(partyName, partyRole, data, partyId) {
            const container = document.getElementById('modal-party-list');

            let confSection = document.getElementById('modal-confirmed-section');
            if (!confSection) {
                confSection = document.createElement('div');
                confSection.id = 'modal-confirmed-section';
                confSection.className = 'confirmed-table-wrap';
                confSection.innerHTML =
                    '<div class="confirmed-table-hd">' +
                        '<div class="confirmed-table-hd-title">' +
                            '<i class="bi bi-people-fill" style="color:#15803d;font-size:.85rem;"></i>' +
                            'Dhinacyada Qaatay' +
                        '</div>' +
                        '<span id="modal-confirmed-count" style="font-size:.72rem;color:#9ca3af;">0 qaatay</span>' +
                    '</div>' +
                    '<table class="conf-tbl">' +
                        '<thead><tr>' +
                            '<th style="width:3rem">T.T</th>' +
                            '<th>MAGACA</th>' +
                            '<th>XUKUNKA DHACAY</th>' +
                            '<th>TAARIIKHDA</th>' +
                            '<th>FAALLO</th>' +
                            '<th style="width:3rem"></th>' +
                        '</tr></thead>' +
                        '<tbody id="modal-confirmed-tbody"></tbody>' +
                    '</table>';
                container.appendChild(confSection);
            }

            const tbody    = document.getElementById('modal-confirmed-tbody');
            const rowCount = tbody.querySelectorAll('[id^="conf-display-"]').length + 1;
            const isWin    = data.judgment_outcome === 'Loo Xukume';
            const outBg    = isWin ? '#dcfce7' : '#fee2e2';
            const outClr   = isWin ? '#15803d' : '#b91c1c';
            const outIco   = isWin ? 'bi-trophy-fill' : 'bi-x-circle-fill';
            const isPlain  = partyRole === 'Dacwoode' || (partyRole||'').toLowerCase() === 'plaintiff';
            const roleClr  = isPlain ? '#0A284D' : '#b91c1c';
            const roleBg   = isPlain ? 'rgba(10,40,77,.08)' : 'rgba(220,38,38,.08)';
            const roleLbl  = isPlain ? 'Dacwoode' : 'Dacweysane';

            tbody.insertAdjacentHTML('beforeend', buildConfirmedRow(
                partyId, rowCount - 1, partyName||'—', roleLbl, roleClr, roleBg,
                data.judgment_outcome||'', outBg, outClr, outIco,
                data.received_date||'—', data.notes||'', currentJudgmentId
            ));

            const countSpan = document.getElementById('modal-confirmed-count');
            if (countSpan) countSpan.textContent = rowCount + ' qaatay';
        }

        function buildConfirmedRow(pid, idx, name, roleLbl, roleClr, roleBg, outcome, outBg, outClr, outIco, dateStr, notes, judgmentId) {
            const escapedOutcome = (outcome||'').replace(/"/g,'&quot;');
            const escapedNotes   = (notes||'').replace(/"/g,'&quot;');
            return (
                '<tr id="conf-display-' + pid + '" data-outcome="' + escapedOutcome + '" data-date="' + dateStr + '" data-notes="' + escapedNotes + '" data-role="' + roleLbl + '">' +
                    '<td style="color:#9ca3af;font-size:.78rem;">' + String(idx+1).padStart(2,'0') + '</td>' +
                    '<td>' +
                        '<div style="font-weight:700;font-size:.83rem;color:#111827;">' + name + '</div>' +
                        '<span id="conf-role-badge-' + pid + '" style="font-size:.68rem;font-weight:700;padding:1px 8px;border-radius:99px;background:' + roleBg + ';color:' + roleClr + ';">' + roleLbl + '</span>' +
                    '</td>' +
                    '<td id="conf-outcome-' + pid + '">' +
                        (outcome ? '<span class="outcome-pill" style="background:' + outBg + ';color:' + outClr + ';"><i class="bi ' + outIco + '"></i> ' + outcome + '</span>' : '<span style="color:#d1d5db">—</span>') +
                    '</td>' +
                    '<td id="conf-date-' + pid + '" style="font-size:.8rem;color:#374151;"><i class="bi bi-calendar-check" style="color:#9ca3af;margin-right:4px;"></i>' + (dateStr||'—') + '</td>' +
                    '<td id="conf-notes-' + pid + '" style="font-size:.78rem;color:#6b7280;">' + (notes||'—') + '</td>' +
                    '<td>' +
                        '<button onclick="showEditForm(' + pid + ',' + judgmentId + ')" style="width:28px;height:28px;border-radius:.5rem;border:1px solid #e5e7eb;background:#f9fafb;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#528CBE;" title="Wax ka bedel">' +
                            '<i class="bi bi-pencil-square" style="font-size:.78rem;"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>' +
                '<tr id="conf-edit-' + pid + '" style="display:none;">' +
                    '<td colspan="6" style="padding:14px 16px;background:#f0f7ff;">' +
                        '<div class="form-field" style="margin-bottom:10px;">' +
                            '<label>MAGACA DHINACA</label>' +
                            '<input type="text" value="' + (name||'').replace(/"/g,'&quot;') + '" class="modal-inp readonly-val" readonly>' +
                        '</div>' +
                        '<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px;">' +
                            '<div class="form-field"><label>XAALADA DACWADDA</label>' +
                                '<select id="edit-role-' + pid + '" class="modal-inp">' +
                                    '<option value="Dacwoode">Dacwoode</option>' +
                                    '<option value="Dacweysane">Dacweysane</option>' +
                                '</select></div>' +
                            '<div class="form-field"><label>XUKUNKA DHACAY <span class="req">*</span></label>' +
                                '<select id="edit-outcome-' + pid + '" class="modal-inp">' +
                                    '<option value="">— Dooro —</option>' +
                                    '<option value="Loo Xukume">✓ Loo Xukume</option>' +
                                    '<option value="Laga Xukume">✗ Laga Xukume</option>' +
                                '</select></div>' +
                            '<div class="form-field"><label>SAXIIXGA <span style="color:#9ca3af;font-weight:400;text-transform:none;">(ikhtiyaari)</span></label>' +
                                '<div class="sig-pad-wrap" id="edit-sig-wrap-' + pid + '">' +
                                    '<canvas id="edit-sig-canvas-' + pid + '" height="90"></canvas>' +
                                    '<div class="sig-pad-actions">' +
                                        '<span class="sig-pad-hint"><i class="bi bi-pencil-fill"></i> Saxiix halkan</span>' +
                                        '<button type="button" class="sig-clear-btn" onclick="clearEditSig(' + pid + ')"><i class="bi bi-trash3"></i> Nadiifi</button>' +
                                    '</div>' +
                                '</div></div>' +
                            '<div class="form-field"><label>TAARIIKHDA</label>' +
                                '<input type="date" id="edit-date-' + pid + '" class="modal-inp"></div>' +
                        '</div>' +
                        '<div class="form-field" style="margin-top:10px;"><label>FAALLO <span style="color:#9ca3af;font-weight:400;text-transform:none;">(ikhtiyaari)</span></label>' +
                            '<textarea id="edit-note-' + pid + '" rows="2" class="modal-inp" style="resize:vertical;"></textarea></div>' +
                        '<div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px;">' +
                            '<button onclick="cancelEditForm(' + pid + ')" style="padding:.5rem 1.2rem;border-radius:.75rem;border:1px solid #d1d5db;background:#fff;font-size:.82rem;font-weight:600;cursor:pointer;color:#6b7280;">Jooji</button>' +
                            '<button id="edit-btn-' + pid + '" onclick="saveEditForm(' + judgmentId + ',' + pid + ')" class="btn-modal-save" style="padding:.5rem 1.4rem;">' +
                                '<i class="bi bi-cloud-arrow-up-fill"></i> Keydi</button>' +
                        '</div>' +
                    '</td>' +
                '</tr>'
            );
        }

        function showEditForm(pid, judgmentId) {
            const row = document.getElementById('conf-display-' + pid);
            if (!row) return;
            // Pre-fill from data attributes
            const roleEl    = document.getElementById('edit-role-'    + pid);
            const outcomeEl = document.getElementById('edit-outcome-' + pid);
            const dateEl    = document.getElementById('edit-date-'    + pid);
            const noteEl    = document.getElementById('edit-note-'    + pid);
            if (roleEl)    roleEl.value    = row.dataset.role    || '';
            if (outcomeEl) outcomeEl.value = row.dataset.outcome || '';
            if (dateEl)    dateEl.value    = row.dataset.date    || '';
            if (noteEl)    noteEl.value    = row.dataset.notes   || '';
            row.style.display = 'none';
            document.getElementById('conf-edit-' + pid).style.display = '';
            initEditPadsForPid(pid);
        }

        function cancelEditForm(pid) {
            document.getElementById('conf-edit-'    + pid).style.display = 'none';
            document.getElementById('conf-display-' + pid).style.display = '';
        }

        function saveEditForm(judgmentId, pid) {
            const btn     = document.getElementById('edit-btn-' + pid);
            const outcome  = document.getElementById('edit-outcome-' + pid)?.value || '';
            const role     = document.getElementById('edit-role-'    + pid)?.value || '';
            const date     = document.getElementById('edit-date-'    + pid)?.value || '';
            const sigData  = getSigData('edit-sig-canvas-' + pid);
            const note     = document.getElementById('edit-note-'    + pid)?.value || '';

            if (!outcome) {
                const sel = document.getElementById('edit-outcome-' + pid);
                if (sel) { sel.style.borderColor = '#ef4444'; setTimeout(() => { sel.style.borderColor = '#d1d5db'; }, 1500); }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Keydinaya...';

            const fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            fd.append('judgment_outcome', outcome);
            fd.append('party_role', role);
            fd.append('received_date', date);
            fd.append('notes', note);
            if (sigData) fd.append('signature_data', sigData);

            fetch('/judgment-receipts/' + judgmentId + '/' + pid, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: fd
            })
            .then(r => r.json())
            .then(function(data) {
                if (data.ok) {
                    const isWin  = data.judgment_outcome === 'Loo Xukume';
                    const outBg  = isWin ? '#dcfce7' : '#fee2e2';
                    const outClr = isWin ? '#15803d' : '#b91c1c';
                    const outIco = isWin ? 'bi-trophy-fill' : 'bi-x-circle-fill';
                    const isPlain = role === 'Dacwoode';
                    const roleClr = isPlain ? '#0A284D' : '#b91c1c';
                    const roleBg  = isPlain ? 'rgba(10,40,77,.08)' : 'rgba(220,38,38,.08)';

                    // Update display row cells
                    const display = document.getElementById('conf-display-' + pid);
                    if (display) {
                        display.dataset.outcome = data.judgment_outcome || '';
                        display.dataset.date    = data.received_date    || '';
                        display.dataset.notes   = data.notes            || '';
                        display.dataset.role    = role;
                    }
                    const outcomeCell = document.getElementById('conf-outcome-' + pid);
                    const dateCell    = document.getElementById('conf-date-'    + pid);
                    const notesCell   = document.getElementById('conf-notes-'   + pid);
                    const roleBadge   = document.getElementById('conf-role-badge-' + pid);
                    if (outcomeCell) outcomeCell.innerHTML = '<span class="outcome-pill" style="background:' + outBg + ';color:' + outClr + ';"><i class="bi ' + outIco + '"></i> ' + data.judgment_outcome + '</span>';
                    if (dateCell)    dateCell.innerHTML    = '<i class="bi bi-calendar-check" style="color:#9ca3af;margin-right:4px;"></i>' + (data.received_date || '—');
                    if (notesCell)   notesCell.textContent = data.notes || '—';
                    if (roleBadge)   { roleBadge.textContent = role; roleBadge.style.background = roleBg; roleBadge.style.color = roleClr; }

                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-cloud-arrow-up-fill"></i> Keydi';
                    cancelEditForm(pid);
                }
            })
            .catch(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-cloud-arrow-up-fill"></i> Keydi';
            });
        }

    </script>
@endsection
