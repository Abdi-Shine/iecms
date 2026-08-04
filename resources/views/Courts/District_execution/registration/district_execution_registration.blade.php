@extends('admin.admin_master')
@section('page_title', 'Diiwaanka Fulinta — Execution Registry')
@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- .ci, .cl, .ci-g, .action-btn and hover variants defined in resources/css/app.css --}}

    <div class="p-4 sm:p-6 w-full" x-data="executionRegManager">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Diiwaanka Fulinta</h1>
            </div>
            <button @click="openModal()"
                class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90"
                style="background:#528CBE">
                <i class="bi bi-plus-lg"></i> Kudar Dacwad Cusub
            </button>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            {{-- Total Cases --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(82,140,190,0.12)">
                    <i class="bi bi-file-earmark-ruled text-xl" style="color:#528CBE"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Wadarta Guud</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $allRecords->count() }}</h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Dhamaan Wadarta Diiwanka </p>
                </div>
            </div>

            {{-- This Month --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(240,180,60,0.12)">
                    <i class="bi bi-calendar-check text-xl" style="color:#F0B43C"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Bishan</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                        {{ $allRecords->filter(fn($r) => date('Y-m', strtotime($r->OpenDate ?? '')) == date('Y-m'))->count() }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">{{ date('F Y') }}</p>
                </div>
            </div>

            {{-- Active Cases --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(82,140,190,0.12)">
                    <i class="bi bi-hourglass-split text-xl" style="color:#528CBE"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Dacwadaha Socda</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                        {{ $allRecords->filter(fn($r) => in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay']))->count() }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Dacwadaha Socda</p>
                </div>
            </div>

            {{-- Closed Cases --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(240,180,60,0.12)">
                    <i class="bi bi-check2-all text-xl" style="color:#F0B43C"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Dacwadaha Xiran</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                        {{ $allRecords->filter(fn($r) => $r->Status === 'Closed')->count() }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Dacwadaha Xiran</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Search & Filter Bar --}}
            <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
                <form action="{{ route('execution-registration.index') }}" method="GET"
                    class="flex flex-wrap gap-3 items-center">

                    {{-- Page size --}}
                    <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                        <span>Show</span>
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
                            placeholder="Search by file no, reg no or remarks…"
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                                                                                                                                                          text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-[#528CBE]
                                                                                                                                                                          focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                    </div>
                    {{-- Status filter --}}
                    <select name="status" onchange="this.form.submit()"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                                                                                                                                   focus:outline-none focus:border-[#528CBE] min-w-[160px] cursor-pointer">
                        <option value="">Xaalada</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->name }}" {{ request('status') == $st->name ? 'selected' : '' }}>
                                {{ $st->name }}
                            </option>
                        @endforeach
                    </select>
                    {{-- Search button --}}
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                        style="background:#528CBE">
                        <i class="bi bi-search"></i> Search
                    </button>

                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('execution-registration.index') }}"
                            class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Title --}}
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-table text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Execution Case Registry</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">{{ $records->total() }}
                    {{ Str::plural('record', $records->total()) }}</span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Gal Lambar
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dacwada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tarikhda</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Warqadaha
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nuxurka
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                Xaalada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($records as $i => $r)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span
                                        class="text-xs font-bold text-neutral-400">{{ str_pad($records->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block font-mono text-xs font-bold px-2 py-0.5 rounded"
                                        style="background:rgba(82,140,190,0.1);color:#3D78AB">{{ $r->FileNo }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                        style="background:rgba(82,140,190,0.1);color:#528CBE">{{ $r->CaseType }}</span>
                                </td>
                                <td class="px-6 py-4 text-neutral-600 text-sm">{{ \Carbon\Carbon::parse($r->OpenDate)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-neutral-600 text-sm">{{ $r->NumberLetter ?: '—' }}</td>
                                <td class="px-6 py-4 text-neutral-500 text-sm">{{ Str::limit($r->Remarks ?? '—', 50) }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $isActive = in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                        $isClosed = $r->Status === 'Closed';
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                        style="background:{{ $isActive ? 'rgba(16,185,129,0.1)' : ($isClosed ? 'rgba(239,68,68,0.1)' : 'rgba(240,180,60,0.12)') }};color:{{ $isActive ? '#059669' : ($isClosed ? '#b91c1c' : '#C07E15') }}">
                                        {{ $r->Status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('execution-registration.supporting', $r->ECID) }}"
                                            title="Supporting Details"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#10B981';this.style.borderColor='#10B981';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-card-list text-xs"></i>
                                        </a>
                                        <a href="{{ route('execution-registration.show', $r->ECID) }}" title="Case Information"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                        <button
                                            @click="openModal({{ $r->ECID }},'{{ addslashes($r->RegisterNo) }}','{{ addslashes($r->FileNo) }}','{{ addslashes($r->GradeCourt) }}','{{ addslashes($r->CaseType) }}','{{ addslashes($r->SubCase ?? '') }}','{{ $r->OpenDate }}','{{ addslashes($r->NumberLetter ?? '') }}','{{ addslashes($r->LegalBasis ?? '') }}','{{ addslashes($r->Orders_Requested ?? '') }}','{{ addslashes($r->Remarks ?? '') }}','{{ addslashes($r->Status) }}')"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-pencil-square text-xs"></i>
                                        </button>
                                        <button @click="deleteRecord({{ $r->ECID }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#DC2626';this.style.borderColor='#DC2626';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-trash3 text-xs"></i>
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
                                            <i class="bi bi-file-earmark-ruled text-2xl" style="color:#528CBE"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">No execution cases found.</p>
                                        <button @click="openModal()"
                                            class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                                            style="background:#528CBE">
                                            Register First Case
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-neutral-100">
                {{ $records->links() }}
            </div>
        </div>

        {{-- Modal --}}
        <div x-show="isModalOpen" style="display:none;background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
            class="fixed inset-0 z-[70] flex items-center justify-center p-4 lg:pl-[260px]" @keydown.escape.window="closeModal()"
            @click.self="closeModal()">
            <div class="bg-white flex flex-col overflow-hidden w-full"
                style="max-width:1100px;max-height:94vh;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between flex-shrink-0"
                    :style="isEditMode?'background:#F0B43C;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0':'background:#528CBE;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0'">
                    <div class="flex items-center gap-3">
                        <div
                            style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                            <i :class="isEditMode?'bi bi-pencil-square':'bi bi-file-earmark-plus'"
                                style="color:white;font-size:1.1rem"></i>
                        </div>
                        <div>
                            <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0"
                                x-text="isEditMode?'Edit Civil Case':'Diiwaanka Dacwada Fulinta'"></h2>
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
                        {{-- Row 1: B.G Lambar | Magaca Maxkamadda | Gal Lambar --}}
                        <div style="display:grid;grid-template-columns:0.8fr 1fr 1.3fr;gap:1rem;margin-bottom:1.1rem">
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">B.G
                                    Lambar <span style="color:#ef4444">*</span></label>
                                <div style="position:relative">
                                    <input type="text" x-model="f.RegisterNo" required placeholder="tusaale: BG-2024-001"
                                        style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <i class="bi bi-hash"
                                        style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                                </div>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Magaca
                                    Maxkamadda <span style="color:#ef4444">*</span></label>
                                <div style="position:relative">
                                    <select x-model="f.GradeCourt" required
                                        :disabled="!isEditMode && '{{ auth()->user()->position }}' !== 'admin' && '{{ $userCourtID }}' !== ''"
                                        style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        :style="(!isEditMode && '{{ auth()->user()->position }}' !== 'admin' && '{{ $userCourtID }}' !== '') ? 'background:#f3f7fb;cursor:not-allowed' : ''"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                        <option value="">— Dooro Maxkamadda —</option>
                                        @foreach($courts as $c)<option value="{{ $c->courtcode }}">{{ $c->longName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="bi bi-chevron-down"
                                        style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                                </div>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Gal
                                    Lambar <span style="color:#ef4444">*</span></label>
                                <div style="position:relative">
                                    <input type="text" x-model="f.FileNo" required readonly placeholder="Xulo Maxkamadda..."
                                        style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #e5e7eb;border-radius:.625rem;background:#f9fafb;color:#9ca3af;outline:none;box-sizing:border-box;cursor:not-allowed">
                                    <i class="bi bi-lock"
                                        style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#d1d5db;pointer-events:none"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Gal Lambar Preview --}}
                        <div x-show="f.FileNo && !isEditMode" style="display:none;margin-bottom:1.1rem">
                            <div
                                style="background:linear-gradient(135deg,#eef4fb,#e8f0f9);border:1.5px solid #c3d9f0;border-radius:.875rem;padding:.9rem 1.1rem;display:flex;align-items:center;gap:.85rem">
                                <div
                                    style="width:34px;height:34px;background:#528CBE;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="bi bi-info-lg" style="color:white;font-size:1rem"></i>
                                </div>
                                <div>
                                    <p style="margin:0;font-size:.82rem;color:#0A284D;font-weight:600">
                                        Lambarka Dacwada Fulinta: &nbsp;<span x-text="f.FileNo"
                                            style="font-size:1rem;font-weight:800;letter-spacing:.04em;color:#0A284D"></span>
                                    </p>
                                    <p style="margin:.2rem 0 0;font-size:.72rem;color:#528CBE">
                                        Tani waa muuqaal hore oo lambarka la siin doono marka foomka la gudbinayo.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" x-model="f.CaseType">
                        {{-- Row 2: Taariikhda Furitaanka | Xaaladda | Lambarka Warqadda --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.1rem">
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Taariikhda
                                    Furitaanka <span style="color:#ef4444">*</span></label>
                                <div style="position:relative">
                                    <input type="date" x-model="f.OpenDate" required
                                        style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;cursor:pointer;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <i class="bi bi-calendar3"
                                        style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                                </div>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Xaaladda</label>
                                <div style="position:relative">
                                    <select x-model="f.Status" required
                                        style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#f9fafb;color:#111827;outline:none;appearance:none;cursor:default;box-sizing:border-box;transition:border-color .15s,box-shadow .15s;pointer-events:none"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                        <option value="Diwaan Galin">Diwaan Galin</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Lambarka
                                    Warqadda</label>
                                <div style="position:relative">
                                    <input type="text" x-model="f.NumberLetter" placeholder="tusaale: WRQ-001"
                                        style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <i class="bi bi-hash"
                                        style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" x-model="f.LegalBasis">
                        {{-- Codsiga Dacwoodaha--}}
                        <div style="margin-bottom:1.1rem">
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                                Codsiga Dacwoodaha</label>
                            <div style="position:relative">
                                <textarea x-model="f.Orders_Requested" rows="3"
                                    placeholder="Sharax amarrada la codsanayo..."
                                    style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;resize:vertical;min-height:90px;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"></textarea>
                            </div>
                        </div>
                        {{-- Mooduca Dacwadda --}}
                        <div style="margin-bottom:1.5rem">
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Mooduca
                                Dacwadda</label>
                            <div style="position:relative">
                                <textarea x-model="f.Remarks" rows="3" placeholder="Faallo dheeraad ah..."
                                    style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;resize:vertical;min-height:90px;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"></textarea>
                            </div>
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
            {{-- Detailed View Modal --}}
            <div x-show="viewModalOpen"
                class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-neutral-900/60 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" @click.self="viewModalOpen = false" style="display:none">
                <div
                    class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden animate__animated animate__zoomIn animate__faster">
                    {{-- Header --}}
                    <div class="bg-neutral-800 p-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-white">
                                <i class="bi bi-info-circle-fill text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-white leading-tight">Case Details</h2>
                                <p class="text-xs text-white/60 font-bold uppercase tracking-widest"
                                    x-text="'File No: ' + v.FileNo"></p>
                            </div>
                        </div>
                        <button @click="viewModalOpen = false"
                            class="w-10 h-10 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-all flex items-center justify-center">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="p-8 max-h-[80vh] overflow-y-auto">
                        <div class="grid grid-cols-2 gap-8 mb-8">
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Register
                                    Number</label>
                                <p class="text-sm font-black text-neutral-800 mt-1" x-text="v.RegisterNo"></p>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Court
                                    Name</label>
                                <p class="text-sm font-black text-[#528CBE] mt-1" x-text="v.GradeCourt"></p>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Case
                                    Type</label>
                                <p class="text-sm font-black text-neutral-800 mt-1 uppercase" x-text="v.CaseType"></p>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Opening
                                    Date</label>
                                <p class="text-sm font-black text-neutral-800 mt-1" x-text="v.OpenDate"></p>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Status</label>
                                <span
                                    class="inline-block mt-1 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest"
                                    :style="['Active','Gal Ku Qoris','Qaatay'].includes(v.Status) ? 'background:rgba(34,197,94,0.1);color:#15803d' : (v.Status == 'Closed' ? 'background:rgba(239,68,68,0.1);color:#b91c1c' : 'background:rgba(240,180,60,0.1);color:#C07E15')"
                                    x-text="v.Status"></span>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Letter
                                    Number</label>
                                <p class="text-sm font-black text-neutral-800 mt-1" x-text="v.NumberLetter || '—'"></p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100">
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Legal
                                    Basis</label>
                                <p class="text-sm font-medium text-neutral-700 mt-2 leading-relaxed"
                                    x-text="v.LegalBasis || '—'"></p>
                            </div>
                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100">
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Orders
                                    Requested</label>
                                <p class="text-sm font-medium text-neutral-700 mt-2 leading-relaxed"
                                    x-text="v.Orders_Requested || '—'"></p>
                            </div>
                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100">
                                <label
                                    style="display:block;font-size:.68rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Remarks</label>
                                <p class="text-sm font-medium text-neutral-700 mt-2 leading-relaxed"
                                    x-text="v.Remarks || '—'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="p-6 bg-neutral-50 border-t border-neutral-100 flex justify-end">
                        <button @click="viewModalOpen = false"
                            class="px-8 py-2.5 bg-neutral-800 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-neutral-900 transition-all">Close
                            Details</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('executionRegManager', () => ({
                    isModalOpen: false, isEditMode: false, isSubmitting: false, currentId: null,
                    viewModalOpen: false, v: {},
                    f: { RegisterNo: '', FileNo: '', GradeCourt: '{{ $userCourtID ?? "" }}', CaseType: 'Fulinta', SubCase: '', OpenDate: '{{ date("Y-m-d") }}', NumberLetter: '', LegalBasis: 'Dacwadda Fulinta', Orders_Requested: '', Remarks: '', Status: 'Diwaan Galin' },
                    init() {
                        this.$watch('f.GradeCourt', (courtcode) => {
                            if (courtcode && !this.isEditMode) this.generateFileNo(courtcode);
                        });
                    },
                    openModal(id = null, RegisterNo = '', FileNo = '', GradeCourt = '{{ $userCourtID ?? "" }}', CaseType = 'Fulinta', SubCase = '', OpenDate = '{{ date("Y-m-d") }}', NumberLetter = '', LegalBasis = 'Dacwadda Fulinta', Orders_Requested = '', Remarks = '', Status = 'Diwaan Galin') {
                        this.isEditMode = !!id; this.currentId = id;
                        this.f = { RegisterNo, FileNo, GradeCourt, CaseType, SubCase, OpenDate, NumberLetter, LegalBasis, Orders_Requested, Remarks, Status };
                        this.isModalOpen = true;
                        if (this.f.GradeCourt) this.generateFileNo(this.f.GradeCourt);
                    },
                    closeModal() { this.isModalOpen = false; this.currentId = null; this.f = { RegisterNo: '', FileNo: '', GradeCourt: '{{ $userCourtID ?? "" }}', CaseType: 'Fulinta', SubCase: '', OpenDate: '{{ date("Y-m-d") }}', NumberLetter: '', LegalBasis: 'Dacwadda Fulinta', Orders_Requested: '', Remarks: '', Status: 'Diwaan Galin' }; },
                    async generateFileNo(courtcode) {
                        if (!courtcode || this.isEditMode) return;
                        this.f.FileNo = '';
                        try {
                            const res = await fetch(`/execution-registration/next-fileno/${encodeURIComponent(courtcode)}`, { headers: { 'Accept': 'application/json' } });
                            if (!res.ok) return;
                            const data = await res.json();
                            if (data.fileNo) this.f.FileNo = data.fileNo;
                        } catch (e) { console.error('generateFileNo failed:', e); }
                    },
                    async submitForm() {
                        this.isSubmitting = true;
                        try {
                            const url = this.isEditMode ? `/execution-registration/${this.currentId}` : '/execution-registration';
                            const method = this.isEditMode ? 'PUT' : 'POST';
                            const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: JSON.stringify(this.f) });
                            const data = await res.json();
                            if (res.ok && data.success) {
                                this.closeModal();
                                Swal.fire({ title: 'Guul!', text: data.message, icon: 'success', confirmButtonText: 'Hagaag', confirmButtonColor: '#528CBE', customClass: { title: 'text-2xl font-bold text-neutral-800', confirmButton: 'px-8 py-2 rounded-lg font-bold uppercase tracking-wider text-sm' }, buttonsStyling: true, showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' }, hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' } }).then(() => window.location.reload());
                            } else throw new Error(data.message || 'Xaqiijintu waa fashilantay.');
                        } catch (e) {
                            Swal.fire({ title: 'Khalad!', text: e.message, icon: 'error', confirmButtonText: 'Hagaag', confirmButtonColor: '#DC2626', customClass: { title: 'text-2xl font-bold text-neutral-800', confirmButton: 'px-8 py-2 rounded-lg font-bold uppercase tracking-wider text-sm' }, buttonsStyling: true, showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' }, hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' } });
                        } finally { this.isSubmitting = false; }
                    },
                    deleteRecord(id) {
                        Swal.fire({ title: 'Ma hubtaa?', text: "Ma awoodi doontid inaad dib u celiso!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#DC2626', cancelButtonColor: '#528CBE', confirmButtonText: 'Haa, tirtir!', cancelButtonText: 'Jooji', customClass: { title: 'text-2xl font-bold text-neutral-800', confirmButton: 'px-6 py-2 rounded-lg font-bold text-sm mx-1', cancelButton: 'px-6 py-2 rounded-lg font-bold text-sm mx-1' }, buttonsStyling: true, showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' }, hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' } })
                            .then(async r => {
                                if (r.isConfirmed) {
                                    try {
                                        const res = await fetch(`/execution-registration/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
                                        const data = await res.json();
                                        if (res.ok && data.success) {
                                            Swal.fire({ title: 'La tirtiray!', text: data.message, icon: 'success', confirmButtonText: 'Hagaag', confirmButtonColor: '#528CBE', customClass: { title: 'text-2xl font-bold text-neutral-800', confirmButton: 'px-8 py-2 rounded-lg font-bold uppercase tracking-wider text-sm' }, buttonsStyling: true, showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' }, hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' } }).then(() => window.location.reload());
                                        } else throw new Error(data.message || 'Tirtirku wuu fashilmay.');
                                    } catch (e) {
                                        Swal.fire({ title: 'Khalad!', text: e.message, icon: 'error', confirmButtonText: 'Hagaag', confirmButtonColor: '#DC2626', customClass: { title: 'text-2xl font-bold text-neutral-800', confirmButton: 'px-8 py-2 rounded-lg font-bold uppercase tracking-wider text-sm' }, buttonsStyling: true });
                                    }
                                }
                            });
                    },
                    viewRecord(id) {
                        const record = @json($records).find(r => r.ECID === id);
                        if (record) {
                            this.v = record;
                            this.viewModalOpen = true;
                        }
                    }
                }));
            });
        </script>
@endsection