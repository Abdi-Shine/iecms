@extends('admin.admin_master')
@section('page_title', 'Arrests Without Warrant')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Arrests Without Warrant</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Legal compliance registry &mdash; each entry must show justification and AGO notification status</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <input type="text" name="case" value="{{ request('case') }}" placeholder="Case number"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <button type="submit" class="text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Case</th>
                    <th class="px-5 py-3">Arrestee</th>
                    <th class="px-5 py-3">Justification</th>
                    <th class="px-5 py-3">AGO Ratification</th>
                    <th class="px-5 py-3">Arrest Date</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($arrests as $a)
                    @php $agoReq = $a->criminalCase->legalProcessRequests->firstWhere('request_type', 'arrest_without_warrant_ago'); @endphp
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $a->criminalCase->case_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $a->arrestee_name }}</td>
                        <td class="px-5 py-3 text-neutral-600 max-w-xs truncate" title="{{ $a->warrantless_justification }}">{{ $a->warrantless_justification ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if(!$agoReq)
                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-red-50 text-red-700">Not Notified</span>
                            @elseif($agoReq->isOverdueForRatification())
                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-red-50 text-red-700">Overdue ({{ $agoReq->status }})</span>
                            @else
                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">{{ $agoReq->status }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-neutral-500">{{ $a->arrest_date->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-right">
                            @if(!$agoReq)
                                <a href="{{ route('cid-legal-process.form', [$a->criminal_case_id, 'arrest-without-warrant-ago']) }}"
                                   class="text-[13px] font-semibold" style="color:#DC2626">Notify AGO &rarr;</a>
                            @else
                                <a href="{{ route('criminal-cases.workflow', $a->criminal_case_id) }}"
                                   class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-neutral-400">No warrantless arrests match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $arrests->links() }}</div>

</div>

@endsection
