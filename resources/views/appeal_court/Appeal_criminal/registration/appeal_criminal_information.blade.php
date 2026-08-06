@extends('admin.admin_master')
@section('page_title', 'Criminal Case Information')
@section('admin_main_content')

    <div class="p-4 sm:p-6 w-full">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-3">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->FileNo }}</h1>
                <p class="text-sm text-neutral-500 mt-0.5">Xogta Guud ee Dacwada Ciqaabta</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('appeal-criminal-registration.supporting', $case->ACMID) }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all shadow-sm"
                    style="background:#528CBE">
                    <i class="bi bi-pencil-square"></i> Wax ka beddel Xogta
                </a>
                <a href="{{ route('appeal-criminal-registration.index') }}"
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

                <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                    <i class="bi bi-file-earmark-text text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Macluumaadka Dacwada Ciqaabta</span>
                </div>

                <div class="divide-y divide-neutral-50">

                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4">

                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Gal Lamber</span>
                                <span class="inline-block font-mono text-sm font-bold px-2.5 py-0.5 rounded"
                                    style="background:rgba(82,140,190,0.1);color:#3D78AB">{{ $case->FileNo }}</span>
                            </div>

                            @if($case->lower_case_no)
                                <div class="flex items-start gap-3">
                                    <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Lambarka Dacwadda Hore</span>
                                    <div class="flex flex-col gap-0.5">
                                        <span class="inline-block font-mono text-sm font-bold px-2.5 py-0.5 rounded w-fit"
                                            style="background:rgba(240,180,60,0.12);color:#C07E15">{{ $case->lower_case_no }}</span>
                                        @php
                                            $lowerCourtName = \App\Models\Court::where('courtcode', $case->lower_court)->value('longName');
                                        @endphp
                                        @if($lowerCourtName)
                                            <span class="text-xs text-neutral-400">{{ $lowerCourtName }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

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
                                <span class="text-sm font-bold" style="color:#528CBE">{{ $case->court->longName ?? $case->GradeCourt }}</span>
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
                                <span class="text-sm text-neutral-600">{{ \Carbon\Carbon::parse($case->OpenDate)->format('d/m/Y') }}</span>
                            </div>

                            @if($case->Orders_Requested)
                                <div class="flex items-start gap-3 md:col-span-2">
                                    <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Amarrada La Codsaday</span>
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

                    <div class="px-6 py-4">
                        <p class="text-[0.65rem] font-black text-neutral-400 uppercase tracking-widest mb-4">Xogta Dacwada</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4">
                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Kaaliyaha Sare</span>
                                <span class="text-sm font-bold text-neutral-800">{{ $case->addedBy }}</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Taariikhda</span>
                                <span class="text-sm text-neutral-600">{{ \Carbon\Carbon::parse($case->addedDate)->format('d/m/Y') }}</span>
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
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhinacyada Dacwada Ciqaabta</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->parties->count() }} {{ Str::plural('party', $case->parties->count()) }}</span>
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
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                            style="background:{{ $party->party_role === 'Dacwoode' ? 'rgba(82,140,190,0.1)' : 'rgba(239,68,68,0.1)' }};color:{{ $party->party_role === 'Dacwoode' ? '#528CBE' : '#b91c1c' }}">
                                            {{ $party->party_role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4"><span class="font-semibold text-neutral-800">{{ $party->full_name }}</span></td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-neutral-700">{{ $party->contact_number ?: '—' }}</span>
                                            <span class="text-xs text-neutral-400">{{ $party->email ?: '' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ $party->district ?: '—' }}</td>
                                    <td class="px-6 py-4 font-mono text-sm text-neutral-600">{{ $party->national_id ?: ($party->passport_number ?: '—') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            @if($party->passport_doc)
                                                <a href="{{ Storage::url($party->passport_doc) }}" class="text-xs font-semibold flex items-center gap-1" style="color:#059669">
                                                    <i class="bi bi-file-check"></i> Passport
                                                </a>
                                            @endif
                                            @if($party->power_of_attorney)
                                                <a href="{{ Storage::url($party->power_of_attorney) }}" class="text-xs font-semibold flex items-center gap-1" style="color:#528CBE">
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
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-people text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">No parties registered for this case.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ═══ WAKIILADA SHARCIGA (rep_name-based legal representatives) ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person-badge text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Wakiilada Sharciga</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->legalRepresentatives->count() }} wakiil</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dhinacaha (Party)</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Doorka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Wakiilka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Lambarka Dukumentiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dukumentiga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($case->legalRepresentatives as $rep)
                                @php
                                    $isPlaintiff = $rep->party_role === 'Dacwoode';
                                    $linkedParty = $rep->party
                                        ?? $case->parties->firstWhere('PID', $rep->party_id)
                                        ?? $case->parties->firstWhere('party_role', $rep->party_role);
                                    $partyName  = $linkedParty?->full_name ?? '—';
                                    $motherName = $linkedParty?->mother_name ?? null;
                                @endphp
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</td>
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
                                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" style="background:rgba(82,140,190,0.1)">
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
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-person-badge text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">Wakiil sharciyeed lama xidhin dacwaddan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ═══ SUPPORTING DOCUMENTS ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-folder2-open text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Warqadaha Dacwada Ciqaabta</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->documents->count() }} {{ Str::plural('document', $case->documents->count()) }}</span>
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
                                <tr class="hover:bg-neutral-50 transition-colors" id="doc-row-{{ $doc->id }}">
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
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ \Carbon\Carbon::parse($doc->document_date)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(240,180,60,0.1)">
                                                <i class="bi bi-folder-x text-2xl" style="color:#F0B43C"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">No supporting documents attached yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ═══ QAREENADA DACWADA (professional Lawyer directory assignments) ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person-badge text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Qareenada Dacwada</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->lawyers->count() }} {{ Str::plural('record', $case->lawyers->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dhinacayada</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Qareenka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Telefoonka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Emailka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($case->lawyers as $index => $assign)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-neutral-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap"
                                                style="background:{{ ($assign->party_role ?? '') === 'Dacwoode' ? 'rgba(82,140,190,0.1)' : 'rgba(239,68,68,0.1)' }};color:{{ ($assign->party_role ?? '') === 'Dacwoode' ? '#528CBE' : '#b91c1c' }}">
                                                {{ $assign->party_role ?? '—' }}
                                            </span>
                                            <span class="font-semibold text-neutral-800">{{ $assign->party->full_name ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $assign->lawyer->LawyerName ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-600">{{ $assign->lawyer->Phone ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-600">{{ $assign->lawyer->Email ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ $assign->assignment_date ? \Carbon\Carbon::parse($assign->assignment_date)->format('d/m/Y') : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-person-badge text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">Qareen lama helin.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ═══ JUDICIAL PANEL ASSIGNMENTS ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person-workspace text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Gal ku Qorista Dacwada Ciqaabta</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">No#</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Gorsoore/Kaaliye</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Imeyl</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Xilka Guddiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Xaalada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($case->assignments as $assignment)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4"><span class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span></td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-neutral-800">{{ $assignment->employee->EmpName ?? 'N/A' }}</span>
                                            <span class="text-xs text-neutral-400 uppercase font-semibold">{{ $assignment->employee->Position ?? 'Staff' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ $assignment->employee->email ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ $assignment->employee->phone ?? '—' }}</td>
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
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-person-workspace text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">No judicial panel members assigned yet.</p>
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

            {{-- ═══ HEARING INFORMATION ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-calendar3-week text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Mudeynta Dacwada Ciqaabta</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $case->hearings->count() }} {{ Str::plural('hearing', $case->hearings->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Kaaliyaha</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tariikhda Dacwadda</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Saacadda</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Ujeedada</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Fayl</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Xaalada</th>
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
                                    <td class="px-6 py-4"><span class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span></td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $hearing->created_by ?: '—' }}</td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $hearing->hearing_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ substr($hearing->hearing_time, 0, 5) }}</td>
                                    <td class="px-6 py-4 text-neutral-500">{{ Str::limit($hearing->hearing_purpose, 40) ?: '—' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('appeal-criminal-hearings.document', $hearing->id) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold"
                                            style="background:rgba(82,140,190,0.1);color:#528CBE">
                                            <i class="bi bi-file-earmark-text"></i> Eeg
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                            style="background:{{ $hBg }};color:{{ $hColor }}">
                                            <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:{{ $hDot }}"></span>
                                            {{ $case->Status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-calendar3-week text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">Wali ma jiraan mudeynno la qorsheeyay.</p>
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
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhageysi Dacwada Ciqaabta</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $scriptures->count() }} {{ Str::plural('record', $scriptures->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Kaaliyaha</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Dhageysiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Dhageysiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Dukuumintiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Xaalada</th>
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
                                    <td class="px-6 py-4"><span class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span></td>
                                    <td class="px-6 py-4 text-neutral-700 text-sm font-medium">{{ $scripture->created_by ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $scripture->hearing_date ? \Carbon\Carbon::parse($scripture->hearing_date)->format('d/m/Y') : '—' }}
                                        @if($scripture->hearing_time)
                                            <span class="text-xs text-neutral-400 block">{{ substr($scripture->hearing_time, 0, 5) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-neutral-500 text-sm">{{ $scripture->hearing_type ?: '—' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('appeal-criminal-hearings.scripture.document', $scripture->id) }}"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-file-earmark-richtext text-xs"></i>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                            style="background:{{ $sBg }};color:{{ $sColor }}">
                                            <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:{{ $sDot }}"></span>
                                            {{ $scripture->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                                <i class="bi bi-journal-text text-2xl" style="color:#528CBE"></i>
                                            </div>
                                            <p class="text-neutral-400 font-medium text-sm">No hearing scripture records found.</p>
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

            @if($case->lower_case_no)
                @include('appeal_court.Appeal_criminal.registration.partials.lower_court_information')
            @endif

        </div>{{-- /ALL SECTIONS WRAPPER --}}

    </div>

@endsection
