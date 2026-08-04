@extends('admin.admin_master')
@section('page_title', 'Dashboard')
@section('admin_main_content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">

    <div class="p-6">

        <!-- Case Statistics Overview -->
        <div class="mb-8">
            <div class="flex items-center gap-2.5 mb-5">
                <span class="w-7 h-7 rounded-lg bg-primary-50 flex items-center justify-center">
                    <i class="bi bi-graph-up-arrow text-[13px] text-primary"></i>
                </span>
                <h2 class="text-sm font-black uppercase tracking-widest text-neutral-500">Guud Ahaan Tirakoobka Dacwadaha</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($caseStats as $stat)
                    @php
                        $percent = $stat['reg'] > 0 ? round(($stat['closed'] / $stat['reg']) * 100) : 0;
                    @endphp
                    <div class="group relative bg-white rounded-2xl border border-neutral-100 p-6 shadow-[0_1px_2px_rgba(16,24,40,0.04)] hover:shadow-[0_12px_28px_-8px_rgba(16,24,40,0.14)] hover:-translate-y-0.5 hover:border-neutral-200 transition-all duration-300">
                        <div class="flex items-start justify-between mb-2">
                            <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-[1.5px] truncate">{{ $stat['name'] }}</p>
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:scale-105"
                                 style="background:{{ $stat['color'] }}14">
                                <i class="bi {{ $stat['icon'] }} text-xl" style="color:{{ $stat['color'] }}"></i>
                            </div>
                        </div>

                        <div class="flex items-end justify-between mb-4">
                            <div>
                                <h3 class="text-3xl font-black text-neutral-800">{{ $stat['reg'] }}</h3>
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">La Diiwaan Geliyay</p>
                            </div>
                            <div class="text-right">
                                <h4 class="text-xl font-black" style="color:{{ $stat['color'] }}">{{ $stat['closed'] }}</h4>
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">La Xidhay</p>
                            </div>
                        </div>

                        <div class="w-full h-1.5 bg-neutral-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700 ease-out"
                                 style="background:{{ $stat['color'] }}; width:{{ $percent }}%"></div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-[9px] font-bold uppercase tracking-wider text-neutral-400">
                            <span>Heerka Dhamaystirka</span>
                            <span style="color:{{ $stat['color'] }}">{{ $percent }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- System Summary removed --}}

        <!-- Calendar -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-border mb-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-text-primary">Hearing Schedule Calendar</h3>
                {{-- Legend removed --}}
            </div>
            <div id="dashboard-calendar"></div>
        </div>

        <!-- Case Activity Chart -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-border">
            <div class="mb-2">
                <h3 class="text-lg font-bold text-text-primary">Case Filing Activity</h3>
            </div>
            <div class="h-80">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calEl = document.getElementById('dashboard-calendar');
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
                    eventClick: function(info) {
                        const props = info.event.extendedProps;
                        window.dispatchEvent(new CustomEvent('open-hearing-modal', { 
                            detail: {
                                fileNo: props.fileNo,
                                caseType: props.caseType,
                                caseStatus: props.caseStatus,
                                plaintiff: props.plaintiff,
                                judges: props.judges,
                                clerk: props.clerk,
                                date: props.date,
                                time: props.time
                            } 
                        }));
                    }
                });
                calendar.render();
                window.addEventListener('resize', () => calendar.updateSize());
            });
        </script>

        {{-- Case Filing Activity Chart — real monthly data for all four case types --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('activityChart')?.getContext('2d');
                if (!ctx) return;

                const caseStats = @json($caseStats);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                        datasets: caseStats.map(stat => ({
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

    <!-- Hearing Detail Modal (Alpine.js) -->
    <div x-data="{ 
            isOpen: false, 
            h: { fileNo: '', caseType: '', caseStatus: '', plaintiff: '', judges: '', clerk: '', date: '', time: '' } 
        }"
        @open-hearing-modal.window="h = $event.detail; isOpen = true"
        x-show="isOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm transition-opacity" 
             x-show="isOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             @click="isOpen = false"></div>

        <!-- Modal Panel -->
        <div class="relative bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden transform transition-all"
             x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <div class="px-6 py-5 bg-neutral-50 border-b border-neutral-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-neutral-800">Case Hearing Schedule</h3>
                <button @click="isOpen = false" class="text-neutral-400 hover:text-neutral-600 transition-colors p-1">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            <div class="px-8 py-7 space-y-5">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Case Number</span>
                    <span class="text-base font-black text-[#528CBE]" x-text="h.fileNo + ' (' + h.caseStatus + ')'"></span>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col border-l-2 border-neutral-100 pl-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Case Type</span>
                        <span class="text-sm font-bold text-neutral-700" x-text="h.caseType"></span>
                    </div>
                    <div class="flex flex-col border-l-2 border-neutral-100 pl-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Plaintiff</span>
                        <span class="text-sm font-bold text-neutral-700" x-text="h.plaintiff"></span>
                    </div>
                </div>

                <div class="flex flex-col border-l-2 border-neutral-100 pl-4">
                    <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Panel of Judges</span>
                    <span class="text-sm font-bold text-neutral-700" x-text="h.judges + ' (Decision pending)'"></span>
                </div>

                <div class="flex flex-col border-l-2 border-neutral-100 pl-4">
                    <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Clerk (Assistant)</span>
                    <span class="text-sm font-bold text-neutral-700" x-text="h.clerk"></span>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col border-l-2 border-[#528CBE]/30 pl-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Hearing Date</span>
                        <span class="text-sm font-bold text-neutral-700" x-text="h.date"></span>
                    </div>
                    <div class="flex flex-col border-l-2 border-[#528CBE]/30 pl-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Next Hearing</span>
                        <span class="text-sm font-bold text-neutral-700" x-text="h.date"></span>
                    </div>
                </div>

                <div class="flex items-center gap-3 bg-[#528CBE]/5 p-3 rounded-2xl border border-[#528CBE]/10">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm text-[#528CBE]">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black uppercase tracking-widest text-neutral-400">Scheduled Time</span>
                        <span class="text-sm font-black text-[#528CBE]" x-text="h.time"></span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-neutral-50 border-t border-neutral-100 flex justify-end">
                <button @click="isOpen = false" 
                        class="px-8 py-2.5 text-xs font-black uppercase tracking-widest text-neutral-500 bg-white border border-neutral-200 rounded-xl hover:bg-neutral-100 transition-all shadow-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
@endsection