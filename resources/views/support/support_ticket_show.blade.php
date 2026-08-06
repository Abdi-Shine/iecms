@extends('admin.admin_master')
@section('page_title', 'Ticket ' . $ticket->ticket_number)
@section('admin_main_content')

    @php
        $statusColors = [
            'Open' => ['bg' => '#DCFCE7', 'text' => '#16A34A'],
            'In Progress' => ['bg' => '#FEF3C7', 'text' => '#D97706'],
            'Waiting for User Response' => ['bg' => '#EDE9FE', 'text' => '#7C3AED'],
            'Resolved' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
            'Closed' => ['bg' => '#F3F4F6', 'text' => '#4B5563'],
        ];
        $priorityColors = [
            'Low' => ['bg' => '#F3F4F6', 'text' => '#4B5563'],
            'Medium' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
            'High' => ['bg' => '#FEE2E2', 'text' => '#DC2626'],
        ];
        $activityIcons = [
            'created' => 'bi-plus-circle',
            'status_changed' => 'bi-arrow-repeat',
            'assigned' => 'bi-person-check',
            'replied' => 'bi-chat-dots',
        ];
    @endphp

    <div class="p-4 sm:p-6 w-full">

        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('support-tickets.index') }}"
                class="w-9 h-9 rounded-lg border border-neutral-200 flex items-center justify-center text-neutral-500 hover:bg-neutral-50 transition-colors shrink-0">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="min-w-0">
                <p class="text-xs font-mono text-neutral-400">{{ $ticket->ticket_number }}</p>
                <h1 class="text-xl font-bold text-neutral-800 tracking-tight truncate">{{ $ticket->title }}</h1>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 px-4 py-3 rounded-xl bg-success-50 border border-success-200 text-success-700 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main column: description + conversation --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold"
                            style="background:{{ $statusColors[$ticket->status]['bg'] ?? '#F3F4F6' }}; color:{{ $statusColors[$ticket->status]['text'] ?? '#4B5563' }}">
                            {{ $ticket->status }}
                        </span>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold"
                            style="background:{{ $priorityColors[$ticket->priority]['bg'] ?? '#F3F4F6' }}; color:{{ $priorityColors[$ticket->priority]['text'] ?? '#4B5563' }}">
                            {{ $ticket->priority }} Priority
                        </span>
                        <span class="text-xs text-neutral-400">{{ $ticket->category }}</span>
                    </div>
                    <p class="text-sm text-neutral-700 whitespace-pre-line leading-relaxed">{{ $ticket->description }}</p>

                    @if($ticket->attachments->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mt-4">
                            @foreach($ticket->attachments as $file)
                                <a href="{{ route('support-tickets.attachments.download', $file->id) }}"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-neutral-200 text-xs font-medium text-neutral-600 hover:bg-neutral-50 transition-colors">
                                    <i class="bi bi-paperclip text-neutral-400"></i>
                                    {{ $file->original_name }}
                                    <span class="text-neutral-400">({{ $file->human_size }})</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <p class="text-xs text-neutral-400 mt-4">Submitted by {{ $ticket->user->name ?? '—' }} · {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-6">
                    <h2 class="font-bold text-neutral-800 mb-4">Conversation</h2>
                    <div class="space-y-4">
                        @forelse($ticket->replies as $reply)
                            <div class="flex gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary-50 flex items-center justify-center text-primary font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($reply->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="flex-1 bg-neutral-50 rounded-xl px-4 py-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-semibold text-neutral-800">{{ $reply->user->name ?? '—' }}</span>
                                        <span class="text-xs text-neutral-400">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-neutral-600 whitespace-pre-line">{{ $reply->message }}</p>
                                    @if($reply->attachments->isNotEmpty())
                                        <div class="flex flex-wrap gap-2 mt-2.5">
                                            @foreach($reply->attachments as $file)
                                                <a href="{{ route('support-tickets.attachments.download', $file->id) }}"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-neutral-200 bg-white text-xs font-medium text-neutral-600 hover:bg-neutral-100 transition-colors">
                                                    <i class="bi bi-paperclip text-neutral-400"></i>
                                                    {{ $file->original_name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-neutral-400">No replies yet.</p>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('support-tickets.reply', $ticket->id) }}" enctype="multipart/form-data" class="mt-5 pt-5 border-t border-neutral-100">
                        @csrf
                        <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Add a reply</label>
                        <textarea name="message" rows="3" required placeholder="Type your message..."
                            class="w-full rounded-lg border border-neutral-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"></textarea>
                        <div class="flex items-center justify-between mt-3 gap-3 flex-wrap">
                            <input type="file" name="attachments[]" multiple
                                class="text-xs text-neutral-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary file:font-semibold file:text-xs hover:file:bg-primary-100">
                            <button type="submit"
                                class="px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl shadow hover:opacity-90 transition-all shrink-0">
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Activity Log --}}
                <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-6">
                    <h2 class="font-bold text-neutral-800 mb-4">Activity Log</h2>
                    <div class="space-y-4">
                        @forelse($ticket->activities as $activity)
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500 shrink-0">
                                    <i class="bi {{ $activityIcons[$activity->type] ?? 'bi-dot' }} text-sm"></i>
                                </div>
                                <div class="flex-1 pt-1">
                                    <p class="text-sm text-neutral-600">{{ $activity->description }}</p>
                                    <p class="text-xs text-neutral-400 mt-0.5">{{ $activity->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-neutral-400">No activity recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Side column: details + admin controls --}}
            <div class="space-y-5">
                <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-5">
                    <h2 class="font-bold text-neutral-800 mb-3 text-sm">Ticket Details</h2>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-400">Submitted By</dt>
                            <dd class="font-semibold text-neutral-700">{{ $ticket->user->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-400">Assigned To</dt>
                            <dd class="font-semibold text-neutral-700">{{ $ticket->assignee->name ?? 'Unassigned' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-400">Created</dt>
                            <dd class="font-semibold text-neutral-700">{{ $ticket->created_at->format('d/m/Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-400">Last Updated</dt>
                            <dd class="font-semibold text-neutral-700">{{ $ticket->updated_at->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>

                @if($isSuperAdmin)
                    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-5">
                        <h2 class="font-bold text-neutral-800 mb-3 text-sm">Manage Ticket</h2>
                        <form method="POST" action="{{ route('support-tickets.update-status', $ticket->id) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wide mb-1.5">Status</label>
                                <select name="status" class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                                    @foreach($statuses as $s)
                                        <option value="{{ $s }}" @selected($ticket->status === $s)>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wide mb-1.5">Assign To</label>
                                <select name="assigned_to" class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                                    <option value="">Unassigned</option>
                                    @foreach($staff as $member)
                                        <option value="{{ $member->id }}" @selected($ticket->assigned_to === $member->id)>{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="w-full px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl shadow hover:opacity-90 transition-all">
                                Update Ticket
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
