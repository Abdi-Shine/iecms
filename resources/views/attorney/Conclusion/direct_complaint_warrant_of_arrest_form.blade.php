@extends('admin.admin_master')
@section('page_title', 'Waaran Xidhitaan — ' . $case->case_number)
@section('admin_main_content')

@php
    $d = $case->warrantOfArrest;
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
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Waaran Xidhitaan</span>
        </div>

        <form action="{{ route('attorney-cases.workflow.arrest-decision.warrant-of-arrest.store', $case->ACID) }}" method="POST"
            enctype="multipart/form-data" class="p-6">
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

            {{-- 1. Suspect & Offence --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-person-badge text-primary-400"></i> Eedeysanaha Iyo Dembiga
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Magaca Eedeysanaha</label>
                        <input type="text" name="suspect_name" list="accusedList"
                            value="{{ old('suspect_name', $d->suspect_name ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                        <datalist id="accusedList">
                            @foreach($case->accused ?? [] as $accused)
                                <option value="{{ $accused->full_name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Cinwaanka Eedeysanaha</label>
                        <input type="text" name="suspect_address"
                            value="{{ old('suspect_address', $d->suspect_address ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Dembiga La Sheegayo</label>
                <textarea name="offence_alleged" rows="2"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all mb-4">{{ old('offence_alleged', $d->offence_alleged ?? '') }}</textarea>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Qodobka Sharciga</label>
                <input type="text" name="legal_provision" placeholder="tusaale: Qodobka 245, KC"
                    value="{{ old('legal_provision', $d->legal_provision ?? '') }}"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all mb-4">
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Soo Koobidda Caddaynta Taageeraysa</label>
                <textarea name="supporting_evidence_summary" rows="3"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('supporting_evidence_summary', $d->supporting_evidence_summary ?? '') }}</textarea>
            </div>

            {{-- 2. Application --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-send text-primary-400"></i> Codsiga
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Taariikhda Codsiga <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="application_date" required
                            value="{{ old('application_date', $d?->application_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Xeer Ilaaliyaha Codsanaya</label>
                        <input type="text" name="applying_prosecutor"
                            value="{{ old('applying_prosecutor', $d->applying_prosecutor ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Magaca Maxkamadda</label>
                        <input type="text" name="court_name"
                            value="{{ old('court_name', $d->court_name ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Magaca Garsooraha</label>
                        <input type="text" name="judge_name"
                            value="{{ old('judge_name', $d->judge_name ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
            </div>

            {{-- 3. Warrant Status --}}
            <div class="rounded-2xl border-2 border-primary-200 p-5 mb-5" style="background:rgba(82,140,190,0.03)">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-file-earmark-check text-primary-400"></i> Xaalada Waaranka
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Xaalada</label>
                        <select name="warrant_status"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                            @foreach($statusOptions as $opt)
                                <option value="{{ $opt }}" {{ old('warrant_status', $d->warrant_status ?? $statusOptions[0]) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Lambarka Waaranka</label>
                        <input type="text" name="warrant_number"
                            value="{{ old('warrant_number', $d->warrant_number ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Lambarka Diiwaanka (OB Reference)</label>
                        <input type="text" name="ob_reference" placeholder="tusaale: OB/045/2026"
                            value="{{ old('ob_reference', $d->ob_reference ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Taariikhda Bixinta</label>
                        <input type="date" name="issue_date"
                            value="{{ old('issue_date', $d?->issue_date?->format('Y-m-d') ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Taariikhda Dhicitaanka</label>
                        <input type="date" name="expiry_date"
                            value="{{ old('expiry_date', $d?->expiry_date?->format('Y-m-d') ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Shuruudo Gaar Ah</label>
                <textarea name="special_conditions" rows="2"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">{{ old('special_conditions', $d->special_conditions ?? '') }}</textarea>
            </div>

            {{-- 4. Supporting Documentation --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-paperclip text-primary-400"></i> Dukumeenti Taageeraya
                </h3>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Nuqulka Waaranka</label>
                <input type="file" name="warrant_document"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-primary-400 file:text-white file:text-xs file:font-bold">
                @if($d->warrant_document_path ?? null)
                    <a href="{{ asset('storage/' . $d->warrant_document_path) }}" target="_blank"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-primary-400 hover:underline mt-1.5">
                        <i class="bi bi-file-earmark-check"></i> Fiiri Faylka La Soo Rarey
                    </a>
                @endif
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
