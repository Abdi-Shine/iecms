@extends('admin.admin_master')
@section('page_title', 'Hubinta Dacwada Cabashada — IECMS')
@section('admin_main_content')

@php
    $statusColors = [
        'Diiwaan'            => 'bg-info-50 text-info-600',
        'Gal Ku Qoris'       => 'bg-teal-100 text-teal-600',
        'Baaritaan Socda'    => 'bg-warning-100 text-warning-600',
        'Hubin'              => 'bg-violet-100 text-violet-600',
        'La Eedeeyay'        => 'bg-violet-100 text-violet-600',
        'Ku Jira Maxkamadda' => 'bg-info-100 text-info-700',
        'La Xukumay'         => 'bg-success-100 text-success-600',
        'La Sii Daayay'      => 'bg-neutral-100 text-neutral-600',
        'La Diiday'          => 'bg-red-100 text-red-600',
        'La Xiray'           => 'bg-neutral-100 text-neutral-600',
    ];
    $statusLabels = [];
@endphp

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Hubinta Dacwada Cabashada</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Hubi oo faallo ku dar dacwadaha, qeexi taariikhda iyo waaxda loo
                qoondeeyay</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('attorney-case-reviews.index', array_merge(request()->except(['review_status', 'page']), ['review_status' => 'all'])) }}"
            class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-primary-400/10">
                <i class="bi bi-folder2-open text-xl text-primary-400"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Wadarta</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['total'] }}</h3>
            </div>
        </a>

        <a href="{{ route('attorney-case-reviews.index', array_merge(request()->except(['review_status', 'page']), ['review_status' => 'reviewed'])) }}"
            class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-success-500/10">
                <i class="bi bi-check2-circle text-xl text-success-500"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">La Hubiyay</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['reviewed'] }}</h3>
            </div>
        </a>

        <a href="{{ route('attorney-case-reviews.index', array_merge(request()->except(['review_status', 'page']), ['review_status' => 'unreviewed'])) }}"
            class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gold-400/12">
                <i class="bi bi-hourglass-split text-xl text-gold-400"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Sugaya Hubin</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['unreviewed'] }}</h3>
            </div>
        </a>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        {{-- Filters --}}
        <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
            <form action="{{ route('attorney-case-reviews.index') }}" method="GET"
                class="flex flex-wrap gap-3 items-center">

                <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                    <span>Tus</span>
                    <select name="per_page" onchange="this.form.submit()"
                        class="px-3 py-1.5 text-sm font-semibold border border-neutral-300 rounded-full bg-white text-neutral-700
                               focus:outline-none focus:border-primary-400 cursor-pointer">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="relative flex-1 min-w-[200px]">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Raadi lambarka dacwadda, cinwaanka ama magaca cabaanaha..."
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                               text-neutral-700 placeholder-neutral-400 outline-none focus:border-primary-400
                               focus:ring-2 focus:ring-primary-400/20 transition-all">
                </div>

                <select name="review_status" onchange="this.form.submit()"
                    class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                           outline-none focus:border-primary-400 cursor-pointer transition-all min-w-[160px]">
                    <option value="all" {{ request('review_status', 'all') == 'all' ? 'selected' : '' }}>Dhammaan</option>
                    <option value="reviewed" {{ request('review_status') == 'reviewed' ? 'selected' : '' }}>La Hubiyay</option>
                    <option value="unreviewed" {{ request('review_status') == 'unreviewed' ? 'selected' : '' }}>Sugaya
                        Hubin</option>
                </select>

                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2 bg-primary-400">
                    <i class="bi bi-search"></i> Raadi
                </button>

                @if(request()->anyFilled(['search', 'review_status']))
                    <a href="{{ route('attorney-case-reviews.index') }}"
                        class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                        <i class="bi bi-x-circle"></i> Nadiifi
                    </a>
                @endif
            </form>
        </div>

        {{-- Card Header --}}
        <div class="px-6 py-4 flex items-center justify-between border-b border-neutral-100">
            <div class="flex items-center gap-2">
                <i class="bi bi-clipboard2-check text-sm text-primary-400"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Diiwaanka Hubinta</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">
                {{ $cases->total() }} dacwadood guud
            </span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-primary-400/6">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">T.T</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Gal Lambar</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Dhinacyada</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Buuxa</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Dacwada</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Waaxda La
                            Qoondeeyay</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-28">
                            Ficilada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @php
                        $caseTypeColors = [
                            'Shaqsi'    => 'bg-primary-400/12 text-primary-400',
                            'Shirkad'   => 'bg-violet-600/10 text-violet-600',
                            "Hay'ad"    => 'bg-gold-400/14 text-gold-600',
                            'Dawladeed' => 'bg-primary-900/10 text-primary-900',
                            'Qaraan'    => 'bg-success-600/12 text-success-600',
                        ];
                    @endphp
                    @forelse($cases as $i => $c)
                        @php
                            $lr = $c->latestReview;
                            $ctc = $caseTypeColors[$c->case_type] ?? 'bg-neutral-500/10 text-neutral-500';
                        @endphp
                        <tr class="hover:bg-neutral-50 transition-colors">

                            <td class="px-4 py-3.5">
                                <span class="text-xs font-bold text-neutral-400">{{ str_pad($cases->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>

                            <td class="px-4 py-3.5">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full inline-block w-fit bg-primary-400/10 text-primary-500">
                                    {{ $c->case_number ?? '—' }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                @if($c->case_type)
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap {{ $ctc }}">{{ $c->case_type }}</span>
                                @else
                                    <span class="text-xs text-neutral-300">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5">
                                @if($c->complainants->first())
                                    <span class="text-xs text-neutral-600">{{ $c->complainants->first()->full_name }}</span>
                                @else
                                    <span class="text-xs text-neutral-300">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5">
                                <span class="text-xs font-semibold text-neutral-700">{{ $c->title ?? '—' }}</span>
                            </td>

                            <td class="px-4 py-3.5">
                                @if($lr && $lr->department)
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap bg-violet-600/10 text-violet-600">{{ $lr->department->name }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wide bg-gold-400/12 text-gold-600">Sugaya Hubin</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-xs text-neutral-500 font-medium whitespace-nowrap">
                                {{ $lr ? $lr->review_date->format('d/m/Y') : '—' }}
                            </td>

                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wide whitespace-nowrap {{ $statusColors[$c->status] ?? 'bg-neutral-100 text-neutral-600' }}">
                                    {{ $statusLabels[$c->status] ?? $c->status }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('attorney-case-reviews.show', $c->ACID) }}" title="Hubi"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-primary-400 hover:border-primary-400 hover:text-white">
                                        <i class="bi bi-clipboard2-check text-xs"></i>
                                    </a>
                                    <a href="{{ route('attorney-cases.show', $c->ACID) }}" title="Arag Dacwadda"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-primary-400 hover:border-primary-400 hover:text-white">
                                        <i class="bi bi-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center bg-primary-400/10">
                                        <i class="bi bi-folder2-open text-2xl text-primary-400"></i>
                                    </div>
                                    <p class="text-neutral-400 font-semibold text-sm">Dacwad lama helin.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $cases->links() }}
        </div>

    </div>
</div>

@endsection
