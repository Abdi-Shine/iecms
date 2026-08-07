@extends('admin.admin_master')
@section('page_title', $internal ? 'OB Gudaha' : 'Buugagga Dhacdooyinka')
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
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $internal ? 'OB Gudaha' : 'Buugagga Dhacdooyinka (OB)' }}</h1>
                <p class="text-sm text-neutral-500 mt-0.5">
                    {{ $internal ? "Dhacdooyinka heer saldhig ah iyo dhacdooyinka shaqaalaha ee CID ka soo saartay gudaheeda" : 'Dhammaan diiwaanada buugga dhacdooyinka ee kiisaska CID' }}
                </p>
            </div>
            <form action="{{ route('criminal-cases.store') }}" method="POST">
                @csrf
                <input type="hidden" name="skip_to_ob" value="1">
                <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90 bg-primary-400">
                    <i class="bi bi-plus-lg"></i> {{ $internal ? 'Ku Dar OB Gudaha Cusub' : 'Ku Dar Diiwaan OB Cusub' }}
                </button>
            </form>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-primary-400/12">
                    <i class="bi bi-journal-plus text-xl text-primary-400"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Qabyo</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['draft'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-primary-400">Weli Lama Xilsaarin</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gold-400/12">
                    <i class="bi bi-person-check-fill text-xl text-gold-400"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">La Xilsaaray</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['assigned'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-gold-400">Sugaya Xaqiijinta Agaasimaha</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-primary-900/10">
                    <i class="bi bi-patch-check-fill text-xl text-primary-900"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Firfircoon</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['active'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-primary-900">Waa La Xaqiijiyay</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-neutral-500/12">
                    <i class="bi bi-archive-fill text-xl text-neutral-500"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">La Xiray</p>
                    <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['closed'] }}</h3>
                    <p class="text-xs font-medium mt-0.5 text-neutral-500">Kiisas La Xiray</p>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            {{-- Search & Filter Bar --}}
            <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
                <form action="{{ request()->url() }}" method="GET" class="flex flex-wrap gap-3 items-center">

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
                            placeholder="Raadi lambarka OB ama lambarka kiiska…" class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                                           text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-primary-900
                                                           focus:ring-2 focus:ring-primary-900/10 transition-all">
                    </div>

                    <input type="text" name="location" value="{{ request('location') }}" placeholder="Deggo"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                       placeholder-neutral-400 focus:outline-none focus:border-primary-900 min-w-[150px]">

                    <input type="text" name="offence_type" value="{{ request('offence_type') }}" placeholder="Nooca Dambiga"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                       placeholder-neutral-400 focus:outline-none focus:border-primary-900 min-w-[150px]">

                    <select name="status" onchange="this.form.submit()"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                                                       focus:outline-none focus:border-primary-900 min-w-[160px] cursor-pointer">
                        <option value="all">Xaalada</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ['Draft' => 'Qabyo', 'Assigned' => 'La Xilsaaray', 'Active' => 'Firfircoon', 'Closed' => 'La Xiray'][$status] ?? $status }}
                            </option>
                        @endforeach
                    </select>

                    <input type="date" name="from" value="{{ request('from') }}" title="Laga Bilaabo"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600 focus:outline-none focus:border-primary-900">
                    <input type="date" name="to" value="{{ request('to') }}" title="Ilaa"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600 focus:outline-none focus:border-primary-900">

                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2 bg-primary-400">
                        <i class="bi bi-search"></i> Raadi
                    </button>

                    @if(request()->anyFilled(['search', 'status', 'location', 'offence_type', 'from', 'to']))
                        <a href="{{ request()->url() }}"
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
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">{{ $internal ? 'OB Gudaha' : 'Buugagga Dhacdooyinka' }}</span>
                </div>
                <span class="text-xs text-neutral-400 font-medium">{{ $obs->total() }} diiwaan</span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-primary-900/5">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Lambarka OB</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Kiiska</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dambiga</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">La Xilsaaray</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Mudnaanta</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @php
                            $priorityLabels = ['Routine' => 'Caadi', 'Urgent' => 'Degdeg', 'Critical' => 'Aad u Degdeg'];
                            $priorityColors = [
                                'Routine'  => 'bg-success-500/12 text-success-600',
                                'Urgent'   => 'bg-gold-400/14 text-gold-600',
                                'Critical' => 'bg-danger-600/18 text-red-800',
                            ];
                            $statusLabels = ['Draft' => 'Qabyo', 'Assigned' => 'La Xilsaaray', 'Active' => 'Firfircoon', 'Closed' => 'La Xiray'];
                            $statusColors = [
                                'Draft'    => 'bg-neutral-500/12 text-neutral-500',
                                'Assigned' => 'bg-teal-100 text-teal-600',
                                'Active'   => 'bg-primary-900/12 text-primary-900',
                                'Closed'   => 'bg-neutral-500/12 text-neutral-500',
                            ];
                        @endphp
                        @forelse($obs as $i => $ob)
                            @php
                                $pc = $priorityColors[$ob->priority] ?? 'bg-neutral-500/10 text-neutral-500';
                                $sc = $statusColors[$ob->statusLabel()] ?? 'bg-primary-900/10 text-primary-900';
                            @endphp
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-xs font-bold text-neutral-400">
                                    {{ sprintf('%02d', $obs->firstItem() + $i) }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('criminal-cases.workflow', $ob->criminal_case_id) }}"
                                        class="font-mono text-sm font-bold text-primary-400">{{ $ob->ob_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-neutral-600">{{ $ob->criminalCase->case_number ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-neutral-800 text-sm">{{ $ob->offence_nature }}</p>
                                </td>
                                <td class="px-6 py-4 text-neutral-600">{{ $ob->assignedInvestigator->name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $pc }}">{{ $priorityLabels[$ob->priority] ?? $ob->priority }}</span>
                                </td>
                                <td class="px-6 py-4 text-neutral-600 text-sm">{{ $ob->ob_datetime->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $sc }}">
                                        {{ $statusLabels[$ob->statusLabel()] ?? $ob->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('criminal-cases.workflow', $ob->criminal_case_id) }}" title="Fur Diiwaanka"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-primary-900 hover:border-primary-900 hover:text-white">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center bg-primary-900/8">
                                            <i class="bi bi-journal-x text-2xl text-primary-900"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">Ma jiraan diiwaanno buugga dhacdooyinka ah oo la mid ah shaandhaystahan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-neutral-100">
                {{ $obs->links() }}
            </div>
        </div>

    </div>
@endsection
