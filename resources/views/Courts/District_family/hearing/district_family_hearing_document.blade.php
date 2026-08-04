@extends('layouts.document_print')
@section('page_title', 'Hearing Document')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-document.css') }}">
    <style>
        /* Footer is now just divider + banner (~70px) — shrink the body clearance */
        #hearing-doc.doc-page-single .doc-body { padding-bottom: 80px !important; overflow: visible !important; }

        /* Restore list styles stripped by Tailwind Preflight */
        #hearing-doc ol { list-style-type: decimal; padding-left: 1.6em; margin: 0.4em 0; }
        #hearing-doc ul { list-style-type: disc;    padding-left: 1.6em; margin: 0.4em 0; }
        #hearing-doc li { margin: 0.25em 0; }
        #hearing-doc .ql-indent-1 { padding-left: 3em; }
        #hearing-doc .ql-indent-2 { padding-left: 4.5em; }
        #hearing-doc .ql-indent-3 { padding-left: 6em; }
        #hearing-doc ol ol, #hearing-doc ol ul { list-style-type: lower-alpha; }
        #hearing-doc ul ul { list-style-type: circle; }

        /* Justify body text */
        #hearing-doc .doc-body,
        #hearing-doc .doc-body p,
        #hearing-doc .doc-body div { text-align: justify; }
        #hearing-doc .doc-title,
        #hearing-doc .sig-name,
        #hearing-doc .sig-position,
        #hearing-doc p[style*="text-align:center"],
        #hearing-doc p[style*="text-align: center"] { text-align: center !important; }

        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            #hearing-doc ol { list-style-type: decimal; padding-left: 1.6em; margin: 0.4em 0; }
            #hearing-doc ul { list-style-type: disc;    padding-left: 1.6em; margin: 0.4em 0; }
            #hearing-doc li { margin: 0.25em 0; }
            #hearing-doc .ql-indent-1 { padding-left: 3em; }
            #hearing-doc .ql-indent-2 { padding-left: 4.5em; }
            #hearing-doc .ql-indent-3 { padding-left: 6em; }
            #hearing-doc ol ol, #hearing-doc ol ul { list-style-type: lower-alpha; }
            #hearing-doc ul ul { list-style-type: circle; }
        }
    </style>
@endpush

@section('admin_main_content')

    @php
        $case = $hearing->familyCase;
        $court = $case->court;
        $plaintiffs = $case->parties->where('party_role', 'Dacwoode');
        $defendants = $case->parties->where('party_role', 'Dacwaysane');
        $hearingDate = $hearing->hearing_date->format('d/m/Y');
        $docDate = now()->format('d/m/Y');
        $ref = $case->FileNo;
        $courtName = $court->longName ?? 'Maxkamadda';
        $courtArabic = $court->arabic_name ?? 'محكمة';
        $courtLogo = asset('images/logo.png');
        $courtStamp = $court->stamp ? asset('storage/' . $court->stamp) : null;
        $courtLetterhead = $court->letterhead ? asset('storage/' . $court->letterhead) : null;
        $courtAddress = strtoupper($court->address ?? '');
        $courtEmail = $court->email ?? null;
        $courtWebsite = strtoupper($court->website ?? '');
    @endphp

    {{-- Toolbar (hidden on print) --}}
    <div class="no-print doc-toolbar">
        <div class="doc-toolbar-left">
            <div class="doc-toolbar-icon">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
                <h1 class="doc-toolbar-title">Mudeeyn Dacwad Qoyska</h1>
                <p class="doc-toolbar-sub">
                    Mudeeyn Dacwad Qoyska Ah · <span>{{ $case->FileNo }}</span>
                </p>
            </div>
        </div>
        <div class="doc-toolbar-actions">
            @if($myAlreadySigned)
                <span
                    style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;font-size:.8rem;font-weight:700;color:#10b981;background:rgba(16,185,129,.08);border:1.5px solid rgba(16,185,129,.25);border-radius:8px">
                    <i class="bi bi-patch-check-fill"></i> Saxiixay
                </span>
            @elseif($canSign)
                <button onclick="openSignModal()" class="btn-download" style="background:#0A284D">
                    <i class="bi bi-pen"></i> Saxiix Dukuumintiga
                </button>
            @endif
            @if($stampApproved)
                <a href="{{ route('family-hearings.approval.stamp', $hearing->id) }}"
                    style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;font-size:.82rem;font-weight:700;color:#065f46;background:rgba(16,185,129,.12);border-radius:.5rem;border:1.5px solid rgba(16,185,129,.3);text-decoration:none;">
                    <i class="bi bi-patch-check-fill"></i> Shaabadda La Ansixiyay
                </a>
            @elseif($stampRequested)
                <a href="{{ route('family-hearings.approval.stamp', $hearing->id) }}"
                    style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;font-size:.82rem;font-weight:700;color:#92600a;background:rgba(240,180,60,.12);border-radius:.5rem;border:1.5px solid rgba(240,180,60,.35);text-decoration:none;">
                    <i class="bi bi-hourglass-split"></i> Codsi Sugaya Ansixinta
                </a>
            @else
                <a href="{{ route('family-hearings.approval.stamp', $hearing->id) }}"
                    style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;font-size:.82rem;font-weight:700;color:#0A284D;background:rgba(10,40,77,.07);border:1.5px solid rgba(10,40,77,.2);border-radius:.5rem;text-decoration:none;">
                    <i class="bi bi-archive"></i> Codsiga Summadda
                </a>
            @endif
            <a href="{{ route('family-hearings.document-pdf', $hearing->id) }}" id="download-btn" class="btn-download" target="_blank">
                <i class="bi bi-file-earmark-arrow-down"></i> Download PDF
            </a>
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

                <p style="text-align:center;font-weight:700;font-size:13px;margin-bottom:12px;letter-spacing:.04em">QAYBTA
                    QOYSKA</p>

                {{-- Ref + Date --}}
                <div class="ref-row">
                    <span><strong>{{ $ref }}</strong></span>
                    <span><strong>{{ $docDate }}</strong></span>
                </div>

                {{-- Title --}}
                <p class="doc-title">UJEEDDO: MUDEYN DACWAD QOYSKA AH.</p>

                {{-- Opening --}}
                <p class="doc-para">
                    Maanta oo ay Taariikhdu tahay <strong>{{ $hearingDate }}</strong>
                    {{ $courtName }} waxey Mudeyneysaa dacwad
                    <strong>{{ $case->CaseType }}</strong>
                    ah oo ka dhaxeysa dhinacyada kala ah:
                </p>

                {{-- Parties with Wakiil + Qareeno per party --}}
                @php
                    $allParties = $case->parties->sortBy('PID')->values();
                    $sideA = $plaintiffs->count() > 0 ? $plaintiffs->values() : $allParties->take(1);
                    $sideB = $defendants->count() > 0 ? $defendants->values() : $allParties->skip(1)->values();
                    $legalByPid = $case->legalRepresentatives->keyBy('party_id');
                    $legalByRole = $case->legalRepresentatives->keyBy('party_role');
                    // Group lawyers: first by party_id, then by party_role as fallback
                    $lawyersByPid = $case->lawyers->groupBy('party_id');
                    $lawyersByRole = $case->lawyers->groupBy('party_role');
                    $getPartyLawyers = function ($party) use ($lawyersByPid, $lawyersByRole) {
                        $byId = $party->PID ? ($lawyersByPid->get($party->PID) ?? collect()) : collect();
                        $byRole = $lawyersByRole->get($party->party_role) ?? collect();
                        return $byId->count() ? $byId : $byRole;
                    };
                @endphp

                <div style="margin:12px 0 8px;">

                    {{-- Side A --}}
                    @foreach($sideA as $i => $p)
                        @php
                            $rep = $p->PID ? ($legalByPid->get($p->PID) ?? $legalByRole->get($p->party_role)) : $legalByRole->get($p->party_role);
                            $partyLawyers = $getPartyLawyers($p);
                            $lawyerNames = $partyLawyers->map(fn($l) => $l->lawyer?->LawyerName)->filter()->unique()->implode(' iyo ');
                        @endphp
                        <div style="margin:10px 20px 2px;">
                            <div style="display:flex;align-items:baseline;">
                                <span style="flex-shrink:0;font-weight:700;min-width:24px;">{{ $i + 1 }}.</span>
                                <span style="flex-shrink:0;font-weight:700;margin-left:6px;">{{ $p->full_name }}</span>
                                <span style="flex:1;border-bottom:1px dotted #222;margin:0 8px 3px;"></span>
                                <span style="flex-shrink:0;font-weight:700;">{{ $p->party_role }}</span>
                            </div>
                            @if($rep)
                                <div style="margin-left:30px;font-size:12.5px;margin-top:3px;">
                                    <strong>Wakiil:</strong> {{ $rep->rep_name }}.
                                </div>
                            @endif
                            @if($lawyerNames)
                                <div style="margin-left:30px;font-size:12.5px;margin-top:3px;">
                                    <strong>Qareeno:</strong> {{ $lawyerNames }}.
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if($sideB->count() > 0)
                        <div style="text-align:center;font-weight:900;font-size:13px;letter-spacing:.2em;padding:8px 0;">LID
                        </div>
                    @endif

                    {{-- Side B --}}
                    @foreach($sideB as $i => $d)
                        @php
                            $rep = $d->PID ? ($legalByPid->get($d->PID) ?? $legalByRole->get($d->party_role)) : $legalByRole->get($d->party_role);
                            $partyLawyers = $getPartyLawyers($d);
                            $lawyerNames = $partyLawyers->map(fn($l) => $l->lawyer?->LawyerName)->filter()->unique()->implode(' iyo ');
                        @endphp
                        <div style="margin:10px 20px 2px;">
                            <div style="display:flex;align-items:baseline;">
                                <span
                                    style="flex-shrink:0;font-weight:700;min-width:24px;">{{ $sideA->count() + $i + 1 }}.</span>
                                <span style="flex-shrink:0;font-weight:700;margin-left:6px;">{{ $d->full_name }}</span>
                                <span style="flex:1;border-bottom:1px dotted #222;margin:0 8px 3px;"></span>
                                <span style="flex-shrink:0;font-weight:700;">{{ $d->party_role }}</span>
                            </div>
                            @if($rep)
                                <div style="margin-left:30px;font-size:12.5px;margin-top:3px;">
                                    <strong>Wakiil:</strong> {{ $rep->rep_name }}.
                                </div>
                            @endif
                            @if($lawyerNames)
                                <div style="margin-left:30px;font-size:12.5px;margin-top:3px;">
                                    <strong>Qareeno:</strong> {{ $lawyerNames }}.
                                </div>
                            @endif
                        </div>
                    @endforeach

                </div>

                {{-- Hearing Notice --}}
                <p class="doc-para doc-para-mt">
                    Haddaba, waxaa la idinka doonayaa in aad ka soo qeyb gashaaan dhegeysiga &amp; doodda dacwadda
                    <strong>Tr: {{ $hearingDate }},</strong>
                    saacadduna tahay
                    <strong>{{ \Carbon\Carbon::parse($hearing->hearing_time)->format('H:i') }} Subaxnimo,</strong>
                    @if($hearing->courtroom) <strong>{{ $hearing->courtroom }}</strong>, @endif
                    dhawrtaanna waqtiga iyo balanta Maxkamadda idin qabatay.
                </p>

                @if($hearing->hearing_purpose)
                    <div class="doc-para"><strong>Ujeeddo gaar ah:</strong> {!! $hearing->hearing_purpose !!}</div>
                @endif

                {{-- ═══ SIGNATURE + STAMP ═══ (plain centered block — Dompdf renders flexbox unreliably) --}}
                <div style="margin-top:48px;text-align:center;page-break-inside:avoid;break-inside:avoid;">
                    @if($clerkSig?->signer?->signature)
                        <img src="{{ asset($clerkSig->signer->signature) }}"
                            style="height:52px;max-width:160px;object-fit:contain;display:block;margin:0 auto">
                    @else
                        <div style="height:52px;"></div>
                    @endif
                    <p class="sig-name" style="line-height:1.2;margin:0;">{{ $clerk?->employee?->EmpName ?? $hearing->created_by ?? 'Kaaliyaha Maxkamadda' }}</p>
                    <p class="sig-position" style="line-height:1.2;margin:0;">{{ $clerk?->employee?->Position ?? 'Kaaliyaha' }}</p>
                    @if($stampApproved && $courtStamp)
                        <img src="{{ $courtStamp }}" alt="Court Stamp" class="stamp-img" style="display:block;margin:8px auto 0;">
                    @endif
                </div>

            </div>{{-- /doc-body --}}

            {{-- ═══ FOOTER ═══ --}}
            <div class="doc-footer">
                <div class="footer-contact-row">
                    <img src="{{ $qrDataUri }}" class="footer-qr" style="width:80px;height:80px">
                </div>
                <div class="footer-divider"></div>
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

        </div>{{-- /hearing-doc --}}
    </div>

    {{-- ═══ SIGN MODAL ═══ --}}
    @if($canSign && !$myAlreadySigned)
        <div id="sign-modal"
            style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center">
            <div
                style="background:white;border-radius:16px;padding:2rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:1rem">
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem">
                    <div
                        style="width:40px;height:40px;background:#0A284D;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="bi bi-pen" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <div>
                        <h3 style="font-size:1rem;font-weight:800;color:#111827;margin:0">Saxiix Dukuumintiga</h3>
                        <p style="font-size:.78rem;color:#6b7280;margin:0">{{ $hearing->id }} · {{ $case->FileNo }}</p>
                    </div>
                    <button onclick="closeSignModal()"
                        style="margin-left:auto;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;line-height:1">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                {{-- Signer Name (read-only) --}}
                <div style="margin-bottom:1rem">
                    <label
                        style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">
                        Doorka Saxiixa
                    </label>
                    <input type="text" readonly value="{{ auth()->user()->name }}"
                        style="width:100%;padding:.65rem .875rem;font-size:.875rem;border:1.5px solid #e5e7eb;border-radius:8px;background:#f9fafb;color:#111827;outline:none;box-sizing:border-box;cursor:default">
                    <input type="hidden" id="sign-role" value="{{ $myRole ?? 'signer' }}">
                </div>

                {{-- Password --}}
                <div style="margin-bottom:1.25rem">
                    <label
                        style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">
                        Xaqiiji Erayga Sirta <span style="color:#ef4444">*</span>
                    </label>
                    <input type="password" id="sign-password" placeholder="Geli eraygaaga sirta ah..."
                        style="width:100%;padding:.65rem .875rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:8px;background:white;color:#111827;outline:none;box-sizing:border-box"
                        onfocus="this.style.borderColor='#0A284D'" onblur="this.style.borderColor='#d1d5db'">
                </div>

                <p id="sign-error"
                    style="display:none;font-size:.8rem;color:#dc2626;background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.2);border-radius:6px;padding:.5rem .75rem;margin-bottom:.75rem">
                </p>

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

    <script>
        function openSignModal() {
            var m = document.getElementById('sign-modal');
            if (m) { m.style.display = 'flex'; document.getElementById('sign-password').focus(); }
        }
        function closeSignModal() {
            var m = document.getElementById('sign-modal');
            if (m) { m.style.display = 'none'; document.getElementById('sign-password').value = ''; document.getElementById('sign-error').style.display = 'none'; }
        }
        function submitSign() {
            var btn = document.getElementById('sign-confirm-btn');
            var pass = document.getElementById('sign-password').value;
            var role = document.getElementById('sign-role').value;
            var err = document.getElementById('sign-error');
            if (!pass) { err.textContent = 'Fadlan erayga sirta ah geli.'; err.style.display = 'block'; return; }
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sugaya...';
            fetch('{{ route("document.sign", ["type" => "family_hearing", "id" => $hearing->id]) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ password: pass, role: role })
            })
                .then(function (r) {
                    if (!r.ok && r.status !== 422) throw new Error('server');
                    return r.json();
                })
                .then(function (data) {
                    if (data.success) {
                        closeSignModal();
                        var toast = document.createElement('div');
                        toast.style.cssText = 'position:fixed;top:24px;right:24px;z-index:99999;background:#0A284D;color:white;padding:14px 22px;border-radius:12px;font-weight:700;font-size:.875rem;box-shadow:0 8px 30px rgba(0,0,0,.2);display:flex;align-items:center;gap:.6rem';
                        toast.innerHTML = '<i class="bi bi-patch-check-fill" style="font-size:1.1rem;color:#10b981"></i> Saxiixa si guul leh ayaa lagu keydsaday!';
                        document.body.appendChild(toast);
                        setTimeout(function () { window.location.reload(); }, 1400);
                    } else {
                        err.textContent = data.message || 'Khalad ayaa dhacay.';
                        err.style.display = 'block';
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-patch-check"></i> Xaqiiji Saxiixa';
                    }
                })
                .catch(function () {
                    err.textContent = 'Xiriirka shabakadda waa xumaaday. Isku day mar kale.';
                    err.style.display = 'block';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-patch-check"></i> Xaqiiji Saxiixa';
                });
        }
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeSignModal(); });
    </script>

@endsection