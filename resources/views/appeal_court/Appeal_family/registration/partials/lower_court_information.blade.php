{{-- ═══════════════════════════════════════════════════════════
     LOWER COURT INFORMATION (Macluumaadka Maxkamadda Hoose)
     Read-only mirror of the original district family case that
     was appealed, adapted from appeal_civil's lower_court_information.blade.php
     ═══════════════════════════════════════════════════════════ --}}
<div class="bg-white">
    <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2" style="background:rgba(10,40,77,0.04)">
        <i class="bi bi-bank2 text-sm" style="color:#0A284D"></i>
        <span class="text-xs font-black uppercase tracking-[2px]" style="color:#0A284D">Macluumaadka Maxkamadda
            Hoose</span>
    </div>

    @if(!$lowerCase)
        <div class="flex flex-col items-center gap-3 py-16 text-center">
            <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(10,40,77,0.08)">
                <i class="bi bi-bank2 text-2xl" style="color:#0A284D"></i>
            </div>
            <p class="text-neutral-400 font-medium text-sm">Dacwadda maxkamadda hoose lama helin.</p>
        </div>
    @else
        <div class="divide-y divide-neutral-100">

            {{-- ═══ LOWER CASE REGISTRATION ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                    <i class="bi bi-file-earmark-text text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Macluumaadka Dacwada
                        Qoyska</span>
                </div>

                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4">

                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Gal
                                Lamber</span>
                            <span class="inline-block font-mono text-sm font-bold px-2.5 py-0.5 rounded"
                                style="background:rgba(82,140,190,0.1);color:#3D78AB">{{ $lowerCase->FileNo }}</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Nooca
                                Dacwada</span>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                style="background:rgba(82,140,190,0.1);color:#528CBE">{{ $lowerCase->CaseType }}</span>
                        </div>

                        @if($lowerCase->SubCase)
                            <div class="flex items-start gap-3">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Hoosaadka</span>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                    style="background:rgba(10,40,77,0.08);color:#0A284D">{{ $lowerCase->SubCase }}</span>
                            </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Maxkamadda</span>
                            <span class="text-sm font-bold"
                                style="color:#528CBE">{{ $lowerCase->court->longName ?? $lowerCase->GradeCourt }}</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Xaalada</span>
                            @php
                                $lIsActive = in_array($lowerCase->Status, ['Active', 'Gal Ku Qoris', 'Qaatay']);
                                $lIsClosed = $lowerCase->Status === 'Closed';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                style="background:{{ $lIsActive ? 'rgba(16,185,129,0.1)' : ($lIsClosed ? 'rgba(239,68,68,0.1)' : 'rgba(240,180,60,0.12)') }};color:{{ $lIsActive ? '#059669' : ($lIsClosed ? '#b91c1c' : '#C07E15') }}">
                                <span class="w-1.5 h-1.5 rounded-full inline-block"
                                    style="background:{{ $lIsActive ? '#10B981' : ($lIsClosed ? '#ef4444' : '#F0B43C') }}"></span>
                                {{ $lowerCase->Status }}
                            </span>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Taariikhda
                                Dacwada</span>
                            <span class="text-sm text-neutral-600">{{ \Carbon\Carbon::parse($lowerCase->OpenDate)->format('d/m/Y') }}</span>
                        </div>

                        @if($lowerCase->Orders_Requested)
                            <div class="flex items-start gap-3 md:col-span-2">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Amarrada La
                                    Codsaday</span>
                                <span class="text-sm text-neutral-600">{{ $lowerCase->Orders_Requested }}</span>
                            </div>
                        @endif

                        @if($lowerCase->Remarks)
                            <div class="flex items-start gap-3 md:col-span-2">
                                <span class="text-xs font-semibold text-neutral-400 w-36 flex-shrink-0 pt-0.5">Nuxurka
                                    Dacwada</span>
                                <span class="text-sm text-neutral-600">{{ $lowerCase->Remarks }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ═══ LOWER CASE PARTIES ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-people text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhinacyada Dacwada
                            Qoyska</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $lowerCase->parties->count() }}
                        {{ Str::plural('party', $lowerCase->parties->count()) }}</span>
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
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Aqoonsiga /
                                    Baasaboor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($lowerCase->parties as $index => $party)
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
                                    <td class="px-6 py-4 font-mono text-sm text-neutral-600">
                                        {{ $party->national_id ?: ($party->passport_number ?: '—') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
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

            {{-- ═══ WAKIILADA SHARCIGA ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person-badge text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Wakiilada Sharciga</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $lowerCase->legalRepresentatives->count() }} wakiil</span>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($lowerCase->legalRepresentatives as $rep)
                                @php
                                    $lIsPlaintiff = $rep->party_role === 'Dacwoode';
                                    $lLinkedParty = $rep->party
                                        ?? $lowerCase->parties->firstWhere('PID', $rep->party_id)
                                        ?? $lowerCase->parties->firstWhere('party_role', $rep->party_role);
                                    $lPartyName   = $lLinkedParty?->full_name ?? '—';
                                @endphp
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</td>
                                    <td class="px-6 py-4"><div class="font-semibold text-neutral-800">{{ $lPartyName }}</div></td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                            style="background:{{ $lIsPlaintiff ? 'rgba(82,140,190,0.1)' : 'rgba(239,68,68,0.1)' }};color:{{ $lIsPlaintiff ? '#528CBE' : '#b91c1c' }}">
                                            {{ $rep->party_role ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4"><span class="font-semibold text-neutral-800">{{ $rep->rep_name }}</span></td>
                                    <td class="px-6 py-4">{{ $rep->rep_doc_number ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
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
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Warqadaha Dacwada
                            Qoyska</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $lowerCase->documents->count() }}
                        {{ Str::plural('document', $lowerCase->documents->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca
                                    Dukumentiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faahfaahin</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">File-ka
                                    Dukumintiga</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($lowerCase->documents as $index => $doc)
                                <tr class="hover:bg-neutral-50 transition-colors">
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
                                            <p class="text-neutral-400 font-medium text-sm">No supporting documents attached yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ═══ LAWYER ASSIGNMENT HISTORY ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person-badge text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Qareenada
                            Dacwada Qoyska</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $lowerCase->lawyers->count() }}
                        {{ Str::plural('record', $lowerCase->lawyers->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-12">#</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dhinacayada</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Qareenka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taleefanka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Imeyl</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($lowerCase->lawyers as $index => $assigned)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-neutral-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                            style="background:{{ ($assigned->party_role ?? '') === 'Dacwoode' ? 'rgba(82,140,190,0.1)' : 'rgba(239,68,68,0.1)' }};color:{{ ($assigned->party_role ?? '') === 'Dacwoode' ? '#528CBE' : '#b91c1c' }}">
                                            {{ $assigned->party_role ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-neutral-800">{{ $assigned->lawyer->LawyerName ?? '—' }}</span>
                                            <span class="text-xs text-neutral-400">License: {{ $assigned->lawyer->LID ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-neutral-700">{{ $assigned->lawyer->Phone ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ $assigned->lawyer->Email ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $assigned->assignment_date ? \Carbon\Carbon::parse($assigned->assignment_date)->format('d/m/Y') : '—' }}
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
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Gal ku Qorista Dacwada
                            Qoyska</span>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($lowerCase->assignments as $assignment)
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
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
            </div>

            {{-- ═══ HANDOVER INFORMATION ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                    <i class="bi bi-file-earmark-arrow-up text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhaqdhaqaaqa
                        Dacwadda Qoyska</span>
                </div>

                @if($lowerHandover)
                    @php
                        $lHvStatus = $lowerHandover->status ?? 'Draft';
                        $lHvBg = $lHvStatus === 'Qaatay' ? 'rgba(16,185,129,0.1)' : ($lHvStatus === 'Sug Qaatay' ? 'rgba(240,180,60,0.12)' : 'rgba(82,140,190,0.08)');
                        $lHvColor = $lHvStatus === 'Qaatay' ? '#059669' : ($lHvStatus === 'Sug Qaatay' ? '#C07E15' : '#528CBE');
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr style="background:rgba(82,140,190,0.06)">
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">T.T</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Abuuraha</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dukuumintiga</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Xaalada</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-xs font-bold text-neutral-400">01</td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $lowerHandover->created_by ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ \Carbon\Carbon::parse($lowerHandover->updated_at)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('family-case-handover.document', $lowerCase->FCID) }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all"
                                            style="background:rgba(10,40,77,.07);border-color:rgba(10,40,77,.18);color:#0A284D">
                                            <i class="bi bi-file-earmark-text"></i> Dukuumintiga
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider whitespace-nowrap"
                                            style="background:{{ $lHvBg }};color:{{ $lHvColor }}">{{ $lHvStatus }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-3 py-16 text-center">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(240,180,60,0.1)">
                            <i class="bi bi-file-earmark-x text-2xl" style="color:#F0B43C"></i>
                        </div>
                        <p class="text-neutral-400 font-medium text-sm">Wareejin lama abuuro weli dacwaddan.</p>
                    </div>
                @endif
            </div>

            {{-- ═══ CLOSE CASE INFORMATION ═══ --}}
            @php $lCc = $lowerCase->closeCase; @endphp
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                    <i class="bi bi-x-circle text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Xidhitaanka Dacwadda</span>
                </div>

                @if($lCc)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr style="background:rgba(82,140,190,0.06)">
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">T.T</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">La Abuuray</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Oodista</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dukuumintiga</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tariikhda Go'aanka</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-xs font-bold text-neutral-400">1.</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ $lCc->created_by ?? '—' }}</td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $lCc->judgment_type ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('family-close-case.document', $lowerCase->FCID) }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all"
                                            style="background:rgba(10,40,77,.07);border-color:rgba(10,40,77,.18);color:#0A284D">
                                            <i class="bi bi-file-earmark-text"></i> Dukuumintiga
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $lCc->judgment_date ? \Carbon\Carbon::parse($lCc->judgment_date)->format('d/m/Y') : '—' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-3 py-16 text-center">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(240,180,60,0.1)">
                            <i class="bi bi-file-earmark-x text-2xl" style="color:#F0B43C"></i>
                        </div>
                        <p class="text-neutral-400 font-medium text-sm">Xidhitaan lama abuuro weli dacwaddan.</p>
                    </div>
                @endif
            </div>

            {{-- ═══ HEARING INFORMATION ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-calendar3-week text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Mudeynta Dacwada
                            Qoyska</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $lowerCase->hearings->count() }}
                        {{ Str::plural('hearing', $lowerCase->hearings->count()) }}</span>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($lowerCase->hearings->sortBy('hearing_date') as $hearing)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4"><span class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span></td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $hearing->created_by ?: '—' }}</td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $hearing->hearing_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ substr($hearing->hearing_time, 0, 5) }}</td>
                                    <td class="px-6 py-4 text-neutral-500">{{ Str::limit($hearing->hearing_purpose, 40) ?: '—' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('family-hearings.document', $hearing->id) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold"
                                            style="background:rgba(82,140,190,0.1);color:#528CBE">
                                            <i class="bi bi-file-earmark-text"></i> Eeg
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
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
            </div>

            {{-- ═══ HEARING SCRIPTURE RECORDS ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-journal-text text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhageysi Dacwada
                            Qoyska</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $lowerScriptures->count() }}
                        {{ Str::plural('record', $lowerScriptures->count()) }}</span>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($lowerScriptures as $scripture)
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
                                        <a href="{{ route('family-hearings.scripture.document', $scripture->id) }}"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all">
                                            <i class="bi bi-file-earmark-richtext text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                                style="background:rgba(82,140,190,0.1)">
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
            </div>

            {{-- ═══ JUDGMENT RECORDS ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-gavel text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Xukunka Dacwada
                            Qoyska</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $lowerJudgments->count() }}
                        {{ Str::plural('record', $lowerJudgments->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Kaaliyaha</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Xukunka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca Xukunka</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dukuumintiga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($lowerJudgments as $judgment)
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4"><span class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span></td>
                                    <td class="px-6 py-4 text-neutral-700 text-sm font-medium">{{ $judgment->created_by ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">
                                        {{ $judgment->judgment_date ? \Carbon\Carbon::parse($judgment->judgment_date)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-500 text-sm">{{ $judgment->judgment_type ?: '—' }}</td>
                                    <td class="px-6 py-4">
                                        @if($judgment->judgment_attachment)
                                            <a href="{{ asset('storage/' . $judgment->judgment_attachment) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-all"
                                                style="background:rgba(82,140,190,0.1);color:#3D78AB;border:1px solid rgba(82,140,190,0.2);">
                                                <i class="bi bi-file-earmark-arrow-down"></i> {{ basename($judgment->judgment_attachment) }}
                                            </a>
                                        @else
                                            <span class="text-neutral-400 text-sm">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
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

                {{-- ── Receipt Records ── --}}
                @php $lAllReceipts = $lowerJudgments->flatMap(fn($j) => $j->receipts)->values(); @endphp
                @if($lAllReceipts->count() > 0)
                    <div class="border-t-2 border-dashed border-neutral-100">
                        <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-person-check text-sm" style="color:#15803d"></i>
                                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhinacyada Qaadashada
                                    Xukunka</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                style="background:#dcfce7;color:#15803d;">
                                <i class="bi bi-check-circle-fill"></i> {{ $lAllReceipts->count() }} qaatay
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr style="background:rgba(34,197,94,0.06)">
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">T.T</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Dhinaca</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Xukunka Dhacay</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Qaadashada</th>
                                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Faallo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    @foreach($lAllReceipts as $receipt)
                                        @php
                                            $lIsWin = $receipt->judgment_outcome === 'Loo Xukume';
                                            $lOutBg = $lIsWin ? 'rgba(34,197,94,.12)' : 'rgba(239,68,68,.1)';
                                            $lOutColor = $lIsWin ? '#15803d' : '#b91c1c';
                                            $lOutIco = $lIsWin ? 'bi-trophy-fill' : 'bi-x-circle-fill';
                                            $lIsPlain = strtolower($receipt->party_role ?? '') === 'plaintiff' || $receipt->party_role === 'Dacwoode';
                                            $lRoleColor = $lIsPlain ? '#0A284D' : '#b91c1c';
                                            $lRoleBg = $lIsPlain ? 'rgba(10,40,77,.08)' : 'rgba(220,38,38,.08)';
                                            $lRoleLbl = $lIsPlain ? 'Dacwoode' : 'Dacweysane';
                                        @endphp
                                        <tr class="hover:bg-neutral-50 transition-colors">
                                            <td class="px-6 py-4"><span class="text-xs font-bold text-neutral-400">{{ sprintf('%02d', $loop->iteration) }}</span></td>
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-neutral-800 text-sm">{{ $receipt->party_name ?? '—' }}</div>
                                                <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-full mt-0.5"
                                                    style="background:{{ $lRoleBg }};color:{{ $lRoleColor }};">{{ $lRoleLbl }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($receipt->judgment_outcome)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                                        style="background:{{ $lOutBg }};color:{{ $lOutColor }};">
                                                        <i class="bi {{ $lOutIco }}"></i> {{ $receipt->judgment_outcome }}
                                                    </span>
                                                @else
                                                    <span class="text-neutral-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-neutral-600 text-sm">
                                                {{ $receipt->received_date ? \Carbon\Carbon::parse($receipt->received_date)->format('d/m/Y') : ($receipt->received_at ? $receipt->received_at->format('d/m/Y') : '—') }}
                                            </td>
                                            <td class="px-6 py-4 text-neutral-500 text-sm">{{ $receipt->notes ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ═══ RETURN FILE INFORMATION ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                    <i class="bi bi-arrow-return-left text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Soo Celinta Faylka</span>
                </div>
                @if($lowerReturnFile)
                    @php
                        $lRfClerk = $lowerCase->assignments->first(fn($a) => in_array($a->panel_role, ['Kaaliye', 'Clerk'])) ?? $lowerCase->assignments->first();
                        $lRfDocCount = !empty($lowerReturnFile->documents) ? count($lowerReturnFile->documents) : 0;
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr style="background:rgba(82,140,190,0.06)">
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">T.T</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Kaaliyaha</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tirada Warqadaha</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dukuumintiga</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tariikhda</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-xs font-bold text-neutral-400">1.</td>
                                    <td class="px-6 py-4">
                                        @if($lRfClerk && $lRfClerk->employee)
                                            <div class="flex flex-col">
                                                <span class="font-semibold text-neutral-800">{{ $lRfClerk->employee->EmpName }}</span>
                                                <span class="text-xs font-semibold text-neutral-400 uppercase tracking-tight">{{ $lRfClerk->employee->Position ?? $lRfClerk->panel_role }}</span>
                                            </div>
                                        @else
                                            <span class="font-semibold text-neutral-800">{{ $lowerReturnFile->created_by ?? '—' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $lRfDocCount }}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('family-return-file.document', $lowerCase->FCID) }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all"
                                            style="background:rgba(10,40,77,.07);border-color:rgba(10,40,77,.18);color:#0A284D">
                                            <i class="bi bi-file-earmark-text"></i> Dukuumintiga
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-neutral-600 text-sm">{{ \Carbon\Carbon::parse($lowerReturnFile->updated_at)->format('d/m/Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-3 py-16 text-center">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(240,180,60,0.1)">
                            <i class="bi bi-file-earmark-x text-2xl" style="color:#F0B43C"></i>
                        </div>
                        <p class="text-neutral-400 font-medium text-sm">Soo celin fayl lama abuuro weli dacwaddan.</p>
                    </div>
                @endif
            </div>

            {{-- ═══ DHAQAN GAL (ENFORCEMENT) INFORMATION ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                    <i class="bi bi-hammer text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Dhaqan Galka Dacwada
                        Qoyska</span>
                </div>

                @if($lowerEnforcement)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr style="background:rgba(82,140,190,0.06)">
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">T.T</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Kaaliyaha</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Dhaqan Galka</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dukuumintiga</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-xs font-bold text-neutral-400">01</td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $lowerEnforcement->created_by ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-500">
                                        {{ $lowerEnforcement->enforcement_date ? \Carbon\Carbon::parse($lowerEnforcement->enforcement_date)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-800">{{ $lowerEnforcement->enforcement_type ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        @if($lowerEnforcement->attachment)
                                            <a href="{{ asset('storage/' . $lowerEnforcement->attachment) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border"
                                                style="background:rgba(82,140,190,.08);border-color:rgba(82,140,190,.25);color:#528CBE">
                                                <i class="bi bi-file-earmark-arrow-down"></i> Dukuumintiga
                                            </a>
                                        @else
                                            <span class="text-neutral-300 text-sm">—</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-3 py-16 text-center">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.08)">
                            <i class="bi bi-hammer text-2xl" style="color:#528CBE"></i>
                        </div>
                        <p class="text-neutral-400 font-medium text-sm">Dhaqan Gal wali lama abuurin.</p>
                    </div>
                @endif
            </div>

            {{-- ═══ RAFCAAN (APPEAL) INFORMATION ═══ --}}
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2">
                    <i class="bi bi-arrow-up-circle text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Rafcaan Dacwada
                        Qoyska</span>
                </div>

                @if($lowerAppeal)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr style="background:rgba(82,140,190,0.06)">
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">T.T</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Magaca Abuuraha</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda Rafcaanka</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Nooca</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dhinaca Gudbinaya</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Dukuumintiga</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="px-6 py-4 text-xs font-bold text-neutral-400">01</td>
                                    <td class="px-6 py-4 font-semibold text-neutral-800">{{ $lowerAppeal->created_by ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-500">
                                        {{ $lowerAppeal->appeal_date ? \Carbon\Carbon::parse($lowerAppeal->appeal_date)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-neutral-800">{{ $lowerAppeal->appeal_type ?? '—' }}</td>
                                    <td class="px-6 py-4 text-neutral-700">
                                        {{ !empty($lowerAppeal->appealing_parties) ? implode(', ', (array) $lowerAppeal->appealing_parties) : '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($lowerAppeal->attachment)
                                            <a href="{{ asset('storage/' . $lowerAppeal->attachment) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border"
                                                style="background:rgba(82,140,190,.08);border-color:rgba(82,140,190,.25);color:#528CBE">
                                                <i class="bi bi-file-earmark-arrow-down"></i> Dukuumintiga
                                            </a>
                                        @else
                                            <span class="text-neutral-300 text-sm">—</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-3 py-16 text-center">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.08)">
                            <i class="bi bi-arrow-up-circle text-2xl" style="color:#528CBE"></i>
                        </div>
                        <p class="text-neutral-400 font-medium text-sm">Rafcaan wali lama abuurin.</p>
                    </div>
                @endif
            </div>

        </div>
    @endif
</div>
