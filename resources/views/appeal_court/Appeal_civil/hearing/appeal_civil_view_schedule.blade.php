@extends('admin.admin_master')
@section('page_title', 'Hearing Schedule')
@section('admin_main_content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">


<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Hearing Schedule</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Schedule, manage, and track all court hearings</p>
        </div>
        <a href="{{ route('appeal-hearings.create') }}"
           class="btn-primary flex items-center gap-2 px-5 py-2.5 text-sm rounded-xl shadow hover:opacity-90 transition-all">
            <i class="bi bi-plus-lg"></i> Schedule Hearing
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-primary">
                <i class="bi bi-calendar3 text-xl text-primary"></i>
            </div>
            <div><p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Total</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['total'] }}</h3></div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-primary">
                <i class="bi bi-calendar-check text-xl text-primary"></i>
            </div>
            <div><p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Scheduled</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['scheduled'] }}</h3></div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-success">
                <i class="bi bi-check2-circle text-xl text-success"></i>
            </div>
            <div><p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Completed</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['completed'] }}</h3></div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-gold">
                <i class="bi bi-arrow-repeat text-xl text-gold"></i>
            </div>
            <div><p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Postponed</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['postponed'] }}</h3></div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-danger">
                <i class="bi bi-x-circle text-xl text-danger"></i>
            </div>
            <div><p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Cancelled</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['cancelled'] }}</h3></div>
        </div>
    </div>

    {{-- Calendar layout --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-neutral-700">Calendar View</h3>
                {{-- Legend removed --}}
        </div>
        <div id="hearing-calendar"></div>
    </div>

    {{-- All Hearings Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
            <i class="bi bi-table text-sm text-primary"></i>
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">All Hearings</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="table-header-tint">
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">No#</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">File No</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Date & Time</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Duration</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Courtroom</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Purpose</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Status</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($hearings as $i => $h)
                        @php
                            $sc = match($h->status) {
                                'Completed' => ['bg'=>'rgba(16,185,129,0.1)','color'=>'#10B981'],
                                'Cancelled' => ['bg'=>'rgba(239,68,68,0.1)','color'=>'#DC2626'],
                                'Postponed' => ['bg'=>'rgba(240,180,60,0.12)','color'=>'#C07E15'],
                                default     => ['bg'=>'rgba(82,140,190,0.1)','color'=>'#528CBE'],
                            };
                        @endphp
                        <tr class="hover:bg-neutral-50 transition-colors" id="row-{{ $h->id }}">
                            <td class="px-5 py-3.5"><span class="text-xs font-bold text-neutral-400">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span></td>
                            <td class="px-5 py-3.5">
                                <span class="badge-case-type">{{ $h->civilCase->FileNo ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs font-semibold text-neutral-700 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($h->hearing_date)->format('d/m/Y') }}<br>
                                <span class="text-neutral-400 font-normal">{{ \Carbon\Carbon::parse($h->hearing_time)->format('H:i') }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-neutral-500">{{ $h->duration ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-xs text-neutral-600 font-medium">{{ $h->courtroom ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-xs text-neutral-500 max-w-[180px] truncate">{{ $h->hearing_purpose ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                                      style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $sc['color'] }}"></span>
                                    {{ $h->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('appeal-hearings.document', $h->id) }}" title="View Document"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#10B981';this.style.borderColor='#10B981';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                        <i class="bi bi-file-earmark-text text-xs"></i>
                                    </a>
                                    <button onclick="window.location='{{ route('appeal-hearings.edit', $h->id) }}'" title="Edit"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                        <i class="bi bi-pencil text-xs"></i>
                                    </button>
                                    <button onclick="deleteHearing({{ $h->id }}, this)" title="Delete"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#DC2626';this.style.borderColor='#DC2626';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                        <i class="bi bi-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-16 text-center text-neutral-400 text-sm">No hearings scheduled yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@php
$hearingsJson = $hearings->map(function($h) {
    return [
        'id'              => $h->id,
        'civil_case_id'   => $h->civil_case_id,
        'hearing_date'    => $h->hearing_date->format('Y-m-d'),
        'hearing_time'    => $h->hearing_time,
        'duration'        => $h->duration,
        'courtroom'       => $h->courtroom,
        'hearing_purpose' => $h->hearing_purpose,
        'notes'           => $h->notes,
        'status'          => $h->status,
    ];
})->values();
@endphp

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
let hearingId = null;

// Calendar
document.addEventListener('DOMContentLoaded', function () {
    const calEl = document.getElementById('hearing-calendar');
    if (!calEl) return;
    const cal = new FullCalendar.Calendar(calEl, {
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'title', center: '', right: 'today prev,next' },
        height: 480,
        expandRows: true,
        events: @json($calendarEvents),
        eventDisplay: 'block',
        dayMaxEvents: 3,
        displayEventTime: false,
        eventTextColor: '#fff',
        eventBorderColor: 'transparent',
        eventClick: function(info) {
            const id = parseInt(info.event.id);
            window.location = '/appeal-hearings/' + id + '/edit';
        },
    });
    cal.render();
    window.addEventListener('resize', () => cal.updateSize());
});

async function deleteHearing(id, btn) {
    const confirmed = await Swal.fire({
        title: 'Delete Hearing?', text: 'This cannot be undone.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#DC2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete',
    });
    if (!confirmed.isConfirmed) return;
    try {
        const res  = await fetch(`/hearings/${id}`, {
            method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok && data.success) {
            document.getElementById(`row-${id}`)?.remove();
            Swal.fire({ title: 'Deleted!', icon: 'success', timer: 1200, showConfirmButton: false });
            setTimeout(() => window.location.reload(), 1300);
        } else throw new Error(data.message || 'Failed.');
    } catch (e) {
        Swal.fire({ title: 'Error', text: e.message, icon: 'error', confirmButtonColor: '#DC2626' });
    }
}
</script>
@endpush
@endsection
