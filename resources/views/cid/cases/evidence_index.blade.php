@extends('admin.admin_master')
@section('page_title', 'Evidence — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Evidence</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }} &mdash; {{ $case->evidenceItems->count() }} item(s) logged</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back
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

    {{-- Add evidence item --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Log New Evidence Item</h3>
            <form action="{{ route('criminal-cases.workflow.evidence.store', $case->id) }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:1rem;align-items:end">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Description</label>
                        <input type="text" name="description" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Type</label>
                        <select name="evidence_type" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                            <option value="physical">Physical</option>
                            <option value="digital">Digital</option>
                            <option value="documentary">Documentary</option>
                            <option value="biological">Biological</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Collected Date</label>
                        <input type="date" name="collection_date" required value="{{ date('Y-m-d') }}" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Collected By</label>
                        <input type="text" name="collected_by" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <button type="submit" style="padding:.65rem 1rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                        <i class="bi bi-plus-lg"></i> Add
                    </button>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Collection Location</label>
                        <input type="text" name="collection_location" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Storage Location</label>
                        <input type="text" name="storage_location" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Evidence list --}}
    <div class="space-y-4">
        @forelse($case->evidenceItems as $item)
            <div class="bg-white rounded-2xl border border-neutral-100 p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-neutral-800">{{ $item->description }}</h3>
                        <p class="text-xs text-neutral-500">{{ ucfirst($item->evidence_type) }} &middot; collected {{ $item->collection_date->format('Y-m-d') }} by {{ $item->collected_by }}
                            @if($item->storage_location) &middot; stored at {{ $item->storage_location }} @endif
                        </p>
                    </div>
                    <form action="{{ route('criminal-cases.workflow.evidence.status', [$case->id, $item->id]) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <select name="status" onchange="this.form.submit()"
                            class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full border-none"
                            style="background:#F3F4F6;color:#374151">
                            @foreach(\App\Models\CriminalCaseEvidenceItem::STATUSES as $status)
                                <option value="{{ $status }}" {{ $item->status == $status ? 'selected' : '' }}>{{ str_replace('_',' ', $status) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Custody chain --}}
                <div class="mt-3 pt-3 border-t border-neutral-50">
                    <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wide mb-2">Chain of Custody</p>
                    <ul class="space-y-1 mb-3">
                        @foreach($item->custodyLogs as $log)
                            <li class="text-xs text-neutral-600">
                                <i class="bi bi-arrow-right-short"></i>
                                {{ $log->to_officer }}
                                @if($log->from_officer) <span class="text-neutral-400">(from {{ $log->from_officer }})</span> @endif
                                &mdash; {{ $log->transferred_at->format('Y-m-d H:i') }}
                                @if($log->reason) <span class="text-neutral-400">{{ $log->reason }}</span> @endif
                            </li>
                        @endforeach
                    </ul>
                    <form action="{{ route('criminal-cases.workflow.evidence.custody', [$case->id, $item->id]) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="to_officer" placeholder="Transfer to officer" required
                            class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg flex-1">
                        <input type="text" name="reason" placeholder="Reason"
                            class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg flex-1">
                        <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#528CBE">Transfer</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-neutral-100 p-10 text-center text-neutral-400">
                No evidence items logged yet.
            </div>
        @endforelse
    </div>

</div>

@endsection
