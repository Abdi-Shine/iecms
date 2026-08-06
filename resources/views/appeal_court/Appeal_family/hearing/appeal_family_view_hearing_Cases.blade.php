@extends('admin.admin_master')
@section('page_title', 'Case Handover Registry')
@section('admin_main_content')

    <div class="p-4 sm:p-6 w-full">

        {{-- Header branding removed --}}

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Diiwaanka Mudeynta Dacwadaha</h1>
            </div>
        </div>

        {{-- KPI Cards (Hearing Related) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total Hearings --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-primary">
                    <i class="bi bi-megaphone text-xl text-primary"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Wadarta Mudeymaha</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $hearingStats['total'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-primary">Diiwaanada hore</p>
                </div>
            </div>
            {{-- Scheduled --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-gold">
                    <i class="bi bi-calendar-event text-xl text-gold"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">La mudeeyay</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $hearingStats['scheduled'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-gold">Fadhiyada soo socda</p>
                </div>
            </div>
            {{-- Completed --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-success">
                    <i class="bi bi-check2-all text-xl text-success"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Dhameystiran</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $hearingStats['completed'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-success">Dacwadaha la soo afjaray</p>
                </div>
            </div>
            {{-- This Month --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-primary">
                    <i class="bi bi-calendar-month text-xl text-primary"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Bishaan</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $hearingStats['thisMonth'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-primary">Muddada hadda</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Search & Filter --}}
            <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
                <form action="{{ route('appeal-family.hearing.cases') }}" method="GET"
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
                    <div class="relative flex-1 min-w-[200px]">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Raadi lambarka faylka ama nooca dacwada..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                                          text-neutral-700 placeholder-neutral-400 outline-none focus:border-[#528CBE]
                                                          focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                    </div>
                    <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                       outline-none focus:border-[#528CBE] cursor-pointer transition-all"
                        style="min-width:150px">
                        <option value="all">Dhammaan Xaaladaha</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->name }}" {{ request('status') == $s->name ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                        style="background:#528CBE">
                        <i class="bi bi-search"></i> Search
                    </button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('appeal-family.hearing.cases') }}"
                           class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Title --}}
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-table text-sm text-primary"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Diiwaanka Dacwadaha</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">
                    {{ $records->total() }} wadarta dacwadaha
                </span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="table-header-tint">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">No#</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Lambarka
                                Faylka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Dacwada
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                                Furitaanka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Lambarka
                                Waraaqda</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nuxurka
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th
                                class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-44">
                                Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($records as $i => $r)
                            <tr class="hover:bg-neutral-50 transition-colors">

                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-neutral-400">
                                        {{ str_pad($records->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}
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
                                        if ($r->Status === 'Mudeyn') {
                                            $sBg    = 'rgba(59,130,246,.12)';
                                            $sColor = '#1d4ed8';
                                        } elseif (in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay'])) {
                                            $sBg    = 'rgba(34,197,94,.12)';
                                            $sColor = '#15803d';
                                        } elseif ($r->Status === 'Closed') {
                                            $sBg    = 'rgba(239,68,68,.1)';
                                            $sColor = '#b91c1c';
                                        } else {
                                            $sBg    = 'rgba(240,180,60,.12)';
                                            $sColor = '#C07E15';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                        style="background:{{ $sBg }};color:{{ $sColor }}">
                                        {{ $r->Status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('appeal-family-registration.show', $r->AFCID) }}"
                                            title="Eeg Macluumaadka Buuxa ee Dacwada"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#0EA5E9';this.style.borderColor='#0EA5E9';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('appeal-family-hearings.create', $r->AFCID) }}" title="Mudee Dacwada"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-calendar-plus text-xs"></i>
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
                                        <p class="text-neutral-400 font-medium text-sm">Wax dacwado qoyska ah lama helin.</p>
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

    </div>

    {{-- Footer branding removed --}}

    {{-- Print styles extracted to resources/css/app.css under "Listing Pages — Print" section --}}

@endsection