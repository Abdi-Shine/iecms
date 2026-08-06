@extends('admin.admin_master')
@section('page_title', 'Lacag Bixinnada')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Lacag Bixinnada</h1>
        </div>
        <a href="{{ route('finance.applicant-request') }}"
            class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90"
            style="background:#528CBE">
            <i class="bi bi-plus-lg"></i> Ku Dar Lacag Bixin
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Total Transactions --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-file-earmark-text text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Wadarta Dhaqdhaqaaqyada</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ number_format($stats['total']) }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Dhammaan diiwaannada lacag bixinta</p>
            </div>
        </div>

        {{-- Approved --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background:rgba(16,185,129,0.12)">
                <i class="bi bi-check2-circle text-xl" style="color:#059669"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">La Ansaxiyay</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ number_format($stats['approved']) }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#059669">Lacagaha la xaqiijiyay</p>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-hourglass-split text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Sugaya</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ number_format($stats['pending']) }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Sugaya xaqiijin</p>
            </div>
        </div>

        {{-- Total Collected --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background:rgba(124,58,237,0.12)">
                <i class="bi bi-currency-dollar text-xl" style="color:#7C3AED"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Wadarta La Ururiyay</p>
                <h3 class="text-2xl font-black text-neutral-800 leading-tight">${{ number_format($stats['collected'], 2) }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#7C3AED">Laga soo ururiyay lacagaha la ansaxiyay</p>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        {{-- Search & Filter Bar --}}
        <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
            <form action="{{ route('finance.payments') }}" method="GET" class="flex flex-wrap gap-3 items-center">

                {{-- Page size --}}
                <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                    <span>Tus</span>
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
                        placeholder="Magaca bixiyaha, taleefanka, ama lambarka dacwadda…"
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                               text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-[#528CBE]
                               focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                </div>

                {{-- Status filter --}}
                @php
                    $statusLabels = [
                        'Approved'          => 'La Ansaxiyay',
                        'Pending'           => 'Sugaya',
                        'Awaiting Approval' => 'Sugaya Ansaxin',
                        'Failed'            => 'Fashilmay',
                    ];
                @endphp
                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                           focus:outline-none focus:border-[#528CBE] min-w-[160px] cursor-pointer">
                    <option value="">Dhammaan Xaaladaha</option>
                    @foreach(['Approved', 'Pending', 'Awaiting Approval', 'Failed'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $statusLabels[$st] }}</option>
                    @endforeach
                </select>

                {{-- Search button --}}
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                    style="background:#528CBE">
                    <i class="bi bi-search"></i> Raadi
                </button>

                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('finance.payments') }}"
                        class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                        <i class="bi bi-x-circle"></i> Tirtir
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Title --}}
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-table text-sm" style="color:#528CBE"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhaqdhaqaaqyada Lacag Bixinta</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">{{ $payments->total() }} {{ Str::plural('diiwaan', $payments->total()) }}</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Buuxa</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Qiimaha</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Adeega</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Xaalada</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Ficilada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($payments as $i => $p)
                        @php
                            $statusColors = [
                                'Approved'          => ['bg' => 'rgba(16,185,129,0.1)', 'text' => '#059669'],
                                'Pending'           => ['bg' => 'rgba(240,180,60,0.12)', 'text' => '#C07E15'],
                                'Awaiting Approval' => ['bg' => 'rgba(124,58,237,0.1)', 'text' => '#7C3AED'],
                                'Failed'            => ['bg' => 'rgba(239,68,68,0.1)', 'text' => '#b91c1c'],
                            ];
                            $sc = $statusColors[$p->status] ?? ['bg' => 'rgba(107,114,128,0.1)', 'text' => '#6b7280'];
                            $statusLabels = [
                                'Approved'          => 'La Ansaxiyay',
                                'Pending'           => 'Sugaya',
                                'Awaiting Approval' => 'Sugaya Ansaxin',
                                'Failed'            => 'Fashilmay',
                            ];
                        @endphp
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-neutral-400">{{ str_pad($payments->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-neutral-700">{{ $p->payer_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-neutral-600">{{ $p->payer_phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-neutral-700">${{ number_format($p->amount, 2) }} {{ $p->currency }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                    style="background:rgba(82,140,190,0.1);color:#528CBE">{{ $p->tariff->name_so ?? 'Tarifad Guud' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-neutral-600 text-sm">{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') : 'Ma Jirto' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                    style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }}">{{ $statusLabels[$p->status] ?? $p->status }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a title="Rasiidka Lacag Bixinta" href="{{ route('finance.payments.receipt', $p->id) }}" target="_blank"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-[#528CBE] hover:border-[#528CBE] hover:text-white">
                                        <i class="bi bi-receipt text-xs"></i>
                                    </a>
                                    @if(in_array($p->status, ['Pending', 'Awaiting Approval']))
                                        <button title="Ansixi" type="button" onclick="approvePaymentRequest({{ $p->id }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-emerald-500 hover:border-emerald-500 hover:text-white">
                                            <i class="bi bi-check2-circle text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                        style="background:rgba(82,140,190,0.1)">
                                        <i class="bi bi-cash-stack text-2xl" style="color:#528CBE"></i>
                                    </div>
                                    <p class="text-neutral-400 font-medium text-sm">Ma jiraan dhaqdhaqaaqyo lacag bixin ah oo la helay.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $payments->links() }}
        </div>
    </div>


</div>

<script>
    function approvePaymentRequest(id) {
        Swal.fire({
            title: 'Ma hubtaa?',
            text: 'Codsigan waa la ansixin doonaa, codsadaha wuxuu awoodi doonaa inuu sii wado lacag bixinta.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#528CBE',
            confirmButtonText: 'Haa, ansixi',
            cancelButtonText: 'Jooji',
        }).then(async (result) => {
            if (!result.isConfirmed) return;
            try {
                const res = await fetch(`{{ url('/finance-payments') }}/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    Swal.fire({ title: 'Guul!', text: data.message, icon: 'success', confirmButtonText: 'Hagaag', confirmButtonColor: '#528CBE' })
                        .then(() => window.location.reload());
                } else {
                    throw new Error(data.message || 'Ansixintu waa fashilantay.');
                }
            } catch (e) {
                Swal.fire({ title: 'Khalad!', text: e.message, icon: 'error', confirmButtonText: 'Hagaag', confirmButtonColor: '#DC2626' });
            }
        });
    }
</script>

@endsection
