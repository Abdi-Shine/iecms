@extends('admin.admin_master')
@section('page_title', 'Oggolaanshaha Dhaqdhaqaaqa — IECMS')
@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="p-4 sm:p-6 w-full">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Oggolaanshaha Dhaqdhaqaaqa</h1>
                <p class="text-sm text-neutral-500 mt-0.5">Dib u eeg, oggolow ama diibi codsiyada dhaqdhaqaaqa dacwadaha qoyska</p>
            </div>
            {{-- Approve All button removed --}}
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <a href="{{ route('appeal-family-handover.approval', array_merge(request()->except(['status', 'page']), ['status' => 'Sug Qaatay'])) }}"
               class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4 cursor-pointer" title="Shaandhee sugayaasha">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(240,180,60,0.12)">
                    <i class="bi bi-hourglass-split text-xl" style="color:#F0B43C"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Sugaya</p>
                    <h3 class="text-2xl font-black text-neutral-800" id="kpi-pending">{{ $pending }}</h3>
                </div>
            </a>
            <a href="{{ route('appeal-family-handover.approval', array_merge(request()->except(['status', 'page']), ['status' => 'Qaatay'])) }}"
               class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4 cursor-pointer" title="Shaandhee la ogolaaday">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(16,185,129,0.12)">
                    <i class="bi bi-check-circle text-xl" style="color:#10B981"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">La Ogolaaday</p>
                    <h3 class="text-2xl font-black text-neutral-800" id="kpi-approved">{{ $approved }}</h3>
                </div>
            </a>
            <a href="{{ route('appeal-family-handover.approval', array_merge(request()->except(['status', 'page']), ['status' => 'Rejected'])) }}"
               class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4 cursor-pointer" title="Shaandhee la diiday">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(239,68,68,0.1)">
                    <i class="bi bi-x-circle text-xl" style="color:#DC2626"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">La Diiday</p>
                    <h3 class="text-2xl font-black text-neutral-800" id="kpi-rejected">{{ $rejected }}</h3>
                </div>
            </a>
            <a href="{{ route('appeal-family-handover.approval', array_merge(request()->except(['status', 'page']), ['status' => 'Draft'])) }}"
               class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4 cursor-pointer" title="Shaandhee muswaaadooyinka">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(82,140,190,0.12)">
                    <i class="bi bi-file-earmark text-xl" style="color:#528CBE"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Muswaaado</p>
                    <h3 class="text-2xl font-black text-neutral-800" id="kpi-draft">{{ $draft }}</h3>
                </div>
            </a>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Search & Filter bar --}}
            <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
                <form action="{{ route('appeal-family-handover.approval') }}" method="GET"
                      class="flex flex-wrap gap-3 items-center">

                    {{-- Page size --}}
                    <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                        <span>Show</span>
                        <select name="per_page" onchange="this.form.submit()"
                            class="px-3 py-1.5 text-sm font-semibold border border-neutral-300 rounded-full bg-white text-neutral-700
                                   focus:outline-none focus:border-[#528CBE] cursor-pointer">
                            @foreach([10, 25, 50, 100] as $n)
                                <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="relative flex-1 min-w-[200px]">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Raadi lambarka kiiska, maxkamadda, ama cidda gudbisay..."
                               class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                      text-neutral-700 placeholder-neutral-400 outline-none focus:border-[#528CBE]
                                      focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                    </div>

                    {{-- Case Type filter --}}
                    <select name="casetype" onchange="this.form.submit()"
                            class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                   outline-none focus:border-[#528CBE] cursor-pointer transition-all"
                            style="min-width:150px">
                        <option value="all">Dhammaan Noocyada</option>
                        @foreach($caseTypes as $ct)
                            <option value="{{ strtolower($ct) }}" {{ request('casetype') == strtolower($ct) ? 'selected' : '' }}>{{ $ct }}</option>
                        @endforeach
                    </select>

                    {{-- Status filter --}}
                    <select name="status" onchange="this.form.submit()"
                            class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                   outline-none focus:border-[#528CBE] cursor-pointer transition-all"
                            style="min-width:170px">
                        <option value="all" {{ request('status', 'Sug Qaatay') == 'all' ? 'selected' : '' }}>Dhammaan Xaalado</option>
                        <option value="Sug Qaatay" {{ request('status', 'Sug Qaatay') == 'Sug Qaatay' ? 'selected' : '' }}>Sug Qaatay</option>
                        <option value="Qaatay" {{ request('status', 'Sug Qaatay') == 'Qaatay' ? 'selected' : '' }}>Qaatay</option>
                        <option value="Rejected" {{ request('status', 'Sug Qaatay') == 'Rejected' ? 'selected' : '' }}>La Diiday</option>
                        <option value="Draft" {{ request('status', 'Sug Qaatay') == 'Draft' ? 'selected' : '' }}>Muswaaado</option>
                    </select>

                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                        style="background:#528CBE">
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
            </div>

            {{-- Card Header --}}
            <div class="px-6 py-4 flex items-center justify-between border-b border-neutral-100">
                <div class="flex items-center gap-2">
                    <i class="bi bi-clipboard2-check text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Diiwaanka Dhaqdhaqaaqa</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">
                    {{ $handovers->total() }} diiwaanood
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Gal Lambarka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Dacwadda</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Cidda Gudbisay</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Warqadaha</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-36">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($handovers as $i => $h)
                            @php
                                $hs = $h->status ?? 'Draft';
                                $statusColor = match($hs) {
                                    'Qaatay'         => ['bg' => 'rgba(16,185,129,0.12)',  'color' => '#10B981'],
                                    'Rejected'         => ['bg' => 'rgba(239,68,68,0.1)',    'color' => '#DC2626'],
                                    'Sug Qaatay' => ['bg' => 'rgba(240,180,60,0.12)',  'color' => '#C07E15'],
                                    default            => ['bg' => 'rgba(82,140,190,0.1)',   'color' => '#528CBE'],
                                };
                            @endphp
                            <tr class="hover:bg-neutral-50 transition-colors"
                                id="row-{{ $h->id }}">

                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-neutral-400">{{ str_pad($handovers->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                                          style="background:rgba(82,140,190,0.1);color:#3D78AB">
                                        {{ $h->case->FileNo ?? '—' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                          style="background:rgba(240,180,60,0.12);color:#C07E15">
                                        {{ $h->case->CaseType ?? '—' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-xs font-semibold text-neutral-700">{{ $h->created_by ?? '—' }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    @php $docCount = !empty($h->documents) ? count($h->documents) : 0; @endphp
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                                          style="background:rgba(82,140,190,0.08);color:#528CBE">
                                        {{ $docCount }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider whitespace-nowrap"
                                          style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }}">
                                        {{ $hs }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-xs text-neutral-500 font-medium">
                                    {{ \Carbon\Carbon::parse($h->updated_at)->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- View --}}
                                        <a href="{{ route('appeal-family-handover.document', $h->family_case_id) }}"
                                           title="Arag Warqadda"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                           onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                           onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-file-earmark-text text-xs"></i>
                                        </a>

                                        @if($hs === 'Sug Qaatay')
                                        {{-- Approve --}}
                                        <button type="button"
                                                onclick="approveSingle({{ $h->family_case_id }}, {{ $h->id }}, this)"
                                                title="Oggolow"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border transition-all"
                                                style="border-color:#10B981;color:#10B981;background:rgba(16,185,129,0.08)"
                                                onmouseover="this.style.background='#10B981';this.style.color='white'"
                                                onmouseout="this.style.background='rgba(16,185,129,0.08)';this.style.color='#10B981'">
                                            <i class="bi bi-check-lg text-xs"></i>
                                        </button>
                                        {{-- Reject --}}
                                        <button type="button"
                                                onclick="rejectSingle({{ $h->family_case_id }}, {{ $h->id }}, this)"
                                                title="Diid"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border transition-all"
                                                style="border-color:#DC2626;color:#DC2626;background:rgba(239,68,68,0.06)"
                                                onmouseover="this.style.background='#DC2626';this.style.color='white'"
                                                onmouseout="this.style.background='rgba(239,68,68,0.06)';this.style.color='#DC2626'">
                                            <i class="bi bi-x-lg text-xs"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                             style="background:rgba(16,185,129,0.1)">
                                            <i class="bi bi-check2-all text-2xl" style="color:#10B981"></i>
                                        </div>
                                        <p class="text-neutral-400 font-semibold text-sm">Diiwaanka dhaqdhaqaaqa lama helin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-neutral-100">
                {{ $handovers->links() }}
            </div>

        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        // After approving/rejecting, the item no longer matches whatever
        // status filter is currently active (e.g. "Sug Qaatay"), so a plain
        // reload can make the list look like it emptied out. Switch the
        // status filter to "all" on reload so the just-processed item (and
        // everything else) stays visible.
        function reloadShowingAll() {
            const params = new URLSearchParams(window.location.search);
            params.set('status', 'all');
            window.location.href = window.location.pathname + '?' + params.toString();
        }

        async function approveSingle(caseId, rowId, btn) {
            const { value: password, isConfirmed } = await Swal.fire({
                title: 'Saxiix oo Aqbal Wareejinta',
                html: `<p style="font-size:.85rem;color:#6b7280;margin-bottom:.75rem">Geli erayga sirta ah si aad si dhab ah u saxiixdo oo aad u aqbasho wareejintan.</p>
                       <input type="password" id="swal-password" placeholder="Erayga sirta ah..."
                            style="width:100%;padding:.6rem .8rem;font-size:.85rem;
                                   border:1.5px solid #e5e7eb;border-radius:.5rem;outline:none;
                                   font-family:inherit" autocomplete="current-password">`,
                icon: 'question', showCancelButton: true,
                confirmButtonColor: '#10B981', cancelButtonColor: '#6b7280',
                confirmButtonText: 'Saxiix oo Aqbal',
                preConfirm: () => {
                    const pass = document.getElementById('swal-password').value;
                    if (!pass) { Swal.showValidationMessage('Fadlan erayga sirta ah geli.'); return false; }
                    return pass;
                },
            });
            if (!isConfirmed) return;

            setLoading(btn, true);
            try {
                const res  = await fetch(`/appeal-family-handover/${caseId}/approve`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ password }),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    const row = document.getElementById(`row-${rowId}`);
                    if (row) {
                        row.dataset.status = 'Qaatay';
                        row.querySelector('[data-status-badge]')?.remove();
                    }
                    nudgeKpi('kpi-pending',  -1);
                    nudgeKpi('kpi-approved', +1);
                    Swal.fire({ title: 'Approved!', icon: 'success', timer: 1500, showConfirmButton: false });
                    setTimeout(reloadShowingAll, 1600);
                } else throw new Error(data.message || 'Failed.');
            } catch (e) {
                setLoading(btn, false);
                Swal.fire({ title: 'Error!', text: e.message, icon: 'error', confirmButtonColor: '#DC2626' });
            }
        }

        async function rejectSingle(caseId, rowId, btn) {
            const { value: formValues, isConfirmed } = await Swal.fire({
                title: 'Reject Handover',
                html: `<textarea id="swal-reason" placeholder="Enter reason for rejection (optional)..."
                            style="width:100%;min-height:90px;padding:.6rem .8rem;font-size:.85rem;
                                   border:1.5px solid #e5e7eb;border-radius:.5rem;outline:none;
                                   resize:vertical;font-family:inherit;margin-bottom:.6rem" maxlength="500"></textarea>
                       <input type="password" id="swal-password" placeholder="Erayga sirta ah (si aad u xaqiijiso)..."
                            style="width:100%;padding:.6rem .8rem;font-size:.85rem;
                                   border:1.5px solid #e5e7eb;border-radius:.5rem;outline:none;
                                   font-family:inherit" autocomplete="current-password">`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#DC2626', cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Reject',
                preConfirm: () => {
                    const pass = document.getElementById('swal-password').value;
                    if (!pass) { Swal.showValidationMessage('Fadlan erayga sirta ah geli.'); return false; }
                    return { reason: document.getElementById('swal-reason').value.trim(), password: pass };
                },
            });
            if (!isConfirmed) return;

            setLoading(btn, true);
            try {
                const res  = await fetch(`/appeal-family-handover/${caseId}/reject`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(formValues),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    nudgeKpi('kpi-pending',  -1);
                    nudgeKpi('kpi-rejected', +1);
                    Swal.fire({ title: 'Rejected', icon: 'info', timer: 1500, showConfirmButton: false });
                    setTimeout(reloadShowingAll, 1600);
                } else throw new Error(data.message || 'Failed.');
            } catch (e) {
                setLoading(btn, false);
                Swal.fire({ title: 'Error!', text: e.message, icon: 'error', confirmButtonColor: '#DC2626' });
            }
        }



        function setLoading(btn, state) {
            btn.disabled = state;
            const icon = btn.querySelector('i');
            if (state) icon.className = 'bi bi-arrow-repeat text-xs spinning';
            else icon.className = btn.title === 'Approve' ? 'bi bi-check-lg text-xs' : 'bi bi-x-lg text-xs';
        }

        function nudgeKpi(id, delta) {
            const el = document.getElementById(id);
            if (el) el.textContent = Math.max(0, parseInt(el.textContent) + delta);
        }
    </script>

@endsection
