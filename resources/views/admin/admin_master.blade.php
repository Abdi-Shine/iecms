<!DOCTYPE html>
<html lang="en" data-font-size="{{ auth()->user()->font_size ?? 'md' }}"
    data-theme="{{ auth()->user()->theme ?? 'light' }}"
    class="{{ (auth()->user()->theme ?? 'light') === 'dark' ? 'dark' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'Dashboard') - IECMS Judiciary Portal</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Theme resolver — runs before CSS paints so "System" resolves to the
         OS preference immediately, and stays live if the OS preference changes. -->
    <script>
        (function () {
            var html = document.documentElement;
            function resolve(theme) {
                if (theme === 'system') {
                    return (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
                }
                return theme === 'dark' ? 'dark' : 'light';
            }
            html.setAttribute('data-resolved-theme', resolve(html.getAttribute('data-theme')));
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
                    if (html.getAttribute('data-theme') === 'system') {
                        html.setAttribute('data-resolved-theme', e.matches ? 'dark' : 'light');
                    }
                });
            }
        })();
    </script>

    <!-- Vite — compiled CSS + JS (single source of truth for Tailwind) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
    @stack('styles')
</head>

<body class="bg-background font-inter overflow-hidden dark:bg-slate-900">
    <!-- SIDEBAR -->
    @include('admin.body.sidebar')
    <!-- TOP HEADER (fixed, never scrolls) -->
    @include('admin.body.header')
    <!-- MAIN CONTENT (only this scrolls) -->
    <main class="lg:ml-[260px] h-screen overflow-y-auto pt-16 dark:bg-slate-900">
        @yield('admin_main_content')
    </main>

    <!-- Mobile Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden"></div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Global Success Alert (matches user branding)
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#528CBE', // Branding Primary Blue
                customClass: {
                    title: 'text-2xl font-bold text-neutral-800',
                    confirmButton: 'px-8 py-2 rounded-lg font-bold uppercase tracking-wider text-sm'
                },
                buttonsStyling: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            });
        @endif

        // Global Warning Alert (matches user branding)
        @if(session('warning'))
            Swal.fire({
                title: 'Warning',
                text: "{{ session('warning') }}",
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#F0B43C', // Branding Warning Yellow
                customClass: {
                    title: 'text-2xl font-bold text-neutral-800',
                    confirmButton: 'px-8 py-2 rounded-lg font-bold uppercase tracking-wider text-sm'
                },
                buttonsStyling: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            });
        @endif

        // Global Delete Confirmation
        function confirmDelete(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626', // Branding Red/Error
                cancelButtonColor: '#528CBE',  // Branding Primary Blue
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    title: 'text-2xl font-bold text-neutral-800',
                    confirmButton: 'px-6 py-2 rounded-lg font-bold text-sm mx-1',
                    cancelButton: 'px-6 py-2 rounded-lg font-bold text-sm mx-1'
                },
                buttonsStyling: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        menuToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        // Case Status Chart
        const caseStatusCtx = document.getElementById('caseStatusChart')?.getContext('2d');
        if (caseStatusCtx) {
            new Chart(caseStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Closed', 'Pending'],
                    datasets: [{
                        data: [60, 25, 15],
                        backgroundColor: ['#528CBE', '#F0B43C', '#A8D0EC'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
                            borderRadius: 8
                        }
                    }
                }
            });
        }

        // Staff Breakdown Chart
        const staffCtx = document.getElementById('staffChart')?.getContext('2d');
        if (staffCtx) {
            new Chart(staffCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Judges', 'Other Staff'],
                    datasets: [{
                        data: [{{ $totalJudges ?? 0 }}, {{ ($totalEmployees ?? 0) - ($totalJudges ?? 0) }}],
                        backgroundColor: ['#528CBE', '#F0B43C'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
                            borderRadius: 8
                        }
                    }
                }
            });
        }

    </script>
    @stack('scripts')
</body>

</html>