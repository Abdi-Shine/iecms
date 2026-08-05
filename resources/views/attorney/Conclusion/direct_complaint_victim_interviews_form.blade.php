@extends('admin.admin_master')
@section('page_title', 'Wareysiga Dhibbanaha — ' . $case->case_number)
@section('admin_main_content')

@php
    $d = $case->victimInterview;
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
            <a href="{{ route('attorney-cases.workflow.evidence-interviews', $case->ACID) }}"
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
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Wareysiga Dhibbanaha</span>
        </div>

        <form action="{{ route('attorney-cases.workflow.evidence-interviews.victim-interviews.store', $case->ACID) }}" method="POST"
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

            {{-- 1. Interview Details --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-person-heart text-primary-400"></i> Faahfaahinta Wareysiga
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Magaca Dhibbanaha</label>
                        <input type="text" name="victim_name" list="victimList"
                            value="{{ old('victim_name', $d->victim_name ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                        <datalist id="victimList">
                            @foreach($case->victims ?? [] as $victim)
                                <option value="{{ $victim->full_name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Goobta Wareysiga</label>
                        <input type="text" name="interview_location"
                            value="{{ old('interview_location', $d->interview_location ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Taariikhda Wareysiga <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="interview_date" required
                            value="{{ old('interview_date', $d?->interview_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Wakhtiga Wareysiga</label>
                        <input type="time" name="interview_time"
                            value="{{ old('interview_time', $d->interview_time ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Sarkaalka Wareysiga</label>
                        <input type="text" name="interviewing_officer"
                            value="{{ old('interviewing_officer', $d->interviewing_officer ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
            </div>

            {{-- 2. Support & Welfare --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-heart-pulse text-primary-400"></i> Taageerada Iyo Caafimaadka
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="flex items-center gap-2.5 mb-3 cursor-pointer">
                            <input type="checkbox" name="support_person_present" value="1"
                                {{ old('support_person_present', $d->support_person_present ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                            <span class="text-sm font-medium text-neutral-700">Qof Taageera Ayaa Joogay</span>
                        </label>
                        <input type="text" name="support_person_name" placeholder="Magaca qofka taageeraya..."
                            value="{{ old('support_person_name', $d->support_person_name ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div class="flex flex-col gap-2 justify-end">
                        <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-neutral-50 border border-neutral-200 cursor-pointer">
                            <input type="checkbox" name="medical_treatment_required" value="1"
                                {{ old('medical_treatment_required', $d->medical_treatment_required ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                            <span class="text-sm font-medium text-neutral-700">Daaweyn Caafimaad Ayaa Loo Baahan Yahay</span>
                        </label>
                        <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-neutral-50 border border-neutral-200 cursor-pointer">
                            <input type="checkbox" name="protective_measures_needed" value="1"
                                {{ old('protective_measures_needed', $d->protective_measures_needed ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                            <span class="text-sm font-medium text-neutral-700">Tallaabooyin Ilaalin Ayaa Loo Baahan Yahay</span>
                        </label>
                    </div>
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Faahfaahinta Tallaabooyinka Ilaalinta</label>
                <textarea name="protective_measures_notes" rows="2"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('protective_measures_notes', $d->protective_measures_notes ?? '') }}</textarea>
            </div>

            {{-- 3. Impact Statement --}}
            <div class="rounded-2xl border-2 border-primary-200 p-5 mb-5" style="background:rgba(82,140,190,0.03)">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-chat-square-text text-primary-400"></i> Saameynta Dhibbanaha
                </h3>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Soo Koobidda Saameynta</label>
                <textarea name="victim_impact_summary" rows="4"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all mb-4">{{ old('victim_impact_summary', $d->victim_impact_summary ?? '') }}</textarea>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Bayaanka Dhibbanaha (Fayl)</label>
                <input type="file" name="statement_file"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-primary-400 file:text-white file:text-xs file:font-bold">
                @if($d->statement_file_path ?? null)
                    <a href="{{ asset('storage/' . $d->statement_file_path) }}" target="_blank"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-primary-400 hover:underline mt-1.5">
                        <i class="bi bi-file-earmark-check"></i> Fiiri Faylka La Soo Rarey
                    </a>
                @endif
            </div>

            {{-- 4. Notes --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-journal-text text-primary-400"></i> Faallooyin Dheeraad Ah
                </h3>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('notes', $d->notes ?? '') }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-neutral-100">
                <a href="{{ route('attorney-cases.workflow.evidence-interviews', $case->ACID) }}"
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
