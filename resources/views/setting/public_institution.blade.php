@extends('admin.admin_master')
@section('page_title', 'Public Institutions Registry')
@section('admin_main_content')

<meta name="csrf-token" content="{{ csrf_token() }}">


<div class="p-4 sm:p-6 w-full" x-data="publicInstitutionManager">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Public Institutions Registry</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Manage government ministries, agencies and public institutions</p>
        </div>
        <button @click="openModal()"
            class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90"
            style="background:#528CBE">
            <i class="bi bi-plus-lg"></i> Register Institution
        </button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        {{-- Total Institutions --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-bank2 text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Total Institutions</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $totalInstitutions }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Registered institutions</p>
            </div>
        </div>

        {{-- System Health --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-check2-circle text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">System Status</p>
                <h3 class="text-xl font-black text-neutral-800 leading-tight mt-1">Operational</h3>
                <p class="text-xs font-medium mt-1.5" style="color:#528CBE">Real-time sync</p>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        {{-- Search & Filter Bar --}}
        <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
            <form action="{{ route('public-institution.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">

                {{-- Page size --}}
                <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                    <span>Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 text-sm font-semibold border border-neutral-300 rounded-full bg-white text-neutral-700
                                               focus:outline-none focus:border-[#528CBE] cursor-pointer">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Search --}}
                <div class="relative flex-1 min-w-[200px]">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by institution name…"
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                               text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-[#528CBE]
                               focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                </div>

                {{-- Search button --}}
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                    style="background:#528CBE">
                    <i class="bi bi-search"></i> Search
                </button>

                @if(request()->filled('search'))
                    <a href="{{ route('public-institution.index') }}"
                        class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Title --}}
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-table text-sm" style="color:#528CBE"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Master Institutions Registry</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">{{ $institutions->total() }} {{ Str::plural('record', $institutions->total()) }}</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-16">No#</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Institution Name</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($institutions as $index => $institution)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        {{-- No --}}
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-neutral-400">
                                {{ str_pad($institutions->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>

                        {{-- Name --}}
                        <td class="px-6 py-4">
                            <span class="font-semibold text-neutral-800">{{ $institution->name }}</span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openModal({{ $institution->id }}, '{{ addslashes($institution->name) }}')"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 hover:text-white transition-all"
                                    onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                    onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                    <i class="bi bi-pencil-square text-xs"></i>
                                </button>
                                <button @click="deleteInstitution({{ $institution->id }})"
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
                        <td colspan="3" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                     style="background:rgba(82,140,190,0.1)">
                                    <i class="bi bi-bank2 text-2xl" style="color:#528CBE"></i>
                                </div>
                                <p class="text-neutral-400 font-medium text-sm">No public institutions found in the registry.</p>
                                <button @click="openModal()"
                                    class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                                    style="background:#528CBE">
                                    Add First Institution
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $institutions->links() }}
        </div>
    </div>

    {{-- ═══ Add / Edit Modal ═══ --}}
    <div x-show="isModalOpen" style="display: none;"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
         @keydown.escape.window="closeModal()">

        <div class="bg-white flex flex-col overflow-hidden w-full"
             style="max-width:550px;max-height:92vh;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             @click.outside="closeModal()">

            {{-- Header --}}
            <div class="flex items-center justify-between flex-shrink-0" :style="isEditMode ? 'background:#F0B43C;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0' : 'background:#528CBE;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0'">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i :class="isEditMode ? 'bi bi-pencil-square' : 'bi bi-bank2'" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <div>
                        <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0" x-text="isEditMode ? 'Edit Institution' : 'Register Institution'"></h2>
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
                    <div style="display:grid;grid-template-columns:1fr;gap:1.25rem;margin-bottom:1.5rem">
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Institution Name <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <input type="text" x-model="formData.name" required placeholder="e.g. Wasaaradda Caafimaadka"
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-bank2" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
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
                            <i class="bi bi-arrow-repeat animate-spin" x-show="isSubmitting" style="display: none;"></i>
                            <span x-text="isEditMode ? 'Save Changes' : 'Register Institution'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('publicInstitutionManager', () => ({
            isModalOpen: false,
            isEditMode: false,
            isSubmitting: false,
            currentId: null,
            formData: {
                name: ''
            },

            openModal(id = null, name = '') {
                this.isEditMode = !!id;
                this.currentId = id;
                this.formData.name = name;
                this.isModalOpen = true;
            },

            closeModal() {
                this.isModalOpen = false;
                this.currentId = null;
                this.formData.name = '';
            },

            async submitForm() {
                this.isSubmitting = true;

                try {
                    let url = '/public-institution';
                    let method = 'POST';

                    if (this.isEditMode) {
                        url = `/public-institution/${this.currentId}`;
                        method = 'PUT';
                    }

                    const response = await fetch(url, {
                        method: method,
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
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown animate__faster'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp animate__faster'
                            }
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Validation failed. Institution might already exist.');
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
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp animate__faster'
                        }
                    });
                } finally {
                    this.isSubmitting = false;
                }
            },

            deleteInstitution(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
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
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    }
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/public-institution/${id}`, {
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
                                    showClass: {
                                        popup: 'animate__animated animate__fadeInDown animate__faster'
                                    },
                                    hideClass: {
                                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                                    }
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                throw new Error(data.message || 'Failed to delete institution.');
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
                                showClass: {
                                    popup: 'animate__animated animate__fadeInDown animate__faster'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                                }
                            });
                        }
                    }
                });
            }
        }));
    });
</script>

@endsection
