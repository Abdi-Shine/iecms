@extends('admin.admin_master')
@section('page_title', 'Wareysiga Eedeysanaha — ' . $case->case_number)
@section('admin_main_content')

@php
    $d = $case->suspectInterview;
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
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Wareysiga Eedeysanaha</span>
        </div>

        <form action="{{ route('attorney-cases.workflow.evidence-interviews.suspect-interviews.store', $case->ACID) }}" method="POST"
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
                    <i class="bi bi-person-video3 text-primary-400"></i> Faahfaahinta Wareysiga
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

            {{-- 2. Safeguards --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-shield-check text-primary-400"></i> Ilaalinta Xuquuqda
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="flex items-center gap-2.5 mb-3 cursor-pointer">
                            <input type="checkbox" name="interpreter_used" value="1"
                                {{ old('interpreter_used', $d->interpreter_used ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                            <span class="text-sm font-medium text-neutral-700">Turjumaan Ayaa La Isticmaalay</span>
                        </label>
                        <input type="text" name="interpreter_name" placeholder="Magaca turjumaanka..."
                            value="{{ old('interpreter_name', $d->interpreter_name ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="flex items-center gap-2.5 mb-3 cursor-pointer">
                            <input type="checkbox" name="legal_counsel_present" value="1"
                                {{ old('legal_counsel_present', $d->legal_counsel_present ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                            <span class="text-sm font-medium text-neutral-700">Qareen Ayaa Joogay</span>
                        </label>
                        <input type="text" name="counsel_name" placeholder="Magaca qareenka..."
                            value="{{ old('counsel_name', $d->counsel_name ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="rights_informed" value="1"
                        {{ old('rights_informed', $d->rights_informed ?? false) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                    <span class="text-sm font-semibold text-neutral-700">Xuquuqda Waa Loo Sheegay Eedeysanaha</span>
                </label>
            </div>

            {{-- 3. Statement --}}
            <div class="rounded-2xl border-2 border-primary-200 p-5 mb-5" style="background:rgba(82,140,190,0.03)">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-chat-square-text text-primary-400"></i> Bayaanka
                </h3>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Habka Duubista</label>
                <select name="recording_method"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all mb-4">
                    <option value="">— Dooro —</option>
                    @foreach($recordingMethodOptions as $opt)
                        <option value="{{ $opt }}" {{ old('recording_method', $d->recording_method ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Soo Koobidda Bayaanka</label>
                <textarea name="statement_summary" rows="4" placeholder="Sharax bayaanka eedeysanaha..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all mb-4">{{ old('statement_summary', $d->statement_summary ?? '') }}</textarea>
                <div class="flex flex-wrap gap-4 mb-4">
                    <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-white border border-neutral-200 cursor-pointer">
                        <input type="checkbox" name="statement_voluntary" value="1"
                            {{ old('statement_voluntary', $d->statement_voluntary ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                        <span class="text-sm font-medium text-neutral-700">Bayaanka Waa Ikhtiyaari</span>
                    </label>
                    <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-white border border-neutral-200 cursor-pointer">
                        <input type="checkbox" name="signature_obtained" value="1"
                            {{ old('signature_obtained', $d->signature_obtained ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                        <span class="text-sm font-medium text-neutral-700">Saxeexa Waa La Helay</span>
                    </label>
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Faylka Duubista (Cod/Muuqaal)</label>
                <input type="file" name="recording_file"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-primary-400 file:text-white file:text-xs file:font-bold">
                @if($d->recording_file_path ?? null)
                    <a href="{{ asset('storage/' . $d->recording_file_path) }}" target="_blank"
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
