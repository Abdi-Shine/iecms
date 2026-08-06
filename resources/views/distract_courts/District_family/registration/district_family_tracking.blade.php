@extends('admin.admin_master')
@section('page_title', 'Lasocodka Dacwadaha Qoyska')
@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="p-4 sm:p-6 w-full">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Lasocodka Dacwadaha Qoyska</h1>
                <p class="text-sm text-neutral-500 mt-0.5">La soco dhammaan dacwadaha qoyska — dhinacyada, garsoorayaasha,
                    wareejinta, iyo xaaladda</p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <a href="{{ route('family-case-tracking.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}"
                class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4 cursor-pointer"
                title="Dhammaan dacwadaha">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(82,140,190,0.1)">
                    <i class="bi bi-folder2-open text-xl" style="color:#528CBE"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Wadarta</p>
                    <h3 class="text-2xl font-black text-neutral-800">{{ $stats['total'] }}</h3>
                </div>
            </a>

            <a href="{{ route('family-case-tracking.index', array_merge(request()->except(['status', 'page']), ['status' => 'Active'])) }}"
                class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4 cursor-pointer"
                title="Shaandhee firfircoon">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(16,185,129,0.1)">
                    <i class="bi bi-activity text-xl" style="color:#10B981"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Firfircoon</p>
                    <h3 class="text-2xl font-black text-neutral-800">{{ $stats['active'] }}</h3>
                </div>
            </a>

            <a href="{{ route('family-case-tracking.index', array_merge(request()->except(['status', 'page']), ['status' => 'Sug Qaatay'])) }}"
                class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4 cursor-pointer"
                title="Shaandhee sugaya wareejinta">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(240,180,60,0.12)">
                    <i class="bi bi-hourglass-split text-xl" style="color:#F0B43C"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Sug Qaatay</p>
                    <h3 class="text-2xl font-black text-neutral-800">{{ $stats['pending'] }}</h3>
                </div>
            </a>

            <a href="{{ route('family-case-tracking.index', array_merge(request()->except(['status', 'page']), ['status' => 'Qaatay'])) }}"
                class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4 cursor-pointer"
                title="Shaandhee dhammaystiran">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(34,197,94,0.1)">
                    <i class="bi bi-check2-circle text-xl" style="color:#22C55E"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Qaatay</p>
                    <h3 class="text-2xl font-black text-neutral-800">{{ $stats['done'] }}</h3>
                </div>
            </a>

            <a href="{{ route('family-case-tracking.index', array_merge(request()->except(['status', 'page']), ['status' => 'Closed'])) }}"
                class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4 cursor-pointer"
                title="Shaandhee la xiray">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(239,68,68,0.1)">
                    <i class="bi bi-archive text-xl" style="color:#DC2626"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">La Xiray</p>
                    <h3 class="text-2xl font-black text-neutral-800">{{ $stats['closed'] }}</h3>
                </div>
            </a>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Filters --}}
            <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
                <form action="{{ route('family-case-tracking.index') }}" method="GET"
                    class="flex flex-wrap gap-3 items-center">

                    {{-- Page size --}}
                    <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                        <span>Tus</span>
                        <select name="per_page" onchange="this.form.submit()"
                            class="px-3 py-1.5 text-sm font-semibold border border-neutral-300 rounded-full bg-white text-neutral-700
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
                            placeholder="Raadi lambarka faylka, magaca dhinaca, telefoonka, ama magaca hooyada..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                                                                  text-neutral-700 placeholder-neutral-400 outline-none focus:border-[#528CBE]
                                                                                  focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                    </div>

                    {{-- Status filter --}}
                    <select name="status" onchange="this.form.submit()"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                                               outline-none focus:border-[#528CBE] cursor-pointer transition-all" style="min-width:160px">
                        <option value="all">Xaaladaha </option>
                        @foreach($allStatuses as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                        style="background:#528CBE">
                        <i class="bi bi-search"></i> Raadi
                    </button>

                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('family-case-tracking.index') }}"
                            class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> Nadiifi
                        </a>
                    @endif
                </form>
            </div>

            {{-- Card Header --}}
            <div class="px-6 py-4 flex items-center justify-between border-b border-neutral-100">
                <div class="flex items-center gap-2">
                    <i class="bi bi-diagram-3 text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Diiwaanka Lasocodka</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">
                    {{ $records->total() }} diiwaan
                </span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Gal Lambar
                            </th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dacwada</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tarikhda</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Garsoore</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th
                                class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-28">
                                Falalka</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($records as $i => $r)
                            @php
                                $cs = $r->Status ?? 'Active';
                                $isGreen = in_array($cs, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                $isAmber = in_array($cs, ['Sug Qaatay', 'Diwaan Galin']);
                                $isRed = $cs === 'Closed';
                                $cStatusBg = $isGreen ? 'rgba(34,197,94,.1)' : ($isRed ? 'rgba(239,68,68,.1)' : 'rgba(240,180,60,.12)');
                                $cStatusColor = $isGreen ? '#16A34A' : ($isRed ? '#DC2626' : '#C07E15');

                                $judge = $r->assignments->first()?->employee;
                            @endphp
                            <tr class="hover:bg-neutral-50 transition-colors">

                                <td class="px-4 py-3.5">
                                    <span
                                        class="text-xs font-bold text-neutral-400">{{ str_pad($records->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full inline-block w-fit"
                                            style="background:rgba(82,140,190,0.1);color:#3D78AB">
                                            {{ $r->FileNo ?? '—' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                        style="background:rgba(240,180,60,0.12);color:#C07E15">
                                        {{ $r->CaseType ?? '—' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 text-xs text-neutral-500 font-medium whitespace-nowrap">
                                    {{ $r->OpenDate ? \Carbon\Carbon::parse($r->OpenDate)->format('d/m/Y') : '—' }}
                                </td>

                                <td class="px-4 py-3.5">
                                    @if($judge)
                                        <span class="text-xs font-semibold text-neutral-700">{{ $judge->EmpName }}</span>
                                    @else
                                        <span class="text-xs text-neutral-300 italic">Unassigned</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wide whitespace-nowrap"
                                        style="background:{{ $cStatusBg }};color:{{ $cStatusColor }}">
                                        {{ $cs }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- View Case --}}
                                        <a href="{{ route('family-registration.show', $r->FCID) }}" title="View Case Details"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                            style="background:rgba(82,140,190,0.1)">
                                            <i class="bi bi-folder2-open text-2xl" style="color:#528CBE"></i>
                                        </div>
                                        <p class="text-neutral-400 font-semibold text-sm">Dacwad qoys ah lama helin.</p>
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

@endsection