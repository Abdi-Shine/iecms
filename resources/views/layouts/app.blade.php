<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IECMS') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-100">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        @php $company = null; @endphp
        @include('admin.body.sidebar')

        <!-- Main content area -->
        <div class="flex flex-col min-w-0 w-full lg:ml-[260px] lg:max-w-[calc(100%-260px)]">

            <!-- Top Header -->
            @include('admin.body.header')

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-auto">
                {{ $slot }}
            </main>

        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div id="sidebar-overlay"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden"
         onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden');">
    </div>

    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>

</body>
</html>
