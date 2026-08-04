@extends('admin.admin_master')
@section('page_title', 'Guriga — Direct Complaint')
@section('admin_main_content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">

    <div class="p-4 sm:p-6 w-full">

        {{-- Stats --}}
        <div class="mb-8">
            <div class="flex items-center gap-2.5 mb-5">
                <span class="w-7 h-7 rounded-lg bg-primary-50 flex items-center justify-center">
                    <i class="bi bi-graph-up-arrow text-[13px] text-primary"></i>
                </span>
                <h2 class="text-sm font-black uppercase tracking-widest text-neutral-500">Guud Ahaan Tirakoobka Dacwadaha</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                @php
                    $total = max($stats['totalCases'], 1);
                    $statCards = [
                        ['value' => $stats['totalCases'],       'label' => 'Wadarta Dacwadaha',        'icon' => 'bi-file-earmark-text-fill', 'color' => '#0A284D'],
                        ['value' => $stats['activeCases'],      'label' => 'Dacwadaha Socda',           'icon' => 'bi-clock-history',          'color' => '#528CBE'],
                        ['value' => $stats['unassignedCases'],  'label' => 'Dacwado Aan Loo Xilsaarin', 'icon' => 'bi-person-dash-fill',       'color' => '#F0B43C', 'highlight' => true],
                        ['value' => $stats['directComplaints'], 'label' => 'Cabashooyinka Toos ah',     'icon' => 'bi-person-fill',            'color' => '#16A34A'],
                        ['value' => $stats['cidReferrals'],     'label' => 'Gudbinta CID',              'icon' => 'bi-send-fill',              'color' => '#B91C1C'],
                    ];
                @endphp
                @foreach($statCards as $card)
                    @php $percent = round(($card['value'] / $total) * 100); @endphp
                    <div class="group relative bg-white rounded-2xl border border-neutral-100 p-6 shadow-[0_1px_2px_rgba(16,24,40,0.04)] hover:shadow-[0_12px_28px_-8px_rgba(16,24,40,0.14)] hover:-translate-y-0.5 hover:border-neutral-200 transition-all duration-300">
                        @if($card['highlight'] ?? false)
                            <span class="absolute top-4 right-16 text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full" style="background:{{ $card['color'] }}1F;color:#C07E15">Baahan Fal</span>
                        @endif
                        <div class="flex items-start justify-between mb-2">
                            <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-[1.5px] truncate">{{ $card['label'] }}</p>
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:scale-105"
                                 style="background:{{ $card['color'] }}14">
                                <i class="bi {{ $card['icon'] }} text-xl" style="color:{{ $card['color'] }}"></i>
                            </div>
                        </div>

                        <div class="flex items-end justify-between mb-4">
                            <div>
                                <h3 class="text-3xl font-black text-neutral-800">{{ $card['value'] }}</h3>
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Tirada</p>
                            </div>
                            <div class="text-right">
                                <h4 class="text-xl font-black" style="color:{{ $card['color'] }}">{{ $percent }}%</h4>
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Boqolkiiba</p>
                            </div>
                        </div>

                        <div class="w-full h-1.5 bg-neutral-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700 ease-out"
                                 style="background:{{ $card['color'] }}; width:{{ $percent }}%"></div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-[9px] font-bold uppercase tracking-wider text-neutral-400">
                            <span>Wadarta Guud Dacwadaha</span>
                            <span style="color:{{ $card['color'] }}">{{ $stats['totalCases'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Hearing Schedule Calendar --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-neutral-100 mb-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-neutral-800">Jadwalka Dhagaysiyada</h3>
            </div>
            <div id="attorney-dashboard-calendar"></div>
        </div>

        {{-- Case Filing Activity --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-neutral-100">
            <div class="mb-2">
                <h3 class="text-lg font-bold text-neutral-800">Dhaqdhaqaaqa Diiwaan Gelinta Dacwadaha</h3>
            </div>
            <div class="h-80">
                <canvas id="attorneyActivityChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calEl = document.getElementById('attorney-dashboard-calendar');
                if (!calEl) return;

                const calendar = new FullCalendar.Calendar(calEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: { left: 'title', center: '', right: 'today prev,next' },
                    height: 520,
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

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('attorneyActivityChart')?.getContext('2d');
                if (!ctx) return;

                const filingActivity = @json($filingActivity);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                        datasets: filingActivity.map(stat => ({
                            label: stat.name,
                            data: stat.monthly,
                            backgroundColor: stat.color,
                            hoverBackgroundColor: stat.color,
                            borderRadius: 4,
                            barThickness: 12,
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                align: 'end',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                    boxHeight: 8,
                                    font: { family: 'Inter', size: 11, weight: '700' },
                                    color: '#6B7280',
                                    padding: 20,
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1F2937',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                padding: 12,
                                borderRadius: 8,
                                callbacks: {
                                    title: (items) => items[0].label,
                                    label: (item) => `  ${item.dataset.label}: ${item.raw}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 5,
                                    font: { family: 'Inter', size: 11 },
                                    color: '#6B7280'
                                },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { family: 'Inter', size: 11 },
                                    color: '#6B7280'
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
