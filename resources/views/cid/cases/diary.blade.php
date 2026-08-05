@extends('admin.admin_master')
@section('page_title', 'Investigation Diary — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Investigation Diary</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }} &mdash; chronological log, entries cannot be edited or deleted</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back to Case
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <form action="{{ route('criminal-cases.diary.store', $case->id) }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="description" placeholder="Add a note to the case diary..." required
                    class="flex-1 text-sm px-4 py-2.5 border border-neutral-200 rounded-xl">
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl text-white" style="background:#528CBE">
                    <i class="bi bi-plus-lg"></i> Add Entry
                </button>
            </form>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($case->diaryEntries as $entry)
            <div class="bg-white rounded-2xl border border-neutral-100 p-4 flex items-start gap-4">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                     style="background:{{ $entry->entry_type == 'system' ? '#EFF6FF' : '#F5F3FF' }};color:{{ $entry->entry_type == 'system' ? '#528CBE' : '#7C3AED' }}">
                    <i class="bi {{ $entry->entry_type == 'system' ? 'bi-gear-fill' : 'bi-pencil-fill' }} text-sm"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-neutral-800">{{ $entry->action_type }}</h4>
                        <span class="text-xs text-neutral-400">{{ $entry->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @if($entry->description)
                        <p class="text-sm text-neutral-600 mt-1">{{ $entry->description }}</p>
                    @endif
                    <p class="text-xs text-neutral-400 mt-1">{{ $entry->user->name ?? 'System' }}</p>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-neutral-100 p-10 text-center text-neutral-400">
                No diary entries yet.
            </div>
        @endforelse
    </div>

</div>

@endsection
