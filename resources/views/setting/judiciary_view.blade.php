@extends('admin.admin_master')
@section('page_title', 'Judiciary Registration')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white"
            style="background:#528CBE">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Judiciary Registration</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Register and manage all courts and judiciary bodies across the justice system</p>
        </div>
        <div class="flex items-center gap-2" x-data="{ showImport: false, importFile: 'No file chosen' }">
            {{-- Export CSV --}}
            <a href="{{ route('court.export') }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border transition-all hover:opacity-90"
               style="border-color:#16a34a;color:#16a34a;background:rgba(22,163,74,0.06)">
                <i class="bi bi-file-earmark-arrow-down"></i> Export
            </a>
            {{-- Import CSV --}}
            <button @click="showImport = true"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border transition-all hover:opacity-90"
               style="border-color:#d97706;color:#d97706;background:rgba(217,119,6,0.06)">
                <i class="bi bi-file-earmark-arrow-up"></i> Import
            </button>
            <a href="{{ route('court.create') }}"
                class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90"
                style="background:#528CBE">
                <i class="bi bi-building-add"></i> Add New Court
            </a>

            {{-- Import Modal --}}
            <div x-show="showImport"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[70] flex items-center justify-center p-4"
                 style="display:none;background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
                 @keydown.escape.window="showImport = false">

                <div class="bg-white flex flex-col overflow-hidden w-full"
                     style="max-width:480px;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)"
                     @click.outside="showImport = false">

                    <div class="flex items-center justify-between flex-shrink-0"
                         style="background:#d97706;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0">
                        <div class="flex items-center gap-3">
                            <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                                <i class="bi bi-file-earmark-arrow-up" style="color:white;font-size:1.1rem"></i>
                            </div>
                            <div>
                                <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Import Courts</h2>
                                <p style="color:rgba(255,255,255,.8);font-size:.75rem;margin:0">Upload a CSV file to bulk-add courts</p>
                            </div>
                        </div>
                        <button @click="showImport = false"
                            style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center"
                            onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                            <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                        </button>
                    </div>

                    <div style="padding:1.5rem 1.75rem">
                        <div class="flex items-start gap-3 mb-5 p-3 rounded-xl" style="background:rgba(217,119,6,0.07);border:1px solid rgba(217,119,6,0.2)">
                            <i class="bi bi-info-circle-fill mt-0.5 flex-shrink-0" style="color:#d97706"></i>
                            <div style="font-size:.78rem;color:#92400e;line-height:1.5">
                                CSV columns (in order):<br>
                                <code style="font-size:.72rem;background:rgba(0,0,0,.06);padding:1px 4px;border-radius:3px">courtcode, shortName, longName, type, status, telephone, email, address</code><br>
                                <span style="color:#b45309">Duplicate courtcode rows will be skipped.</span>
                            </div>
                        </div>

                        <form action="{{ route('court.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div style="margin-bottom:1.25rem">
                                <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.5rem">
                                    CSV File <span style="color:#ef4444">*</span>
                                </label>
                                <label for="import_court_csv"
                                       style="display:flex;align-items:center;justify-content:center;gap:.6rem;height:80px;border:2px dashed #d97706;border-radius:.75rem;background:rgba(217,119,6,0.04);cursor:pointer;transition:background .15s"
                                       onmouseover="this.style.background='rgba(217,119,6,0.09)'" onmouseout="this.style.background='rgba(217,119,6,0.04)'">
                                    <i class="bi bi-cloud-arrow-up" style="font-size:1.4rem;color:#d97706"></i>
                                    <span style="font-size:.82rem;font-weight:600;color:#d97706"
                                          x-text="importFile === 'No file chosen' ? 'Click to choose CSV file' : importFile"></span>
                                </label>
                                <input type="file" id="import_court_csv" name="csv_file" accept=".csv,.txt" class="sr-only" required
                                       @change="importFile = $event.target.files[0]?.name || 'No file chosen'">
                            </div>

                            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:.875rem;border-top:1.5px solid #f3f4f6">
                                <button type="button" @click="showImport = false"
                                    style="padding:.6rem 1.4rem;font-size:.8rem;font-weight:700;color:#6b7280;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer"
                                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    Cancel
                                </button>
                                <button type="submit"
                                    style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.8rem;font-weight:700;color:white;background:#d97706;border:none;border-radius:.625rem;cursor:pointer;box-shadow:0 4px 14px rgba(217,119,6,.3)"
                                    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                                    <i class="bi bi-upload"></i> Import Courts
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-building text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Total Courts</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['total'] }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">All branches</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(22,163,74,0.1)">
                <i class="bi bi-check-circle text-xl text-green-600"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Active</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['active'] }}</h3>
                <p class="text-xs font-medium mt-0.5 text-green-600">Operational</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(220,38,38,0.08)">
                <i class="bi bi-x-circle text-xl text-red-500"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Inactive</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['inactive'] }}</h3>
                <p class="text-xs font-medium mt-0.5 text-red-500">Non-operational</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-diagram-3 text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Court Types</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['types'] }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Distinct categories</p>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        {{-- Search & Filter --}}
        <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
            <form action="{{ route('court.index') }}" method="GET"
                class="flex flex-wrap gap-3 items-center">
                {{-- Page size --}}
                <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                    <span>Show</span>
                    <select name="per_page" onchange="this.form.submit()"
                        class="px-3 py-1.5 text-sm font-semibold border border-neutral-300 rounded-full bg-white text-neutral-700
                               focus:outline-none focus:border-[#528CBE] cursor-pointer">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative flex-1 min-w-[200px]">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by code, short or long name…"
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                               text-neutral-700 focus:outline-none focus:border-[#528CBE] focus:ring-2
                               focus:ring-[#528CBE]/20 transition-all placeholder-neutral-400">
                </div>
                <select name="type" onchange="this.form.submit()"
                    class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                           focus:outline-none focus:border-[#528CBE] min-w-[180px] cursor-pointer">
                    <option value="">All Types</option>
                    @foreach($courtTypes as $ct)
                        <option value="{{ $ct->name }}" {{ request('type') == $ct->name ? 'selected' : '' }}>{{ $ct->name }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                           focus:outline-none focus:border-[#528CBE] min-w-[140px] cursor-pointer">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                    style="background:#528CBE">
                    <i class="bi bi-search"></i> Search
                </button>
                @if(request()->anyFilled(['search','type','status']))
                    <a href="{{ route('court.index') }}"
                        class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500
                               hover:bg-neutral-50 transition flex items-center gap-2">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Title --}}
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-table text-sm" style="color:#528CBE"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Judiciary Registry</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">{{ $courts->total() }} {{ Str::plural('record', $courts->total()) }}</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-10">No#</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Short Name</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Long Name</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Type</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Telephone</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Status</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($courts as $index => $court)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-4 py-4">
                                <span class="text-xs font-bold text-neutral-400">{{ str_pad($courts->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="font-semibold text-neutral-800">{{ $court->shortName }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-neutral-700 text-sm font-medium max-w-[200px] truncate" title="{{ $court->longName }}">
                                    {{ $court->longName }}
                                </p>
                                @if($court->address)
                                    <p class="text-xs text-neutral-400 mt-0.5 truncate max-w-[200px]">{{ $court->address }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap"
                                    style="background:rgba(240,180,60,0.12);color:#C07E15">
                                    {{ $court->type }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-neutral-600 text-sm">{{ $court->telephone ?? '—' }}</td>
                            <td class="px-4 py-4">
                                @if($court->status === 'active')
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-green-50 text-green-600">Active</span>
                                @else
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-500">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('court.edit', $court->CAI) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                        onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                        onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                        <i class="bi bi-pencil-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('court.destroy', $court->CAI) }}" method="POST"
                                        onsubmit="confirmDelete(event, this)">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                            onmouseover="this.style.background='#DC2626';this.style.borderColor='#DC2626';this.style.color='white'"
                                            onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                            <i class="bi bi-trash3 text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                        style="background:rgba(82,140,190,0.1)">
                                        <i class="bi bi-building text-2xl" style="color:#528CBE"></i>
                                    </div>
                                    <p class="text-neutral-400 font-medium text-sm">No courts found in the registry.</p>
                                    <a href="{{ route('court.create') }}"
                                        class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                                        style="background:#528CBE">
                                        Add First Court
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $courts->links() }}
        </div>
    </div>

</div>
@endsection
