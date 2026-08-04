@extends('admin.admin_master')
@section('page_title', 'Audit Logs')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="mb-6 mt-2">
        <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Audit Logs</h1>
        <p class="text-sm text-neutral-500 mt-0.5">Who changed what, when — across every institution</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        <form action="{{ route('audit-logs.index') }}" method="GET" class="reg-filter">
            <select name="action" onchange="this.form.submit()" class="reg-filter-sel">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                @endforeach
            </select>
            @if(request()->anyFilled(['action','user_id']))
                <a href="{{ route('audit-logs.index') }}" class="btn-outline btn-sm">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">When</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">User</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Institution</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Action</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Description</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-4 py-4 text-neutral-500 text-xs">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-4">{{ $log->user?->name ?? '—' }}</td>
                        <td class="px-4 py-4 text-neutral-500">{{ $log->institution?->name ?? '—' }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold"
                                  style="background:rgba(82,140,190,0.1);color:#528CBE">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-neutral-500">{{ $log->description }}</td>
                        <td class="px-4 py-4 text-neutral-400 text-xs">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center text-neutral-400 font-medium text-sm">No audit log entries yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
