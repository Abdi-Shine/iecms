@extends('admin.admin_master')
@section('page_title', 'Court Appearance Calendar')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Court Appearance</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Consolidated view of upcoming court appearances across all CID cases</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <input type="text" name="court" value="{{ request('court') }}" placeholder="Court name"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <input type="text" name="officer" value="{{ request('officer') }}" placeholder="Responsible officer"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <input type="date" name="from" value="{{ request('from') }}" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <input type="date" name="to" value="{{ request('to') }}" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <select name="status" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Outcome</option>
            </select>
            <button type="submit" class="col-span-2 sm:col-span-1 text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Case</th>
                    <th class="px-5 py-3">Hearing Type</th>
                    <th class="px-5 py-3">Court</th>
                    <th class="px-5 py-3">Responsible Officer</th>
                    <th class="px-5 py-3">Outcome</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($appearances as $a)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 text-neutral-600">{{ $a->appearance_date->format('Y-m-d') }} {{ $a->appearance_time }}</td>
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $a->criminalCase->case_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $a->hearing_type }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $a->court_name }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $a->responsible_officer ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($a->outcome)
                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-green-50 text-green-700">Recorded</span>
                            @else
                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-amber-50 text-amber-700">Pending</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('criminal-cases.workflow.custody.form', $a->criminal_case_id) }}"
                               class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-neutral-400">No court appearances match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $appearances->links() }}</div>

</div>

@endsection
