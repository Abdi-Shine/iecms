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
            <a href="{{ route('execution-registration.create') }}"
                class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90"
                style="background:#528CBE">
                <i class="bi bi-plus-lg"></i> Kudar Dacwad Cusub
            </a>
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
                                        <a href="{{ route('execution-registration.edit', $r->ECID) }}" title="Edit"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-pencil-square text-xs"></i>
                                        </a>
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
                                        <a href="{{ route('execution-registration.create') }}"
                                            class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                                            style="background:#528CBE">
                                            Register First Case
                                        </a>
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

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('executionRegManager', () => ({
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
                    }
                }));
            });
        </script>
@endsection