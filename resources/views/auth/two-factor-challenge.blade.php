<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Two-Factor Authentication — IECMS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="login-page">

    <div class="login-wrapper">
        <div class="login-right">
            <div class="login-card">

                <div class="login-card-logo">
                    <div class="login-logo-icon">
                        <img src="{{ asset('images/logo.png') }}" alt="IECMS Logo">
                    </div>
                    <h2>IECMS</h2>
                    <p>Two-factor authentication required</p>
                </div>

                <div class="login-card-body">

                    <p style="font-size:0.85rem;color:#6B7280;margin-bottom:1.25rem">
                        Enter the 6-digit code from your authenticator app, or use one of your recovery codes.
                    </p>

                    <form method="POST" action="{{ route('two-factor.challenge.store') }}" id="two-factor-form">
                        @csrf

                        <div class="login-form-group">
                            <label for="code">Authentication code</label>
                            <input id="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code"
                                autofocus placeholder="123456">
                            @error('code')
                                <p class="login-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="login-form-group">
                            <label for="recovery_code">Or a recovery code</label>
                            <input id="recovery_code" type="text" name="recovery_code" autocomplete="off"
                                placeholder="Only if you've lost access to your device">
                        </div>

                        <button type="submit" class="btn-login">
                            <i class="fas fa-shield-halved"></i> Verify
                        </button>
                    </form>

                    <p style="margin-top:1rem;text-align:center">
                        <a href="{{ route('login') }}" style="color:#6B7280;font-size:0.8rem;text-decoration:underline">
                            Cancel and return to sign in
                        </a>
                    </p>

                </div>

                <div class="login-card-footer">
                    <p>&copy; {{ date('Y') }} IECMS. All rights reserved.</p>
                </div>

            </div>
        </div>
    </div>

</body>

</html>
