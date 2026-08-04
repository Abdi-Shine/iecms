@extends('admin.admin_master')
@section('page_title', 'Jadwalka Maxkamadaha — Direct Complaint')
@section('admin_main_content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">

@php
    $statusColors = [
        'Scheduled' => ['bg' => '#EFF6FF', 'text' => '#2563EB'],
        'Completed' => ['bg' => '#DCFCE7', 'text' => '#16A34A'],
        'Postponed' => ['bg' => '#FEF3C7', 'text' => '#D97706'],
        'Cancelled' => ['bg' => '#FEE2E2', 'text' => '#DC2626'],
    ];
@endphp

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Jadwalka Maxkamadaha</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Taariikhaha maxkamadeynta ee dacwadaha cabashada toosan ah</p>
        </div>
        <a href="{{ route('attorney-cases.tracking') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-diagram-3"></i> Lasocodka Dacwadaha
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(82,140,190,0.1)">
                <i class="bi bi-calendar3 text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Wadarta</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['total'] }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(10,40,77,0.1)">
                <i class="bi bi-calendar-month text-xl" style="color:#0A284D"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Bishaan</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['thisMonth'] }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(37,99,235,0.1)">
                <i class="bi bi-hourglass-split text-xl" style="color:#2563EB"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">La Qorsheeyay</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['scheduled'] }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(34,197,94,0.1)">
                <i class="bi bi-check2-circle text-xl" style="color:#22C55E"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Dhammaystiran</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['completed'] }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-arrow-repeat text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Dib U Dhigay</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['postponed'] }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Calendar --}}
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-neutral-100 p-5">
            <div id="attorney-calendar"></div>
        </div>

        {{-- Upcoming list --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                <i class="bi bi-list-check text-sm" style="color:#528CBE"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Maxkamadaha Soo Socda</span>
            </div>
            <div class="divide-y divide-neutral-100">
                @forelse($upcoming as $p)
                    @php $sc = $statusColors[$p->status] ?? ['bg' => '#F3F4F6', 'text' => '#4B5563']; @endphp
                    <a href="{{ route('attorney-cases.show', $p->attorney_case_id) }}" class="block px-6 py-3.5 hover:bg-neutral-50 transition-colors">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-mono text-sm font-bold" style="color:#528CBE">{{ $p->attorneyCase->case_number ?? '—' }}</span>
                            <span class="text-[10px] font-black uppercase tracking-wide px-2 py-0.5 rounded-full whitespace-nowrap" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }}">{{ $p->status }}</span>
                        </div>
                        <p class="text-sm text-neutral-700 truncate">{{ $p->attorneyCase->title ?? '—' }}</p>
                        <p class="text-xs text-neutral-400 mt-0.5 flex items-center gap-1.5">
                            <i class="bi bi-calendar3"></i> {{ $p->hearing_date->format('d/m/Y') }}
                            @if($p->hearing_time)
                                <span>&middot;</span> {{ $p->hearing_time }}
                            @endif
                            <span>&middot;</span> {{ $p->proceeding_type }}
                        </p>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center gap-2 py-12 px-4 text-center">
                        <i class="bi bi-calendar-x text-2xl text-neutral-300"></i>
                        <p class="text-sm text-neutral-400">Ma jiraan maxkamado soo socda oo la qorsheeyay.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calEl = document.getElementById('attorney-calendar');
            if (!calEl) return;

            const calendar = new FullCalendar.Calendar(calEl, {
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'title', center: '', right: 'today prev,next' },
                height: 650,
                expandRows: true,
                events: @json($calendarEvents),
                eventDisplay: 'block',
                dayMaxEvents: 4,
                displayEventTime: false,
                eventTextColor: '#fff',
                eventBorderColor: 'transparent',
            });
            calendar.render();
            window.addEventListener('resize', () => calendar.updateSize());
        });
    </script>
@endpush

@endsection
