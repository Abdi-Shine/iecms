@extends('layouts.document_print')
@section('page_title', $formMeta['label'] . ' Letter — ' . $case->case_number)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-document.css') }}">
@endpush

@section('admin_main_content')

    @php
        $employee = $record->employee;
        $position = $employee->Position ?? 'Xeer Ilaaliye';
        $signedDate = $record->signed_date ? $record->signed_date->format('d/m/Y') : '—';

        $courtName = $court->longName ?? 'Xeer Ilaaliyaha Guud';
        $courtArabic = $court->arabic_name ?? 'النائب العام';
        $courtLogo = asset('images/logo.png');
        $courtStamp = $court->stamp ? asset('storage/' . $court->stamp) : null;
        $courtLetterhead = $court->letterhead ? asset('storage/' . $court->letterhead) : null;
        $courtAddress = ucwords(strtolower($court->address ?? ''));
        $courtEmail = $court->email ?? null;
        $courtWebsite = strtoupper($court->website ?? '');
    @endphp

    {{-- Toolbar (hidden on print) --}}
    <div class="no-print doc-toolbar">
        <div class="doc-toolbar-left">
            <div class="doc-toolbar-icon">
                <i class="bi bi-file-earmark-lock2"></i>
            </div>
            <div>
                <h1 class="doc-toolbar-title">{{ $formMeta['label'] }} Letter</h1>
                <p class="doc-toolbar-sub">
                    <span>{{ $case->case_number }}</span> — {{ $employee->EmpName ?? '—' }}
                </p>
            </div>
        </div>
        <div class="doc-toolbar-actions">
            <a href="{{ route('attorney-cases.compliance.letter-pdf', $record->id) }}" class="btn-download" target="_blank">
                <i class="bi bi-file-earmark-arrow-down"></i> Download PDF
            </a>
            <a href="{{ url()->previous() }}" class="btn-doc-back">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- A4 Document --}}
    <div class="doc-wrapper" id="doc-wrapper">
        <div id="compliance-letter-doc" class="doc-page-multi">

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

            {{-- ═══ DOCUMENT BODY ═══ --}}
            <div class="doc-body">

                @if($type === 'non_disclosure')

                    <p class="doc-title" style="text-align:left;text-decoration:none;font-size:15px;margin-bottom:24px">
                        FOOMKA QARINTA SIRTA IYO XOGTA {{ $case->case_number }}
                    </p>

                    <p class="doc-para" style="margin-bottom:20px">
                        Anigoo: <span
                            style="display:inline-block;min-width:340px;border-bottom:1px solid #111;text-align:center">{{ $employee->EmpName ?? '' }}</span>
                        &nbsp;ah&nbsp;{{ $position }}
                    </p>

                    <p class="section-title" style="text-transform:uppercase;margin-bottom:14px">EEDEYSANAHA/YAASHA:</p>

                    <p class="doc-para" style="margin-bottom:14px">
                        {{ $case->accused->pluck('full_name')->filter()->implode(', ') ?: '—' }}
                    </p>

                    <p class="doc-para">
                        dacwadda qayb-ka-ah sidoo kalana akhriyay Qodobada Hab-dhaqanka Xirfadeed ee Xubnaha
                        Xeer Ilaaliyaha Guud ee Qaranka, Qodobada Sharci Lanbar 11: ee Shaqaalaha Rayidka ah
                        iyo Qodobada Xeerka Ciqaabta ee la xiriira qarinta macluumaadka lagu ogaadaa
                        shaqadaada awgeed, waxaan halkan ka caddaynayaa inaanan la wadaagi doonin dhinac aanu
                        xeerku u fasixin macluumaad kasta oo qarsoodi ah sida ay qabaan xeerarkaa la xusay
                        inta ay dacwaddu socoto ama marka la dhammaystiraa.
                    </p>

                    <p class="doc-para">
                        Waxaan fahamsanahay haddii aan macluumaad la wadaago cid aan xeerku u fasaxin la i
                        anshax-marin karo sida ay qabaan Xeerarka dalka iyo Hab-dhaqanka Xirfadeed ee Xubnaha
                        Xeer Ilaaliyaha Guud ee Qaranka. Waxaan qaadi doonaa tallaabo kasta oo lagama maarmaan
                        u ah ilaalinta, kana hortagi doonaa intii macquul ah inay lunto, baaba'do ama cid aan
                        xaq u lahayn hesho, isticmaasho ama bannaanka timaaddo macluumaad kasta oo qarsoodi
                        ah, anigoo adeegsanaya tab kasta oo si macquul ah loogu dhawrayo macluumaadka.
                    </p>

                    <p class="doc-para">
                        Macluumaad qarsoodi ah waxaa loola jeedaa dhammaan macluumaadka iyo xogaha noocay
                        doonaan ha ahaadeen sida qoraal, email, xog hadal ama taleefoon lagu iskula wadaago,
                        lanbarrada gaarka ah ee {{ $case->case_number }} iyo nuqul kasta oo la xiriira galka
                        ee aan dadweynaha loogu tala galin.
                    </p>

                    <p class="doc-para">
                        Waxaan fahamsanahay inaan macluumaadka qarsoodiga ah ku shaacin karo xaaladaha oo
                        kaliya <strong>ah</strong>:
                    </p>

                    <table style="width:100%;border-collapse:collapse;margin-bottom:10px">
                        <tr>
                            <td style="width:24px;vertical-align:top;padding:2px 0">a.</td>
                            <td style="vertical-align:top;padding:2px 0;text-align:justify">
                                Haddii baahinta macluumaadka ay maxkamadi si sharci ah u amarto.
                            </td>
                        </tr>
                        <tr>
                            <td style="width:24px;vertical-align:top;padding:2px 0">b.</td>
                            <td style="vertical-align:top;padding:2px 0;text-align:justify">
                                Haddii uu qoraal ku amro Xeer Ilaaliyaha Guud ee Qaranka.
                            </td>
                        </tr>
                    </table>

                    <p class="doc-para">
                        Haddii macluumaadka qarsoodiga ah si aan ku talagal ahayn u baxo, waxaan Xeer
                        Ilaaliyaha Guud sida ugu dhaqsiga badan la socodsiinayaa markaan ka warhelo bixidda
                        macluumaadka.
                    </p>

                @else {{-- conflict_of_interest --}}

                    <p class="doc-title" style="text-align:center;text-decoration:none;font-size:15px;margin-bottom:20px">
                        FOOMKA CADDEYNTA DANAHA ISKA HOR IMANAYA
                    </p>

                    <table style="width:100%;border-collapse:collapse;margin-bottom:12px">
                        <tr>
                            <td style="border:1px solid #d4a017;border-radius:4px;padding:8px 10px">
                                <strong>Lambarka Gal-Dacwadeedka:</strong> {{ $case->case_number }}
                            </td>
                        </tr>
                    </table>

                    <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
                        <tr>
                            <td style="border:1px solid #d4a017;border-radius:4px;padding:8px 10px">
                                <strong>DAWLADDA SOOMAALIYA</strong>&nbsp;VS&nbsp;{{ $case->accused->pluck('full_name')->filter()->implode(', ') ?: '—' }}
                            </td>
                        </tr>
                    </table>

                    <p class="doc-para" style="margin-bottom:20px">
                        Anigoo: <span
                            style="display:inline-block;min-width:340px;border-bottom:1px solid #111;text-align:center">{{ $employee->EmpName ?? '' }}</span>
                        &nbsp;ah&nbsp;{{ $position }}
                    </p>

                    <p class="doc-para">
                        Waxaan caddeynayaa inaanay jirin arrin laga yaabo inay xaggayga si macquul ah ugu
                        muuqato inay tahay dano isa ka hor imaanaya oo galka la xiriira. Waxaan sidoo kale
                        soo gudbin doonaa haddii ay soo baxdo arrin uun, isku-dey ama faragelin laga
                        sameeyo gudashada waajibaadkayga galkan oo ay ku jiraan wax u muuqda ama dambe u
                        muuqan kara dano isa ka hor imaanaya oo gal-dacwadeedka la xiriira.
                    </p>

                    <p class="doc-para" style="margin-bottom:8px">Gaar ahaan:</p>

                    <table style="width:100%;border-collapse:collapse;margin-bottom:14px">
                        <tr>
                            <td style="width:24px;vertical-align:top;padding:2px 0">1.</td>
                            <td style="vertical-align:top;padding:2px 0;text-align:justify">Dano maal ama faa'iido aniga qaar ii ah.</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;padding:2px 0">2.</td>
                            <td style="vertical-align:top;padding:2px 0;text-align:justify">Hantida la isku baahdo aniga ama qaraabo dhaw ama saaxiibkay ganacsi ayaa leh.</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;padding:2px 0">3.</td>
                            <td style="vertical-align:top;padding:2px 0;text-align:justify">Waxaa kiiska eedaysane, dhibbane ama wakiil ka ah qaraabadayda dhaw.</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;padding:2px 0">4.</td>
                            <td style="vertical-align:top;padding:2px 0;text-align:justify">Xiriir ayaa si uun dhinacyada nooga dhexeeya.</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;padding:2px 0">5.</td>
                            <td style="vertical-align:top;padding:2px 0;text-align:justify">Dacwaddu waxay ka dhalatay go'aan ama fal aan qaatay.</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;padding:2px 0">6.</td>
                            <td style="vertical-align:top;padding:2px 0;text-align:justify">Waxaa la iigu yeeri karaa in dacwada marag ka furo.</td>
                        </tr>
                    </table>

                    <p class="doc-para">
                        Waxaan sidoo kale ballanqaadayaa inaan shaaciyo sida ugu dhakhsaha badan wax kasta
                        oo u dhigma inuu yahay dano isku-khilaaf ah oo loo arki karo inuu saameyn macquul
                        ah ku yeelan karo go'aanka aan qaadayo, iyo inaanan ka qaybgelin ama istaagin
                        mawqif laga yaabo inuu saameeyo go'aan-qaadashadayda.
                    </p>

                @endif

                @if($record->notes)
                    <p class="doc-para doc-fg">
                        <strong>Faahfaahin Dheeraad ah:</strong> {{ $record->notes }}
                    </p>
                @endif

                {{-- Signature block --}}
                <table style="width:100%;border-collapse:collapse;margin-top:56px">
                    <tr>
                        <td style="width:100%;text-align:center;vertical-align:bottom;padding:0 10px">
                            @if($record->signature && file_exists(public_path($record->signature)))
                                <img src="{{ asset($record->signature) }}"
                                    style="height:60px;max-width:220px;object-fit:contain;display:block;margin:0 auto .3rem">
                            @endif
                            <p class="sig-name">Magaca: {{ $employee->EmpName ?? '—' }}</p>
                            <p class="sig-position">{{ $position }}</p>
                            <p class="sig-position" style="color:#666">Taariikhda Saxeexa: {{ $signedDate }}</p>
                            @if($courtStamp)
                                <img src="{{ $courtStamp }}" alt="Court Stamp" class="stamp-img"
                                    style="display:block;margin:8px auto 0;">
                            @endif
                        </td>
                    </tr>
                </table>

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

        </div>{{-- /compliance-letter-doc --}}
    </div>

@endsection