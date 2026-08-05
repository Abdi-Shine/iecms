@extends('admin.admin_master')
@section('page_title', 'Investigation Conclusion Reports')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Investigation Conclusion Reports</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Read-only registry of final investigation reports across all CID cases</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <select name="recommendation" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All Recommendations</option>
                @foreach(\App\Models\CriminalCaseFinalReport::RECOMMENDATIONS as $r)
                    <option value="{{ $r }}" {{ request('recommendation') == $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <input type="date" name="to" value="{{ request('to') }}" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <button type="submit" class="text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Report Number</th>
                    <th class="px-5 py-3">Case</th>
                    <th class="px-5 py-3">Recommendation</th>
                    <th class="px-5 py-3">Endorsed By</th>
                    <th class="px-5 py-3">AGO Status</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $report->report_number }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $report->criminalCase->case_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $report->recommendation }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $report->supervisor->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($report->isSubmitted())
                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-green-50 text-green-700">Submitted</span>
                            @else
                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">Not Submitted</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-neutral-500">{{ $report->created_at->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('criminal-cases.workflow.report.form', $report->criminal_case_id) }}"
                               class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-neutral-400">No conclusion reports match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $reports->links() }}</div>

</div>

@endsection
