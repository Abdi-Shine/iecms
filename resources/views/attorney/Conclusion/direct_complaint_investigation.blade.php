@extends('admin.admin_master')
@section('page_title', 'Baaritaanka — ' . $case->case_number)
@section('admin_main_content')

@php
    $isComplete = (bool) $case->investigation;
    $subtitleParts = array_filter([$case->added_by, optional($case->complainants->first())->full_name]);
@endphp

<div class="p-4 sm:p-6 w-full">

    {{-- Case Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->case_number }}</h1>
                <p class="text-sm text-neutral-500 mt-0.5">{{ implode(' - ', $subtitleParts) ?: '—' }}</p>
            </div>
            <a href="{{ route('attorney-cases.workflow', $case->ACID) }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i> Ku Laabo Workflow-ka
            </a>
        </div>

        <div class="flex items-center gap-6 mt-5 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Tallaabada:</span>
                <span class="text-xs font-bold px-3 py-1 rounded-full text-white bg-primary-400">Baaritaanka</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Xaalada:</span>
                <span
                    class="text-xs font-bold px-3 py-1 rounded-full text-white {{ $isComplete ? 'bg-success-600' : 'bg-neutral-800' }}">
                    {{ $isComplete ? 'Dhammaystiran' : 'Sugaya' }}
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Foomamka:</span>
                <span class="text-xs font-bold px-3 py-1 rounded-full text-white" style="background:#17a2b8">1 Loo
                    Baahan Yahay</span>
            </div>
        </div>
    </div>

    {{-- Step Information --}}
    <div class="rounded-2xl p-5 mb-6 flex items-start gap-3"
        style="background:rgba(23,162,184,0.08);border:1px solid rgba(23,162,184,0.2)">
        <i class="bi bi-info-circle-fill text-lg" style="color:#17a2b8"></i>
        <div>
            <p class="text-sm font-bold text-neutral-800 mb-1">Faahfaahinta Tallaabada</p>
            <p class="text-sm text-neutral-600">AGO conducts investigation or refers to CID</p>
            <p class="text-sm text-neutral-600 mt-1"><strong>Foomamka Loo Baahan Yahay:</strong> 1 foom</p>
        </div>
    </div>

    {{-- Required Forms --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 mb-6">
        <h2 class="text-base font-bold text-neutral-800 mb-4">Foomamka Loo Baahan Yahay ee Baaritaanka</h2>

        <div class="rounded-xl border border-neutral-200 p-5 max-w-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                    style="background:rgba(82,140,190,0.1)">
                    <i class="bi bi-file-earmark-text text-lg" style="color:#528CBE"></i>
                </div>
                <span
                    class="text-xs font-bold px-2.5 py-1 rounded-full text-white {{ $isComplete ? 'bg-success-600' : 'bg-neutral-800' }}">
                    {{ $isComplete ? 'Dhammaystiran' : 'Sugaya' }}
                </span>
            </div>
            <h3 class="text-sm font-bold text-neutral-800">Bilowga Baaritaanka</h3>
            <p class="text-xs text-primary-400 font-medium mt-1 mb-4">Bilow hawsha baaritaanka rasmiga ah</p>
            <a href="{{ route('attorney-cases.workflow.investigation.form', $case->ACID) }}"
                class="w-full flex items-center justify-center gap-2.5 px-4 py-3.5 text-sm font-bold text-white rounded-2xl transition-all bg-ago hover:opacity-90 shadow-sm">
                <i class="bi {{ $isComplete ? 'bi-pencil-square' : 'bi-play-fill' }}"></i>
                <span>{{ $isComplete ? 'Eeg / Wax Ka Beddel Foomka' : 'Bilow Foomka' }}</span>
            </a>
        </div>
    </div>

    {{-- Investigation Updates --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <i class="bi bi-clock-history text-lg text-primary-400"></i>
                <div>
                    <p class="text-sm font-bold text-neutral-800">Isbeddelada Baaritaanka</p>
                    <p class="text-xs text-neutral-500">Diiwaanka horumarka baaritaanka ee dacwaddan</p>
                </div>
            </div>
        </div>

        @if($isComplete)
            <form action="{{ route('attorney-cases.workflow.investigation.updates.store', $case->ACID) }}" method="POST"
                class="px-6 py-4 border-b border-neutral-100 flex items-start gap-3">
                @csrf
                <div class="flex-1">
                    <textarea name="note" rows="2" required placeholder="Ku dar isbeddel cusub oo ku saabsan baaritaanka..."
                        class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('note') }}</textarea>
                    @error('note')<p class="text-danger-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                    class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl transition-all hover:opacity-90 bg-ago">
                    <i class="bi bi-plus-lg"></i> Ku Dar
                </button>
            </form>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-neutral-50">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faallada</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Kii Qoray</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse(($case->investigation?->updates ?? collect()) as $update)
                        <tr>
                            <td class="px-4 py-3 text-neutral-600 whitespace-nowrap">{{ $update->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-neutral-700">{{ $update->note }}</td>
                            <td class="px-4 py-3 text-neutral-600">{{ $update->added_by ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-neutral-400">
                                <i class="bi bi-info-circle"></i>
                                {{ $isComplete ? 'Weli isbeddel lama darin.' : 'Marka hore bilow baaritaanka si aad u darto isbeddelo.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
