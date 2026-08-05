@extends('admin.admin_master')
@section('page_title', 'Extension Request Form — ' . $case->case_number)
@section('admin_main_content')

@php
    $e = $case->investigationExtension;
    $isComplete = (bool) $e;
    $subtitleParts = array_filter([$case->added_by, optional($case->complainants->first())->full_name]);

    $accusedData = old('accused', $e && $e->accused->isNotEmpty()
        ? $e->accused->map(fn($a) => [
            'full_name'   => $a->full_name,
            'mother_name' => $a->mother_name,
            'sex'         => $a->sex,
            'residence'   => $a->residence,
        ])->values()->all()
        : ($case->accused->isNotEmpty()
            ? $case->accused->map(fn($a) => [
                'full_name'   => $a->full_name,
                'mother_name' => $a->mother_name,
                'sex'         => $a->gender,
                'residence'   => $a->address,
            ])->values()->all()
            : [])
    );

    $reasonFlags = [
        'reason_ongoing_investigation'         => 'Ongoing further investigation',
        'reason_awaiting_scan_results'         => 'Awaiting results of scans, fingerprints, or DNA',
        'reason_awaiting_institutional_experts' => 'Awaiting institutional experts (e.g. Auditor General, PBC, etc.)',
        'reason_awaiting_witness_statements'   => 'Awaiting statements from witnesses or expert critical to the case',
    ];
@endphp

<div class="p-4 sm:p-6 w-full" x-data="investigationExtensionForm(@js($accusedData))">

    {{-- Case Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->case_number }}</h1>
                <p class="text-sm text-neutral-500 mt-0.5">{{ implode(' - ', $subtitleParts) ?: '—' }}</p>
            </div>
            <a href="{{ route('attorney-cases.workflow.investigation-extension', $case->ACID) }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i> Ku Laabo
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="px-6 py-5 text-center border-b border-neutral-100"
            style="background:{{ $isComplete ? 'rgba(240,180,60,0.06)' : 'rgba(82,140,190,0.06)' }}">
            <p class="text-xs font-bold uppercase tracking-widest text-neutral-500">Office of the Attorney General</p>
            <h2 class="text-lg font-black text-neutral-800 tracking-tight mt-1">EXTENSION REQUEST FORM</h2>
        </div>

        <form action="{{ route('attorney-cases.workflow.investigation-extension.store', $case->ACID) }}" method="POST" class="p-6">
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

            {{-- General Information About The Case --}}
            <div class="mb-6">
                <h3 class="text-sm font-black uppercase tracking-wider text-neutral-700 pb-2 mb-4 border-b-2 border-neutral-800">
                    General Information About The Case
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">Case File Number</label>
                        <p class="text-sm font-bold text-neutral-800 px-4 py-2.5 rounded-xl bg-neutral-50">{{ $case->case_number }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Date the Case Was Registered for Investigation <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="date_registered_investigation" required
                            value="{{ old('date_registered_investigation', $e?->date_registered_investigation?->format('Y-m-d') ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Type of Incident Related to the Offence <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="incident_type" required
                            value="{{ old('incident_type', $e->incident_type ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Relevant Legal Article(s) <span class="text-danger-500">*</span>
                        </label>
                        <textarea name="legal_articles" required rows="2"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">{{ old('legal_articles', $e->legal_articles ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Date the Offence Occurred <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="date_offence_occurred" required
                            value="{{ old('date_offence_occurred', $e?->date_offence_occurred?->format('Y-m-d') ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Location Where the Offence Took Place <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="offence_location" required
                            value="{{ old('offence_location', $e->offence_location ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Date Police Investigation Commenced <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="date_investigation_commenced" required
                            value="{{ old('date_investigation_commenced', $e?->date_investigation_commenced?->format('Y-m-d') ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Name of the Court Handling the Case <span class="text-danger-500">*</span>
                        </label>
                        <select name="court_name" required
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">Select Court</option>
                            @foreach($courts as $c)
                                <option value="{{ $c->longName }}" {{ old('court_name', $e->court_name ?? '') === $c->longName ? 'selected' : '' }}>{{ $c->longName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Court Case Reference Number <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="court_case_reference" required
                            value="{{ old('court_case_reference', $e->court_case_reference ?? $case->case_number) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
            </div>

            {{-- Information About The Accused Person(s) --}}
            <div class="mb-6">
                <h3 class="text-sm font-black uppercase tracking-wider text-neutral-700 pb-2 mb-4 border-b-2 border-primary-400">
                    Information About The Accused Person(s)
                </h3>

                <template x-for="(a, i) in accused" :key="i">
                    <div class="rounded-xl border border-neutral-200 p-5 mb-4 bg-neutral-50">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-primary-400" x-text="'Person #' + (i + 1)"></span>
                            <button type="button" x-show="accused.length > 1" @click="accused.splice(i, 1)"
                                class="text-xs font-semibold text-danger-500 flex items-center gap-1">
                                <i class="bi bi-trash3"></i> Remove
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Full name <span class="text-danger-500">*</span>
                                </label>
                                <input type="text" :name="'accused['+i+'][full_name]'" x-model="a.full_name" required
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Mother's Name <span class="text-danger-500">*</span>
                                </label>
                                <input type="text" :name="'accused['+i+'][mother_name]'" x-model="a.mother_name" required
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Sex <span class="text-danger-500">*</span>
                                </label>
                                <select :name="'accused['+i+'][sex]'" x-model="a.sex" required
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                                    <option value="">Select</option>
                                    @foreach($sexOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Residence <span class="text-danger-500">*</span>
                                </label>
                                <input type="text" :name="'accused['+i+'][residence]'" x-model="a.residence" required
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                            </div>
                        </div>
                    </div>
                </template>

                <button type="button" @click="accused.push(blankAccused())"
                    class="px-4 py-2 text-xs font-bold text-white rounded-xl flex items-center gap-1.5 bg-ago hover:opacity-90 transition-all">
                    <i class="bi bi-plus-lg"></i> Add Second Person
                </button>
            </div>

            {{-- Reason For Extension Request --}}
            <div class="mb-6">
                <h3 class="text-sm font-black uppercase tracking-wider text-neutral-700 pb-2 mb-4 border-b-2 border-neutral-800">
                    Reason For Extension Request
                </h3>
                <div class="space-y-2.5">
                    @foreach($reasonFlags as $field => $label)
                        <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-neutral-50 border border-neutral-200 cursor-pointer">
                            <input type="checkbox" name="{{ $field }}" value="1"
                                {{ old($field, $e->$field ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                            <span class="text-sm font-medium text-neutral-700">{{ $label }}</span>
                        </label>
                    @endforeach
                    <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-neutral-50 border border-neutral-200 cursor-pointer">
                        <input type="checkbox" name="reason_other" value="1"
                            {{ old('reason_other', $e->reason_other ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                        <span class="text-sm font-medium text-neutral-700">Other reasons (Specify)</span>
                    </label>
                    <textarea name="reason_other_specify" rows="2" placeholder="Specify other reason..."
                        class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">{{ old('reason_other_specify', $e->reason_other_specify ?? '') }}</textarea>
                </div>
            </div>

            {{-- Requested Extension Period --}}
            <div class="mb-6">
                <h3 class="text-sm font-black uppercase tracking-wider text-neutral-700 pb-2 mb-4 border-b-2 border-neutral-800">
                    Requested Extension Period
                </h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($extensionPeriodOptions as $opt)
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-neutral-50 border border-neutral-200 cursor-pointer">
                            <input type="radio" name="extension_period" value="{{ $opt }}" required
                                {{ old('extension_period', $e->extension_period ?? '') === $opt ? 'checked' : '' }}
                                class="w-4 h-4 border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer">
                            <span class="text-sm font-medium text-neutral-700">{{ $opt }}</span>
                        </label>
                    @endforeach
                </div>
                <input type="text" name="extension_period_other" placeholder="If Other, specify number of days..."
                    value="{{ old('extension_period_other', $e->extension_period_other ?? '') }}"
                    class="w-full mt-3 px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
            </div>

            {{-- Signature --}}
            <div class="rounded-2xl border-2 border-primary-200 p-5 mb-6" style="background:rgba(82,140,190,0.03)">
                <h3 class="text-sm font-black uppercase tracking-wider text-neutral-700 mb-4">Signature</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Name of the Prosecutor <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="prosecutor_name" required
                            value="{{ old('prosecutor_name', $e->prosecutor_name ?? $case->added_by) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Title <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="prosecutor_title" required
                            value="{{ old('prosecutor_title', $e->prosecutor_title ?? '') }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-neutral-100">
                <a href="{{ route('attorney-cases.workflow.investigation-extension', $case->ACID) }}"
                    class="px-6 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200 rounded-xl bg-white hover:bg-neutral-50 transition-all">
                    Cancel
                </a>
                <button type="submit"
                    class="flex items-center gap-2 px-8 py-2.5 text-white text-sm font-bold rounded-xl shadow hover:opacity-90 transition-all bg-ago">
                    <i class="bi bi-cloud-check-fill"></i>
                    <span>Submit Form</span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
    <script>
        function investigationExtensionForm(accused) {
            const blankAccused = () => ({ full_name: '', mother_name: '', sex: '', residence: '' });

            return {
                accused: accused.length ? accused : [blankAccused()],
                blankAccused,
            };
        }
    </script>
@endpush

@endsection
