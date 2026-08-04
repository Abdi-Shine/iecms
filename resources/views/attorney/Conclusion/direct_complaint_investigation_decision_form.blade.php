@extends('admin.admin_master')
@section('page_title', 'Go’aanka Baaritaanka — ' . $case->case_number)
@section('admin_main_content')

@php
    $d = $case->investigationDecision;
    $isComplete = (bool) $d;
    $subtitleParts = array_filter([$case->added_by, optional($case->complainants->first())->full_name]);
    $riskFactorsSelected = old('risk_factors', $d->risk_factors ?? []);
@endphp

<div class="p-4 sm:p-6 w-full">

    {{-- Case Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->case_number }}</h1>
                <p class="text-sm text-neutral-500 mt-0.5">{{ implode(' - ', $subtitleParts) ?: '—' }}</p>
            </div>
            <a href="{{ route('attorney-cases.workflow.investigation-decision', $case->ACID) }}"
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
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Go'aanka Baaritaanka</span>
        </div>

        <form action="{{ route('attorney-cases.workflow.investigation-decision.store', $case->ACID) }}" method="POST"
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
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Cinwaanka Dacwadda</label>
                        <p class="text-sm font-bold text-neutral-800 px-4 py-2.5 rounded-xl bg-neutral-50">{{ $case->title ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- 2. CID Investigation Summary --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-file-text text-primary-400"></i> Warbixinta Baaritaanka CID
                </h3>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                    Soo Koobidda Baaritaanka <span class="text-danger-500">*</span>
                </label>
                <textarea name="investigation_summary" rows="4" required placeholder="Sharax natiijada baaritaanka CID..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('investigation_summary', $d->investigation_summary ?? '') }}</textarea>
            </div>

            {{-- 3. Evidence Assessment --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-shield-check text-primary-400"></i> Qiimaynta Caddaynta
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Tayada Caddaynta</label>
                        <select name="evidence_quality"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro —</option>
                            @foreach($evidenceQualityOptions as $opt)
                                <option value="{{ $opt }}" {{ old('evidence_quality', $d->evidence_quality ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Dhammaystiranaanta Caddaynta</label>
                        <select name="evidence_completeness"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro —</option>
                            @foreach($evidenceCompletenessOptions as $opt)
                                <option value="{{ $opt }}" {{ old('evidence_completeness', $d->evidence_completeness ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Faallooyinka Qiimaynta Caddaynta</label>
                <textarea name="evidence_assessment_notes" rows="3" placeholder="Faahfaahin dheeraad ah oo ku saabsan caddaynta..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('evidence_assessment_notes', $d->evidence_assessment_notes ?? '') }}</textarea>
            </div>

            {{-- 4. Witness Interviews --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-people text-primary-400"></i> Wareysiga Markhaatiyaasha
                </h3>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Tirada Markhaatiyaasha La Wareystay</label>
                <input type="text" name="witnesses_interviewed" placeholder="tusaale: 3"
                    value="{{ old('witnesses_interviewed', $d->witnesses_interviewed ?? '') }}"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all mb-4">
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Faallooyinka Wareysiga</label>
                <textarea name="witness_interview_notes" rows="3" placeholder="Faahfaahin dheeraad ah oo ku saabsan wareysiyada..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('witness_interview_notes', $d->witness_interview_notes ?? '') }}</textarea>
            </div>

            {{-- 5. Legal Assessment --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-bank text-primary-400"></i> Qiimaynta Sharciga
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Ku Filnaanta Sharciga</label>
                        <select name="legal_sufficiency"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro —</option>
                            @foreach($legalSufficiencyOptions as $opt)
                                <option value="{{ $opt }}" {{ old('legal_sufficiency', $d->legal_sufficiency ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Aasaaska Sharciga Ma La Aqoonsaday</label>
                        <select name="legal_basis_identified"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro —</option>
                            @foreach($legalBasisOptions as $opt)
                                <option value="{{ $opt }}" {{ old('legal_basis_identified', $d->legal_basis_identified ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Faallooyinka Qiimaynta Sharciga</label>
                <textarea name="legal_assessment_notes" rows="3" placeholder="Faahfaahin dheeraad ah oo ku saabsan sharciga..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('legal_assessment_notes', $d->legal_assessment_notes ?? '') }}</textarea>
            </div>

            {{-- 6. Investigation Decision --}}
            <div class="rounded-2xl border-2 border-primary-200 p-5 mb-5" style="background:rgba(82,140,190,0.03)">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-clipboard-check text-primary-400"></i> Go'aanka Baaritaanka
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Nooca Go'aanka <span class="text-danger-500">*</span>
                        </label>
                        <select name="decision" required
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro Go'aanka —</option>
                            @foreach($decisions as $dec)
                                <option value="{{ $dec }}" {{ old('decision', $d->decision ?? '') === $dec ? 'selected' : '' }}>{{ $dec }}</option>
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
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Sababta Go'aanka</label>
                <textarea name="reasoning" rows="3" placeholder="Fadlan sharax sababta go'aankan..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all mb-4">{{ old('reasoning', $d->reasoning ?? '') }}</textarea>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Tallaabooyinka Xiga</label>
                <textarea name="next_steps" rows="3" placeholder="Waa maxay tallaabada xigta..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">{{ old('next_steps', $d->next_steps ?? '') }}</textarea>
            </div>

            {{-- 7. Resource Requirements --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-stack text-primary-400"></i> Baahida Kheyraadka
                </h3>
                <label class="flex items-center gap-2.5 mb-4 cursor-pointer">
                    <input type="checkbox" name="additional_investigation_needed" value="1"
                        {{ old('additional_investigation_needed', $d->additional_investigation_needed ?? false) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                    <span class="text-sm font-semibold text-neutral-700">Baaritaan Dheeraad Ah Ayaa Loo Baahan Yahay</span>
                </label>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Waqtiga La Filayo Dhammaystirka</label>
                <input type="text" name="estimated_completion_time" placeholder="tusaale: 2 toddobaad"
                    value="{{ old('estimated_completion_time', $d->estimated_completion_time ?? '') }}"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all mb-4">
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Baahida Kheyraadka</label>
                <textarea name="resource_requirements" rows="3" placeholder="Sharax kheyraadka loo baahan yahay..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('resource_requirements', $d->resource_requirements ?? '') }}</textarea>
            </div>

            {{-- 8. Risk Assessment --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-exclamation-triangle text-primary-400"></i> Qiimaynta Halista
                </h3>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Heerka Halista Guud</label>
                <select name="overall_risk_level"
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all mb-4">
                    <option value="">— Dooro —</option>
                    @foreach($riskLevelOptions as $opt)
                        <option value="{{ $opt }}" {{ old('overall_risk_level', $d->overall_risk_level ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Sababaha Halista</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                    @foreach($riskFactorOptions as $factor)
                        <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-neutral-50 border border-neutral-200 cursor-pointer">
                            <input type="checkbox" name="risk_factors[]" value="{{ $factor }}"
                                {{ in_array($factor, $riskFactorsSelected ?? []) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                            <span class="text-sm font-medium text-neutral-700">{{ $factor }}</span>
                        </label>
                    @endforeach
                </div>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Xeeladaha Yareynta Halista</label>
                <textarea name="risk_mitigation_strategies" rows="3" placeholder="Sharax xeeladaha loo maareynayo halista..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">{{ old('risk_mitigation_strategies', $d->risk_mitigation_strategies ?? '') }}</textarea>
            </div>

            {{-- 9. Supporting Documentation --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-paperclip text-primary-400"></i> Dukumentiyada Taageeraya
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach([
                        'cid_investigation_report' => ['label' => 'Warbixinta Baaritaanka CID', 'path' => $d->cid_investigation_report_path ?? null],
                        'evidence_photographs'     => ['label' => 'Sawirrada Caddaynta', 'path' => $d->evidence_photographs_path ?? null],
                        'witness_statements'       => ['label' => 'Bayaannada Markhaatiyaasha', 'path' => $d->witness_statements_path ?? null],
                        'other_documents'          => ['label' => 'Dukumentiyo Kale', 'path' => $d->other_documents_path ?? null],
                    ] as $field => $meta)
                        <div>
                            <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">{{ $meta['label'] }}</label>
                            <input type="file" name="{{ $field }}"
                                class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-primary-400 file:text-white file:text-xs file:font-bold">
                            @if($meta['path'])
                                <a href="{{ asset('storage/' . $meta['path']) }}" target="_blank"
                                    class="inline-flex items-center gap-1 text-xs font-semibold text-primary-400 hover:underline mt-1.5">
                                    <i class="bi bi-file-earmark-check"></i> Fiiri Faylka La Soo Rarey
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 10. Approval --}}
            <div class="rounded-2xl border border-success-200 p-5 mb-5" style="background:rgba(16,163,74,0.03)">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-2">
                    <i class="bi bi-patch-check-fill text-success-600"></i> Ansixinta
                </h3>
                <p class="text-xs text-neutral-500 mb-4">
                    <i class="bi bi-info-circle"></i>
                    Go'aankan baaritaanka waa inuu dib u eegis oo ansixin ka helaa hoggaanka la doortay ka hor intaanu shaqo laga qaban.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Ku Talinaya</label>
                        <input type="text" name="recommended_by" placeholder="Magaca qofka ku talinaya..."
                            value="{{ old('recommended_by', $d->recommended_by ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Taariikhda Talada</label>
                        <input type="date" name="recommended_date"
                            value="{{ old('recommended_date', $d?->recommended_date?->format('Y-m-d') ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Ansixiyay</label>
                        <input type="text" name="approved_by" placeholder="Magaca qofka ansixiyay..."
                            value="{{ old('approved_by', $d->approved_by ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Taariikhda Ansixinta</label>
                        <input type="date" name="approved_date"
                            value="{{ old('approved_date', $d?->approved_date?->format('Y-m-d') ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-neutral-100">
                <a href="{{ route('attorney-cases.workflow.investigation-decision', $case->ACID) }}"
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
