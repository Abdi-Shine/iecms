@extends('admin.admin_master')
@section('page_title', $typeLabel)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $typeLabel }}</h1>
            <p class="text-sm text-neutral-500 mt-0.5">All {{ strtolower($typeLabel) }} requests across CID cases</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <input type="text" name="case" value="{{ request('case') }}" placeholder="Case number"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <select name="status" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All Statuses</option>
                @foreach(\App\Models\CriminalLegalProcessRequest::STATUSES as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <button type="submit" class="text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Case</th>
                    <th class="px-5 py-3">Requesting Officer</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Expiry</th>
                    <th class="px-5 py-3">Requested</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $r)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $r->criminalCase->case_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $r->requesting_officer }}</td>
                        <td class="px-5 py-3">
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">{{ $r->status }}</span>
                            @if($r->isOverdueForRatification())
                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-red-50 text-red-700">Overdue</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-neutral-500">{{ $r->expiry_date?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $r->created_at->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('cid-legal-process.form', [$r->criminal_case_id, $slug]) }}"
                               class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-neutral-400">No {{ strtolower($typeLabel) }} requests match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $requests->links() }}</div>

</div>

@endsection
