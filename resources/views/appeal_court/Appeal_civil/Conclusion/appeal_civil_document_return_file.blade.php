@extends('layouts.document_print')
@section('page_title', 'Soo Celinta Faylka — Dukuumintiga')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-document.css') }}">
@endpush

@section('admin_main_content')

    @php
        $court = $case->court;
        $courtName = $court->longName ?? 'Maxkamadda';
        $courtArabic = $court->arabic_name ?? 'محكمة';
        $courtLogo = asset('images/logo.png');
        $courtStamp = $court->stamp ? asset('storage/' . $court->stamp) : null;
        $courtLetterhead = $court->letterhead ? asset('storage/' . $court->letterhead) : null;
        $courtAddress = ucwords(strtolower($court->address ?? ''));
        $courtEmail = $court->email ?? null;
        $courtPhone = $court->telephone ?? null;
        $courtWebsite = strtoupper($court->website ?? '');
        $docDate = $returnFile
            ? \Carbon\Carbon::parse($returnFile->updated_at)->format('d/m/Y')
            : date('d/m/Y');
    @endphp

    {{-- Toolbar --}}
    <div class="no-print doc-toolbar">
        <div class="doc-toolbar-left">
            <div class="doc-toolbar-icon">
                <i class="bi bi-arrow-return-left"></i>
            </div>
            <div>
                <h1 class="doc-toolbar-title">Soo Celinta Faylka — Dacwadda Madaniga</h1>
                <p class="doc-toolbar-sub"><span>{{ $case->FileNo }}</span></p>
            </div>
        </div>
        <div class="doc-toolbar-actions">
            @if($returnFile)
                @if($myAlreadySigned)
                    <span style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;font-size:.8rem;font-weight:700;color:#10b981;background:rgba(16,185,129,.08);border:1.5px solid rgba(16,185,129,.25);border-radius:8px">
                        <i class="bi bi-patch-check-fill"></i> Saxiixay
                    </span>
                @elseif($canSign)
                    <button onclick="openSignModal()" class="btn-download" style="background:#0A284D">
                        <i class="bi bi-pen"></i> Saxiix Dukuumintiga
                    </button>
                @endif
                <a href="{{ route('appeal-return-file.document-pdf', $case->ACID) }}" id="download-btn" class="btn-download" target="_blank">
                    <i class="bi bi-file-earmark-arrow-down"></i> Download PDF
                </a>
            @endif
            <a href="{{ url()->previous() }}" class="btn-doc-back">
                <i class="bi bi-arrow-left"></i> Ka Noqo
            </a>
        </div>
    </div>

    {{-- No return file state --}}
    @if(!$returnFile)
        <div class="doc-wrapper">
            <div class="doc-empty">
                <div class="doc-empty-icon"><i class="bi bi-file-earmark-x"></i></div>
                <p>Soo celin fayl lama abuuro weli dacwaddan.</p>
                <a href="{{ route('appeal-return-file.create', $case->ACID) }}" class="btn-download">
                    <i class="bi bi-plus-lg"></i> Abuur Soo Celin
                </a>
            </div>
        </div>
    @else

        {{-- A4 Document --}}
        <div class="doc-wrapper" id="doc-wrapper">
            <div id="return-file-doc" class="doc-page-multi">

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

                    {{-- Ref + Date --}}
                    <div class="ref-row">
                        <span><strong>Summad: {{ $case->FileNo }}</strong></span>
                        <span><strong>Tr: {{ $docDate }}</strong></span>
                    </div>

                    {{-- Subject --}}
                    <p class="doc-title">Ujeedo: Warqadda Soo Celinta Faylka Dacwadda Madaniga</p>

                    {{-- Intro --}}
                    <p class="doc-para">
                        Maxkamadu waxay soo celisay Faylka Dacwadda Madaniga
                        <strong>{{ $case->FileNo }}</strong>
                        ee nooca <strong>{{ $case->CaseType }}</strong>,
                        kaas oo la furay
                        <strong>{{ \Carbon\Carbon::parse($case->OpenDate)->format('d/m/Y') }}</strong>.
                        Warqadda soo celinta faylku waxay ku jirtaa dukumeentiyada hoos ku xusan:
                    </p>

                    {{-- Documents List --}}
                    @if(!empty($returnFile->documents))
                        @php
                            $runningPage = 0;
                            $totalPages  = 0;
                            foreach ($returnFile->documents as $d) {
                                $totalPages += max(1, (int) ($d['pages'] ?? 1));
                            }
                        @endphp
                        <table class="doc-list">
                            @foreach($returnFile->documents as $idx => $doc)
                                @php
                                    $cnt   = max(1, (int) ($doc['pages'] ?? 1));
                                    $start = $runningPage + 1;
                                    $end   = $runningPage + $cnt;
                                    $range = $start === $end ? (string) $start : $start . '-' . $end;
                                    $runningPage = $end;
                                @endphp
                                <tr>
                                    <td class="dl-num">{{ $idx + 1 }}.</td>
                                    <td class="dl-name">{{ $doc['name'] ?? '—' }}</td>
                                    <td class="dl-dots"></td>
                                    <td class="dl-pages">{{ $range }}</td>
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
                    @if($returnFile->additional_notes)
                        <p class="doc-para doc-fg">
                            <strong>FG:</strong> {{ $returnFile->additional_notes }}
                        </p>
                    @endif

                    @if($returnFile->special_instructions)
                        <p class="doc-para doc-para-italic">
                            {{ $returnFile->special_instructions }}
                        </p>
                    @endif

                    {{-- Signature --}}
                    <div class="sig-row">

                        {{-- Right: Clerk / Kaaliye --}}
                        <div class="sig-col sig-col-right">
                            @if($clerkSig?->signer?->signature)
                                <img src="{{ asset($clerkSig->signer->signature) }}"
                                     style="height:52px;max-width:160px;object-fit:contain;display:block;margin:0 auto .3rem">
                            @else
                                <div style="height:52px;"></div>
                            @endif
                            <p class="sig-name">
                                @if($clerk?->employee)
                                    {{ $clerk->employee->EmpName }}
                                @else
                                    Xoghayaha Maxkamadda
                                @endif
                            </p>
                            <p class="sig-position">
                                @if($clerk?->employee?->Position)
                                    {{ $clerk->employee->Position }}
                                @else
                                    Kaaliyaha
                                @endif
                            </p>
                        </div>

                        {{-- QR sits beside the signature, same baseline --}}
                        <div style="flex:0 0 90px;display:flex;align-items:flex-end;justify-content:center;">
                            <div id="return-file-qr" style="width:80px;height:80px;line-height:0;"></div>
                        </div>

                    </div>

                </div>{{-- /doc-body --}}

                {{-- ═══ FOOTER ═══ --}}
                <div class="doc-footer">
                    <div class="footer-divider"></div>
                    <div class="footer-address-banner">
                        @if($courtWebsite || $courtEmail)
                            @if($courtWebsite){{ $courtWebsite }}@endif
                            @if($courtWebsite && $courtEmail) &nbsp;|&nbsp; @endif
                            @if($courtEmail){{ $courtEmail }}@endif
                            <br>
                        @endif
                        @if($courtAddress){{ strtoupper($courtAddress) }}@endif
                    </div>
                </div>

            </div>{{-- /return-file-doc --}}
        </div>

    @endif

    {{-- Sign Modal --}}
    @if($returnFile && $canSign && !$myAlreadySigned)
    <div id="sign-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:16px;padding:2rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:1rem">
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem">
                <div style="width:40px;height:40px;background:#0A284D;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-pen" style="color:white;font-size:1.1rem"></i>
                </div>
                <div>
                    <h3 style="font-size:1rem;font-weight:800;color:#111827;margin:0">Saxiix Dukuumintiga</h3>
                    <p style="font-size:.78rem;color:#6b7280;margin:0">{{ $returnFile->id }} · {{ $case->FileNo }}</p>
                </div>
                <button onclick="closeSignModal()" style="margin-left:auto;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;line-height:1">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div style="margin-bottom:1rem">
                <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">Doorka Saxiixa</label>
                <input type="text" readonly value="{{ auth()->user()->name }}"
                       style="width:100%;padding:.65rem .875rem;font-size:.875rem;border:1.5px solid #e5e7eb;border-radius:8px;background:#f9fafb;color:#111827;outline:none;box-sizing:border-box;cursor:default">
                <input type="hidden" id="sign-role" value="{{ $myRole ?? 'clerk' }}">
            </div>
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
                <button onclick="closeSignModal()" style="flex:1;padding:.7rem;font-size:.82rem;font-weight:700;color:#6b7280;background:white;border:1.5px solid #e5e7eb;border-radius:8px;cursor:pointer">Jooji</button>
                <button id="sign-confirm-btn" onclick="submitSign()" style="flex:2;padding:.7rem;font-size:.82rem;font-weight:700;color:white;background:#0A284D;border:none;border-radius:8px;cursor:pointer">
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
                var el = document.getElementById('return-file-qr');
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
            fetch('{{ route("document.sign", ["type" => "return_file", "id" => $returnFile?->id ?? 0]) }}', {
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