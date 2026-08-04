@extends('admin.admin_master')
@section('page_title', 'Lawyer Profile')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('lawyer.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl border border-neutral-200 text-neutral-400 transition-all"
                onmouseover="this.style.borderColor='#059669';this.style.color='#059669'"
                onmouseout="this.style.borderColor='#e5e7eb';this.style.color=''">
                <i class="bi bi-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Lawyer Profile</h1>
                <p class="text-sm text-neutral-500 mt-0.5">{{ $lawyer->LawyerName }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('lawyer.edit', $lawyer->LRID) }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border transition-all hover:opacity-90"
                style="border-color:#528CBE;color:#528CBE;background:rgba(82,140,190,0.06);text-decoration:none">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            <span class="text-xs font-semibold px-3 py-1.5 rounded-full"
                style="background:rgba(5,150,105,0.1);color:#059669">
                <i class="bi bi-eye me-1"></i> Viewing Record
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left: Photo & Status Card --}}
        <div class="flex flex-col gap-5">

            <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 flex flex-col items-center text-center">
                @if($lawyer->photo)
                    <img src="{{ Storage::url($lawyer->photo) }}" alt="{{ $lawyer->LawyerName }}"
                        class="w-28 h-28 rounded-full object-cover border-4 shadow-md mb-4"
                        style="border-color:rgba(82,140,190,0.3)">
                @else
                    <div class="w-28 h-28 rounded-full flex items-center justify-center text-white text-4xl font-black shadow-md mb-4"
                        style="background:linear-gradient(135deg,#528CBE,#3a6fa0)">
                        {{ strtoupper(substr($lawyer->LawyerName, 0, 1)) }}
                    </div>
                @endif
                <h2 class="text-lg font-black text-neutral-800">{{ $lawyer->LawyerName }}</h2>
                <p class="text-sm text-neutral-500 mt-0.5">{{ $lawyer->Position ?? '—' }}</p>
                <div class="mt-3">
                    @if($lawyer->status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                            style="background:rgba(16,185,129,0.1);color:#059669">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                            style="background:rgba(156,163,175,0.15);color:#6B7280">
                            <span class="w-2 h-2 rounded-full bg-neutral-400 inline-block"></span> Inactive
                        </span>
                    @endif
                </div>
            </div>

            {{-- Audit Info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-5">
                <p class="text-xs font-black uppercase tracking-widest text-neutral-400 mb-3">Record Info</p>
                <div class="space-y-2.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-400 font-medium">Added By</span>
                        <span class="font-semibold text-neutral-700">{{ $lawyer->addedBy ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-400 font-medium">Added Date</span>
                        <span class="font-semibold text-neutral-700">{{ $lawyer->addedDate ? \Carbon\Carbon::parse($lawyer->addedDate)->format('d/m/Y') : '—' }}</span>
                    </div>
                    @if($lawyer->updatedBy)
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-400 font-medium">Updated By</span>
                        <span class="font-semibold text-neutral-700">{{ $lawyer->updatedBy }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-400 font-medium">Updated Date</span>
                        <span class="font-semibold text-neutral-700">{{ $lawyer->updatedDate ? \Carbon\Carbon::parse($lawyer->updatedDate)->format('d/m/Y') : '—' }}</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right: Details Card --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

                <div class="flex items-center gap-3 px-6 py-4 border-b border-neutral-100"
                    style="background:rgba(5,150,105,0.04)">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#059669">
                        <i class="bi bi-person-lines-fill text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-neutral-700">Lawyer Details</p>
                        <p class="text-xs text-neutral-400">Full profile information</p>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    @php
                    $field = 'display:flex;flex-direction:column;gap:.25rem;padding:1rem;border-radius:.75rem;background:#f9fafb;border:1px solid #f3f4f6';
                    $lbl   = 'font-size:.65rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em';
                    $val   = 'font-size:.95rem;font-weight:600;color:#111827';
                    @endphp

                    <div style="{{ $field }}">
                        <span style="{{ $lbl }}"><i class="bi bi-card-text me-1"></i> License ID</span>
                        <span style="{{ $val }}">{{ $lawyer->LID ?? '—' }}</span>
                    </div>

                    <div style="{{ $field }}">
                        <span style="{{ $lbl }}"><i class="bi bi-gender-ambiguous me-1"></i> Gender</span>
                        <span style="{{ $val }}">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $lawyer->Gender === 'Male' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-500' }}">
                                {{ $lawyer->Gender }}
                            </span>
                        </span>
                    </div>

                    <div style="{{ $field }}">
                        <span style="{{ $lbl }}"><i class="bi bi-calendar3 me-1"></i> Date of Birth</span>
                        <span style="{{ $val }}">{{ $lawyer->DOB ? \Carbon\Carbon::parse($lawyer->DOB)->format('d/m/Y') : '—' }}</span>
                    </div>

                    <div style="{{ $field }}">
                        <span style="{{ $lbl }}"><i class="bi bi-geo-alt me-1"></i> Place of Birth</span>
                        <span style="{{ $val }}">{{ $lawyer->POB ?? '—' }}</span>
                    </div>

                    <div style="{{ $field }}">
                        <span style="{{ $lbl }}"><i class="bi bi-telephone me-1"></i> Phone Number</span>
                        <span style="{{ $val }}">{{ $lawyer->Phone ?? '—' }}</span>
                    </div>

                    <div style="{{ $field }}">
                        <span style="{{ $lbl }}"><i class="bi bi-envelope me-1"></i> Email Address</span>
                        <span style="{{ $val }}">{{ $lawyer->Email ?? '—' }}</span>
                    </div>

                    <div style="{{ $field }}">
                        <span style="{{ $lbl }}"><i class="bi bi-building me-1"></i> Court</span>
                        <span style="{{ $val }}">{{ $lawyer->court->longName ?? $lawyer->CourtID ?? '—' }}</span>
                    </div>

                    <div style="{{ $field }}">
                        <span style="{{ $lbl }}"><i class="bi bi-briefcase me-1"></i> Position</span>
                        <span style="{{ $val }}">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                style="background:rgba(240,180,60,0.12);color:#C07E15">
                                {{ $lawyer->Position ?? '—' }}
                            </span>
                        </span>
                    </div>

                    <div style="{{ $field }}">
                        <span style="{{ $lbl }}"><i class="bi bi-award me-1"></i> Grade</span>
                        <span style="{{ $val }}">{{ $lawyer->Grade ?? '—' }}</span>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>
@endsection
