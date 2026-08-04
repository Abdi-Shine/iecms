<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ogeysiis Diiwaangelinta Dacwadda</title>
</head>
<body style="margin:0;padding:0;background:#f3f6fb;font-family:'Segoe UI',Arial,sans-serif">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f6fb;padding:40px 0">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);max-width:600px;width:100%">

                {{-- Header --}}
                <tr>
                    <td style="background:#0A284D;padding:32px 40px;text-align:center">
                        <div style="width:60px;height:60px;background:rgba(255,255,255,0.12);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px">
                            <span style="font-size:28px">⚖️</span>
                        </div>
                        <h1 style="color:#ffffff;font-size:20px;font-weight:700;margin:0 0 6px">{{ $courtName }}</h1>
                        <p style="color:rgba(255,255,255,0.7);font-size:13px;margin:0">Ogeysiis Rasmi ah — Official Court Notice</p>
                    </td>
                </tr>

                {{-- Case Info Bar --}}
                <tr>
                    <td style="background:#528CBE;padding:14px 40px">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="color:white;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em">
                                    Lambarka Dacwadda
                                </td>
                                <td style="color:white;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;text-align:center">
                                    Nooca Dacwadda
                                </td>
                                <td style="color:white;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;text-align:right">
                                    Taariikhda
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#ffffff;font-size:16px;font-weight:800;padding-top:4px">
                                    {{ $fileNo }}
                                </td>
                                <td style="color:#ffffff;font-size:14px;font-weight:700;padding-top:4px;text-align:center">
                                    {{ $caseType }}
                                </td>
                                <td style="color:#ffffff;font-size:14px;font-weight:700;padding-top:4px;text-align:right">
                                    {{ $openDate }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="padding:36px 40px">

                        <p style="font-size:15px;color:#374151;margin:0 0 20px">
                            Mudane / Marwo <strong style="color:#0A284D">{{ $partyName }}</strong>,
                        </p>

                        <p style="font-size:14px;color:#4b5563;line-height:1.8;margin:0 0 16px">
                            Waxaan idiin rajeynayaa caafimaad iyo xaalad wanaagsan.
                        </p>

                        <p style="font-size:14px;color:#4b5563;line-height:1.8;margin:0 0 16px">
                            Waxaa si rasmi ah laguugu wargelinayaa in dacwadda aad soo gudbisay ay si rasmi ah uga diiwaangashan tahay
                            <strong style="color:#0A284D">{{ $courtName }}</strong>.
                            Dacwadda waxaa loo qoondeeyay lambar rasmi ah
                            <strong style="color:#528CBE">{{ $fileNo }}</strong>,
                            waxaana lagu daray nidaamka maxkamadda si loo sii wado habraaca sharciyeed ee ku habboon.
                        </p>

                        <p style="font-size:14px;color:#4b5563;line-height:1.8;margin:0 0 24px">
                            Fadlan ogow in dhammaan tallaabooyinka xiga ee dacwadda, sida u yeerista dhinacyada,
                            dhageysiga iyo go'aannada maxkamadda, lagu fulin doono sida ku cad jadwalka iyo nidaamka maxkamadda.
                        </p>

                        {{-- Notice Box --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px">
                            <tr>
                                <td style="background:rgba(82,140,190,0.08);border-left:4px solid #528CBE;border-radius:0 8px 8px 0;padding:16px 20px">
                                    <p style="font-size:13px;color:#374151;margin:0;line-height:1.7">
                                        Haddii aad u baahan tahay faahfaahin dheeraad ah ama aad qabto su'aalo la xiriira dacwadda,
                                        waxaad la xiriiri kartaa <strong>Xafiiska Diiwaangelinta Maxkamadda</strong>.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:14px;color:#4b5563;line-height:1.8;margin:0 0 4px">
                            Mahadsanid.
                        </p>

                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:24px 40px;text-align:center">
                        <p style="font-size:12px;color:#9ca3af;margin:0 0 4px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em">
                            {{ $courtName }}
                        </p>
                        <p style="font-size:11px;color:#d1d5db;margin:0">
                            Warqaddan waa mid rasmi ah — This is an official court notification
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
