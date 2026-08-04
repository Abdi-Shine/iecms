@extends('admin.admin_master')
@section('page_title', $case->case_number . ' — Direct Complaint')
@section('admin_main_content')

@php
    $statusColors = [
        'Diiwaan'            => 'bg-info-50 text-info-600',
        'Gal Ku Qoris'       => 'bg-teal-100 text-teal-600',
        'Baaritaan Socda'    => 'bg-warning-100 text-warning-600',
        'Hubin'              => 'bg-violet-100 text-violet-600',
        'La Eedeeyay'        => 'bg-violet-100 text-violet-600',
        'Ku Jira Maxkamadda' => 'bg-info-100 text-info-700',
        'La Xukumay'         => 'bg-success-100 text-success-600',
        'La Sii Daayay'      => 'bg-neutral-100 text-neutral-600',
        'La Diiday'          => 'bg-red-100 text-red-600',
        'La Xiray'           => 'bg-neutral-100 text-neutral-600',
    ];
    $sc = $statusColors[$case->status] ?? 'bg-neutral-100 text-neutral-600';

    $statusLabels = [];

    $priorityColors = [
        'Hoose'       => 'bg-success-100 text-success-600',
        'Dhexdhexaad' => 'bg-warning-100 text-warning-600',
        'Sarreeya'    => 'bg-red-100 text-red-600',
        'Degdeg ah'   => 'bg-red-100 text-red-800',
    ];
    $pc = $priorityColors[$case->priority] ?? 'bg-neutral-100 text-neutral-600';

    $priorityLabels = [];

    $partyRoleColors = [
        'Eedeysane' => 'bg-red-100 text-red-600',
        'Dhibbane'  => 'bg-info-50 text-info-600',
        'Markhaati' => 'bg-warning-100 text-warning-600',
    ];

    $caseTypeColors = [
        'Shaqsi'    => 'bg-info-50 text-primary-400',
        'Shirkad'   => 'bg-violet-100 text-violet-600',
        "Hay'ad"    => 'bg-warning-100 text-gold-600',
        'Dawladeed' => 'bg-primary-50 text-primary-900',
        'Qaraan'    => 'bg-success-100 text-success-600',
    ];
    $ctc = $caseTypeColors[$case->case_type] ?? 'bg-neutral-100 text-neutral-600';

    $complainantTypeLabels = ['Lawyer' => 'Qareen', 'Individual' => 'Shakhsi', 'Government Agency' => "Hay'ad Dawladeed", 'Company' => 'Shirkad'];
@endphp

<div class="p-4 sm:p-6 w-full">

    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white bg-success-600">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-[1.5px] text-neutral-400 mb-1.5">Dacwad Toos ah</p>
            <div class="flex items-center gap-3 mb-1 flex-wrap">
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->title }}</h1>
            </div>
            <p class="text-sm text-neutral-500 font-mono">{{ $case->case_number }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('attorney-cases.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- ══ ALL SECTIONS WRAPPER ══ --}}
    <div class="rounded-2xl shadow-sm border border-neutral-100 overflow-hidden divide-y divide-neutral-100">

        {{-- ═══ XOGTA GUUD ═══ --}}
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                <i class="bi bi-info-circle-fill text-sm text-primary-400"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Xogta Guud</span>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-x-4 gap-y-5 lg:divide-x lg:divide-neutral-100">
                    <div class="lg:pl-4 lg:first:pl-0">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Lambarka</dt>
                        <dd class="text-sm font-bold text-neutral-800 font-mono">{{ $case->case_number }}</dd>
                    </div>
                    <div class="lg:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Dembiga</dt>
                        <dd class="text-sm font-bold text-neutral-800">{{ $case->offense_type }}</dd>
                    </div>
                    <div class="lg:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Nooca Dacwada</dt>
                        <dd>
                            @if($case->case_type)
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $ctc }}">{{ $case->case_type }}</span>
                            @else
                                <span class="text-sm font-bold text-neutral-800">—</span>
                            @endif
                        </dd>
                    </div>
                    <div class="lg:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Mudnaanta</dt>
                        <dd><span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $pc }}">{{ $priorityLabels[$case->priority] ?? $case->priority }}</span></dd>
                    </div>
                    <div class="lg:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Goobta Dhacdada</dt>
                        <dd class="text-sm font-bold text-neutral-800">{{ $case->incident_location ?? '—' }}</dd>
                    </div>
                    <div class="lg:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Taariikhda Dhacdada</dt>
                        <dd class="text-sm font-bold text-neutral-800">
                            {{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('d/m/Y') : '—' }}
                            @if($case->incident_time)
                                <span class="text-neutral-400 font-normal">{{ $case->incident_time }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="lg:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Taariikhda La Diiwaan Geliyay</dt>
                        <dd class="text-sm font-bold text-neutral-800">{{ \Carbon\Carbon::parse($case->date_reported)->format('d/m/Y') }}</dd>
                    </div>
                    <div class="lg:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">La Diiwaan Geliyay</dt>
                        <dd class="text-sm font-bold text-neutral-800">{{ $case->added_by ?? '—' }}</dd>
                    </div>
                    <div class="lg:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Cusbooneysiin</dt>
                        <dd class="text-sm font-bold text-neutral-800">{{ $case->updated_at->diffForHumans() }}</dd>
                    </div>
                    <div class="lg:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Xaalada</dt>
                        <dd><span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $sc }}">{{ $statusLabels[$case->status] ?? $case->status }}</span></dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- ═══ CABASHADAHA ═══ --}}
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-person-lines-fill text-sm text-primary-400"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Cabashadaha</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">{{ $case->complainants->count() }} {{ Str::plural('codsade', $case->complainants->count()) }}</span>
            </div>

            @if($case->complainants->isEmpty())
                <div class="px-6 py-4">
                    <div class="flex flex-col items-center justify-center gap-2 py-8 px-4 border border-dashed border-neutral-200 rounded-xl bg-neutral-50 text-center">
                        <i class="bi bi-person-lines-fill text-2xl text-neutral-300"></i>
                        <p class="text-sm text-neutral-400">Wali lama darin codsade.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-primary-900/5">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xilka/Shaqada</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Cinwaanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">La Abuuray</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($case->complainants as $i => $complainant)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-3 text-xs font-bold text-neutral-400">{{ sprintf('%02d', $i + 1) }}</td>
                                    <td class="px-6 py-3">
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-primary-50 text-primary whitespace-nowrap">{{ $complainantTypeLabels[$complainant->type] ?? $complainant->type }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <p class="font-semibold text-neutral-800">{{ $complainant->representative_name ?: ($complainant->full_name ?: '—') }}</p>
                                        @if($complainant->representative_name && $complainant->full_name)
                                            <p class="text-xs text-neutral-400 mt-0.5">{{ $complainant->full_name }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $complainant->phone_number ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $complainant->representative_title ?: ($complainant->occupation ?: '—') }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $complainant->address ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-500 text-xs">{{ $complainant->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ═══ DHIBBANAYAASHA ═══ --}}
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-person-heart text-sm text-primary-400"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhibbanayaasha</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">{{ $case->victims->count() }} {{ Str::plural('dhibbane', $case->victims->count()) }}</span>
            </div>

            @if($case->victims->isEmpty())
                <div class="px-6 py-4">
                    <div class="flex flex-col items-center justify-center gap-2 py-8 px-4 border border-dashed border-neutral-200 rounded-xl bg-neutral-50 text-center">
                        <i class="bi bi-person-heart text-2xl text-neutral-300"></i>
                        <p class="text-sm text-neutral-400">Wali lama darin dhibbane.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-primary-900/5">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Jinsiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Cinwaanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tar-Dhalashada</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Degaanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Aqoonsiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xiriirka Eedeysanaha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($case->victims as $i => $victim)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-3 text-xs font-bold text-neutral-400">{{ sprintf('%02d', $i + 1) }}</td>
                                    <td class="px-6 py-3">
                                        <p class="font-semibold text-neutral-800">{{ $victim->full_name ?: '—' }}</p>
                                        @if($victim->mother_name)
                                            <p class="text-xs text-neutral-400 mt-0.5">M/N: {{ $victim->mother_name }}</p>
                                        @endif
                                        @if($victim->custodian_full_name)
                                            <p class="text-xs text-neutral-400 mt-0.5">Masuul: {{ $victim->custodian_full_name }}{{ $victim->custodian_relationship ? ' — '.$victim->custodian_relationship : '' }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $victim->gender ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $victim->phone_number ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $victim->address ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $victim->date_of_birth ? $victim->date_of_birth->format('d/m/Y') : '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ trim(collect([$victim->district, $victim->region])->filter()->implode(', ')) ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $victim->id_number ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $victim->relationship_to_accused ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ═══ EEDEYSANAYAASHA ═══ --}}
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-person-exclamation text-sm text-primary-400"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Eedeysanayaasha</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">{{ $case->accused->count() }} {{ Str::plural('eedeysane', $case->accused->count()) }}</span>
            </div>

            @if($case->accused->isEmpty())
                <div class="px-6 py-4">
                    <div class="flex flex-col items-center justify-center gap-2 py-8 px-4 border border-dashed border-neutral-200 rounded-xl bg-neutral-50 text-center">
                        <i class="bi bi-person-exclamation text-2xl text-neutral-300"></i>
                        <p class="text-sm text-neutral-400">Wali lama darin eedeysane.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-primary-900/5">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Jinsiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Cinwaanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tar-Dhalashada</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Degaanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Aqoonsiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faahfaahin Dheeraad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($case->accused as $i => $accused)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-3 text-xs font-bold text-neutral-400">{{ sprintf('%02d', $i + 1) }}</td>
                                    <td class="px-6 py-3">
                                        <p class="font-semibold text-neutral-800">{{ $accused->full_name ?: '—' }}</p>
                                        @if($accused->mother_name)
                                            <p class="text-xs text-neutral-400 mt-0.5">M/N: {{ $accused->mother_name }}</p>
                                        @endif
                                        @if($accused->alias)
                                            <p class="text-xs text-neutral-400 mt-0.5">Naaneys: {{ $accused->alias }}</p>
                                        @endif
                                        @if($accused->custodian_full_name)
                                            <p class="text-xs text-neutral-400 mt-0.5">Masuul: {{ $accused->custodian_full_name }}{{ $accused->custodian_relationship ? ' — '.$accused->custodian_relationship : '' }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $accused->gender ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $accused->phone_number ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $accused->address ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $accused->date_of_birth ? $accused->date_of_birth->format('d/m/Y') : '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ trim(collect([$accused->district, $accused->region])->filter()->implode(', ')) ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $accused->id_number ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $accused->additional_details ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ═══ DHACDADA ═══ --}}
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                <i class="bi bi-geo-alt-fill text-sm text-primary-400"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhacdada</span>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-4 gap-y-5 sm:divide-x sm:divide-neutral-100">
                    <div class="sm:pl-4 sm:first:pl-0">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Goobta Dhacdada</dt>
                        <dd class="text-sm font-bold text-neutral-800">{{ $case->incident_location ?: '—' }}</dd>
                    </div>
                    <div class="sm:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Taariikhda Dhacdada</dt>
                        <dd class="text-sm font-bold text-neutral-800">{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('d/m/Y') : '—' }}</dd>
                    </div>
                    <div class="sm:pl-4">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1">Saacadda Dhacdada</dt>
                        <dd class="text-sm font-bold text-neutral-800">{{ $case->incident_time ?: '—' }}</dd>
                    </div>
                </dl>
                <div class="mt-5 pt-5 border-t border-neutral-100">
                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1.5">Sharaxaada Dhacdada</dt>
                    <dd class="text-sm text-neutral-600 leading-relaxed">{{ $case->summary ?: '—' }}</dd>
                </div>
            </div>
        </div>

        {{-- ═══ DEMBIGA ═══ --}}
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-journal-text text-sm text-primary-400"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dembiga</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">{{ $case->legalProvisions->count() }} {{ Str::plural('qodob', $case->legalProvisions->count()) }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-primary-900/5">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Dembiga</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Sharciga La Xadgudbay</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Mudnaanta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-3 text-xs font-bold text-neutral-400">01</td>
                            <td class="px-6 py-3 font-semibold text-neutral-800">{{ $case->offense_type ?: '—' }}</td>
                            <td class="px-6 py-3 text-neutral-600">
                                @forelse($case->legalProvisions as $provision)
                                    <p>{{ $provision->provision }}</p>
                                @empty
                                    —
                                @endforelse
                            </td>
                            <td class="px-6 py-3"><span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $pc }}">{{ $priorityLabels[$case->priority] ?? $case->priority }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ═══ CADDAYNTA ═══ --}}
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-folder2-open text-sm text-primary-400"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Caddaynta</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">{{ $case->evidenceItems->count() }} {{ Str::plural('caddayn', $case->evidenceItems->count()) }}</span>
            </div>

            @if($case->evidenceItems->isEmpty())
                <div class="px-6 py-4">
                    <div class="flex flex-col items-center justify-center gap-2 py-8 px-4 border border-dashed border-neutral-200 rounded-xl bg-neutral-50 text-center">
                        <i class="bi bi-folder2-open text-2xl text-neutral-300"></i>
                        <p class="text-sm text-neutral-400">Wali lama darin caddayn.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-primary-900/5">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Sharaxaad</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faylka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda La Ururiyay</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($case->evidenceItems as $i => $evidence)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-3 text-xs font-bold text-neutral-400">{{ sprintf('%02d', $i + 1) }}</td>
                                    <td class="px-6 py-3 font-semibold text-neutral-800">{{ $evidence->evidence_type ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $evidence->description ?: '—' }}</td>
                                    <td class="px-6 py-3">
                                        @if($evidence->file_path)
                                            <a href="{{ asset('storage/'.$evidence->file_path) }}" target="_blank" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                                                <i class="bi bi-paperclip"></i> Fayl
                                            </a>
                                        @else
                                            <span class="text-xs text-neutral-400 italic">Ma jiro</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $evidence->collected_date ? $evidence->collected_date->format('d/m/Y') : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ═══ MARKHAATIYAASHA ═══ --}}
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-mic text-sm text-primary-400"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Markhaatiyaasha</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">{{ $case->witnesses->count() }} {{ Str::plural('markhaati', $case->witnesses->count()) }}</span>
            </div>

            @if($case->witnesses->isEmpty())
                <div class="px-6 py-4">
                    <div class="flex flex-col items-center justify-center gap-2 py-8 px-4 border border-dashed border-neutral-200 rounded-xl bg-neutral-50 text-center">
                        <i class="bi bi-mic text-2xl text-neutral-300"></i>
                        <p class="text-sm text-neutral-400">Wali lama darin markhaati.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-primary-900/5">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Jinsiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Cinwaanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tar-Dhalashada</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Degaanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Aqoonsiga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($case->witnesses as $i => $witness)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-3 text-xs font-bold text-neutral-400">{{ sprintf('%02d', $i + 1) }}</td>
                                    <td class="px-6 py-3">
                                        <p class="font-semibold text-neutral-800">{{ $witness->full_name ?: '—' }}</p>
                                        @if($witness->mother_name)
                                            <p class="text-xs text-neutral-400 mt-0.5">M/N: {{ $witness->mother_name }}</p>
                                        @endif
                                        @if($witness->statement)
                                            <p class="text-xs text-neutral-400 mt-0.5">Bayaan: {{ Str::limit($witness->statement, 80) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $witness->gender ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $witness->phone_number ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $witness->address ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $witness->date_of_birth ? \Carbon\Carbon::parse($witness->date_of_birth)->format('d/m/Y') : '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ trim(collect([$witness->district, $witness->region])->filter()->implode(', ')) ?: '—' }}</td>
                                    <td class="px-6 py-3 text-neutral-600">{{ $witness->id_number ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ═══ CODSIGA ═══ --}}
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                <i class="bi bi-file-earmark-text text-sm text-primary-400"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Codsiga</span>
            </div>
            <div class="px-6 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-neutral-400 mb-1.5">Waa Maxay Ficilka Ama Xalka La Codsanayo?</p>
                <p class="text-sm text-neutral-600 leading-relaxed">{{ $case->relief_sought ?: '—' }}</p>
            </div>
        </div>

        {{-- ═══ QIRASHADA ═══ --}}
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                <i class="bi bi-patch-check-fill text-sm text-primary-400"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhamaad</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-primary-900/5">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca iyo Jagada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Saxeexa Sarkaalka Sharciga</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-3 text-xs font-bold text-neutral-400">01</td>
                            <td class="px-6 py-3 font-semibold text-neutral-800">{{ $case->declaration_signed_by ?: '—' }}</td>
                            <td class="px-6 py-3">
                                @if($case->declaration_signature)
                                    <a href="{{ asset('storage/'.$case->declaration_signature) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$case->declaration_signature) }}" alt="Saxeexa" class="h-10 w-auto object-contain border border-neutral-200 rounded-lg bg-neutral-50 p-1">
                                    </a>
                                @else
                                    <span class="text-xs text-neutral-400 italic">Ma jiro</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-neutral-600">{{ $case->declaration_date ? \Carbon\Carbon::parse($case->declaration_date)->format('d/m/Y') : '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /ALL SECTIONS WRAPPER --}}

    {{-- ═══ HUBINTA CABASHADA (separate card) ═══ --}}
    <div class="mt-6 rounded-2xl shadow-sm border border-neutral-100 overflow-hidden bg-white">
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-clipboard2-check text-sm text-primary-400"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Hubinta Cabashada</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">{{ $case->reviews->count() }} {{ Str::plural('hubin', $case->reviews->count()) }}</span>
        </div>

        @if($case->reviews->isEmpty())
            <div class="px-6 py-4">
                <div class="flex flex-col items-center justify-center gap-2 py-8 px-4 border border-dashed border-neutral-200 rounded-xl bg-neutral-50 text-center">
                    <i class="bi bi-clipboard2-check text-2xl text-neutral-300"></i>
                    <p class="text-sm text-neutral-400">Dacwaddan weli lama hubin.</p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-primary-900/5">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Buuxa</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Waaxda La Qoondeeyay</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faallada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($case->reviews as $i => $r)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-3 text-xs font-bold text-neutral-400">{{ sprintf('%02d', $i + 1) }}</td>
                                <td class="px-6 py-3 font-semibold text-neutral-800">{{ $r->user->name ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full whitespace-nowrap bg-violet-600/10 text-violet-600">{{ $r->department->name ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-3 text-neutral-600">{{ $r->comment ?: '—' }}</td>
                                <td class="px-6 py-3 text-neutral-600">{{ $r->review_date ? $r->review_date->format('d/m/Y') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ═══ GAL KU QORIS XEERILAALINTA (separate card) ═══ --}}
    <div class="mt-6 rounded-2xl shadow-sm border border-neutral-100 overflow-hidden bg-white">
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-person-check-fill text-sm text-primary-400"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Gal Ku Qoris Xeerilaalinta</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">{{ $case->prosecutorAssignments->count() }} {{ Str::plural('xilsaaris', $case->prosecutorAssignments->count()) }}</span>
        </div>

        @if($case->prosecutorAssignments->isEmpty())
            <div class="px-6 py-4">
                <div class="flex flex-col items-center justify-center gap-2 py-8 px-4 border border-dashed border-neutral-200 rounded-xl bg-neutral-50 text-center">
                    <i class="bi bi-person-check-fill text-2xl text-neutral-300"></i>
                    <p class="text-sm text-neutral-400">Dacwaddan weli lama xilsaarin xeer ilaaliye.</p>
                </div>
            </div>
        @else
            @php
                $prosecutorRoleLabels = ['Lead' => 'Hogaamiye', 'Assistant' => 'Kaaliye', 'Support' => 'Taageere'];
            @endphp
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-primary-900/5">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Buuxa</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Jagada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xilka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($case->prosecutorAssignments as $i => $a)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-3 text-xs font-bold text-neutral-400">{{ sprintf('%02d', $i + 1) }}</td>
                                <td class="px-6 py-3 font-semibold text-neutral-800">{{ $a->prosecutor->EmpName ?? '—' }}</td>
                                <td class="px-6 py-3 text-neutral-600">{{ $a->prosecutor->Position ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full whitespace-nowrap bg-violet-600/10 text-violet-600">{{ $prosecutorRoleLabels[$a->role] ?? $a->role }}</span>
                                </td>
                                <td class="px-6 py-3 text-neutral-600">{{ $a->assigned_date ? $a->assigned_date->format('d/m/Y') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
