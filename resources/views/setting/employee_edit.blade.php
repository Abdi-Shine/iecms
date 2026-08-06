@extends('admin.admin_master')
@section('admin_main_content')

<div class="p-6 sm:p-8 max-w-[1020px] mx-auto">

    {{-- Breadcrumb --}}
    <div class="mb-6 flex items-center gap-2 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
        <a href="{{ route('employee.index') }}" class="hover:text-[#F0B43C] transition-colors">Employee Management</a>
        <i class="bi bi-chevron-right text-[9px]"></i>
        <span style="color:#F0B43C">Edit Staff Profile</span>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">

        {{-- Header --}}
        <div style="background:#F0B43C;padding:1.5rem 2rem;display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:1rem">
                <div style="width:48px;height:48px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-pencil-square" style="font-size:1.4rem;color:white"></i>
                </div>
                <div>
                    <h2 style="font-size:1.15rem;font-weight:800;color:white;margin:0;letter-spacing:-.01em">Edit Staff Profile</h2>
                    <p style="font-size:.78rem;color:rgba(255,255,255,.6);margin:0;font-weight:500">
                        Updating records for: <span style="color:rgba(255,255,255,.9);font-weight:700">{{ $employee->EmpName }}</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('employee.index') }}"
               style="width:36px;height:36px;background:rgba(255,255,255,0.1);border-radius:.5rem;display:flex;align-items:center;justify-content:center;color:white;text-decoration:none;transition:background .15s"
               onmouseover="this.style.background='rgba(255,255,255,0.2)'"
               onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="bi bi-x-lg" style="font-size:.9rem"></i>
            </a>
        </div>

        {{-- Form Body --}}
        <div style="padding:2rem 2.25rem">
            <form action="{{ route('employee.update', $employee->AID) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Photo Upload --}}
                <div style="margin-bottom:2rem">
                    <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.6rem">
                        Update Photo
                    </label>
                    <div style="display:flex;align-items:center;gap:1rem">
                        <div style="width:52px;height:52px;border-radius:.625rem;overflow:hidden;border:1.5px solid #e5e7eb;flex-shrink:0">
                            <img src="{{ asset($employee->photo ?? 'uploads/employees/default.png') }}"
                                 style="width:100%;height:100%;object-fit:cover">
                        </div>
                        <label for="photo_up"
                               style="flex:1;display:flex;align-items:center;justify-content:center;gap:.5rem;height:52px;border:1.5px dashed #d1d5db;border-radius:.625rem;background:#fafafa;cursor:pointer;transition:border-color .15s,background .15s"
                               onmouseover="this.style.borderColor='#528CBE';this.style.background='rgba(82,140,190,.04)'"
                               onmouseout="this.style.borderColor='#d1d5db';this.style.background='#fafafa'">
                            <i class="bi bi-cloud-arrow-up" style="font-size:1.1rem;color:#9ca3af"></i>
                            <span style="font-size:.72rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em" id="photoLabel">Choose New Photo</span>
                        </label>
                        <input type="file" id="photo_up" name="photo" class="hidden" accept="image/*"
                               onchange="document.getElementById('photoLabel').textContent = this.files[0]?.name || 'Choose New Photo'">
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:1.5rem">

                    {{-- Row 1: Employee ID | Full Name | Gender --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Employee ID
                            </label>
                            <div style="position:relative">
                                <input type="text" value="{{ $employee->EmpID }}" readonly tabindex="-1"
                                       style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #e5e7eb;border-radius:.625rem;background:#f9fafb;color:#9ca3af;outline:none;box-sizing:border-box;cursor:not-allowed">
                                <i class="bi bi-lock" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.85rem;color:#d1d5db;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Full Name <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="text" name="EmpName" value="{{ $employee->EmpName }}" required
                                       style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-person" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.85rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Gender
                            </label>
                            <div style="position:relative">
                                <select name="gender"
                                        style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="Male" {{ $employee->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $employee->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Phone | Email | Place of Birth --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Telephone
                            </label>
                            <div style="position:relative">
                                <input type="text" name="phone" value="{{ $employee->phone }}" placeholder="+252 XXX XXX XXX" inputmode="numeric"
                                       oninput="this.value=this.value.replace(/\D/g,'')"
                                       style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-telephone" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.85rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Email <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="email" name="email" value="{{ $employee->email }}" required
                                       style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-envelope" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.85rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Region (Place of Birth) <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <select name="POB" id="pobRegion" required onchange="onPobRegionChange()"
                                        style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->state_name }}" {{ $employee->POB == $region->state_name ? 'selected' : '' }}>{{ $region->state_name }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: District | Date of Birth | Court Assignment --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                District (Place of Birth) <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <select name="district" id="pobDistrict" required
                                        style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="">Select District</option>
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Date of Birth <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="date" name="DOB" value="{{ optional($employee->DOB)->format('Y-m-d') }}" required
                                       style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-calendar3" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.85rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Court Assignment <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                @php $courts = \App\Models\Court::all(); @endphp
                                <select name="courtID" required
                                        style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    @foreach($courts as $court)
                                        <option value="{{ $court->courtcode }}" {{ $employee->courtID == $court->courtcode ? 'selected' : '' }}>{{ $court->longName }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Row 4: Role --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Role <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                @php $roles = \App\Models\Role::all(); @endphp
                                <select name="Position" required
                                        style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ $employee->Position == $role->name ? 'selected' : '' }}>{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Digital Signature --}}
                    <div style="border:1.5px solid #e5e7eb;border-radius:1rem;padding:1.25rem 1.5rem;background:#fafafa">

                        {{-- Header --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
                            <label style="font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em">
                                Digital Signature
                            </label>
                            @if($employee->signature)
                                <span style="font-size:.7rem;color:#10b981;font-weight:700;display:flex;align-items:center;gap:.35rem">
                                    <i class="bi bi-patch-check-fill"></i> Signature on file
                                </span>
                            @endif
                        </div>

                        {{-- Current preview --}}
                        @if($employee->signature)
                        <div style="margin-bottom:1rem;padding:.625rem;background:white;border:1.5px solid #d1d5db;border-radius:.625rem;display:flex;align-items:center;gap:.875rem">
                            <div style="width:150px;height:60px;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                                <img src="{{ asset($employee->signature) }}" style="max-width:100%;max-height:100%;object-fit:contain">
                            </div>
                            <div>
                                <p style="font-size:.72rem;font-weight:700;color:#374151;margin:0 0 .2rem">Current signature</p>
                                <p style="font-size:.7rem;color:#9ca3af;margin:0">Draw or upload below to replace it.</p>
                            </div>
                        </div>
                        @endif

                        {{-- Tabs --}}
                        <div style="display:flex;gap:0;border:1.5px solid #e5e7eb;border-radius:.625rem;overflow:hidden;margin-bottom:1rem;background:white">
                            <button type="button" id="sig-tab-draw" onclick="sigTab('draw')"
                                style="flex:1;padding:.55rem;font-size:.75rem;font-weight:700;border:none;cursor:pointer;background:#528CBE;color:white;display:flex;align-items:center;justify-content:center;gap:.4rem;transition:background .15s">
                                <i class="bi bi-pen"></i> Draw Signature
                            </button>
                            <button type="button" id="sig-tab-upload" onclick="sigTab('upload')"
                                style="flex:1;padding:.55rem;font-size:.75rem;font-weight:700;border:none;cursor:pointer;background:white;color:#6b7280;display:flex;align-items:center;justify-content:center;gap:.4rem;transition:background .15s">
                                <i class="bi bi-image"></i> Upload Image
                            </button>
                        </div>

                        {{-- Draw panel --}}
                        <div id="sig-panel-draw">
                            <canvas id="sig-canvas" width="460" height="110"
                                    style="display:block;width:100%;height:110px;border:1.5px dashed #d1d5db;border-radius:.5rem;background:white;cursor:crosshair;touch-action:none">
                            </canvas>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.5rem">
                                <span style="font-size:.72rem;color:#9ca3af"><i class="bi bi-mouse2"></i> Draw with mouse or touch</span>
                                <div style="display:flex;gap:.5rem">
                                    <button type="button" onclick="clearSigCanvas()"
                                        style="padding:.3rem .75rem;font-size:.72rem;font-weight:700;color:#6b7280;background:white;border:1.5px solid #e5e7eb;border-radius:.4rem;cursor:pointer">
                                        <i class="bi bi-eraser"></i> Clear
                                    </button>
                                    <button type="button" onclick="captureSig()"
                                        style="padding:.3rem .75rem;font-size:.72rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.4rem;cursor:pointer">
                                        <i class="bi bi-check2"></i> Use This
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Upload panel --}}
                        <div id="sig-panel-upload" style="display:none">
                            <label for="sig-upload-file"
                                   style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;height:110px;border:2px dashed #d1d5db;border-radius:.5rem;background:white;cursor:pointer;transition:border-color .15s"
                                   onmouseover="this.style.borderColor='#528CBE'" onmouseout="this.style.borderColor='#d1d5db'">
                                <i class="bi bi-cloud-arrow-up" style="font-size:1.75rem;color:#9ca3af"></i>
                                <span style="font-size:.78rem;font-weight:600;color:#6b7280">Click to choose image</span>
                                <span style="font-size:.7rem;color:#9ca3af">PNG, JPG, JPEG — white background recommended</span>
                            </label>
                            <input type="file" id="sig-upload-file" accept="image/png,image/jpeg,image/jpg"
                                   style="display:none" onchange="sigFileSelected(this)">
                            <div id="sig-upload-preview" style="display:none;margin-top:.5rem;padding:.5rem;background:white;border:1.5px solid #d1d5db;border-radius:.5rem;text-align:center">
                                <img id="sig-upload-img" style="max-height:70px;max-width:100%;object-fit:contain">
                            </div>
                        </div>

                        <p id="sig-captured-msg" style="display:none;font-size:.72rem;color:#10b981;font-weight:700;margin:.5rem 0 0">
                            <i class="bi bi-patch-check-fill"></i> Signature captured — will be saved with the form.
                        </p>
                        <input type="hidden" name="signature_data" id="sig-data">
                    </div>

                    {{-- Row 4: Status | Registration Date --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Status <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <select name="status" required
                                        style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Registration Date <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="date" name="Dates" value="{{ optional($employee->Dates)->format('Y-m-d') }}" required
                                       style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-calendar-check" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.85rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div></div>
                    </div>

                </div>

                {{-- Form Actions --}}
                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
                    <a href="{{ route('employee.index') }}"
                       style="display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.75rem;font-size:.82rem;font-weight:800;color:#6b7280;border:1.5px solid #e5e7eb;border-radius:.75rem;background:white;text-decoration:none;text-transform:uppercase;letter-spacing:.05em;transition:all .15s"
                       onmouseover="this.style.background='#f9fafb';this.style.borderColor='#d1d5db'"
                       onmouseout="this.style.background='white';this.style.borderColor='#e5e7eb'">
                        Discard Changes
                    </a>
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:.6rem;padding:.75rem 2.25rem;font-size:.82rem;font-weight:800;color:white;background:#F0B43C;border:none;border-radius:.75rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em;box-shadow:0 4px 14px rgba(240,180,60,.35);transition:opacity .15s"
                            onmouseover="this.style.opacity='.88'"
                            onmouseout="this.style.opacity='1'">
                        <i class="bi bi-cloud-check-fill"></i>
                        Save Updates
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var ctx, drawing = false;

    function getCanvas() { return document.getElementById('sig-canvas'); }
    function msg(show)   { document.getElementById('sig-captured-msg').style.display = show ? 'block' : 'none'; }

    function pos(e) {
        var c = getCanvas(), r = c.getBoundingClientRect();
        var src = e.touches ? e.touches[0] : e;
        return { x: (src.clientX - r.left) * (c.width / r.width), y: (src.clientY - r.top) * (c.height / r.height) };
    }

    (function init() {
        var c = getCanvas();
        if (!c) { setTimeout(init, 200); return; }
        ctx = c.getContext('2d');
        c.addEventListener('mousedown',  function(e) { drawing = true; var p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        c.addEventListener('mousemove',  function(e) { if (!drawing) return; var p = pos(e); ctx.lineTo(p.x, p.y); ctx.strokeStyle='#528CBE'; ctx.lineWidth=2.5; ctx.lineCap='round'; ctx.lineJoin='round'; ctx.stroke(); });
        c.addEventListener('mouseup',    function() { drawing = false; });
        c.addEventListener('mouseleave', function() { drawing = false; });
        c.addEventListener('touchstart', function(e) { e.preventDefault(); drawing=true; var p=pos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); }, { passive:false });
        c.addEventListener('touchmove',  function(e) { e.preventDefault(); if(!drawing)return; var p=pos(e); ctx.lineTo(p.x,p.y); ctx.strokeStyle='#528CBE'; ctx.lineWidth=2.5; ctx.lineCap='round'; ctx.lineJoin='round'; ctx.stroke(); }, { passive:false });
        c.addEventListener('touchend',   function(e) { e.preventDefault(); drawing=false; }, { passive:false });
    })();

    window.sigTab = function(tab) {
        var isDraw = tab === 'draw';
        document.getElementById('sig-panel-draw').style.display   = isDraw ? 'block' : 'none';
        document.getElementById('sig-panel-upload').style.display = isDraw ? 'none'  : 'block';
        document.getElementById('sig-tab-draw').style.background   = isDraw ? '#528CBE' : 'white';
        document.getElementById('sig-tab-draw').style.color        = isDraw ? 'white'   : '#6b7280';
        document.getElementById('sig-tab-upload').style.background = isDraw ? 'white'   : '#528CBE';
        document.getElementById('sig-tab-upload').style.color      = isDraw ? '#6b7280' : 'white';
        document.getElementById('sig-data').value = '';
        msg(false);
    };

    window.clearSigCanvas = function() {
        var c = getCanvas(); if (!c) return;
        c.getContext('2d').clearRect(0, 0, c.width, c.height);
        document.getElementById('sig-data').value = '';
        c.style.borderColor = '#d1d5db';
        msg(false);
    };

    window.captureSig = function() {
        var c = getCanvas(); if (!c) return;
        document.getElementById('sig-data').value = c.toDataURL('image/png');
        c.style.borderColor = '#10b981';
        msg(true);
    };

    window.sigFileSelected = function(input) {
        if (!input.files || !input.files[0]) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('sig-data').value = e.target.result;
            var preview = document.getElementById('sig-upload-preview');
            document.getElementById('sig-upload-img').src = e.target.result;
            preview.style.display = 'block';
            msg(true);
        };
        reader.readAsDataURL(input.files[0]);
    };
})();

const pobCitiesByRegion = @json($regions->mapWithKeys(fn($r) => [$r->state_name => $r->cities->pluck('city_name')]));
const pobCurrentDistrict = @json($employee->district);

function onPobRegionChange() {
    const region = document.getElementById('pobRegion').value;
    const districtSelect = document.getElementById('pobDistrict');
    const cities = pobCitiesByRegion[region] || [];
    const previousValue = districtSelect.value;
    districtSelect.innerHTML = '<option value="">Select District</option>';
    cities.forEach(city => {
        const option = document.createElement('option');
        option.value = city;
        option.textContent = city;
        if (city === previousValue) option.selected = true;
        districtSelect.appendChild(option);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    onPobRegionChange();
    if (pobCurrentDistrict) {
        document.getElementById('pobDistrict').value = pobCurrentDistrict;
    }
});
</script>

@endsection
