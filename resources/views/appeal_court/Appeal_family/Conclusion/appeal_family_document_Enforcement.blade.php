@extends('layouts.document_print')
@section('page_title', 'Dhaqan Galka — Dukuumintiga')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-document.css') }}">
@endpush

@section('admin_main_content')

    @php
        $courtName       = $court->longName    ?? 'Maxkamadda';
        $courtArabic     = $court->arabic_name ?? 'محكمة';
        $courtLogo       = asset('images/logo.png');
        $courtLetterhead = $court->letterhead ? asset('storage/' . $court->letterhead) : null;
        $courtAddress    = ucwords(strtolower($court->address ?? ''));
        $courtEmail      = $court->email ?? null;
        $courtWebsite    = strtoupper($court->website ?? '');
        $docDate         = $enforcement->enforcement_date
            ? \Carbon\Carbon::parse($enforcement->enforcement_date)->format('d/m/Y')
            : \Carbon\Carbon::parse($enforcement->created_at)->format('d/m/Y');
    @endphp

    {{-- Toolbar --}}
    <div class="no-print doc-toolbar">
        <div class="doc-toolbar-left">
            <div class="doc-toolbar-icon">
                <i class="bi bi-hammer"></i>
            </div>
            <div>
                <h1 class="doc-toolbar-title">Dhaqan Galka Dacwadda Rafcaanka Qoyska</h1>
                <p class="doc-toolbar-sub"><span>{{ $case->FileNo }}</span></p>
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

    {{-- A4 Document --}}
    <div class="doc-wrapper" id="doc-wrapper">
        <div id="enforcement-doc" class="doc-page-multi">

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
                <p class="doc-title">Ujeedo: Dhaqan Galka Dacwadda Rafcaanka Qoyska</p>

                {{-- Intro --}}
                <p class="doc-para">
                    Maxkamadu waxay bilowday dhaqan galka go'aankii dacwadda fulinta
                    <strong>{{ $case->FileNo }}</strong>
                    ee nooca <strong>{{ $case->CaseType }}</strong>,
                    kaas oo la furay
                    <strong>{{ \Carbon\Carbon::parse($case->OpenDate)->format('d/m/Y') }}</strong>.
                </p>

                {{-- Enforcement summary box --}}
                <div style="border:1.5px solid #d1d5db;border-radius:8px;padding:12px 16px;margin:14px 0;background:#fafafa">
                    <p style="font-size:12px;font-weight:700;color:#374151;margin:0 0 8px;text-transform:uppercase;letter-spacing:.05em">
                        Macluumaadka Dhaqan Galka
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
                            <td style="padding:3px 0;color:#6b7280">Taariikhda Dhaqan Galka:</td>
                            <td style="padding:3px 0;font-weight:700;color:#111827">{{ $docDate }}</td>
                        </tr>
                        @if($enforcement->enforcement_type)
                            <tr>
                                <td style="padding:3px 0;color:#6b7280">Xaaladda Dacwada:</td>
                                <td style="padding:3px 0;font-weight:700;color:#111827">{{ $enforcement->enforcement_type }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td style="padding:3px 0;color:#6b7280">Xaalada:</td>
                            <td style="padding:3px 0;font-weight:700;color:#111827">{{ $enforcement->status }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Additional Notes --}}
                @if($enforcement->additional_notes)
                    <p class="doc-para">
                        <strong>Faallo:</strong> {{ $enforcement->additional_notes }}
                    </p>
                @endif

                {{-- Attachment --}}
                @if($enforcement->attachment)
                    <p class="doc-para">
                        <strong>Dukumintiga Lifaaqan:</strong>
                        <a href="{{ asset('storage/' . $enforcement->attachment) }}" target="_blank">
                            {{ basename($enforcement->attachment) }}
                        </a>
                    </p>
                @endif

                {{-- Created by --}}
                <div style="margin-top:48px;text-align:center;page-break-inside:avoid;break-inside:avoid;">
                    <p class="sig-name">{{ $enforcement->created_by ?? '____________________________' }}</p>
                    <p class="sig-position">Diiwaangeliyaha</p>
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

        </div>{{-- /enforcement-doc --}}
    </div>

@endsection
