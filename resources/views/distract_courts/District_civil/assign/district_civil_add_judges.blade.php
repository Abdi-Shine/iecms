@extends('admin.admin_master')
@section('page_title', 'Xukumaynta Garsooreyaasha — IECMS')
@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="p-4 sm:p-6 w-full" x-data="assignmentManager">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Gal ku qorista Dacwadda</h1>
                <p class="text-sm text-neutral-500 mt-0.5"><strong style="color:#528CBE">{{ $case->FileNo }}</strong></p>
            </div>
            <a href="{{ route('civil-registration.show', $case->CRID) }}"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i> Ku Laabo Dacwadda
            </a>
        </div>

        {{-- Assignment Form Panel --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">

            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between"
                style="background:rgba(82,140,190,0.04)">
                <div class="flex items-center gap-2">
                    <i class="bi bi-plus-circle text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Macluumaadka Garsooraha iyo
                        Kaaliyaha</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">Dacwadda: <strong
                        class="text-neutral-600">{{ $case->FileNo }}</strong></span>
            </div>

            <div class="p-6">

                {{-- Entry Cards --}}
                <template x-for="(entry, index) in entries" :key="index">
                    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6 mb-4">

                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black text-white"
                                    style="background:#528CBE" x-text="index + 1"></span>
                                <span class="text-sm font-bold text-neutral-800"> Gal Ku Qorista Garsooraha ama
                                    Kaaliyaha</span>
                            </div>
                            <button type="button" @click="removeEntry(index)" x-show="entries.length > 1"
                                class="w-7 h-7 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-300 transition-all"
                                onmouseover="this.style.background='#DC2626';this.style.borderColor='#DC2626';this.style.color='white'"
                                onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                <i class="bi bi-trash3-fill text-xs"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                            {{-- Staff --}}
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Howlwadeenada <span style="color:#ef4444">*</span>
                                </label>
                                <select x-model="entry.employee_id"
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-[#528CBE] transition-all">
                                    <option value="">— Dooro Howlwadeenka —</option>
                                    <template x-for="e in employees" :key="e.AID">
                                        <option :value="e.AID" x-text="e.EmpName + ' — ' + e.Position"></option>
                                    </template>
                                </select>
                                <div class="mt-1" x-show="entry.employee_id">
                                    <template x-for="e in employees.filter(x => x.AID == entry.employee_id)" :key="e.AID">
                                        <span class="text-[9px] font-bold uppercase tracking-tighter" style="color:#528CBE"
                                            x-text="'Xilka: ' + e.Position"></span>
                                    </template>
                                </div>
                            </div>

                            {{-- Panel Role --}}
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Xilka Guddiga <span style="color:#ef4444">*</span>
                                </label>
                                <select x-model="entry.panel_role"
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-[#528CBE] transition-all">
                                    <option value="">— Dooro Xilka —</option>
                                    <option value="Guddoomiye">Guddoomiye</option>
                                    <option value="Xubin">Xubin</option>
                                    <option value="Kaaliye">Kaaliye</option>
                                </select>
                            </div>

                            {{-- Assignment Date --}}
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Taariikhda Lagu Qoray
                                </label>
                                <input type="date" x-model="entry.assignment_date"
                                    class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-[#528CBE] transition-all">
                            </div>

                            {{-- Workflow stage status fetched from status_processes --}}
                            <div>
                                <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                                    Xaalada Dacwadda
                                </label>
                                <div>
                                    <input type="text" value="{{ $stageStatus?->name ?? 'Gal Ku Qoris' }}" readonly
                                        class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-100 font-bold focus:outline-none cursor-not-allowed"
                                        style="color:#528CBE;">
                                </div>
                                <p class="text-[9px] text-neutral-400 mt-1">Xaaladda waxaa laga soo qaatay Miiska Xaaladda</p>
                            </div>

                        </div>
                    </div>
                </template>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap items-center justify-between gap-4 mt-6">
                    <button type="button" @click="addEntry"
                        class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-bold rounded-xl shadow transition-all hover:opacity-90"
                        style="background:#10B981">
                        <i class="bi bi-plus-lg"></i> Ku Dar Howlwadeen Kale
                    </button>
                    <button type="button" @click="saveAssignment" :disabled="isSaving"
                        class="flex items-center gap-2 px-8 py-2.5 text-white text-sm font-bold rounded-xl shadow hover:opacity-90 transition-all disabled:opacity-50"
                        style="background:#528CBE">
                        <i class="bi bi-cloud-check-fill" x-show="!isSaving"></i>
                        <i class="bi bi-arrow-repeat animate-spin" x-show="isSaving"></i>
                        <span x-text="isSaving ? 'Waa La Shaqaynayaa...' : 'Keydi'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Existing Assignments Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">

            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between"
                style="background:rgba(82,140,190,0.04)">
                <div class="flex items-center gap-2">
                    <i class="bi bi-people text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Xubnaha Hadda Jira</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">
                    {{ count($case->assignments) }} xukumis guud
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-16">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Garsoore /
                                Shaqaale</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xilka Guddi
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada
                                Dacwadda</th>
                            <th
                                class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-28">
                                Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($case->assignments as $index => $a)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span
                                        class="text-xs font-bold text-neutral-400">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($a->employee)
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-neutral-800">{{ $a->employee->EmpName }}</span>
                                            <span
                                                class="text-xs font-bold text-neutral-400 uppercase tracking-tighter">{{ $a->employee->Position }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs italic text-neutral-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleBg = match ($a->panel_role) { 'Chair' => 'rgba(82,140,190,0.1)', 'Member' => 'rgba(240,180,60,0.12)', 'Clerk' => 'rgba(16,185,129,0.1)', default => 'rgba(156,163,175,0.15)'};
                                        $roleClr = match ($a->panel_role) { 'Chair' => '#528CBE', 'Member' => '#C07E15', 'Clerk' => '#059669', default => '#6B7280'};
                                        $roleLbl = match ($a->panel_role) { 'Chair' => 'Guddoomiye', 'Member' => 'Xubin', 'Clerk' => 'Kaaliye', default => $a->panel_role};
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                        style="background:{{ $roleBg }};color:{{ $roleClr }}">
                                        {{ $roleLbl }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-neutral-600 text-xs">{{ \Carbon\Carbon::parse($a->assignment_date)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full font-bold uppercase tracking-wider whitespace-nowrap"
                                        style="font-size:9px;background:rgba(82,140,190,0.08);color:#528CBE">
                                        {{ $a->caseStatus }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            @click="editAssignment({{ $a->id }}, '{{ $a->employee_id }}', '{{ $a->panel_role }}', '{{ $a->assignment_date }}', '{{ addslashes($a->notes) }}')"
                                            title="Wax Ka Beddel"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#F0B43C';this.style.borderColor='#F0B43C';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-pencil-square text-xs"></i>
                                        </button>
                                        <button @click="deleteAssignment({{ $a->id }})" title="Tirtir"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#DC2626';this.style.borderColor='#DC2626';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-trash3 text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if(count($case->assignments) === 0)
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                            style="background:rgba(82,140,190,0.1)">
                                            <i class="bi bi-person-x text-2xl" style="color:#528CBE"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">Xil lama xukumin dacwaddan weli.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-neutral-100">
                <p class="text-xs text-neutral-400 font-medium">
                    Waxaa la muujinayaa <span class="font-bold text-neutral-600">{{ count($case->assignments) }}</span>
                    {{ count($case->assignments) === 1 ? 'xukumis' : 'xukumis' }}
                </p>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('assignmentManager', () => ({
                case: @json($case),
                employees: @json($employees),
                isSaving: false,
                entries: [],

                init() {
                    this.addEntry();
                },

                addEntry() {
                    this.entries.push({
                        employee_id: '',
                        panel_role: '',
                        assignment_date: new Date().toISOString().split('T')[0],
                        notes: ''
                    });
                },

                removeEntry(index) {
                    this.entries.splice(index, 1);
                    if (this.entries.length === 0) this.addEntry();
                },

                async saveAssignment() {
                    const validEntries = this.entries.filter(e => e.employee_id);
                    if (validEntries.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Khalad Xaqiijin',
                            text: 'Fadlan xukumi ugu yaraan shaqaale hal ah.',
                            confirmButtonColor: '#528CBE',
                            confirmButtonText: 'Hagaag'
                        });
                        return;
                    }
                    this.isSaving = true;
                    try {
                        for (let entry of validEntries) {
                            const isUpdate = !!entry.id;
                            let url = '{{ route("civil-case-assign.store") }}';
                            if (isUpdate) url = '{{ route("civil-case-assign.index") }}/' + entry.id;

                            const res = await fetch(url, {
                                method: isUpdate ? 'PUT' : 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    civil_case_id: this.case.CRID,
                                    employee_id: entry.employee_id,
                                    panel_role: entry.panel_role,
                                    assignment_date: entry.assignment_date,
                                    notes: entry.notes
                                })
                            });
                            if (!res.ok) {
                                const err = await res.json();
                                let msg = err.message || 'Kuma guuleysanin keydsiga';
                                if (err.errors) msg = Object.values(err.errors).flat().join('\n');
                                throw new Error(msg);
                            }
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Guul!',
                            text: 'Xukumistii si guul ah ayaa loo keydsaday.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => window.location.reload());

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
                },

                editAssignment(id, empId, role, date, notes) {
                    this.entries = [{ id, employee_id: empId, panel_role: role, assignment_date: date, notes }];
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async deleteAssignment(id) {
                    Swal.fire({
                        title: 'Ma hubtaa?',
                        text: 'Ma awoodi doontid inaad dib u celiso!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#DC2626',
                        cancelButtonColor: '#528CBE',
                        confirmButtonText: 'Haa, tirtir!',
                        cancelButtonText: 'Jooji'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                const res = await fetch(`{{ route('civil-case-assign.index') }}/${id}`, {
                                    method: 'DELETE',
                                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                });
                                if (res.ok) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'La tirtiray!',
                                        text: 'Xukumistii si guul ah ayaa loo tirtiray.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire('Khalad', 'Tirtirka xukumistii wuu fashilmay', 'error');
                                }
                            } catch (e) {
                                Swal.fire('Khalad', e.message, 'error');
                            }
                        }
                    });
                }
            }));
        });
    </script>

@endsection