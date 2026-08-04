@extends('admin.admin_master')
@section('page_title', 'Socodsinta Cabashada — IECMS')
@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="p-4 sm:p-6 w-full">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Socodsinta Cabashada</h1>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(82,140,190,0.12)">
                    <i class="bi bi-file-earmark-ruled text-xl" style="color:#528CBE"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Wadarta Dacwadaha</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['total'] }}</h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Dhamaan Dacwadaha diiwanka</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(240,180,60,0.12)">
                    <i class="bi bi-check2-circle text-xl" style="color:#F0B43C"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Dacwadaha Ku Qoran</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['assigned'] }}</h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Dacwadaha Ku Qoran Garsoorayaasha</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(82,140,190,0.12)">
                    <i class="bi bi-person-exclamation text-xl" style="color:#528CBE"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Dacwadaha Aan Ku Qoran</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['unassigned'] }}</h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Sugaya Gal Ku Qorid</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(240,180,60,0.12)">
                    <i class="bi bi-activity text-xl" style="color:#F0B43C"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Xaalada Nidaamka</p>
                    <h3 class="text-lg font-black text-neutral-800 mt-1">Hawlgal</h3>
                    <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Dhammaan adeegyada waa socota</p>
                </div>
            </div>
        </div>

        {{-- Cases Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Search & Filter --}}
            <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
                <form action="{{ route('attorney-cases.socodsinta') }}" method="GET"
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

                    <div class="relative flex-1 min-w-[200px]">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Raadi lambarka kiiska ama nooca dacwadda..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                                                               text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-[#528CBE]
                                                                               focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                    </div>

                    <select name="case_type" onchange="this.form.submit()"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                                           focus:outline-none focus:border-[#528CBE] min-w-[160px] cursor-pointer">
                        <option value="">Nooca Dacwada</option>
                        @foreach($caseTypes as $type)
                            <option value="{{ $type }}" {{ request('case_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>

                    <select name="assign_status" onchange="this.form.submit()"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                                           focus:outline-none focus:border-[#528CBE] min-w-[160px] cursor-pointer">
                        <option value="">Dhamaad Xaalada</option>
                        <option value="assigned" {{ request('assign_status') == 'assigned' ? 'selected' : '' }}>Loo Xilsaaray
                        </option>
                        <option value="unassigned" {{ request('assign_status') == 'unassigned' ? 'selected' : '' }}>Sugaya
                        </option>
                    </select>

                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                        style="background:#528CBE">
                        <i class="bi bi-search"></i> Search
                    </button>

                    @if(request()->anyFilled(['search', 'assign_status', 'case_type']))
                        <a href="{{ route('attorney-cases.socodsinta') }}"
                            class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> Nadiifi
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Header Bar --}}
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-table text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Diiwanka Dacwadaha</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">
                    {{ $cases->total() }} dacwadood guud
                </span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-16">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Gal Lambarka
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca
                                Dhinacyada
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Buuxa
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Dacwada
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                                Furitaanka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xeer
                                Ilaaliyaha</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nuxurka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th
                                class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-28">
                                Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($cases as $index => $c)
                            <tr class="hover:bg-neutral-50 transition-colors">

                                <td class="px-6 py-4">
                                    <span
                                        class="text-xs font-bold text-neutral-400">{{ str_pad($cases->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded"
                                        style="background:rgba(82,140,190,0.1);color:#3D78AB">{{ $c->case_number }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    @if($c->case_type)
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                            style="background:rgba(82,140,190,0.1);color:#528CBE">{{ $c->case_type }}</span>
                                    @else
                                        <span class="text-xs text-neutral-300">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($c->complainants->first())
                                        <span class="text-xs text-neutral-600">{{ $c->complainants->first()->full_name }}</span>
                                    @else
                                        <span class="text-xs text-neutral-300">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-neutral-700 font-medium">{{ Str::limit($c->title, 40) }}</td>

                                <td class="px-6 py-4 text-neutral-600 text-xs">
                                    {{ $c->date_reported ? \Carbon\Carbon::parse($c->date_reported)->format('d/m/Y') : '—' }}
                                </td>

                                <td class="px-6 py-4">
                                    @forelse($c->prosecutorAssignments as $a)
                                        <div class="text-xs font-semibold text-neutral-700">{{ $a->prosecutor->EmpName ?? '—' }}
                                        </div>
                                    @empty
                                        <span class="text-xs text-neutral-300">—</span>
                                    @endforelse
                                </td>

                                <td class="px-6 py-4 text-neutral-500 text-xs">
                                    <span>{{ Str::limit($c->summary ?? '—', 35) }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $isGreen = in_array($c->status, ['La Xukumay', 'Ku Jira Maxkamadda']);
                                        $isClosed = in_array($c->status, ['La Xiray', 'La Diiday', 'La Sii Daayay']);
                                        $sBg = $isGreen ? 'rgba(34,197,94,.12)' : ($isClosed ? 'rgba(239,68,68,.1)' : 'rgba(240,180,60,.12)');
                                        $sColor = $isGreen ? '#15803d' : ($isClosed ? '#b91c1c' : '#C07E15');
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                        style="background:{{ $sBg }};color:{{ $sColor }}">
                                        {{ $c->status ?: 'Sugaya' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('attorney-cases.workflow', $c->ACID) }}"
                                            title="Case Workflow"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-diagram-3 text-xs"></i>
                                        </a>
                                        <a href="{{ route('attorney-cases.show', $c->ACID) }}" title="Arag Dacwadda"
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
                                <td colspan="9" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                            style="background:rgba(82,140,190,0.1)">
                                            <i class="bi bi-folder2-open text-2xl" style="color:#528CBE"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">Dacwad lama helin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-neutral-100">
                {{ $cases->links() }}
            </div>
        </div>

    </div>

@endsection