@extends('admin.admin_master')
@section('page_title', 'Soo Celinta Faylka — IECMS')
@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="p-4 sm:p-6 w-full">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Soo Celinta Faylasha</h1>
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
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['total'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-primary">Dhamaan dacwadaha diiwanka</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-success">
                    <i class="bi bi-check2-circle text-xl text-success"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">La Soo Celiyay</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                        {{ $stats['returned'] }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5 text-success">Faylasha la soo celiyay</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-gold">
                    <i class="bi bi-hourglass-split text-xl text-gold"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Sugaya</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                        {{ $stats['pending'] }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5 text-gold">Sugaya xaqiijinta</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 kpi-icon-primary">
                    <i class="bi bi-calendar-check text-xl text-primary"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Bishan Galinta</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                        {{ $stats['thisMonth'] }}
                    </h3>
                    <p class="text-xs font-medium mt-0.5 text-primary">Dacwadaha bishan la furay</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Search & Filter --}}
            <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
                <form action="{{ route('criminal-return-file.index') }}" method="GET"
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
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Raadi lambarka kiiska ama nooca dacwadda..."
                               class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                      text-neutral-700 placeholder-neutral-400 outline-none focus:border-[#528CBE]
                                      focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                    </div>
                    <select name="status" onchange="this.form.submit()"
                            class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                   outline-none focus:border-[#528CBE] cursor-pointer transition-all"
                            style="min-width:150px">
                        <option value="all">Dhamaad Xaalada</option>
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
                        <a href="{{ route('criminal-return-file.index') }}"
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
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Diiwanka Dacwadaha</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">
                    {{ $records->total() }} dacwadood guud
                </span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="table-header-tint">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Gal Lambarka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dacwada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Furitaanka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tirada Warqadda</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nuxurka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500" style="width:11rem">Mudada 30-ka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-36">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($records as $i => $r)
                            @php
                                $rf  = $r->returnFile;
                                $rfs = $rf?->status;
                                $isGreen  = in_array($r->Status, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                $isClosed = $r->Status === 'Closed';
                                $sBg    = $isGreen ? 'rgba(34,197,94,.12)'   : ($isClosed ? 'rgba(239,68,68,.1)'   : 'rgba(240,180,60,.12)');
                                $sColor = $isGreen ? '#15803d'               : ($isClosed ? '#b91c1c'              : '#C07E15');

                                $latestJudgment    = $r->judgments->sortByDesc('created_at')->first();
                                $jReceipts         = $latestJudgment?->receipts ?? collect();
                                $latestReceiptDate = null;
                                if ($latestJudgment && $jReceipts->isNotEmpty()) {
                                    $latestReceiptDate = $jReceipts
                                        ->filter(fn($rc) => $rc->received_date)
                                        ->sortByDesc('received_date')
                                        ->first()?->received_date;
                                }
                                $jDate       = $latestReceiptDate ? \Carbon\Carbon::parse($latestReceiptDate) : null;
                                $daysElapsed = $jDate ? max(0, $jDate->startOfDay()->diffInDays(now()->startOfDay())) : null;
                                $daysLeft    = $daysElapsed !== null ? max(0, 30 - $daysElapsed) : null;
                                $pct         = $daysElapsed !== null ? min(100, (int) round($daysElapsed / 30 * 100)) : 0;
                                $barColor    = $pct < 50 ? '#16a34a' : ($pct < 84 ? '#C07E15' : '#dc2626');
                                $barTrack    = $pct < 50 ? 'rgba(34,197,94,.12)' : ($pct < 84 ? 'rgba(240,180,60,.15)' : 'rgba(239,68,68,.1)');
                                $labelColor  = $pct < 50 ? '#15803d' : ($pct < 84 ? '#92600a' : '#b91c1c');
                            @endphp
                            <tr class="hover:bg-neutral-50 transition-colors">

                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-neutral-400">{{ str_pad($records->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge-case-type">{{ $r->FileNo }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge-role">{{ $r->CaseType }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-neutral-600">{{ \Carbon\Carbon::parse($r->OpenDate)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-neutral-600">{{ $r->NumberLetter ?: '—' }}</td>
                                <td class="px-6 py-4 text-sm text-neutral-600">{{ Str::limit($r->Remarks ?? '—', 45) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                          style="background:{{ $sBg }};color:{{ $sColor }}">
                                        {{ $r->Status }}
                                    </span>
                                </td>

                                {{-- 30-day progress column: starts only after parties sign --}}
                                <td style="padding:10px 16px;">
                                    @if($latestJudgment && !$jDate)
                                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:600;color:#9ca3af;">
                                            <i class="bi bi-pen" style="font-size:.75rem;"></i> Sugaya Saxiixa
                                        </span>
                                    @elseif($jDate)
                                        <div style="display:flex;flex-direction:column;gap:4px;">
                                            <div style="height:6px;border-radius:99px;background:{{ $barTrack }};overflow:hidden;">
                                                <div style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;"></div>
                                            </div>
                                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                                @if($daysElapsed <= 30)
                                                    <span style="font-size:.72rem;font-weight:700;color:{{ $labelColor }};">
                                                        {{ $daysLeft }} Maalmaha
                                                    </span>
                                                @else
                                                    <span style="font-size:.72rem;font-weight:700;color:#b91c1c;">
                                                        <i class="bi bi-exclamation-circle-fill" style="font-size:.7rem;"></i>
                                                        {{ $daysElapsed - 30 }} Maalmo Ka Dhaafay
                                                    </span>
                                                @endif
                                                <span style="font-size:.7rem;color:#9ca3af;">{{ $pct }}%</span>
                                            </div>
                                            <span style="font-size:.7rem;color:#6b7280;">{{ min($daysElapsed, 30) }} / 30 maalmood</span>
                                        </div>
                                    @else
                                        <span style="font-size:.8rem;color:#d1d5db;">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Create / Edit return file --}}
                                        <a href="{{ route('criminal-return-file.create', $r->CMID) }}"
                                           title="Soo Celi Faylka"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                           onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                           onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-arrow-return-left text-xs"></i>
                                        </a>

                                        {{-- Document button --}}
                                        @if($rf)
                                            <a href="{{ route('criminal-return-file.document', $r->CMID) }}"
                                               title="Dukuumintiga Soo Celinta"
                                               class="w-8 h-8 flex items-center justify-center rounded-lg border transition-all"
                                               style="background:rgba(10,40,77,.07);border-color:rgba(10,40,77,.18);color:#0A284D"
                                               onmouseover="this.style.background='#0A284D';this.style.borderColor='#0A284D';this.style.color='white'"
                                               onmouseout="this.style.background='rgba(10,40,77,.07)';this.style.borderColor='rgba(10,40,77,.18)';this.style.color='#0A284D'">
                                                <i class="bi bi-file-earmark-text text-xs"></i>
                                            </a>
                                        @else
                                            <span title="Dukuuminti ma jirto"
                                                  class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-100"
                                                  style="color:#d1d5db;cursor:default">
                                                <i class="bi bi-file-earmark-text text-xs"></i>
                                            </span>
                                        @endif

                                        {{-- View case --}}
                                        <a href="{{ route('criminal-registration.show', $r->CMID) }}"
                                           title="Arag Macluumaadka Dacwadda"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                           onmouseover="this.style.background='#0EA5E9';this.style.borderColor='#0EA5E9';this.style.color='white'"
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
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center kpi-icon-primary">
                                            <i class="bi bi-arrow-return-left text-2xl text-primary"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">Dacwad ciqaabta ah lama helin.</p>
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
