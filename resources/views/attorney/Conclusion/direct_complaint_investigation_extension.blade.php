@extends('admin.admin_master')
@section('page_title', 'Kordhinta Baaritaanka — ' . $case->case_number)
@section('admin_main_content')

    @php
        $e = $case->investigationExtension;
        $isComplete = (bool) $e;
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
                    <span class="text-xs font-bold px-3 py-1 rounded-full text-white bg-primary-400">Kordhinta Baaritaanka</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Xaalada:</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full text-white {{ $isComplete ? 'bg-success-600' : 'bg-neutral-500' }}">
                        {{ $isComplete ? 'Dhammaystiran' : 'Sugaya' }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Foomamka:</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full text-white" style="background:#17a2b8">1 Loo Baahan Yahay</span>
                </div>
            </div>
        </div>

        {{-- Step Information --}}
        <div class="rounded-2xl p-5 mb-6 flex items-start gap-3"
            style="background:rgba(23,162,184,0.08);border:1px solid rgba(23,162,184,0.2)">
            <i class="bi bi-info-circle-fill text-lg" style="color:#17a2b8"></i>
            <div>
                <p class="text-sm font-bold text-neutral-800 mb-1">Faahfaahinta Tallaabada</p>
                <p class="text-sm text-neutral-600">Codso kordhinta haddii muddada baaritaanka dhacdo</p>
                <p class="text-sm text-neutral-600 mt-1"><strong>Foomamka Loo Baahan Yahay:</strong> 1 foom</p>
            </div>
        </div>

        {{-- Required Forms --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 mb-6">
            <h2 class="text-base font-bold text-neutral-800 mb-4">Foomamka Loo Baahan Yahay ee Kordhinta Baaritaanka</h2>

            <div class="rounded-xl border border-neutral-200 p-5 max-w-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                        <i class="bi bi-file-earmark-text text-lg" style="color:#528CBE"></i>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white {{ $isComplete ? 'bg-success-600' : 'bg-neutral-800' }}">
                        {{ $isComplete ? 'Dhammaystiran' : 'Sugaya' }}
                    </span>
                </div>
                <h3 class="text-sm font-bold text-neutral-800">Kordhinta Baaritaanka</h3>
                <p class="text-xs text-primary-400 font-medium mt-1 mb-4">Codso kordhinta muddada baaritaanka</p>
                <a href="{{ route('attorney-cases.workflow.investigation-extension.form', $case->ACID) }}"
                    class="w-full flex items-center justify-center gap-2.5 px-4 py-3.5 text-sm font-bold text-white rounded-2xl transition-all bg-ago hover:opacity-90 shadow-sm">
                    <i class="bi {{ $isComplete ? 'bi-pencil-square' : 'bi-play-fill' }}"></i>
                    <span>{{ $isComplete ? 'Eeg / Wax Ka Beddel Foomka' : 'Bilow Foomka' }}</span>
                </a>
            </div>
        </div>

        {{-- Form Submission Progress --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-neutral-100">
                <p class="text-sm font-bold text-neutral-800 flex items-center gap-2">
                    <i class="bi bi-list-check text-primary-400"></i> Horumarka Gudbinta Foomamka
                </p>
                <p class="text-xs text-neutral-500 mt-0.5">La soco xaalada codsiga kordhinta</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-neutral-50">
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Foomka</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Gudbinta</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Muddada La Codsanayo</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Sababta Ansixinta</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Kii Ansixiyay</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Ansixinta</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @if($e)
                            @php
                                $statusStyles = [
                                    'Sugaya'       => 'bg-neutral-100 text-neutral-600',
                                    'La Ansixiyay' => 'bg-success-50 text-success-700',
                                    'La Diiday'    => 'bg-danger-50 text-danger-600',
                                ];
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-semibold text-neutral-800">Kordhinta Baaritaanka</td>
                                <td class="px-4 py-3 text-neutral-600">{{ $e->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-4 py-3 text-neutral-600">
                                    {{ $e->extension_period === 'Other' ? ($e->extension_period_other ?: 'Other') : ($e->extension_period ?? '—') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $statusStyles[$e->status] ?? 'bg-neutral-100 text-neutral-600' }}">
                                        {{ $e->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-neutral-500">{{ Str::limit($e->approval_reason ?? '—', 30) }}</td>
                                <td class="px-4 py-3 text-neutral-600">{{ $e->approved_by ?: '—' }}</td>
                                <td class="px-4 py-3 text-neutral-600">{{ $e->approved_date?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if($e->status === 'Sugaya')
                                        <form action="{{ route('attorney-cases.workflow.investigation-extension.approve', $case->ACID) }}" method="POST"
                                            class="flex items-center gap-1 justify-center">
                                            @csrf
                                            <input type="text" name="reason" placeholder="Sababta..."
                                                class="text-xs px-2 py-1 border border-neutral-200 rounded-lg w-24 focus:outline-none focus:border-primary-400">
                                            <button type="submit" name="decision" value="La Ansixiyay"
                                                class="text-xs px-2 py-1 rounded-lg bg-success-600 text-white font-bold hover:opacity-90" title="Ansixi">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button type="submit" name="decision" value="La Diiday"
                                                class="text-xs px-2 py-1 rounded-lg bg-danger-600 text-white font-bold hover:opacity-90" title="Diid">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-neutral-400 flex items-center justify-center gap-1">
                                            <i class="bi bi-check-circle"></i> La Fuliyay
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-neutral-400">
                                    <i class="bi bi-info-circle"></i> Foom weli lama gudbin.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Generated Letters --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <i class="bi bi-file-earmark-pdf text-lg text-success-600"></i>
                    <div>
                        <p class="text-sm font-bold text-neutral-800">Waraaqaha La Sameeyay</p>
                        <p class="text-xs text-neutral-500">Waraaqaha laga sameeyay codsiga kordhinta baaritaanka</p>
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
