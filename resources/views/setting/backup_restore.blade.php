@extends('admin.admin_master')
@section('page_title', 'Backup & Restore')
@section('admin_main_content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="p-4 bg-neutral-50 min-h-screen max-w-full overflow-x-hidden" x-data="backupManager">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">System Backup & Security</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Manage backups, snapshots, and data restoration</p>
        </div>
        <button @click="createBackup()"
            class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90"
            style="background:#528CBE">
            <i class="bi bi-cloud-arrow-up"></i> Immediate Backup
        </button>
    </div>

    {{-- Alert Banner --}}
    <div class="mb-6 bg-primary/10 border border-primary/20 rounded-2xl p-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center text-sm shadow-sm ring-4 ring-primary/5">
                <i class="bi bi-info text-primary"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-xs font-bold text-neutral-700">Dynamic Snapshot Protection Active</span>
                <span class="text-[10px] text-neutral-500 font-medium italic">Last backup verified
                    {{ $backups->first() ? \Carbon\Carbon::parse($backups->first()->created_at)->diffForHumans() : 'No recent entries' }}</span>
            </div>
        </div>
        <div class="hidden md:flex items-center gap-6">
            <div class="flex flex-col items-end">
                <span class="text-[9px] font-black text-neutral-400 uppercase tracking-widest">Next Scheduled Window</span>
                <span class="text-[11px] font-bold text-neutral-700" x-text="'Daily at ' + formatTime(switches.backup_time)"></span>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-hdd-stack text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Archived States</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ number_format($stats['total_backups'] ?? 0) }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Available restores</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-shield-check text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Success Rate</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['success_rate'] ?? '100%' }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Operational</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-database-fill text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Vault Volume</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $dbSize }} <span class="text-lg font-bold">MB</span></h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Database integrity</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-clock-history text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Retention Policy</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $settings['backup_retention'] ?? '30' }} <span class="text-lg font-bold">days</span></h3>
                <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Auto-cleanup active</p>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">

        {{-- Left: Table + Cloud --}}
        <div class="lg:col-span-3 space-y-4 min-w-0">

            {{-- History Table --}}
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">

                {{-- Table Title --}}
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-clock-history text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Historical Snapshot Ledger</span>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">{{ $backups->count() }} {{ Str::plural('record', $backups->count()) }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr style="background:rgba(82,140,190,0.06)">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Date & Time</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Type</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Volume</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Backup Status</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Restore Status</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($backups as $backup)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-neutral-800 block leading-tight">{{ \Carbon\Carbon::parse($backup->created_at)->format('d/m/Y') }}</span>
                                    <span class="text-xs text-neutral-400 font-medium leading-tight">{{ \Carbon\Carbon::parse($backup->created_at)->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($backup->type === 'auto')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                              style="background:rgba(240,180,60,0.12);color:#C07E15">
                                            <i class="bi bi-robot"></i> Auto
                                        </span>
                                    @elseif($backup->type === 'gmail')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                              style="background:rgba(82,140,190,0.1);color:#528CBE">
                                            <i class="bi bi-envelope-at"></i> Gmail
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                              style="background:rgba(156,163,175,0.15);color:#6B7280">
                                            <i class="bi bi-hand-index-thumb"></i> Manual
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-neutral-600">{{ $backup->size }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                          style="background:rgba(16,185,129,0.1);color:#059669">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Verified
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-neutral-600">
                                    @if($backup->restore_status === 'restored')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                              style="background:rgba(82,140,190,0.1);color:#528CBE">
                                            <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#528CBE"></span>
                                            Restored ({{ \Carbon\Carbon::parse($backup->restored_at)->format('d/m/Y') }})
                                        </span>
                                    @elseif($backup->restore_status === 'failed')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                              style="background:rgba(239,68,68,0.1);color:#b91c1c">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span> Failed
                                        </span>
                                    @else
                                        <span class="text-neutral-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="restoreBackup('{{ $backup->id }}')"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            title="Restore"
                                            onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-arrow-clockwise text-xs"></i>
                                        </button>
                                        <a href="{{ route('backup.download', $backup->id) }}"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            title="Download"
                                            onmouseover="this.style.background='#F0B43C';this.style.borderColor='#F0B43C';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-download text-xs"></i>
                                        </a>
                                        <button @click="deleteBackup('{{ $backup->id }}')"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            title="Delete"
                                            onmouseover="this.style.background='#DC2626';this.style.borderColor='#DC2626';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                             style="background:rgba(82,140,190,0.1)">
                                            <i class="bi bi-database-exclamation text-2xl" style="color:#528CBE"></i>
                                        </div>
                                        <p class="text-neutral-400 font-medium text-sm">No archival records found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-neutral-100">
                    <p class="text-xs text-neutral-400 font-medium">
                        Showing <span class="font-bold text-neutral-600">{{ $backups->count() }}</span> {{ Str::plural('backup', $backups->count()) }}
                    </p>
                </div>
            </div>

            {{-- Cloud Synchronizer --}}
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-1">
                        <h4 class="text-sm font-bold text-neutral-800 tracking-tight">Cloud Synchronizer</h4>
                        <p class="text-[11px] text-neutral-500 leading-relaxed font-medium">Automatic multi-point data mirroring to off-site secure vaults.</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full border-2 border-white bg-primary/10 flex items-center justify-center text-primary text-xs shadow-sm">
                                <i class="bi bi-google"></i>
                            </div>
                            <div class="w-8 h-8 rounded-full border-2 border-white bg-primary/10 flex items-center justify-center text-primary text-xs shadow-sm">
                                <i class="bi bi-box"></i>
                            </div>
                        </div>
                        <button @click="backupToGmail()"
                            class="px-4 py-1.5 bg-accent text-neutral-800 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm hover:opacity-90 transition-all">
                            Configure
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Control Panel --}}
        <div class="lg:col-span-1 space-y-4 min-w-0">
            <div class="bg-primary rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-accent/5 rounded-full blur-3xl"></div>

                <div class="flex items-center justify-between mb-4 relative z-10">
                    <h4 class="text-sm font-bold flex items-center gap-2">
                        <i class="bi bi-gear-fill text-accent"></i> Control Panel
                    </h4>
                    <div x-show="isSaving" x-cloak class="flex items-center gap-1.5 px-2 py-0.5 bg-accent/10 border border-accent/20 rounded-full">
                        <i class="bi bi-arrow-repeat animate-spin text-[10px] text-accent"></i>
                        <span class="text-[8px] font-black text-accent uppercase tracking-widest">Syncing</span>
                    </div>
                </div>

                <div class="space-y-1 relative z-10">
                    {{-- Automated Toggle --}}
                    <div class="flex items-center justify-between p-3 rounded-xl hover:bg-white/5 transition-colors">
                        <div class="flex flex-col">
                            <span class="text-[11px] font-bold text-white tracking-wide">Automated Protocol</span>
                            <span class="text-[9px] text-white/50 italic"
                                x-text="switches.automated ? 'Daily at ' + formatTime(switches.backup_time) : 'Protocol Disabled'"></span>
                        </div>
                        <div @click="switches.automated = !switches.automated; commitChanges()"
                            class="w-8 h-4 rounded-full relative flex items-center px-0.5 cursor-pointer transition-all duration-300"
                            :class="switches.automated ? 'bg-accent' : 'bg-white/20'">
                            <div class="w-3 h-3 bg-white rounded-full transition-transform duration-300 shadow-sm"
                                :class="switches.automated ? 'translate-x-4' : 'translate-x-0'"></div>
                        </div>
                    </div>

                    {{-- System Clock --}}
                    <div class="px-3 py-1.5 bg-white/5 rounded-lg flex items-center justify-between mt-1">
                        <span class="text-[9px] font-semibold text-white/50 uppercase tracking-tight whitespace-nowrap">System Clock</span>
                        <span class="text-[10px] font-bold text-accent whitespace-nowrap" x-text="currentTime"></span>
                    </div>

                    {{-- Time & Retention --}}
                    <div class="p-3 bg-white/5 rounded-xl space-y-3" x-show="switches.automated" x-transition>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-white/70">Execution Time</span>
                            <input type="time" x-model="switches.backup_time" @change="commitChanges()"
                                class="bg-white/10 border border-white/20 rounded-md text-[10px] px-2 py-1 focus:outline-none focus:border-accent text-white">
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-white/70">Retention (Days)</span>
                            <input type="number" x-model="switches.retention" @change="commitChanges()" min="1"
                                class="w-16 bg-white/10 border border-white/20 rounded-md text-[10px] px-2 py-1 focus:outline-none focus:border-accent text-white">
                        </div>
                    </div>

                    {{-- Compression --}}
                    <div class="flex items-center justify-between p-3 rounded-xl hover:bg-white/5 transition-colors">
                        <div class="flex flex-col">
                            <span class="text-[11px] font-bold text-white tracking-wide">Data Compression</span>
                            <span class="text-[9px] text-white/50 italic">Optimized Storage</span>
                        </div>
                        <div @click="switches.compression = !switches.compression"
                            class="w-8 h-4 rounded-full relative flex items-center px-0.5 cursor-pointer transition-all duration-300"
                            :class="switches.compression ? 'bg-accent' : 'bg-white/20'">
                            <div class="w-3 h-3 bg-white rounded-full transition-transform duration-300 shadow-sm"
                                :class="switches.compression ? 'translate-x-4' : 'translate-x-0'"></div>
                        </div>
                    </div>

                    {{-- Encryption --}}
                    <div class="flex items-center justify-between p-3 rounded-xl hover:bg-white/5 transition-colors">
                        <div class="flex flex-col">
                            <span class="text-[11px] font-bold text-white tracking-wide">High Encryption</span>
                            <span class="text-[9px] text-white/50 italic">AES-256 Grade</span>
                        </div>
                        <div @click="switches.encryption = !switches.encryption"
                            class="w-8 h-4 rounded-full relative flex items-center px-0.5 cursor-pointer transition-all duration-300"
                            :class="switches.encryption ? 'bg-accent' : 'bg-white/20'">
                            <div class="w-3 h-3 bg-white rounded-full transition-transform duration-300 shadow-sm"
                                :class="switches.encryption ? 'translate-x-4' : 'translate-x-0'"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('backupManager', () => ({
        isSaving: false,
        serverDate: new Date('{{ $settings["server_time"] }}'.replace(/-/g, '/')),
        currentTime: '',
        switches: {
            automated:   {{ $settings['auto_backup_enabled'] ? 'true' : 'false' }},
            compression: true,
            encryption:  true,
            retention:   {{ $settings['backup_retention'] }},
            backup_time: '{{ $settings['backup_time'] }}'
        },

        init() {
            this.updateClock();
            setInterval(() => {
                this.serverDate.setSeconds(this.serverDate.getSeconds() + 1);
                this.updateClock();
            }, 1000);
            this.checkScheduledBackup();
            setInterval(() => this.checkScheduledBackup(), 60000);
        },

        updateClock() {
            let h = this.serverDate.getHours(), m = this.serverDate.getMinutes(), s = this.serverDate.getSeconds();
            const ap = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            this.currentTime = [h, m, s].map(n => String(n).padStart(2, '0')).join(':') + ' ' + ap;
        },

        formatTime(t) {
            if (!t) return '--:--';
            const [h, m] = t.split(':');
            const hi = parseInt(h), ap = hi >= 12 ? 'PM' : 'AM';
            return `${hi % 12 || 12}:${m} ${ap}`;
        },

        commitChanges() {
            this.isSaving = true;
            fetch('{{ route('backup.settings.update') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    automated: this.switches.automated,
                    retention: this.switches.retention,
                    backup_time: this.switches.backup_time
                })
            }).finally(() => { this.isSaving = false; });
        },

        createBackup() {
            Swal.fire({
                title: 'Confirm System Backup?',
                text: 'This will generate a full snapshot of your database.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#528CBE',
                cancelButtonColor: '#F0B43C',
                confirmButtonText: 'Start Backup',
                customClass: { popup: 'rounded-[1.25rem]' }
            }).then(r => {
                if (!r.isConfirmed) return;
                Swal.fire({ title: 'Creating backup...', allowOutsideClick: false, didOpen: () => {
                    Swal.showLoading();
                    fetch('{{ route('backup.create') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Backup Successful', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Backup Failed', data.message, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Error', err.message, 'error'));
                }});
            });
        },

        restoreBackup(id) {
            Swal.fire({
                title: 'Confirm System Restore?',
                text: 'WARNING: This will overwrite your current database. This action is irreversible!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#528CBE',
                confirmButtonText: 'Yes, Restore System',
                customClass: { popup: 'rounded-[1.25rem]' }
            }).then(r => {
                if (!r.isConfirmed) return;
                Swal.fire({ title: 'Restoring System...', html: 'Please do not close this window.', allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        fetch(`/backup/restore/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Restored!', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Restore Failed', data.message, 'error').then(() => location.reload());
                            }
                        })
                        .catch(err => Swal.fire('Error!', err.message, 'error').then(() => location.reload()));
                    }
                });
            });
        },

        deleteBackup(id) {
            Swal.fire({
                title: 'Delete Backup?',
                text: 'This file will be permanently removed from disk.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#528CBE',
                confirmButtonText: 'Delete Now'
            }).then(r => {
                if (!r.isConfirmed) return;
                fetch(`/backup/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                });
            });
        },

        checkScheduledBackup() {
            if (!this.switches.automated) return;
            fetch('{{ route('backup.scheduled.trigger') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ automated: true, backup_time: this.switches.backup_time })
            })
            .then(r => r.json())
            .then(data => { if (data.success) location.reload(); })
            .catch(() => {});
        },

        backupToGmail() {
            Swal.fire({
                title: 'Gmail Security Vault',
                text: 'Enter the destination email for the encrypted system snapshot:',
                input: 'email',
                inputPlaceholder: 'admin@example.com',
                showCancelButton: true,
                confirmButtonColor: '#528CBE',
                confirmButtonText: 'Encrypt & Send',
                customClass: { popup: 'rounded-[1.25rem]' }
            }).then(r => {
                if (!r.isConfirmed || !r.value) return;
                Swal.fire({ title: 'Sending backup...', allowOutsideClick: false, didOpen: () => {
                    Swal.showLoading();
                    fetch('{{ route('backup.gmail') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: r.value })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Sent!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Failed!', data.message, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Error', err.message, 'error'));
                }});
            });
        }
    }));
});
</script>

@endsection
