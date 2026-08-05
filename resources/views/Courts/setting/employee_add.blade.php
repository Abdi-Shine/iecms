@extends('admin.admin_master')
@section('page_title', 'Register New Staff Member')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Register New Staff Member</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Fill in all required fields to add a staff member</p>
        </div>
        <a href="{{ route('employee.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200
                   text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">

        {{-- Header --}}
        <div style="background:#528CBE;padding:1.5rem 2rem;display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:1rem">
                <div style="width:48px;height:48px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-person-plus-fill" style="font-size:1.4rem;color:white"></i>
                </div>
                <div>
                    <h2 style="font-size:1.15rem;font-weight:800;color:white;margin:0;letter-spacing:-.01em">Staff Details</h2>
                    <p style="font-size:.78rem;color:rgba(255,255,255,.6);margin:0;font-weight:500">Fill in all required fields to add a new member</p>
                </div>
            </div>
        </div>

        {{-- Form Body --}}
        <div style="padding:2rem 2.25rem">
            <form action="{{ route('employee.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Photo Upload --}}
                <div style="margin-bottom:2rem">
                    <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.6rem">
                        Upload Photo <span style="color:#ef4444">*</span>
                    </label>
                    <label for="photoUpload"
                           style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;height:110px;border:1.5px dashed #d1d5db;border-radius:.75rem;background:#fafafa;cursor:pointer;transition:border-color .15s,background .15s"
                           onmouseover="this.style.borderColor='#528CBE';this.style.background='rgba(82,140,190,.04)'"
                           onmouseout="this.style.borderColor='#d1d5db';this.style.background='#fafafa'">
                        <i class="bi bi-cloud-arrow-up" style="font-size:1.8rem;color:#9ca3af"></i>
                        <span style="font-size:.72rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em" id="photoLabel">Upload Image</span>
                    </label>
                    <input type="file" id="photoUpload" name="photo" class="hidden" accept="image/*"
                           onchange="document.getElementById('photoLabel').textContent = this.files[0]?.name || 'Upload Image'">
                </div>

                <div style="display:flex;flex-direction:column;gap:1.5rem">

                    {{-- Row 1: Employee ID | Full Name | Gender --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Employee ID <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="text" name="EmpID" value="{{ $nextEmpId }}" readonly tabindex="-1"
                                       style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#f3f4f6;color:#6b7280;outline:none;box-sizing:border-box;cursor:not-allowed">
                                <i class="bi bi-hash" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.85rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                            <p style="font-size:.7rem;color:#9ca3af;margin:.4rem 0 0">Auto-generated, sequential</p>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Full Name <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="text" name="EmpName" required placeholder="Enter full name"
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
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Phone | Email | Region --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Telephone <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="text" name="phone" placeholder="+252 XXX XXX XXX" inputmode="numeric"
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
                                <input type="email" name="email" required placeholder="name@iecms.gov.so"
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
                                        <option value="{{ $region->state_name }}">{{ $region->state_name }}</option>
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
                                <input type="date" name="DOB" required
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
                                <select name="courtID" required
                                        style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="">Select Court</option>
                                    @foreach($courts as $court)
                                        <option value="{{ $court->courtcode }}">{{ $court->longName }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Row 4: Role | Status | Registration Date --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Role <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <select name="Position" required
                                        style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Status <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <select name="status" required
                                        style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                                Registration Date <span style="color:#ef4444">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="date" name="Dates" required value="{{ date('Y-m-d') }}"
                                       style="width:100%;padding:.75rem 2.5rem .75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-calendar-check" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.85rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Form Actions --}}
                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
                    <a href="{{ route('employee.index') }}"
                       style="display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.75rem;font-size:.82rem;font-weight:800;color:#6b7280;border:1.5px solid #e5e7eb;border-radius:.75rem;background:white;text-decoration:none;text-transform:uppercase;letter-spacing:.05em;transition:all .15s"
                       onmouseover="this.style.background='#f9fafb';this.style.borderColor='#d1d5db'"
                       onmouseout="this.style.background='white';this.style.borderColor='#e5e7eb'">
                        Cancel
                    </a>
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:.6rem;padding:.75rem 2.25rem;font-size:.82rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.75rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em;box-shadow:0 4px 14px rgba(82,140,190,.35);transition:opacity .15s"
                            onmouseover="this.style.opacity='.88'"
                            onmouseout="this.style.opacity='1'">
                        <i class="bi bi-check-circle-fill"></i>
                        Register Staff Member
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

<script>
    const pobCitiesByRegion = @json($regions->mapWithKeys(fn($r) => [$r->state_name => $r->cities->pluck('city_name')]));

    function onPobRegionChange() {
        const region = document.getElementById('pobRegion').value;
        const districtSelect = document.getElementById('pobDistrict');
        const cities = pobCitiesByRegion[region] || [];
        districtSelect.innerHTML = '<option value="">Select District</option>';
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            districtSelect.appendChild(option);
        });
    }
</script>

@endsection
