@extends('admin.admin_master')
@section('page_title', $internal ? 'Internal OB' : 'Occurrence Books')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $internal ? 'Internal OB' : 'Occurrence Books' }}</h1>
            <p class="text-sm text-neutral-500 mt-0.5">
                {{ $internal ? 'Station-level incidents and staff incidents generated internally by CID' : 'All occurrence book entries across CID cases' }}
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-6 gap-3">
            <input type="text" name="ob_number" value="{{ request('ob_number') }}" placeholder="OB number"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <input type="text" name="offence_type" value="{{ request('offence_type') }}" placeholder="Offence type"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <select name="priority" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All Priorities</option>
                @foreach(['Routine','Urgent','Critical'] as $p)
                    <option value="{{ $p }}" {{ request('priority') == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
            <select name="officer" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All Officers</option>
                @foreach($investigators as $inv)
                    <option value="{{ $inv->id }}" {{ request('officer') == $inv->id ? 'selected' : '' }}>{{ $inv->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <input type="date" name="to" value="{{ request('to') }}" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <button type="submit" class="col-span-2 sm:col-span-1 text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">OB Number</th>
                    <th class="px-5 py-3">Case</th>
                    <th class="px-5 py-3">Offence</th>
                    <th class="px-5 py-3">Priority</th>
                    <th class="px-5 py-3">Assigned</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Entry Date</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($obs as $ob)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $ob->ob_number }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $ob->criminalCase->case_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $ob->offence_nature }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $ob->priority }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $ob->assignedInvestigator->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">{{ $ob->statusLabel() }}</span>
                        </td>
                        <td class="px-5 py-3 text-neutral-500">{{ $ob->ob_datetime->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('criminal-cases.workflow', $ob->criminal_case_id) }}"
                               class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-neutral-400">No occurrence book entries match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $obs->links() }}</div>

</div>

@endsection
