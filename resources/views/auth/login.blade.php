<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>The Integrated Electronic Case Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="login-page">

    <!-- Page wrapper -->
    <div class="login-wrapper">

        <!-- Left panel -->

        <!-- Right panel -->
        <div class="login-right">
            <div class="login-card">

                <!-- Logo header -->
                <div class="login-card-logo">
                    <div class="login-logo-icon">
                        <img src="{{ asset('images/logo.png') }}" alt="IECMS Logo">
                    </div>
                    <h2>IECMS</h2>
                    <p>Integrated Electronic Case Management System</p>
                </div>

                <!-- Form body -->
                <div class="login-card-body">

                    <!-- Toast container -->
                    <div id="toast-container" class="toast-container"></div>

                    <form method="POST" action="{{ route('login') }}" id="login-form">
                        @csrf

                        <div class="login-form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email" autofocus placeholder="Enter your email address">
                            @error('email')
                                <p class="login-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="login-form-group">
                            <label for="password">Password <span class="required">*</span></label>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password" placeholder="Enter your password">
                            @error('password')
                                <p class="login-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="login-form-row">
                            <label class="login-remember-label">
                                <input id="remember" name="remember" type="checkbox">
                                Remember me
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="login-forgot-link">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </button>
                    </form>

                </div>

                <!-- Footer -->
                <div class="login-card-footer">
                    <p>&copy; {{ date('Y') }} IECMS. All rights reserved.</p>
                </div>

            </div>
        </div>

    </div>

    <script>
        function showToast(message, type = 'error') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const colors = {
                error: 'background-color:#DC2626',
                success: 'background-color:#16A34A',
                info: 'background-color:#3D78AB',
            };
            const icons = {
                error: 'fa-exclamation-circle',
                success: 'fa-check-circle',
                info: 'fa-info-circle',
            };

            const toast = document.createElement('div');
            toast.style.cssText = `display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border-radius:0.5rem;box-shadow:0 4px 12px rgba(0,0,0,0.2);color:#fff;min-width:280px;max-width:380px;font-size:0.875rem;${colors[type] || colors.info}`;
            toast.innerHTML = `
                <i class="fas ${icons[type] || icons.info}"></i>
                <span style="flex:1">${message}</span>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#fff;cursor:pointer;padding:0;">
                    <i class="fas fa-times"></i>
                </button>`;

            container.appendChild(toast);
            setTimeout(() => { if (toast.parentElement) toast.remove(); }, 5000);
        }
    </script>

</body>

</html>