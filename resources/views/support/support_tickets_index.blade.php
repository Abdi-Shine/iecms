@extends('admin.admin_master')
@section('page_title', 'Help Desk')
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
    @endphp

    <div class="p-4 sm:p-6 w-full">

        {{-- Page Header --}}
        <div class="flex items-start justify-between gap-4 mb-6 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Help Desk</h1>
                <p class="text-sm text-neutral-500 mt-1">View and manage your support tickets</p>
            </div>
            <a href="{{ route('support-tickets.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90 shrink-0 ml-auto">
                <i class="bi bi-plus-lg"></i> Create New Ticket
            </a>
        </div>

        @if(session('success'))
            <div class="mb-5 px-4 py-3 rounded-xl bg-success-50 border border-success-200 text-success-700 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-neutral-100 p-5 shadow-sm">
                <p class="text-sm text-neutral-500">Total Tickets</p>
                <h3 class="text-2xl font-black text-neutral-800 mt-1">{{ $stats['total'] }}</h3>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-100 p-5 shadow-sm">
                <p class="text-sm text-neutral-500">Open</p>
                <h3 class="text-2xl font-black mt-1" style="color:#16A34A">{{ $stats['open'] }}</h3>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-100 p-5 shadow-sm">
                <p class="text-sm text-neutral-500">In Progress</p>
                <h3 class="text-2xl font-black mt-1" style="color:#D97706">{{ $stats['in_progress'] }}</h3>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-100 p-5 shadow-sm">
                <p class="text-sm text-neutral-500">Waiting</p>
                <h3 class="text-2xl font-black mt-1" style="color:#7C3AED">{{ $stats['waiting'] }}</h3>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-100 p-5 shadow-sm">
                <p class="text-sm text-neutral-500">Resolved</p>
                <h3 class="text-2xl font-black mt-1" style="color:#2563EB">{{ $stats['resolved'] }}</h3>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-100 p-5 shadow-sm">
                <p class="text-sm text-neutral-500">Closed</p>
                <h3 class="text-2xl font-black text-neutral-500 mt-1">{{ $stats['closed'] }}</h3>
            </div>
        </div>

        {{-- Search & Filters --}}
        <form method="GET" action="{{ route('support-tickets.index') }}"
            class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-5 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-5">
                    <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wide mb-1.5">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ticket number, title..."
                        class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wide mb-1.5">Status</label>
                    <select name="status" class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                        <option value="all">All Statuses</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wide mb-1.5">Priority</label>
                    <select name="priority" class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                        <option value="all">All Priorities</option>
                        @foreach($priorities as $p)
                            <option value="{{ $p }}" @selected(request('priority') === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wide mb-1.5">Category</label>
                    <select name="category" class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                        <option value="all">All Categories</option>
                        @foreach($categories as $c)
                            <option value="{{ $c }}" @selected(request('category') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:opacity-90 transition-all">
                        <i class="bi bi-funnel-fill text-xs"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- Ticket List --}}
        <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 gap-4 flex-wrap">
                <h2 class="font-bold text-neutral-800">{{ $isSuperAdmin ? 'All Tickets' : 'My Tickets' }}</h2>
                <div class="flex items-center gap-4">
                    <form method="GET" action="{{ route('support-tickets.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="priority" value="{{ request('priority') }}">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <label for="per_page" class="text-sm text-neutral-500">Show</label>
                        <select id="per_page" name="per_page" onchange="this.form.submit()"
                            class="rounded-full border border-neutral-200 pl-3 pr-7 py-1 text-sm font-semibold text-neutral-700 focus:outline-none focus:ring-2 focus:ring-primary-300">
                            @foreach($perPageOptions as $opt)
                                <option value="{{ $opt }}" @selected($perPage === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </form>
                    <p class="text-sm text-neutral-500">Total: <span class="font-bold text-neutral-700">{{ $tickets->total() }}</span></p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wide bg-neutral-50">
                            <th class="px-6 py-3">Ticket Number</th>
                            <th class="px-6 py-3">Title</th>
                            @if($isSuperAdmin)
                                <th class="px-6 py-3">Submitted By</th>
                            @endif
                            <th class="px-6 py-3">Priority</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Assigned To</th>
                            <th class="px-6 py-3">Last Updated</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-neutral-600">{{ $ticket->ticket_number }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-neutral-800 truncate max-w-xs">{{ $ticket->title }}</p>
                                    <p class="text-xs text-neutral-400">Category: {{ $ticket->category }}</p>
                                </td>
                                @if($isSuperAdmin)
                                    <td class="px-6 py-4 text-neutral-600">{{ $ticket->user->name ?? '—' }}</td>
                                @endif
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold"
                                        style="background:{{ $priorityColors[$ticket->priority]['bg'] ?? '#F3F4F6' }}; color:{{ $priorityColors[$ticket->priority]['text'] ?? '#4B5563' }}">
                                        {{ $ticket->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold"
                                        style="background:{{ $statusColors[$ticket->status]['bg'] ?? '#F3F4F6' }}; color:{{ $statusColors[$ticket->status]['text'] ?? '#4B5563' }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-neutral-600">{{ $ticket->assignee->name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-4 text-neutral-500">{{ $ticket->updated_at->diffForHumans() }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('support-tickets.show', $ticket->id) }}" class="text-primary font-semibold hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isSuperAdmin ? 8 : 7 }}" class="px-6 py-10 text-center text-neutral-400">
                                    No tickets found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-neutral-100">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
