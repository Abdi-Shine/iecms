@extends('admin.admin_master')
@section('page_title', $typeLabel . ' — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $typeLabel }}</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }}</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back to Case
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">New Request</h3>
            <form action="{{ route('cid-legal-process.store', [$case->id, $slug]) }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Requesting Officer</label>
                        <input type="text" name="requesting_officer" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    @if($slug === 'warrant-of-arrest-ago')
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Urgency Level</label>
                            <select name="urgency_level" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                                <option value="Routine">Routine</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                    @elseif($slug === 'asset-recovery-ago')
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Estimated Value</label>
                            <input type="number" step="0.01" name="estimated_value" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                        </div>
                    @endif
                </div>
                <div style="margin-bottom:1rem">
                    <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">
                        {{ $slug === 'asset-recovery-ago' ? 'Asset Description' : ($slug === 'search-seizure-ago' || $slug === 'search-warrants' ? 'Items Sought / Target' : 'Details') }}
                    </label>
                    <textarea name="details" rows="2" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem"></textarea>
                </div>
                <div style="margin-bottom:1rem">
                    <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">
                        {{ $slug === 'arrest-without-warrant-ago' ? 'Circumstances / Legal Basis' : 'Grounds' }}
                    </label>
                    <textarea name="grounds" rows="2" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem"></textarea>
                </div>
                <button type="submit" style="padding:.65rem 1.25rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                    <i class="bi bi-send-fill"></i> Submit Request
                </button>
                @if(in_array($type, \App\Models\CriminalLegalProcessRequest::AGO_TYPES))
                    <span style="font-size:.75rem;color:#6b7280;margin-left:.75rem">Submitting creates a linked AGO referral case.</span>
                @endif
            </form>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($existing as $r)
            <div class="bg-white rounded-2xl border border-neutral-100 p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-neutral-700">{{ $r->details ?? $r->grounds ?? '—' }}</p>
                        <p class="text-xs text-neutral-500 mt-1">By {{ $r->requesting_officer }} on {{ $r->created_at->format('Y-m-d') }}
                            @if($r->attorneyCase) &middot; AGO ref: {{ $r->attorneyCase->case_number }} @endif
                        </p>
                    </div>
                    <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">{{ $r->status }}</span>
                </div>

                <form action="{{ route('cid-legal-process.status', [$case->id, $slug, $r->id]) }}" method="POST" class="flex gap-2 flex-wrap mb-3">
                    @csrf
                    <select name="status" class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg">
                        @foreach(\App\Models\CriminalLegalProcessRequest::STATUSES as $s)
                            <option value="{{ $s }}" {{ $r->status == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="issuing_authority" placeholder="Issuing authority" value="{{ $r->issuing_authority }}" class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg">
                    <input type="date" name="issue_date" value="{{ optional($r->issue_date)->format('Y-m-d') }}" class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg" title="Issue date">
                    <input type="date" name="expiry_date" value="{{ optional($r->expiry_date)->format('Y-m-d') }}" class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg" title="Expiry date">
                    <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#528CBE">Update Status</button>
                </form>

                @if($r->status !== 'Executed' && in_array($slug, ['search-seizure-ago', 'search-warrants']))
                    <form action="{{ route('cid-legal-process.execution', [$case->id, $slug, $r->id]) }}" method="POST" class="flex gap-2 flex-wrap">
                        @csrf
                        <input type="text" name="execution_outcome" placeholder="Execution outcome" required class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg flex-1">
                        <input type="date" name="execution_date" required class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg">
                        <input type="text" name="items_seized" placeholder="Items seized" class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg flex-1">
                        <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#16A34A">Record Execution</button>
                    </form>
                @elseif($r->execution_outcome)
                    <p class="text-xs text-neutral-500"><strong>Execution:</strong> {{ $r->execution_outcome }} ({{ $r->execution_date?->format('Y-m-d') }})
                        @if($r->items_seized) &mdash; Seized: {{ $r->items_seized }} @endif
                    </p>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-neutral-100 p-10 text-center text-neutral-400">No requests yet for this case.</div>
        @endforelse
    </div>

</div>

@endsection
