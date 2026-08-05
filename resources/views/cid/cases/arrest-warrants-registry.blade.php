@extends('admin.admin_master')
@section('page_title', 'Arrest Warrants')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Arrest Warrants</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Registry of all arrest warrants used across CID cases</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <input type="text" name="case" value="{{ request('case') }}" placeholder="Case number"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <select name="status" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
            <button type="submit" class="text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Warrant Number</th>
                    <th class="px-5 py-3">Case</th>
                    <th class="px-5 py-3">Arrestee</th>
                    <th class="px-5 py-3">Issuing Court</th>
                    <th class="px-5 py-3">Expiry</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($arrests as $a)
                    @php $expired = $a->warrant_expiry_date && $a->warrant_expiry_date->isPast(); @endphp
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $a->warrant_number }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $a->criminalCase->case_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $a->arrestee_name }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $a->warrant_issuing_court ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $a->warrant_expiry_date?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full {{ $expired ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                                {{ $expired ? 'Expired' : 'Active' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('criminal-cases.workflow', $a->criminal_case_id) }}"
                               class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-neutral-400">No arrest warrants match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $arrests->links() }}</div>

</div>

@endsection
