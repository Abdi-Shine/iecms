@extends('admin.admin_master')
@section('page_title', 'Codsiga Summadda Archiifka')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-document.css') }}">
@endpush

@section('admin_main_content')

@php
    $court           = $case->court;
    $courtName       = $court->longName    ?? 'Maxkamadda';
    $courtArabic     = $court->arabic_name ?? 'محكمة';
    $courtLogo       = asset('images/logo.png');
    $courtStamp      = $court->stamp      ? asset('storage/' . $court->stamp)      : null;
    $courtLetterhead = $court->letterhead ? asset('storage/' . $court->letterhead) : null;
    $courtAddress    = strtoupper($court->address  ?? '');
    $courtEmail      = $court->email   ?? null;
    $courtWebsite    = strtoupper($court->website  ?? '');
    $docDate         = now()->format('d/m/Y');
    $hearingDate     = $hearing->hearing_date->format('d/m/Y');
    $kaaliyeName     = $clerk?->employee?->EmpName ?? $hearing->created_by ?? 'Kaaliyaha Maxkamadda';
    $kaaliyePos      = $clerk?->employee?->Position ?? 'Kaaliyaha';
    $isComplete      = $clerkSig && $archiveSig;
@endphp

{{-- ═══ TOOLBAR ═══ --}}
<div class="no-print doc-toolbar">
    <div class="doc-toolbar-left">
        <div class="doc-toolbar-icon">
            <i class="bi bi-archive"></i>
        </div>
        <div>
            <h1 class="doc-toolbar-title">Codsiga Summadda Archiifka</h1>
            <p class="doc-toolbar-sub">
                {{ $case->FileNo }} · Dhageysiga: {{ $hearingDate }}
            </p>
        </div>
    </div>
    <div class="doc-toolbar-actions">
        @if(!$myAlreadySigned && $myRole === 'kaaliye' && !$clerkSig)
            <button onclick="openSignModal()"
                style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;font-size:.82rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer">
                <i class="bi bi-send-fill"></i> Codso Summadda
            </button>
        @elseif(!$isComplete && $myRole === 'archive_officer')
            <button onclick="openSignModal()"
                style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;font-size:.82rem;font-weight:700;color:white;background:#10b981;border:none;border-radius:.5rem;cursor:pointer;box-shadow:0 3px 10px rgba(16,185,129,.3)">
                <i class="bi bi-patch-check-fill"></i> La Ogolaaday
            </button>
        @elseif($isComplete)
            <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.45rem 1.1rem;font-size:.82rem;font-weight:700;color:#065f46;background:rgba(16,185,129,.12);border-radius:.5rem">
                <i class="bi bi-patch-check-fill"></i> La Ogolaaday
            </span>
        @endif
        <button onclick="downloadPDF()" id="download-btn" class="btn-download">
            <i class="bi bi-file-earmark-arrow-down"></i> Download PDF
        </button>
        <a href="{{ url()->previous() }}" class="btn-doc-back">
            <i class="bi bi-arrow-left"></i> Ka Noqo
        </a>
    </div>
</div>

{{-- ═══ A4 DOCUMENT ═══ --}}
<div class="doc-wrapper" id="doc-wrapper">
<div id="approval-doc" class="doc-page-single">

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

    {{-- WATERMARK --}}
    <div class="watermark">
        <img src="{{ $courtLogo }}" alt="">
    </div>

    {{-- ═══ DOCUMENT BODY ═══ --}}
    <div class="doc-body">

        <p style="text-align:center;font-weight:700;font-size:13px;margin-bottom:12px;letter-spacing:.04em">QAYBTA ARCHIIFKA</p>

        {{-- Ref + Date --}}
        <div class="ref-row">
            <span><strong>{{ $case->FileNo }}</strong></span>
            <span><strong>{{ $docDate }}</strong></span>
        </div>

        {{-- Title --}}
        <p class="doc-title">UJEEDDO: CODSIGA SUMMADDA ARCHIIFKA.</p>

        {{-- Opening paragraph --}}
        <p class="doc-para">
            Kaaliyaha Maxkamadda <strong>{{ $courtName }}</strong> wuxuu codsaday in
            Dukuumintiga Mudaynta Dacwadda Madaniga nooca
            <strong>{{ $case->CaseType }}</strong>
            (Summad: <strong>{{ $case->FileNo }}</strong>),
            oo taariikhda dhageysiga ahayd <strong>{{ $hearingDate }}</strong>,
            lagu daro summadda rasmiga ah ee Maxkamadda si loo xaqiijiyo
            rasmiyaantiisa iyo in uu archiifku helo.
        </p>

        {{-- Hearing summary box --}}
        <div style="border:1.5px solid #d1d5db;border-radius:8px;padding:12px 16px;margin:14px 0;background:#fafafa">
            <p style="font-size:12px;font-weight:700;color:#374151;margin:0 0 8px;text-transform:uppercase;letter-spacing:.05em">
                Macluumaadka Dhageysiga
            </p>
            <table style="width:100%;font-size:12.5px;border-collapse:collapse">
                <tr>
                    <td style="padding:3px 0;color:#6b7280;width:42%">Summada Dacwadda:</td>
                    <td style="padding:3px 0;font-weight:700;color:#111827">{{ $case->FileNo }}</td>
                </tr>
                <tr>
                    <td style="padding:3px 0;color:#6b7280">Nooca Dacwadda:</td>
                    <td style="padding:3px 0;font-weight:700;color:#111827">{{ $case->CaseType }}</td>
                </tr>
                <tr>
                    <td style="padding:3px 0;color:#6b7280">Taariikhda Dhageysiga:</td>
                    <td style="padding:3px 0;font-weight:700;color:#111827">{{ $hearingDate }}</td>
                </tr>
                <tr>
                    <td style="padding:3px 0;color:#6b7280">Saacadda:</td>
                    <td style="padding:3px 0;font-weight:700;color:#111827">
                        {{ \Carbon\Carbon::parse($hearing->hearing_time)->format('H:i') }} Subaxnimo
                    </td>
                </tr>
                @if($hearing->courtroom)
                <tr>
                    <td style="padding:3px 0;color:#6b7280">Qolka Dacwadda:</td>
                    <td style="padding:3px 0;font-weight:700;color:#111827">{{ $hearing->courtroom }}</td>
                </tr>
                @endif
                @if($hearing->hearing_purpose)
                <tr>
                    <td style="padding:3px 0;color:#6b7280">Ujeedada:</td>
                    <td style="padding:3px 0;font-weight:700;color:#111827">{{ $hearing->hearing_purpose }}</td>
                </tr>
                @endif
            </table>
        </div>

        {{-- Parties summary --}}
        @php
            $plaintiffs = $case->parties->where('party_role', 'Dacwoode');
            $defendants = $case->parties->where('party_role', 'Dacwaysane');
        @endphp
        <p class="doc-para">
            Dacwaddu waxay ka dhaxeysa
            <strong>{{ $plaintiffs->map(fn($p) => $p->full_name)->implode(', ') ?: '—' }}</strong>
            (Dacwoode)
            iyo
            <strong>{{ $defendants->map(fn($p) => $p->full_name)->implode(', ') ?: '—' }}</strong>
            (Dacwaysane).
        </p>

        {{-- Closing --}}
        <p class="doc-para">
            Haddaba, waxaa laga codsanayaa Deeqa Maamulka (Archive Officer) in ay
            baaraan-degto codsigan kooban una saxiixdo si oggolanaheedu rasmiga uga
            dhigo summadda loo baahan yahay.
        </p>

        {{-- Status banner --}}
        @if($isComplete)
            <div style="display:flex;align-items:center;gap:.5rem;background:rgba(16,185,129,.07);border:1.5px solid rgba(16,185,129,.3);border-radius:8px;padding:8px 14px;margin:14px 0">
                <i class="bi bi-patch-check-fill" style="color:#10b981;font-size:1rem;flex-shrink:0"></i>
                <span style="font-size:12px;font-weight:700;color:#065f46">
                    Codsigan si rasmi ah ayaa loo ogolaaday —
                    Taariikhda: {{ \Carbon\Carbon::parse($archiveSig->signed_at)->format('d/m/Y H:i') }}
                </span>
            </div>
        @elseif($clerkSig)
            <div style="display:flex;align-items:center;gap:.5rem;background:rgba(245,158,11,.07);border:1.5px solid rgba(245,158,11,.3);border-radius:8px;padding:8px 14px;margin:14px 0">
                <i class="bi bi-hourglass-split" style="color:#d97706;font-size:1rem;flex-shrink:0"></i>
                <span style="font-size:12px;font-weight:700;color:#92400e">
                    Kaaliyuhu wuu saxiixay — Sugaya oggolaanshaha Deeqa Maamulka
                </span>
            </div>
        @endif

        {{-- ═══ SIGNATURES ═══ --}}
        <div class="sig-row" style="margin-top:44px;display:flex;align-items:flex-end;justify-content:space-between">

            {{-- Left: Kaaliye (requester) --}}
            <div class="sig-col" style="text-align:center;flex:1">
                <p class="sig-name">{{ $kaaliyeName }}</p>
                <p class="sig-position">{{ $kaaliyePos }}</p>
                @if($clerkSig?->signer?->signature)
                    <img src="{{ asset($clerkSig->signer->signature) }}"
                         style="height:52px;max-width:160px;object-fit:contain;display:block;margin:.3rem auto 0">
                @else
                    <div style="height:52px"></div>
                @endif
                @if($clerkSig)
                    <p style="font-size:10px;color:#6b7280;margin-top:3px">
                        <i class="bi bi-check-circle-fill" style="color:#10b981"></i>
                        {{ \Carbon\Carbon::parse($clerkSig->signed_at)->format('d/m/Y H:i') }}
                    </p>
                @elseif($myRole === 'kaaliye')
                    <button onclick="openSignModal()" class="no-print"
                        style="margin-top:8px;display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;font-size:.78rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:8px;cursor:pointer;box-shadow:0 3px 10px rgba(82,140,190,.3)">
                        <i class="bi bi-send-fill"></i> Codso Summadda
                    </button>
                @endif
            </div>

            {{-- Center: Court stamp — only after full approval --}}
            <div class="sig-stamp" style="flex:0 0 auto;display:flex;align-items:center;justify-content:center">
                @if($isComplete && $courtStamp)
                    <img src="{{ $courtStamp }}" alt="Court Stamp" class="stamp-img">
                @endif
            </div>

            {{-- Right: Archive Officer (approver) --}}
            <div class="sig-col sig-col-right" style="text-align:center;flex:1">
                <p class="sig-name">{{ $archiveSig?->signer?->EmpName ?? 'Deeqa Maamulka' }}</p>
                <p class="sig-position">Deeqa Maamulka / Archive Officer</p>
                @if($archiveSig?->signer?->signature)
                    <img src="{{ asset($archiveSig->signer->signature) }}"
                         style="height:52px;max-width:160px;object-fit:contain;display:block;margin:.3rem auto 0">
                @else
                    <div style="height:52px"></div>
                @endif
                @if($archiveSig)
                    <p style="font-size:10px;color:#6b7280;margin-top:3px">
                        <i class="bi bi-check-circle-fill" style="color:#10b981"></i>
                        {{ \Carbon\Carbon::parse($archiveSig->signed_at)->format('d/m/Y H:i') }}
                    </p>
                @elseif(!$isComplete && $myRole === 'archive_officer')
                    <button onclick="openSignModal()" class="no-print"
                        style="margin-top:8px;display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;font-size:.78rem;font-weight:700;color:white;background:#10b981;border:none;border-radius:8px;cursor:pointer;box-shadow:0 3px 10px rgba(16,185,129,.3)">
                        <i class="bi bi-patch-check-fill"></i> La Ogolaaday
                    </button>
                @endif
            </div>

        </div>

    </div>{{-- /doc-body --}}

    {{-- ═══ FOOTER ═══ --}}
    <div class="doc-footer">
        <div class="footer-contact-row">
            <div id="approval-qr" class="footer-qr"></div>
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

</div>{{-- /approval-doc --}}
</div>

{{-- ═══ SIGN MODAL ═══ --}}
@if(!$myAlreadySigned && $myRole)
<div id="sign-modal"
     style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center">
    <div style="background:white;border-radius:16px;padding:2rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:1rem">

        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem">
            <div style="width:40px;height:40px;background:#0A284D;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-pen" style="color:white;font-size:1.1rem"></i>
            </div>
            <div>
                <h3 style="font-size:1rem;font-weight:800;color:#111827;margin:0">
                    @if($myRole === 'kaaliye') Codso Summadda @else Ogolow Codsigu @endif
                </h3>
                <p style="font-size:.78rem;color:#6b7280;margin:0">{{ $case->FileNo }} · {{ $hearingDate }}</p>
            </div>
            <button onclick="closeSignModal()"
                    style="margin-left:auto;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;line-height:1">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Signer name (read-only) --}}
        <div style="margin-bottom:1rem">
            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">
                Doorka Saxiixa
            </label>
            <input type="text" readonly value="{{ auth()->user()->name }}"
                   style="width:100%;padding:.65rem .875rem;font-size:.875rem;border:1.5px solid #e5e7eb;border-radius:8px;background:#f9fafb;color:#111827;outline:none;box-sizing:border-box;cursor:default">
            <input type="hidden" id="sign-role" value="{{ $myRole }}">
        </div>

        {{-- Password --}}
        <div style="margin-bottom:1.25rem">
            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">
                Xaqiiji Erayga Sirta <span style="color:#ef4444">*</span>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
(function () {
    function initQR() {
        var el = document.getElementById('approval-qr');
        if (!el) return;
        if (typeof QRCode === 'undefined') { setTimeout(initQR, 150); return; }
        new QRCode(el, {
            text: '{{ $case->FileNo }}',
            width: 80,
            height: 80,
            colorDark: '#0A284D',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQR);
    } else {
        initQR();
    }
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
    fetch('{{ route("document.sign", ["type" => "family_hearing_stamp", "id" => $hearing->id]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
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

function downloadPDF() {
    var btn = document.getElementById('download-btn');
    var el  = document.getElementById('approval-doc');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generating...';
    el.style.minHeight = '0';
    html2pdf()
        .set({
            margin: 0,
            filename: 'ApprovalStamp-{{ addslashes($case->FileNo) }}-{{ $docDate }}.pdf',
            image:       { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                logging: false,
                width: 794,
                height: 1123,
                scrollX: 0,
                scrollY: 0
            },
            jsPDF: { unit: 'px', format: [794, 1123], orientation: 'portrait' }
        })
        .from(el)
        .save()
        .then(function () {
            el.style.minHeight = '';
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-file-earmark-arrow-down"></i> Download PDF';
        });
}
</script>

@endsection
