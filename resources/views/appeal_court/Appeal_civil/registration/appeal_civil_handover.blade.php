@extends('admin.admin_master')
@section('page_title', 'Diiwaan Dhaqdhaqaaqa Dacwadaha — IECMS')
@section('admin_main_content')

    <div class="p-4 sm:p-6 w-full" x-data="{ search: '', status: 'all' }" x-init="$watch('search', () => $nextTick(() => filterRows()));
                                                          $watch('status',  () => $nextTick(() => filterRows()));">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Dhaqdhaqaaqa Dacwadaha Madaniga</h1>

            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-primary">
                    <i class="bi bi-file-earmark-ruled text-xl text-primary"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Wadarta Guud</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ count($records) }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-primary">Dhamaan dacwadaha diiwanka</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-success">
                    <i class="bi bi-check2-circle text-xl text-success"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Gal Ku Qoris</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                        {{ $records->filter(fn($r) => $r->Status === 'Gal Ku Qoris')->count() }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5 text-success">Dacwadaha ku qoran garsoorayaasha</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-gold">
                    <i class="bi bi-hourglass-split text-xl text-gold"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Diwaan Galin</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                        {{ $records->filter(fn($r) => $r->Status === 'Diwaan Galin')->count() }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5 text-gold">Sugaya galinta</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-primary">
                    <i class="bi bi-calendar-check text-xl text-primary"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Bishan Galinta</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                        {{ $records->filter(fn($r) => date('Y-m', strtotime($r->OpenDate ?? '')) == date('Y-m'))->count() }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5 text-primary">Dacwadaha bishan la furay</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Search & Filter --}}
            <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
                <div class="flex flex-wrap gap-3 items-center">
                    <div class="relative flex-1 min-w-[200px]">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                        <input x-model="search" type="text" placeholder="Raadi lambarka kiiska ama nooca dacwadda..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                                                              text-neutral-700 placeholder-neutral-400 outline-none focus:border-[#528CBE]
                                                                              focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                    </div>
                    <select x-model="status"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                                           outline-none focus:border-[#528CBE] cursor-pointer transition-all"
                        style="min-width:150px">
                        <option value="all">Dhamaad Xaalada</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->name }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Table Title --}}
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-table text-sm text-primary"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Diiwanka Dacwadaha</span>
                </div>
                <span id="total-count" class="text-xs text-neutral-400 font-medium">
                    {{ count($records) }} dacwadood guud
                </span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="table-header-tint">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Gal Lambarka
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Dacwadda
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                                Furitaanka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tirada
                                Warqadda</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nuxurka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th
                                class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-36">
                                Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($records as $i => $r)
                            <tr class="hover:bg-neutral-50 transition-colors" data-row
                                data-fileno="{{ strtolower($r->FileNo) }}" data-casetype="{{ strtolower($r->CaseType) }}"
                                data-status="{{ $r->Status }}">

                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-neutral-400">
                                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="badge-case-type">{{ $r->FileNo }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="badge-role">{{ $r->CaseType }}</span>
                                </td>

                                <td class="px-6 py-4 text-sm text-neutral-600">{{ \Carbon\Carbon::parse($r->OpenDate)->format('d/m/Y') }}</td>

                                <td class="px-6 py-4 text-sm text-neutral-600">{{ $r->NumberLetter ?: '—' }}</td>

                                <td class="px-6 py-4 text-sm text-neutral-600">
                                    {{ Str::limit($r->Remarks ?? '—', 45) }}
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $isGreen = in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                        $isAmber = in_array($r->Status, ['Sug Qaatay', 'Diwaan Galin']);
                                        $isClosed = $r->Status === 'Closed';
                                        $sBg = $isGreen ? 'rgba(34,197,94,.12)' : ($isClosed ? 'rgba(239,68,68,.1)' : 'rgba(240,180,60,.12)');
                                        $sColor = $isGreen ? '#15803d' : ($isClosed ? '#b91c1c' : '#C07E15');
                                        $sDot = $isGreen ? '#16a34a' : ($isClosed ? '#dc2626' : '#F0B43C');
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                        style="background:{{ $sBg }};color:{{ $sColor }}">

                                        {{ $r->Status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @php $hs = $r->handover?->status; @endphp
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('appeal-civil-handover.create', $r->ACID) }}"
                                            title="Dhaqdhaqaaq / Xukumi"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200
                                                                                                                                                  text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-person-fill-add text-xs"></i>
                                        </a>
                                        <a href="{{ route('appeal-civil-handover.document', $r->ACID) }}"
                                            title="Arag Warqadda Dhaqdhaqaaqa"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200
                                                                                                                                                  text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#10B981';this.style.borderColor='#10B981';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-file-earmark-text text-xs"></i>
                                        </a>
                                        @if($hs === 'Sug Qaatay')
                                            <button type="button" onclick="approveHandover({{ $r->ACID }}, this)"
                                                title="Ogolow Dhaqdhaqaaqa"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border transition-all"
                                                style="border-color:#10B981;color:#10B981;background:rgba(16,185,129,0.08)"
                                                onmouseover="this.style.background='#10B981';this.style.color='white'"
                                                onmouseout="this.style.background='rgba(16,185,129,0.08)';this.style.color='#10B981'">
                                                <i class="bi bi-check-lg text-xs"></i>
                                            </button>
                                        @elseif($hs === 'Qaatay')
                                            <span title="La Ogolaaday" class="w-8 h-8 flex items-center justify-center rounded-lg"
                                                style="background:rgba(16,185,129,0.12);color:#10B981">
                                                <i class="bi bi-check-circle-fill text-xs"></i>
                                            </span>
                                        @endif
                                        <a href="{{ route('appeal-civil-registration.show', $r->ACID) }}"
                                            title="Arag Macluumaadka Dacwadda"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200
                                                                                                                                                  text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#0EA5E9';this.style.borderColor='#0EA5E9';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center kpi-icon-primary">
                                            <i class="bi bi-file-earmark-ruled text-2xl text-primary"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">Dacwad madani lama helin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="no-results-row" style="display:none">
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full flex items-center justify-center kpi-icon-primary">
                                        <i class="bi bi-search text-xl text-primary"></i>
                                    </div>
                                    <p class="text-neutral-400 font-medium text-sm">Dacwad lama helin raadintaada.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-neutral-100 flex items-center justify-between">
                <p class="text-xs text-neutral-400 font-medium">
                    Waxaa la muujinayaa <span id="showing-count"
                        class="font-bold text-neutral-600">{{ count($records) }}</span>
                    diiwaan
                </p>
                <span id="record-count" class="text-xs text-neutral-400 font-medium">
                    {{ count($records) }} guud
                </span>
            </div>
        </div>

    </div>

    <script>
        function filterRows() {
            const search = document.querySelector('[x-model="search"]')?.value?.toLowerCase() ?? '';
            const status = document.querySelector('[x-model="status"]')?.value ?? 'all';
            const rows = document.querySelectorAll('[data-row]');
            let visible = 0;

            rows.forEach(row => {
                const fileNo = row.dataset.fileno ?? '';
                const caseType = row.dataset.casetype ?? '';
                const rowStatus = row.dataset.status ?? '';

                const matchSearch = !search || fileNo.includes(search) || caseType.includes(search);
                const matchStatus = status === 'all' || rowStatus === status;

                if (matchSearch && matchStatus) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            const noResults = document.getElementById('no-results-row');
            if (noResults) noResults.style.display = visible === 0 ? '' : 'none';

            ['total-count', 'showing-count', 'record-count'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                if (id === 'total-count') el.textContent = visible + ' dacwadood guud';
                if (id === 'showing-count') el.textContent = visible;
                if (id === 'record-count') el.textContent = visible + ' guud';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('[x-model="search"]')?.addEventListener('input', filterRows);
            document.querySelector('[x-model="status"]')?.addEventListener('change', filterRows);
        });

        async function approveHandover(caseId, btn) {
            const { value: password, isConfirmed } = await Swal.fire({
                title: 'Saxiix oo Aqbal Wareejinta',
                html: `<p style="font-size:.85rem;color:#6b7280;margin-bottom:.75rem">Geli erayga sirta ah si aad si dhab ah u saxiixdo oo aad u aqbasho wareejintan.</p>
                       <input type="password" id="swal-password" placeholder="Erayga sirta ah..."
                            style="width:100%;padding:.6rem .8rem;font-size:.85rem;
                                   border:1.5px solid #e5e7eb;border-radius:.5rem;outline:none;
                                   font-family:inherit" autocomplete="current-password">`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Saxiix oo Aqbal',
                cancelButtonText: 'Jooji',
                preConfirm: () => {
                    const pass = document.getElementById('swal-password').value;
                    if (!pass) { Swal.showValidationMessage('Fadlan erayga sirta ah geli.'); return false; }
                    return pass;
                },
            });
            if (!isConfirmed) return;

            try {
                const res = await fetch(`/appeal-civil-handover/${caseId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ password }),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    await Swal.fire({
                        title: 'La Ogolaaday!', text: data.message, icon: 'success',
                        confirmButtonColor: '#10B981', timer: 1800, showConfirmButton: false
                    });
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Approval failed.');
                }
            } catch (e) {
                Swal.fire({ title: 'Khalad!', text: e.message, icon: 'error', confirmButtonColor: '#DC2626' });
            }
        }
    </script>

@endsection