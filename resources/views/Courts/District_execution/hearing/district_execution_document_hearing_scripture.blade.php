@extends('layouts.document_print')
@section('page_title', 'Hearing Scripture Document')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-document.css') }}">
    <style>
        /* ── Restore list styles stripped by Tailwind Preflight ── */
        #hearing-doc ol {
            list-style-type: decimal;
            padding-left: 1.6em;
            margin: 0.4em 0;
        }
        #hearing-doc ul {
            list-style-type: disc;
            padding-left: 1.6em;
            margin: 0.4em 0;
        }
        #hearing-doc li { margin: 0.25em 0; }
        /* Quill nested-indent classes */
        #hearing-doc .ql-indent-1 { padding-left: 3em; }
        #hearing-doc .ql-indent-2 { padding-left: 4.5em; }
        #hearing-doc .ql-indent-3 { padding-left: 6em; }
        #hearing-doc ol ol, #hearing-doc ol ul { list-style-type: lower-alpha; }
        #hearing-doc ul ul { list-style-type: circle; }

        /* On-screen: justify body text */
        #hearing-doc .doc-body { text-align: justify; }
        #hearing-doc .doc-body p,
        #hearing-doc .doc-body div { text-align: justify; }
        /* Exceptions: centred elements stay centred */
        #hearing-doc .doc-title,
        #hearing-doc .lid-separator,
        #hearing-doc .sig-name,
        #hearing-doc .sig-position,
        #hearing-doc p[style*="text-align:center"],
        #hearing-doc p[style*="text-align: center"] { text-align: center !important; }
        /* Party / panel lines stay left-aligned */
        #hearing-doc [style*="display:flex"],
        #hearing-doc [style*="display: flex"] { text-align: left; }

        @media print {
            /* ── Hide admin chrome ── */
            #sidebar,
            header.app-header,
            #overlay,
            .no-print { display: none !important; }

            /* ── Unlock scrolling containers ── */
            body, html {
                overflow: visible !important;
                background: white !important;
                height: auto !important;
            }
            main {
                margin-left: 0 !important;
                padding-top: 0 !important;
                height: auto !important;
                overflow: visible !important;
            }

            /* ── Watermark: one centered instance per printed page ── */
            .watermark {
                position: fixed !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                z-index: 0;
            }

            /* ── Document shell ── */
            .doc-wrapper { padding: 0 !important; }

            /* Switch from flex to block so page-break rules work reliably */
            #hearing-doc {
                display: block !important;
                width: 100% !important;
                min-height: 0 !important;
                height: auto !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }
            .doc-page-multi { min-height: 0 !important; }

            /* Body grows naturally; no flex stretch needed in print */
            .doc-page-multi .doc-body {
                flex: none !important;
                display: block !important;
            }

            /* Keep footer attached to the last page — never orphan it */
            .doc-footer {
                display: block !important;
                page-break-before: avoid !important;
                break-before: avoid !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Keep signatures + footer together on the same page */
            .doc-body > div:last-child,
            #hearing-doc .sig-row,
            #hearing-doc [style*="margin-top:48px"] {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            /* Justify text in print too */
            #hearing-doc .doc-body,
            #hearing-doc .doc-body p,
            #hearing-doc .doc-body div { text-align: justify; }
            #hearing-doc .doc-title,
            #hearing-doc .lid-separator,
            #hearing-doc .sig-name,
            #hearing-doc .sig-position { text-align: center !important; }

            /* Restore list styles in print */
            #hearing-doc ol { list-style-type: decimal; padding-left: 1.6em; margin: 0.4em 0; }
            #hearing-doc ul { list-style-type: disc;    padding-left: 1.6em; margin: 0.4em 0; }
            #hearing-doc li { margin: 0.25em 0; }
            #hearing-doc .ql-indent-1 { padding-left: 3em; }
            #hearing-doc .ql-indent-2 { padding-left: 4.5em; }
            #hearing-doc .ql-indent-3 { padding-left: 6em; }
            #hearing-doc ol ol, #hearing-doc ol ul { list-style-type: lower-alpha; }
            #hearing-doc ul ul { list-style-type: circle; }

            @page {
                size: A4;
                margin: 12mm 15mm 18mm 15mm;

                @bottom-center {
                    content: "— " counter(page) " / " counter(pages) " —";
                    font-family: 'Times New Roman', Times, serif;
                    font-size: 8.5pt;
                    color: #6b7280;
                    letter-spacing: .08em;
                    border-top: 1px solid #d1d5db;
                    padding-top: 4pt;
                    width: 100%;
                    text-align: center;
                }
            }
        }
    </style>
@endpush

@section('admin_main_content')

    @php
        $courtName = $court->longName ?? 'Maxkamadda';
        $courtArabic = $court->arabic_name ?? 'محكمة';
        $courtLogo = asset('images/logo.png');
        $courtStamp = $court->stamp ? asset('storage/' . $court->stamp) : null;
        $courtLetterhead = $court->letterhead ? asset('storage/' . $court->letterhead) : null;
        $courtAddress = ucwords(strtolower($court->address ?? ''));
        $courtEmail = $court->email ?? null;
        $courtPhone = $court->telephone ?? null;
        $courtWebsite = strtoupper($court->website ?? '');
        $docDate = \Carbon\Carbon::parse($scripture->created_at)->format('d/m/Y');
        $hearingDate = $scripture->hearing_date ? \Carbon\Carbon::parse($scripture->hearing_date)->format('d/m/Y') : '—';
        $hearingTime = $scripture->hearing_time ? substr($scripture->hearing_time, 0, 5) : '—';
        $ref = $case->FileNo;
        $sessionOrdinal = $scripture->session_number . 'AAD';
        $allParties = $case->parties->sortBy('PID')->values();
        $plaintiffs = $case->parties->where('party_role', 'Dacwoode')->values();
        $defendants = $case->parties->where('party_role', 'Dacwaysane')->values();
        // Fallback: if no formal plaintiff/defendant split, first party = Dacwoode, rest = Dacwaysane
        if ($plaintiffs->isEmpty() && $defendants->isEmpty()) {
            $plaintiffs = $allParties->take(1);
            $defendants = $allParties->skip(1)->values();
        } elseif ($plaintiffs->isEmpty()) {
            $plaintiffs = $defendants->take(1);
            $defendants = $defendants->skip(1)->values();
        }
        $chair = $case->assignments->whereIn('panel_role', ['Chair', 'Guddoomiye'])->first() ?? $case->assignments->first();
        $clerk = $case->assignments->whereIn('panel_role', ['Clerk', 'Kaaliye'])->first();
        $panelMembers = $case->assignments->whereNotIn('panel_role', ['Chair', 'Guddoomiye', 'Clerk', 'Kaaliye'])
            ->filter(fn($a) => $a->id !== ($chair?->id));
        $legalByPid  = $case->legalRepresentatives->keyBy('party_id');
        $legalByRole = $case->legalRepresentatives->keyBy('party_role');
        $lawyersByPid  = $case->lawyers->groupBy('party_id');
        $lawyersByRole = $case->lawyers->groupBy('party_role');
        $getPartyLawyers = function ($party) use ($lawyersByPid, $lawyersByRole) {
            $byId = $party->PID ? ($lawyersByPid->get($party->PID) ?? collect()) : collect();
            $byRole = $lawyersByRole->get($party->party_role) ?? collect();
            return $byId->count() ? $byId : $byRole;
        };
    @endphp

    {{-- Toolbar (hidden on print) --}}
    <div class="no-print doc-toolbar">
        <div class="doc-toolbar-left">
            <div class="doc-toolbar-icon">
                <i class="bi bi-journal-text"></i>
            </div>
            <div>
                <h1 class="doc-toolbar-title">Dhageysiga Dacwada Fulinta</h1>
                <p class="doc-toolbar-sub">

                    Gal Lambar:<span>{{ $case->FileNo }}</span> &nbsp;·&nbsp;

                </p>
            </div>
        </div>
        <div class="doc-toolbar-actions">

            {{-- Signature action --}}
            @if($myRole && !$myAlreadySigned && !$isComplete)
                <button onclick="openSignModal()"
                    style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;font-size:.82rem;font-weight:700;color:white;background:{{ $myRole === 'judge' ? '#0A284D' : '#528CBE' }};border:none;border-radius:.5rem;cursor:pointer">
                    <i class="bi bi-pen-fill"></i>
                    {{ $myRole === 'judge' ? 'Saxiix (Garsooraha)' : 'Saxiix (Kaaliyaha)' }}
                </button>
            @elseif($isComplete)
                <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.45rem 1.1rem;font-size:.82rem;font-weight:700;color:#065f46;background:rgba(16,185,129,.12);border-radius:.5rem">
                    <i class="bi bi-patch-check-fill"></i> Saxiixyo Dhameeyay
                </span>
            @endif

            <button onclick="window.print()" id="download-btn" class="btn-download">
                <i class="bi bi-file-earmark-arrow-down"></i> Download PDF
            </button>
            <a href="{{ url()->previous() }}" class="btn-doc-back">
                <i class="bi bi-arrow-left"></i> Ka Noqo
            </a>
        </div>
    </div>

    {{-- A4 Document --}}
    <div class="doc-wrapper" id="doc-wrapper">
        <div id="hearing-doc" class="doc-page-multi">

            {{-- ═══ LETTERHEAD HEADER ═══ --}}
            @if($courtLetterhead)
                <div class="doc-letterhead-img">
                    <img src="{{ $courtLetterhead }}" alt="{{ $courtName }} Letterhead">
                </div>
            @else
                <div class="doc-header">
                    <div class="header-left">
                        <span class="org-somali">Jamhuuriyadda Federaalka Soomaaliya</span>
                        <span class="court-somali">{{ $courtName }}</span>
                    </div>
                    <div class="header-center">
                        <img src="{{ $courtLogo }}" alt="{{ $courtName }}" class="header-logo">
                    </div>
                    <div class="header-right">
                        <span class="org-arabic">جمهـوريـة الصومـال الفيدرالية</span>
                        <span class="court-arabic">{{ $courtArabic }}</span>
                    </div>
                </div>
                <div class="header-english-sub">
                    <span class="org-english">Federal Republic of Somalia</span>
                    <span class="court-english">{{ $courtName }}</span>
                </div>
                <div class="header-divider"></div>
            @endif

            {{-- ═══ WATERMARK ═══ --}}
            <div class="watermark">
                <img src="{{ $courtLogo }}" alt="">
            </div>

            {{-- ═══ DOCUMENT BODY ═══ --}}
            <div class="doc-body">

                {{-- Document category label --}}
                <p style="text-align:center;font-weight:700;font-size:13px;margin-bottom:12px;letter-spacing:.04em">QAYBTA
                    FULINTA</p>

                {{-- Ref + Date --}}
                <div class="ref-row">
                    <span><strong>{{ $ref }}</strong></span>
                    <span><strong>{{ $hearingDate }}</strong></span>
                </div>

                {{-- Session Title --}}
                <p class="doc-title">GARMAQALKA FADHIGIISA {{ $sessionOrdinal }}</p>

                {{-- Court panel intro with date --}}
                <p class="doc-para">
                    Maanta oo ay taariikhdu tahay <strong>{{ $hearingDate }}</strong>
                    {{ $courtName }} oo ka kooban:-
                </p>

                {{-- Panel members with dotted leader lines --}}
                @php $pn = 1; @endphp
                @if($chair?->employee)
                    <div style="display:flex;align-items:baseline;margin:6px 20px;">
                        <span style="flex-shrink:0;font-weight:700;min-width:22px;">{{ $pn++ }}.</span>
                        <span style="flex-shrink:0;font-weight:700;margin-left:4px;">{{ $chair->employee->EmpName }}</span>
                        <span style="flex:1;border-bottom:1px dotted #222;margin:0 8px 3px;"></span>
                        <span style="flex-shrink:0;font-weight:700;">Garsoore</span>
                    </div>
                @endif
                @foreach($panelMembers as $m)
                    @if($m->employee)
                        <div style="display:flex;align-items:baseline;margin:6px 20px;">
                            <span style="flex-shrink:0;font-weight:700;min-width:22px;">{{ $pn++ }}.</span>
                            <span style="flex-shrink:0;font-weight:700;margin-left:4px;">{{ $m->employee->EmpName }}</span>
                            <span style="flex:1;border-bottom:1px dotted #222;margin:0 8px 3px;"></span>
                            <span style="flex-shrink:0;font-weight:700;">Xubin</span>
                        </div>
                    @endif
                @endforeach
                @if($clerk?->employee)
                    <div style="display:flex;align-items:baseline;margin:6px 20px;">
                        <span style="flex-shrink:0;font-weight:700;min-width:22px;">{{ $pn++ }}.</span>
                        <span style="flex-shrink:0;font-weight:700;margin-left:4px;">{{ $clerk->employee->EmpName }}</span>
                        <span style="flex:1;border-bottom:1px dotted #222;margin:0 8px 3px;"></span>
                        <span style="flex-shrink:0;font-weight:700;">Kaaliye</span>
                    </div>
                @endif

                {{-- Session description paragraph --}}
                <p class="doc-para doc-para-mt">
                    Waxay u fadhiisatay fadhiga
                    <strong>{{ $scripture->session_number }}<sup>aad</sup></strong>
                    dhageysiga dacwad madani ah ee summadeedu tahay:
                    <strong>{{ $ref }},</strong>
                    dacwaddaas oo ka dhaxeysa dhinacyada kala ah:-
                </p>

                {{-- Plaintiffs (Dacwoode) --}}
                @php $partyNum = 1; @endphp
                @foreach($plaintiffs as $p)
                    @php
                        $rep = $p->PID ? ($legalByPid->get($p->PID) ?? $legalByRole->get($p->party_role)) : $legalByRole->get($p->party_role);
                        $partyLawyers = $getPartyLawyers($p);
                        $lawyerNames = $partyLawyers->map(fn($l) => $l->lawyer?->LawyerName)->filter()->unique()->implode(' iyo ');
                    @endphp
                    <div style="margin:10px 20px 2px;">
                        <div style="display:flex;align-items:baseline;">
                            <span style="flex-shrink:0;font-weight:700;min-width:28px;">{{ $partyNum++ }}.</span>
                            <span style="flex-shrink:0;font-weight:700;margin-left:4px;">{{ $p->full_name }}</span>
                            <span style="flex:1;border-bottom:1px dotted #222;margin:0 10px 3px;"></span>
                            <span style="flex-shrink:0;font-weight:700;">{{ $p->party_role }}</span>
                        </div>
                        @if($rep)
                            <div style="margin-left:32px;font-size:12.5px;margin-top:3px;">
                                <strong>Wakiil:</strong> {{ $rep->rep_name }}.
                            </div>
                        @endif
                        @if($lawyerNames)
                            <div style="margin-left:32px;font-size:12.5px;margin-top:3px;">
                                <strong>Qareeno:</strong> {{ $lawyerNames }}.
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- LID separator --}}
                @if($plaintiffs->count() > 0 && $defendants->count() > 0)
                    <p class="lid-separator">L &nbsp;&nbsp; I &nbsp;&nbsp; D</p>
                @endif

                {{-- Defendants (Dacwaysane) — numbering continues from plaintiffs --}}
                @foreach($defendants as $d)
                    @php
                        $rep = $d->PID ? ($legalByPid->get($d->PID) ?? $legalByRole->get($d->party_role)) : $legalByRole->get($d->party_role);
                        $partyLawyers = $getPartyLawyers($d);
                        $lawyerNames = $partyLawyers->map(fn($l) => $l->lawyer?->LawyerName)->filter()->unique()->implode(' iyo ');
                    @endphp
                    <div style="margin:10px 20px 2px;">
                        <div style="display:flex;align-items:baseline;">
                            <span style="flex-shrink:0;font-weight:700;min-width:28px;">{{ $partyNum++ }}.</span>
                            <span style="flex-shrink:0;font-weight:700;margin-left:4px;">{{ $d->full_name }}</span>
                            <span style="flex:1;border-bottom:1px dotted #222;margin:0 10px 3px;"></span>
                            <span style="flex-shrink:0;font-weight:700;">{{ $d->party_role }}</span>
                        </div>
                        @if($rep)
                            <div style="margin-left:32px;font-size:12.5px;margin-top:3px;">
                                <strong>Wakiil:</strong> {{ $rep->rep_name }}.
                            </div>
                        @endif
                        @if($lawyerNames)
                            <div style="margin-left:32px;font-size:12.5px;margin-top:3px;">
                                <strong>Qareeno:</strong> {{ $lawyerNames }}.
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- ── Body Content ── --}}
                @if($scripture->body_content)
                    <div style="margin-top:16px;line-height:1.7;font-size:13px;text-align:justify;">
                        {!! $scripture->body_content !!}
                    </div>
                @endif

                {{-- ── Signatures ── --}}
                <div style="margin-top:48px;display:flex;align-items:flex-end;justify-content:space-between;page-break-inside:avoid;break-inside:avoid;">

                    {{-- Judge --}}
                    <div style="text-align:center;flex:1;">
                        @if($judgeSig?->signer?->signature)
                            <img src="{{ asset($judgeSig->signer->signature) }}"
                                 style="height:52px;max-width:160px;object-fit:contain;display:block;margin:0 auto .3rem">
                        @else
                            <div style="height:52px"></div>
                        @endif
                        <p class="sig-name">{{ $chair?->employee?->EmpName ?? '____________________________' }}</p>
                        <p class="sig-position">{{ $chair?->employee?->Position ?? 'Garsooraha' }}</p>
                    </div>

                    {{-- QR code centred between the two signatures --}}
                    <div style="flex:0 0 90px;display:flex;align-items:flex-end;justify-content:center;">
                        <div id="scripture-qr" style="position:static;transform:none;top:auto;right:auto;width:80px;height:80px;line-height:0;"></div>
                    </div>

                    {{-- Clerk --}}
                    <div style="text-align:center;flex:1;">
                        @if($clerkSig?->signer?->signature)
                            <img src="{{ asset($clerkSig->signer->signature) }}"
                                 style="height:52px;max-width:160px;object-fit:contain;display:block;margin:0 auto .3rem">
                        @else
                            <div style="height:52px"></div>
                        @endif
                        <p class="sig-name">{{ $clerk?->employee?->EmpName ?? $scripture->created_by ?? '____________________________' }}</p>
                        <p class="sig-position">{{ $clerk?->employee?->Position ?? 'Diiwaangeliyaha' }}</p>
                    </div>

                </div>

            </div>{{-- /doc-body --}}

        </div>{{-- /hearing-doc --}}
    </div>

    {{-- QR Code --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        (function () {
            function initQR() {
                var el = document.getElementById('scripture-qr');
                if (!el) return;
                if (typeof QRCode === 'undefined') { setTimeout(initQR, 150); return; }
                new QRCode(el, {
                    text: '{{ $case->FileNo }}',
                    width: 80, height: 80,
                    colorDark: '#0A284D', colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initQR);
            } else {
                initQR();
            }
        })();
    </script>

    {{-- ═══ SIGN MODAL ═══ --}}
    @if($myRole && !$myAlreadySigned && !$isComplete)
    <div id="sign-modal"
         style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:16px;padding:2rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:1rem">

            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem">
                <div style="width:40px;height:40px;background:#0A284D;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-pen" style="color:white;font-size:1.1rem"></i>
                </div>
                <div>
                    <h3 style="font-size:1rem;font-weight:800;color:#111827;margin:0">
                        {{ $myRole === 'judge' ? 'Saxiix — Garsooraha' : 'Saxiix — Kaaliyaha' }}
                    </h3>
                    <p style="font-size:.78rem;color:#6b7280;margin:0">{{ $ref }} · Garmaqalka {{ $scripture->session_number }}aad</p>
                </div>
                <button onclick="closeSignModal()"
                        style="margin-left:auto;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">
                    Magacaaga
                </label>
                <input type="text" readonly value="{{ auth()->user()->name }}"
                       style="width:100%;padding:.65rem .875rem;font-size:.875rem;border:1.5px solid #e5e7eb;border-radius:8px;background:#f9fafb;color:#111827;outline:none;box-sizing:border-box;cursor:default">
                <input type="hidden" id="sign-role" value="{{ $myRole }}">
            </div>

            <div style="margin-bottom:1.25rem">
                <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">
                    Erayga Sirta <span style="color:#ef4444">*</span>
                </label>
                <input type="password" id="sign-password" placeholder="Geli eraygaaga sirta ah..."
                       style="width:100%;padding:.65rem .875rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:8px;background:white;color:#111827;outline:none;box-sizing:border-box"
                       onfocus="this.style.borderColor='#0A284D'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            <p id="sign-error"
               style="display:none;font-size:.8rem;color:#dc2626;background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.2);border-radius:6px;padding:.5rem .75rem;margin-bottom:.75rem"></p>

            <div style="display:flex;gap:.75rem">
                <button onclick="closeSignModal()"
                        style="flex:1;padding:.7rem;font-size:.82rem;font-weight:700;color:#6b7280;background:white;border:1.5px solid #e5e7eb;border-radius:8px;cursor:pointer">
                    Jooji
                </button>
                <button id="sign-confirm-btn" onclick="submitSign()"
                        style="flex:2;padding:.7rem;font-size:.82rem;font-weight:700;color:white;background:#0A284D;border:none;border-radius:8px;cursor:pointer">
                    <i class="bi bi-patch-check"></i> Xaqiiji Saxiixa
                </button>
            </div>
        </div>
    </div>

    <script>
    function openSignModal() {
        var m = document.getElementById('sign-modal');
        if (m) { m.style.display = 'flex'; document.getElementById('sign-password').focus(); }
    }
    function closeSignModal() {
        var m = document.getElementById('sign-modal');
        if (m) {
            m.style.display = 'none';
            document.getElementById('sign-password').value = '';
            document.getElementById('sign-error').style.display = 'none';
        }
    }
    function submitSign() {
        var btn  = document.getElementById('sign-confirm-btn');
        var pass = document.getElementById('sign-password').value;
        var role = document.getElementById('sign-role').value;
        var err  = document.getElementById('sign-error');
        if (!pass) { err.textContent = 'Fadlan erayga sirta ah geli.'; err.style.display = 'block'; return; }
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sugaya...';
        fetch('{{ route("document.sign", ["type" => "execution_scripture", "id" => $scripture->id]) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ password: pass, role: role })
        })
        .then(function(r) { if (!r.ok && r.status !== 422) throw new Error('server'); return r.json(); })
        .then(function(data) {
            if (data.success) {
                closeSignModal();
                var toast = document.createElement('div');
                toast.style.cssText = 'position:fixed;top:24px;right:24px;z-index:99999;background:#0A284D;color:white;padding:14px 22px;border-radius:12px;font-weight:700;font-size:.875rem;box-shadow:0 8px 30px rgba(0,0,0,.2);display:flex;align-items:center;gap:.6rem';
                toast.innerHTML = '<i class="bi bi-patch-check-fill" style="font-size:1.1rem;color:#10b981"></i> Saxiixa si guul leh ayaa lagu keydsaday!';
                document.body.appendChild(toast);
                setTimeout(function() { window.location.reload(); }, 1400);
            } else {
                err.textContent = data.message || 'Khalad ayaa dhacay.';
                err.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-patch-check"></i> Xaqiiji Saxiixa';
            }
        })
        .catch(function() {
            err.textContent = 'Xiriirka shabakadda waa xumaaday. Isku day mar kale.';
            err.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-patch-check"></i> Xaqiiji Saxiixa';
        });
    }
    </script>
    @endif

@endsection