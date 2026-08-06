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
                <h1 class="doc-toolbar-title">Soo Celinta Faylka — Dacwadda Ciqaabta</h1>
                <p class="doc-toolbar-sub"><span>{{ $case->FileNo }}</span></p>
            </div>
        </div>
        <div class="doc-toolbar-actions">
            @if($returnFile)
                <a href="{{ route('criminal-return-file.document-pdf', $case->CMID) }}" id="download-btn" class="btn-download" target="_blank">
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
                    <p class="doc-title">Ujeedo: Warqadda Soo Celinta Faylka Dacwadda Ciqaabta</p>

                    {{-- Intro --}}
                    <p class="doc-para">
                        Maxkamadu waxay soo celisay Faylka Dacwadda Ciqaabta
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
                    <div class="sig-row" style="justify-content:center;">

                        {{-- Clerk / Kaaliye, with QR centered underneath --}}
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
                            <div id="return-file-qr" style="width:80px;height:80px;line-height:0;margin:8px auto 0;"></div>
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
    </script>

@endsection
