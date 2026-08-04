@extends('admin.admin_master')
@section('page_title', 'Create Institution Admin')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full" style="max-width:640px">

    <div class="flex items-center gap-3 mb-6 mt-2">
        <a href="{{ route('institutions.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-500 hover:bg-neutral-50">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Create Institution Admin</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $institution->name }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium text-white" style="background:#ef4444">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100" style="padding:1.75rem">
        <form action="{{ route('institutions.admin.store', $institution) }}" method="POST">
            @csrf
            @php
            $fi = 'width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box';
            $lb = 'display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem';
            @endphp
            <div style="display:grid;gap:1rem;margin-bottom:1.5rem">
                <div>
                    <label style="{{ $lb }}">Full Name <span style="color:#ef4444">*</span></label>
                    <input type="text" name="name" required maxlength="150" value="{{ old('name') }}" style="{{ $fi }}">
                </div>
                <div>
                    <label style="{{ $lb }}">Email <span style="color:#ef4444">*</span></label>
                    <input type="email" name="email" required value="{{ old('email') }}" style="{{ $fi }}">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div>
                        <label style="{{ $lb }}">Password <span style="color:#ef4444">*</span></label>
                        <input type="password" name="password" required minlength="8" style="{{ $fi }}">
                    </div>
                    <div>
                        <label style="{{ $lb }}">Confirm Password <span style="color:#ef4444">*</span></label>
                        <input type="password" name="password_confirmation" required minlength="8" style="{{ $fi }}">
                    </div>
                </div>
            </div>
            <p class="text-xs text-neutral-400 mb-4">
                This account will be added to the "{{ $institution->name }} Admins" group with the
                Institution Admin role — scoped to managing staff, roles, and departments within
                {{ $institution->name }} only.
            </p>
            <div style="display:flex;align-items:center;justify-content:flex-end;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                <button type="submit"
                    style="display:flex;align-items:center;gap:.5rem;padding:.65rem 1.75rem;font-size:.85rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer;box-shadow:0 4px 14px rgba(82,140,190,.4)">
                    <i class="bi bi-person-plus-fill"></i> Create Institution Admin
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
