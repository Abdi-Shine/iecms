@extends('admin.admin_master')
@section('page_title', 'Lawyer Assignment')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-wrap" x-data="lawyerManager">

        {{-- Page Header --}}
        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-person-badge-fill"></i></div>
                <div>
                    <h1 class="page-title">Qarakeen Ku daris</h1>
                    <p class="page-sub"><span class="page-sub-accent">{{ $case->FileNo }}</span></p>
                </div>
            </div>
            <a href="{{ route('family-registration.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> dib ugu noqo
            </a>
        </div>

        <datalist id="lawyer-options">
            @foreach($lawyers as $lawyer)
                <option value="{{ $lawyer->LawyerName }}">
            @endforeach
        </datalist>

        <form @submit.prevent="saveAssignments" enctype="multipart/form-data">

            <template x-for="(assign, index) in entries" :key="index">
                <div class="entry-card">

                    {{-- Card Header --}}
                    <div class="card-hd" :style="index % 2 === 0 ? 'background:#528CBE' : 'background:#0A284D'">
                        <div class="card-hd-left">
                            <div class="card-hd-icon"><i class="bi bi-person-badge"></i></div>
                            <div>
                                <h2 class="card-hd-title">Qarakeen Ku daris <span x-text="index + 1"></span></h2>
                            </div>
                        </div>
                        <button type="button" @click="removeEntry(index)" x-show="entries.length > 1"
                            class="card-hd-remove">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-bd">
                        <div class="form-row-4">

                            <div>
                                <label class="form-lbl">Dhinacyada <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <select x-model="assign.party_index" @change="onPartySelected(index)" required class="form-sel">
                                        <option value="">Dooro Dhinacyada...</option>
                                        <template x-for="(p, i) in parties" :key="p.PID">
                                            <option :value="i" x-text="(p.full_name || 'Dhinac ' + (i+1)) + ' — ' + (p.party_role || '?')"></option>
                                        </template>
                                    </select>
                                    <i class="bi bi-chevron-down inp-ico-xs"></i>
                                </div>
                            </div>

                            <div>
                                <label class="form-lbl">Dooro Qarakeen <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="assign.lawyerSearch" list="lawyer-options"
                                           @input="onLawyerSearchInput(index)" required
                                           placeholder="Raadi magaca qareenka..." class="form-inp">
                                    <i class="bi bi-search inp-ico-xs"></i>
                                </div>
                            </div>

                            <div>
                                <label class="form-lbl">Tarikhda <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <input type="date" x-model="assign.assignment_date" required class="form-inp">
                                    <i class="bi bi-calendar3 inp-ico"></i>
                                </div>
                            </div>

                            <div>
                                <label class="form-lbl">Qaraarka Ay u xil saaran yihiin</label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="assign.reason" placeholder="e.g., Lead Counsel"
                                        class="form-inp">
                                    <i class="bi bi-chat-square-text inp-ico"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </template>

            {{-- Form Actions --}}
            <div class="form-actions">
                <button type="button" @click="addEntry" class="btn-add">
                    <i class="bi bi-plus-lg"></i> Ku daro Qarakeen kale
                </button>
                <button type="submit" :disabled="isSaving" class="btn-save">
                    <span x-show="!isSaving" class="btn-icon-row">
                        <i class="bi bi-cloud-check-fill"></i> Keydi
                    </span>
                    <span x-show="isSaving" class="btn-icon-row">
                        <i class="bi bi-arrow-repeat cf-spin"></i> Keydi...
                    </span>
                </button>
            </div>

        </form>

        {{-- Active Assignments Summary --}}
        <div class="data-card">
            <div class="data-card-hd">
                <div class="data-card-hd-left">
                    <i class="bi bi-table"></i>
                    <span class="data-card-hd-title">Koobka Qareenada Firfircoon</span>
                </div>
                <span class="data-card-count" x-text="assignments.length + ' Wadarta'"></span>
            </div>

            <div class="data-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:3.5rem">#</th>
                            <th>Dhinacayada</th>
                            <th>Magaca Qareenka</th>
                            <th>Ruqsadda</th>
                            <th>Taariikhda</th>
                            <th class="center">Tallaabooyin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(assign, index) in assignments" :key="assign.id">
                            <tr>
                                <td><span class="td-num" x-text="index + 1"></span></td>
                                <td>
                                    <div class="party-tag-row">
                                        <span class="party-tag"
                                            :class="assign.party_role === 'Dacwoode' ? 'plaintiff' : 'defendant'"
                                            x-text="assign.party_role"></span>
                                        <span class="party-nm" x-text="assign.party ? assign.party.full_name : ''"></span>
                                    </div>
                                </td>
                                <td class="td-bold" x-text="assign.lawyer ? assign.lawyer.LawyerName : ''"></td>
                                <td class="td-upper" x-text="assign.lawyer ? assign.lawyer.LID : ''"></td>
                                <td class="td-date" x-text="assign.assignment_date"></td>
                                <td>
                                    <div class="td-actions">
                                        <button @click="editAssignment(assign)" title="Edit" class="act-btn act-btn-edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button @click="deleteAssignment(assign.id)" title="Remove"
                                            class="act-btn act-btn-del">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="assignments.length === 0">
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-ico"><i class="bi bi-person-x"></i></div>
                                        <p class="empty-msg">No lawyers assigned to this case yet.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="data-card-ft">
                <p>Muujinaya <span x-text="assignments.length"></span> <span
                        x-text="assignments.length === 1 ? 'xaaladood' : 'xaaladood'"></span></p>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('lawyerManager', () => ({
                caseId: {{ $case->FCID }},
                assignments: [],
                parties: [],
                isSaving: false,
                entries: [],
                lawyersList: @json($lawyers->map(fn($l) => ['id' => $l->LRID, 'name' => $l->LawyerName])),

                init() {
                    this.loadParties();
                    this.loadAssignments();
                    this.addEntry();
                },

                async loadParties() {
                    try {
                        const res = await fetch(`/family-case-parties/case/${this.caseId}`);
                        this.parties = await res.json();
                    } catch (e) { console.error(e); }
                },

                addEntry() {
                    this.entries.push({
                        id: null,
                        family_case_id: this.caseId,
                        lawyer_id: '',
                        lawyerSearch: '',
                        party_index: '',
                        party_id: null,
                        party_role: '',
                        assignment_date: new Date().toISOString().split('T')[0],
                        reason: ''
                    });
                },

                onPartySelected(index) {
                    const entry = this.entries[index];
                    const party = this.parties[entry.party_index];
                    if (party) {
                        entry.party_id = party.PID;
                        entry.party_role = party.party_role;
                    }
                },

                onLawyerSearchInput(index) {
                    const entry = this.entries[index];
                    const match = this.lawyersList.find(l => l.name === entry.lawyerSearch);
                    entry.lawyer_id = match ? match.id : '';
                },

                removeEntry(index) {
                    this.entries.splice(index, 1);
                    if (this.entries.length === 0) this.addEntry();
                },

                async loadAssignments() {
                    try {
                        const res = await fetch(`/family-case-lawyers/case/${this.caseId}`);
                        this.assignments = await res.json();
                    } catch (e) { console.error(e); }
                },

                editAssignment(assign) {
                    const lawyer = this.lawyersList.find(l => l.id == assign.lawyer_id);
                    const partyIdx = this.parties.findIndex(p => p.PID == assign.party_id);
                    this.entries = [{
                        id: assign.id,
                        family_case_id: assign.family_case_id,
                        lawyer_id: assign.lawyer_id,
                        lawyerSearch: lawyer ? lawyer.name : (assign.lawyer ? assign.lawyer.LawyerName : ''),
                        party_index: partyIdx >= 0 ? partyIdx : '',
                        party_id: assign.party_id,
                        party_role: assign.party_role,
                        assignment_date: assign.assignment_date,
                        reason: assign.reason
                    }];
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async saveAssignments() {
                    this.isSaving = true;
                    try {
                        for (let entry of this.entries) {
                            const url = entry.id ? `/family-case-lawyers/${entry.id}` : '/family-case-lawyers';
                            const bodyData = { ...entry };
                            if (entry.id) bodyData._method = 'PUT';

                            const res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(bodyData)
                            });
                            const data = await res.json();
                            if (!res.ok) {
                                if (data.errors) {
                                    let errorMsg = Object.values(data.errors).flat().join('<br>');
                                    throw new Error(errorMsg);
                                } else {
                                    throw new Error(data.message || 'Error saving assignment');
                                }
                            }
                        }

                        Swal.fire({ title: 'Guul!', text: 'Lawyer assignments saved successfully.', icon: 'success', confirmButtonColor: '#528CBE' });
                        this.entries = [];
                        this.addEntry();
                        this.loadAssignments();
                    } catch (e) {
                        Swal.fire({ title: 'Error', html: e.message, icon: 'error' });
                    } finally {
                        this.isSaving = false;
                    }
                },

                async deleteAssignment(id) {
                    const result = await Swal.fire({
                        title: 'Are you sure?', text: "Remove this lawyer from the case?", icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#DC2626', confirmButtonText: 'Yes, remove!'
                    });
                    if (result.isConfirmed) {
                        try {
                            const res = await fetch(`/family-case-lawyers/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                            });
                            if (res.ok) {
                                Swal.fire('Removed!', 'Lawyer has been unassigned.', 'success');
                                this.loadAssignments();
                            }
                        } catch (e) { console.error(e); }
                    }
                }
            }));
        });
    </script>

@endsection