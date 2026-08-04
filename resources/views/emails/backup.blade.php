<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f4f6f8;margin:0;padding:30px">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,0.08)">
    <div style="background:#004161;padding:28px 32px;text-align:center">
        <h1 style="color:#F0B43C;margin:0;font-size:22px;font-weight:900;letter-spacing:1px">IECMS Portal</h1>
        <p style="color:#fff;margin:6px 0 0;font-size:12px;opacity:.8">Judiciary System — Automated Backup Vault</p>
    </div>
    <div style="padding:32px">
        <h2 style="color:#1f2937;font-size:16px;margin:0 0 12px">Database Backup Attached</h2>
        <p style="color:#6b7280;font-size:13px;line-height:1.6;margin:0 0 20px">
            A full snapshot of the <strong>IECMS database</strong> has been generated and attached to this email.
            Please store it in a secure location.
        </p>
        <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:20px">
            <table style="width:100%;font-size:12px;color:#374151">
                <tr><td style="padding:4px 0;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">Date</td><td style="padding:4px 0">{{ now()->format('d/m/Y H:i') }}</td></tr>
                <tr><td style="padding:4px 0;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">Database Size</td><td style="padding:4px 0">{{ $dbSize }} MB</td></tr>
                <tr><td style="padding:4px 0;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">File</td><td style="padding:4px 0">{{ $fileName }}</td></tr>
            </table>
        </div>
        <p style="color:#9ca3af;font-size:11px;margin:0">This is an automated message from the IECMS system. Do not reply to this email.</p>
    </div>
    <div style="background:#f8fafc;padding:16px 32px;border-top:1px solid #e5e7eb;text-align:center">
        <p style="color:#9ca3af;font-size:10px;margin:0">© {{ date('Y') }} Judiciary of Somalia — ICT Division</p>
    </div>
</div>
</body>
</html>
