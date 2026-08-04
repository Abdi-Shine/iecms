@extends('admin.admin_master')
@section('page_title', 'Document Attachment Registry')
@section('admin_main_content')

<meta name="csrf-token" content="{{ csrf_token() }}">


<div class="p-4 sm:p-6 w-full" x-data="documentAttachmentManager">

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
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Document Attachment Registry</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Manage document attachment types and their court assignments</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Export CSV --}}
            <a href="{{ route('document-attachment.export') }}"
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
            <button @click="openModal()"
                class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90"
                style="background:#528CBE">
                <i class="bi bi-plus-lg"></i> Add Attachment
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-file-earmark-text text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Total</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ count($attachments) }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Document types</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-building text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Court Linked</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                    {{ $attachments->filter(fn($a) => !empty($a->courtID))->count() }}
                </h3>
                <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Assigned to courts</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-pencil-square text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Updated</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                    {{ $attachments->filter(fn($a) => !empty($a->updatedBy))->count() }}
                </h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Recently modified</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-check2-circle text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">System Status</p>
                <h3 class="text-xl font-black text-neutral-800 leading-tight mt-1">Operational</h3>
                <p class="text-xs font-medium mt-1.5" style="color:#F0B43C">Real-time sync</p>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        <div class="px-6 py-4 flex items-center justify-between border-b border-neutral-100">
            <div class="flex items-center gap-2">
                <i class="bi bi-table text-sm" style="color:#528CBE"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Master Document Attachment Registry</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">{{ count($attachments) }} {{ Str::plural('record', count($attachments)) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-14">No#</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Att. Code</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Att. Name</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Court</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($attachments as $index => $att)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-bold text-neutral-400">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg"
                                  style="background:rgba(82,140,190,0.1);color:#3D78AB">
                                {{ $att->Acode }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-sm font-semibold text-neutral-800">{{ $att->Aname }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($att->court)
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                      style="background:rgba(240,180,60,0.12);color:#C07E15">
                                    {{ $att->court->longName }}
                                </span>
                            @elseif($att->courtID)
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                      style="background:rgba(240,180,60,0.12);color:#C07E15">
                                    {{ $att->courtID }}
                                </span>
                            @else
                                <span class="text-xs text-neutral-400 italic">— All Courts —</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openModal({{ $att->AID }}, '{{ addslashes($att->Acode) }}', '{{ addslashes($att->Aname) }}', '{{ addslashes($att->courtID ?? '') }}')"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 hover:text-white transition-all"
                                    onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                    onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                    <i class="bi bi-pencil-square text-xs"></i>
                                </button>
                                <button @click="deleteAttachment({{ $att->AID }})"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                    onmouseover="this.style.background='#DC2626';this.style.borderColor='#DC2626';this.style.color='white'"
                                    onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                    <i class="bi bi-trash3 text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                    <i class="bi bi-file-earmark-text text-2xl" style="color:#528CBE"></i>
                                </div>
                                <p class="text-neutral-400 font-medium text-sm">No document attachments found in the registry.</p>
                                <button @click="openModal()"
                                    class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                                    style="background:#528CBE">
                                    Add First Attachment
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-neutral-100">
            <p class="text-xs text-neutral-400 font-medium">
                Showing <span class="font-bold text-neutral-600">{{ count($attachments) }}</span> {{ Str::plural('record', count($attachments)) }}
            </p>
        </div>
    </div>

    {{-- ═══ Import Modal ═══ --}}
    <div x-show="showImport"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="display:none;background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
         @keydown.escape.window="showImport = false">

        <div class="bg-white flex flex-col overflow-hidden w-full"
             style="max-width:480px;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             @click.outside="showImport = false">

            <div class="flex items-center justify-between flex-shrink-0"
                 style="background:#d97706;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-file-earmark-arrow-up" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <div>
                        <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Import Attachments</h2>
                        <p style="color:rgba(255,255,255,.8);font-size:.75rem;margin:0">Upload a CSV file to bulk-add attachments</p>
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
                        <code style="font-size:.72rem;background:rgba(0,0,0,.06);padding:1px 4px;border-radius:3px">Acode, Aname, courtID</code><br>
                        <span style="color:#b45309">Leave courtID empty for All Courts. Duplicate Acode rows will be skipped.</span>
                    </div>
                </div>

                <form action="{{ route('document-attachment.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom:1.25rem">
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.5rem">
                            CSV File <span style="color:#ef4444">*</span>
                        </label>
                        <label for="import_da_csv"
                               style="display:flex;align-items:center;justify-content:center;gap:.6rem;height:80px;border:2px dashed #d97706;border-radius:.75rem;background:rgba(217,119,6,0.04);cursor:pointer;transition:background .15s"
                               onmouseover="this.style.background='rgba(217,119,6,0.09)'" onmouseout="this.style.background='rgba(217,119,6,0.04)'">
                            <i class="bi bi-cloud-arrow-up" style="font-size:1.4rem;color:#d97706"></i>
                            <span style="font-size:.82rem;font-weight:600;color:#d97706"
                                  x-text="importFile === 'No file chosen' ? 'Click to choose CSV file' : importFile"></span>
                        </label>
                        <input type="file" id="import_da_csv" name="csv_file" accept=".csv,.txt" class="sr-only" required
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
                            <i class="bi bi-upload"></i> Import Attachments
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══ Add / Edit Modal ═══ --}}
    <div x-show="isModalOpen" style="display:none;"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
         @keydown.escape.window="closeModal()">

        <div class="bg-white flex flex-col overflow-hidden w-full"
             style="max-width:600px;max-height:92vh;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             @click.outside="closeModal()">

            {{-- Header --}}
            <div class="flex items-center justify-between flex-shrink-0"
                 :style="isEditMode ? 'background:#F0B43C;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0' : 'background:#528CBE;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0'">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i :class="isEditMode ? 'bi bi-pencil-square' : 'bi bi-file-earmark-plus'" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <div>
                        <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0" x-text="isEditMode ? 'Edit Document Attachment' : 'Register Document Attachment'"></h2>
                        <p style="color:rgba(255,255,255,.8);font-size:.75rem;margin:0">Fill in all required fields</p>
                    </div>
                </div>
                <button @click="closeModal()"
                    style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s"
                    onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                    <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1" style="padding:1.5rem 1.75rem">
                <form @submit.prevent="submitForm">

                    {{-- Row 1: Acode | Aname --}}
                    <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:1rem;margin-bottom:1.25rem">
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Attachment Code <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <input type="text" x-model="formData.Acode" required
                                       placeholder="e.g. DOC-001"
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-hash" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Attachment Name <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <input type="text" x-model="formData.Aname" required
                                       placeholder="e.g. Birth Certificate"
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-card-text" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Court --}}
                    <div style="margin-bottom:1.5rem">
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Court Assignment</label>
                        <div style="position:relative">
                            <select x-model="formData.courtID"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <option value="">— All Courts (General) —</option>
                                @foreach($courts as $court)
                                    <option value="{{ $court->courtcode }}">{{ $court->longName }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                        <button type="button" @click="closeModal()"
                            style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer;transition:background .15s"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                            Cancel
                        </button>
                        <button type="submit" :disabled="isSubmitting"
                            :style="(isEditMode ? 'background:#F0B43C;box-shadow:0 4px 14px rgba(240,180,60,.4)' : 'background:#528CBE;box-shadow:0 4px 14px rgba(82,140,190,.4)') + ';display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;border:none;border-radius:.625rem;cursor:pointer;transition:opacity .15s'"
                            :class="{'opacity-70 cursor-not-allowed': isSubmitting}"
                            onmouseover="if(!this.disabled) this.style.opacity='.88'" onmouseout="if(!this.disabled) this.style.opacity='1'">
                            <i class="bi bi-check-circle-fill" x-show="!isSubmitting"></i>
                            <i class="bi bi-arrow-repeat animate-spin" x-show="isSubmitting" style="display:none;"></i>
                            <span x-text="isEditMode ? 'Save Changes' : 'Register Attachment'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('documentAttachmentManager', () => ({
            isModalOpen:  false,
            isEditMode:   false,
            isSubmitting: false,
            currentId:    null,
            showImport:   false,
            importFile:   'No file chosen',
            formData: { Acode: '', Aname: '', courtID: '' },

            openModal(id = null, Acode = '', Aname = '', courtID = '') {
                this.isEditMode  = !!id;
                this.currentId   = id;
                this.formData    = { Acode, Aname, courtID };
                this.isModalOpen = true;
            },

            closeModal() {
                this.isModalOpen = false;
                this.currentId   = null;
                this.formData    = { Acode: '', Aname: '', courtID: '' };
            },

            async submitForm() {
                this.isSubmitting = true;
                try {
                    const url    = this.isEditMode ? `/document-attachment/${this.currentId}` : '/document-attachment';
                    const method = this.isEditMode ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.closeModal();
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#528CBE',
                            customClass: {
                                title: 'text-2xl font-bold text-neutral-800',
                                confirmButton: 'px-8 py-2 rounded-lg font-bold uppercase tracking-wider text-sm'
                            },
                            buttonsStyling: true,
                            showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
                            hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' }
                        }).then(() => window.location.reload());
                    } else {
                        throw new Error(data.message || 'Validation failed. Attachment code may already exist.');
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#DC2626',
                        customClass: {
                            title: 'text-2xl font-bold text-neutral-800',
                            confirmButton: 'px-8 py-2 rounded-lg font-bold uppercase tracking-wider text-sm'
                        },
                        buttonsStyling: true,
                        showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
                        hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' }
                    });
                } finally {
                    this.isSubmitting = false;
                }
            },

            deleteAttachment(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DC2626',
                    cancelButtonColor: '#528CBE',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        title: 'text-2xl font-bold text-neutral-800',
                        confirmButton: 'px-6 py-2 rounded-lg font-bold text-sm mx-1',
                        cancelButton: 'px-6 py-2 rounded-lg font-bold text-sm mx-1'
                    },
                    buttonsStyling: true,
                    showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
                    hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' }
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/document-attachment/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await response.json();

                            if (response.ok && data.success) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#528CBE',
                                    customClass: {
                                        title: 'text-2xl font-bold text-neutral-800',
                                        confirmButton: 'px-8 py-2 rounded-lg font-bold uppercase tracking-wider text-sm'
                                    },
                                    buttonsStyling: true,
                                    showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
                                    hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' }
                                }).then(() => window.location.reload());
                            } else {
                                throw new Error(data.message || 'Failed to delete attachment.');
                            }
                        } catch (error) {
                            Swal.fire({
                                title: 'Error!',
                                text: error.message,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#DC2626',
                                customClass: {
                                    title: 'text-2xl font-bold text-neutral-800',
                                    confirmButton: 'px-8 py-2 rounded-lg font-bold uppercase tracking-wider text-sm'
                                },
                                buttonsStyling: true,
                                showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
                                hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' }
                            });
                        }
                    }
                });
            }
        }));
    });
</script>

@endsection
