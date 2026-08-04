@extends('admin.admin_master')
@php
    $isEdit = isset($case);
    $hasOldInput = $errors->any();

    $existingWizardData = null;
    if ($hasOldInput) {
        $oldEvidence = old('evidence', []);
        $existingFilesById = $isEdit ? $case->evidenceItems->keyBy('id') : collect();
        foreach ($oldEvidence as $i => $row) {
            $existingId = $row['existing_id'] ?? null;
            $oldEvidence[$i]['existing_file_url'] = ($existingId && $existingFilesById->has($existingId) && $existingFilesById[$existingId]->file_path)
                ? asset('storage/' . $existingFilesById[$existingId]->file_path)
                : null;
        }

        $existingWizardData = [
            'sourceOfCase' => old('source_of_case', ''),
            'offenseType' => old('offense_type', ''),
            'incidentDate' => old('incident_date', ''),
            'complainants' => old('complainants', []),
            'victims' => old('victims', []),
            'accused' => old('accused', []),
            'evidence' => $oldEvidence,
            'witnesses' => old('witnesses', []),
            'legalProvisions' => old('legal_provisions', []),
        ];
    } elseif ($isEdit) {
        $existingWizardData = [
            'sourceOfCase' => optional($case->complainants->first())->type ?? '',
            'offenseType' => $case->offense_type ?? '',
            'incidentDate' => $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('Y-m-d') : '',
            'complainants' => $case->complainants->map(function ($c) {
                return [
                    'full_name' => $c->full_name ?? '',
                    'phone_number' => $c->phone_number ?? '',
                    'email' => $c->email ?? '',
                    'date_of_birth' => $c->date_of_birth ? $c->date_of_birth->format('Y-m-d') : '',
                    'nationality' => $c->nationality ?? '',
                    'occupation' => $c->occupation ?? '',
                    'address' => $c->address ?? '',
                    'license_number' => $c->license_number ?? '',
                    'representative_name' => $c->representative_name ?? '',
                    'representative_title' => $c->representative_title ?? '',
                    'lawyer_name' => $c->lawyer_name ?? '',
                    'lawyer_license_no' => $c->lawyer_license_no ?? '',
                ];
            })->values()->all(),
            'victims' => $case->victims->map(function ($v) {
                return [
                    'type' => $v->type ?: 'Shakhsi',
                    'full_name' => $v->full_name ?? '',
                    'mother_name' => $v->mother_name ?? '',
                    'date_of_birth' => $v->date_of_birth ? $v->date_of_birth->format('Y-m-d') : '',
                    'gender' => $v->gender ?? '',
                    'place_of_birth' => $v->place_of_birth ?? '',
                    'district' => $v->district ?? '',
                    'region' => $v->region ?? '',
                    'address' => $v->address ?? '',
                    'phone_number' => $v->phone_number ?? '',
                    'id_number' => $v->id_number ?? '',
                    'impact_description' => $v->impact_description ?? '',
                    'custodian_full_name' => $v->custodian_full_name ?? '',
                    'custodian_relationship' => $v->custodian_relationship ?? '',
                    'custodian_id_number' => $v->custodian_id_number ?? '',
                    'custodian_phone_number' => $v->custodian_phone_number ?? '',
                    'custodian_address' => $v->custodian_address ?? '',
                    'custodian_email' => $v->custodian_email ?? '',
                ];
            })->values()->all(),
            'accused' => $case->accused->map(function ($a) {
                return [
                    'full_name' => $a->full_name ?? '',
                    'mother_name' => $a->mother_name ?? '',
                    'date_of_birth' => $a->date_of_birth ? $a->date_of_birth->format('Y-m-d') : '',
                    'gender' => $a->gender ?? '',
                    'place_of_birth' => $a->place_of_birth ?? '',
                    'district' => $a->district ?? '',
                    'region' => $a->region ?? '',
                    'address' => $a->address ?? '',
                    'phone_number' => $a->phone_number ?? '',
                    'id_number' => $a->id_number ?? '',
                    'additional_details' => $a->additional_details ?? '',
                    'custodian_full_name' => $a->custodian_full_name ?? '',
                    'custodian_relationship' => $a->custodian_relationship ?? '',
                    'custodian_id_number' => $a->custodian_id_number ?? '',
                    'custodian_phone_number' => $a->custodian_phone_number ?? '',
                    'custodian_address' => $a->custodian_address ?? '',
                    'custodian_email' => $a->custodian_email ?? '',
                ];
            })->values()->all(),
            'evidence' => $case->evidenceItems->map(function ($e) {
                return [
                    'existing_id' => $e->id,
                    'evidence_type' => $e->evidence_type ?? '',
                    'description' => $e->description ?? '',
                    'collected_date' => $e->collected_date ? $e->collected_date->format('Y-m-d') : '',
                    'existing_file_url' => $e->file_path ? asset('storage/' . $e->file_path) : null,
                ];
            })->values()->all(),
            'witnesses' => $case->witnesses->map(function ($w) {
                return [
                    'full_name' => $w->full_name ?? '',
                    'mother_name' => $w->mother_name ?? '',
                    'date_of_birth' => $w->date_of_birth ?? '',
                    'gender' => $w->gender ?? '',
                    'place_of_birth' => $w->place_of_birth ?? '',
                    'district' => $w->district ?? '',
                    'region' => $w->region ?? '',
                    'id_number' => $w->id_number ?? '',
                    'nationality' => $w->nationality ?? '',
                    'occupation' => $w->occupation ?? '',
                    'phone_number' => $w->phone_number ?? '',
                    'address' => $w->address ?? '',
                    'statement' => $w->statement ?? '',
                ];
            })->values()->all(),
            'legalProvisions' => $case->legalProvisions->map(function ($lp) {
                return ['provision' => $lp->provision ?? ''];
            })->values()->all(),
        ];
    }

    $errorStep = null;
    if ($errors->any()) {
        $stepFieldMap = [
            1 => ['source_of_case', 'complainants'],
            2 => ['victims'],
            3 => ['accused'],
            4 => ['incident_date', 'incident_time', 'incident_location', 'summary'],
            5 => ['offense_type', 'priority', 'legal_provisions'],
            6 => ['evidence'],
            7 => ['witnesses'],
            8 => ['relief_sought'],
            9 => ['declaration_accepted', 'declaration_signed_by', 'declaration_date', 'declaration_signature_data'],
        ];
        foreach ($errors->keys() as $key) {
            foreach ($stepFieldMap as $step => $prefixes) {
                $matches = false;
                foreach ($prefixes as $prefix) {
                    if ($key === $prefix || str_starts_with($key, $prefix . '.')) {
                        $matches = true;
                        break;
                    }
                }
                if ($matches) {
                    $errorStep = $errorStep === null ? $step : min($errorStep, $step);
                    break;
                }
            }
        }
    }
@endphp
@section('page_title', $isEdit ? ($case->case_number . ' — Wax Ka Beddel') : 'Dacwad Cusub — Direct Complaint')

@push('styles')
    <style>
        .sig-pad-wrap {
            border: 1.5px solid #d1d5db;
            border-radius: .625rem;
            background: #fff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .sig-pad-wrap canvas {
            width: 100%;
            height: 90px;
            cursor: crosshair;
            display: block;
            touch-action: none;
        }

        .sig-pad-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 8px;
            border-top: 1px solid #f3f4f6;
            background: #fafafa;
        }

        .sig-pad-hint {
            font-size: .65rem;
            color: #9ca3af;
        }

        .sig-clear-btn {
            font-size: .68rem;
            color: #ef4444;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            transition: background .15s;
        }

        .sig-clear-btn:hover {
            background: #fee2e2;
        }

        .sig-pad-wrap.has-sig {
            border-color: #528CBE;
        }
    </style>
@endpush

@section('admin_main_content')

    <div class="p-4 sm:p-6 w-full" x-data="complaintWizard()">

        <form action="{{ $isEdit ? route('attorney-cases.update', $case->ACID) : route('attorney-cases.store') }}"
            method="POST" enctype="multipart/form-data" novalidate @submit="onSubmit">
            @csrf
            @if($isEdit) @method('PUT') @endif

            {{-- Header --}}
            <div class="rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-4">
                <div class="flex items-center gap-3 px-6 py-4" style="background:#528CBE">
                    <div
                        style="width:38px;height:38px;background:rgba(255,255,255,0.15);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-file-earmark-text" style="color:white;font-size:1rem"></i>
                    </div>
                    <div>
                        <h2 style="color:white;font-size:1.05rem;font-weight:800;margin:0;letter-spacing:.01em">
                            @if($isEdit) WAX KA BEDDEL CABASHADA CIQAABTA @else FOOMKA WARBIXINTA CABASHADA CIQAABTA
                            (CABASHO TOOS AH) @endif
                        </h2>
                        <p style="color:rgba(255,255,255,.65);font-size:.78rem;margin:0">Xafiiska Xeer Ilaaliyaha Guud —
                            Soomaaliya</p>
                    </div>
                </div>

                {{-- Step indicator --}}
                <div class="bg-white px-6 py-4 overflow-x-auto">
                    <div class="flex items-center justify-between min-w-[760px]">
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
                                    <div class="flex-1 h-px mx-1"
                                        :style="step > idx + 1 ? 'background:#528CBE' : 'background:rgba(82,140,190,0.15)'">
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ═══ STEP 1: COMPLAINANT ═══ --}}
            <div x-show="step === 1" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
                <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-3">
                    <i class="bi bi-person-fill" style="color:#528CBE"></i> I. XOGTA CABASHADA
                </h3>

                <div class="flex flex-wrap items-end gap-4 mb-5">


                    <div class="flex-1 min-w-[220px]">
                        <label class="block text-xs font-bold text-neutral-600 uppercase tracking-wide mb-2">Isha Dacwadda
                            <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <select name="source_of_case" x-model="sourceOfCase" required
                                class="flex-1 px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-[#528CBE]">
                                <option value="">Dooro Isha</option>
                                <option value="Qareen">Qareen</option>
                                <option value="Shakhsi">Shakhsi</option>
                                <option value="Hay'ad Dawladeed">Hay'ad Dawladeed</option>
                                <option value="Shirkad">Shirkad</option>
                                <option value="Baaris Xig">Baaris Xig</option>
                                <template x-for="type in sourceTypes" :key="type.id">
                                    <option :value="type.name" x-text="type.name"></option>
                                </template>
                            </select>
                            <button type="button" @click="openSourceTypeModal()" title="Ku Dar Isha Cusub"
                                class="shrink-0 w-11 h-11 flex items-center justify-center rounded-xl border"
                                style="border-color:#528CBE;color:#528CBE">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold shrink-0"
                        style="background:rgba(82,140,190,0.08);color:#1e4a73">
                        <i class="bi bi-hash"></i> Lambarka Dacwadda:
                        @if($isEdit)
                            {{ $case->case_number }}
                        @else
                            <span x-text="previewCaseNumber()"></span>
                        @endif
                    </div>
                    @if($isEdit)
                        <div class="min-w-[200px]">
                            <label
                                class="block text-xs font-bold text-neutral-600 uppercase tracking-wide mb-2">Xaaladda</label>
                            <select name="status" required
                                class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-[#528CBE]">
                                @foreach($statuses as $s)
                                    <option value="{{ $s }}" {{ $case->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <h4 x-show="sourceOfCase !== 'Shirkad' && sourceOfCase !== 'Hay\'ad Dawladeed' && sourceOfCase !== 'Baaris Xig'"
                    class="flex items-center gap-2 font-bold text-sm text-neutral-700 mb-3 pb-2 border-b-2"
                    style="border-color:#528CBE">
                    <i class="bi bi-person-badge"></i> Xogta Qareenka &amp; Shakhsiga
                </h4>
                <h4 x-show="sourceOfCase === 'Shirkad'" x-cloak
                    class="flex items-center gap-2 font-bold text-sm text-neutral-700 mb-3 pb-2 border-b-2"
                    style="border-color:#528CBE">
                    <i class="bi bi-building"></i> Xogta Shirkadda
                </h4>
                <h4 x-show="sourceOfCase === 'Hay\'ad Dawladeed'" x-cloak
                    class="flex items-center gap-2 font-bold text-sm text-neutral-700 mb-3 pb-2 border-b-2"
                    style="border-color:#528CBE">
                    <i class="bi bi-bank"></i> Xogta Hay'adda Dawladeed
                </h4>
                <h4 x-show="sourceOfCase === 'Baaris Xig'" x-cloak
                    class="flex items-center gap-2 font-bold text-sm text-neutral-700 mb-3 pb-2 border-b-2"
                    style="border-color:#528CBE">
                    <i class="bi bi-search"></i> Xogta Baaris Xig
                </h4>

                <template x-for="(c, i) in complainants" :key="i">
                    <div class="bg-white rounded-xl border border-neutral-200 p-5 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold px-2.5 py-1 rounded-full"
                                    style="background:rgba(82,140,190,0.12);color:#528CBE"
                                    x-text="typeLabel(sourceOfCase) + ' — Diiwaan'"></span>
                                <input type="hidden" :name="'complainants['+i+'][type]'"
                                    :value="sourceOfCase || 'Shakhsi'">
                            </div>
                            <button type="button" x-show="complainants.length > 1" @click="complainants.splice(i, 1)"
                                class="text-xs font-semibold text-red-500 flex items-center gap-1"><i
                                    class="bi bi-trash3"></i> Ka Saar</button>
                        </div>

                        {{-- Standard fields: Individual / Lawyer --}}
                        <template x-if="sourceOfCase !== 'Shirkad' && sourceOfCase !== 'Hay\'ad Dawladeed' && sourceOfCase !== 'Baaris Xig'">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Oo Dhan <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" :name="'complainants['+i+'][full_name]'" x-model="c.full_name"
                                        :required="sourceOfCase !== 'Shirkad' && sourceOfCase !== 'Hay\'ad Dawladeed' && sourceOfCase !== 'Baaris Xig'"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka Taleefanka <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" :name="'complainants['+i+'][phone_number]'" x-model="c.phone_number"
                                        inputmode="numeric" @input="c.phone_number = c.phone_number.replace(/\D/g,'')"
                                        :required="sourceOfCase !== 'Shirkad' && sourceOfCase !== 'Hay\'ad Dawladeed' && sourceOfCase !== 'Baaris Xig'"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Ciwaanka Emailka</label>
                                    <input type="email" :name="'complainants['+i+'][email]'" x-model="c.email"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda
                                        Dhalashada</label>
                                    <div class="relative">
                                        <input type="text" :name="'complainants['+i+'][date_of_birth]'"
                                            x-model="c.date_of_birth" x-init="window.iecmsDatePicker($el)"
                                            autocomplete="off" placeholder="dd/mm/yyyy"
                                            class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                        <i
                                            class="bi bi-calendar3 absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Jinsiyadda</label>
                                    <select :name="'complainants['+i+'][nationality]'" x-model="c.nationality"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                        <option value="">Dooro Jinsiyadda</option>
                                        <template x-for="country in countries" :key="country">
                                            <option :value="country" x-text="country"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Shaqada</label>
                                    <input type="text" :name="'complainants['+i+'][occupation]'" x-model="c.occupation"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Cinwaanka</label>
                                    <textarea :name="'complainants['+i+'][address]'" x-model="c.address" rows="2"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                                </div>
                                <div x-show="sourceOfCase === 'Qareen'">
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka Shatiga
                                        Qareenka</label>
                                    <input type="text" :name="'complainants['+i+'][license_number]'"
                                        x-model="c.license_number"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                            </div>
                        </template>

                        {{-- Company-specific fields --}}
                        <template x-if="sourceOfCase === 'Shirkad'">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Shirkadda <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" :name="'complainants['+i+'][full_name]'" x-model="c.full_name"
                                        :required="sourceOfCase === 'Shirkad'"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka Shatiga
                                        Shirkadda</label>
                                    <input type="text" :name="'complainants['+i+'][license_number]'"
                                        x-model="c.license_number"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Oo Dhan ee
                                        Wakiilka
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'complainants['+i+'][representative_name]'"
                                        x-model="c.representative_name" :required="sourceOfCase === 'Shirkad'"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Jagada Wakiilka</label>
                                    <input type="text" :name="'complainants['+i+'][representative_title]'"
                                        x-model="c.representative_title"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Xogta La Xiriirka <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" :name="'complainants['+i+'][phone_number]'" x-model="c.phone_number"
                                        inputmode="numeric" @input="c.phone_number = c.phone_number.replace(/\D/g,'')"
                                        :required="sourceOfCase === 'Shirkad'"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Emailka</label>
                                    <input type="email" :name="'complainants['+i+'][email]'" x-model="c.email"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Cinwaanka</label>
                                    <textarea :name="'complainants['+i+'][address]'" x-model="c.address" rows="2"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Qareenka (haddii
                                        uu
                                        jiro)</label>
                                    <input type="text" :name="'complainants['+i+'][lawyer_name]'" x-model="c.lawyer_name"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka Shatiga
                                        Qareenka</label>
                                    <input type="text" :name="'complainants['+i+'][lawyer_license_no]'"
                                        x-model="c.lawyer_license_no"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                            </div>
                        </template>

                        {{-- Government Institution fields --}}
                        <template x-if="sourceOfCase === 'Hay\'ad Dawladeed'">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Hay'adda <span
                                            class="text-red-500">*</span></label>
                                    <select :name="'complainants['+i+'][full_name]'" x-model="c.full_name"
                                        :required="sourceOfCase === 'Hay\'ad Dawladeed'"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                        <option value="">Dooro Hay'adda</option>
                                        @foreach($institutions as $institution)
                                            <option value="{{ $institution->name }}">{{ $institution->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Sarkaalka</label>
                                    <input type="text" :name="'complainants['+i+'][representative_name]'"
                                        x-model="c.representative_name"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Jagada</label>
                                    <input type="text" :name="'complainants['+i+'][representative_title]'"
                                        x-model="c.representative_title"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Telefoonka <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" :name="'complainants['+i+'][phone_number]'" x-model="c.phone_number"
                                        inputmode="numeric" @input="c.phone_number = c.phone_number.replace(/\D/g,'')"
                                        :required="sourceOfCase === 'Hay\'ad Dawladeed'"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Emailka</label>
                                    <input type="email" :name="'complainants['+i+'][email]'" x-model="c.email"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Cinwaanka</label>
                                    <textarea :name="'complainants['+i+'][address]'" x-model="c.address" rows="2"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                                </div>
                            </div>
                        </template>

                        {{-- Baaris Xig fields --}}
                        <template x-if="sourceOfCase === 'Baaris Xig'">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Hay'ada <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" :name="'complainants['+i+'][full_name]'" x-model="c.full_name"
                                        :required="sourceOfCase === 'Baaris Xig'"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda <span
                                            class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="text" :name="'complainants['+i+'][date_of_birth]'"
                                            x-model="c.date_of_birth" x-init="window.iecmsDatePicker($el)" autocomplete="off"
                                            placeholder="dd/mm/yyyy" :required="sourceOfCase === 'Baaris Xig'"
                                            class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                        <i
                                            class="bi bi-calendar3 absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="flex justify-end">
                    <button type="button" @click="complainants.push(blankComplainant())"
                        class="px-4 py-2 text-xs font-bold text-white rounded-xl flex items-center gap-1.5"
                        style="background:#528CBE">
                        <i class="bi bi-plus-lg"></i> Ku Dar Mid Kale
                    </button>
                </div>
            </div>

            {{-- ═══ STEP 2: VICTIMS ═══ --}}
            <div x-show="step === 2" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
                <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-3">
                    <i class="bi bi-shield-exclamation" style="color:#528CBE"></i> II. XOGTA DHIBANAHA
                </h3>

                <template x-for="(v, i) in victims" :key="i">
                    <div class="bg-white rounded-xl border border-neutral-200 p-5 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold" style="color:#528CBE">Diiwaanka Dhibanaha</span>
                            <button type="button" @click="victims.splice(i, 1)"
                                class="text-xs font-semibold text-red-500 flex items-center gap-1"><i
                                    class="bi bi-trash3"></i> Ka Saar</button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">Nooca Dhibanaha <span
                                    class="text-red-500">*</span></label>
                            <select :name="'victims['+i+'][type]'" x-model="v.type" required
                                class="w-full sm:w-1/2 px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                <option value="">Dooro Nooca Dhibanaha</option>
                                <option value="Shakhsi">Shakhsi</option>
                                <option value="Qaranka">Qaranka</option>
                            </select>
                        </div>

                        <template x-if="v.type === 'Qaranka'">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Booxa <span
                                        class="text-red-500">*</span></label>
                                <input type="text" :name="'victims['+i+'][full_name]'" x-model="v.full_name" required
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Hay'adda Codsatay</label>
                                <input type="text" :name="'victims['+i+'][custodian_full_name]'"
                                    x-model="v.custodian_full_name"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" :name="'victims['+i+'][date_of_birth]'" x-model="v.date_of_birth"
                                        x-init="window.iecmsDatePicker($el)" autocomplete="off" placeholder="dd/mm/yyyy"
                                        required
                                        class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <i
                                        class="bi bi-calendar3 absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>
                        </template>

                        <template x-if="v.type === 'Shakhsi'">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Oo Dhan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" :name="'victims['+i+'][full_name]'" x-model="v.full_name" required
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Hooyada</label>
                                <input type="text" :name="'victims['+i+'][mother_name]'" x-model="v.mother_name"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda Dhalashada
                                    (haddii la ogyahay)</label>
                                <div class="relative">
                                    <input type="text" :name="'victims['+i+'][date_of_birth]'" x-model="v.date_of_birth"
                                        x-init="window.iecmsDatePicker($el)" autocomplete="off" placeholder="dd/mm/yyyy"
                                        class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <i
                                        class="bi bi-calendar3 absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
                                </div>
                                <template x-if="v.date_of_birth">
                                    <span
                                        class="inline-block mt-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold text-white"
                                        :class="ageCategoryColor(v.date_of_birth)"
                                        x-text="ageCategoryLabel(v.date_of_birth)"></span>
                                </template>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Jinsiga</label>
                                <select :name="'victims['+i+'][gender]'" x-model="v.gender"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <option value="">Dooro Jinsiga</option>
                                    <option value="Male">Lab</option>
                                    <option value="Female">Dheddig</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Gobolka
                                    (Deganaanshaha)</label>
                                <select :name="'victims['+i+'][region]'" x-model="v.region" @change="v.district = ''"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <option value="">Dooro Gobolka</option>
                                    <template x-for="r in regionOptions" :key="r">
                                        <option :value="r" x-text="r"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Degmada
                                    (Deganaanshaha)</label>
                                <select :name="'victims['+i+'][district]'" x-model="v.district"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <option value="">Dooro Degmada</option>
                                    <template x-for="c in citiesFor(v.region)" :key="c">
                                        <option :value="c" x-text="c"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Goobta Dhalashada</label>
                                <input type="text" :name="'victims['+i+'][place_of_birth]'" x-model="v.place_of_birth"
                                    placeholder="Magaalada/Tuulada dhalashada"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Cinwaanka</label>
                                <textarea :name="'victims['+i+'][address]'" x-model="v.address" rows="1"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka Taleefanka</label>
                                <input type="text" :name="'victims['+i+'][phone_number]'" x-model="v.phone_number"
                                    inputmode="numeric" @input="v.phone_number = v.phone_number.replace(/\D/g,'')"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka
                                    Aqoonsiga/Baasaboorka</label>
                                <input type="text" :name="'victims['+i+'][id_number]'" x-model="v.id_number"
                                    placeholder="Aqoonsi Qaran ama Lambarka Baasaboorka"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Faahfaahin
                                    Dheeraad ah</label>
                                <textarea :name="'victims['+i+'][impact_description]'" x-model="v.impact_description"
                                    rows="2" placeholder="Faahfaahin dheeraad ah oo ku saabsan dhibanaha"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                            </div>
                        </div>
                        </template>

                        <div x-show="isMinor(v.date_of_birth)" x-cloak class="mt-5 pt-5 border-t border-neutral-200">
                            <template x-if="ageCategory(v.date_of_birth) === 'under14'">
                                <div class="mb-4 p-3 rounded-xl text-sm" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Waxii 1–13: Wax Dambi Ah Lagama Qaadi Karo — Waxuu Ka Yar Yahay 14 Sano, Waxuu U Baahan Yahay Wakiil.
                                    Waxaa loo baahan yahay in Wakiil/Masuul (waalid ama qof mas'uul ah) buuxiyo xogta hoose halkii dambi loo qaadi lahaa.
                                </div>
                            </template>
                            <template x-if="ageCategory(v.date_of_birth) === 'under18'">
                                <div class="mb-4 p-3 rounded-xl text-sm" style="background:#fffbeb;color:#92400e;border:1px solid #fde68a">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Waxii 14–18: Wuxuu Ku Jiraa Xeerka Caruurta (Dhalinyarada) — waa in Wakiil/Masuul buuxiya xogta hoose.
                                </div>
                            </template>
                            <h4 class="flex items-center gap-2 text-sm font-bold text-neutral-700 mb-4">
                                <i class="bi bi-person-badge" style="color:#528CBE"></i> Xogta Masuulka/Waalidka
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Oo Dhan ee
                                        Masuulka <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'victims['+i+'][custodian_full_name]'"
                                        x-model="v.custodian_full_name" :required="isMinor(v.date_of_birth)"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Xiriirka la Ilmaha
                                        <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-2">
                                        <select :name="'victims['+i+'][custodian_relationship]'"
                                            x-model="v.custodian_relationship" :required="isMinor(v.date_of_birth)"
                                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                            <option value="">Dooro Xiriirka</option>
                                            <template x-for="type in relationshipTypes" :key="type.id">
                                                <option :value="type.name" x-text="type.name"></option>
                                            </template>
                                        </select>
                                        <button type="button" @click="openRelationshipModal('victim', i)"
                                            title="Ku Dar Xiriir Cusub"
                                            class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border"
                                            style="border-color:#528CBE;color:#528CBE">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka Aqoonsiga
                                        Masuulka</label>
                                    <input type="text" :name="'victims['+i+'][custodian_id_number]'"
                                        x-model="v.custodian_id_number"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka
                                        Taleefanka Masuulka <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'victims['+i+'][custodian_phone_number]'" inputmode="numeric"
                                        x-model="v.custodian_phone_number"
                                        @input="v.custodian_phone_number = v.custodian_phone_number.replace(/\D/g,'')"
                                        :required="isMinor(v.date_of_birth)"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Cinwaanka
                                        Masuulka</label>
                                    <textarea :name="'victims['+i+'][custodian_address]'" x-model="v.custodian_address"
                                        rows="2"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Emailka Masuulka
                                        (Ikhtiyaari)</label>
                                    <input type="email" :name="'victims['+i+'][custodian_email]'"
                                        x-model="v.custodian_email" placeholder="Ikhtiyaari"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end">
                    <button type="button" @click="victims.push(blankVictim())"
                        class="px-4 py-2 text-xs font-bold text-white rounded-xl flex items-center gap-1.5"
                        style="background:#528CBE">
                        <i class="bi bi-plus-lg"></i> Ku Dar Dhibane
                    </button>
                </div>
            </div>

            {{-- ═══ STEP 3: ACCUSED ═══ --}}
            <div x-show="step === 3" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
                <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-3">
                    <i class="bi bi-person-fill-exclamation" style="color:#528CBE"></i> III. XOGTA EEDEYSANAHA
                </h3>

                <template x-for="(a, i) in accused" :key="i">
                    <div class="bg-white rounded-xl border border-neutral-200 p-5 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold" style="color:#528CBE">Diiwaanka Eedeysanaha</span>
                            <button type="button" @click="accused.splice(i, 1)"
                                class="text-xs font-semibold text-red-500 flex items-center gap-1"><i
                                    class="bi bi-trash3"></i> Ka Saar</button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Oo Dhan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" :name="'accused['+i+'][full_name]'" x-model="a.full_name" required
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Hooyada</label>
                                <input type="text" :name="'accused['+i+'][mother_name]'" x-model="a.mother_name"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda Dhalashada
                                    (haddii la ogyahay)</label>
                                <div class="relative">
                                    <input type="text" :name="'accused['+i+'][date_of_birth]'" x-model="a.date_of_birth"
                                        x-init="window.iecmsDatePicker($el)" autocomplete="off" placeholder="dd/mm/yyyy"
                                        class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <i
                                        class="bi bi-calendar3 absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
                                </div>
                                <template x-if="a.date_of_birth">
                                    <span
                                        class="inline-block mt-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold text-white"
                                        :class="ageCategoryColor(a.date_of_birth)"
                                        x-text="ageCategoryLabel(a.date_of_birth)"></span>
                                </template>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Jinsiga</label>
                                <select :name="'accused['+i+'][gender]'" x-model="a.gender"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <option value="">Dooro Jinsiga</option>
                                    <option value="Male">Lab</option>
                                    <option value="Female">Dheddig</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Gobolka
                                    (Deganaanshaha)</label>
                                <select :name="'accused['+i+'][region]'" x-model="a.region" @change="a.district = ''"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <option value="">Dooro Gobolka</option>
                                    <template x-for="r in regionOptions" :key="r">
                                        <option :value="r" x-text="r"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Degmada
                                    (Deganaanshaha)</label>
                                <select :name="'accused['+i+'][district]'" x-model="a.district"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <option value="">Dooro Degmada</option>
                                    <template x-for="c in citiesFor(a.region)" :key="c">
                                        <option :value="c" x-text="c"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Goobta Dhalashada</label>
                                <input type="text" :name="'accused['+i+'][place_of_birth]'" x-model="a.place_of_birth"
                                    placeholder="Magaalada/Tuulada dhalashada"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Cinwaanka</label>
                                <textarea :name="'accused['+i+'][address]'" x-model="a.address" rows="1"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka Taleefanka</label>
                                <input type="text" :name="'accused['+i+'][phone_number]'" x-model="a.phone_number"
                                    inputmode="numeric" @input="a.phone_number = a.phone_number.replace(/\D/g,'')"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka
                                    Aqoonsiga/Baasaboorka</label>
                                <input type="text" :name="'accused['+i+'][id_number]'" x-model="a.id_number"
                                    placeholder="Aqoonsi Qaran ama Lambarka Baasaboorka"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Faahfaahin
                                    Dheeraad ah</label>
                                <textarea :name="'accused['+i+'][additional_details]'" x-model="a.additional_details"
                                    rows="2" placeholder="Faahfaahin dheeraad ah oo ku saabsan eedeysanaha"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                            </div>
                        </div>

                        <div x-show="isMinor(a.date_of_birth)" x-cloak class="mt-5 pt-5 border-t border-neutral-200">
                            <template x-if="ageCategory(a.date_of_birth) === 'under14'">
                                <div class="mb-4 p-3 rounded-xl text-sm" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Waxii 1–13: Wax Dambi Ah Lagama Qaadi Karo — Waxuu Ka Yar Yahay 14 Sano, Waxuu U Baahan Yahay Wakiil.
                                    Waxaa loo baahan yahay in Wakiil/Masuul (waalid ama qof mas'uul ah) buuxiyo xogta hoose halkii dambi loo qaadi lahaa.
                                </div>
                            </template>
                            <template x-if="ageCategory(a.date_of_birth) === 'under18'">
                                <div class="mb-4 p-3 rounded-xl text-sm" style="background:#fffbeb;color:#92400e;border:1px solid #fde68a">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Waxii 14–18: Wuxuu Ku Jiraa Xeerka Caruurta (Dhalinyarada) — waa in Wakiil/Masuul buuxiya xogta hoose.
                                </div>
                            </template>
                            <h4 class="flex items-center gap-2 text-sm font-bold text-neutral-700 mb-4">
                                <i class="bi bi-person-badge" style="color:#528CBE"></i> Xogta Masuulka/Waalidka
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Oo Dhan ee
                                        Masuulka <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'accused['+i+'][custodian_full_name]'"
                                        x-model="a.custodian_full_name" :required="isMinor(a.date_of_birth)"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Xiriirka la Ilmaha
                                        <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-2">
                                        <select :name="'accused['+i+'][custodian_relationship]'"
                                            x-model="a.custodian_relationship" :required="isMinor(a.date_of_birth)"
                                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                            <option value="">Dooro Xiriirka</option>
                                            <template x-for="type in relationshipTypes" :key="type.id">
                                                <option :value="type.name" x-text="type.name"></option>
                                            </template>
                                        </select>
                                        <button type="button" @click="openRelationshipModal('accused', i)"
                                            title="Ku Dar Xiriir Cusub"
                                            class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border"
                                            style="border-color:#528CBE;color:#528CBE">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka Aqoonsiga
                                        Masuulka</label>
                                    <input type="text" :name="'accused['+i+'][custodian_id_number]'"
                                        x-model="a.custodian_id_number"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka
                                        Taleefanka Masuulka <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'accused['+i+'][custodian_phone_number]'" inputmode="numeric"
                                        x-model="a.custodian_phone_number"
                                        @input="a.custodian_phone_number = a.custodian_phone_number.replace(/\D/g,'')"
                                        :required="isMinor(a.date_of_birth)"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Cinwaanka
                                        Masuulka</label>
                                    <textarea :name="'accused['+i+'][custodian_address]'" x-model="a.custodian_address"
                                        rows="2"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Emailka Masuulka
                                        (Ikhtiyaari)</label>
                                    <input type="email" :name="'accused['+i+'][custodian_email]'"
                                        x-model="a.custodian_email" placeholder="Ikhtiyaari"
                                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end">
                    <button type="button" @click="accused.push(blankAccused())"
                        class="px-4 py-2 text-xs font-bold text-white rounded-xl flex items-center gap-1.5"
                        style="background:#528CBE">
                        <i class="bi bi-plus-lg"></i> Ku Dar Eedeysane
                    </button>
                </div>
            </div>

            {{-- ═══ STEP 4: INCIDENT ═══ --}}
            <div x-show="step === 4" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
                <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-3">
                    <i class="bi bi-geo-alt-fill" style="color:#528CBE"></i> IV. FAAHFAAHINTA DHACDADA
                </h3>
                <div class="bg-white rounded-xl border border-neutral-200 p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda Dhacdada <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="incident_date" x-model="incidentDate"
                                    x-init="window.iecmsDatePicker($el)" autocomplete="off" placeholder="dd/mm/yyyy"
                                    required
                                    class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                <i
                                    class="bi bi-calendar3 absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">Waqtiga Dhacdada</label>
                            <div class="relative">
                                <input type="text" name="incident_time" x-init="window.iecmsTimePicker($el)"
                                    autocomplete="off" placeholder="--:--"
                                    value="{{ old('incident_time', $isEdit ? $case->incident_time : '') }}"
                                    class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                <i
                                    class="bi bi-clock absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">Goobta <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="incident_location" required
                                value="{{ old('incident_location', $isEdit ? $case->incident_location : '') }}"
                                class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Sharaxaada Dhacdada <span
                                class="text-red-500">*</span></label>
                        <textarea name="summary" required rows="4"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none">{{ old('summary', $isEdit ? $case->summary : '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ═══ STEP 5: CRIME ═══ --}}
            <div x-show="step === 5" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
                <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-3">
                    <i class="bi bi-journal-bookmark-fill" style="color:#528CBE"></i> V. NOOCA DEMBIGA
                </h3>
                <div class="bg-white rounded-xl border border-neutral-200 p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div x-data="{ open: false }" @click.outside="open = false">
                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">Nooca Dembiga La Eedeeyay <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <div @click="open = true; $refs.offenseSearch.focus()"
                                    class="w-full min-h-[42px] px-2.5 py-1.5 text-sm border border-neutral-200 rounded-xl flex flex-wrap gap-1.5 items-center cursor-text bg-white">
                                    <template x-for="type in selectedOffenseTypes" :key="type">
                                        <span class="inline-flex items-center gap-1.5 pl-2.5 pr-1.5 py-1 rounded-full text-xs font-semibold"
                                            style="background:rgba(82,140,190,0.12);color:#528CBE">
                                            <span x-text="type"></span>
                                            <button type="button" @click.stop="selectedOffenseTypes = selectedOffenseTypes.filter(t => t !== type)"
                                                class="w-4 h-4 flex items-center justify-center rounded-full hover:bg-white/60">
                                                <i class="bi bi-x-lg" style="font-size:9px"></i>
                                            </button>
                                        </span>
                                    </template>
                                    <input x-ref="offenseSearch" type="text" x-model="offenseSearch" @focus="open = true"
                                        placeholder="Raadi oo dooro nooca dembiga..."
                                        class="flex-1 min-w-[140px] text-sm outline-none border-none p-1">
                                </div>
                                <div x-show="open" x-cloak
                                    class="absolute z-20 mt-1 w-full max-h-64 overflow-y-auto bg-white border border-neutral-200 rounded-xl shadow-lg">
                                    <template x-for="type in filteredOffenseSubcases()" :key="type">
                                        <button type="button" @click="toggleOffenseType(type)"
                                            class="w-full text-left px-3.5 py-2 text-sm hover:bg-neutral-50 flex items-center justify-between"
                                            :class="selectedOffenseTypes.includes(type) ? 'text-primary font-semibold' : 'text-neutral-700'"
                                            style="background:transparent">
                                            <span x-text="type" class="truncate pr-2"></span>
                                            <i class="bi bi-check-lg shrink-0" x-show="selectedOffenseTypes.includes(type)" style="color:#528CBE"></i>
                                        </button>
                                    </template>
                                    <div x-show="filteredOffenseSubcases().length === 0" class="px-3.5 py-3 text-xs text-neutral-400 text-center">
                                        Wax lama helin
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="offense_type" :value="selectedOffenseTypes.join(', ')">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">Sharciga La Xadgudbay
                                (haddii la ogyahay)</label>
                            <template x-for="(lp, i) in legalProvisions" :key="i">
                                <div class="flex items-center gap-2 mb-2">
                                    <select :name="'legal_provisions['+i+'][provision]'" x-model="lp.provision"
                                        class="flex-1 min-w-0 px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                        <option value="">Dooro Qodobka</option>
                                        @foreach($articles as $article)
                                            <option value="{{ $article->rule }} – {{ $article->description }}">{{ $article->rule }} – {{ $article->description }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" @click="legalProvisions.push(blankLegalProvision())"
                                        title="Ku Dar Qodob Kale"
                                        class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border"
                                        style="border-color:#528CBE;color:#528CBE">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                    <button type="button" @click="legalProvisions.splice(i, 1)" title="Ka Saar"
                                        class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border border-red-200 text-red-500">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">Mudnaanta <span
                                    class="text-red-500">*</span></label>
                            @php $selectedPriority = old('priority', $isEdit ? $case->priority : ''); @endphp
                            <select name="priority" required
                                class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                <option value="">Dooro</option>
                                @foreach($priorities as $p)
                                    <option value="{{ $p }}" {{ $selectedPriority === $p ? 'selected' : '' }}>
                                        {{ $p }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ STEP 6: EVIDENCE ═══ --}}
            <div x-show="step === 6" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
                <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-3">
                    <i class="bi bi-archive-fill" style="color:#528CBE"></i> VI. CADDAYNTA
                </h3>

                <template x-for="(ev, i) in evidence" :key="i">
                    <div class="bg-white rounded-xl border border-neutral-200 p-5 mb-4">
                        <input type="hidden" :name="'evidence['+i+'][existing_id]'" :value="ev.existing_id || ''">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold" style="color:#528CBE">Caddayn</span>
                            <button type="button" @click="evidence.splice(i, 1)"
                                class="text-xs font-semibold text-red-500 flex items-center gap-1"><i
                                    class="bi bi-trash3"></i> Ka Saar</button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Nooca Caddaynta</label>
                                <input type="text" :name="'evidence['+i+'][evidence_type]'" x-model="ev.evidence_type"
                                    placeholder="tusaale: Dukumeenti, Jireed, Dhijitaal, Markhaati, iwm."
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda La
                                    Ururiyay</label>
                                <div class="relative">
                                    <input type="text" :name="'evidence['+i+'][collected_date]'" x-model="ev.collected_date"
                                        x-init="window.iecmsDatePicker($el)" autocomplete="off" placeholder="dd/mm/yyyy"
                                        class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <i
                                        class="bi bi-calendar3 absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">lifaaq</label>
                                <input type="file" :name="'evidence['+i+'][file]'"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                <template x-if="ev.existing_file_url">
                                    <a :href="ev.existing_file_url" target="_blank"
                                        class="mt-1.5 inline-flex items-center gap-1 text-xs font-semibold"
                                        style="color:#528CBE">
                                        <i class="bi bi-paperclip"></i> Lifaaqa Hore
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end">
                    <button type="button" @click="evidence.push(blankEvidence())"
                        class="px-4 py-2 text-xs font-bold text-white rounded-xl flex items-center gap-1.5"
                        style="background:#528CBE">
                        <i class="bi bi-plus-lg"></i> Ku Dar Caddayn
                    </button>
                </div>
            </div>

            {{-- ═══ STEP 7: WITNESSES ═══ --}}
            <div x-show="step === 7" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
                <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-3">
                    <i class="bi bi-people-fill" style="color:#528CBE"></i> VII. MARKHAATIYAASHA
                </h3>

                <template x-for="(w, i) in witnesses" :key="i">
                    <div class="bg-white rounded-xl border border-neutral-200 p-5 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold" style="color:#528CBE">Diiwaanka Markhaatiga</span>
                            <button type="button" @click="witnesses.splice(i, 1)"
                                class="text-xs font-semibold text-red-500 flex items-center gap-1"><i
                                    class="bi bi-trash3"></i> Ka Saar</button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Oo Dhan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" :name="'witnesses['+i+'][full_name]'" x-model="w.full_name" required
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Hooyada</label>
                                <input type="text" :name="'witnesses['+i+'][mother_name]'" x-model="w.mother_name"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda Dhalashada
                                    (haddii la ogyahay)</label>
                                <div class="relative">
                                    <input type="text" :name="'witnesses['+i+'][date_of_birth]'" x-model="w.date_of_birth"
                                        x-init="window.iecmsDatePicker($el)" autocomplete="off" placeholder="dd/mm/yyyy"
                                        class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <i
                                        class="bi bi-calendar3 absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Jinsiga</label>
                                <select :name="'witnesses['+i+'][gender]'" x-model="w.gender"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <option value="">Dooro Jinsiga</option>
                                    <option value="Male">Lab</option>
                                    <option value="Female">Dheddig</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Gobolka
                                    (Deganaanshaha)</label>
                                <select :name="'witnesses['+i+'][region]'" x-model="w.region" @change="w.district = ''"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <option value="">Dooro Gobolka</option>
                                    <template x-for="r in regionOptions" :key="r">
                                        <option :value="r" x-text="r"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Degmada
                                    (Deganaanshaha)</label>
                                <select :name="'witnesses['+i+'][district]'" x-model="w.district"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <option value="">Dooro Degmada</option>
                                    <template x-for="c in citiesFor(w.region)" :key="c">
                                        <option :value="c" x-text="c"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Goobta Dhalashada</label>
                                <input type="text" :name="'witnesses['+i+'][place_of_birth]'" x-model="w.place_of_birth"
                                    placeholder="Magaalada/Tuulada dhalashada"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka Taleefanka</label>
                                <input type="text" :name="'witnesses['+i+'][phone_number]'" x-model="w.phone_number"
                                    inputmode="numeric" @input="w.phone_number = w.phone_number.replace(/\D/g,'')"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Lambarka
                                    Aqoonsiga/Baasaboorka</label>
                                <input type="text" :name="'witnesses['+i+'][id_number]'" x-model="w.id_number"
                                    placeholder="Aqoonsi Qaran ama Lambarka Baasaboorka"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Jinsiyadda</label>
                                <select :name="'witnesses['+i+'][nationality]'" x-model="w.nationality"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                    <option value="">Dooro Jinsiyadda</option>
                                    <template x-for="country in countries" :key="country">
                                        <option :value="country" x-text="country"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Shaqada</label>
                                <input type="text" :name="'witnesses['+i+'][occupation]'" x-model="w.occupation"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">Cinwaanka</label>
                                <input type="text" :name="'witnesses['+i+'][address]'" x-model="w.address"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">faallo</label>
                                <textarea :name="'witnesses['+i+'][statement]'" x-model="w.statement" rows="3"
                                    class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end">
                    <button type="button" @click="witnesses.push(blankWitness())"
                        class="px-4 py-2 text-xs font-bold text-white rounded-xl flex items-center gap-1.5"
                        style="background:#528CBE">
                        <i class="bi bi-plus-lg"></i> Ku Dar Markhaati
                    </button>
                </div>
            </div>

            {{-- ═══ STEP 8: RELIEF ═══ --}}
            <div x-show="step === 8" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
                <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-3">
                    <i class="bi bi-hand-index-thumb-fill" style="color:#528CBE"></i> VIII. CODSIGA LA RABO
                </h3>
                <div class="bg-white rounded-xl border border-neutral-200 p-5">
                    <label class="block text-xs font-bold text-neutral-500 mb-1.5">Waa maxay ficilka ama xalka la
                        codsanayo?
                        <span class="text-red-500">*</span></label>
                    <textarea name="relief_sought" required rows="5"
                        placeholder="Sharax natiijada uu cabashada leh doonayo…"
                        class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none">{{ old('relief_sought', $isEdit ? $case->relief_sought : '') }}</textarea>
                </div>
            </div>

            {{-- ═══ STEP 9: DECLARATION ═══ --}}
            <div x-show="step === 9" x-cloak class="bg-neutral-50 rounded-2xl border border-neutral-100 p-5">
                <h3 class="flex items-center gap-2 font-bold text-neutral-800 mb-3">
                    <i class="bi bi-pen-fill" style="color:#528CBE"></i> IX. DHAMAAD IYO DHAARTA
                </h3>
                <div class="bg-white rounded-xl border border-neutral-200 p-5">
                    <div class="rounded-xl p-4 mb-5 text-sm" style="background:rgba(82,140,190,0.08);color:#1e4a73">
                        <strong>Qirasho:</strong> Anigoo saxeexaya hoos, waxaan qirtay in xogta lagu bixiyay
                        cabashadan ay run tahay oo sax ah ilaa aqoontayda shakhsi ahaaneed iyo/ama ay taageerayaan
                        dukumeenti la xiriira.
                    </div>

                    <input type="checkbox" name="declaration_accepted" value="1" required checked class="hidden">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">Saxeexa Sarkaalka
                                Sharciga/Noteerka/Sarkaalka La Fasaxay <span class="text-red-500">*</span></label>
                            <div id="declaration-sig-wrap" class="sig-pad-wrap">
                                <canvas id="declaration-sig-canvas" height="90"></canvas>
                                <div class="sig-pad-actions">
                                    <span class="sig-pad-hint">Halkan ku saxeex</span>
                                    <button type="button" class="sig-clear-btn" onclick="clearDeclarationSig()">
                                        <i class="bi bi-eraser"></i> Nadiifi
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="declaration_signature_data" id="declaration-sig-data">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">Taariikhda <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="declaration_date" x-init="window.iecmsDatePicker($el)"
                                    autocomplete="off" placeholder="dd/mm/yyyy" required
                                    value="{{ old('declaration_date', $isEdit ? \Carbon\Carbon::parse($case->declaration_date)->format('Y-m-d') : date('Y-m-d')) }}"
                                    class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-neutral-200 rounded-xl">
                                <i
                                    class="bi bi-calendar3 absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca iyo Jagada <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="declaration_signed_by" required
                                value="{{ old('declaration_signed_by', $isEdit ? $case->declaration_signed_by : ($currentEmployee ? trim($currentEmployee->EmpName . ' – ' . $currentEmployee->Position, ' –') : '')) }}"
                                placeholder="tusaale: Maxamed Cabdiaziis – Karraaniga Diiwaanka"
                                class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                        </div>

                    </div>
                </div>
            </div>

            {{-- Footer navigation --}}
            <div class="flex items-center justify-between mt-6 pt-5 border-t border-neutral-200">
                <a href="{{ route('attorney-cases.index') }}"
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
                        <i class="bi bi-check-circle-fill"></i> {{ $isEdit ? 'Kaydi Isbeddellada' : 'Gudbi Cabashada' }}
                    </button>
                </div>
            </div>
        </form>

        {{-- ═══ ADD RELATIONSHIP TYPE MODAL ═══ --}}
        <div x-show="showRelationshipModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @keydown.escape.window="showRelationshipModal = false">
            <div @click.outside="showRelationshipModal = false"
                class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 text-white" style="background:#528CBE">
                    <span class="font-bold flex items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i> Ku Dar Nooc Xiriir Cusub
                    </span>
                    <button type="button" @click="showRelationshipModal = false" class="text-white/90 hover:text-white">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Xiriirka <span
                                class="text-red-500">*</span></label>
                        <input type="text" x-model="newRelationshipName" placeholder="tusaale: Ilmo-korosho, Waalidkii Kale"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                        <p class="text-xs text-red-500 mt-1" x-show="relationshipModalError"
                            x-text="relationshipModalError"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Faahfaahin (Ikhtiyaari)</label>
                        <textarea x-model="newRelationshipDescription" rows="2"
                            placeholder="Faahfaahin kooban oo ku saabsan xiriirkan"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-neutral-100">
                    <button type="button" @click="showRelationshipModal = false"
                        class="px-4 py-2 text-xs font-bold text-neutral-600 bg-neutral-100 rounded-xl">
                        Jooji
                    </button>
                    <button type="button" @click="saveRelationshipType()"
                        class="px-4 py-2 text-xs font-bold text-white rounded-xl flex items-center gap-1.5"
                        style="background:#528CBE">
                        <i class="bi bi-save"></i> Kaydi Xiriirka
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══ ADD SOURCE TYPE MODAL ═══ --}}
        <div x-show="showSourceTypeModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @keydown.escape.window="showSourceTypeModal = false">
            <div @click.outside="showSourceTypeModal = false"
                class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 text-white" style="background:#528CBE">
                    <span class="font-bold flex items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i> Ku Dar Isha Cusub
                    </span>
                    <button type="button" @click="showSourceTypeModal = false" class="text-white/90 hover:text-white">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Magaca Isha <span
                                class="text-red-500">*</span></label>
                        <input type="text" x-model="newSourceTypeName" placeholder="tusaale: NGO, Ururka Caalamiga ah"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl">
                        <p class="text-xs text-red-500 mt-1" x-show="sourceTypeModalError" x-text="sourceTypeModalError">
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">Faahfaahin (Ikhtiyaari)</label>
                        <textarea x-model="newSourceTypeDescription" rows="2"
                            placeholder="Faahfaahin kooban oo ku saabsan ishan"
                            class="w-full px-3.5 py-2.5 text-sm border border-neutral-200 rounded-xl resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-neutral-100">
                    <button type="button" @click="showSourceTypeModal = false"
                        class="px-4 py-2 text-xs font-bold text-neutral-600 bg-neutral-100 rounded-xl">
                        Jooji
                    </button>
                    <button type="button" @click="saveSourceType()"
                        class="px-4 py-2 text-xs font-bold text-white rounded-xl flex items-center gap-1.5"
                        style="background:#528CBE">
                        <i class="bi bi-save"></i> Kaydi Isha
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function complaintWizard() {
                return {
                    step: 1,
                    totalSteps: 9,
                    steps: ['Cabashada', 'Dhibanayaasha', 'Eedeysanayaasha', 'Dhacdada', 'Dembiga', 'Caddaynta', 'Markhaatiyaasha', 'Codsiga', 'Dhamaad'],
                    offenseSubcases: @json($offenseSubcases),
                    offenseSubcaseArticles: @json($offenseSubcaseArticles),
                    selectedOffenseTypes: [],
                    offenseSearch: '',
                    filteredOffenseSubcases() {
                        const q = this.offenseSearch.trim().toLowerCase();
                        const list = q ? this.offenseSubcases.filter(t => t.toLowerCase().includes(q)) : this.offenseSubcases;
                        return list.slice(0, 50);
                    },
                    toggleOffenseType(type) {
                        const willAdd = !this.selectedOffenseTypes.includes(type);
                        if (willAdd) {
                            this.selectedOffenseTypes.push(type);
                        } else {
                            this.selectedOffenseTypes = this.selectedOffenseTypes.filter(t => t !== type);
                        }
                        this.offenseSearch = '';

                        // Sub-case and article share the same case_categories row — keep them in sync.
                        const provision = this.offenseSubcaseArticles[type];
                        if (!provision) return;

                        if (willAdd) {
                            if (!this.legalProvisions.some(lp => lp.provision === provision)) {
                                const emptyRow = this.legalProvisions.find(lp => !lp.provision);
                                if (emptyRow) {
                                    emptyRow.provision = provision;
                                } else {
                                    this.legalProvisions.push({ provision });
                                }
                            }
                        } else {
                            this.legalProvisions = this.legalProvisions.filter(lp => lp.provision !== provision);
                            if (this.legalProvisions.length === 0) {
                                this.legalProvisions.push(this.blankLegalProvision());
                            }
                        }
                    },
                    countries: [
                        'Somalia', 'Kenya', 'Ethiopia', 'Djibouti', 'Uganda', 'Yemen', 'United Arab Emirates', 'Saudi Arabia', 'United Kingdom', 'United States',
                        'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan',
                        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia',
                        'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada',
                        'Cape Verde', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo (DRC)', 'Congo (Republic)', 'Costa Rica',
                        "Cote d'Ivoire", 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt',
                        'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Fiji', 'Finland', 'France', 'Gabon', 'Gambia',
                        'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti',
                        'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy',
                        'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kiribati', 'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon',
                        'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives',
                        'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia',
                        'Montenegro', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua',
                        'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Palestine', 'Panama',
                        'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda',
                        'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone',
                        'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'South Africa', 'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan',
                        'Suriname', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo',
                        'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Ukraine', 'Uruguay', 'Uzbekistan', 'Vanuatu',
                        'Vatican City', 'Venezuela', 'Vietnam', 'Zambia', 'Zimbabwe',
                    ],
                    regionOptions: @json($regions->pluck('state_name')),
                    citiesByRegion: @json($regions->mapWithKeys(fn($r) => [$r->state_name => $r->cities->pluck('city_name')])),
                    citiesFor(regionName) {
                        return this.citiesByRegion[regionName] || [];
                    },
                    relationshipTypes: @json($relationshipTypes->map(fn($t) => ['id' => $t->id, 'name' => $t->name])),
                    showRelationshipModal: false,
                    activeRelationshipTarget: null,
                    newRelationshipName: '',
                    newRelationshipDescription: '',
                    relationshipModalError: '',
                    sourceTypes: @json($sourceTypes->map(fn($t) => ['id' => $t->id, 'name' => $t->name])),
                    showSourceTypeModal: false,
                    newSourceTypeName: '',
                    newSourceTypeDescription: '',
                    sourceTypeModalError: '',
                    incidentDate: '',
                    sourceOfCase: '',
                    complainants: [],
                    victims: [],
                    accused: [],
                    evidence: [],
                    witnesses: [],
                    legalProvisions: [],
                    typeLabel(type) {
                        // Legacy English values (pre-Somali-migration records) still map correctly; new records already store the Somali word, which passes through as-is via the fallback.
                        return { Individual: 'Shakhsi', Lawyer: 'Qareen', 'Government Agency': "Hay'ad Dawladeed", Company: 'Shirkad' }[type] || (type || 'Shakhsi');
                    },
                    blankComplainant() {
                        return { full_name: '', phone_number: '', email: '', date_of_birth: '', nationality: 'Somalia', occupation: '', address: '', license_number: '', representative_name: '', representative_title: '', lawyer_name: '', lawyer_license_no: '' };
                    },
                    blankVictim() {
                        return { type: '', full_name: '', mother_name: '', date_of_birth: '', gender: '', place_of_birth: '', district: '', region: 'Banaadir', address: '', phone_number: '', id_number: '', impact_description: '', custodian_full_name: '', custodian_relationship: '', custodian_id_number: '', custodian_phone_number: '', custodian_address: '', custodian_email: '' };
                    },
                    ageCategory(dob) {
                        if (!dob) return null;
                        const birth = new Date(dob);
                        if (isNaN(birth.getTime())) return null;
                        const today = new Date();
                        let age = today.getFullYear() - birth.getFullYear();
                        const m = today.getMonth() - birth.getMonth();
                        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
                        if (age < 0) return null;
                        if (age < 14) return 'under14';
                        if (age < 18) return 'under18';
                        return 'adult';
                    },
                    ageCategoryLabel(dob) {
                        return {
                            under14: 'Ka Yar 14 Sano — Wax Dambi Ah Lagama Qaadi Karo',
                            under18: '14–18 Sano — Wuxuu Ku Jiraa Xeerka Caruurta',
                            adult: 'Qaangaar',
                        }[this.ageCategory(dob)] || '';
                    },
                    ageCategoryColor(dob) {
                        return {
                            under14: 'bg-red-500',
                            under18: 'bg-amber-500',
                            adult: 'bg-emerald-500',
                        }[this.ageCategory(dob)] || 'bg-neutral-400';
                    },
                    isMinor(dob) {
                        const cat = this.ageCategory(dob);
                        return cat === 'under14' || cat === 'under18';
                    },
                    openRelationshipModal(type, i) {
                        this.activeRelationshipTarget = { type, index: i };
                        this.newRelationshipName = '';
                        this.newRelationshipDescription = '';
                        this.relationshipModalError = '';
                        this.showRelationshipModal = true;
                    },
                    saveRelationshipType() {
                        const name = this.newRelationshipName.trim();
                        if (!name) {
                            this.relationshipModalError = 'Fadlan geli magaca xiriirka.';
                            return;
                        }

                        fetch('{{ route('attorney-cases.relationship-types.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ name: name, description: this.newRelationshipDescription || null }),
                        })
                            .then(async (res) => {
                                if (!res.ok) {
                                    const body = await res.json().catch(() => ({}));
                                    const msg = body.errors ? Object.values(body.errors)[0][0] : 'Xiriirka lama kaydin karin.';
                                    throw new Error(msg);
                                }
                                return res.json();
                            })
                            .then((type) => {
                                this.relationshipTypes.push({ id: type.id, name: type.name });
                                const target = this.activeRelationshipTarget;
                                if (target) {
                                    const arr = target.type === 'accused' ? this.accused : this.victims;
                                    if (arr[target.index]) {
                                        arr[target.index].custodian_relationship = type.name;
                                    }
                                }
                                this.showRelationshipModal = false;
                            })
                            .catch((err) => {
                                this.relationshipModalError = err.message || 'Xiriirka lama kaydin karin.';
                            });
                    },
                    openSourceTypeModal() {
                        this.newSourceTypeName = '';
                        this.newSourceTypeDescription = '';
                        this.sourceTypeModalError = '';
                        this.showSourceTypeModal = true;
                    },
                    saveSourceType() {
                        const name = this.newSourceTypeName.trim();
                        if (!name) {
                            this.sourceTypeModalError = 'Fadlan geli magaca isha.';
                            return;
                        }

                        fetch('{{ route('attorney-cases.source-types.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ name: name, description: this.newSourceTypeDescription || null }),
                        })
                            .then(async (res) => {
                                if (!res.ok) {
                                    const body = await res.json().catch(() => ({}));
                                    const msg = body.errors ? Object.values(body.errors)[0][0] : 'Isha lama kaydin karin.';
                                    throw new Error(msg);
                                }
                                return res.json();
                            })
                            .then((type) => {
                                this.sourceTypes.push({ id: type.id, name: type.name });
                                this.sourceOfCase = type.name;
                                this.showSourceTypeModal = false;
                            })
                            .catch((err) => {
                                this.sourceTypeModalError = err.message || 'Isha lama kaydin karin.';
                            });
                    },
                    blankAccused() {
                        return { full_name: '', mother_name: '', date_of_birth: '', gender: '', place_of_birth: '', district: '', region: 'Banaadir', address: '', phone_number: '', id_number: '', additional_details: '', custodian_full_name: '', custodian_relationship: '', custodian_id_number: '', custodian_phone_number: '', custodian_address: '', custodian_email: '' };
                    },
                    blankEvidence() {
                        return { evidence_type: '', description: '', collected_date: '' };
                    },
                    blankWitness() {
                        return { full_name: '', mother_name: '', date_of_birth: '', gender: '', place_of_birth: '', district: '', region: 'Banaadir', id_number: '', nationality: 'Somalia', occupation: '', phone_number: '', address: '', statement: '' };
                    },
                    blankLegalProvision() {
                        return { provision: '' };
                    },
                    isEdit: @json($isEdit),
                    existingData: @json($existingWizardData),
                    errorStep: @json($errorStep ?? 0),
                    nextCaseNumbers: @json($nextCaseNumbers ?? []),
                    caseNumberGroup(type) {
                        if (type === "Hay'ad Dawladeed" || type === 'Government Agency') return 'HD';
                        if (type === 'Baaris Xig') return 'BXIG';
                        return 'WDD';
                    },
                    previewCaseNumber() {
                        return this.nextCaseNumbers[this.caseNumberGroup(this.sourceOfCase)] || '';
                    },
                    stepStorageKey: 'directComplaintWizardStep',
                    saveStep() {
                        if (this.isEdit) return;
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
                        // native form submission — clear the saved step so a new complaint starts fresh
                        sessionStorage.removeItem(this.stepStorageKey);
                    },
                    init() {
                        if (this.existingData) {
                            const d = this.existingData;
                            const fill = (rows, blank) => (rows && rows.length ? rows.map(r => Object.assign(blank(), r)) : [blank()]);
                            this.sourceOfCase = d.sourceOfCase || '';
                            this.selectedOffenseTypes = d.offenseType ? d.offenseType.split(',').map(t => t.trim()).filter(Boolean) : [];
                            this.incidentDate = d.incidentDate || '';
                            this.complainants = fill(d.complainants, this.blankComplainant.bind(this));
                            this.victims = fill(d.victims, this.blankVictim.bind(this));
                            this.accused = fill(d.accused, this.blankAccused.bind(this));
                            this.evidence = fill(d.evidence, this.blankEvidence.bind(this));
                            this.witnesses = fill(d.witnesses, this.blankWitness.bind(this));
                            this.legalProvisions = fill(d.legalProvisions, this.blankLegalProvision.bind(this));
                        } else {
                            this.complainants.push(this.blankComplainant());
                            this.victims.push(this.blankVictim());
                            this.accused.push(this.blankAccused());
                            this.evidence.push(this.blankEvidence());
                            this.witnesses.push(this.blankWitness());
                            this.legalProvisions.push(this.blankLegalProvision());
                            this.incidentDate = new Date().toISOString().slice(0, 10);
                        }

                        if (this.errorStep) {
                            this.step = this.errorStep;
                        } else if (!this.isEdit) {
                            const savedStep = parseInt(sessionStorage.getItem(this.stepStorageKey), 10);
                            if (!isNaN(savedStep) && savedStep >= 1 && savedStep <= this.totalSteps) {
                                this.step = savedStep;
                            }
                        }
                    }
                }
            }
        </script>

        <script>
            /* ══ Declaration Signature Pad ══ */
            (function () {
                let ctx, canvas, drawing = false, lastX = 0, lastY = 0;

                function pos(e) {
                    const r = canvas.getBoundingClientRect();
                    const scaleX = canvas.width / r.width;
                    const scaleY = canvas.height / r.height;
                    const src = e.touches ? e.touches[0] : e;
                    return [(src.clientX - r.left) * scaleX, (src.clientY - r.top) * scaleY];
                }
                function start(e) { e.preventDefault(); drawing = true;[lastX, lastY] = pos(e); }
                function draw(e) {
                    if (!drawing) return; e.preventDefault();
                    const [x, y] = pos(e);
                    ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(x, y); ctx.stroke();
                    [lastX, lastY] = [x, y];
                    document.getElementById('declaration-sig-wrap')?.classList.add('has-sig');
                }
                function stop() {
                    if (!drawing) return;
                    drawing = false;
                    saveDeclarationSig();
                }

                function saveDeclarationSig() {
                    const dataInput = document.getElementById('declaration-sig-data');
                    if (dataInput) dataInput.value = canvas.toDataURL('image/png');
                }

                window.clearDeclarationSig = function () {
                    if (!ctx) return;
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    document.getElementById('declaration-sig-wrap')?.classList.remove('has-sig');
                    const dataInput = document.getElementById('declaration-sig-data');
                    if (dataInput) dataInput.value = '';
                };

                document.addEventListener('DOMContentLoaded', function () {
                    canvas = document.getElementById('declaration-sig-canvas');
                    if (!canvas) return;
                    ctx = canvas.getContext('2d');

                    const rect = canvas.getBoundingClientRect();
                    canvas.width = rect.width || 300;
                    canvas.height = 90;
                    ctx.strokeStyle = '#1a4fa8'; ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.lineJoin = 'round';

                    canvas.addEventListener('mousedown', start);
                    canvas.addEventListener('mousemove', draw);
                    canvas.addEventListener('mouseup', stop);
                    canvas.addEventListener('mouseleave', stop);
                    canvas.addEventListener('touchstart', start, { passive: false });
                    canvas.addEventListener('touchmove', draw, { passive: false });
                    canvas.addEventListener('touchend', stop);

                    const existingSignatureUrl = @json($isEdit && $case->declaration_signature
                        ? asset('storage/' . $case->declaration_signature)
                    : ($currentEmployee && $currentEmployee->signature ? asset($currentEmployee->signature) : null));
                    if (existingSignatureUrl) {
                        const img = new Image();
                        img.crossOrigin = 'anonymous';
                        img.onload = function () {
                            const scale = Math.min(canvas.width / img.width, canvas.height / img.height, 1);
                            const w = img.width * scale;
                            const h = img.height * scale;
                            ctx.drawImage(img, (canvas.width - w) / 2, (canvas.height - h) / 2, w, h);
                            document.getElementById('declaration-sig-wrap')?.classList.add('has-sig');
                            saveDeclarationSig();
                        };
                        img.src = existingSignatureUrl;
                    }
                });
            })();
        </script>

        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const errorMessages = @json($errors->all());
                    Swal.fire({
                        title: 'Fadlan Hubi Foomka',
                        html: '<ul style="text-align:right;padding-right:1.25rem;margin:0;line-height:1.7;font-size:.85rem">' +
                            errorMessages.map(function (m) {
                                return '<li>' + String(m).replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</li>';
                            }).join('') +
                            '</ul>',
                        icon: 'error',
                        confirmButtonText: 'Hagaaji',
                        confirmButtonColor: '#528CBE',
                    });
                });
            </script>
        @endif
    @endpush
@endsection