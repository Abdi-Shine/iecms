@extends('layouts.document_print')
@section('page_title', 'Handover Document')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/court-document.css') }}">
@endpush

@section('admin_main_content')

@php
    $court           = $case->court;
    $courtName       = $court->longName    ?? 'Maxkamadda';
    $courtArabic     = $court->arabic_name ?? 'محكمة';
    $courtLogo       = asset('images/logo.png');
    $courtStamp      = $court->stamp       ? asset('storage/' . $court->stamp)       : null;
    $courtLetterhead = $court->letterhead  ? asset('storage/' . $court->letterhead)  : null;
    $courtAddress    = strtoupper($court->address    ?? '');
    $courtEmail      = strtoupper($court->email      ?? '');
    $courtWebsite    = strtoupper($court->website    ?? '');
    $courtPhone      = $court->telephone ?? null;
    $docDate         = $handover
        ? \Carbon\Carbon::parse($handover->updated_at)->format('d/m/Y')
        : date('d/m/Y');
@endphp

{{-- Toolbar (hidden on print) --}}
<div class="no-print doc-toolbar">
    <div class="doc-toolbar-left">
        <div class="doc-toolbar-icon">
            <i class="bi bi-file-earmark-arrow-up"></i>
        </div>
        <div>
            <h1 class="doc-toolbar-title">Wareejinta Dacwadda Madaniga</h1>
            <p class="doc-toolbar-sub">
            <span>{{ $case->FileNo }}</span>
            </p>
        </div>
    </div>
    <div class="doc-toolbar-actions">
        @if($handover)
            @if($myAlreadySigned)
                <span style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;font-size:.8rem;font-weight:700;color:#10b981;background:rgba(16,185,129,.08);border:1.5px solid rgba(16,185,129,.25);border-radius:8px">
                    <i class="bi bi-patch-check-fill"></i> Saxiixay
                </span>
            @elseif($canSign)
                <button onclick="openSignModal()" class="btn-download" style="background:#0A284D">
                    <i class="bi bi-pen"></i> Saxiix Dukuumintiga
                </button>
            @endif
            <a href="{{ route('family-case-handover.document-pdf', $case->FCID) }}" id="download-btn" class="btn-download" target="_blank">
                <i class="bi bi-file-earmark-arrow-down"></i> Download PDF
            </a>
        @endif
        <a href="{{ url()->previous() }}" class="btn-doc-back">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

{{-- No handover state --}}
@if(!$handover)
<div class="doc-wrapper">
    <div class="doc-empty">
        <div class="doc-empty-icon">
            <i class="bi bi-file-earmark-x"></i>
        </div>
        <p>No handover document found for this case.</p>
        <a href="{{ route('family-case-handover.create', $case->FCID) }}" class="btn-download">
            <i class="bi bi-plus-lg"></i> Create Handover
        </a>
    </div>
</div>
@else

{{-- A4 Document --}}
<div class="doc-wrapper" id="doc-wrapper">
<div id="handover-doc" class="doc-page-multi">

    {{-- ═══ LETTERHEAD HEADER ═══ --}}
    @if($courtLetterhead)
        {{-- Image letterhead: pixel-perfect match to official --}}
        <div class="doc-letterhead-img">
            <img src="{{ $courtLetterhead }}" alt="{{ $courtName }} Letterhead">
        </div>
    @else
        {{-- HTML fallback --}}
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

        {{-- Ref + Date --}}
        <div class="ref-row">
            <span><strong>Summad: {{ $case->FileNo }}</strong></span>
            <span><strong>Tr: {{ $docDate }}</strong></span>
        </div>

        {{-- Subject --}}
        <p class="doc-title">Ujeedo: Tiro-Koobka Kaalinta Dhexe</p>

        {{-- Intro --}}
        <p class="doc-para">
            Xafiiska Kaalinta Dhexe ee Maxkamada waxa uu wareejiyay galka Dacwadda Madaniga
            <strong>{{ $case->FileNo }}</strong>
            ee nooca <strong>{{ $case->CaseType }}</strong>,
            kaas oo la furay
            <strong>{{ \Carbon\Carbon::parse($case->OpenDate)->format('d/m/Y') }}</strong>.
            Warqadda wareejintu waxay ku jirtaa saxiixa hoos ku xusan:
        </p>

        {{-- Documents List --}}
        @if(!empty($handover->documents))
        @php
            $totalPages = 0;
            foreach ($handover->documents as $doc) {
                $pageVal = trim((string)($doc['pages'] ?? 0));
                if (str_contains($pageVal, '-')) {
                    [$start, $end] = array_map('intval', explode('-', $pageVal, 2));
                    $totalPages += max(0, $end - $start + 1);
                } else {
                    $totalPages += max(0, (int)$pageVal);
                }
            }
        @endphp
        <table class="doc-list">
            @php $runningPage = 0; @endphp
            @foreach($handover->documents as $idx => $doc)
                @php
                    $pageVal = trim((string)($doc['pages'] ?? 0));
                    if (str_contains($pageVal, '-')) {
                        [$ps, $pe] = array_map('intval', explode('-', $pageVal, 2));
                        $pageCount = max(0, $pe - $ps + 1);
                    } else {
                        $pageCount = max(0, (int)$pageVal);
                    }
                    $startPage = $runningPage + 1;
                    $endPage   = $runningPage + $pageCount;
                    $runningPage = $endPage;
                    $pageDisplay = ($pageCount <= 1) ? (string)$startPage : "$startPage-$endPage";
                @endphp
                <tr>
                    <td class="dl-num">{{ $idx + 1 }}.</td>
                    <td class="dl-name">{{ $doc['name'] ?? '—' }}</td>
                    <td class="dl-dots"></td>
                    <td class="dl-pages">{{ $pageDisplay }}</td>
                </tr>
            @endforeach
            <tr class="dl-total-row">
                <td></td>
                <td class="dl-name dl-total-label">Wadarta Guud</td>
                <td class="dl-dots"></td>
                <td class="dl-pages">{{ $totalPages }}</td>
            </tr>
        </table>
        @endif

        {{-- Additional Notes --}}
        @if($handover->additional_notes)
        <p class="doc-para doc-fg">
            <strong>FG:</strong> {{ $handover->additional_notes }}
        </p>
        @endif

        @if($handover->special_instructions)
        <p class="doc-para doc-para-italic">
            {{ $handover->special_instructions }}
        </p>
        @endif

        {{-- Signatures + QR — table layout (not flexbox) so this lays out
             left/right reliably both on-screen and in the Dompdf-rendered PDF,
             which has unreliable flexbox support. --}}
        <table style="width:100%;border-collapse:collapse;margin-top:48px">
            <tr>
                {{-- Left: Registrar --}}
                <td style="width:50%;text-align:center;vertical-align:bottom;padding:0 10px">
                    @if($regSig?->signer?->signature && file_exists(public_path($regSig->signer->signature)))
                        <img src="{{ asset($regSig->signer->signature) }}"
                             style="height:52px;max-width:160px;object-fit:contain;display:block;margin:0 auto .3rem">
                    @elseif($regSig)
                        <div style="display:inline-block;padding:3px 10px;border:1.5px solid #10B981;border-radius:20px;background:rgba(16,185,129,.08);margin:0 auto .3rem">
                            <i class="bi bi-patch-check-fill" style="color:#10B981;font-size:11px"></i>
                            <span style="font-size:9px;font-weight:800;color:#059669;letter-spacing:.02em">SAXIIX DIJITAAL AH</span>
                        </div>
                        <div style="font-size:9px;color:#6b7280;margin-bottom:.3rem">{{ \Carbon\Carbon::parse($regSig->signed_at)->format('d/m/Y H:i') }}</div>
                    @endif
                    <p class="sig-name">{{ $handover->created_by ?? auth()->user()->name ?? '—' }}</p>
                    <p class="sig-position">Diiwaangeliyaha</p>
                </td>

                {{-- Right: Kaaliye --}}
                <td style="width:50%;text-align:center;vertical-align:bottom;padding:0 10px">
                    @if($clerkSig?->signer?->signature && file_exists(public_path($clerkSig->signer->signature)))
                        <img src="{{ asset($clerkSig->signer->signature) }}"
                             style="height:52px;max-width:160px;object-fit:contain;display:block;margin:0 auto .3rem">
                    @elseif($clerkSig)
                        <div style="display:inline-block;padding:3px 10px;border:1.5px solid #10B981;border-radius:20px;background:rgba(16,185,129,.08);margin:0 auto .3rem">
                            <i class="bi bi-patch-check-fill" style="color:#10B981;font-size:11px"></i>
                            <span style="font-size:9px;font-weight:800;color:#059669;letter-spacing:.02em">SAXIIX DIJITAAL AH</span>
                        </div>
                        <div style="font-size:9px;color:#6b7280;margin-bottom:.3rem">{{ \Carbon\Carbon::parse($clerkSig->signed_at)->format('d/m/Y H:i') }}</div>
                    @endif
                    <p class="sig-name">{{ $clerk?->employee?->EmpName ?? 'Kaaliyaha Maxkamadda' }}</p>
                    <p class="sig-position">{{ $clerk?->employee?->Position ?? 'Kaaliyaha' }}</p>
                </td>
            </tr>
        </table>

    </div>{{-- /doc-body --}}

    {{-- ═══ FOOTER ═══ --}}
    <div class="doc-footer">

        {{-- QR sits just above the footer, right-aligned — plain block (no flex/transform,
             so it renders reliably both on-screen and in the Dompdf-rendered PDF) --}}
        <div style="text-align:right;padding:8px 24px 4px">
            <img src="{{ $qrDataUri }}" style="width:80px;height:80px;display:inline-block">
        </div>

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

</div>{{-- /handover-doc --}}
</div>

@endif

{{-- Sign Modal --}}
@if($handover && $canSign && !$myAlreadySigned)
<div id="sign-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center">
    <div style="background:white;border-radius:16px;padding:2rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:1rem">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem">
            <div style="width:40px;height:40px;background:#0A284D;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-pen" style="color:white;font-size:1.1rem"></i>
            </div>
            <div>
                <h3 style="font-size:1rem;font-weight:800;color:#111827;margin:0">Saxiix Dukuumintiga</h3>
                <p style="font-size:.78rem;color:#6b7280;margin:0">{{ $handover->id }} · {{ $case->FileNo }}</p>
            </div>
            <button onclick="closeSignModal()" style="margin-left:auto;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;line-height:1">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Signer Name (read-only) --}}
        <div style="margin-bottom:1rem">
            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">
                Doorka Saxiixa
            </label>
            <input type="text" readonly value="{{ auth()->user()->name }}"
                   style="width:100%;padding:.65rem .875rem;font-size:.875rem;border:1.5px solid #e5e7eb;border-radius:8px;background:#f9fafb;color:#111827;outline:none;box-sizing:border-box;cursor:default">
            <input type="hidden" id="sign-role" value="{{ $myRole ?? 'signer' }}">
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

        <p id="sign-error" style="display:none;font-size:.8rem;color:#dc2626;background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.2);border-radius:6px;padding:.5rem .75rem;margin-bottom:.75rem"></p>

        <div style="display:flex;gap:.75rem">
            <button onclick="closeSignModal()" style="flex:1;padding:.7rem;font-size:.82rem;font-weight:700;color:#6b7280;background:white;border:1.5px solid #e5e7eb;border-radius:8px;cursor:pointer">
                Jooji
            </button>
            <button id="sign-confirm-btn" onclick="submitSign()" style="flex:2;padding:.7rem;font-size:.82rem;font-weight:700;color:white;background:#0A284D;border:none;border-radius:8px;cursor:pointer">
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
    var btn  = document.getElementById('sign-confirm-btn');
    var pass = document.getElementById('sign-password').value;
    var role = document.getElementById('sign-role').value;
    var err  = document.getElementById('sign-error');
    if (!pass) { err.textContent = 'Fadlan erayga sirta ah geli.'; err.style.display = 'block'; return; }
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sugaya...';
    fetch('{{ route("document.sign", ["type" => "family_handover", "id" => $handover?->id ?? 0]) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        body: JSON.stringify({ password: pass, role: role })
    })
    .then(function(r) {
        if (!r.ok && r.status !== 422) throw new Error('server');
        return r.json();
    })
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
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeSignModal(); });
</script>

@endsection
