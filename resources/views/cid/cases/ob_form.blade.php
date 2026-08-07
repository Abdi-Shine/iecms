@extends('admin.admin_master')
@php
    $ob = $case->occurrenceBook;

    $errorStep = null;
    if ($errors->any()) {
        $stepFieldMap = [
            1 => ['ob_datetime', 'reporting_officer'],
            2 => ['offence_nature', 'incident_datetime', 'incident_location'],
            3 => ['complainant_name', 'complainant_contact', 'complainant_id', 'complainant_relationship'],
            4 => ['suspect_details', 'narrative'],
            5 => ['witnesses', 'initial_evidence_noted'],
            6 => ['priority', 'assigned_investigator_id', 'assigned_unit', 'is_internal'],
        ];
        foreach ($errors->keys() as $key) {
            foreach ($stepFieldMap as $step => $fields) {
                if (in_array($key, $fields, true)) {
                    $errorStep = $errorStep === null ? $step : min($errorStep, $step);
                    break;
                }
            }
        }
    }
@endphp
@section('page_title', 'Wajiga 2 — Buugga Dhacdooyinka (OB)')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full" x-data="obWizard()">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Wajiga 2: Buugga Dhacdooyinka (OB)</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }} &mdash; Warbixinta Bilowga &amp; U Xilsaarista</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Dib
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($ob?->supervisor_acknowledged_at)
        <div class="mb-4 p-4 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-700 flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            Waxaa xaqiijiyay {{ $ob->supervisor->name ?? 'agaasimaha' }} taariikhda {{ $ob->supervisor_acknowledged_at->format('Y-m-d H:i') }}. Wajiga 3 waa la furay.
        </div>
    @endif

    <form action="{{ route('criminal-cases.workflow.ob.store', $case->id) }}" method="POST" novalidate @submit="onSubmit">
        @csrf

        {{-- Header --}}
        <div class="rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-4">
            <div class="flex items-center justify-between gap-3 px-6 py-4" style="background:#528CBE">
                <div class="flex items-center gap-3">
                    <div style="width:38px;height:38px;background:rgba(255,255,255,0.15);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-journal-text" style="color:white;font-size:1rem"></i>
                    </div>
                    <div>
                        <h2 style="color:white;font-size:1.05rem;font-weight:800;margin:0;letter-spacing:.01em">FOOMKA BUUGGA DHACDOOYINKA (OB)</h2>
                        <p style="color:rgba(255,255,255,.65);font-size:.78rem;margin:0">Hay'adda Baaritaanka Dembiyada (CID)</p>
                    </div>
                </div>
                @if($ob)
                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold shrink-0" style="background:rgba(255,255,255,.15);color:white">
                        <i class="bi bi-hash"></i> {{ $ob->ob_number }}
                    </div>
                @endif
            </div>

            {{-- Step indicator --}}
            <div class="bg-white px-6 py-4 overflow-x-auto">
                <div class="flex items-center justify-between min-w-[640px]">
                    <template x-for="(label, idx) in steps" :key="idx">
                        <div class="flex items-center flex-1">
                            <div class="flex flex-col items-center gap-1.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors"
                                    :style="step === idx + 1 ? 'background:#F0B43C;color:white' : (step > idx + 1 ? 'background:#528CBE;color:white' : 'background:rgba(82,140,190,0.1);color:#528CBE')"
                                    x-text="idx + 1"></div>
                                <span class="text-[11px] font-semibold whitespace-nowrap"
                                    :style="step === idx + 1 ? 'color:#F0B43C' : 'color:#528CBE'" x-text="label"></span>
                            </div>
                            <template x-if="idx < steps.length - 1">
                                <div class="flex-1 h-px mx-1" :style="step > idx + 1 ? 'background:#528CBE' : 'background:rgba(82,140,190,0.15)'"></div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ═══ STEP 1: OB ENTRY ═══ --}}
        <div x-show="step === 1" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
            <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-4">
                <i class="bi bi-journal-plus" style="color:#528CBE"></i> I. XOGTA DIIWAANKA OB-GA
            </h3>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda &amp; Saacadda Diiwaanka OB <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="ob_datetime" required
                            value="{{ old('ob_datetime', $ob?->ob_datetime?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Sarkaalka Warbixinta Bixiyay <span class="text-red-500">*</span></label>
                        <input type="text" name="reporting_officer" required value="{{ old('reporting_officer', $ob->reporting_officer ?? '') }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ STEP 2: OFFENCE DETAILS ═══ --}}
        <div x-show="step === 2" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
            <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-4">
                <i class="bi bi-exclamation-octagon" style="color:#528CBE"></i> II. FAAHFAAHINTA DEMBIGA
            </h3>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Nooca Dambiga <span class="text-red-500">*</span></label>
                        <input type="text" name="offence_nature" required value="{{ old('offence_nature', $ob->offence_nature ?? $case->arrest?->alleged_offence) }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda &amp; Saacadda Dhacdada</label>
                        <input type="datetime-local" name="incident_datetime" value="{{ old('incident_datetime', $ob?->incident_datetime?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Goobta Dhacdada</label>
                        <input type="text" name="incident_location" value="{{ old('incident_location', $ob->incident_location ?? $case->arrest?->arrest_location) }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ STEP 3: COMPLAINANT ═══ --}}
        <div x-show="step === 3" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
            <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-4">
                <i class="bi bi-person-fill" style="color:#528CBE"></i> III. XOGTA CODSADAHA
            </h3>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca</label>
                        <input type="text" name="complainant_name" value="{{ old('complainant_name', $ob->complainant_name ?? '') }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Xiriirka (Telefoon)</label>
                        <input type="text" name="complainant_contact" value="{{ old('complainant_contact', $ob->complainant_contact ?? '') }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka Aqoonsiga</label>
                        <input type="text" name="complainant_id" value="{{ old('complainant_id') }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Xiriirka Kiiska</label>
                        <input type="text" name="complainant_relationship" value="{{ old('complainant_relationship', $ob->complainant_relationship ?? '') }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ STEP 4: SUSPECT & NARRATIVE ═══ --}}
        <div x-show="step === 4" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
            <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-4">
                <i class="bi bi-person-fill-exclamation" style="color:#528CBE"></i> IV. EEDEYSANAHA &amp; SHEEKO-QORAALKA
            </h3>
            <div class="bg-white rounded-xl border border-neutral-200 p-5 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Faahfaahinta Eedeysanaha (Tuhmanka)</label>
                    <textarea name="suspect_details" rows="2" placeholder="{{ $case->arrest ? 'Waxaa laga soo xiriiray Wajiga 1: ' . $case->arrest->arrestee_name : '' }}"
                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none">{{ old('suspect_details', $ob->suspect_details ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Sheeko-qoraalka Dhacdada</label>
                    <textarea name="narrative" rows="4"
                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none">{{ old('narrative', $ob->narrative ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ═══ STEP 5: WITNESSES & EVIDENCE ═══ --}}
        <div x-show="step === 5" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
            <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-4">
                <i class="bi bi-people" style="color:#528CBE"></i> V. MARKHAATIYAAL &amp; CADDAYN
            </h3>
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Markhaatiyaal</label>
                        <textarea name="witnesses" rows="3"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none">{{ old('witnesses', $ob->witnesses ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Caddaynta Bilowga ah ee La Xusay</label>
                        <textarea name="initial_evidence_noted" rows="3"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none">{{ old('initial_evidence_noted', $ob->initial_evidence_noted ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ STEP 6: PRIORITY & ASSIGNMENT ═══ --}}
        <div x-show="step === 6" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
            <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-4">
                <i class="bi bi-flag" style="color:#528CBE"></i> VI. MUDNAANTA &amp; U XILSAARISTA
            </h3>
            <div class="bg-white rounded-xl border border-neutral-200 p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Mudnaanta Kiiska <span class="text-red-500">*</span></label>
                        <select name="priority" required class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            @foreach(['Routine' => 'Caadi', 'Urgent' => 'Degdeg', 'Critical' => 'Aad u Degdeg'] as $val => $label)
                                <option value="{{ $val }}" {{ old('priority', $case->priority) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">U Xilsaar Baarayaha</label>
                        <select name="assigned_investigator_id" class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            <option value="">Dooro Baaraha</option>
                            @foreach($investigators as $inv)
                                <option value="{{ $inv->id }}" {{ old('assigned_investigator_id', $ob->assigned_investigator_id ?? '') == $inv->id ? 'selected' : '' }}>{{ $inv->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">U Xilsaar Guddiga/Xarunta</label>
                        <input type="text" name="assigned_unit" value="{{ old('assigned_unit', $ob->assigned_unit ?? '') }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-neutral-700">
                    <input type="checkbox" name="is_internal" value="1" {{ old('is_internal', $ob->is_internal ?? false) ? 'checked' : '' }}>
                    OB Gudaha (dhacdo heer saldhig ah, mana aha cabasho dibadeed/dadweyne)
                </label>
            </div>
        </div>

        {{-- Footer navigation --}}
        <div class="flex items-center justify-between mt-6 pt-5 border-t border-neutral-200">
            <a href="{{ route('criminal-cases.workflow', $case->id) }}"
                class="px-5 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition flex items-center gap-2">
                <i class="bi bi-x-lg"></i> Jooji
            </a>
            <div class="flex items-center gap-2">
                <button type="button" x-show="step > 1" @click="prevStep()"
                    class="px-5 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition flex items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Dib
                </button>
                <button type="button" x-show="step < totalSteps" @click="nextStep()"
                    class="px-5 py-2.5 text-sm font-semibold rounded-xl text-white transition flex items-center gap-2"
                    style="background:#528CBE">
                    Xiga <i class="bi bi-arrow-right"></i>
                </button>
                <button type="submit" x-show="step === totalSteps"
                    class="px-6 py-2.5 text-sm font-semibold rounded-xl text-white transition flex items-center gap-2"
                    style="background:#528CBE">
                    <i class="bi bi-save-fill"></i> Kaydi Diiwaanka OB
                </button>
            </div>
        </div>
    </form>

    @if($ob && !$ob->supervisor_acknowledged_at)
        <form action="{{ route('criminal-cases.workflow.ob.acknowledge', $case->id) }}" method="POST"
              class="mt-4 pt-5 border-t border-neutral-200">
            @csrf
            <p class="text-sm text-neutral-500 mb-4">
                Waa in agaasime (Baaraha ama Maamulaha Hay'adda) uu xaqiijiyaa diiwaankan ka hor intaan Wajiga 3 la furin.
            </p>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl text-white transition"
                style="background:#16A34A">
                <i class="bi bi-patch-check-fill"></i> Xaqiiji (Agaasime)
            </button>
        </form>
    @endif

</div>

@push('scripts')
    <script>
        function obWizard() {
            return {
                step: 1,
                totalSteps: 6,
                steps: ['OB-ga', 'Dembiga', 'Codsadaha', 'Eedeysanaha', 'Markhaatiyaal', 'Mudnaanta'],
                errorStep: @json($errorStep ?? 0),
                stepStorageKey: 'obWizardStep_{{ $case->id }}',
                saveStep() {
                    sessionStorage.setItem(this.stepStorageKey, String(this.step));
                },
                nextStep() {
                    if (this.step < this.totalSteps) this.step++;
                    this.saveStep();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                prevStep() {
                    if (this.step > 1) this.step--;
                    this.saveStep();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                onSubmit() {
                    sessionStorage.removeItem(this.stepStorageKey);
                },
                init() {
                    if (this.errorStep) {
                        this.step = this.errorStep;
                        return;
                    }
                    const savedStep = parseInt(sessionStorage.getItem(this.stepStorageKey), 10);
                    if (!isNaN(savedStep) && savedStep >= 1 && savedStep <= this.totalSteps) {
                        this.step = savedStep;
                    }
                }
            }
        }
    </script>
@endpush

@endsection
