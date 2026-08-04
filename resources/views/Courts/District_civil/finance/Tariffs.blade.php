@extends('admin.admin_master')
@section('page_title', 'Tarifadka Maxkamadda')
@section('admin_main_content')

    <div class="p-4 sm:p-6 w-full" x-data="tariffModal">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Tarifadka Maxkamadda</h1>
            </div>
            <button @click="openModal()"
                class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90"
                style="background:#528CBE">
                <i class="bi bi-plus-lg"></i> Ku Dar Tarifad Cusub
            </button>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            {{-- Total Tariffs --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(82,140,190,0.12)">
                    <i class="bi bi-receipt text-xl" style="color:#528CBE"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Wadarta Tarifadyada</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ number_format($stats['total']) }}</h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Dhammaan khidmadaha</p>
                </div>
            </div>

            {{-- Active --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(16,185,129,0.12)">
                    <i class="bi bi-check2-circle text-xl" style="color:#059669"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Firfircoon</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ number_format($stats['active']) }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#059669">Hadda la isticmaalayo</p>
                </div>
            </div>

            {{-- Fixed --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(240,180,60,0.12)">
                    <i class="bi bi-cash-stack text-xl" style="color:#F0B43C"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Qiimo Go'an</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ number_format($stats['fixed']) }}</h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Khidmooyin qiimo go'an ah</p>
                </div>
            </div>

            {{-- Variable --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(124,58,237,0.12)">
                    <i class="bi bi-sliders text-xl" style="color:#7C3AED"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Qiimo Kala Duwan</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ number_format($stats['variable']) }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#7C3AED">Khidmooyin ku xiran dacwadda</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Search & Filter Bar --}}
            <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
                <form action="{{ route('finance.tariffs') }}" method="GET" class="flex flex-wrap gap-3 items-center">

                    {{-- Page size --}}
                    <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                        <span>Tus</span>
                        <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 text-sm font-semibold border border-neutral-300 rounded-full bg-white text-neutral-700
                                       focus:outline-none focus:border-[#528CBE] cursor-pointer">
                            @foreach([10, 25, 50, 100] as $n)
                                <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="relative flex-1 min-w-[200px]">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Raadi lambarka tarifadka ama magaca adeegga…" class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                       text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-[#528CBE]
                                       focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                    </div>

                    {{-- Amount Type filter --}}
                    <select name="type" onchange="this.form.submit()" class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                   focus:outline-none focus:border-[#528CBE] min-w-[160px] cursor-pointer">
                        <option value="">Dhammaan Noocyada</option>
                        <option value="Fixed" {{ request('type') == 'Fixed' ? 'selected' : '' }}>Go'an</option>
                        <option value="Variable" {{ request('type') == 'Variable' ? 'selected' : '' }}>Kala Duwan</option>
                    </select>

                    {{-- Status filter --}}
                    <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                   focus:outline-none focus:border-[#528CBE] min-w-[160px] cursor-pointer">
                        <option value="">Dhammaan Xaaladaha</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Firfircoon</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Aan Firfircoonayn</option>
                    </select>

                    {{-- Search button --}}
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                        style="background:#528CBE">
                        <i class="bi bi-search"></i> Raadi
                    </button>

                    @if(request()->anyFilled(['search', 'type', 'status']))
                        <a href="{{ route('finance.tariffs') }}"
                            class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> Tirtir
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Title --}}
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-table text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Tarifadyada Adeegyada</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">{{ $tariffs->total() }}
                    {{ Str::plural('tarifad', $tariffs->total()) }}</span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Lambarka
                                Adeegga</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                Adeegga</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                Maxkamadda</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Qiimaha</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                Nooca</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                Xaalada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($tariffs as $i => $t)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span
                                        class="text-xs font-bold text-neutral-400">{{ str_pad($tariffs->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block font-mono text-xs font-bold px-2 py-0.5 rounded"
                                        style="background:rgba(82,140,190,0.1);color:#3D78AB">{{ $t->tariff_code ?: ('TF-' . str_pad($t->id, 5, '0', STR_PAD_LEFT)) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-neutral-800">{{ $t->name_so }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($t->court)
                                        <span class="text-sm font-semibold" style="color:#528CBE">{{ $t->court->longName }}</span>
                                    @else
                                        <span class="text-xs text-neutral-400 italic">Dhammaan Maxkamadaha</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold"
                                        style="color:#528CBE">${{ number_format($t->amount, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                        style="background:{{ $t->type === 'Fixed' ? 'rgba(82,140,190,0.1)' : 'rgba(240,180,60,0.12)' }};color:{{ $t->type === 'Fixed' ? '#528CBE' : '#C07E15' }}">{{ $t->type === 'Fixed' ? "Go'an" : 'Kala Duwan' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                        style="background:{{ $t->status === 'Active' ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)' }};color:{{ $t->status === 'Active' ? '#059669' : '#b91c1c' }}">{{ $t->status === 'Active' ? 'Firfircoon' : 'Aan Firfircoonayn' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button title="Eeg"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-[#528CBE] hover:border-[#528CBE] hover:text-white">
                                            <i class="bi bi-eye text-xs"></i>
                                        </button>
                                        <button title="Wax ka beddel"
                                            @click="openModal({{ $t->id }}, @js($t->name_so), {{ $t->amount }}, @js($t->status), @js($t->description), @js($t->court_id), @js($t->tariff_code))"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-emerald-500 hover:border-emerald-500 hover:text-white">
                                            <i class="bi bi-pencil-square text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                            style="background:rgba(82,140,190,0.1)">
                                            <i class="bi bi-receipt text-2xl" style="color:#528CBE"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">Ma jiro tarifad la helay oo la mid ah
                                            shuruudahaaga.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-neutral-100">
                {{ $tariffs->links() }}
            </div>
        </div>

        {{-- Create / Edit Modal --}}
        <div x-show="isModalOpen" style="display:none;background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
            class="fixed inset-0 z-[70] flex items-center justify-center p-4" @keydown.escape.window="closeModal()"
            @click.self="closeModal()">
            <div class="bg-white flex flex-col overflow-hidden w-full"
                style="max-width:640px;max-height:94vh;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between flex-shrink-0"
                    :style="isEditMode?'background:#F0B43C;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0':'background:#528CBE;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0'">
                    <div class="flex items-center gap-3">
                        <div
                            style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                            <i :class="isEditMode?'bi bi-pencil-square':'bi bi-receipt'"
                                style="color:white;font-size:1.1rem"></i>
                        </div>
                        <div>
                            <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0"
                                x-text="isEditMode?'Wax ka Beddel Tarifadka':'Ku Dar Tarifad Cusub'"></h2>
                            <p style="color:rgba(255,255,255,.8);font-size:.75rem;margin:0">Buuxi dhammaan goobaha loo
                                baahdo</p>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center"
                        onmouseover="this.style.background='rgba(255,255,255,0.22)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                        <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="overflow-y-auto flex-1" style="padding:1.5rem 1.75rem">
                    <form @submit.prevent="submitForm">
                        <div style="margin-bottom:1.1rem">
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                                Maxkamadaha <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <select x-model="f.court_id" required
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="">— Dooro Maxkamadda —</option>
                                    @foreach($courts as $court)
                                        <option value="{{ $court->CAI }}">{{ $court->longName }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down"
                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>

                        <div style="margin-bottom:1.1rem">
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                                Magaca Ajuurada <span style="color:#ef4444">*</span></label>
                            <input type="text" x-model="f.name_so" required placeholder="tusaale: Lacagta Gal Ku Qoris"
                                style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.5rem">
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                                    Tariifa ID <span style="color:#ef4444">*</span></label>
                                <input type="text" x-model="f.tariff_code" required placeholder="tusaale: TF-00007"
                                    style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                                    Qiimaha (USD) <span style="color:#ef4444">*</span></label>
                                <input type="number" step="0.01" min="0" x-model="f.amount" required placeholder="0.00"
                                    style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                                    Xaalada <span style="color:#ef4444">*</span></label>
                                <div style="position:relative">
                                    <select x-model="f.status" required
                                        style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                        <option value="Active">Firfircoon</option>
                                        <option value="Inactive">Aan Firfircoonayn</option>
                                    </select>
                                    <i class="bi bi-chevron-down"
                                        style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom:1.5rem">
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                                Sharaxaad</label>
                            <textarea x-model="f.description" rows="3" placeholder="Faallo dheeraad ah..."
                                style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;resize:vertical;min-height:80px;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"></textarea>
                        </div>

                        {{-- Footer --}}
                        <div
                            style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                            <button type="button" @click="closeModal()"
                                style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer"
                                onmouseover="this.style.background='#f9fafb'"
                                onmouseout="this.style.background='white'">Jooji</button>
                            <button type="submit" :disabled="isSubmitting"
                                :style="(isEditMode?'background:#F0B43C;box-shadow:0 4px 14px rgba(240,180,60,.4)':'background:#528CBE;box-shadow:0 4px 14px rgba(82,140,190,.4)')+';display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;border:none;border-radius:.625rem;cursor:pointer'"
                                :class="{'opacity-70 cursor-not-allowed':isSubmitting}">
                                <i class="bi bi-check-circle-fill" x-show="!isSubmitting"></i>
                                <i class="bi bi-arrow-repeat" x-show="isSubmitting" style="display:none"></i>
                                <span x-text="isEditMode?'Kaydi Isbeddelada':'Kaydi'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tariffModal', () => ({
                isModalOpen: false, isEditMode: false, isSubmitting: false, currentId: null,
                f: { court_id: '', tariff_code: '', name_so: '', amount: '', status: 'Active', description: '' },

                openModal(id = null, name_so = '', amount = '', status = 'Active', description = '', court_id = '', tariff_code = '') {
                    this.isEditMode = !!id;
                    this.currentId = id;
                    this.f = { court_id: court_id || '', tariff_code, name_so, amount, status: status || 'Active', description };
                    this.isModalOpen = true;
                },
                closeModal() {
                    this.isModalOpen = false;
                    this.currentId = null;
                    this.f = { court_id: '', tariff_code: '', name_so: '', amount: '', status: 'Active', description: '' };
                },
                async submitForm() {
                    this.isSubmitting = true;
                    try {
                        const url = this.isEditMode ? `{{ url('/finance-tariffs') }}/${this.currentId}` : `{{ route('finance.tariffs.store') }}`;
                        const method = this.isEditMode ? 'PUT' : 'POST';
                        const res = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.f),
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.closeModal();
                            Swal.fire({ title: 'Guul!', text: data.message, icon: 'success', confirmButtonText: 'Hagaag', confirmButtonColor: '#528CBE' })
                                .then(() => window.location.reload());
                        } else {
                            throw new Error(data.message || 'Xaqiijintu waa fashilantay.');
                        }
                    } catch (e) {
                        Swal.fire({ title: 'Khalad!', text: e.message, icon: 'error', confirmButtonText: 'Hagaag', confirmButtonColor: '#DC2626' });
                    } finally {
                        this.isSubmitting = false;
                    }
                },
            }));
        });
    </script>

@endsection
