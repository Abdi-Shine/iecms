@extends('admin.admin_master')
@section('page_title', 'Evidence Registry')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Evidence</h1>
            <p class="text-sm text-neutral-500 mt-0.5">All evidence items across CID cases &mdash; tamper-evident, status-only updates</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-6 gap-3">
            <input type="text" name="evidence_id" value="{{ request('evidence_id') }}" placeholder="Evidence ID"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <input type="text" name="case" value="{{ request('case') }}" placeholder="Case number"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <select name="type" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All Types</option>
                @foreach(['physical','digital','documentary','biological'] as $t)
                    <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
            <select name="status" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All Statuses</option>
                @foreach(\App\Models\CriminalCaseEvidenceItem::STATUSES as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ str_replace('_',' ',$s) }}</option>
                @endforeach
            </select>
            <input type="text" name="officer" value="{{ request('officer') }}" placeholder="Collected by"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <button type="submit" class="text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">ID</th>
                    <th class="px-5 py-3">Case</th>
                    <th class="px-5 py-3">Description</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Current Holder</th>
                    <th class="px-5 py-3">Collected</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $item->evidence_id }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $item->criminalCase->case_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $item->description }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ ucfirst($item->evidence_type) }}</td>
                        <td class="px-5 py-3">
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">{{ str_replace('_',' ',$item->status) }}</span>
                        </td>
                        <td class="px-5 py-3 text-neutral-600">{{ $item->custodyLogs->first()->to_officer ?? $item->collected_by }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $item->collection_date->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('criminal-cases.workflow.evidence.index', $item->criminal_case_id) }}"
                               class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-neutral-400">No evidence items match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

</div>

@endsection
