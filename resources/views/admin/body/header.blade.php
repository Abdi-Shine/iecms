<header class="app-header">
    <div class="flex items-center justify-between h-full px-3 sm:px-6">

        <!-- Left Section -->
        <div class="flex items-center gap-3">
            <button id="menuToggle" class="header-menu-btn">
                <i class="bi bi-list text-2xl"></i>
            </button>

            @php
                $courtName    = null;
                $userPosition = auth()->user()?->position ?? 'Administrator';
                try {
                    $authUser  = auth()->user();
                    $employee  = $authUser?->employee
                              ?? \App\Models\Employee::where('email', $authUser?->email)->first()
                              ?? \App\Models\Employee::where('EmpName', $authUser?->name)->first();
                    $courtName    = $employee?->court?->longName;
                    $userPosition = $employee?->Position ?? $userPosition;
                } catch (\Throwable $e) {
                    // DB not ready or migration pending — skip employee lookup
                }
            @endphp

            @if($courtName)
                <span class="header-court-name">{{ $courtName }}</span>
            @endif
        </div>

        <!-- Right Section -->
        <div class="flex items-center gap-2 sm:gap-4">

            <!-- Notifications -->
            <div x-data="notifBell()" x-init="init()" class="relative">
                <button @click="toggle()" class="header-bell-btn" style="position:relative">
                    <i class="bi bi-bell text-xl"></i>
                    <span x-show="unread > 0" x-text="unread > 9 ? '9+' : unread"
                          style="position:absolute;top:-4px;right:-4px;min-width:18px;height:18px;background:#ef4444;color:white;border-radius:999px;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1"></span>
                </button>

                <!-- Dropdown -->
                <div x-show="open" @click.away="open = false"
                     style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:340px;background:white;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.15);border:1px solid #e5e7eb;z-index:9999;overflow:hidden">

                    <!-- Header -->
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid #f3f4f6">
                        <span style="font-size:.875rem;font-weight:800;color:#111827">Ogeysiisyada</span>
                        <button x-show="unread > 0" @click="markAll()"
                                style="font-size:.72rem;font-weight:700;color:#528CBE;background:none;border:none;cursor:pointer">
                            Dhamaan akhri
                        </button>
                    </div>

                    <!-- List -->
                    <div style="max-height:380px;overflow-y:auto">
                        <template x-if="notifications.length === 0">
                            <div style="padding:2rem;text-align:center;color:#9ca3af;font-size:.83rem">
                                <i class="bi bi-bell-slash" style="font-size:1.5rem;display:block;margin-bottom:.5rem"></i>
                                Wax ogeysiis ah ma jiro
                            </div>
                        </template>
                        <template x-for="n in notifications" :key="n.id">
                            <div @click="open_notif(n)"
                                 :style="n.read_at ? 'background:white' : 'background:#eff6ff'"
                                 style="display:flex;gap:10px;padding:12px 16px;cursor:pointer;border-bottom:1px solid #f9fafb;transition:background .1s"
                                 onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                                <div :style="n.data.type === 'stamp_approved' ? 'background:rgba(16,185,129,.1)' : 'background:rgba(82,140,190,.1)'"
                                     style="width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
                                    <i :class="n.data.type === 'stamp_approved' ? 'bi bi-patch-check-fill' : 'bi bi-archive'"
                                       :style="n.data.type === 'stamp_approved' ? 'color:#10b981' : 'color:#528CBE'"
                                       style="font-size:.9rem"></i>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:6px">
                                        <p x-text="n.data.title" style="font-size:.8rem;font-weight:700;color:#111827;margin:0;line-height:1.3"></p>
                                        <span x-show="!n.read_at" style="width:7px;height:7px;background:#528CBE;border-radius:50%;flex-shrink:0;margin-top:4px"></span>
                                    </div>
                                    <p x-text="n.data.body" style="font-size:.75rem;color:#6b7280;margin:2px 0 0;line-height:1.4;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical"></p>
                                    <p x-text="n.created_at" style="font-size:.68rem;color:#9ca3af;margin:4px 0 0"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <script>
            function notifBell() {
                return {
                    open: false,
                    unread: 0,
                    notifications: [],
                    init() {
                        this.fetchCount();
                        // Poll every 60 seconds for new notifications
                        setInterval(() => this.fetchCount(), 60000);
                    },
                    fetchCount() {
                        fetch('{{ route("notifications.index") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(r => r.json())
                            .then(data => {
                                this.unread = data.unread;
                                this.notifications = data.notifications;
                            }).catch(() => {});
                    },
                    toggle() {
                        this.open = !this.open;
                        if (this.open) this.fetchCount();
                    },
                    open_notif(n) {
                        if (!n.read_at) {
                            fetch('{{ url("/notifications") }}/' + n.id + '/read', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' }
                            }).then(() => {
                                n.read_at = new Date().toISOString();
                                this.unread = Math.max(0, this.unread - 1);
                            });
                        }
                        if (n.data.link) window.location.href = n.data.link;
                    },
                    markAll() {
                        fetch('{{ route("notifications.read-all") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' }
                        }).then(() => {
                            this.notifications.forEach(n => n.read_at = new Date().toISOString());
                            this.unread = 0;
                        });
                    }
                };
            }
            </script>

            <!-- User Menu -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" class="header-user-btn">

                    <!-- Avatar -->
                    <div class="header-avatar">
                        @if(auth()->check() && auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}"
                                 alt="{{ auth()->user()->name ?? 'User' }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="header-avatar-initials">
                                <span class="text-primary text-sm font-bold">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name ?? 'AD', ' '), 1, 1) ?: substr(auth()->user()->name ?? 'AD', 1, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="hidden sm:block text-left">
                        <div class="header-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div class="header-user-role">{{ ucfirst($userPosition) }}</div>
                    </div>

                    <i class="bi bi-chevron-down header-chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <!-- Dropdown -->
                <div x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="header-dropdown"
                    style="display:none">

                    <!-- Dropdown Header -->
                    <div class="header-dropdown-header">
                        <div class="header-dropdown-avatar">
                            @if(auth()->check() && auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}"
                                     alt="{{ auth()->user()->name ?? 'User' }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="header-dropdown-avatar-initials">
                                    <span class="text-white text-sm font-bold">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name ?? 'AD', ' '), 1, 1) ?: substr(auth()->user()->name ?? 'AD', 1, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="header-dropdown-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="header-dropdown-role">{{ ucfirst($userPosition) }}</div>
                        </div>
                    </div>

                    <!-- Dropdown Items -->
                    <div class="py-2">
                        <a href="{{ route('profile.edit') }}" class="header-dropdown-item">
                            <i class="bi bi-person text-gray-400 text-base"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="{{ route('two-factor.show') }}" class="header-dropdown-item">
                            <i class="bi bi-shield-lock text-gray-400 text-base"></i>
                            <span>Two-Factor Authentication</span>
                        </a>
                        <form method="POST" action="{{ route('lock-screen.lock') }}">
                            @csrf
                            <button type="submit" class="header-dropdown-item">
                                <i class="bi bi-lock text-gray-400 text-base"></i>
                                <span>Lock Screen</span>
                            </button>
                        </form>
                        <div class="header-dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="header-dropdown-item-danger">
                                <i class="bi bi-box-arrow-right text-base"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
