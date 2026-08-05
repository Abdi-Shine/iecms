@extends('admin.admin_master')
@section('page_title', 'Go’aanka Xidhitaanka — ' . $case->case_number)
@section('admin_main_content')

    @php
        $completedCount = collect($forms)->filter(fn ($f) => $f['complete'])->count();
        $isStepComplete = $completedCount === count($forms);
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
                <a href="{{ route('attorney-cases.workflow', $case->ACID) }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
                    <i class="bi bi-arrow-left"></i> Ku Laabo Workflow-ka
                </a>
            </div>

            <div class="flex items-center gap-6 mt-5 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Tallaabada:</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full text-white bg-primary-400">Go'aanka Xidhitaanka</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Xaalada:</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full text-white {{ $isStepComplete ? 'bg-success-600' : 'bg-neutral-500' }}">
                        {{ $isStepComplete ? 'Dhammaystiran' : 'Sugaya' }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Foomamka:</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full text-white" style="background:#17a2b8">
                        {{ $completedCount }}/{{ count($forms) }} Loo Baahan Yahay
                    </span>
                </div>
            </div>
        </div>

        {{-- Step Information --}}
        <div class="rounded-2xl p-5 mb-6 flex items-start gap-3"
            style="background:rgba(23,162,184,0.08);border:1px solid rgba(23,162,184,0.2)">
            <i class="bi bi-info-circle-fill text-lg" style="color:#17a2b8"></i>
            <div>
                <p class="text-sm font-bold text-neutral-800 mb-1">Faahfaahinta Tallaabada</p>
                <p class="text-sm text-neutral-600">Go'aami haddii xidhitaan loo baahan yahay</p>
                <p class="text-sm text-neutral-600 mt-1"><strong>Foomamka Loo Baahan Yahay:</strong> {{ count($forms) }} foom</p>
            </div>
        </div>

        {{-- Required Forms --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 mb-6">
            <h2 class="text-base font-bold text-neutral-800 mb-4">Foomamka Loo Baahan Yahay ee Go'aanka Xidhitaanka</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($forms as $form)
                    <div class="rounded-xl border border-neutral-200 p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                <i class="bi bi-file-earmark-text text-lg" style="color:#528CBE"></i>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white {{ $form['complete'] ? 'bg-success-600' : 'bg-neutral-800' }}">
                                {{ $form['complete'] ? 'Dhammaystiran' : 'Sugaya' }}
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-neutral-800">{{ $form['title'] }}</h3>
                        <p class="text-xs text-primary-400 font-medium mt-1 mb-4">{{ $form['description'] }}</p>
                        <a href="{{ $form['route'] }}"
                            class="w-full flex items-center justify-center gap-2.5 px-4 py-3.5 text-sm font-bold text-white rounded-2xl transition-all bg-ago hover:opacity-90 shadow-sm">
                            <i class="bi {{ $form['complete'] ? 'bi-pencil-square' : 'bi-play-fill' }}"></i>
                            <span>{{ $form['complete'] ? 'Eeg / Wax Ka Beddel Foomka' : 'Bilow Foomka' }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Generated Letters --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <i class="bi bi-file-earmark-pdf text-lg text-success-600"></i>
                    <div>
                        <p class="text-sm font-bold text-neutral-800">Waraaqaha La Sameeyay</p>
                        <p class="text-xs text-neutral-500">Waraaqaha laga sameeyay foomamka xidhitaanka la gudbiyay</p>
                    </div>
                </div>
                <button type="button" disabled
                    class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl opacity-50 cursor-not-allowed"
                    style="background:#198754" title="Weli lama horumarin">
                    <i class="bi bi-pencil-square"></i> Samee Waraaqo Foomamka La Gudbiyay
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-neutral-50">
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Waraaqda</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Lambarka Waraaqda</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Maxkamadda</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Kii Sameeyay</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-neutral-400">
                                <i class="bi bi-info-circle"></i> Waraaq weli lama samayn.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

@endsection
