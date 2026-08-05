@extends('admin.admin_master')
@section('page_title', 'CID Dashboard')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Criminal Investigation Department</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $institution->name ?? 'CID' }} &middot; {{ $roleLabel }} view</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="mb-8">
        <div class="flex items-center gap-2.5 mb-5">
            <span class="w-7 h-7 rounded-lg bg-primary-50 flex items-center justify-center">
                <i class="bi bi-graph-up-arrow text-[13px] text-primary"></i>
            </span>
            <h2 class="text-sm font-black uppercase tracking-widest text-neutral-500">Overview</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $statCards = [
                    ['value' => $stats['staffCount'], 'label' => 'CID Staff', 'icon' => 'bi-people-fill', 'color' => '#0A284D'],
                    ['value' => 0, 'label' => 'Active Cases', 'icon' => 'bi-folder2-open', 'color' => '#528CBE'],
                    ['value' => 0, 'label' => 'SLA Breaches', 'icon' => 'bi-exclamation-triangle-fill', 'color' => '#DC2626'],
                    ['value' => 0, 'label' => 'Detainees in Custody', 'icon' => 'bi-shield-lock-fill', 'color' => '#7C3AED'],
                ];
            @endphp
            @foreach($statCards as $card)
                <div class="bg-white rounded-2xl border border-neutral-100 p-6 shadow-[0_1px_2px_rgba(16,24,40,0.04)]">
                    <div class="flex items-start justify-between mb-4">
                        <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-[1.5px] truncate">{{ $card['label'] }}</p>
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:{{ $card['color'] }}14">
                            <i class="bi {{ $card['icon'] }} text-xl" style="color:{{ $card['color'] }}"></i>
                        </div>
                    </div>
                    <h3 class="text-3xl font-black text-neutral-800">{{ $card['value'] }}</h3>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Empty state for case-driven widgets --}}
    <div class="bg-white rounded-2xl border border-neutral-100 p-10 text-center">
        <i class="bi bi-cone-striped text-3xl text-neutral-300"></i>
        <h3 class="mt-3 text-base font-bold text-neutral-700">Investigation Workflow is rolling out</h3>
        <p class="text-sm text-neutral-500 mt-1 max-w-md mx-auto">
            Case tracking, SLA alerts, and detention occupancy widgets will populate here once the
            Investigation Workflow, Case Management, and Detention Center modules are live.
        </p>
    </div>

</div>

@endsection
