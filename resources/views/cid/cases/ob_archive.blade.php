@extends('admin.admin_master')
@section('page_title', 'OB Archive')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">OB Archive</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Read-only &mdash; occurrence books for closed cases</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <input type="text" name="ob_number" value="{{ request('ob_number') }}" placeholder="OB number"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <input type="date" name="from" value="{{ request('from') }}" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <input type="date" name="to" value="{{ request('to') }}" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <button type="submit" class="text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">OB Number</th>
                    <th class="px-5 py-3">Case</th>
                    <th class="px-5 py-3">Offence</th>
                    <th class="px-5 py-3">Entry Date</th>
                    <th class="px-5 py-3">Closed</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($obs as $ob)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $ob->ob_number }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $ob->criminalCase->case_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $ob->offence_nature }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $ob->ob_datetime->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $ob->criminalCase->updated_at->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('criminal-cases.workflow', $ob->criminal_case_id) }}"
                               class="text-[13px] font-semibold" style="color:#528CBE">View &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-neutral-400">No archived occurrence books yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $obs->links() }}</div>

</div>

@endsection
