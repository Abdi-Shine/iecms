@extends('admin.admin_master')
@section('page_title', 'Execution Case Information')
@section('admin_main_content')

    <div class="p-4 sm:p-6 w-full"
        x-data="{
                                                                                                                                                                                                                                    deleteAssignment(id) {
                                                                                                                                                                                                                                        Swal.fire({
                                                                                                                                                                                                                                            title: 'Are you sure?',
                                                                                                                                                                                                                                            text: 'You won\'t be able to revert this!',
                                                                                                                                                                                                                                            icon: 'warning',
                                                                                                                                                                                                                                            showCancelButton: true,
                                                                                                                                                                                                                                            confirmButtonColor: '#DC2626',
                                                                                                                                                                                                                                            cancelButtonColor: '#528CBE',
                                                                                                                                                                                                                                            confirmButtonText: 'Yes, delete it!'
                                                                                                                                                                                                                                        }).then(async (result) => {
                                                                                                                                                                                                                                            if (result.isConfirmed) {
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    const res = await fetch(`{{ route('execution-case-assign.index') }}/${id}`, {
                                                                                                                                                                                                                                                        method: 'DELETE',
                                                                                                                                                                                                                                                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                                                                                                                                                                                                                                    });
                                                                                                                                                                                                                                                    if (res.ok) {
                                                                                                                                                                                                                                                        Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Assignment deleted.', timer: 1500, showConfirmButton: false })
                                                                                                                                                                                                                                                            .then(() => window.location.reload());
                                                                                                                                                                                                                                                    } else { Swal.fire('Error', 'Failed to delete assignment', 'error'); }
                                                                                                                                                                                                                                                } catch (e) { Swal.fire('Error', e.message, 'error'); }
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                        });
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                }">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-3">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->FileNo }}</h1>
                <p class="text-sm text-neutral-500 mt-0.5">Xogta Guud ee Dacwada Fulinta</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('execution-case-assign.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200
                                                                                                                                                                                                                                                      rounded-xl bg-white hover:bg-neutral-50 transition-all shadow-sm">
                    <i class="bi bi-arrow-left"></i> Dib ugu Noqo
                </a>
            </div>
        </div>

        {{-- ══ ALL SECTIONS WRAPPER ══ --}}
        <div class="rounded-2xl shadow-sm border border-neutral-100 overflow-hidden divide-y divide-neutral-100">

            {{-- ═══ CASE REGISTRATION ═══ --}}
            <div class="bg-white">

                {{-- Section Title --}}
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                    <i class="bi bi-file-earmark-text text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Macluumaadka Dacwada
                        Fulinta</span>
                </div>

                <div class="divide-y divide-neutral-50">

                    {{-- Case Information --}}
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4">

                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Gal
                                    Lamber</span>
                                <span class="inline-block font-mono text-sm font-bold px-2.5 py-0.5 rounded"
                                    style="background:rgba(82,140,190,0.1);color:#3D78AB">{{ $case->FileNo }}</span>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Nooca
                                    Dacwada</span>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                    style="background:rgba(82,140,190,0.1);color:#528CBE">{{ $case->CaseType }}</span>
                            </div>

                            @if($case->SubCase)
                                <div class="flex items-start gap-3">
                                    <span
                                        class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Hoosaadka</span>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                        style="background:rgba(10,40,77,0.08);color:#0A284D">{{ $case->SubCase }}</span>
                                </div>
                            @endif

                            <div class="flex items-start gap-3">
                                <span
                                    class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Maxkamadda</span>
                                <span class="text-sm font-bold"
                                    style="color:#528CBE">{{ $case->court->longName ?? $case->GradeCourt }}</span>
                            </div>

                            <div class="flex items-start gap-3">
                                <span
                                    class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Xaalada</span>
                                @php
                                    $isActive = in_array($case->Status, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                    $isClosed = $case->Status === 'Closed';
                                @endphp
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                    style="background:{{ $isActive ? 'rgba(16,185,129,0.1)' : ($isClosed ? 'rgba(239,68,68,0.1)' : 'rgba(240,180,60,0.12)') }};color:{{ $isActive ? '#059669' : ($isClosed ? '#b91c1c' : '#C07E15') }}">
                                    {{ $case->Status }}
                                </span>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Taariikhda
                                    Dacwada</span>
                                <span
                                    class="text-sm text-neutral-600">{{ \Carbon\Carbon::parse($case->OpenDate)->format('d/m/Y') }}</span>
                            </div>


                            @if($case->Orders_Requested)
                                <div class="flex items-start gap-3 md:col-span-2">
                                    <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Amarrada La
                                        Codsaday</span>
                                    <span class="text-sm text-neutral-600">{{ $case->Orders_Requested }}</span>
                                </div>
                            @endif

                            @if($case->Remarks)
                                <div class="flex items-start gap-3 md:col-span-2">
                                    <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Nuxurka
                                        Dacwada</span>
                                    <span class="text-sm text-neutral-600">{{ $case->Remarks }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Registration Details --}}
                    <div class="px-6 py-4">
                        <p class="text-[0.65rem] font-black text-neutral-400 uppercase tracking-widest mb-4">Xogta Dacwada
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4">
                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Kaaliyaha
                                    Sare</span>
                                <span class="text-sm font-bold text-neutral-800">{{ $case->addedBy }}</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">
                                    Taariikhda</span>
                                <span
                                    class="text-sm text-neutral-600">{{ \Carbon\Carbon::parse($case->addedDate)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">
                                    Taariikhda</span>
                                <span
                                    class="text-sm text-neutral-600">{{ \Carbon\Carbon::parse($case->OpenDate)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">
                                    Saxiixid</span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                    style="background:rgba(16,185,129,0.1);color:#059669">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Yes
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ CASE PARTIES ═══ --}}
            <div class="bg-white">

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-people text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhinacyada Dacwada
                            Fulinta</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->parties->count() }}
                        {{ Str::plural('party', $case->parties->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dhinaca
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                    Buuxa
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Deegaanka
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Aqoonsiga
                                    /
                                    Baasaboor</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                    Dukumentiyada
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($case->parties as $index => $party)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-neutral-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                            style="background:{{ $party->party_role === 'Dacwoode' ? 'rgba(82,140,190,0.1)' : 'rgba(239,68,68,0.1)' }};color:{{ $party->party_role === 'Dacwoode' ? '#528CBE' : '#b91c1c' }}">
                                            {{ $party->party_role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-neutral-800">{{ $party->full_name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-neutral-700">{{ $party->contact_number ?: '—' }}</span>
                                            <span class="text-xs text-neutral-400">{{ $party->email ?: '' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ $party->district ?: '—' }}</td>
                                    <td class="px-6 py-4 font-mono text-sm text-neutral-600">
                                        {{ $party->national_id ?: ($party->passport_number ?: '—') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            @if($party->passport_doc)
                                                <a href="{{ Storage::url($party->passport_doc) }}"
                                                    class="text-xs font-semibold flex items-center gap-1" style="color:#059669">
                                                    <i class="bi bi-file-check"></i> Passport
                                                </a>
                                            @endif
                                            @if($party->power_of_attorney)
                                                <a href="{{ Storage::url($party->power_of_attorney) }}"
                                                    class="text-xs font-semibold flex items-center gap-1" style="color:#528CBE">
                                                    <i class="bi bi-file-check"></i> POA
                                                </a>
                                            @endif
                                            @if(!$party->passport_doc && !$party->power_of_attorney)
                                                <span class="text-xs text-neutral-400 italic">Not uploaded</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-people text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">No parties registered for this case.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        Showing <span class="font-bold text-neutral-600">{{ $case->parties->count() }}</span>
                        {{ Str::plural('party', $case->parties->count()) }}
                    </p>
                </div>
            </div>

            {{-- ═══ PAYMENT INFORMATION ═══ --}}
            <div class="bg-white">

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-cash-coin text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Codsiyada Lacag
                            Bixinta</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->payments->count() }}
                        {{ Str::plural('diiwaan', $case->payments->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Codsadaha
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Adeegga
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Cadadka
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                    Xaalada</th>
                                <th
                                    class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                    Ficilada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($case->payments as $index => $payment)
                                @php
                                    $pStatusColors = [
                                        'Approved' => ['bg' => 'rgba(16,185,129,0.1)', 'text' => '#059669'],
                                        'Pending' => ['bg' => 'rgba(240,180,60,0.12)', 'text' => '#C07E15'],
                                        'Awaiting Approval' => ['bg' => 'rgba(124,58,237,0.1)', 'text' => '#7C3AED'],
                                        'Failed' => ['bg' => 'rgba(239,68,68,0.1)', 'text' => '#b91c1c'],
                                    ];
                                    $psc = $pStatusColors[$payment->status] ?? ['bg' => 'rgba(107,114,128,0.1)', 'text' => '#6b7280'];
                                    $pStatusLabels = [
                                        'Approved' => 'La Ansaxiyay',
                                        'Pending' => 'Sugaya',
                                        'Awaiting Approval' => 'Sugaya Ansaxin',
                                        'Failed' => 'Fashilmay',
                                    ];
                                @endphp
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-neutral-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-neutral-800">{{ $payment->payer_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ $payment->payer_phone ?: '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $payment->tariff->name_so ?? 'Tarifad Guud' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-neutral-700 text-sm">
                                        ${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                            style="background:{{ $psc['bg'] }};color:{{ $psc['text'] }}">{{ $pStatusLabels[$payment->status] ?? $payment->status }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a title="Rasiidka Lacag Bixinta"
                                            href="{{ route('execution-registration.payments.receipt', $payment->id) }}"
                                            target="_blank"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all hover:bg-[#528CBE] hover:border-[#528CBE] hover:text-white">
                                            <i class="bi bi-receipt text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-cash-coin text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">Weli lama codsan lacag bixin
                                                dacwaddan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        Muujinaya <span
                            class="font-bold text-neutral-600">{{ $case->payments->count() }}</span>
                        {{ $case->payments->count() === 1 ? 'codsi lacag bixin' : 'codsiyo lacag bixin' }}
                    </p>
                </div>
            </div>

            {{-- ═══ WAKIILADA SHARCIGA ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person-badge text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Wakiilada Sharciga</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->legalRepresentatives->count() }}
                        wakiil</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dhinacaha
                                    (Party)</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Doorka
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                    Wakiilka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Lambarka
                                    Dukumentiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                    Dukumentiga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($case->legalRepresentatives as $rep)
                                @php
                                    $isPlaintiff = $rep->party_role === 'Dacwoode';
                                    $linkedParty = $rep->party
                                        ?? $case->parties->firstWhere('PID', $rep->party_id)
                                        ?? $case->parties->firstWhere('party_role', $rep->party_role);
                                    $partyName = $linkedParty?->full_name ?? '—';
                                    $motherName = $linkedParty?->mother_name ?? null;
                                @endphp
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-neutral-400">
                                        {{ sprintf('%02d', $loop->iteration) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-neutral-800">{{ $partyName }}</div>
                                        @if($motherName)
                                            <div class="text-xs text-neutral-400 mt-0.5">M/N: {{ $motherName }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                            style="background:{{ $isPlaintiff ? 'rgba(82,140,190,0.1)' : 'rgba(239,68,68,0.1)' }};color:{{ $isPlaintiff ? '#528CBE' : '#b91c1c' }}">
                                            {{ $rep->party_role ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                                                style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-person text-xs" style="color:#528CBE"></i>
                                            </div>
                                            <span class="font-semibold text-neutral-800">{{ $rep->rep_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($rep->rep_doc_number)
                                            <div class="flex items-center gap-1.5">
                                                <i class="bi bi-file-earmark text-neutral-400 text-xs"></i>
                                                <span class="font-mono text-sm text-neutral-700">{{ $rep->rep_doc_number }}</span>
                                            </div>
                                        @else
                                            <span class="text-neutral-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($rep->rep_doc)
                                            <a href="{{ Storage::url($rep->rep_doc) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all"
                                                style="background:rgba(82,140,190,0.07);border-color:rgba(82,140,190,0.25);color:#528CBE"
                                                onmouseover="this.style.background='#528CBE';this.style.color='white'"
                                                onmouseout="this.style.background='rgba(82,140,190,0.07)';this.style.color='#528CBE'">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        @else
                                            <span class="text-xs text-neutral-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-person-badge text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">Wakiil sharciyeed lama xidhin
                                                dacwaddan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        Showing <span class="font-bold text-neutral-600">{{ $case->legalRepresentatives->count() }}</span>
                        {{ $case->legalRepresentatives->count() === 1 ? 'legal representative' : 'legal representatives' }}
                    </p>
                </div>
            </div>

            {{-- ═══ SUPPORTING DOCUMENTS ═══ --}}
            <div class="bg-white">

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-folder2-open text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500"> Warqadaha Dacwada
                            Fulinta</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->documents->count() }}
                        {{ Str::plural('document', $case->documents->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                    Dukumentiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faahfaahin
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">File-ka
                                    Dukumintiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($case->documents as $index => $doc)
                                <tr class="hover:bg-neutral-50 transition-colors" id="doc-row-{{ $doc->id }}">
                                    <td class="px-6 py-4 text-sm font-semibold text-neutral-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $doc->document_name }}</td>
                                    <td class="px-6 py-4 text-neutral-500">{{ $doc->description ?: '—' }}</td>
                                    <td class="px-6 py-4">
                                        @if($doc->file_path)
                                            <div class="flex items-center gap-2">
                                                <i class="bi bi-paperclip text-neutral-400"></i>
                                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                                    class="text-xs font-semibold hover:underline" style="color:#528CBE">Document
                                                    Attached</a>
                                            </div>
                                        @else
                                            <span class="text-xs text-neutral-400 italic">No file</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ \Carbon\Carbon::parse($doc->document_date)->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(240,180,60,0.1)">
                                                <i class="bi bi-folder-x text-2xl" style="color:#F0B43C"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">No supporting documents attached
                                                yet.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        Showing <span class="font-bold text-neutral-600">{{ $case->documents->count() }}</span>
                        {{ Str::plural('document', $case->documents->count()) }}
                    </p>
                </div>
            </div>

            {{-- ═══ LAWYER ASSIGNMENT HISTORY ═══ --}}
            <div class="bg-white">

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person-badge text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Qareenada
                            Dacwada Fulinta </span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->lawyers->count() }}
                        {{ Str::plural('record', $case->lawyers->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                    Dhinacayada
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                    Qareenka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Imeyl</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($case->lawyers as $index => $assigned)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-neutral-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap"
                                                style="background:{{ ($assigned->party_role ?? '') === 'Dacwoode' ? 'rgba(82,140,190,0.1)' : 'rgba(239,68,68,0.1)' }};color:{{ ($assigned->party_role ?? '') === 'Dacwoode' ? '#528CBE' : '#b91c1c' }}">
                                                {{ $assigned->party_role ?? '—' }}
                                            </span>
                                            <span
                                                class="font-semibold text-neutral-800">{{ $assigned->party->full_name ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">
                                        {{ $assigned->lawyer->LawyerName ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-neutral-700">
                                        {{ $assigned->lawyer->Phone ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $assigned->lawyer->Email ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ \Carbon\Carbon::parse($assigned->assignment_date)->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-person-badge text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">No lawyer assignment history found.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        Showing <span class="font-bold text-neutral-600">{{ $case->lawyers->count() }}</span>
                        {{ Str::plural('record', $case->lawyers->count()) }}
                    </p>
                </div>
            </div>

            {{-- ═══ JUDICIAL PANEL ASSIGNMENTS ═══ --}}
            <div class="bg-white">

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person-workspace text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500"> Gal ku Qorista Dacwada
                            Fulinta</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">No#
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                    Gorsoore/Kaaliye</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Imeyl</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                    Xilka Guddiga</th>
                                <th
                                    class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                    Xaalada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($case->assignments as $assignment)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-semibold text-neutral-800">{{ $assignment->employee->EmpName ?? 'N/A' }}</span>
                                            <span
                                                class="text-xs text-neutral-400 uppercase font-semibold">{{ $assignment->employee->Position ?? 'Staff' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ $assignment->employee->email ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ $assignment->employee->phone ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                            style="background:{{ $assignment->panel_role === 'Chair' ? 'rgba(82,140,190,0.1)' : 'rgba(240,180,60,0.12)' }};color:{{ $assignment->panel_role === 'Chair' ? '#528CBE' : '#C07E15' }}">
                                            {{ $assignment->panel_role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $caseStatus = $case->Status;
                                            $isActive = in_array($caseStatus, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                            $isClosed = $caseStatus === 'Closed';
                                            $csBg = $isActive ? 'rgba(16,185,129,0.1)' : ($isClosed ? 'rgba(239,68,68,0.1)' : 'rgba(240,180,60,0.12)');
                                            $csColor = $isActive ? '#059669' : ($isClosed ? '#b91c1c' : '#C07E15');
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                            style="background:{{ $csBg }};color:{{ $csColor }}">
                                            {{ $caseStatus }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-person-workspace text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">No judicial panel members assigned
                                                yet.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        Showing <span class="font-bold text-neutral-600">{{ $case->assignments->count() }}</span>
                        {{ Str::plural('assignment', $case->assignments->count()) }}
                    </p>
                </div>
            </div>
            {{-- ═══ HANDOVER INFORMATION ═══ --}}
            <div class="bg-white">

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-file-earmark-arrow-up text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhaqdhaqaaqa
                            Dacwadda Fulinta</span>
                    </div>
                </div>

                @if($handover)
                    @php
                        $hvStatus = $handover->status ?? 'Draft';
                        $hvBg = $hvStatus === 'Qaatay' ? 'rgba(16,185,129,0.1)'
                            : ($hvStatus === 'Sug Qaatay' ? 'rgba(240,180,60,0.12)'
                                : 'rgba(82,140,190,0.08)');
                        $hvColor = $hvStatus === 'Qaatay' ? '#059669'
                            : ($hvStatus === 'Sug Qaatay' ? '#C07E15'
                                : '#528CBE');
                        $hvDot = $hvStatus === 'Qaatay' ? '#10B981'
                            : ($hvStatus === 'Sug Qaatay' ? '#F0B43C'
                                : '#528CBE');
                        $hvDocs = !empty($handover->documents) ? count($handover->documents) : 0;
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr style="background:rgba(82,140,190,0.06)">
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">T.T</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                        Abuuraha</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tirada
                                        Dukumintiyada</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                                    </th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                        Dukuumintiga</th>
                                    <th
                                        class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                        Xaalada</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-xs font-bold text-neutral-400">01</td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $handover->created_by ?? '—' }}</td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $hvDocs }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ \Carbon\Carbon::parse($handover->updated_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('execution-case-handover.document-pdf', $case->ECID) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all"
                                            style="background:rgba(10,40,77,.07);border-color:rgba(10,40,77,.18);color:#0A284D"
                                            onmouseover="this.style.background='#0A284D';this.style.color='white'"
                                            onmouseout="this.style.background='rgba(10,40,77,.07)';this.style.color='#0A284D'">
                                            <i class="bi bi-file-earmark-text"></i> Dukuumintiga
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider whitespace-nowrap"
                                            style="background:{{ $hvBg }};color:{{ $hvColor }}">
                                            {{ $hvStatus }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-3 py-16 text-center">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center"
                            style="background:rgba(240,180,60,0.1)">
                            <i class="bi bi-file-earmark-x text-2xl" style="color:#F0B43C"></i>
                        </div>
                        <p class="text-neutral-400 font-medium text-sm">Wareejin lama abuuro weli dacwaddan.</p>
                    </div>
                @endif

                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        @if($handover)
                            La abuuray: <span
                                class="font-bold text-neutral-600">{{ \Carbon\Carbon::parse($handover->created_at)->format('d/m/Y') }}</span>
                        @else
                            Wareejin wali lama abuuro
                        @endif
                    </p>
                </div>
            </div>
            {{-- ═══ Close Case INFORMATION ═══ --}}
            @php $cc = $case->closeCase; @endphp
            <div class="bg-white">

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                    <i class="bi bi-x-circle text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Xidhitaanka Dacwadda</span>
                </div>

                @if($cc)
                    @php
                        $ccStatus = $case->Status ?? '—';
                        $ccBg = in_array($ccStatus, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Mudeyn'])
                            ? 'rgba(16,185,129,0.12)'
                            : ($ccStatus === 'Closed'
                                ? 'rgba(239,68,68,0.1)'
                                : 'rgba(240,180,60,0.12)');
                        $ccColor = in_array($ccStatus, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Mudeyn'])
                            ? '#059669'
                            : ($ccStatus === 'Closed' ? '#DC2626' : '#C07E15');
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr style="background:rgba(82,140,190,0.06)">
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">T.T</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">La Abuuray
                                    </th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca
                                        Oodista</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tariikhda
                                        Go'aanka</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                        Dukuumintiga</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xaalada
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-xs font-bold text-neutral-400">1.</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $cc->created_by ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">
                                        {{ $cc->judgment_type ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $cc->judgment_date ? \Carbon\Carbon::parse($cc->judgment_date)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('execution-close-case.document.readonly', $case->ECID) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all"
                                            style="background:rgba(10,40,77,.07);border-color:rgba(10,40,77,.18);color:#0A284D"
                                            onmouseover="this.style.background='#0A284D';this.style.color='white'"
                                            onmouseout="this.style.background='rgba(10,40,77,.07)';this.style.color='#0A284D'">
                                            <i class="bi bi-file-earmark-text"></i> Dukuumintiga
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider whitespace-nowrap"
                                            style="background:{{ $ccBg }};color:{{ $ccColor }}">
                                            {{ $ccStatus }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-3 py-16 text-center">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center"
                            style="background:rgba(240,180,60,0.1)">
                            <i class="bi bi-file-earmark-x text-2xl" style="color:#F0B43C"></i>
                        </div>
                        <p class="text-neutral-400 font-medium text-sm">Xidhitaan lama abuuro weli dacwaddan.</p>
                    </div>
                @endif

                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        @if($cc)
                            Taariikhda la abuuray: <span
                                class="font-bold text-neutral-600">{{ \Carbon\Carbon::parse($cc->created_at)->format('d/m/Y') }}</span>
                        @else
                            Xidhitaan wali lama abuuro
                        @endif
                    </p>
                </div>
            </div>
            {{-- ═══ HEARING INFORMATION ═══ --}}
            <div class="bg-white">

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-calendar3-week text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Mudeynta Dacwada
                            Fulinta</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->hearings->count() }}
                        {{ Str::plural('hearing', $case->hearings->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                    Kaaliyaha
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tariikhda
                                    Dacwadda</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Saacadda
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca
                                    Dhageysiga
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                    Dukumintiga</th>
                                <th
                                    class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                    Xaalada</th>

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($case->hearings->sortBy('hearing_date') as $hearing)
                                @php
                                    $cs = $case->Status;
                                    $hBg = in_array($cs, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Mudeyn']) ? 'rgba(16,185,129,0.12)' : ($cs === 'Closed' ? 'rgba(239,68,68,0.1)' : 'rgba(240,180,60,0.12)');
                                    $hColor = in_array($cs, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Mudeyn']) ? '#059669' : ($cs === 'Closed' ? '#DC2626' : '#C07E15');
                                    $hDot = in_array($cs, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Mudeyn']) ? '#10B981' : ($cs === 'Closed' ? '#ef4444' : '#F0B43C');
                                @endphp
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">
                                        {{ $hearing->created_by ?: '—' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">
                                        {{ $hearing->hearing_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ substr($hearing->hearing_time, 0, 5) }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-500">
                                        Dhageysiga {{ $loop->iteration }}aad
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('execution-hearings.document-pdf', $hearing->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all"
                                            style="background:rgba(10,40,77,.07);border-color:rgba(10,40,77,.18);color:#0A284D"
                                            onmouseover="this.style.background='#0A284D';this.style.color='white'"
                                            onmouseout="this.style.background='rgba(10,40,77,.07)';this.style.color='#0A284D'">
                                            <i class="bi bi-file-earmark-text"></i> Dukuumintiga
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                            style="background:{{ $hBg }};color:{{ $hColor }}">
                                            {{ $case->Status }}
                                        </span>
                                    </td>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-calendar3-week text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">Wali ma jiraan mudeynno la
                                                qorsheeyay.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        Muujinaya <span class="font-bold text-neutral-600">{{ $case->hearings->count() }}</span>
                        {{ Str::plural('mudeyn', $case->hearings->count()) }}
                    </p>
                </div>
            </div>

            {{-- ═══ HEARING SCRIPTURE RECORDS ═══ --}}
            <div class="bg-white">

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-journal-text text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhageysi Dacwada
                            Fulinta</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $scriptures->count() }}
                        {{ Str::plural('record', $scriptures->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                    Kaaliyaha</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                                    Dhageysiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Saacada
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca
                                    Dhageysiga</th>
                                <th
                                    class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                    Dukuumintiga</th>
                                <th
                                    class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                    Xaalada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($scriptures as $scripture)
                                @php
                                    $sBg = match ($scripture->status) { 'Submitted' => 'rgba(82,140,190,0.1)', 'Confirmed' => 'rgba(16,185,129,0.1)', default => 'rgba(240,180,60,0.12)'};
                                    $sColor = match ($scripture->status) { 'Submitted' => '#528CBE', 'Confirmed' => '#059669', default => '#C07E15'};
                                    $sDot = match ($scripture->status) { 'Submitted' => '#528CBE', 'Confirmed' => '#10B981', default => '#F0B43C'};
                                @endphp
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-700 text-sm font-medium">
                                        {{ $scripture->created_by ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $scripture->hearing_date ? \Carbon\Carbon::parse($scripture->hearing_date)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $scripture->hearing_time ? substr($scripture->hearing_time, 0, 5) : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-500 text-sm">{{ $scripture->hearing_type ?: '—' }}</td>

                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('execution-hearings.scripture.document', $scripture->id) }}"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-file-earmark-richtext text-xs"></i>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                            style="background:{{ $hBg }};color:{{ $hColor }}">
                                            {{ $case->Status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-journal-text text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">No hearing scripture records found.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        Showing <span class="font-bold text-neutral-600">{{ $scriptures->count() }}</span>
                        {{ Str::plural('scripture record', $scriptures->count()) }}
                    </p>
                </div>
            </div>
            {{-- ═══ Judgement Records ═══ --}}
            <div class="bg-white">

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-gavel text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Xukunka Dacwada
                            Fulinta</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $judgments->count() }}
                        {{ Str::plural('record', $judgments->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                    Kaaliyaha</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                                    Xukunka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Saacada
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca
                                    Xukunka
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                    Dukuumintiga
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                    Xaalada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($judgments as $judgment)
                                @php
                                    $cs = $case->Status;
                                    $jBg = $cs === 'Gal Celin' ? 'rgba(240,180,60,0.12)' : (in_array($cs, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Mudeyn']) ? 'rgba(16,185,129,0.12)' : ($cs === 'Closed' ? 'rgba(239,68,68,0.1)' : 'rgba(82,140,190,0.1)'));
                                    $jColor = $cs === 'Gal Celin' ? '#C07E15' : (in_array($cs, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Mudeyn']) ? '#059669' : ($cs === 'Closed' ? '#DC2626' : '#528CBE'));
                                    $jDot = $cs === 'Gal Celin' ? '#F0B43C' : (in_array($cs, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Mudeyn']) ? '#10B981' : ($cs === 'Closed' ? '#DC2626' : '#528CBE'));
                                @endphp
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-700 text-sm font-medium">
                                        {{ $judgment->created_by ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $judgment->judgment_date ? \Carbon\Carbon::parse($judgment->judgment_date)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $judgment->judgment_time ? \Carbon\Carbon::parse($judgment->judgment_time)->format('H:i') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-500 text-sm">{{ $judgment->judgment_type ?: '—' }}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('execution-judgments.document.readonly', $judgment->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-all"
                                            style="background:rgba(82,140,190,0.1);color:#3D78AB;border:1px solid rgba(82,140,190,0.2);"
                                            onmouseover="this.style.background='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='rgba(82,140,190,0.1)';this.style.color='#3D78AB'">
                                            <i class="bi bi-file-earmark-text"></i>
                                            Dukuumintiga
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                            style="background:{{ $jBg }};color:{{ $jColor }}">
                                            {{ $cs }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-gavel text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">Xukun la diiwaangelinin.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        Showing <span class="font-bold text-neutral-600">{{ $judgments->count() }}</span>
                        {{ Str::plural('judgment record', $judgments->count()) }}
                    </p>
                </div>
            </div>

            <div class="bg-white">

                {{-- ── Receipt Records ── --}}
                @php
                    $allReceipts = $judgments->flatMap(fn($j) => $j->receipts)->values();
                @endphp
                @if($allReceipts->count() > 0)
                    <div class="border-t-2 border-dashed border-neutral-100">
                        <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-person-check text-sm" style="color:#15803d"></i>
                                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Xaqiijinta Dhinacyada
                                    Qaadashada
                                    Xukunka</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                style="background:#dcfce7;color:#15803d;">
                                <i class="bi bi-check-circle-fill"></i>
                                {{ $allReceipts->count() }} qaatay
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr style="background:rgba(34,197,94,0.06)">
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">
                                            T.T</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                            Dhinaca</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Xukunka Dhacay</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Taariikhda Qaadashada</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Saxiixa</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faallo
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    @foreach($allReceipts as $receipt)
                                        @php
                                            $isWin = $receipt->judgment_outcome === 'Loo Xukume';
                                            $outBg = $isWin ? 'rgba(34,197,94,.12)' : 'rgba(239,68,68,.1)';
                                            $outColor = $isWin ? '#15803d' : '#b91c1c';
                                            $outIco = $isWin ? 'bi-trophy-fill' : 'bi-x-circle-fill';
                                            $isPlain = strtolower($receipt->party_role ?? '') === 'plaintiff' || $receipt->party_role === 'Dacwoode';
                                            $roleColor = $isPlain ? '#0A284D' : '#b91c1c';
                                            $roleBg = $isPlain ? 'rgba(10,40,77,.08)' : 'rgba(220,38,38,.08)';
                                            $roleLbl = $isPlain ? 'Dacwoode' : 'Dacweysane';
                                            $sigExt = $receipt->signature ? strtolower(pathinfo($receipt->signature, PATHINFO_EXTENSION)) : null;
                                            $isImg = $sigExt && in_array($sigExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                        @endphp
                                        <tr class="hover:bg-neutral-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <span
                                                    class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-neutral-800 text-sm">
                                                    {{ $receipt->party_name ?? '—' }}
                                                </div>
                                                <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-full mt-0.5"
                                                    style="background:{{ $roleBg }};color:{{ $roleColor }};">{{ $roleLbl }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($receipt->judgment_outcome)
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                                        style="background:{{ $outBg }};color:{{ $outColor }};">
                                                        <i class="bi {{ $outIco }}"></i>
                                                        {{ $receipt->judgment_outcome }}
                                                    </span>
                                                @else
                                                    <span class="text-neutral-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-neutral-600 text-sm">
                                                <i class="bi bi-calendar-check text-neutral-300 mr-1"></i>
                                                {{ $receipt->received_date ? \Carbon\Carbon::parse($receipt->received_date)->format('d/m/Y') : ($receipt->received_at ? $receipt->received_at->format('d/m/Y') : '—') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($receipt->signature && $isImg)
                                                    <a href="{{ asset('storage/' . $receipt->signature) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $receipt->signature) }}" alt="Saxiixa"
                                                            style="width:56px;height:36px;object-fit:contain;border:1px solid #e5e7eb;border-radius:.5rem;background:#f9fafb;padding:2px;">
                                                    </a>
                                                @elseif($receipt->signature)
                                                    <a href="{{ asset('storage/' . $receipt->signature) }}" target="_blank"
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold"
                                                        style="background:rgba(82,140,190,.1);color:#3D78AB;border:1px solid rgba(82,140,190,.2);">
                                                        <i class="bi bi-file-earmark-arrow-down"></i> Fur
                                                    </a>
                                                @else
                                                    <span class="text-neutral-300 text-sm">—</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-neutral-500 text-sm">{{ $receipt->notes ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                {{-- ═══ Return File INFORMATION ═══ --}}
                <div class="bg-white">
                    <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                        <i class="bi bi-arrow-return-left text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Soo Celinta Faylka</span>
                    </div>
                    @if($returnFile)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr style="background:rgba(82,140,190,0.06)">
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">T.T
                                        </th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                            Kaaliyaha</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tirada
                                            Warqadaha</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Dukuumintiga
                                        </th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Tariikhda
                                        </th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Xaalada
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    @php
                                        $rfClerk = $case->assignments->first(fn($a) => in_array($a->panel_role, ['Kaaliye', 'Clerk']))
                                            ?? $case->assignments->first();
                                        $rfDocCount = !empty($returnFile->documents) ? count($returnFile->documents) : 0;
                                        $cs = $case->Status;
                                        $csBg = in_array($cs, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Mudeyn']) ? 'rgba(16,185,129,0.12)' : ($cs === 'Closed' ? 'rgba(239,68,68,0.1)' : 'rgba(240,180,60,0.12)');
                                        $csColor = in_array($cs, ['Active', 'Gal Ku Qoris', 'Qaatay', 'Mudeyn']) ? '#059669' : ($cs === 'Closed' ? '#DC2626' : '#C07E15');
                                    @endphp
                                    <tr class="hover:bg-neutral-50 transition-colors">
                                        <td class="px-6 py-4 text-xs font-bold text-neutral-400">1.</td>
                                        <td class="px-6 py-4">
                                            @if($rfClerk && $rfClerk->employee)
                                                <div class="flex flex-col">
                                                    <span
                                                        class="font-semibold text-neutral-800">{{ $rfClerk->employee->EmpName }}</span>
                                                    <span
                                                        class="text-xs font-semibold text-neutral-400 uppercase tracking-tight">{{ $rfClerk->employee->Position ?? $rfClerk->panel_role }}</span>
                                                </div>
                                            @else
                                                <span
                                                    class="font-semibold text-neutral-800">{{ $returnFile->created_by ?? '—' }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-neutral-800">
                                            {{ $rfDocCount }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('execution-return-file.document.readonly', $case->ECID) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all"
                                                style="background:rgba(10,40,77,.07);border-color:rgba(10,40,77,.18);color:#0A284D"
                                                onmouseover="this.style.background='#0A284D';this.style.color='white'"
                                                onmouseout="this.style.background='rgba(10,40,77,.07)';this.style.color='#0A284D'">
                                                <i class="bi bi-file-earmark-text"></i> Dukuumintiga
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-neutral-600 text-sm">
                                            {{ \Carbon\Carbon::parse($returnFile->updated_at)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider whitespace-nowrap"
                                                style="background:{{ $csBg }};color:{{ $csColor }}">
                                                {{ $cs }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center gap-3 py-16 text-center">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                style="background:rgba(240,180,60,0.1)">
                                <i class="bi bi-file-earmark-x text-2xl" style="color:#F0B43C"></i>
                            </div>
                            <p class="text-neutral-400 font-medium text-sm">Soo celin fayl lama abuuro weli dacwaddan.</p>
                        </div>
                    @endif

                    <div class="px-6 py-4 border-t border-neutral-100">
                        <p class="text-xs text-neutral-400 font-medium">
                            @if($returnFile)
                                Warqadaha soo celinta: <span
                                    class="font-bold text-neutral-600">{{ !empty($returnFile->documents) ? count($returnFile->documents) : 0 }}</span>
                            @else
                                Soo celin wali lama abuuro
                            @endif
                        </p>
                    </div>
                </div>
                {{-- ═══ DHAQAN GAL INFORMATION ═══ --}}
                <div class="bg-white">

                    <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                        <i class="bi bi-hammer text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhaqan Galka Dacwada
                            Fulinta</span>
                    </div>

                    @if($enforcement)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr style="background:rgba(82,140,190,0.06)">
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">T.T
                                        </th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                            Kaaliyaha</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faallo
                                        </th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Taariikhda Dhaqan Galka</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Dukuumintiga</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Xaalada</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    <tr class="hover:bg-neutral-50 transition-colors">
                                        <td class="px-6 py-4 text-xs font-bold text-neutral-400">01</td>
                                        <td class="px-6 py-4 font-semibold text-neutral-800">
                                            {{ $enforcement->created_by ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-neutral-800">
                                            {{ $enforcement->additional_notes ? \Illuminate\Support\Str::limit(strip_tags($enforcement->additional_notes), 40) : '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-neutral-500">
                                            {{ $enforcement->enforcement_date ? \Carbon\Carbon::parse($enforcement->enforcement_date)->format('d/m/Y') : '—' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($enforcement->attachment)
                                                <a href="{{ asset('storage/' . $enforcement->attachment) }}" target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border"
                                                    style="background:rgba(82,140,190,.08);border-color:rgba(82,140,190,.25);color:#528CBE">
                                                    <i class="bi bi-file-earmark-arrow-down"></i> Dukuumintiga
                                                </a>
                                            @else
                                                <span class="text-neutral-300 text-sm">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $es = $case->Status;
                                                $esBg = $es === 'Dhaqan Gal' ? 'rgba(82,140,190,.1)' : ($es === 'Closed' ? 'rgba(239,68,68,.1)' : 'rgba(240,180,60,.1)');
                                                $esColor = $es === 'Dhaqan Gal' ? '#528CBE' : ($es === 'Closed' ? '#DC2626' : '#C07E15');
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold whitespace-nowrap"
                                                style="background:{{ $esBg }};color:{{ $esColor }}">
                                                {{ $es }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center gap-3 py-16 text-center">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                style="background:rgba(82,140,190,0.08)">
                                <i class="bi bi-hammer text-2xl" style="color:#528CBE"></i>
                            </div>
                            <p class="text-neutral-400 font-medium text-sm">Dhaqan Gal wali lama abuurin.</p>
                        </div>
                    @endif

                    <div class="px-6 py-4 border-t border-neutral-100">
                        <p class="text-xs text-neutral-400 font-medium">
                            {{ $enforcement ? 'La abuuray: ' . ($enforcement->created_by ?? '—') : 'Dhaqan Gal ma jiro' }}
                        </p>
                    </div>
                </div>
                {{-- ═══ RAFCAAN INFORMATION ═══ --}}
                <div class="bg-white">

                    <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                        <i class="bi bi-arrow-up-circle text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Rafcaan Dacwada
                            Fulinta</span>
                    </div>

                    @if($appeal)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr style="background:rgba(82,140,190,0.06)">
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">T.T
                                        </th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                            Abuuraha</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faallo
                                        </th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Taariikhda Rafcaanka</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Dukuumintiga</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">
                                            Xaalada</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    <tr class="hover:bg-neutral-50 transition-colors">
                                        <td class="px-6 py-4 text-xs font-bold text-neutral-400">01</td>
                                        <td class="px-6 py-4 font-semibold text-neutral-800">
                                            {{ $appeal->created_by ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-neutral-800">
                                            {{ $appeal->additional_notes ? \Illuminate\Support\Str::limit(strip_tags($appeal->additional_notes), 40) : '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-neutral-500">
                                            {{ $appeal->appeal_date ? \Carbon\Carbon::parse($appeal->appeal_date)->format('d/m/Y') : '—' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($appeal->attachment)
                                                <a href="{{ asset('storage/' . $appeal->attachment) }}" target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border"
                                                    style="background:rgba(82,140,190,.08);border-color:rgba(82,140,190,.25);color:#528CBE">
                                                    <i class="bi bi-file-earmark-arrow-down"></i> Dukuumintiga
                                                </a>
                                            @else
                                                <span class="text-neutral-300 text-sm">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $as = $case->Status;
                                                $asBg = $as === 'Rafcaan' ? 'rgba(240,180,60,.1)'
                                                    : ($as === 'Closed' ? 'rgba(239,68,68,.1)'
                                                        : 'rgba(82,140,190,.1)');
                                                $asColor = $as === 'Rafcaan' ? '#C07E15'
                                                    : ($as === 'Closed' ? '#DC2626'
                                                        : '#528CBE');
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold whitespace-nowrap"
                                                style="background:{{ $asBg }};color:{{ $asColor }}">
                                                {{ $as }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center gap-3 py-16 text-center">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                style="background:rgba(82,140,190,0.08)">
                                <i class="bi bi-arrow-up-circle text-2xl" style="color:#528CBE"></i>
                            </div>
                            <p class="text-neutral-400 font-medium text-sm">Rafcaan wali lama abuurin.</p>
                        </div>
                    @endif

                    <div class="px-6 py-4 border-t border-neutral-100">
                        <p class="text-xs text-neutral-400 font-medium">
                            {{ $appeal ? 'La abuuray: ' . ($appeal->created_by ?? '—') : 'Rafcaan ma jiro' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>{{-- /ALL SECTIONS WRAPPER --}}
    </div>

    @push('scripts')
        <script>
            function deleteDocument(id) {
                if (!confirm('Ma hubtaa inaad tirtirto dukumintigan?')) return;
                fetch(`/execution-case-documents/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success || data.message) {
                            const row = document.getElementById('doc-row-' + id);
                            if (row) row.remove();
                        } else {
                            alert('Khalad ayaa dhacay. Dib u isku day.');
                        }
                    })
                    .catch(() => alert('Khalad ayaa dhacay. Dib u isku day.'));
            }
        </script>
    @endpush

@endsection