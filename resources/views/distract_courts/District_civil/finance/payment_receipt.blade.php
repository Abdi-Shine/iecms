@extends('layouts.document_print')
@section('page_title', 'Rasiidka Lacag Bixinta')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/court-document.css') }}">
@endpush

@section('admin_main_content')

@php
    $courtName    = $court->longName ?? 'Maxkamadda';
    $courtLogo    = asset('images/logo.png');
    $receiptNo    = 'RCT-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);

    $serviceLabel = $payment->tariff->name_so ?? 'Tarifad Guud';
    if (!empty($payment->tariff->description)) {
        $serviceLabel .= ' (' . $payment->tariff->description . ')';
    }

    $statusText = [
        'Pending'           => 'Lacagta lama bixin',
        'Awaiting Approval' => 'Lacagta waa la sugayaa ansaxinta',
        'Approved'          => 'Lacagta waa la bixiyay',
        'Failed'            => 'Lacag bixintu way fashilantay',
    ][$payment->status] ?? $payment->status;

    $officerName = $payment->approver->EmpName ?? $payment->cashier->EmpName ?? '—';
@endphp

{{-- ═══ TOOLBAR ═══ --}}
<div class="no-print doc-toolbar">
    <div class="doc-toolbar-left">
        <div class="doc-toolbar-icon"><i class="bi bi-receipt"></i></div>
        <div>
            <h1 class="doc-toolbar-title">Rasiidka Lacag Bixinta</h1>
            <p class="doc-toolbar-sub">#{{ $receiptNo }} &nbsp;·&nbsp; {{ $payment->payer_name }}</p>
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
<div id="receipt-doc" class="doc-page-single">

    <div class="receipt-seal-header">
        <img src="{{ $courtLogo }}" alt="Astaanta Dowladda">
        <div class="receipt-org-line">Dowladda Federaalka Soomaaliya</div>
        <div class="receipt-dept-line">Wasaaradda Maaliyadda</div>
    </div>

    <div class="doc-body" style="padding-top:0">

        <div class="receipt-meta">
            <p class="receipt-doc-title">RAASIDKA LACAG QABASHADA</p>
            <div class="receipt-court-line">{{ $courtName }}</div>
            <div class="receipt-meta-line">Rasiid Lambar: {{ $receiptNo }}</div>
            <div class="receipt-meta-line">Tariikhda Lagac Bixinta: {{ $payment->created_at?->format('d/m/Y H:i:s') }}</div>
        </div>

        <p class="receipt-section-title">MACLUUMAADKA ADEEGA LACAG BIXINTA</p>

        <div class="receipt-fields">
            <div class="receipt-row">
                <span class="receipt-row-label">Magaca Buuxa:</span>
                <span class="receipt-row-value">{{ $payment->payer_name }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Taleefanka:</span>
                <span class="receipt-row-value">{{ $payment->payer_phone }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Imeylka:</span>
                <span class="receipt-row-value">{{ $payment->payer_email }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Maxkamadda:</span>
                <span class="receipt-row-value">{{ $courtName }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Adeega:</span>
                <span class="receipt-row-value">{{ $serviceLabel }}</span>
            </div>

            <div class="receipt-row-gap"></div>

            <div class="receipt-row">
                <span class="receipt-row-label">Cadadka Ajuurada:</span>
                <span class="receipt-row-value">{{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Xaalada:</span>
                <span class="receipt-row-value">{{ $statusText }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Kaaliyaha:</span>
                <span class="receipt-row-value">{{ $officerName }}</span>
            </div>
        </div>

        <p class="receipt-disclaimer">
            Rasiidhkani wuxuu u adeegayaa sidii Caddayn rasmi ah oo Muujinaysa bixinta lacagta adeegyada Maxkamadda.
            Fadlan ku hay Rasiidkan Diiwaankaagaas si Tixraac Mustaqbalka ah.
        </p>

        <div class="receipt-qr-block">
            <img src="{{ $qrDataUri }}" alt="QR Code">
        </div>
        <p class="receipt-qr-caption">Sawir koodkan si aad u xaqiijiso rasiidhka.</p>

    </div>{{-- /doc-body --}}

</div>{{-- /receipt-doc --}}
</div>

@endsection
