@extends('admin.admin_master')
@section('page_title', 'Case Workflow — ' . $case->case_number)
@section('admin_main_content')

@php
    $progressPct = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
    $subtitleParts = array_filter([$case->added_by, optional($case->complainants->first())->full_name]);
@endphp

<div class="p-4 sm:p-6 w-full">

    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white bg-success-600">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Case Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->case_number }}</h1>
                <p class="text-sm text-neutral-500 mt-0.5">{{ implode(' - ', $subtitleParts) ?: '—' }}</p>
            </div>
            <form action="{{ route('attorney-cases.workflow.send-to-court', $case->ACID) }}" method="POST"
                onsubmit="return confirm('Ma hubtaa inaad dacwaddan u dirto Maxkamadda?');">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow hover:opacity-90 transition-all bg-ago">
                    <i class="bi bi-send-fill"></i> U Dir Maxkamadda
                </button>
            </form>
        </div>

        <div class="flex items-center justify-between gap-6 mt-5 flex-wrap">
            <div class="flex items-center gap-3 flex-1">
                <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide whitespace-nowrap">Horumarka:</span>
                <div class="flex-1 h-2 rounded-full bg-neutral-100 overflow-hidden">
                    <div class="h-full rounded-full bg-primary-400" style="width:{{ $progressPct }}%"></div>
                </div>
                <span class="text-xs font-bold text-neutral-700 whitespace-nowrap">{{ $completedSteps }}/{{ $totalSteps }} ({{ $progressPct }}%)</span>
            </div>

            @if($currentStep)
                <span class="text-xs font-bold px-3 py-1 rounded-full text-neutral-900 whitespace-nowrap" style="background:#F0B43C">
                    Tallaabada Hadda: {{ $currentStep['position'] }}/{{ $totalSteps }} - {{ $currentStep['title'] }}
                </span>
            @endif
        </div>
    </div>

    {{-- Mandatory Forms On File --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
            <i class="bi bi-person-check text-sm text-primary-400"></i>
            <span class="text-sm font-bold text-neutral-800">Foomamka Waajibka ah ee Diiwaanka (dacwaddan)</span>
        </div>
        <div class="p-6">
            <p class="text-xs text-neutral-500 mb-5">
                Akhris-kaliya: dhammaan foomamka la saxeexay ee
                @foreach($complianceByType as $type => $data)
                    {{ $data['meta']['label'] }} ({{ $data['meta']['code'] }}){{ !$loop->last ? ' iyo' : '' }}
                @endforeach
                ee la xiriira dacwaddan (dhammaan shaqaalaha).
            </p>

            <div class="space-y-6">
                @foreach($complianceByType as $type => $data)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <a href="{{ route('attorney-cases.compliance.create', ['id' => $case->ACID, 'type' => $type]) }}"
                                class="text-sm font-bold text-primary-400 hover:underline flex items-center gap-1.5">
                                <i class="bi bi-{{ $type === 'non_disclosure' ? 'file-earmark-lock2' : 'balance-scale' }}"></i>
                                {{ $data['meta']['label'] }} ({{ $data['meta']['code'] }})
                            </a>
                        </div>

                        @if($data['records']->isEmpty())
                            <p class="text-sm text-neutral-400">Dacwaddan diiwaan lagama helin.</p>
                        @else
                            <div class="overflow-x-auto border border-neutral-100 rounded-xl">
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="bg-neutral-50">
                                            <th class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-neutral-500">Shaqaale</th>
                                            <th class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Saxeexa</th>
                                            <th class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-neutral-500">Faahfaahin</th>
                                            <th class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-neutral-500">Saxeexa</th>
                                            <th class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-neutral-500">Warqad</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-100">
                                        @foreach($data['records'] as $record)
                                            <tr>
                                                <td class="px-4 py-2 font-semibold text-neutral-800">{{ $record->employee->EmpName ?? '—' }}</td>
                                                <td class="px-4 py-2 text-neutral-600">{{ $record->signed_date->format('d/m/Y') }}</td>
                                                <td class="px-4 py-2 text-neutral-500">{{ Str::limit($record->notes ?? '—', 40) }}</td>
                                                <td class="px-4 py-2">
                                                    <a href="{{ asset($record->signature) }}" target="_blank">
                                                        <img src="{{ asset($record->signature) }}" alt="Saxeexa" class="h-8 w-auto object-contain border border-neutral-200 rounded bg-neutral-50 p-0.5">
                                                    </a>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <a href="{{ route('attorney-cases.compliance.letter', $record->id) }}" target="_blank"
                                                        class="inline-flex items-center gap-1 text-xs font-bold text-primary-400 hover:underline">
                                                        <i class="bi bi-file-earmark-text"></i> Fiiri
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Workflow Steps --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-bold text-neutral-800">Tallaabooyinka Hawsha</h2>
            <span class="text-sm text-neutral-400">Dhammaystir tallaabooyinka si kasta</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($steps as $step)
                @if($step['enabled'])
                    <a href="{{ $step['route'] }}"
                        class="block text-left rounded-xl border border-neutral-200 p-4 hover:border-primary-400 hover:shadow-sm transition-all bg-neutral-50">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="text-sm font-bold text-neutral-800">{{ $step['title'] }}</h3>
                            @if($step['complete'] ?? false)
                                <i class="bi bi-check-circle-fill text-success-600"></i>
                            @endif
                        </div>
                        <p class="text-xs text-neutral-500 mt-1">{{ $step['description'] }}</p>
                        <span class="text-xs font-semibold text-primary-400 mt-2 inline-block">
                            {{ $step['formsCount'] }} Foom Loo Baahan Yahay
                        </span>
                    </a>
                @else
                    <div class="rounded-xl border border-neutral-200 p-4 bg-neutral-50 {{ isset($step['note']) ? 'opacity-60' : '' }}">
                        <h3 class="text-sm font-bold {{ isset($step['note']) ? 'text-neutral-400' : 'text-neutral-800' }}">{{ $step['title'] }}</h3>
                        <p class="text-xs {{ isset($step['note']) ? 'text-neutral-400' : 'text-neutral-500' }} mt-1">{{ $step['description'] }}</p>
                        @if(isset($step['note']))
                            <span class="text-xs text-neutral-400 mt-2 block">{{ $step['formsCount'] }} Foom Loo Baahan Yahay</span>
                            <span class="text-xs text-primary-300 mt-1 inline-flex items-center gap-1">
                                <i class="bi bi-info-circle"></i> {{ $step['note'] }}
                            </span>
                        @else
                            <span class="text-xs font-semibold text-neutral-400 mt-2 inline-block">
                                {{ $step['formsCount'] }} Foom Loo Baahan Yahay
                            </span>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>

</div>
@endsection
