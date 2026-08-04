@extends('admin.admin_master')
@section('page_title', 'View Hearings')
@section('admin_main_content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">


    <div class="p-4 sm:p-6 w-full">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Mudeymaha Dacwadaha Fulinta </h1>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="flex gap-4 mb-6">
            <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm border border-neutral-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-primary">
                    <i class="bi bi-calendar3 text-primary"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-neutral-400 uppercase tracking-widest">Wadarta</p>
                    <h3 class="text-xl font-black text-neutral-800">{{ $stats['total'] }}</h3>
                </div>
            </div>
            <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm border border-neutral-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-primary">
                    <i class="bi bi-calendar-check text-primary"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-neutral-400 uppercase tracking-widest">Mudeymaha</p>
                    <h3 class="text-xl font-black text-neutral-800">{{ $stats['scheduled'] }}</h3>
                </div>
            </div>
            <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm border border-neutral-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-success">
                    <i class="bi bi-check2-circle text-success"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-neutral-400 uppercase tracking-widest">Dhameestiran</p>
                    <h3 class="text-xl font-black text-neutral-800">{{ $stats['completed'] }}</h3>
                </div>
            </div>
            <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm border border-neutral-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-gold">
                    <i class="bi bi-arrow-repeat text-gold"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-neutral-400 uppercase tracking-widest">Dib U dhacay</p>
                    <h3 class="text-xl font-black text-neutral-800">{{ $stats['postponed'] }}</h3>
                </div>
            </div>
            <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm border border-neutral-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-danger">
                    <i class="bi bi-x-circle text-danger"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-neutral-400 uppercase tracking-widest">La joojiyay</p>
                    <h3 class="text-xl font-black text-neutral-800">{{ $stats['cancelled'] }}</h3>
                </div>
            </div>
            <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm border border-neutral-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-primary">
                    <i class="bi bi-calendar-month text-primary"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-neutral-400 uppercase tracking-widest">Bishaan </p>
                    <h3 class="text-xl font-black text-neutral-800">{{ $stats['thisMonth'] }}</h3>
                </div>
            </div>
        </div>

        {{-- Calendar layout --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-5 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-neutral-700">Jadwalka Dacwadaha</h3>
                {{-- Legend removed --}}
            </div>
            <div id="view-calendar"></div>
        </div>

        {{-- Search / Filter --}}
        <div
            class="bg-white rounded-2xl shadow-sm border border-neutral-100 px-5 py-4 mb-4 flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" id="search-input" placeholder="Raadi lambarka faylka, ujeedada, gurfaha..."
                    class="w-full px-4 py-2 text-xs border border-neutral-200 rounded-xl outline-none focus:border-[#528CBE] focus:ring-2 focus:ring-[#528CBE]/10 transition-all"
                    oninput="filterTable()">
            </div>
            <div>
                <select id="status-filter"
                    class="px-3 py-2 text-xs border border-neutral-200 rounded-xl outline-none focus:border-[#528CBE] transition-all"
                    onchange="filterTable()">
                    <option value="">Dhammaan Xaaladaha</option>
                    <option value="Scheduled">Mudeystay</option>
                    <option value="Completed">Dhameystiran</option>
                    <option value="Postponed">Dib u dhacay</option>
                    <option value="Cancelled">La joojiyay</option>
                </select>
            </div>
            <div>
                <select id="subcase-filter"
                    class="px-3 py-2 text-xs border border-neutral-200 rounded-xl outline-none focus:border-[#528CBE] transition-all"
                    onchange="filterTable()">
                    <option value="">Nooca Dacwada</option>
                    @foreach($executionSubCases as $sub)
                        <option value="{{ strtolower($sub) }}">{{ $sub }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="month" id="month-filter"
                    class="px-3 py-2 text-xs border border-neutral-200 rounded-xl outline-none focus:border-[#528CBE] transition-all"
                    value="{{ date('Y-m') }}" onchange="filterTable()">
            </div>
            <button onclick="clearFilters()"
                class="px-4 py-2 text-xs font-semibold text-neutral-500 border border-neutral-200 rounded-xl hover:bg-neutral-50 transition-all">
                <i class="bi bi-x-circle mr-1"></i> Nadiifi
            </button>
        </div>

        {{-- All Hearings Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-table text-sm text-primary"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhammaan Mudeymaha</span>
                </div>
                <span id="row-count" class="text-[10px] font-semibold text-neutral-400">{{ $hearings->count() }} diiwaanada</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="hearings-table">
                    <thead>
                        <tr class="table-header-tint">
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">No#</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Lambarka Faylka</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dacwada</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Dacwada</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda & Wakhtiga</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Muddada</th>

                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Ujeedada</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-24">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100" id="table-body">
                        @forelse($hearings as $i => $h)
                            @php
                                $caseStatus = $h->executionCase->Status ?? 'N/A';
                                $isActive = in_array($caseStatus, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                $isClosed = $caseStatus === 'Closed';
                                $csBg = $isActive ? 'rgba(16,185,129,0.1)' : ($isClosed ? 'rgba(239,68,68,0.1)' : 'rgba(240,180,60,0.12)');
                                $csColor = $isActive ? '#059669' : ($isClosed ? '#b91c1c' : '#C07E15');
                            @endphp
                            <tr class="hover:bg-neutral-50 transition-colors hearing-row"
                                data-file="{{ strtolower($h->executionCase->FileNo ?? '') }}" data-status="{{ $h->status }}"
                                data-month="{{ $h->hearing_date->format('Y-m') }}"
                                data-purpose="{{ strtolower($h->hearing_purpose ?? '') }}"
                                data-courtroom="{{ strtolower($h->courtroom ?? '') }}"
                                data-subcase="{{ strtolower($h->executionCase->SubCase ?? '') }}">
                                <td class="px-5 py-3.5"><span
                                        class="text-xs font-bold text-neutral-400">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="badge-case-type">{{ $h->executionCase->FileNo ?? '—' }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-neutral-600">{{ $h->executionCase->CaseType ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    @if($h->executionCase->SubCase ?? null)
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                            style="background:rgba(10,40,77,0.08);color:#0A284D">{{ $h->executionCase->SubCase }}</span>
                                    @else
                                        <span class="text-xs text-neutral-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs font-semibold text-neutral-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($h->hearing_date)->format('d/m/Y') }}<br>
                                    <span
                                        class="text-neutral-400 font-normal">{{ \Carbon\Carbon::parse($h->hearing_time)->format('H:i') }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-neutral-500">{{ $h->duration ?? '—' }}</td>

                                <td class="px-5 py-3.5 text-xs text-neutral-500 max-w-[200px] truncate">
                                    {{ $h->hearing_purpose ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                                          style="background:{{ $csBg }};color:{{ $csColor }}">
                                        {{ $caseStatus }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('execution-hearings.edit', $h->id) }}" title="Edit Hearing"
                                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-[#528CBE] hover:border-[#528CBE] hover:text-white">
                                        <i class="bi bi-pencil-square text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-neutral-400 text-sm">Mudeym lama helin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="no-results" class="hidden px-6 py-16 text-center text-neutral-400 text-sm">
                    <i class="bi bi-search text-3xl text-neutral-200 block mb-2"></i>
                    Mudeym kuma helin shaandhayntaada.
                </div>
            </div>
        </div>

    </div>

    @php
        $calendarEvents = $hearings->map(function ($h) {
            $color = match ($h->status) {
                'Completed' => '#10B981',
                'Cancelled' => '#DC2626',
                'Postponed' => '#F0B43C',
                default => '#528CBE',
            };

            $case = $h->executionCase;
            $plaintiff = $case->parties->where('party_type', 'Plaintiff')->first()->party_name ?? '—';
            $judges = $case->assignments->whereIn('panel_role', ['Chair', 'Member'])->pluck('employee.EmpName')->implode(' and ') ?: '—';
            $clerk = $case->assignments->where('panel_role', 'Clerk')->first()->employee->EmpName ?? '—';

            return [
                'title' => ($case->FileNo ?? '—'),
                'start' => $h->hearing_date->format('Y-m-d'),
                'color' => $color,
                'extendedProps' => [
                    'fileNo' => $case->FileNo ?? '—',
                    'caseType' => $case->CaseType ?? '—',
                    'caseStatus' => $case->Status ?? 'Pending',
                    'plaintiff' => $plaintiff,
                    'judges' => $judges,
                    'clerk' => $clerk,
                    'date' => $h->hearing_date->format('Y-m-d'),
                    'time' => $h->hearing_time,
                    'status' => $h->status ?? '—',
                ],
            ];
        })->values();
    @endphp

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calEl = document.getElementById('view-calendar');
                if (!calEl) return;
                const cal = new FullCalendar.Calendar(calEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: { left: 'title', center: '', right: 'today prev,next' },
                    height: 440,
                    expandRows: true,
                    events: @json($calendarEvents),
                    eventDisplay: 'block',
                    dayMaxEvents: 3,
                    displayEventTime: false,
                    eventTextColor: '#fff',
                    eventBorderColor: 'transparent',
                    eventClick: function (info) {
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
                cal.render();
                window.addEventListener('resize', () => cal.updateSize());
            });

            function filterTable() {
                const search = document.getElementById('search-input').value.toLowerCase();
                const status = document.getElementById('status-filter').value;
                const month = document.getElementById('month-filter').value;
                const subcase = document.getElementById('subcase-filter').value;
                const rows = document.querySelectorAll('.hearing-row');
                let visible = 0;

                rows.forEach(row => {
                    const matchSearch = !search ||
                        row.dataset.file.includes(search) ||
                        row.dataset.purpose.includes(search) ||
                        row.dataset.courtroom.includes(search);
                    const matchStatus = !status || row.dataset.status === status;
                    const matchMonth = !month || row.dataset.month === month;
                    const matchSubcase = !subcase || row.dataset.subcase === subcase;

                    if (matchSearch && matchStatus && matchMonth && matchSubcase) {
                        row.classList.remove('hidden');
                        visible++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                document.getElementById('row-count').textContent = visible + ' diiwaanada';
                document.getElementById('no-results').classList.toggle('hidden', visible > 0);
                document.getElementById('table-body').classList.toggle('hidden', visible === 0);
            }

            function clearFilters() {
                document.getElementById('search-input').value = '';
                document.getElementById('status-filter').value = '';
                document.getElementById('month-filter').value = '{{ date("Y-m") }}';
                document.getElementById('subcase-filter').value = '';
                filterTable();
            }
        </script>
    @endpush

    {{-- Hearing Detail Modal (Alpine.js) --}}
    <div x-data="{ 
                        isOpen: false, 
                        h: { fileNo: '', caseType: '', caseStatus: '', plaintiff: '', judges: '', clerk: '', date: '', time: '' } 
                    }" @open-hearing-modal.window="h = $event.detail; isOpen = true" x-show="isOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="display: none;">

        <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm transition-opacity" x-show="isOpen"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="isOpen = false"></div>

        <div class="relative bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden transform transition-all"
            x-show="isOpen" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <div class="px-6 py-5 bg-neutral-50 border-b border-neutral-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-neutral-800">Jadwalka Mudeyga Dacwada</h3>
                <button @click="isOpen = false" class="text-neutral-400 hover:text-neutral-600 transition-colors p-1">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            <div class="px-8 py-7 space-y-5">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Lambarka Dacwada</span>
                    <span class="text-base font-black text-[#528CBE]" x-text="h.fileNo + ' (' + h.caseStatus + ')'"></span>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col border-l-2 border-neutral-100 pl-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Nooca Dacwada</span>
                        <span class="text-sm font-bold text-neutral-700" x-text="h.caseType"></span>
                    </div>
                    <div class="flex flex-col border-l-2 border-neutral-100 pl-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Dacwadaha</span>
                        <span class="text-sm font-bold text-neutral-700" x-text="h.plaintiff"></span>
                    </div>
                </div>

                <div class="flex flex-col border-l-2 border-neutral-100 pl-4">
                    <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Gorsooreyaasha</span>
                    <span class="text-sm font-bold text-neutral-700" x-text="h.judges + ' (Go\'aan la sugayo)'"></span>
                </div>

                <div class="flex flex-col border-l-2 border-neutral-100 pl-4">
                    <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Kaaliyaha</span>
                    <span class="text-sm font-bold text-neutral-700" x-text="h.clerk"></span>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col border-l-2 border-[#528CBE]/30 pl-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Taariikhda Mudeyga</span>
                        <span class="text-sm font-bold text-neutral-700" x-text="h.date"></span>
                    </div>
                    <div class="flex flex-col border-l-2 border-[#528CBE]/30 pl-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-neutral-400 mb-1.5">Mudeyga Xiga</span>
                        <span class="text-sm font-bold text-neutral-700" x-text="h.date"></span>
                    </div>
                </div>

                <div class="flex items-center gap-3 bg-[#528CBE]/5 p-3 rounded-2xl border border-[#528CBE]/10">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm text-[#528CBE]">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black uppercase tracking-widest text-neutral-400">Wakhtiga La Qorsheeyay</span>
                        <span class="text-sm font-black text-[#528CBE]" x-text="h.time"></span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-neutral-50 border-t border-neutral-100 flex justify-end">
                <button @click="isOpen = false"
                    class="px-8 py-2.5 text-xs font-black uppercase tracking-widest text-neutral-500 bg-white border border-neutral-200 rounded-xl hover:bg-neutral-100 transition-all shadow-sm">
                    Xir
                </button>
            </div>
        </div>
    </div>
@endsection