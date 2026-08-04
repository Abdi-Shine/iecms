@extends('admin.admin_master')
@section('page_title', 'Gal Ku Qorista Dacwadaha Cabashada — IECMS')
@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="p-4 sm:p-6 w-full" x-data="prosecutorAssignManager">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Gal Ku Qorista Dacwadaha Cabashada</h1>
                <p class="text-sm text-neutral-500 mt-0.5"><strong
                        class="text-primary-400">{{ $case->case_number }}</strong>
                    — {{ $case->title }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('attorney-cases.show', $case->ACID) }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
                    <i class="bi bi-eye"></i> Arag Dacwadda
                </a>
                <a href="{{ route('attorney-prosecutor-assignments.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
                    <i class="bi bi-arrow-left"></i> Ku Laabo Liiska
                </a>
            </div>
        </div>

        {{-- Assignment Form Panel --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">

            <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2 bg-primary-400/4">
                <i class="bi bi-plus-circle text-sm text-primary-400"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Xilsaar Xeer Ilaaliye
                    Cusub</span>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">

                    {{-- Prosecutor --}}
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Xeer Ilaaliyaha <span class="text-danger-500">*</span>
                        </label>
                        <select x-model="form.prosecutor_id"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro Xeer Ilaaliyaha —</option>
                            @foreach($prosecutors as $p)
                                <option value="{{ $p->AID }}">{{ $p->EmpName }} — {{ $p->Position }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Xilka <span class="text-danger-500">*</span>
                        </label>
                        <select x-model="form.role"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                            <option value="">— Dooro Xilka —</option>
                            @foreach($roles as $r)
                                <option value="{{ $r }}">
                                    {{ ['Lead' => 'Hogaamiye', 'Assistant' => 'Kaaliye', 'Support' => 'Taageere'][$r] ?? $r }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Assigned Date --}}
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Taariikhda La Xilsaaray <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" x-model="form.assigned_date"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="resetForm" x-show="form.id"
                        class="px-5 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition">
                        Jooji Wax Ka Beddelka
                    </button>
                    <button type="button" @click="saveAssignment" :disabled="isSaving"
                        class="flex items-center gap-2 px-8 py-2.5 text-white text-sm font-bold rounded-xl shadow hover:opacity-90 transition-all disabled:opacity-50 bg-primary-400">
                        <i class="bi bi-cloud-check-fill" x-show="!isSaving"></i>
                        <i class="bi bi-arrow-repeat animate-spin" x-show="isSaving"></i>
                        <span x-text="isSaving ? 'Waa La Shaqaynayaa...' : (form.id ? 'Cusboonaysii' : 'Keydi')"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Existing Assignments Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">

            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between bg-primary-400/4">
                <div class="flex items-center gap-2">
                    <i class="bi bi-people text-sm text-primary-400"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Xeer Ilaaliyayaasha Hadda
                        Loo Xilsaaray</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">
                    {{ count($case->prosecutorAssignments) }} xilsaaris guud
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-primary-400/6">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-16">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xeer
                                Ilaaliyaha</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xilka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-24">
                                Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @php
                            $roleLabels = ['Lead' => 'Hogaamiye', 'Assistant' => 'Kaaliye', 'Support' => 'Taageere'];
                        @endphp
                        @foreach($case->prosecutorAssignments as $index => $a)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span
                                        class="text-xs font-bold text-neutral-400">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($a->prosecutor)
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-neutral-800">{{ $a->prosecutor->EmpName }}</span>
                                            <span
                                                class="text-xs font-bold text-neutral-400 uppercase tracking-tighter">{{ $a->prosecutor->Position }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs italic text-neutral-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-violet-600/10 text-violet-600">
                                        {{ $roleLabels[$a->role] ?? $a->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-neutral-600 text-xs">{{ $a->assigned_date?->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            @click="editAssignment({{ $a->id }}, '{{ $a->prosecutor_id }}', '{{ $a->role }}', '{{ $a->assigned_date?->format('Y-m-d') }}')"
                                            title="Wax Ka Beddel"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-gold-400 hover:border-gold-400 hover:text-white">
                                            <i class="bi bi-pencil-square text-xs"></i>
                                        </button>
                                        <button @click="deleteAssignment({{ $a->id }})" title="Tirtir"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-danger-600 hover:border-danger-600 hover:text-white">
                                            <i class="bi bi-trash3 text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if(count($case->prosecutorAssignments) === 0)
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full flex items-center justify-center bg-primary-400/10">
                                            <i class="bi bi-person-x text-2xl text-primary-400"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">Xeer ilaaliye weli lama xilsaarin
                                            dacwaddan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('prosecutorAssignManager', () => ({
                caseId: {{ $case->ACID }},
                isSaving: false,
                form: {
                    id: null,
                    prosecutor_id: '',
                    role: '',
                    assigned_date: new Date().toISOString().split('T')[0]
                },

                resetForm() {
                    this.form = {
                        id: null,
                        prosecutor_id: '',
                        role: '',
                        assigned_date: new Date().toISOString().split('T')[0]
                    };
                },

                editAssignment(id, prosecutorId, role, date) {
                    this.form = { id, prosecutor_id: String(prosecutorId), role, assigned_date: date };
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async saveAssignment() {
                    if (!this.form.prosecutor_id || !this.form.role || !this.form.assigned_date) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Khalad Xaqiijin',
                            text: 'Fadlan buuxi xeer ilaaliyaha, xilka, iyo taariikhda.',
                            confirmButtonColor: '#528CBE',
                            confirmButtonText: 'Hagaag'
                        });
                        return;
                    }

                    this.isSaving = true;
                    try {
                        const isUpdate = !!this.form.id;
                        const url = isUpdate
                            ? '{{ route("attorney-prosecutor-assignments.index") }}/' + this.form.id
                            : '{{ route("attorney-prosecutor-assignments.store") }}';

                        const res = await fetch(url, {
                            method: isUpdate ? 'PUT' : 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                attorney_case_id: this.caseId,
                                prosecutor_id: this.form.prosecutor_id,
                                role: this.form.role,
                                assigned_date: this.form.assigned_date
                            })
                        });

                        if (!res.ok) {
                            const err = await res.json();
                            let msg = err.message || 'Kuma guuleysanin keydsiga';
                            if (err.errors) msg = Object.values(err.errors).flat().join('\n');
                            throw new Error(msg);
                        }

                        const result = await res.json();

                        Swal.fire({
                            icon: result.email_sent === false ? 'warning' : 'success',
                            title: result.email_sent === false ? 'Guul (ogeysiis lama dirin)' : 'Guul!',
                            text: result.message || 'Xilsaaristii si guul ah ayaa loo keydsaday.',
                            timer: result.email_sent === false ? undefined : 2000,
                            showConfirmButton: result.email_sent === false
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
                                const res = await fetch(`{{ route('attorney-prosecutor-assignments.index') }}/${id}`, {
                                    method: 'DELETE',
                                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                });
                                if (res.ok) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'La tirtiray!',
                                        text: 'Xilsaaristii si guul ah ayaa loo tirtiray.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire('Khalad', 'Tirtirka xilsaaristii wuu fashilmay', 'error');
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