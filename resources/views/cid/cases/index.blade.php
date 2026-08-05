@extends('admin.admin_master')
@section('page_title', 'Investigation Cases')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Investigation Cases</h1>
            <p class="text-sm text-neutral-500 mt-0.5">CID case registry &mdash; Investigation Workflow</p>
        </div>
        <form action="{{ route('criminal-cases.store') }}" method="POST">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl text-white transition"
                style="background:#528CBE">
                <i class="bi bi-plus-lg"></i> Start New Investigation
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Case Number</th>
                    <th class="px-5 py-3">Arrestee</th>
                    <th class="px-5 py-3">Stage</th>
                    <th class="px-5 py-3">Priority</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Opened</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($cases as $case)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $case->case_number }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $case->arrest->arrestee_name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">
                                {{ str_replace('_', ' ', $case->current_stage) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-neutral-600">{{ $case->priority }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $case->status }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $case->created_at->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('criminal-cases.workflow', $case->id) }}"
                               class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-neutral-400">
                            No investigation cases yet. Click "Start New Investigation" to open one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $cases->links() }}</div>

</div>

@endsection
