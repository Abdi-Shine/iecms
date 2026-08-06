@extends('admin.admin_master')
@section('page_title', 'Register Lawyer')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('lawyer.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl border border-neutral-200 text-neutral-400 transition-all"
                onmouseover="this.style.borderColor='#528CBE';this.style.color='#528CBE'"
                onmouseout="this.style.borderColor='#e5e7eb';this.style.color=''">
                <i class="bi bi-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Register Lawyer</h1>
                <p class="text-sm text-neutral-500 mt-0.5">Add a new lawyer to the registry</p>
            </div>
        </div>
        <span class="text-xs font-semibold px-3 py-1.5 rounded-full"
            style="background:rgba(82,140,190,0.1);color:#528CBE">
            <i class="bi bi-people me-1"></i> Lawyer Registry
        </span>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="mb-5 px-4 py-3 rounded-xl text-sm"
            style="background:rgba(220,38,38,0.07);border:1px solid rgba(220,38,38,0.2);color:#dc2626">
            <p class="font-bold mb-1 flex items-center gap-2"><i class="bi bi-exclamation-circle-fill"></i> Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-neutral-100"
            style="background:rgba(82,140,190,0.04)">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#528CBE">
                <i class="bi bi-person-plus-fill text-white text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-neutral-700">Lawyer Information</p>
                <p class="text-xs text-neutral-400">Fields marked <span style="color:#ef4444">*</span> are required</p>
            </div>
        </div>

        <form action="{{ route('lawyer.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @php
            $i  = 'width:100%;padding:.7rem 2.4rem .7rem .9rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s';
            $s  = 'width:100%;padding:.7rem 2.4rem .7rem .9rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s';
            $l  = 'display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.5rem';
            $fo = "this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'";
            $bl = "this.style.borderColor='#d1d5db';this.style.boxShadow='none'";
            $ic = 'position:absolute;right:.75rem;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none';
            $regions   = ['Awdal','Bakool','Banaadir','Bari','Bay','Galguduud','Gedo','Hiiraan','Jubbada Dhexe','Jubbada Hoose','Mudug','Nugaal','Sanaag','Shabeellaha Dhexe','Shabeellaha Hoose','Sool','Togdheer','Woqooyi Galbeed'];
            @endphp

            <div class="px-6 py-6" style="display:flex;flex-direction:column;gap:1.5rem">

                {{-- Row 1: License ID | Full Name | Gender --}}
                <div style="display:grid;grid-template-columns:1fr 1.5fr 1fr;gap:1.25rem">
                    <div>
                        <label style="{{ $l }}">License ID</label>
                        <div style="position:relative">
                            <input type="text" name="LID" value="{{ old('LID') }}" placeholder="e.g. LS-2024-001"
                                style="{{ $i }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                            <i class="bi bi-card-text text-sm" style="{{ $ic }}"></i>
                        </div>
                    </div>
                    <div>
                        <label style="{{ $l }}">Full Name <span style="color:#ef4444">*</span></label>
                        <div style="position:relative">
                            <input type="text" name="LawyerName" value="{{ old('LawyerName') }}" required
                                placeholder="Enter full name" style="{{ $i }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                            <i class="bi bi-person text-sm" style="{{ $ic }}"></i>
                        </div>
                    </div>
                    <div>
                        <label style="{{ $l }}">Gender <span style="color:#ef4444">*</span></label>
                        <div style="position:relative">
                            <select name="Gender" required style="{{ $s }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                                <option value="">Select</option>
                                <option value="Male"   {{ old('Gender') == 'Male'   ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('Gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            <i class="bi bi-chevron-down" style="{{ $ic }};font-size:.65rem"></i>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Date of Birth | Place of Birth | Phone Number --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem">
                    <div>
                        <label style="{{ $l }}">Date of Birth</label>
                        <div style="position:relative">
                            <input type="date" name="DOB" value="{{ old('DOB') }}"
                                style="{{ $i }};cursor:pointer" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                            <i class="bi bi-calendar3 text-sm" style="{{ $ic }}"></i>
                        </div>
                    </div>
                    <div>
                        <label style="{{ $l }}">Place of Birth</label>
                        <div style="position:relative">
                            <select name="POB" style="{{ $s }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                                <option value="">Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region }}" {{ old('POB') == $region ? 'selected' : '' }}>{{ $region }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down" style="{{ $ic }};font-size:.65rem"></i>
                        </div>
                    </div>
                    <div>
                        <label style="{{ $l }}">Phone Number</label>
                        <div style="position:relative">
                            <input type="text" name="Phone" value="{{ old('Phone') }}" placeholder="+252 XXX XXX XXX" inputmode="numeric"
                                oninput="this.value=this.value.replace(/\D/g,'')"
                                style="{{ $i }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                            <i class="bi bi-telephone text-sm" style="{{ $ic }}"></i>
                        </div>
                    </div>
                </div>

                {{-- Row 3: Email Address | Court | Position | Grade --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1.25rem">
                    <div>
                        <label style="{{ $l }}">Email Address</label>
                        <div style="position:relative">
                            <input type="email" name="Email" value="{{ old('Email') }}" placeholder="lawyer@example.com"
                                style="{{ $i }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                            <i class="bi bi-envelope text-sm" style="{{ $ic }}"></i>
                        </div>
                    </div>
                    <div>
                        <label style="{{ $l }}">Court <span style="color:#ef4444">*</span></label>
                        <div style="position:relative">
                            <select name="CourtID" required style="{{ $s }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                                <option value="">Select Court</option>
                                @foreach($courts as $court)
                                    <option value="{{ $court->courtcode }}" {{ old('CourtID') == $court->courtcode ? 'selected' : '' }}>
                                        {{ $court->longName }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down" style="{{ $ic }};font-size:.65rem"></i>
                        </div>
                    </div>
                    <div>
                        <label style="{{ $l }}">Position <span style="color:#ef4444">*</span></label>
                        <div style="position:relative">
                            <select name="Position" required style="{{ $s }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                                <option value="">Select Position</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos }}" {{ old('Position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down" style="{{ $ic }};font-size:.65rem"></i>
                        </div>
                    </div>
                    <div>
                        <label style="{{ $l }}">Grade</label>
                        <div style="position:relative">
                            <select name="Grade" style="{{ $s }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                                <option value="">Select Grade</option>
                                <option value="Darajada Koobaad"  {{ old('Grade') == 'Darajada Koobaad'  ? 'selected' : '' }}>Darajada Koobaad</option>
                                <option value="Darajada Labaad"   {{ old('Grade') == 'Darajada Labaad'   ? 'selected' : '' }}>Darajada Labaad</option>
                                <option value="Darajada Sadaxaad" {{ old('Grade') == 'Darajada Sadaxaad' ? 'selected' : '' }}>Darajada Sadaxaad</option>
                            </select>
                            <i class="bi bi-chevron-down" style="{{ $ic }};font-size:.65rem"></i>
                        </div>
                    </div>
                </div>

                {{-- Row 4: Status | Photo --}}
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:1.25rem">
                    <div>
                        <label style="{{ $l }}">Status <span style="color:#ef4444">*</span></label>
                        <div style="position:relative">
                            <select name="status" required style="{{ $s }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                                <option value="active"   {{ old('status','active') == 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <i class="bi bi-chevron-down" style="{{ $ic }};font-size:.65rem"></i>
                        </div>
                    </div>
                    <div x-data="{ fileName: '' }">
                        <label style="{{ $l }}">Photo</label>
                        <label for="add_photo"
                            style="display:flex;align-items:center;gap:.6rem;height:44px;padding:0 1rem;border:1.5px dashed #d1d5db;border-radius:.625rem;background:#fafafa;cursor:pointer;transition:border-color .15s,background .15s"
                            onmouseover="this.style.borderColor='#528CBE';this.style.background='rgba(82,140,190,.04)'"
                            onmouseout="this.style.borderColor='#d1d5db';this.style.background='#fafafa'">
                            <i class="bi bi-cloud-arrow-up" style="color:#9ca3af;font-size:1rem;flex-shrink:0"></i>
                            <span x-text="fileName || 'Click to choose a photo'"
                                style="font-size:.8rem;font-weight:600;color:#9ca3af;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
                        </label>
                        <input type="file" id="add_photo" name="photo" accept="image/*" class="sr-only"
                            @change="fileName = $event.target.files[0]?.name || ''">
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-neutral-100 flex items-center justify-between"
                style="background:rgba(249,250,251,0.8)">
                <a href="{{ route('lawyer.index') }}"
                    style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.5rem;font-size:.8rem;font-weight:700;color:#6b7280;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;text-decoration:none;text-transform:uppercase;letter-spacing:.04em;transition:background .15s"
                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="submit"
                    style="display:inline-flex;align-items:center;gap:.5rem;padding:.65rem 2rem;font-size:.85rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer;text-transform:uppercase;letter-spacing:.04em;box-shadow:0 4px 14px rgba(82,140,190,.35);transition:opacity .15s"
                    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                    <i class="bi bi-check-circle-fill"></i> Register Lawyer
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
