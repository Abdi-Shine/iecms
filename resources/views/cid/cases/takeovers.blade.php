@extends('admin.admin_master')
@section('page_title', 'Investigation Takeovers — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Investigation Takeovers</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }} &mdash; current investigator: {{ $case->assignment->assignedInvestigator->name ?? 'Unassigned' }}</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back to Case
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Request Takeover</h3>
            <form action="{{ route('criminal-cases.takeovers.store', $case->id) }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Reason</label>
                        <input type="text" name="reason" required class="w-full" style="padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem;width:100%">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Incoming Investigator</label>
                        <select name="incoming_investigator_id" required style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                            <option value="">Select investigator</option>
                            @foreach($investigators as $inv)
                                <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" style="padding:.65rem 1.25rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                    <i class="bi bi-arrow-left-right"></i> Request Takeover
                </button>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($case->takeovers as $t)
            <div class="bg-white rounded-2xl border border-neutral-100 p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h4 class="text-sm font-bold text-neutral-800">
                            {{ $t->outgoingInvestigator->name ?? 'Unassigned' }} &rarr; {{ $t->incomingInvestigator->name }}
                        </h4>
                        <p class="text-xs text-neutral-500 mt-1">{{ $t->reason }}</p>
                    </div>
                    @php
                        $badgeColor = match($t->statusLabel()) {
                            'Approved' => ['#DCFCE7','#16A34A'], 'Rejected' => ['#FEE2E2','#DC2626'], default => ['#F3F4F6','#6B7280']
                        };
                    @endphp
                    <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full" style="background:{{ $badgeColor[0] }};color:{{ $badgeColor[1] }}">{{ $t->statusLabel() }}</span>
                </div>

                <div class="flex items-center gap-4 text-xs text-neutral-500 mb-3">
                    <span><i class="bi {{ $t->outgoing_acknowledged_at ? 'bi-check-circle-fill text-green-600' : 'bi-circle' }}"></i> Outgoing Ack</span>
                    <span><i class="bi {{ $t->incoming_accepted_at ? 'bi-check-circle-fill text-green-600' : 'bi-circle' }}"></i> Incoming Accept</span>
                    <span><i class="bi {{ $t->admin_approved_at ? 'bi-check-circle-fill text-green-600' : 'bi-circle' }}"></i> Admin Approval</span>
                </div>

                @if(!$t->rejected_at && !$t->admin_approved_at)
                    <div class="flex gap-2">
                        @if(!$t->outgoing_acknowledged_at)
                            <form action="{{ route('criminal-cases.takeovers.acknowledge-outgoing', [$case->id, $t->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#528CBE">Acknowledge (Outgoing)</button>
                            </form>
                        @elseif(!$t->incoming_accepted_at)
                            <form action="{{ route('criminal-cases.takeovers.accept-incoming', [$case->id, $t->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#528CBE">Accept (Incoming)</button>
                            </form>
                        @else
                            <form action="{{ route('criminal-cases.takeovers.approve', [$case->id, $t->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#16A34A">Approve (Admin)</button>
                            </form>
                        @endif
                        <form action="{{ route('criminal-cases.takeovers.reject', [$case->id, $t->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg border border-neutral-200 text-neutral-600">Reject (Admin)</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-neutral-100 p-10 text-center text-neutral-400">No takeover requests for this case.</div>
        @endforelse
    </div>

</div>

@endsection
