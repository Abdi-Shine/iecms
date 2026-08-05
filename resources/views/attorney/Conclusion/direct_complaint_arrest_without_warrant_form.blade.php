@extends('admin.admin_master')
@section('page_title', 'Xidhitaan Aan Waaran Lahayn — ' . $case->case_number)
@section('admin_main_content')

@php
    $d = $case->arrestWithoutWarrant;
    $isComplete = (bool) $d;
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
            <a href="{{ route('attorney-cases.workflow.arrest-decision', $case->ACID) }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i> Ku Laabo
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2"
            style="background:{{ $isComplete ? 'rgba(240,180,60,0.06)' : 'rgba(82,140,190,0.06)' }}">
            <i class="bi bi-{{ $isComplete ? 'pencil-square' : 'file-earmark-plus' }} text-sm"
                style="color:{{ $isComplete ? '#F0B43C' : '#528CBE' }}"></i>
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Xidhitaan Aan Waaran Lahayn</span>
        </div>

        <form action="{{ route('attorney-cases.workflow.arrest-decision.arrest-without-warrant.store', $case->ACID) }}" method="POST" class="p-6">
            @csrf

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-white bg-danger-600">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. Arrest Details --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-person-lock text-primary-400"></i> Faahfaahinta Xidhitaanka
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Magaca Eedeysanaha</label>
                        <input type="text" name="suspect_name" list="accusedList"
                            value="{{ old('suspect_name', $d->suspect_name ?? '') }}"
                            placeholder="Geli magaca eedeysanaha..."
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                        <datalist id="accusedList">
                            @foreach($case->accused ?? [] as $accused)
                                <option value="{{ $accused->full_name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Goobta Xidhitaanka</label>
                        <input type="text" name="arrest_location" placeholder="Goobta xidhitaanka..."
                            value="{{ old('arrest_location', $d->arrest_location ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Taariikhda Xidhitaanka <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="arrest_date" required
                            value="{{ old('arrest_date', $d?->arrest_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Wakhtiga Xidhitaanka</label>
                        <input type="time" name="arrest_time"
                            value="{{ old('arrest_time', $d->arrest_time ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Sababta Xidhitaanka Aan Waaran Lahayn</label>
                <select name="grounds_for_warrantless_arrest"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all mb-4">
                    <option value="">— Dooro —</option>
                    @foreach($groundsOptions as $opt)
                        <option value="{{ $opt }}" {{ old('grounds_for_warrantless_arrest', $d->grounds_for_warrantless_arrest ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Xaaladaha</label>
                <textarea name="circumstances" rows="3" placeholder="Sharax xaaladaha xidhitaanka..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('circumstances', $d->circumstances ?? '') }}</textarea>
            </div>

            {{-- 2. Officer & Conduct --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-shield-check text-primary-400"></i> Sarkaalka Iyo Habdhaqanka
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Magaca Sarkaalka Xidhay</label>
                        <input type="text" name="arresting_officer_name"
                            value="{{ old('arresting_officer_name', $d->arresting_officer_name ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Darajada Sarkaalka</label>
                        <input type="text" name="arresting_officer_rank"
                            value="{{ old('arresting_officer_rank', $d->arresting_officer_rank ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Markhaatiyaasha Joogay</label>
                        <input type="text" name="witnesses_present"
                            value="{{ old('witnesses_present', $d->witnesses_present ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Lambarka Diiwaanka (OB Reference)</label>
                        <input type="text" name="ob_reference" placeholder="tusaale: OB/045/2026"
                            value="{{ old('ob_reference', $d->ob_reference ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
                <div class="flex flex-wrap gap-4 mb-4">
                    <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-neutral-50 border border-neutral-200 cursor-pointer">
                        <input type="checkbox" name="force_used" value="1"
                            {{ old('force_used', $d->force_used ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                        <span class="text-sm font-medium text-neutral-700">Xoog Ayaa La Isticmaalay</span>
                    </label>
                    <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-neutral-50 border border-neutral-200 cursor-pointer">
                        <input type="checkbox" name="rights_informed" value="1"
                            {{ old('rights_informed', $d->rights_informed ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                        <span class="text-sm font-medium text-neutral-700">Xuquuqda Waa Loo Sheegay Eedeysanaha</span>
                    </label>
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Faahfaahinta Xoogga La Isticmaalay</label>
                <textarea name="force_description" rows="2" placeholder="Haddii xoog la isticmaalay, sharax..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('force_description', $d->force_description ?? '') }}</textarea>
            </div>

            {{-- 3. Reporting --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-file-earmark-text text-primary-400"></i> Warbixinta
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Sarkaalka Warbixiyay</label>
                        <input type="text" name="reporting_officer"
                            value="{{ old('reporting_officer', $d->reporting_officer ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Taariikhda Warbixinta</label>
                        <input type="date" name="report_date"
                            value="{{ old('report_date', $d?->report_date?->format('Y-m-d') ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-neutral-100">
                <a href="{{ route('attorney-cases.workflow.arrest-decision', $case->ACID) }}"
                    class="px-6 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200 rounded-xl bg-white hover:bg-neutral-50 transition-all">
                    Jooji
                </a>
                <button type="submit"
                    class="flex items-center gap-2 px-8 py-2.5 text-white text-sm font-bold rounded-xl shadow hover:opacity-90 transition-all bg-ago">
                    <i class="bi bi-cloud-check-fill"></i>
                    <span>Keydi</span>
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
