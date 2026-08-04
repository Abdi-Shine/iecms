@extends('admin.admin_master')
@section('page_title', 'Baaritaanka — ' . $case->case_number)
@section('admin_main_content')

@php
    $inv = $case->investigation;
    $isComplete = (bool) $inv;

    $accusedData = old('accused', $case->accused->map(fn($a) => [
        'full_name'   => $a->full_name,
        'mother_name' => $a->mother_name,
        'gender'      => $a->gender,
        'address'     => $a->address,
        'id_number'   => $a->id_number,
    ])->values()->all());

    $evidenceData = old('evidence', $case->evidenceItems->map(fn($e) => [
        'existing_id'   => $e->id,
        'evidence_type' => $e->evidence_type,
        'description'   => $e->description,
    ])->values()->all());

    $legalProvisionsData = old('legal_provisions', $case->legalProvisions->map(fn($lp) => [
        'provision' => $lp->provision,
    ])->values()->all());
@endphp

<div class="p-4 sm:p-6 w-full" x-data="investigationInitiationForm(@js($accusedData), @js($evidenceData), @js($legalProvisionsData))">

    {{-- Case Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->case_number }}</h1>
                <p class="text-sm text-neutral-500 mt-0.5">{{ implode(' - ', array_filter([$case->added_by, optional($case->complainants->first())->full_name])) ?: '—' }}</p>
            </div>
            <a href="{{ route('attorney-cases.workflow.investigation', $case->ACID) }}"
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
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Bilowga Baaritaanka</span>
        </div>

        <div class="mx-6 mt-6 rounded-2xl p-5 flex items-start gap-3"
            style="background:rgba(23,162,184,0.08);border:1px solid rgba(23,162,184,0.2)">
            <i class="bi bi-info-circle-fill text-lg" style="color:#17a2b8"></i>
            <div>
                <p class="text-sm font-bold text-neutral-800 mb-1">Bilowga Baaritaanka</p>
                <p class="text-sm text-neutral-600">Bixi faahfaahin dhammaystiran si loo furo rasmiga ah faylka baaritaanka dembiyada.</p>
            </div>
        </div>

        <form action="{{ route('attorney-cases.workflow.investigation.store', $case->ACID) }}" method="POST"
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

            {{-- 1. Case Overview --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-folder2-open text-primary-400"></i> Guudmarka Dacwadda
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Lambarka Faylka Dacwadda</label>
                        <p class="text-sm font-bold text-neutral-800 px-4 py-2.5 rounded-xl bg-neutral-50">{{ $case->case_number }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Taariikhda Bilowga <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="commencement_date" required
                            value="{{ old('commencement_date', $inv?->start_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
            </div>

            {{-- 2. Accused / Suspects --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-1">
                    <i class="bi bi-person-fill-exclamation text-primary-400"></i> Eedeysanayaasha / Shakisannada
                </h3>
                <p class="text-xs text-neutral-500 mb-4">Ku dar qof kasta oo la shakisan yahay iyo xogta lagu aqoonsan karo.</p>

                <template x-for="(a, i) in accused" :key="i">
                    <div class="rounded-xl border border-neutral-200 p-5 mb-4 bg-neutral-50">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-primary-400" x-text="'Eedeysane / Shakisan #' + (i + 1)"></span>
                            <button type="button" x-show="accused.length > 1" @click="accused.splice(i, 1)"
                                class="text-xs font-semibold text-danger-500 flex items-center gap-1">
                                <i class="bi bi-trash3"></i> Ka Saar
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Magaca Oo Dhan <span class="text-danger-500">*</span>
                                </label>
                                <input type="text" :name="'accused['+i+'][full_name]'" x-model="a.full_name" required
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Magaca Hooyada</label>
                                <input type="text" :name="'accused['+i+'][mother_name]'" x-model="a.mother_name"
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Jinsiga <span class="text-danger-500">*</span>
                                </label>
                                <select :name="'accused['+i+'][gender]'" x-model="a.gender" required
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                                    <option value="">Dooro</option>
                                    @foreach($sexOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Deganaanshaha <span class="text-danger-500">*</span>
                                </label>
                                <textarea :name="'accused['+i+'][address]'" x-model="a.address" rows="1" required
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all resize-none"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Lambarka Aqoonsiga</label>
                                <input type="text" :name="'accused['+i+'][id_number]'" x-model="a.id_number"
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                            </div>
                        </div>
                    </div>
                </template>

                <button type="button" @click="accused.push(blankAccused())"
                    class="px-4 py-2 text-xs font-bold text-white rounded-xl flex items-center gap-1.5 bg-ago hover:opacity-90 transition-all">
                    <i class="bi bi-plus-lg"></i> Ku Dar Eedeysane/Shakisan
                </button>
            </div>

            {{-- 3. Offence Details --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-journal-bookmark-fill text-primary-400"></i> Faahfaahinta Dembiga
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Nooca Dembiga <span class="text-danger-500">*</span>
                        </label>
                        <select name="offence_category" required
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro Nooca Dembiga —</option>
                            @foreach($offenceCategories as $cat)
                                <option value="{{ $cat }}" {{ old('offence_category', $case->offense_type) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Dembiga Gaarka Ah</label>
                        <input type="text" name="specific_offence" placeholder="Sharax dembiga sax ah..."
                            value="{{ old('specific_offence', $inv->specific_offence ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>

                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Sharciyada La Xiriira</label>
                <datalist id="legal-provision-options">
                    @foreach($legalProvisionOptions as $article)
                        <option value="{{ $article->rule }} – {{ $article->description }}"></option>
                    @endforeach
                </datalist>
                <template x-for="(lp, i) in legalProvisions" :key="i">
                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" :name="'legal_provisions['+i+'][provision]'" x-model="lp.provision"
                            list="legal-provision-options" placeholder="Qor si aad u raadiso ama dooro sharciga..."
                            class="flex-1 min-w-0 px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                        <button type="button" @click="legalProvisions.push(blankLegalProvision())" title="Ku Dar Sharci Kale"
                            class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border border-primary-400 text-primary-400">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                        <button type="button" x-show="legalProvisions.length > 1" @click="legalProvisions.splice(i, 1)" title="Ka Saar"
                            class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border border-danger-200 text-danger-500">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </template>
            </div>

            {{-- 4. Victim Information --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-shield-exclamation text-primary-400"></i> Xogta Dhibanaha
                </h3>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                    Nooca Dhibanaha <span class="text-danger-500">*</span>
                </label>
                <select name="victim_type" required
                    class="w-full md:w-1/2 px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    <option value="">— Dooro —</option>
                    @foreach($victimTypeOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('victim_type', $inv->victim_type ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 5. Preliminary Evidence --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-1">
                    <i class="bi bi-archive-fill text-primary-400"></i> Caddaynta Bilowga Ah
                </h3>
                <p class="text-xs text-neutral-500 mb-4">Liiso caddaynta la haysto marka baaritaanka bilaabmayo (dukumeenti, markhaatiyaal, warbaahin, iwm).</p>

                <template x-for="(ev, i) in evidence" :key="i">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-3">
                        <input type="hidden" :name="'evidence['+i+'][existing_id]'" :value="ev.existing_id || ''">
                        <div class="w-full sm:w-48 shrink-0">
                            <select :name="'evidence['+i+'][evidence_type]'" x-model="ev.evidence_type"
                                class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                                <option value="">Dooro Nooca</option>
                                @foreach($evidenceTypeOptions as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="text" :name="'evidence['+i+'][description]'" x-model="ev.description"
                            placeholder="Sharax caddaynta..."
                            class="flex-1 w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                        <button type="button" x-show="evidence.length > 1" @click="evidence.splice(i, 1)"
                            class="shrink-0 text-xs font-semibold text-danger-500 flex items-center gap-1">
                            <i class="bi bi-trash3"></i> Ka Saar
                        </button>
                    </div>
                </template>

                <button type="button" @click="evidence.push(blankEvidence())"
                    class="px-4 py-2 text-xs font-bold text-white rounded-xl flex items-center gap-1.5 bg-ago hover:opacity-90 transition-all">
                    <i class="bi bi-plus-lg"></i> Ku Dar Caddayn
                </button>
            </div>

            {{-- 6. Case Summary --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-file-text text-primary-400"></i> Soo Koobidda Dacwadda
                </h3>
                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Sharaxaada Dacwadda</label>
                <textarea name="case_description" rows="4" placeholder="Bixi sharaxaad dhammaystiran oo ku saabsan dacwadda..."
                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all mb-4">{{ old('case_description', $case->summary) }}</textarea>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Goobta Dembiga</label>
                        <input type="text" name="location_of_offence"
                            value="{{ old('location_of_offence', $case->incident_location) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Waqtiga Qiyaastiga Ah ee Dembiga</label>
                        <input type="text" name="approximate_time_of_offence" placeholder="tusaale: Intii u dhaxaysay 2-4 galabnimo"
                            value="{{ old('approximate_time_of_offence', $case->incident_time) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
            </div>

            {{-- 7. Prosecutor Information --}}
            <div class="rounded-2xl border-2 border-primary-200 p-5 mb-5" style="background:rgba(82,140,190,0.03)">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-person-badge text-primary-400"></i> Xogta Xeer Ilaaliyaha
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Magaca Xeer Ilaaliyaha <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="prosecutor_name" required
                            value="{{ old('prosecutor_name', $inv->prosecutor_name ?? auth()->user()->name ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Xafiiska/Waaxda</label>
                        <input type="text" name="prosecutor_department"
                            value="{{ old('prosecutor_department', $inv->prosecutor_department ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Saxiixa <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="prosecutor_signature" required placeholder="Magaca/Saxiixa..."
                            value="{{ old('prosecutor_signature', $inv->prosecutor_signature ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Taariikhda Saxiixa <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="signature_date" required
                            value="{{ old('signature_date', $inv?->signature_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
            </div>

            {{-- 8. Attachments --}}
            <div class="rounded-2xl border border-neutral-200 p-5 mb-5">
                <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-800 mb-4">
                    <i class="bi bi-paperclip text-primary-400"></i> Lifaaqyada
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Caddaynta Bilowga Ah</label>
                        <input type="file" name="initial_evidence_file"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-primary-400 file:text-white file:text-xs file:font-bold">
                        @if($inv?->initial_evidence_file)
                            <a href="{{ asset('storage/' . $inv->initial_evidence_file) }}" target="_blank"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-primary-400 hover:underline mt-1.5">
                                <i class="bi bi-file-earmark-check"></i> Fiiri Faylka La Soo Rarey
                            </a>
                        @endif
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Dukumentiyada Taageeraya</label>
                        <input type="file" name="supporting_documents_file"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-primary-400 file:text-white file:text-xs file:font-bold">
                        @if($inv?->supporting_documents_file)
                            <a href="{{ asset('storage/' . $inv->supporting_documents_file) }}" target="_blank"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-primary-400 hover:underline mt-1.5">
                                <i class="bi bi-file-earmark-check"></i> Fiiri Faylka La Soo Rarey
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-neutral-100">
                <a href="{{ route('attorney-cases.workflow.investigation', $case->ACID) }}"
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

@push('scripts')
    <script>
        function investigationInitiationForm(accused, evidence, legalProvisions) {
            const blankAccused = () => ({ full_name: '', mother_name: '', gender: '', address: '', id_number: '' });
            const blankEvidence = () => ({ existing_id: '', evidence_type: '', description: '' });
            const blankLegalProvision = () => ({ provision: '' });

            return {
                accused: accused.length ? accused : [blankAccused()],
                evidence: evidence.length ? evidence : [blankEvidence()],
                legalProvisions: legalProvisions.length ? legalProvisions : [blankLegalProvision()],
                blankAccused,
                blankEvidence,
                blankLegalProvision,
            };
        }
    </script>
@endpush

@endsection
