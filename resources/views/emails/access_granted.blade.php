<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koontadaada IECMS</title>
    <style>
        body { margin:0; padding:0; background:#f4f6f9; font-family:Arial,sans-serif; }
        .wrap { max-width:580px; margin:32px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.08); }
        .header { background:#528CBE; padding:32px 36px; }
        .header h1 { color:#fff; font-size:20px; margin:0 0 4px; }
        .header p  { color:rgba(255,255,255,.75); font-size:13px; margin:0; }
        .body { padding:32px 36px; color:#374151; font-size:14px; line-height:1.7; }
        .body p { margin:0 0 14px; }
        .cred-box { background:#f0f6fb; border:1px solid #d1e4f3; border-radius:8px; padding:20px 24px; margin:20px 0; }
        .cred-box table { width:100%; border-collapse:collapse; }
        .cred-box td { padding:5px 0; font-size:13px; }
        .cred-box td:first-child { color:#6b7280; font-weight:700; text-transform:uppercase; font-size:11px; letter-spacing:.04em; width:45%; }
        .cred-box td:last-child  { color:#111827; font-weight:600; }
        .cred-val { background:#fff; border:1px solid #d1e4f3; border-radius:5px; padding:4px 10px; font-family:monospace; font-size:13px; display:inline-block; }
        .btn-wrap { text-align:center; margin:28px 0 16px; }
        .btn { display:inline-block; background:#528CBE; color:#fff; text-decoration:none; font-weight:700; font-size:14px; padding:12px 32px; border-radius:8px; }
        .notice { background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:14px 18px; font-size:12.5px; color:#92400e; margin:20px 0; }
        .notice strong { display:block; margin-bottom:4px; }
        .footer { background:#f9fafb; border-top:1px solid #e5e7eb; padding:20px 36px; font-size:11.5px; color:#9ca3af; text-align:center; line-height:1.6; }
    </style>
</head>
<body>
<div class="wrap">

    <div class="header">
        <h1>Nidaamka IECMS</h1>
        <p>Integrated Electronic Case Management System</p>
    </div>

    <div class="body">
        <p>Mudane/Marwo <strong>{{ $empName }}</strong>,</p>

        <p>Waxaan si sharaf leh kuugu soo dhawaynaynaa Nidaamka Casriga ee Maamulka Dacwadaha Maxkamadaha <strong>(IECMS)</strong>.</p>

        <p>Waxaa laguu sameeyay koonto rasmi ah oo aad ku geli karto nidaamka. Fadlan isticmaal xogta hoos ku xusan si aad u gasho:</p>

        <div class="cred-box">
            <table>
                <tr>
                    <td>Magaca Isticmaalaha (Username)</td>
                    <td><span class="cred-val">{{ $username }}</span></td>
                </tr>
                <tr>
                    <td>Furaha Sirta (Password)</td>
                    <td><span class="cred-val">{{ $password }}</span></td>
                </tr>
                <tr>
                    <td>Iimayl</td>
                    <td><span class="cred-val">{{ $empEmail }}</span></td>
                </tr>
            </table>
        </div>

        <div class="btn-wrap">
            <a href="https://ecms.judiciary.gov.so/" class="btn">Gal Nidaamka &rarr;</a>
        </div>

        <div class="notice">
            <strong>&#9888; Ogeysiis Muhiim ah</strong>
            Waxaan kugula talinaynaa in aad si degdeg ah u beddesho furaha sirta marka aad gasho nidaamka markii ugu horreysa, si loo xaqiijiyo amniga koontadaada.
            Fadlan ogow in ilaalinta xogta koontadaadu ay tahay mas'uuliyad gaar ah — <strong>ha la wadaagin cid kale</strong>.
        </div>

        <p>Haddii aad u baahato taageero ama macluumaad dheeraad ah, fadlan la xiriir <strong>Waaxda ICT</strong>.</p>

        <p>Mahadsanid.<br>Wabilaahi Towfiiq,</p>
    </div>

    <div class="footer">
        <strong style="color:#6b7280;">Waaxda ICT &mdash; Maxkamadda Sare</strong><br>
        Jamhuuriyadda Federaalka Soomaaliya<br>
        <span style="color:#d1d5db;">Farriintaan waxay u taagan tahay qofka magiciisa ku xusan. Ha u gudbin cid kale.</span>
    </div>

</div>
</body>
</html>
