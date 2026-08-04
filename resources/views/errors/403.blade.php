<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Oggolaansho la'aani</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.08);
            padding: 56px 48px;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .icon-wrap {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: rgba(220,38,38,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .icon-wrap i { font-size: 40px; color: #DC2626; }
        .code {
            font-size: 72px;
            font-weight: 900;
            color: #e5e7eb;
            line-height: 1;
            margin-bottom: 8px;
            letter-spacing: -4px;
        }
        h1 {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 10px;
        }
        p {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: #528CBE;
            color: white;
            font-size: 14px;
            font-weight: 700;
            border-radius: 12px;
            text-decoration: none;
            transition: opacity .15s;
        }
        .btn-back:hover { opacity: .88; }
        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: white;
            color: #DC2626;
            font-size: 14px;
            font-weight: 700;
            border-radius: 12px;
            border: 2px solid #fecaca;
            text-decoration: none;
            cursor: pointer;
            transition: background .15s, border-color .15s;
        }
        .btn-logout:hover { background: #fef2f2; border-color: #DC2626; }
        .btn-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .divider {
            width: 48px;
            height: 3px;
            background: linear-gradient(90deg, #DC2626, #F0B43C);
            border-radius: 4px;
            margin: 16px auto 24px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div class="code">403</div>
        <div class="divider"></div>
        <h1>Oggolaansho la'aani</h1>
        <p>
            Adiga kuma haysid ogolaanshaha aad u baahan tahay si aad u gasho boggan.<br>
            Fadlan la xiriir maamulaha nidaamka haddaad u maleynayso in tani khalad tahay.
        </p>
        <div class="btn-group">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Ku Noqo
            </a>
            @auth
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Ka Bax Nidaamka
                </button>
            </form>
            @endauth
        </div>
    </div>
</body>
</html>
