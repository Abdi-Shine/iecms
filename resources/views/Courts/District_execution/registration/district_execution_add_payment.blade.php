@extends('admin.admin_master')
@section('page_title', 'Foomka Codsiga Lacag Bixinta')
@section('admin_main_content')

@php
    $roleLabels = ['Plaintiff' => 'Dacwoode', 'Defendant' => 'Dacwaysane', 'Witness' => 'Markhaati', 'Applicant' => 'Codsade', 'Other' => 'Soo Dhaxgale'];

    $partiesForJs = $case->parties->map(function ($p) use ($roleLabels) {
        return [
            'role' => $roleLabels[$p->party_role] ?? $p->party_role,
            'full_name' => $p->full_name,
            'contact_number' => $p->contact_number,
            'email' => $p->email,
        ];
    })->values();

    $knownApplicantsForJs = $knownApplicants->map(function ($a) {
        return [
            'full_name' => $a->payer_name,
            'phone' => $a->payer_phone,
            'email' => $a->payer_email,
        ];
    })->values();
@endphp

<datalist id="applicant-options">
    @foreach($knownApplicants as $applicant)
        <option value="{{ $applicant->payer_name }}">
    @endforeach
</datalist>

<div class="p-4 sm:p-6 w-full" x-data="paymentRequestForm">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->FileNo ?? 'Codsiga Lacag Bixinta' }}</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Foomka Codsiga Lacag Bixinta</p>
        </div>
        <a href="{{ route('execution-registration.supporting', $case->ECID) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200
                   rounded-xl bg-white hover:bg-neutral-50 transition-all shadow-sm">
            <i class="bi bi-arrow-left"></i> Dib ugu Noqo
        </a>
    </div>

    <form action="{{ route('execution-registration.payment-request.store') }}" method="POST">
        @csrf

        <input type="hidden" name="execution_case_id" value="{{ $case->ECID }}">

        <div class="rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Blue Title Bar --}}
            <div class="flex items-center gap-3 px-6 py-4" style="background:linear-gradient(135deg,#528CBE,#3D78AB)">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,0.15)">
                    <i class="bi bi-cash-coin text-white"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-base leading-tight">Foomka Codsiga Lacag Bixinta</h2>
                    <p class="text-white/75 text-xs mt-0.5">Buuxi dhammaan goobaha loo baahdo si codsigu u dhamaystirmo</p>
                </div>
            </div>

            <div class="bg-white divide-y divide-neutral-100">

                {{-- Case Parties (Applicant Info Reference) --}}
                <div x-show="parties.length > 0" x-cloak>
                    <div class="px-6 py-3 flex items-center gap-2 bg-neutral-50/60">
                        <i class="bi bi-people text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhinacyada Dacwadda</span>
                    </div>
                    <div class="overflow-x-auto px-6 py-4">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400">Doorka</th>
                                    <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400">Magaca</th>
                                    <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400">Taleefanka</th>
                                    <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400">Imeyl</th>
                                    <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400 text-center">Ficil</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <template x-for="(p, idx) in parties" :key="idx">
                                    <tr class="transition-colors" :class="selectedPartyIdx === idx ? 'bg-[#528CBE]/5' : 'hover:bg-neutral-50'">
                                        <td class="px-3 py-2">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:rgba(82,140,190,0.1);color:#528CBE" x-text="p.role"></span>
                                        </td>
                                        <td class="px-3 py-2 font-semibold text-neutral-800 text-sm" x-text="p.full_name"></td>
                                        <td class="px-3 py-2 text-neutral-600 text-sm" x-text="p.contact_number || '—'"></td>
                                        <td class="px-3 py-2 text-neutral-600 text-sm" x-text="p.email || '—'"></td>
                                        <td class="px-3 py-2 text-center">
                                            <button type="button" @click="useParty(p, idx)" :disabled="selectedPartyIdx === idx"
                                                class="px-3 py-1 text-xs font-semibold rounded-lg border transition-all inline-flex items-center gap-1"
                                                :class="selectedPartyIdx === idx
                                                    ? 'bg-emerald-500 border-emerald-500 text-white cursor-default'
                                                    : 'border-neutral-200 text-neutral-500 hover:bg-[#528CBE] hover:text-white hover:border-[#528CBE]'">
                                                <i class="bi" :class="selectedPartyIdx === idx ? 'bi-check2-circle' : ''"></i>
                                                <span x-text="selectedPartyIdx === idx ? 'La Doortay' : 'Isticmaal'"></span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Applicant Info --}}
                <div>
                    <div class="px-6 py-3 flex items-center gap-2 bg-neutral-50/60">
                        <i class="bi bi-person-vcard text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Macluumaadka Codsadaha</span>
                    </div>
                    <div class="px-6 py-5">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-neutral-600 mb-1.5">Magaca Buuxa <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="bi bi-person absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                                    <input type="text" name="payer_name" list="applicant-options" x-model="payerName"
                                        @input="onApplicantNameInput()" @blur="lookupHistory()" required
                                        placeholder="Soo Geli ama Dooro Magaca Buuxa"
                                        class="w-full pl-10 pr-3.5 py-2.5 text-sm border border-neutral-200 rounded-lg bg-white
                                               focus:outline-none focus:border-[#528CBE] focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-600 mb-1.5">Taleefan <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="bi bi-telephone absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                                    <input type="text" name="payer_phone" x-model="payerPhone" inputmode="numeric"
                                        @input="payerPhone = payerPhone.replace(/\D/g,'')" @blur="lookupHistory()" required
                                        placeholder="61 000 0000"
                                        class="w-full pl-10 pr-3.5 py-2.5 text-sm border border-neutral-200 rounded-lg bg-white
                                               focus:outline-none focus:border-[#528CBE] focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-600 mb-1.5">Imeyl <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="bi bi-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                                    <input type="email" name="payer_email" x-model="payerEmail" @blur="lookupHistory()" required
                                        placeholder="tusaale@email.com"
                                        class="w-full pl-10 pr-3.5 py-2.5 text-sm border border-neutral-200 rounded-lg bg-white
                                               focus:outline-none focus:border-[#528CBE] focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Applicant Payment History --}}
                <div x-show="historyChecked" x-cloak>
                    <div class="px-6 py-3 flex items-center gap-2 bg-neutral-50/60">
                        <i class="bi bi-clock-history text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Taariikhda Lacag Bixinta ee Codsadaha</span>
                    </div>
                    <div class="px-6 py-4">
                        <div x-show="historyLoading" class="text-xs text-neutral-400 flex items-center gap-2 py-2">
                            <i class="bi bi-arrow-repeat animate-spin"></i> Sugaya...
                        </div>
                        <div x-show="!historyLoading && paymentHistory.length === 0" class="text-xs text-neutral-400 py-2">
                            Ma jiraan diiwaanno lacag bixin oo hore loo helay codsadahan.
                        </div>
                        <div x-show="!historyLoading && paymentHistory.length > 0" class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400 w-10">#</th>
                                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400">Codsadaha</th>
                                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400">Taleefanka</th>
                                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400">Adeegga</th>
                                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400">Cadadka</th>
                                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400">Taariikhda</th>
                                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400 text-center">Xaalada</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    <template x-for="(h, idx) in paymentHistory" :key="h.id">
                                        <tr>
                                            <td class="px-3 py-2 text-xs font-semibold text-neutral-400" x-text="idx + 1"></td>
                                            <td class="px-3 py-2 font-semibold text-neutral-800 text-sm" x-text="h.payer_name"></td>
                                            <td class="px-3 py-2 text-neutral-600 text-sm" x-text="h.payer_phone || '—'"></td>
                                            <td class="px-3 py-2 text-neutral-600 text-sm" x-text="h.service"></td>
                                            <td class="px-3 py-2 font-semibold text-neutral-700 text-sm" x-text="'$' + Number(h.amount).toFixed(2) + ' ' + h.currency"></td>
                                            <td class="px-3 py-2 text-neutral-600 text-sm" x-text="h.payment_date"></td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                                    :class="{
                                                        'bg-emerald-50 text-emerald-600': h.status === 'Approved',
                                                        'bg-amber-50 text-amber-600': h.status === 'Pending',
                                                        'bg-purple-50 text-purple-600': h.status === 'Awaiting Approval',
                                                        'bg-red-50 text-red-600': h.status === 'Failed',
                                                    }" x-text="statusLabel(h.status)"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between gap-3 mt-6">
            <a href="{{ route('execution-registration.supporting', $case->ECID) }}"
                class="px-5 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200 rounded-xl bg-white hover:bg-neutral-50 transition-all">
                Kanoqo
            </a>
            <button type="submit"
                class="flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white rounded-xl shadow transition-all hover:opacity-90"
                style="background:#528CBE">
                <i class="bi bi-check2-circle"></i> Keyd
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('paymentRequestForm', () => ({
            parties: @json($partiesForJs),
            knownApplicants: @json($knownApplicantsForJs),
            selectedPartyIdx: null,
            payerName: '{{ old('payer_name') }}',
            payerPhone: '{{ old('payer_phone') }}',
            payerEmail: '{{ old('payer_email') }}',
            paymentHistory: [],
            historyLoading: false,
            historyChecked: false,

            useParty(p, idx) {
                this.selectedPartyIdx = idx;
                this.payerName = p.full_name || '';
                this.payerPhone = p.contact_number || '';
                this.payerEmail = p.email || '';
                this.lookupHistory();
            },

            onApplicantNameInput() {
                const match = this.knownApplicants.find(a => a.full_name === this.payerName);
                if (match) {
                    this.payerPhone = match.phone || '';
                    this.payerEmail = match.email || '';
                    this.lookupHistory();
                }
            },

            async lookupHistory() {
                const name = this.payerName.trim();
                const phone = this.payerPhone.trim();
                const email = this.payerEmail.trim();
                if (!phone && !email && name.length < 3) {
                    this.paymentHistory = [];
                    this.historyChecked = false;
                    return;
                }
                this.historyLoading = true;
                this.historyChecked = true;
                try {
                    const params = new URLSearchParams({ name, phone, email });
                    const res = await fetch(`{{ route('finance.applicant-request.payment-history') }}?${params.toString()}`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    this.paymentHistory = res.ok ? await res.json() : [];
                } catch (e) {
                    this.paymentHistory = [];
                } finally {
                    this.historyLoading = false;
                }
            },

            statusLabel(status) {
                return { 'Pending': 'Sugaya', 'Awaiting Approval': 'Sugaya Ansaxin', 'Approved': 'La Ansaxiyay', 'Failed': 'Fashilmay' }[status] || status;
            },
        }));
    });
</script>

@endsection
