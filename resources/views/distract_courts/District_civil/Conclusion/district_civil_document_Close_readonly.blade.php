@extends('layouts.document_print')
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
    $courtAddress    = strtoupper($court->address ?? '');
    $courtEmail      = strtoupper($court->email ?? '');
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
            <h1 class="doc-toolbar-title">Dukuumintiga Qaraarka</h1>
            <p class="doc-toolbar-sub">
                {{ $ref }} &nbsp;·&nbsp; {{ $judgment->judgment_type ?? 'Xukun' }}
            </p>
        </div>
    </div>
    <div class="doc-toolbar-actions">
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

        <p style="text-align:center;font-weight:700;font-size:13px;margin-bottom:12px;letter-spacing:.04em">QAYBTA MADANIGA</p>

        <div class="ref-row">
            <span><strong>{{ $ref }}</strong></span>
            <span><strong>{{ $judgDate }}</strong></span>
        </div>

        <p class="doc-title">QARAARKA DACWADA MADANIGA</p>

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
            Waxay u fadhiisatay dhageysiga dacwad madani ah ee summadeedu tahay:
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
            @if($judgment->decision_body)
                <p class="section-title" style="text-align:center;margin-top:20px">QARAARKA MAXKAMADDA</p>
                <div style="margin-top:16px;line-height:1.7;font-size:13px;text-align:justify;">{!! $judgment->decision_body !!}</div>
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

        {{-- Party signatures not applicable for close case documents --}}

    </div>{{-- /doc-body --}}

    {{-- ═══ FOOTER ═══ --}}
    <div class="doc-footer">

        {{-- Gold divider --}}
        <div class="footer-divider"></div>

        {{-- Blue banner: contact info --}}
        <div class="footer-address-banner">
            @if($courtWebsite || $courtEmail)
                @if($courtWebsite){{ $courtWebsite }}@endif
                @if($courtWebsite && $courtEmail) &nbsp;|&nbsp; @endif
                @if($courtEmail){{ $courtEmail }}@endif
                <br>
            @endif
            @if($courtAddress){{ $courtAddress }}@endif
        </div>

    </div>

</div>{{-- /judgment-doc --}}
</div>

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
</script>

@endsection
