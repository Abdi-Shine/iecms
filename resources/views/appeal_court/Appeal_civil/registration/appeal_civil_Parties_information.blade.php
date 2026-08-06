@extends('admin.admin_master')
@section('page_title', 'Parties Information')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-wrap" x-data="partiesManager">

        {{-- Page Header --}}
        <div class="page-hd">
            <div>
                <h1 class="page-title">Dhinacyada Macluumadkoda </h1>
                <p class="page-sub">
                    <span class="page-sub-accent">{{ $case->FileNo }}</span>
                </p>
            </div>
            <a href="{{ route('appeal-civil-registration.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Dib U Noqo
            </a>
        </div>

        <form @submit.prevent="saveParties" enctype="multipart/form-data">

            <template x-for="(party, index) in parties" :key="index">
                <div :id="'party-card-'+index" class="entry-card">

                    {{-- Card Header (clickable to expand/collapse) --}}
                    <div class="card-hd" :style="index % 2 === 0 ? 'background:#528CBE;cursor:pointer' : 'background:#0A284D;cursor:pointer'"
                        @click="party.isOpen = !party.isOpen">
                        <div class="card-hd-left">
                            <div class="card-hd-icon"><i class="bi bi-person-fill"></i></div>
                            <div>
                                <h2 class="card-hd-title">Dhinacyada <span x-text="index + 1"></span>
                                    <template x-if="party.full_name">
                                        <span style="font-weight:400;opacity:.85"> — <span x-text="party.full_name"></span></span>
                                    </template>
                                </h2>
                                <p class="card-hd-sub" x-text="party.party_role ? getRoleLabel(party.party_role) : 'Role not selected'"></p>
                            </div>
                        </div>
                        <div style="margin-left:auto;color:white;font-size:1rem;opacity:.8;transition:transform .2s"
                            :style="party.isOpen ? 'transform:rotate(180deg)' : 'transform:rotate(0deg)'">
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-bd" x-show="party.isOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

                        {{-- Row 1: Role | Full Name | Mother's Name --}}
                        <div class="form-row-3">
                            <div>
                                <label class="form-lbl">Dhinacyada <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <select x-model="party.party_role" required class="form-sel">
                                        <option value="">Dooro Dhinacyada</option>
                                        <option value="Dacwoode">Dacwoode</option>
                                        <option value="Dacwaysane">Dacwaysane</option>
                                        <option value="Markhaati">Markhaati</option>
                                        <option value="Soo Dhaxgale">Soo Dhaxgale</option>
                                    </select>
                                    <i class="bi bi-chevron-down inp-ico-xs"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl">Magaca<span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="party.full_name" required placeholder="Enter full name"
                                        class="form-inp">
                                    <i class="bi bi-person inp-ico"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl">Magaca Hooyo</label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="party.mother_name" placeholder="Magaca Hooyo"
                                        class="form-inp">
                                    <i class="bi bi-person-heart inp-ico"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Row 2: Sex | DOB | Contact --}}
                        <div class="form-row-3">
                            <div>
                                <label class="form-lbl">Jinsiyada</label>
                                <div class="inp-wrap">
                                    <select x-model="party.sex" class="form-sel">
                                        <option value="">Dooro Jinsiyada</option>
                                        <option value="Male">Lab</option>
                                        <option value="Female">Dhedig</option>
                                    </select>
                                    <i class="bi bi-chevron-down inp-ico-xs"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl">Taariikhda Dhalashada</label>
                                <div class="inp-wrap">
                                    <input type="date" x-model="party.dob" class="form-inp">
                                    <i class="bi bi-calendar3 inp-ico"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl"> Telephone Number</label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="party.contact_number" placeholder="+252 XXX XXX XXX" inputmode="numeric"
                                        @input="party.contact_number = party.contact_number.replace(/\D/g,'')"
                                        class="form-inp">
                                    <i class="bi bi-telephone inp-ico"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Row 3: Email | District | National ID --}}
                        <div class="form-row-3">
                            <div>
                                <label class="form-lbl">Imeyl</label>
                                <div class="inp-wrap">
                                    <input type="email" x-model="party.email" placeholder="example@email.com"
                                        class="form-inp">
                                    <i class="bi bi-envelope inp-ico"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl">Deegaanka</label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="party.district" placeholder="Enter district"
                                        class="form-inp">
                                    <i class="bi bi-geo-alt inp-ico"></i>
                                </div>
                            </div>
                            <div>
                                <label class="form-lbl"> kaarka Aqoonsiga</label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="party.national_id" placeholder="Enter ID number"
                                        class="form-inp">
                                    <i class="bi bi-credit-card-2-front inp-ico"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Row 4: Passport No | Passport Doc | Power of Attorney --}}
                        <div class="form-row-stretch">

                            <div class="form-col-flex">
                                <label class="form-lbl"> Basaboor Lambarkeeda</label>
                                <div class="inp-wrap-flex">
                                    <input type="text" x-model="party.passport_number" placeholder="Enter passport number"
                                        class="form-inp-fill">
                                    <i class="bi bi-passport inp-ico"></i>
                                </div>
                            </div>

                            <div class="form-col-flex">
                                <div class="lbl-row">
                                    <label class="form-lbl"> dukumentiga Basaboorka</label>
                                    <template x-if="party.passport_doc_url">
                                        <a :href="party.passport_doc_url" target="_blank" class="lbl-link">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </template>
                                </div>
                                <label :for="'pass_'+index" class="upload-box">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <span x-text="party.passport_doc_name || 'Upload PDF'"></span>
                                </label>
                                <input type="file" :id="'pass_'+index" @change="handleFile($event, index, 'passport_doc')"
                                    style="display:none" accept=".pdf,.jpg,.png">
                            </div>

                            <div class="form-col-flex">
                                <div class="lbl-row">
                                    <label class="form-lbl"> Dukumentiga qareenka</label>
                                    <template x-if="party.power_of_attorney_url">
                                        <a :href="party.power_of_attorney_url" target="_blank" class="lbl-link">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </template>
                                </div>
                                <label :for="'poa_'+index" class="upload-box">
                                    <i class="bi bi-shield-lock"></i>
                                    <span x-text="party.power_of_attorney_name || 'Upload PDF'"></span>
                                </label>
                                <input type="file" :id="'poa_'+index"
                                    @change="handleFile($event, index, 'power_of_attorney')" style="display:none"
                                    accept=".pdf,.jpg,.png">
                            </div>

                        </div>

                    </div>
                </div>
            </template>

            {{-- Form Actions --}}
            <div class="form-actions">
                <button type="button" @click="addParty" class="btn-add">
                    <i class="bi bi-plus-lg"></i> Kudar dhinacyada
                </button>
                <button type="button" @click="sendNotifications" :disabled="isSending"
                    style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.4rem;font-size:.85rem;font-weight:700;color:white;background:#10b981;border:none;border-radius:.75rem;cursor:pointer;transition:opacity .15s"
                    :class="{'opacity-60 cursor-not-allowed': isSending}"
                    onmouseover="if(!this.disabled)this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                    <i class="bi bi-envelope-fill" x-show="!isSending"></i>
                    <i class="bi bi-arrow-repeat" x-show="isSending" style="display:none"></i>
                    <span x-text="isSending ? 'Diraya...' : 'Dir Ogeysiiska E-mail'"></span>
                </button>
                <button type="submit" :disabled="isSaving" class="btn-save">
                    <span x-show="!isSaving" class="btn-icon-row">
                        <i class="bi bi-cloud-check-fill"></i> Kaydi Dhinacyada
                    </span>
                    <span x-show="isSaving" class="btn-icon-row">
                        <i class="bi bi-arrow-repeat cf-spin"></i> Diiwaangelinaya...
                    </span>
                </button>
            </div>

        </form>

        {{-- Registered Parties Summary --}}
        <div class="data-card">
            <div class="data-card-hd">
                <div class="data-card-hd-left">
                    <i class="bi bi-table"></i>
                    <span class="data-card-hd-title">Diiwanka Kooban Ee Dhinacyada</span>
                </div>
                <span class="data-card-count"
                    x-text="parties.length + ' ' + (parties.length === 1 ? 'party' : 'parties')"></span>
            </div>

            <div class="data-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:3.5rem">#</th>
                            <th>Magaca Buuxa</th>
                            <th>Dhinaca</th>
                            <th>Taleefanka</th>
                            <th>Deegaanka</th>
                            <th>Dukumentiyada</th>
                            <th class="center">ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(party, index) in parties" :key="index">
                            <tr>
                                <td><span class="td-num" x-text="String(index + 1).padStart(2, '0')"></span></td>
                                <td>
                                    <div class="td-bold" x-text="party.full_name || '—'"></div>
                                    <div class="td-sub" x-text="party.mother_name ? 'M/N: ' + party.mother_name : ''"></div>
                                </td>
                                <td>
                                    <span class="role-badge"
                                        :class="party.party_role === 'Dacwoode' ? 'role-plaintiff' : (party.party_role === 'Dacwaysane' ? 'role-defendant' : 'role-other')"
                                        x-text="party.party_role || '—'"></span>
                                </td>
                                <td>
                                    <div class="td-text" x-text="party.contact_number || '—'"></div>
                                    <div class="td-sub" x-text="party.email || ''"></div>
                                </td>
                                <td class="td-muted" x-text="party.district || '—'"></td>
                                <td>
                                    <div class="doc-actions">
                                        <template x-if="party.passport_doc_url">
                                            <a :href="party.passport_doc_url" target="_blank" title="Passport"
                                                class="act-btn act-btn-pdf">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                        </template>
                                        <template x-if="party.power_of_attorney_url">
                                            <a :href="party.power_of_attorney_url" target="_blank" title="POA"
                                                class="act-btn act-btn-poa">
                                                <i class="bi bi-shield-lock"></i>
                                            </a>
                                        </template>
                                        <template x-if="party.rep_doc_url">
                                            <a :href="party.rep_doc_url" target="_blank" title="Legal Rep"
                                                class="act-btn" style="background:rgba(82,140,190,0.1);color:#528CBE;border:1px solid rgba(82,140,190,0.25)">
                                                <i class="bi bi-person-badge"></i>
                                            </a>
                                        </template>
                                        <template x-if="!party.passport_doc_url && !party.power_of_attorney_url && !party.rep_doc_url">
                                            <span class="td-sub" style="font-style:italic">No docs</span>
                                        </template>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-actions">
                                        <button type="button"
                                            @click="parties[index].isOpen = true; $nextTick(() => document.getElementById('party-card-'+index).scrollIntoView({behavior:'smooth'}))"
                                            title="Edit" class="act-btn act-btn-edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" @click="removeParty(index)" title="Remove"
                                            class="act-btn act-btn-del">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="data-card-ft">
                <p>Showing <span x-text="parties.length"></span> <span
                        x-text="parties.length === 1 ? 'party' : 'parties'"></span></p>
            </div>
        </div>

        {{-- Registered lawyers, for autocomplete on the name field below --}}
        <datalist id="registered-lawyers">
            @foreach($lawyers as $lawyer)
                <option value="{{ $lawyer->LawyerName }}">
            @endforeach
        </datalist>

        {{-- ═══ WAKIILADA SHARCIGA — Separate Section ═══ --}}
        <div class="data-card" style="margin-top:1.5rem">
            <div class="data-card-hd">
                <div class="data-card-hd-left">
                    <i class="bi bi-person-badge-fill" style="color:#528CBE"></i>
                    <span class="data-card-hd-title">Wakiilada Sharciga</span>
                </div>
                <span class="data-card-count" x-text="legalReps.length + ' wakiil'"></span>
            </div>

            {{-- Entry Form --}}
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #f0f0f0;background:rgba(82,140,190,0.02)">
                <p style="font-size:.68rem;font-weight:800;color:#528CBE;text-transform:uppercase;letter-spacing:.08em;margin:0 0 .85rem">
                    <i class="bi bi-plus-circle"></i> Ku Dar Wakiil Cusub
                </p>
                <div class="form-row-4">
                    <div>
                        <label class="form-lbl">Dhinacaha <span class="req">*</span></label>
                        <div class="inp-wrap">
                            <select x-model="repEntry.party_index" class="form-sel">
                                <option value="">Dooro Dhinacaha...</option>
                                <template x-for="(p, i) in parties" :key="i">
                                    <option :value="i" x-text="(p.full_name || 'Dhinac ' + (i+1)) + ' — ' + (p.party_role || '?')"></option>
                                </template>
                            </select>
                            <i class="bi bi-chevron-down inp-ico-xs"></i>
                        </div>
                    </div>
                    <div>
                        <label class="form-lbl">Magaca Wakiilka <span class="req">*</span></label>
                        <div class="inp-wrap">
                            <input type="text" x-model="repEntry.rep_name" list="registered-lawyers" @input="onRepNameInput()"
                                   placeholder="Raadi qareen diiwaan gashan ama qor mid cusub..." class="form-inp">
                            <i class="bi bi-person inp-ico"></i>
                        </div>
                    </div>
                    <div>
                        <label class="form-lbl">Lambarka Dukumentiga</label>
                        <div class="inp-wrap">
                            <input type="text" x-model="repEntry.rep_doc_number" placeholder="Tusaale: LIC-2024-001" class="form-inp">
                            <i class="bi bi-file-earmark-text inp-ico"></i>
                        </div>
                    </div>
                    <div>
                        <div class="lbl-row">
                            <label class="form-lbl">Dukumentiga Wakiilka</label>
                            <template x-if="repEntry.rep_doc_url">
                                <a :href="repEntry.rep_doc_url" target="_blank" class="lbl-link"><i class="bi bi-eye"></i> View</a>
                            </template>
                        </div>
                        <label for="repform_doc" class="upload-box">
                            <i class="bi bi-person-badge"></i>
                            <span x-text="repEntry.rep_doc_name || 'Upload PDF'"></span>
                        </label>
                        <input type="file" id="repform_doc" @change="handleRepFile($event)" style="display:none" accept=".pdf,.jpg,.png">
                    </div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:1rem;gap:.6rem">
                    <button type="button" @click="clearRepEntry()"
                        style="padding:.55rem 1.2rem;font-size:.82rem;font-weight:600;color:#6b7280;background:white;border:1.5px solid #e5e7eb;border-radius:.625rem;cursor:pointer">
                        <i class="bi bi-x-lg" style="font-size:.75rem"></i> Jooji
                    </button>
                    <button type="button" @click="setRep()"
                        style="display:flex;align-items:center;gap:.4rem;padding:.55rem 1.4rem;font-size:.82rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer">
                        <i class="bi bi-check-lg"></i> Ku Dar Wakiilka
                    </button>
                </div>
            </div>

            {{-- Reps Table --}}
            <div class="data-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:3.5rem">#</th>
                            <th>Dhinacaha</th>
                            <th>Doorka</th>
                            <th>Magaca Wakiilka</th>
                            <th>Lambarka Dukumentiga</th>
                            <th class="center">Dukumentiga</th>
                            <th class="center">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(r, i) in legalReps" :key="r.id">
                            <tr>
                                <td><span class="td-num" x-text="String(i + 1).padStart(2, '0')"></span></td>
                                <td>
                                    <div class="td-bold" x-text="r.party_full_name || '—'"></div>
                                    <div class="td-sub" x-text="r.party_mother_name ? 'M/N: ' + r.party_mother_name : ''"></div>
                                </td>
                                <td>
                                    <span class="role-badge"
                                        :class="r.party_role === 'Dacwoode' ? 'role-plaintiff' : (r.party_role === 'Dacwaysane' ? 'role-defendant' : 'role-other')"
                                        x-text="r.party_role || '—'"></span>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:.45rem">
                                        <div style="width:28px;height:28px;background:rgba(82,140,190,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                            <i class="bi bi-person-fill" style="color:#528CBE;font-size:.72rem"></i>
                                        </div>
                                        <span class="td-bold" x-text="r.rep_name"></span>
                                    </div>
                                </td>
                                <td>
                                    <template x-if="r.rep_doc_number">
                                        <span style="display:inline-flex;align-items:center;gap:.35rem;font-size:.78rem;font-weight:600;color:#374151;background:#f3f4f6;padding:.25rem .65rem;border-radius:.4rem">
                                            <i class="bi bi-file-earmark-text" style="color:#528CBE"></i>
                                            <span x-text="r.rep_doc_number"></span>
                                        </span>
                                    </template>
                                    <template x-if="!r.rep_doc_number"><span class="td-sub">—</span></template>
                                </td>
                                <td class="center">
                                    <template x-if="r.rep_doc">
                                        <a :href="'/storage/' + r.rep_doc" target="_blank" class="act-btn act-btn-edit" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </template>
                                    <template x-if="!r.rep_doc"><span class="td-sub">—</span></template>
                                </td>
                                <td>
                                    <div class="td-actions">
                                        <button type="button" @click="editRep(r)" title="Wax ka beddel" class="act-btn act-btn-edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" @click="removeRep(r.id)" title="Ka saar" class="act-btn act-btn-del">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="legalReps.length === 0">
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-ico"><i class="bi bi-person-badge"></i></div>
                                        <p class="empty-msg">Wakiil sharciyeed lama geelin weli.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="data-card-ft">
                <p>Muujinaya <span x-text="legalReps.length"></span> wakiil</p>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('partiesManager', () => ({
                caseId: {{ $case->ACID }},
                parties: [],
                isSaving: false,
                isSending: false,
                repEntry: { id: null, party_index: '', party_id: null, party_role: '', rep_name: '', rep_doc_number: '', rep_doc: null, rep_doc_name: '', rep_doc_url: '' },
                legalReps: [],
                isSavingRep: false,
                registeredLawyers: @json($lawyers->map(fn($l) => ['name' => $l->LawyerName, 'code' => $l->Lcode])),

                onRepNameInput() {
                    const match = this.registeredLawyers.find(l => l.name === this.repEntry.rep_name);
                    if (match && match.code && !this.repEntry.rep_doc_number) {
                        this.repEntry.rep_doc_number = match.code;
                    }
                },

                async init() { await this.loadParties(); await this.loadLegalReps(); },

                getRoleLabel(role) {
                    return role || 'Ma Set';
                },

                async loadParties() {
                    try {
                        const res = await fetch(`/appeal-civil-parties/case/${this.caseId}`);
                        const data = await res.json();
                        if (data.length > 0) {
                            this.parties = data.map((p, i) => ({
                                isOpen: i === data.length - 1,
                                PID: p.PID,
                                party_role: p.party_role,
                                full_name: p.full_name,
                                mother_name: p.mother_name,
                                sex: p.sex,
                                dob: p.dob,
                                contact_number: p.contact_number,
                                email: p.email,
                                district: p.district,
                                national_id: p.national_id,
                                passport_number: p.passport_number,
                                passport_doc_name: p.passport_doc ? 'Document Attached' : '',
                                passport_doc_url: p.passport_doc ? `/storage/${p.passport_doc}` : '',
                                power_of_attorney_name: p.power_of_attorney ? 'Document Attached' : '',
                                power_of_attorney_url: p.power_of_attorney ? `/storage/${p.power_of_attorney}` : '',
                                has_rep: !!(p.rep_name || p.rep_doc_number || p.rep_doc),
                                rep_name: p.rep_name || '',
                                rep_doc_number: p.rep_doc_number || '',
                                rep_doc_name: p.rep_doc ? 'Document Attached' : '',
                                rep_doc_url: p.rep_doc ? `/storage/${p.rep_doc}` : '',
                            }));
                        } else {
                            this.addParty();
                        }
                    } catch (e) {
                        console.error('Failed to load parties:', e);
                        this.addParty();
                    }
                },

                addParty() {
                    this.parties.forEach(p => p.isOpen = false);
                    this.parties.push({
                        isOpen: true,
                        party_role: '', full_name: '', mother_name: '', sex: '', dob: '',
                        contact_number: '', email: '', district: '', national_id: '', passport_number: '',
                        passport_doc: null, power_of_attorney: null,
                        passport_doc_name: '', power_of_attorney_name: '',
                        passport_doc_url: '', power_of_attorney_url: '',
                        has_rep: false, rep_name: '', rep_doc_number: '',
                        rep_doc: null, rep_doc_name: '', rep_doc_url: ''
                    });
                    this.$nextTick(() => {
                        const last = document.getElementById('party-card-' + (this.parties.length - 1));
                        if (last) last.scrollIntoView({ behavior: 'smooth' });
                    });
                },

                removeParty(index) {
                    const party = this.parties[index];
                    Swal.fire({
                        title: 'Ma hubtaa?', text: "Ma awoodi doontid inaad dib u celiso!", icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#DC2626', cancelButtonColor: '#528CBE',
                        confirmButtonText: 'Haa, tirtir!', cancelButtonText: 'Jooji'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            if (party.PID) {
                                try {
                                    const res = await fetch(`/appeal-civil-parties/${party.PID}`, {
                                        method: 'DELETE',
                                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                                    });
                                    const data = await res.json();
                                    if (!res.ok) throw new Error(data.message || 'Tirtirku wuu fashilmay.');
                                } catch (e) {
                                    Swal.fire('Khalad!', e.message, 'error'); return;
                                }
                            }
                            this.parties.splice(index, 1);
                            if (this.parties.length === 0) this.addParty();
                            Swal.fire('La tirtiray!', 'Macluumaadka waa laga saaray.', 'success');
                        }
                    });
                },

                async loadLegalReps() {
                    try {
                        const res = await fetch(`/appeal-civil-legal-reps/case/${this.caseId}`);
                        const data = await res.json();
                        this.legalReps = data.map(r => {
                            // Try name from eager-loaded party first, then fall back to parties array
                            let fullName   = r.party_full_name   || '';
                            let motherName = r.party_mother_name || '';
                            if (!fullName) {
                                const match = this.parties.find(p =>
                                    (r.party_id && p.PID == r.party_id) ||
                                    p.party_role === r.party_role
                                );
                                fullName   = match?.full_name   || '';
                                motherName = match?.mother_name || '';
                            }
                            return { ...r, party_full_name: fullName, party_mother_name: motherName };
                        });
                    } catch (e) { console.error(e); }
                },

                async setRep() {
                    const idx = parseInt(this.repEntry.party_index);
                    if (isNaN(idx) || !this.parties[idx]) {
                        Swal.fire('Khalad!', 'Fadlan dooro dhinacaha.', 'warning'); return;
                    }
                    if (!this.repEntry.rep_name.trim()) {
                        Swal.fire('Khalad!', 'Fadlan geli magaca wakiilka.', 'warning'); return;
                    }
                    this.isSavingRep = true;
                    try {
                        const party = this.parties[idx];
                        const fd = new FormData();
                        if (this.repEntry.id) fd.append('id', this.repEntry.id);
                        fd.append('civil_case_id',  this.caseId);
                        fd.append('party_id',       party.PID || '');
                        fd.append('party_role',     party.party_role || '');
                        fd.append('rep_name',       this.repEntry.rep_name);
                        fd.append('rep_doc_number', this.repEntry.rep_doc_number || '');
                        if (this.repEntry.rep_doc) fd.append('rep_doc', this.repEntry.rep_doc);
                        const res = await fetch('/appeal-civil-legal-reps', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: fd
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Khalad ayaa dhacay.');
                        await this.loadLegalReps();
                        this.clearRepEntry();
                        Swal.fire({ title: 'La Keydsaday!', text: data.message, icon: 'success', confirmButtonColor: '#528CBE' });
                    } catch (e) {
                        Swal.fire('Khalad!', e.message, 'error');
                    } finally { this.isSavingRep = false; }
                },

                editRep(r) {
                    const idx = this.parties.findIndex(p => p.PID == r.party_id || p.party_role === r.party_role);
                    this.repEntry = {
                        id:             r.id,
                        party_index:    idx >= 0 ? idx : '',
                        party_id:       r.party_id,
                        party_role:     r.party_role,
                        rep_name:       r.rep_name || '',
                        rep_doc_number: r.rep_doc_number || '',
                        rep_doc:        null,
                        rep_doc_name:   r.rep_doc ? 'Document Attached' : '',
                        rep_doc_url:    r.rep_doc ? '/storage/' + r.rep_doc : ''
                    };
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async removeRep(id) {
                    const result = await Swal.fire({
                        title: 'Ma hubtaa?', text: 'Wakiilkan ma tirtiraysaa?', icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#DC2626',
                        confirmButtonText: 'Haa, tirtir!', cancelButtonText: 'Jooji'
                    });
                    if (!result.isConfirmed) return;
                    try {
                        const res = await fetch(`/appeal-civil-legal-reps/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                        });
                        if (res.ok) { await this.loadLegalReps(); Swal.fire('La tirtiray!', 'Wakiilka waa laga saaray.', 'success'); }
                    } catch (e) { Swal.fire('Khalad!', e.message, 'error'); }
                },

                clearRepEntry() {
                    this.repEntry = { id: null, party_index: '', party_id: null, party_role: '', rep_name: '', rep_doc_number: '', rep_doc: null, rep_doc_name: '', rep_doc_url: '' };
                },

                handleRepFile(event) {
                    const file = event.target.files[0];
                    if (file) { this.repEntry.rep_doc = file; this.repEntry.rep_doc_name = file.name; }
                },

                handleFile(event, index, field) {
                    const file = event.target.files[0];
                    if (file) {
                        this.parties[index][field] = file;
                        this.parties[index][field + '_name'] = file.name;
                    }
                },

                async sendNotifications() {
                    this.isSending = true;
                    try {
                        const res = await fetch(`/appeal-civil-parties/notify/{{ $case->ACID }}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            Swal.fire({
                                title: 'E-mail La Diray!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#10b981'
                            });
                        } else {
                            Swal.fire({ title: 'Khalad!', text: data.message, icon: 'error', confirmButtonColor: '#DC2626' });
                        }
                    } catch (e) {
                        Swal.fire({ title: 'Khalad!', text: 'Xiriirka shabakadda waa xumaaday.', icon: 'error', confirmButtonColor: '#DC2626' });
                    } finally {
                        this.isSending = false;
                    }
                },

                async saveParties() {
                    this.isSaving = true;
                    try {
                        const formData = new FormData();
                        formData.append('case_id', this.caseId);
                        this.parties.forEach((p, i) => {
                            Object.keys(p).forEach(key => {
                                if (p[key] !== null && p[key] !== undefined) {
                                    formData.append(`parties[${i}][${key}]`, p[key]);
                                }
                            });
                        });
                        const res = await fetch('/appeal-civil-parties', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: formData
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            Swal.fire({ title: 'Guul!', text: data.message, icon: 'success', confirmButtonColor: '#528CBE' })
                                .then(() => window.location.reload());
                        } else {
                            let errorMsg = data.message || 'Khalad ayaa dhacay.';
                            if (res.status === 422 && data.errors) {
                                errorMsg = Object.values(data.errors).flat().join('\n');
                            }
                            throw new Error(errorMsg);
                        }
                    } catch (e) {
                        Swal.fire({ title: 'Khalad!', text: e.message, icon: 'error', confirmButtonColor: '#DC2626' });
                    } finally {
                        this.isSaving = false;
                    }
                }
            }));
        });
    </script>

@endsection