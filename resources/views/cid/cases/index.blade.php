@extends('admin.admin_master')
@section('page_title', 'Investigation Cases')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Investigation Cases</h1>
            <p class="text-sm text-neutral-500 mt-0.5">CID case registry &mdash; Investigation Workflow</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('criminal-cases.export') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-download"></i> Export
            </a>
            <form action="{{ route('criminal-cases.store') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl text-white transition"
                    style="background:#528CBE">
                    <i class="bi bi-plus-lg"></i> Start New Investigation
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-neutral-100 p-4 mb-4">
        <form action="{{ route('criminal-cases.index') }}" method="GET" class="grid grid-cols-2 sm:grid-cols-6 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Case number"
                class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <select name="stage" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All Stages</option>
                @foreach(\App\Models\CriminalCase::STAGES as $stage)
                    <option value="{{ $stage }}" {{ request('stage') == $stage ? 'selected' : '' }}>{{ str_replace('_',' ', $stage) }}</option>
                @endforeach
            </select>
            <select name="priority" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All Priorities</option>
                @foreach(['Routine','Urgent','Critical'] as $p)
                    <option value="{{ $p }}" {{ request('priority') == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
            <select name="officer" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">All Investigators</option>
                @foreach($investigators as $inv)
                    <option value="{{ $inv->id }}" {{ request('officer') == $inv->id ? 'selected' : '' }}>{{ $inv->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <input type="date" name="to" value="{{ request('to') }}" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
            <button type="submit" class="col-span-2 sm:col-span-1 text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Filter</button>
        </form>
    </div>

    <form action="{{ route('criminal-cases.bulk-reassign') }}" method="POST" id="bulkForm">
        @csrf

        @if($isAdmin)
            <div class="bg-white rounded-2xl border border-neutral-100 p-3 mb-4 flex items-center gap-3">
                <select name="investigator_id" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <option value="">Reassign selected to...</option>
                    @foreach($investigators as $inv)
                        <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Bulk Reassign</button>
                <button type="submit" formaction="{{ route('criminal-cases.bulk-close') }}" class="text-sm font-semibold px-3 py-2 rounded-lg border border-neutral-200 text-neutral-600">Bulk Close</button>
            </div>
        @endif

        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                        @if($isAdmin)<th class="px-5 py-3 w-8"></th>@endif
                        <th class="px-5 py-3">Case Number</th>
                        <th class="px-5 py-3">OB Number</th>
                        <th class="px-5 py-3">Suspect</th>
                        <th class="px-5 py-3">Assigned Investigator</th>
                        <th class="px-5 py-3">Stage</th>
                        <th class="px-5 py-3">Priority</th>
                        <th class="px-5 py-3">Last Updated</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cases as $case)
                        <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                            @if($isAdmin)
                                <td class="px-5 py-3"><input type="checkbox" name="case_ids[]" value="{{ $case->id }}"></td>
                            @endif
                            <td class="px-5 py-3 font-semibold text-neutral-800">{{ $case->case_number }}</td>
                            <td class="px-5 py-3 text-neutral-600">{{ $case->occurrenceBook->ob_number ?? '—' }}</td>
                            <td class="px-5 py-3 text-neutral-600">{{ $case->arrest->arrestee_name ?? '—' }}</td>
                            <td class="px-5 py-3 text-neutral-600">
                                {{ $case->assignment->assignedInvestigator->name ?? $case->occurrenceBook->assignedInvestigator->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">
                                    {{ str_replace('_', ' ', $case->current_stage) }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                @php $prColor = ['Routine'=>'#6B7280','Urgent'=>'#F0B43C','Critical'=>'#DC2626'][$case->priority] ?? '#6B7280'; @endphp
                                <span class="text-[11px] font-bold uppercase tracking-wide" style="color:{{ $prColor }}">{{ $case->priority }}</span>
                            </td>
                            <td class="px-5 py-3 text-neutral-500">{{ $case->updated_at->diffForHumans() }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('criminal-cases.workflow', $case->id) }}"
                                   class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-neutral-400">
                                No investigation cases match these filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-4">{{ $cases->links() }}</div>

</div>

@endsection
