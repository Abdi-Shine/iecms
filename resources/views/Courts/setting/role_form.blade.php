@extends('admin.admin_master')
@section('page_title', isset($role) ? 'Edit Role' : 'Add New Role')
@section('admin_main_content')

@php
    $isEditMode = isset($role);
@endphp

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">
                {{ $isEditMode ? 'Edit Role' : 'Add New Role' }}
            </h1>
            <p class="text-sm text-neutral-500 mt-0.5">
                {{ $isEditMode ? $role->display_name : 'Define a new system role and its badge color' }}
            </p>
        </div>
        <a href="{{ route('roles.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden" style="max-width:560px">
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2"
            style="background:{{ $isEditMode ? 'rgba(240,180,60,0.06)' : 'rgba(82,140,190,0.06)' }}">
            <i class="bi bi-{{ $isEditMode ? 'pencil-square' : 'shield-plus' }} text-sm"
                style="color:{{ $isEditMode ? '#F0B43C' : '#528CBE' }}"></i>
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Role Details</span>
        </div>

        <form action="{{ $isEditMode ? route('roles.update', $role->id) : route('roles.store') }}"
            method="POST" class="p-6">
            @csrf
            @if($isEditMode)
                @method('PUT')
            @endif

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-white bg-danger-600">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display:grid;gap:1rem;margin-bottom:1.25rem">
                <div>
                    <label class="cl">Role ID
                        @if($isEditMode)
                            <span style="font-size:.55rem;color:#9ca3af;background:#f3f4f6;padding:.1rem .35rem;border-radius:.2rem;margin-left:.2rem;font-weight:700">LOCKED</span>
                        @endif
                    </label>
                    <div style="position:relative">
                        <input type="text" value="{{ $isEditMode ? ($role->role_id ?? '—') : $nextRoleId }}" readonly tabindex="-1"
                               style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;font-family:monospace;font-weight:700;border:1.5px solid #d1d5db;border-radius:.625rem;background:#f3f4f6;color:#6b7280;outline:none;box-sizing:border-box;cursor:not-allowed">
                        <i class="bi bi-lock" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                    </div>
                    @unless($isEditMode)
                        <p style="font-size:.68rem;color:#9ca3af;margin:.3rem 0 0">Auto-generated, sequential.</p>
                    @endunless
                </div>

                <div>
                    <label class="cl">Display Name <span class="text-danger-500">*</span></label>
                    <div style="position:relative">
                        <input type="text" name="display_name" id="roleDisplayName" required maxlength="100"
                               value="{{ old('display_name', $role->display_name ?? '') }}"
                               placeholder="e.g. Court Manager"
                               style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                               onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                               onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                        <i class="bi bi-person" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                    </div>
                </div>

                <div>
                    <label class="cl">Role Slug
                        @if($isEditMode)
                            <span style="font-size:.55rem;color:#9ca3af;background:#f3f4f6;padding:.1rem .35rem;border-radius:.2rem;margin-left:.2rem;font-weight:700">LOCKED</span>
                        @else
                            <span class="text-danger-500">*</span>
                        @endif
                    </label>
                    <div style="position:relative">
                        @if($isEditMode)
                            <input type="text" value="{{ $role->name }}" disabled
                                   style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #e5e7eb;border-radius:.625rem;background:#f9fafb;color:#9ca3af;outline:none;box-sizing:border-box;font-family:monospace;font-weight:700;cursor:not-allowed">
                        @else
                            <input type="text" name="name" id="roleSlug" required maxlength="50"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. court_manager" pattern="[a-zA-Z0-9_\-]+"
                                   style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;font-family:monospace;font-weight:700;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                   onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                   onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                        @endif
                        <i class="bi bi-{{ $isEditMode ? 'lock' : 'hash' }}" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:{{ $isEditMode ? '#d1d5db' : '#9ca3af' }};pointer-events:none"></i>
                    </div>
                    @unless($isEditMode)
                        <p style="font-size:.68rem;color:#9ca3af;margin:.3rem 0 0">Lowercase, underscores only. Auto-filled from name.</p>
                    @endunless
                </div>

                <div>
                    <label class="cl">Badge Color <span class="text-danger-500">*</span></label>
                    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
                        <input type="color" name="color" id="roleColor" value="{{ old('color', $role->color ?? '#528CBE') }}"
                               style="width:42px;height:36px;border-radius:.5rem;border:1.5px solid #e5e7eb;cursor:pointer;padding:2px">
                        @foreach(['#528CBE','#F0B43C','#10b981','#7C3AED','#ef4444','#0891B2','#6B7280','#f97316'] as $c)
                        <button type="button" onclick="document.getElementById('roleColor').value='{{ $c }}'"
                                style="width:24px;height:24px;border-radius:50%;background:{{ $c }};border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,.15);cursor:pointer;transition:transform .15s"
                                onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-neutral-100">
                <a href="{{ route('roles.index') }}"
                    class="px-6 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200 rounded-xl bg-white hover:bg-neutral-50 transition-all">
                    Cancel
                </a>
                <button type="submit"
                    class="flex items-center gap-2 px-7 py-2.5 text-white text-sm font-bold rounded-xl shadow transition-all hover:opacity-90"
                    style="background:{{ $isEditMode ? '#F0B43C' : '#528CBE' }}">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ $isEditMode ? 'Save Changes' : 'Save Role' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

@unless($isEditMode)
<script>
document.getElementById('roleDisplayName').addEventListener('input', function () {
    const slug = document.getElementById('roleSlug');
    if (!slug.dataset.edited) {
        slug.value = this.value.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
    }
});
document.getElementById('roleSlug').addEventListener('input', function () {
    this.dataset.edited = '1';
});
</script>
@endunless

@endsection
