@extends('admin.admin_master')
@section('page_title', 'Judgment Document')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/court-document.css') }}">
<style>
    /* ── Restore list styles stripped by Tailwind Preflight ── */
    #judgment-doc ol { list-style-type: decimal; padding-left: 1.6em; margin: 0.4em 0; }
    #judgment-doc ul { list-style-type: disc;    padding-left: 1.6em; margin: 0.4em 0; }
    #judgment-doc li { margin: 0.25em 0; }
    #judgment-doc .ql-indent-1 { padding-left: 3em; }
    #judgment-doc .ql-indent-2 { padding-left: 4.5em; }
    #judgment-doc .ql-indent-3 { padding-left: 6em; }
    #judgment-doc ol ol, #judgment-doc ol ul { list-style-type: lower-alpha; }
    #judgment-doc ul ul { list-style-type: circle; }

    /* On-screen justify */
    #judgment-doc .doc-body { text-align: justify; }
    #judgment-doc .doc-body p, #judgment-doc .doc-body div { text-align: justify; }
    #judgment-doc .doc-title, #judgment-doc .lid-separator,
    #judgment-doc .sig-name, #judgment-doc .sig-position,
    #judgment-doc p[style*="text-align:center"],
    #judgment-doc p[style*="text-align: center"] { text-align: center !important; }
    #judgment-doc [style*="display:flex"],
    #judgment-doc [style*="display: flex"] { text-align: left; }

    /* Word-body fallback */
    .word-body { font-family: inherit; color: #111; }
    .word-body p { margin: 0 0 6px; }
    .word-body table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    .word-body td, .word-body th { padding: 4px 6px; border: 1px solid #ccc; font-size: 12px; }
    .word-body img { max-width: 100%; }

    @media print {
        #sidebar, header.app-header, #overlay, .no-print { display: none !important; }
        body, html { overflow: visible !important; background: white !important; height: auto !important; }
        main { margin-left: 0 !important; padding-top: 0 !important; height: auto !important; overflow: visible !important; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

        .watermark { position: fixed !important; top: 50% !important; left: 50% !important; transform: translate(-50%,-50%) !important; z-index: 0; }
        .doc-wrapper { padding: 0 !important; }

        #judgment-doc {
            display: block !important; width: 100% !important; min-height: 0 !important;
            height: auto !important; border: none !important; box-shadow: none !important;
            border-radius: 0 !important; margin: 0 !important;
        }
        .doc-page-multi { min-height: 0 !important; }
        .doc-page-multi .doc-body { flex: none !important; display: block !important; }

        .archive-stamp { display: flex !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

        .doc-footer {
            display: block !important;
            page-break-before: avoid !important; break-before: avoid !important;
            page-break-inside: avoid !important; break-inside: avoid !important;
        }
        .doc-body > div:last-child, #judgment-doc [style*="margin-top:48px"] {
            page-break-after: avoid !important; break-after: avoid !important;
        }

        #judgment-doc .doc-body, #judgment-doc .doc-body p,
        #judgment-doc .doc-body div { text-align: justify; }
        #judgment-doc .doc-title, #judgment-doc .lid-separator,
        #judgment-doc .sig-name, #judgment-doc .sig-position { text-align: center !important; }

        #judgment-doc ol { list-style-type: decimal; padding-left: 1.6em; margin: 0.4em 0; }
        #judgment-doc ul { list-style-type: disc;    padding-left: 1.6em; margin: 0.4em 0; }
        #judgment-doc li { margin: 0.25em 0; }
        #judgment-doc .ql-indent-1 { padding-left: 3em; }
        #judgment-doc .ql-indent-2 { padding-left: 4.5em; }
        #judgment-doc .ql-indent-3 { padding-left: 6em; }

        @page {
            size: A4;
            margin: 12mm 15mm 18mm 15mm;
            @bottom-center {
                content: "— " counter(page) " / " counter(pages) " —";
                font-family: 'Times New Roman', Times, serif;
                font-size: 8.5pt; color: #6b7280; letter-spacing: .08em;
                border-top: 1px solid #d1d5db; padding-top: 4pt;
                width: 100%; text-align: center;
            }
        }
    }
</style>
@endpush

@section('admin_main_content')

@php
    $chair ??= null;
    $clerk ??= null;
    $courtName       = $court->longName    ?? 'Maxkamadda';
    $courtArabic     = $court->arabic_name ?? 'محكمة';
    $courtLogo       = asset('images/logo.png');
    $courtStamp      = $court->stamp      ? asset('storage/' . $court->stamp)      : null;
    $courtLetterhead = $court->letterhead ? asset('storage/' . $court->letterhead) : null;
    $courtAddress    = ucwords(strtolower($court->address ?? ''));
    $courtEmail      = $court->email     ?? null;
    $courtWebsite    = strtoupper($court->website ?? '');
    $judgDate        = $judgment->judgment_date
                            ? \Carbon\Carbon::parse($judgment->judgment_date)->format('d/m/Y')
                            : \Carbon\Carbon::parse($judgment->created_at)->format('d/m/Y');
    $ref             = $case->FileNo;

    $allParties = $case->parties->sortBy('PID')->values();
    $plaintiffs = $case->parties->where('party_role', 'Dacwoode')->values();
    $defendants = $case->parties->where('party_role', 'Dacwaysane')->values();
    if ($plaintiffs->isEmpty() && $defendants->isEmpty()) {
        $plaintiffs = $allParties->take(1);
        $defendants = $allParties->skip(1)->values();
    } elseif ($plaintiffs->isEmpty()) {
        $plaintiffs = $defendants->take(1);
        $defendants = $defendants->skip(1)->values();
    }

    $panelMembers = $case->assignments
                        ->whereNotIn('panel_role', ['Chair', 'Guddoomiye', 'Clerk', 'Kaaliye'])
                        ->filter(fn($a) => $a->id !== ($chair?->id));

    $legalByPid    = $case->legalRepresentatives->keyBy('party_id');
    $legalByRole   = $case->legalRepresentatives->keyBy('party_role');
    $lawyersByPid  = $case->lawyers->groupBy('party_id');
    $lawyersByRole = $case->lawyers->groupBy('party_role');
    $getPartyLawyers = function ($party) use ($lawyersByPid, $lawyersByRole) {
        $byId   = $party->PID ? ($lawyersByPid->get($party->PID) ?? collect()) : collect();
        $byRole = $lawyersByRole->get($party->party_role) ?? collect();
        return $byId->count() ? $byId : $byRole;
    };
@endphp

{{-- ═══ TOOLBAR ═══ --}}
<div class="no-print doc-toolbar">
    <div class="doc-toolbar-left">
        <div class="doc-toolbar-icon"><i class="bi bi-gavel"></i></div>
        <div>
            <h1 class="doc-toolbar-title">Dukuumintiga Xukunku</h1>
            <p class="doc-toolbar-sub">
                {{ $ref }} &nbsp;·&nbsp; {{ $judgment->judgment_type ?? 'Xukun' }}
            </p>
        </div>
    </div>
    <div class="doc-toolbar-actions">

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

        {{-- Archive stamp request/approval isn't built for Judgment yet (no
             signing mechanism backs it, unlike Close Case's modal) — $isStampApproved
             and $isStampRequested are always false here, so this block is
             omitted rather than showing a button that goes nowhere. --}}

        <button onclick="window.print()" id="download-btn" class="btn-download">
            <i class="bi bi-file-earmark-arrow-down"></i> Download PDF
        </button>

        <a href="{{ url()->previous() }}" class="btn-doc-back">
            <i class="bi bi-arrow-left"></i> Ka Noqo
        </a>
    </div>
</div>

{{-- ═══ A4 DOCUMENT ═══ --}}
<div class="doc-wrapper" id="doc-wrapper">
<div id="judgment-doc" class="doc-page-multi">

    {{-- LETTERHEAD --}}
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

    <div class="watermark"><img src="{{ $courtLogo }}" alt=""></div>

    <div class="doc-body">

        <p style="text-align:center;font-weight:700;font-size:13px;margin-bottom:12px;letter-spacing:.04em">QAYBTA CIQAABTA</p>

        <div class="ref-row">
            <span><strong>{{ $ref }}</strong></span>
            <span><strong>{{ $judgDate }}</strong></span>
        </div>

        <p class="doc-title">XUKUNKA DACWADA CIQAABTA</p>

        {{-- Court panel --}}
        <p class="doc-para">
            Maanta oo ay taariikhdu tahay <strong>{{ $judgDate }}</strong>
            {{ $courtName }} oo ka kooban:-
        </p>

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

        <p class="doc-para doc-para-mt">
            Waxay u fadhiisatay dhageysiga dacwad ciqaab ah ee summadeedu tahay:
            <strong>{{ $ref }},</strong>
            dacwaddaas oo ka dhaxeysa dhinacyada kala ah:-
        </p>

        {{-- Plaintiffs --}}
        @php $partyNum = 1; @endphp
        @foreach($plaintiffs as $p)
            @php
                $rep          = $p->PID ? ($legalByPid->get($p->PID) ?? $legalByRole->get($p->party_role)) : $legalByRole->get($p->party_role);
                $partyLawyers = $getPartyLawyers($p);
                $lawyerNames  = $partyLawyers->map(fn($l) => $l->lawyer?->LawyerName)->filter()->unique()->implode(' iyo ');
            @endphp
            <div style="margin:10px 20px 2px;">
                <div style="display:flex;align-items:baseline;">
                    <span style="flex-shrink:0;font-weight:700;min-width:28px;">{{ $partyNum++ }}.</span>
                    <span style="flex-shrink:0;font-weight:700;margin-left:4px;">{{ $p->full_name }}</span>
                    <span style="flex:1;border-bottom:1px dotted #222;margin:0 10px 3px;"></span>
                    <span style="flex-shrink:0;font-weight:700;">{{ $p->party_role }}</span>
                </div>
                @if($rep)
                    <div style="margin-left:32px;font-size:12.5px;margin-top:3px;"><strong>Wakiil:</strong> {{ $rep->rep_name }}.</div>
                @endif
                @if($lawyerNames)
                    <div style="margin-left:32px;font-size:12.5px;margin-top:3px;"><strong>Qareeno:</strong> {{ $lawyerNames }}.</div>
                @endif
            </div>
        @endforeach

        @if($plaintiffs->count() > 0 && $defendants->count() > 0)
            <p class="lid-separator">L &nbsp;&nbsp; I &nbsp;&nbsp; D</p>
        @endif

        {{-- Defendants --}}
        @foreach($defendants as $d)
            @php
                $rep          = $d->PID ? ($legalByPid->get($d->PID) ?? $legalByRole->get($d->party_role)) : $legalByRole->get($d->party_role);
                $partyLawyers = $getPartyLawyers($d);
                $lawyerNames  = $partyLawyers->map(fn($l) => $l->lawyer?->LawyerName)->filter()->unique()->implode(' iyo ');
            @endphp
            <div style="margin:10px 20px 2px;">
                <div style="display:flex;align-items:baseline;">
                    <span style="flex-shrink:0;font-weight:700;min-width:28px;">{{ $partyNum++ }}.</span>
                    <span style="flex-shrink:0;font-weight:700;margin-left:4px;">{{ $d->full_name }}</span>
                    <span style="flex:1;border-bottom:1px dotted #222;margin:0 10px 3px;"></span>
                    <span style="flex-shrink:0;font-weight:700;">{{ $d->party_role }}</span>
                </div>
                @if($rep)
                    <div style="margin-left:32px;font-size:12.5px;margin-top:3px;"><strong>Wakiil:</strong> {{ $rep->rep_name }}.</div>
                @endif
                @if($lawyerNames)
                    <div style="margin-left:32px;font-size:12.5px;margin-top:3px;"><strong>Qareeno:</strong> {{ $lawyerNames }}.</div>
                @endif
            </div>
        @endforeach

        {{-- Body content --}}
        @if(!empty($wordBodyHtml))
            <div class="word-body" style="margin-top:18px;line-height:1.7;font-size:13px;">{!! $wordBodyHtml !!}</div>
        @else
            @if($judgment->verdict)
                <p class="section-title" style="text-align:center;margin-top:20px">XUKUNKA MAXKAMADDA</p>
                <div style="margin-top:16px;line-height:1.7;font-size:13px;text-align:justify;">{!! $judgment->verdict !!}</div>
            @endif
        @endif

        {{-- ── Judge / Clerk Signatures ── --}}
        <div style="margin-top:48px;display:flex;align-items:flex-end;justify-content:space-between;page-break-inside:avoid;break-inside:avoid;">

            {{-- Judge --}}
            <div style="text-align:center;flex:1;">
                @if($judgeSig?->signer?->signature)
                    <img src="{{ asset($judgeSig->signer->signature) }}"
                         style="height:52px;max-width:160px;object-fit:contain;display:block;margin:0 auto .3rem;">
                @else
                    <div style="height:52px;"></div>
                @endif
                <p class="sig-name">{{ $chair?->employee?->EmpName ?? $judgeSig?->signer?->EmpName ?? '____________________________' }}</p>
                <p class="sig-position">{{ $chair?->employee?->Position ?? 'Garsooraha' }}</p>
            </div>

            {{-- Centre: Court Stamp (when approved) + QR --}}
            <div style="flex:0 0 120px;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;gap:8px;">
                @if($isStampApproved && $courtStamp)
                    <img src="{{ $courtStamp }}" alt="Court Stamp" class="stamp-img"
                         style="width:90px;height:90px;object-fit:contain;">
                @endif
                <div id="judgment-qr" style="position:static;transform:none;top:auto;right:auto;width:80px;height:80px;line-height:0;"></div>
            </div>

            {{-- Clerk --}}
            <div style="text-align:center;flex:1;">
                @if($clerkSig?->signer?->signature)
                    <img src="{{ asset($clerkSig->signer->signature) }}"
                         style="height:52px;max-width:160px;object-fit:contain;display:block;margin:0 auto .3rem;">
                @else
                    <div style="height:52px;"></div>
                @endif
                <p class="sig-name">{{ $clerk?->employee?->EmpName ?? $clerkSig?->signer?->EmpName ?? $judgment->created_by ?? '____________________________' }}</p>
                <p class="sig-position">{{ $clerk?->employee?->Position ?? 'Kaaliyaha' }}</p>
            </div>

        </div>

        {{-- ── Party Signatures (after judge/clerk) ── --}}
        @php
            $sigParties = $allParties->filter(fn($p) => $p->PID && $judgment->receipts->firstWhere('party_id', $p->PID));
        @endphp
        @if($sigParties->isNotEmpty())
            <div style="margin-top:36px;padding-top:24px;border-top:1px solid #e5e7eb;page-break-inside:avoid;break-inside:avoid;">
                <p style="text-align:center;font-size:11px;font-weight:700;letter-spacing:.1em;color:#6b7280;margin-bottom:20px;text-transform:uppercase;">Saxiixyada Dhinacyada</p>
                <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:40px;">
                    @foreach($sigParties as $sp)
                        @php $spReceipt = $judgment->receipts->firstWhere('party_id', $sp->PID); @endphp
                        <div style="text-align:center;min-width:130px;">
                            @if($spReceipt->signature)
                                <img src="{{ asset('storage/' . $spReceipt->signature) }}"
                                     style="height:52px;max-width:150px;object-fit:contain;display:block;margin:0 auto;border-bottom:1px solid #333;padding-bottom:2px;">
                            @else
                                <div style="height:52px;border-bottom:1px solid #333;"></div>
                            @endif
                            <p style="font-size:11.5px;font-weight:700;margin:4px 0 1px;">{{ $sp->full_name }}</p>
                            <p style="font-size:10.5px;color:#555;margin:0;">{{ $sp->party_role }}</p>
                            @if($spReceipt->rep_signature)
                                <div style="margin-top:10px;">
                                    <img src="{{ asset('storage/' . $spReceipt->rep_signature) }}"
                                         style="height:40px;max-width:130px;object-fit:contain;display:block;margin:0 auto;border-bottom:1px solid #999;padding-bottom:2px;">
                                    <p style="font-size:10.5px;font-weight:600;margin:3px 0 0;color:#374151;">{{ $spReceipt->rep_name }}</p>
                                    <p style="font-size:10px;color:#777;margin:0;">Wakiilka</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>{{-- /doc-body --}}

</div>{{-- /judgment-doc --}}
</div>

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
                <p style="font-size:.78rem;color:#6b7280;margin:0">{{ $ref }} · {{ $judgment->judgment_type ?? 'Xukun' }}</p>
            </div>
            <button onclick="closeSignModal()"
                    style="margin-left:auto;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div style="margin-bottom:1rem">
            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">Magacaaga</label>
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
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function () {
    function initQR() {
        var el = document.getElementById('judgment-qr');
        if (!el) return;
        if (typeof QRCode === 'undefined') { setTimeout(initQR, 150); return; }
        new QRCode(el, {
            text: '{{ $case->FileNo }}',
            width: 80, height: 80,
            colorDark: '#0A284D', colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initQR); } else { initQR(); }
})();

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
    fetch('{{ route("document.sign", ["type" => "judgment", "id" => $judgment->id]) }}', {
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
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeSignModal(); } });
</script>

@endsection
