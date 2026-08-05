@extends('admin.admin_master')
@section('page_title', 'Go’aanka Xidhitaanka — ' . $case->case_number)
@section('admin_main_content')

@php
    $d = $case->arrestDecision;
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
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Go'aanka Xidhitaanka</span>
        </div>

        <form action="{{ route('attorney-cases.workflow.arrest-decision.arrest-decision.store', $case->ACID) }}" method="POST" class="p-6">
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

            {{-- 1. Case Information (read-only) --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-folder2-open text-primary-400"></i> Xogta Dacwadda
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Lambarka Dacwadda</label>
                        <p class="text-sm font-bold text-neutral-800 px-4 py-2.5 rounded-xl bg-neutral-50">{{ $case->case_number }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Magaca Eedeysanaha</label>
                        <input type="text" name="suspect_name" list="accusedList"
                            value="{{ old('suspect_name', $d->suspect_name ?? '') }}"
                            placeholder="Geli magaca eedeysanaha..."
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                        <datalist id="accusedList">
                            @foreach($case->accused ?? [] as $accused)
                                <option value="{{ $accused->full_name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
            </div>

            {{-- 2. Assessment --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-shield-exclamation text-primary-400"></i> Qiimaynta Halista
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Heerka Degdegga</label>
                        <select name="urgency_level"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro —</option>
                            @foreach($urgencyOptions as $opt)
                                <option value="{{ $opt }}" {{ old('urgency_level', $d->urgency_level ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-neutral-50 border border-neutral-200 cursor-pointer">
                        <input type="checkbox" name="flight_risk" value="1"
                            {{ old('flight_risk', $d->flight_risk ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                        <span class="text-sm font-medium text-neutral-700">Halis Carar Ah</span>
                    </label>
                    <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-neutral-50 border border-neutral-200 cursor-pointer">
                        <input type="checkbox" name="public_safety_risk" value="1"
                            {{ old('public_safety_risk', $d->public_safety_risk ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                        <span class="text-sm font-medium text-neutral-700">Halis Ammaanka Dadweynaha</span>
                    </label>
                </div>
            </div>

            {{-- 3. Decision --}}
            <div class="rounded-2xl border-2 border-primary-200 p-5 mb-5" style="background:rgba(82,140,190,0.03)">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-clipboard-check text-primary-400"></i> Go'aanka
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Nooca Go'aanka <span class="text-danger-500">*</span>
                        </label>
                        <select name="decision" required
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro Go'aanka —</option>
                            @foreach($decisionOptions as $opt)
                                <option value="{{ $opt }}" {{ old('decision', $d->decision ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Taariikhda Go'aanka <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="decision_date" required
                            value="{{ old('decision_date', $d?->decision_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Lambarka Diiwaanka (OB Reference)</label>
                        <input type="text" name="ob_reference" placeholder="tusaale: OB/045/2026"
                            value="{{ old('ob_reference', $d->ob_reference ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Aasaaska Sharciga</label>
                <textarea name="legal_grounds" rows="3" placeholder="Sharax aasaaska sharciga ee xidhitaanka..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all mb-4">{{ old('legal_grounds', $d->legal_grounds ?? '') }}</textarea>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Sababta Go'aanka</label>
                <textarea name="reasoning" rows="3" placeholder="Fadlan sharax sababta go'aankan..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all mb-4">{{ old('reasoning', $d->reasoning ?? '') }}</textarea>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Tallaabada Xigta</label>
                <select name="next_action"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    <option value="">— Dooro —</option>
                    @foreach($nextActionOptions as $opt)
                        <option value="{{ $opt }}" {{ old('next_action', $d->next_action ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
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
