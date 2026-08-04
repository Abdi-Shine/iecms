<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lock Screen - IECMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        html, body { height: 100%; margin: 0; padding: 0; }
        .lock-card { width: 320px; }
    </style>
</head>
<body class="bg-primary" style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem;">

    <div class="lock-card bg-white rounded-2xl shadow-2xl overflow-hidden">

        <!-- Top Section -->
        <div class="bg-primary px-6 pt-8 pb-6 text-center">
            <!-- Icon -->
            <div class="w-12 h-12 bg-accent rounded-xl flex items-center justify-center mx-auto mb-4 shadow">
                <i class="bi bi-shield-shaded text-primary text-2xl"></i>
            </div>
            <h2 class="text-white font-bold text-base tracking-wide">IECMS</h2>
            <p class="text-white/50 text-[10px] uppercase tracking-widest mt-0.5">Judiciary Portal</p>
        </div>

        <!-- Accent Divider -->
        <div class="h-1 bg-accent"></div>

        <!-- Body -->
        <div class="px-6 pt-5 pb-6">

            <!-- Clock & Date -->
            <div class="mb-4">
                <div id="lock-time" class="text-primary font-bold text-lg leading-tight"></div>
                <div id="lock-date" class="text-primary text-sm"></div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('lock-screen.unlock') }}">
                @csrf

                <p class="text-primary font-semibold text-sm mb-2">Password</p>

                <div class="relative mb-4">
                    <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input id="lock-password" type="password" name="password" placeholder="••••••••"
                        class="w-full pl-9 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary transition-all"
                        autofocus>
                    <button type="button" onclick="togglePassword()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i id="eye-icon" class="bi bi-eye text-sm"></i>
                    </button>
                </div>

                @error('password')
                    <p class="text-red-500 text-xs mb-3">{{ $message }}</p>
                @enderror

                <button type="submit"
                    class="w-full py-2.5 bg-accent text-primary font-bold rounded-xl text-sm tracking-widest uppercase hover:bg-accent/90 transition-all flex items-center justify-center gap-2">
                    <i class="bi bi-unlock text-sm"></i> Unlock Session
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="px-6 pb-5 text-center border-t border-gray-100 pt-4">
            <a href="{{ route('login') }}" class="text-xs text-gray-400 hover:text-primary transition-colors">
                Sign in as a different user
            </a>
            <p class="text-[10px] text-gray-300 mt-2">&copy; {{ date('Y') }} IECMS. All rights reserved.</p>
        </div>
    </div>

    <script>
        function pad(n) { return n < 10 ? '0' + n : n; }

        function updateClock() {
            const now = new Date();
            const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            document.getElementById('lock-time').textContent =
                pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
            document.getElementById('lock-date').textContent =
                days[now.getDay()] + ', ' + months[now.getMonth()] + ' ' + now.getDate() + ', ' + now.getFullYear();
        }

        updateClock();
        setInterval(updateClock, 1000);

        function togglePassword() {
            const input = document.getElementById('lock-password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash text-sm';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye text-sm';
            }
        }
    </script>
</body>
</html>
