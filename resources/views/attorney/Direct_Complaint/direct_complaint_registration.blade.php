@extends('admin.admin_master')
@section('page_title', 'Diiwaanka Dacwadaha — Direct Complaint')
@section('admin_main_content')

    <div class="p-4 sm:p-6 w-full">

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white bg-success-600">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Diiwaanka Cabashada toosan</h1>
            </div>
            <a href="{{ route('attorney-cases.create') }}"
                class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90 bg-primary-400">
                <i class="bi bi-plus-lg"></i> Kudar cabasho Cusub
            </a>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-primary-400/12">
                    <i class="bi bi-file-earmark-plus-fill text-xl text-primary-400"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Furan</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['open'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-primary-400">Cabashooyinka Cusub</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gold-400/12">
                    <i class="bi bi-binoculars-fill text-xl text-gold-400"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Baaritaan Socda</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['under_investigation'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-gold-400">Waa La Baarayaa Hadda</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-primary-900/10">
                    <i class="bi bi-patch-check-fill text-xl text-primary-900"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Dhammaystiran</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['complete'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-primary-900">Dacwad Socota Maxkamad</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-neutral-500/12">
                    <i class="bi bi-journal-check text-xl text-neutral-500"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Xiran</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['closed'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-neutral-500">La Xiray Ama La Diiday</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Search & Filter Bar --}}
            <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
                <form action="{{ route('attorney-cases.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">

                    <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                        <span>Tus</span>
                        <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 text-sm font-semibold border border-neutral-300 rounded-full bg-white text-neutral-700
                                                           focus:outline-none focus:border-primary-900 cursor-pointer">
                            @foreach([10, 25, 50, 100] as $n)
                                <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative flex-1 min-w-[200px]">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Raadi lambarka, cinwaanka ama faahfaahinta…" class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                                           text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-primary-900
                                                           focus:ring-2 focus:ring-primary-900/10 transition-all">
                    </div>

                    <select name="case_type" onchange="this.form.submit()"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                       focus:outline-none focus:border-primary-900 min-w-[150px] cursor-pointer">
                        <option value="all">Nooca Dhinacyada</option>
                        @foreach($caseTypes as $type)
                            <option value="{{ $type }}" {{ request('case_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>

                    <select name="priority" onchange="this.form.submit()"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                       focus:outline-none focus:border-primary-900 min-w-[150px] cursor-pointer">
                        <option value="all">Mudnaanta</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>
                                {{ $priority }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" onchange="this.form.submit()"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                       focus:outline-none focus:border-primary-900 min-w-[160px] cursor-pointer">
                        <option value="all">Xaalada</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2 bg-primary-400">
                        <i class="bi bi-search"></i> Raadi
                    </button>

                    @if(request()->anyFilled(['search', 'status', 'priority', 'case_type']))
                        <a href="{{ route('attorney-cases.index') }}"
                            class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> Nadiifi
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Title --}}
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-table text-sm text-primary-900"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Diiwaanka Cabashada
                        Toosan</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">{{ $cases->total() }} diiwaan</span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-primary-900/5">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Gal
                                lambar</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca
                                Dhinacyada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Buuxa
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Dacwada
                            </th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Mudnaanta</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tariikhda</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @php
                            $statusColors = [
                                'Diiwaan'            => 'bg-primary-400/12 text-primary-400',
                                'Gal Ku Qoris'       => 'bg-teal-100 text-teal-600',
                                'Baaritaan Socda'    => 'bg-gold-400/12 text-gold-400',
                                'Hubin'              => 'bg-violet-600/12 text-violet-600',
                                'La Eedeeyay'        => 'bg-violet-600/12 text-violet-600',
                                'Ku Jira Maxkamadda' => 'bg-primary-900/12 text-primary-900',
                                'La Xukumay'         => 'bg-success-600/12 text-success-600',
                                'La Sii Daayay'      => 'bg-neutral-500/12 text-neutral-500',
                                'La Diiday'          => 'bg-danger-600/12 text-danger-600',
                                'La Xiray'           => 'bg-neutral-500/12 text-neutral-500',
                            ];
                            $caseTypeColors = [
                                'Shaqsi'    => 'bg-primary-400/12 text-primary-400',
                                'Shirkad'   => 'bg-violet-600/10 text-violet-600',
                                "Hay'ad"    => 'bg-gold-400/14 text-gold-600',
                                'Dawladeed' => 'bg-primary-900/10 text-primary-900',
                                'Qaraan'    => 'bg-success-600/12 text-success-600',
                            ];
                            $priorityColors = [
                                'Hoose'       => 'bg-success-500/12 text-success-600',
                                'Dhexdhexaad' => 'bg-gold-400/14 text-gold-600',
                                'Sarreeya'    => 'bg-danger-600/12 text-danger-600',
                                'Degdeg ah'   => 'bg-danger-600/18 text-red-800',
                            ];
                        @endphp
                        @forelse($cases as $i => $case)
                            @php
                                $complainant = $case->complainants->first();
                                $statusLabels = [];
                                $ctc = $caseTypeColors[$case->case_type] ?? 'bg-neutral-500/10 text-neutral-500';
                                $pc = $priorityColors[$case->priority] ?? 'bg-neutral-500/10 text-neutral-500';
                                $sc = $statusColors[$case->status] ?? 'bg-primary-900/10 text-primary-900';
                            @endphp
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-xs font-bold text-neutral-400">
                                    {{ sprintf('%02d', $cases->firstItem() + $i) }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('attorney-cases.show', $case->ACID) }}"
                                        class="font-mono text-sm font-bold text-primary-400">{{ $case->case_number }}</a>
                                </td>
                                <td class="px-6 py-4">
                                    @if($case->case_type)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $ctc }}">{{ $case->case_type }}</span>
                                    @else
                                        <span class="text-xs text-neutral-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($complainant)
                                        <p class="font-semibold text-neutral-800 text-sm">{{ $complainant->full_name }}</p>
                                        @if($complainant->phone_number)
                                            <p class="text-xs text-neutral-400 flex items-center gap-1">
                                                <i class="bi bi-telephone-fill"></i> {{ $complainant->phone_number }}
                                            </p>
                                        @endif
                                    @else
                                        <span class="text-xs text-neutral-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('attorney-cases.show', $case->ACID) }}"
                                        class="font-semibold text-neutral-800 hover:text-primary-900">{{ $case->title }}</a>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $pc }}">{{ $case->priority }}</span>
                                </td>
                                <td class="px-6 py-4 text-neutral-600 text-sm">{{ $case->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $sc }}">
                                        {{ $statusLabels[$case->status] ?? $case->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('attorney-cases.show', $case->ACID) }}" title="Faahfaahinta Dacwadda"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-primary-900 hover:border-primary-900 hover:text-white">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('attorney-cases.edit', $case->ACID) }}" title="Wax Ka Beddel"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-primary-400 hover:border-primary-400 hover:text-white">
                                            <i class="bi bi-pencil-square text-xs"></i>
                                        </a>
                                        <form action="{{ route('attorney-cases.destroy', $case->ACID) }}" method="POST"
                                            onsubmit="return confirm('Ma hubtaa?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Tirtir"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-danger-600 hover:border-danger-600 hover:text-white">
                                                <i class="bi bi-trash3 text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center bg-primary-900/8">
                                            <i class="bi bi-file-earmark-text text-2xl text-primary-900"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">Dacwad lama helin.</p>
                                        <a href="{{ route('attorney-cases.create') }}"
                                            class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg transition hover:opacity-90 bg-primary-400">
                                            Dacwad Cusub
                                        </a>
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
