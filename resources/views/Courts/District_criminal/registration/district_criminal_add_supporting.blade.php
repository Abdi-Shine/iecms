@extends('admin.admin_master')
@section('page_title', 'Criminal Case Information')
@section('admin_main_content')

    <div class="p-4 sm:p-6 w-full" x-data="{
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
                        const res = await fetch(`{{ route('criminal-case-assign.index') }}/${id}`, {
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
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->FileNo }}</h1>
                <p class="text-sm text-neutral-500 mt-0.5">Macluumaadka Dacwada Ciqaabta</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('criminal-case-assign.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200
                          rounded-xl bg-white hover:bg-neutral-50 transition-all shadow-sm">
                    <i class="bi bi-arrow-left"></i> Dib ugu Noqo
                </a>
            </div>
        </div>


        {{-- ═══ CASE REGISTRATION ═══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">

            {{-- Section Title --}}
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                <i class="bi bi-file-earmark-text text-sm" style="color:#528CBE"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Macluumaadka Dacwada Ciqaabta</span>
            </div>

            <div class="divide-y divide-neutral-50">

                {{-- Case Information --}}
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4">

                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Gal Lamber</span>
                            <span class="inline-block font-mono text-sm font-bold px-2.5 py-0.5 rounded"
                            style="background:rgba(82,140,190,0.1);color:#3D78AB">{{ $case->FileNo }}</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Nooca Dacwada</span>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                style="background:rgba(82,140,190,0.1);color:#528CBE">{{ $case->CaseType }}</span>
                        </div>

                        @if($case->SubCase)
                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Hoosaadka</span>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                style="background:rgba(10,40,77,0.08);color:#0A284D">{{ $case->SubCase }}</span>
                        </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Maxkamadda</span>
                            <span class="text-sm font-bold"
                                style="color:#528CBE">{{ $case->court->longName ?? $case->GradeCourt }}</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Xaalada</span>
                            @php
                                $isActive = in_array($case->Status, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                $isClosed = $case->Status === 'Closed';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                style="background:{{ $isActive ? 'rgba(16,185,129,0.1)' : ($isClosed ? 'rgba(239,68,68,0.1)' : 'rgba(240,180,60,0.12)') }};color:{{ $isActive ? '#059669' : ($isClosed ? '#b91c1c' : '#C07E15') }}">
                                <span class="w-1.5 h-1.5 rounded-full inline-block"
                                    style="background:{{ $isActive ? '#10B981' : ($isClosed ? '#ef4444' : '#F0B43C') }}"></span>
                                {{ $case->Status }}
                            </span>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Taariikhda Dacwada</span>
                            <span
                                class="text-sm text-neutral-600">{{ \Carbon\Carbon::parse($case->OpenDate)->format('d/m/Y') }}</span>
                        </div>
                       @if($case->Orders_Requested)
                            <div class="flex items-start gap-3 md:col-span-2">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">
                                    Amarrada La Codsaday</span>
                                <span class="text-sm text-neutral-600">{{ $case->Orders_Requested }}</span>
                            </div>
                        @endif
                        @if($case->Remarks)
                            <div class="flex items-start gap-3 md:col-span-2">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Nuxurka Dacwada</span>
                                <span class="text-sm text-neutral-600">{{ $case->Remarks }}</span>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Registration Details --}}
                <div class="px-6 py-4">
                    <p class="text-[0.65rem] font-black text-neutral-400 uppercase tracking-widest mb-4">Xogta Dacwada</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4">
                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Kaaliyaha Sare</span>
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
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                style="background:rgba(16,185,129,0.1);color:#059669">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Yes
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ PAYMENT REQUESTS ═══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">

            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-cash-coin text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Codsiyada Lacag Bixinta</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->payments->count() }}
                        {{ Str::plural('diiwaan', $case->payments->count()) }}</span>
                    <a href="{{ route('criminal-registration.payment-request', $case->CMID) }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                        style="background:#528CBE">
                        <i class="bi bi-plus-lg"></i> Codso Lacag Bixin
                    </a>
                </div>
            </div>

            @php
                $applicantParty = $case->parties->firstWhere('party_role', 'Plaintiff');
            @endphp
            @if($applicantParty)
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/60">
                    <p class="text-[0.65rem] font-black text-neutral-400 uppercase tracking-widest mb-3">Macluumaadka Codsadaha</p>
                    <div class="flex flex-wrap items-center gap-x-8 gap-y-2">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-person-circle text-sm" style="color:#528CBE"></i>
                            <span class="text-sm font-semibold text-neutral-800">{{ $applicantParty->full_name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-telephone text-sm text-neutral-400"></i>
                            <span class="text-sm text-neutral-600">{{ $applicantParty->contact_number ?: '—' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-envelope text-sm text-neutral-400"></i>
                            <span class="text-sm text-neutral-600">{{ $applicantParty->email ?: '—' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Codsadaha</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Adeegga</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Cadadka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Xaalada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($case->payments as $index => $payment)
                            @php
                                $pStatusColors = [
                                    'Approved'          => ['bg' => 'rgba(16,185,129,0.1)', 'text' => '#059669'],
                                    'Pending'           => ['bg' => 'rgba(240,180,60,0.12)', 'text' => '#C07E15'],
                                    'Awaiting Approval' => ['bg' => 'rgba(124,58,237,0.1)', 'text' => '#7C3AED'],
                                    'Failed'            => ['bg' => 'rgba(239,68,68,0.1)', 'text' => '#b91c1c'],
                                ];
                                $psc = $pStatusColors[$payment->status] ?? ['bg' => 'rgba(107,114,128,0.1)', 'text' => '#6b7280'];
                                $pStatusLabels = [
                                    'Approved'          => 'La Ansaxiyay',
                                    'Pending'           => 'Sugaya',
                                    'Awaiting Approval' => 'Sugaya Ansaxin',
                                    'Failed'            => 'Fashilmay',
                                ];
                            @endphp
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-neutral-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-neutral-800">{{ $payment->payer_name }}</div>
                                </td>
                                <td class="px-6 py-4 text-neutral-600 text-sm">{{ $payment->payer_phone ?: '—' }}</td>
                                <td class="px-6 py-4 text-neutral-600 text-sm">{{ $payment->tariff->name_so ?? 'Tarifad Guud' }}</td>
                                <td class="px-6 py-4 font-semibold text-neutral-700 text-sm">${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                                <td class="px-6 py-4 text-neutral-600 text-sm">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                        style="background:{{ $psc['bg'] }};color:{{ $psc['text'] }}">{{ $pStatusLabels[$payment->status] ?? $payment->status }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a title="Rasiidka Lacag Bixinta" href="{{ route('criminal-registration.payments.receipt', $payment->id) }}" target="_blank"
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
                                        <p class="text-neutral-400 font-medium text-sm">Weli lama codsan lacag bixin dacwaddan.</p>
                                        <a href="{{ route('criminal-registration.payment-request', $case->CMID) }}"
                                            class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition"
                                            style="background:#528CBE">
                                            Codso Lacag Bixin
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-neutral-100">
                <p class="text-xs text-neutral-400 font-medium">
                    Muujinaya <span class="font-bold text-neutral-600">{{ $case->payments->count() }}</span>
                    {{ $case->payments->count() === 1 ? 'codsi lacag bixin' : 'codsiyo lacag bixin' }}
                </p>
            </div>
        </div>

        {{-- ═══ CASE PARTIES ═══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">

            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-people text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhinacyada Dacwada</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->parties->count() }}
                        {{ Str::plural('party', $case->parties->count()) }}</span>
                    <a href="{{ route('criminal-case-parties.index', ['case_id' => $case->CMID]) }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                        style="background:#528CBE">
                        <i class="bi bi-plus-lg"></i> Manage Parties
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dhinaca</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Buuxa</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Deegaanka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Aqoonsiga / Baasaboor</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dukumentiyada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($case->parties as $index => $party)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-neutral-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleMap = ['Plaintiff' => 'Dacwoode', 'Defendant' => 'Dacwaysane', 'Witness' => 'Markhaati', 'Applicant' => 'Codsade', 'Other' => 'Soo Dhaxgale'];
                                        $localizedRole = $roleMap[$party->party_role] ?? $party->party_role;
                                    @endphp
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                        style="background:{{ $party->party_role === 'Dacwoode' ? 'rgba(82,140,190,0.1)' : 'rgba(239,68,68,0.1)' }};color:{{ $party->party_role === 'Dacwaysane' ? '#528CBE' : '#b91c1c' }}">
                                        {{ $localizedRole }}
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
                                    {{ $party->national_id ?: ($party->passport_number ?: '—') }}</td>
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
                                        <p class="text-neutral-400 font-medium text-sm">No parties registered for this case.</p>
                                        <a href="{{ route('criminal-case-parties.index', ['case_id' => $case->CMID]) }}"
                                            class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition"
                                            style="background:#528CBE">
                                            Register First Party
                                        </a>
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

        {{-- ═══ SUPPORTING DOCUMENTS ═══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">

            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-folder2-open text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Lifaaqyada Dacwada</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->documents->count() }}
                        {{ Str::plural('document', $case->documents->count()) }}</span>
                    <a href="{{ route('criminal-case-documents.index', ['case_id' => $case->CMID]) }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                        style="background:#528CBE">
                        <i class="bi bi-plus-lg"></i> Manage Documents
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Dukumentiga</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faahfaahin</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">File-ka Dukumintiga</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($case->documents as $index => $doc)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-neutral-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-semibold text-neutral-800">{{ $doc->document_name }}</td>
                                <td class="px-6 py-4 text-neutral-500">{{ $doc->description ?: '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($doc->file_path)
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-paperclip text-neutral-400"></i>
                                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-xs font-semibold hover:underline" style="color:#528CBE">Document Attached</a>
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
                                        <p class="text-neutral-400 font-medium text-sm">No supporting documents attached yet.
                                        </p>
                                        <a href="{{ route('criminal-case-documents.index', ['case_id' => $case->CMID]) }}"
                                            class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition"
                                            style="background:#528CBE">
                                            Attach Documents
                                        </a>
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
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-6">

            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-person-badge text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Qareenada Dacwada
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->lawyers->count() }}
                        {{ Str::plural('record', $case->lawyers->count()) }}</span>
                    <a href="{{ route('criminal-case-lawyers.index', ['case_id' => $case->CMID]) }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                        style="background:#528CBE">
                        <i class="bi bi-person-check"></i> Assign Lawyer
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dhinacayada</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Qareenka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">imeyl</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($case->lawyers as $index => $assigned)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-neutral-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleMap = ['Plaintiff' => 'Dacwoode', 'Defendant' => 'Dacwaysane', 'Witness' => 'Markhaati', 'Applicant' => 'Codsade', 'Other' => 'Soo Dhaxgale'];
                                        $localizedRole = $roleMap[$assigned->party_role ?? ''] ?? ($assigned->party_role ?? '—');
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap"
                                            style="background:{{ ($assigned->party_role ?? '') === 'Plaintiff' ? 'rgba(82,140,190,0.1)' : 'rgba(239,68,68,0.1)' }};color:{{ ($assigned->party_role ?? '') === 'Plaintiff' ? '#528CBE' : '#b91c1c' }}">
                                            {{ $localizedRole }}
                                        </span>
                                        <span class="font-semibold text-neutral-800">{{ $assigned->party->full_name ?? '' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-neutral-800">{{ $assigned->lawyer->LawyerName ?? '—' }}</td>
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
                                        <p class="text-neutral-400 font-medium text-sm">No lawyer assignment history found.</p>
                                        <a href="{{ route('criminal-case-lawyers.index', ['case_id' => $case->CMID]) }}"
                                            class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition"
                                            style="background:#528CBE">
                                            Assign Lawyer
                                        </a>
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




    </div>

@endsection