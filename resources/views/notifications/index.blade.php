@extends('admin.admin_master')
@section('page_title', 'Notifications')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Notifications</h1>
            <p class="text-sm text-neutral-500 mt-0.5">All system notifications sent to your account</p>
        </div>
        <button type="button" onclick="markAllReadPage()"
            class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90 bg-primary-400">
            <i class="bi bi-check2-all"></i> Mark All Read
        </button>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('notifications.page', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}"
            class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-primary-400/10">
                <i class="bi bi-bell text-xl text-primary-400"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Total</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['total'] }}</h3>
            </div>
        </a>

        <a href="{{ route('notifications.page', array_merge(request()->except(['status', 'page']), ['status' => 'unread'])) }}"
            class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-danger-600/10">
                <i class="bi bi-envelope-fill text-xl text-danger-600"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Unread</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['unread'] }}</h3>
            </div>
        </a>

        <a href="{{ route('notifications.page', array_merge(request()->except(['status', 'page']), ['status' => 'read'])) }}"
            class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-success-500/10">
                <i class="bi bi-envelope-open-fill text-xl text-success-500"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Read</p>
                <h3 class="text-2xl font-black text-neutral-800">{{ $stats['read'] }}</h3>
            </div>
        </a>
    </div>

    <!-- List Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        <!-- Filter -->
        <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
            <form action="{{ route('notifications.page') }}" method="GET" class="flex flex-wrap gap-3 items-center">
                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                           outline-none focus:border-primary-400 cursor-pointer transition-all min-w-[160px]">
                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                </select>

                @if(request()->filled('status') && request('status') !== 'all')
                    <a href="{{ route('notifications.page') }}"
                        class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Card Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-neutral-100">
            <div class="flex items-center gap-2">
                <i class="bi bi-bell text-sm text-primary-400"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Notification Log</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">
                {{ $notifications->total() }} total
            </span>
        </div>

        <!-- List -->
        <div class="divide-y divide-neutral-100">
            @forelse($notifications as $n)
                @php
                    $type = $n->data['type'] ?? '';
                    $isApproved = str_contains($type, 'approved');
                    $isRequested = str_contains($type, 'requested') || str_contains($type, 'submitted');
                    $iconBg = $isApproved ? 'bg-success-500/10' : ($isRequested ? 'bg-gold-400/12' : 'bg-primary-400/10');
                    $iconColor = $isApproved ? 'text-success-500' : ($isRequested ? 'text-gold-400' : 'text-primary-400');
                    $icon = $isApproved ? 'bi-patch-check-fill' : ($isRequested ? 'bi-send-fill' : 'bi-bell-fill');
                @endphp
                <div class="flex items-start gap-4 px-6 py-4 hover:bg-neutral-50 transition-colors cursor-pointer notif-row {{ !$n->read_at ? 'bg-primary-400/4' : '' }}"
                    data-id="{{ $n->id }}" data-link="{{ $n->data['link'] ?? '' }}" data-read="{{ $n->read_at ? '1' : '0' }}"
                    onclick="openNotification(this)">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $iconBg }}">
                        <i class="bi {{ $icon }} text-sm {{ $iconColor }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-bold text-neutral-800">{{ $n->data['title'] ?? 'Notification' }}</p>
                            @if(!$n->read_at)
                                <span class="w-2 h-2 rounded-full bg-primary-400 mt-1.5 flex-shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-xs text-neutral-500 mt-0.5">{{ $n->data['body'] ?? '' }}</p>
                        <p class="text-[11px] text-neutral-400 mt-1.5">{{ $n->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="px-6 py-24 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center bg-primary-400/10">
                            <i class="bi bi-bell-slash text-2xl text-primary-400"></i>
                        </div>
                        <p class="text-neutral-400 font-semibold text-sm">No notifications.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $notifications->links() }}
        </div>

    </div>
</div>

<script>
    function openNotification(el) {
        const id = el.dataset.id;
        const link = el.dataset.link;
        const wasRead = el.dataset.read === '1';

        if (!wasRead) {
            fetch(`{{ url('/notifications') }}/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).catch(() => {});
        }

        if (link) {
            window.location.href = link;
        } else if (!wasRead) {
            window.location.reload();
        }
    }

    function markAllReadPage() {
        fetch('{{ route("notifications.read-all") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(() => window.location.reload());
    }
</script>

@endsection
