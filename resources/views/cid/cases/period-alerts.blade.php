@extends('admin.admin_master')
@section('page_title', 'Period Alerts')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Period Alerts</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Remand expiry, court appearances, and warrant expiry due within 72 hours</p>
        </div>
    </div>

    <div class="mb-4 p-4 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-700">
        <i class="bi bi-info-circle-fill"></i>
        This is a live, on-demand view &mdash; there is no background notification job yet, so nothing is pushed via
        email/SMS. Refresh this page to see current deadlines.
    </div>

    <div class="space-y-3">
        @forelse($alerts as $alert)
            <div class="bg-white rounded-2xl border p-4 flex items-center justify-between"
                 style="border-color:{{ $alert['overdue'] ? '#FECACA' : '#F3F4F6' }}">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                         style="background:{{ $alert['overdue'] ? '#FEE2E2' : '#FEF3C7' }};color:{{ $alert['overdue'] ? '#DC2626' : '#B45309' }}">
                        <i class="bi {{ $alert['overdue'] ? 'bi-exclamation-octagon-fill' : 'bi-clock-fill' }}"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-neutral-800">{{ $alert['label'] }}</h4>
                        <p class="text-xs text-neutral-500">{{ $alert['case']->case_number ?? '—' }} &mdash;
                            {{ $alert['overdue'] ? 'Overdue since' : 'Due' }} {{ $alert['due_at']->format('Y-m-d') }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('criminal-cases.workflow', $alert['case']->id ?? 0) }}"
                   class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-neutral-100 p-10 text-center text-neutral-400">
                No deadlines due within the next 72 hours.
            </div>
        @endforelse
    </div>

</div>

@endsection
