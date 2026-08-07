@extends('admin.admin_master')
@section('page_title', 'Kaydka OB')
@section('admin_main_content')

    <div class="p-4 sm:p-6 w-full">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Kaydka Buugga Dhacdooyinka (OB)</h1>
                <p class="text-sm text-neutral-500 mt-0.5">Akhriska Kaliya &mdash; buugagga dhacdooyinka ee kiisaska la xiray</p>
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
                            placeholder="Raadi lambarka OB, dambiga ama lambarka kiiska…" class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                                           text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-primary-900
                                                           focus:ring-2 focus:ring-primary-900/10 transition-all">
                    </div>

                    <input type="date" name="from" value="{{ request('from') }}" title="Laga Bilaabo"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600 focus:outline-none focus:border-primary-900">
                    <input type="date" name="to" value="{{ request('to') }}" title="Ilaa"
                        class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600 focus:outline-none focus:border-primary-900">

                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2 bg-primary-400">
                        <i class="bi bi-search"></i> Raadi
                    </button>

                    @if(request()->anyFilled(['search', 'from', 'to']))
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
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Kaydka OB</span>
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
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Diiwaanka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Xirida</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($obs as $i => $ob)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-xs font-bold text-neutral-400">
                                    {{ sprintf('%02d', $obs->firstItem() + $i) }}
                                </td>
                                <td class="px-6 py-4 font-mono text-sm font-bold text-neutral-700">{{ $ob->ob_number }}</td>
                                <td class="px-6 py-4 text-neutral-600">{{ $ob->criminalCase->case_number ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-neutral-800 text-sm">{{ $ob->offence_nature }}</p>
                                </td>
                                <td class="px-6 py-4 text-neutral-600 text-sm">{{ $ob->ob_datetime->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-neutral-600 text-sm">{{ $ob->criminalCase->updated_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('criminal-cases.workflow', $ob->criminal_case_id) }}" title="Fiiri Diiwaanka"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-primary-900 hover:border-primary-900 hover:text-white">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center bg-primary-900/8">
                                            <i class="bi bi-archive text-2xl text-primary-900"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">Wali ma jiraan buugag dhacdooyin ah oo kaydsan.</p>
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
