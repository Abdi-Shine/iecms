@extends('admin.admin_master')
@section('page_title', 'Investigation Decision — ' . $case->case_number)
@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $isComplete = (bool) $case->investigationDecision;
        $subtitleParts = array_filter([$case->added_by, optional($case->complainants->first())->full_name]);
    @endphp

    <div class="p-4 sm:p-6 w-full" x-data="investigationDecisionForm">

        {{-- Case Header --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 mb-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->case_number }}</h1>
                    <p class="text-sm text-neutral-500 mt-0.5">{{ implode(' - ', $subtitleParts) ?: '—' }}</p>
                </div>
                <a href="{{ route('attorney-cases.workflow', $case->ACID) }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
                    <i class="bi bi-arrow-left"></i> Ku Laabo Workflow-ka
                </a>
            </div>

            <div class="flex items-center gap-6 mt-5 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Tallaabada:</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full text-white bg-primary-400">Investigation Decision</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Xaalada:</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full text-white {{ $isComplete ? 'bg-success-600' : 'bg-neutral-500' }}">
                        {{ $isComplete ? 'Dhammaystiran' : 'Sugaya' }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide">Foomamka:</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full text-white" style="background:#17a2b8">1 Loo Baahan Yahay</span>
                </div>
            </div>
        </div>

        {{-- Step Information --}}
        <div class="rounded-2xl p-5 mb-6 flex items-start gap-3" style="background:rgba(23,162,184,0.08);border:1px solid rgba(23,162,184,0.2)">
            <i class="bi bi-info-circle-fill text-lg" style="color:#17a2b8"></i>
            <div>
                <p class="text-sm font-bold text-neutral-800 mb-1">Faahfaahinta Tallaabada</p>
                <p class="text-sm text-neutral-600">Prosecutors decide: Is investigation needed?</p>
                <p class="text-sm text-neutral-600 mt-1"><strong>Foomamka Loo Baahan Yahay:</strong> 1 foom</p>
            </div>
        </div>

        {{-- Required Forms --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 mb-6">
            <h2 class="text-base font-bold text-neutral-800 mb-4">Foomamka Loo Baahan Yahay ee Investigation Decision</h2>

            <div class="rounded-xl border border-neutral-200 p-5 max-w-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                        <i class="bi bi-file-earmark-text text-lg" style="color:#528CBE"></i>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white {{ $isComplete ? 'bg-success-600' : 'bg-neutral-500' }}">
                        {{ $isComplete ? 'Dhammaystiran' : 'Sugaya' }}
                    </span>
                </div>
                <h3 class="text-sm font-bold text-neutral-800">Investigation Decision</h3>
                <p class="text-xs text-neutral-500 mt-1 mb-4">Decide whether investigation is required</p>
                <button type="button" @click="showForm = !showForm"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white rounded-xl transition-all bg-ago">
                    <i class="bi" :class="showForm ? 'bi-chevron-up' : 'bi-play-fill'"></i>
                    <span x-text="showForm ? 'Qari Foomka' : '{{ $isComplete ? "Eeg / Wax Ka Beddel Foomka" : "Start Form" }}'"></span>
                </button>
            </div>

            {{-- Actual Form (revealed on Start Form) --}}
            <div x-show="showForm" x-cloak x-transition class="mt-6 pt-6 border-t border-neutral-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">

                    {{-- Decision --}}
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Go'aanka <span class="text-danger-500">*</span>
                        </label>
                        <select x-model="form.decision"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro Go'aanka —</option>
                            @foreach($decisions as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Decision Date --}}
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Taariikhda Go'aanka <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" x-model="form.decision_date"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>

                    {{-- Reasoning --}}
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Sababta Go'aanka
                        </label>
                        <textarea x-model="form.reasoning" rows="4"
                            placeholder="Fadlan sharax sababta go'aankan..."
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="saveDecision" :disabled="isSaving"
                        class="flex items-center gap-2 px-8 py-2.5 text-white text-sm font-bold rounded-xl shadow hover:opacity-90 transition-all disabled:opacity-50 bg-ago">
                        <i class="bi bi-cloud-check-fill" x-show="!isSaving"></i>
                        <i class="bi bi-arrow-repeat animate-spin" x-show="isSaving"></i>
                        <span x-text="isSaving ? 'Waa La Shaqaynayaa...' : 'Keydi'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Generated Letters --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <i class="bi bi-file-earmark-pdf text-lg text-success-600"></i>
                    <div>
                        <p class="text-sm font-bold text-neutral-800">Waraaqaha La Sameeyay</p>
                        <p class="text-xs text-neutral-500">Waraaqaha gudbinta CID ee laga sameeyay go'aannada baaritaanka</p>
                    </div>
                </div>
                <button type="button" disabled
                    class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl opacity-50 cursor-not-allowed"
                    style="background:#198754" title="Weli lama horumarin">
                    <i class="bi bi-pencil-square"></i> Samee Waraaqo Foomamka La Gudbiyay
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-neutral-50">
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Waraaqda</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Lambarka Waraaqda</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Maxkamadda</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Kii Sameeyay</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-neutral-400">
                                <i class="bi bi-info-circle"></i> Waraaq weli lama samayn.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('investigationDecisionForm', () => ({
                isSaving: false,
                showForm: false,
                form: {
                    decision: {{ $case->investigationDecision ? "'" . addslashes($case->investigationDecision->decision) . "'" : "''" }},
                    reasoning: {{ $case->investigationDecision ? "'" . addslashes($case->investigationDecision->reasoning ?? '') . "'" : "''" }},
                    decision_date: {{ $case->investigationDecision ? "'" . $case->investigationDecision->decision_date->format('Y-m-d') . "'" : "'" . now()->format('Y-m-d') . "'" }}
                },

                async saveDecision() {
                    if (!this.form.decision || !this.form.decision_date) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Khalad Xaqiijin',
                            text: 'Fadlan buuxi go\'aanka iyo taariikhda.',
                            confirmButtonColor: '#528CBE',
                            confirmButtonText: 'Hagaag'
                        });
                        return;
                    }

                    this.isSaving = true;
                    try {
                        const res = await fetch('{{ route('attorney-cases.workflow.investigation-decision.store', $case->ACID) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });

                        if (!res.ok) {
                            const err = await res.json();
                            let msg = err.message || 'Kuma guuleysanin keydsiga';
                            if (err.errors) msg = Object.values(err.errors).flat().join('\n');
                            throw new Error(msg);
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Guul!',
                            text: 'Go\'aanka baaritaanka waa la keydiyay.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route('attorney-cases.workflow', $case->ACID) }}';
                        });

                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hawshu waa Fashilantay',
                            text: e.message,
                            confirmButtonColor: '#528CBE',
                            confirmButtonText: 'Hagaag'
                        });
                    } finally {
                        this.isSaving = false;
                    }
                }
            }));
        });
    </script>

@endsection
