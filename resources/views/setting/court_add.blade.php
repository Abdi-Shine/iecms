@extends('admin.admin_master')
@section('page_title', 'Judiciary Registration')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Judiciary Registration</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Fill in all required fields to add an institution</p>
        </div>
        <a href="{{ route('court.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200
                   text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        {{-- Header Bar --}}
        <div class="flex items-center gap-3 px-6 py-4" style="background:#528CBE">
            <div style="width:38px;height:38px;background:rgba(255,255,255,0.15);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                <i class="bi bi-building-add" style="color:white;font-size:1rem"></i>
            </div>
            <h2 style="color:white;font-size:1rem;font-weight:700;margin:0">Court Details</h2>
        </div>

        <div class="p-6" x-data="{ logoFile: 'No file chosen', stampFile: 'No file chosen', letterheadFile: 'No file chosen' }">
            <form action="{{ route('court.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Logo + Stamp + Letterhead Upload --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.5rem">
                    {{-- Logo --}}
                    <div>
                        <label for="add_logo"
                            style="display:block;border:2px dashed #d1d5db;border-radius:.875rem;padding:1.25rem;text-align:center;cursor:pointer;background:#fafafa;transition:all .15s"
                            onmouseover="this.style.borderColor='#528CBE';this.style.background='#f0f7ff'"
                            onmouseout="this.style.borderColor='#d1d5db';this.style.background='#fafafa'">
                            <i class="bi bi-image" style="font-size:1.4rem;color:#9ca3af;display:block;margin-bottom:.3rem"></i>
                            <p style="font-size:.65rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;margin:0"
                                x-text="logoFile === 'No file chosen' ? 'UPLOAD LOGO' : logoFile"></p>
                            <p style="font-size:.6rem;color:#9ca3af;margin:.25rem 0 0">PNG or JPG — institution logo</p>
                        </label>
                        <input type="file" id="add_logo" name="logo" accept="image/*" class="sr-only"
                            @change="logoFile = $event.target.files[0]?.name || 'No file chosen'">
                    </div>

                    {{-- Stamp --}}
                    <div>
                        <label for="add_stamp"
                            style="display:block;border:2px dashed #d1d5db;border-radius:.875rem;padding:1.25rem;text-align:center;cursor:pointer;background:#fafafa;transition:all .15s"
                            onmouseover="this.style.borderColor='#528CBE';this.style.background='#f0f7ff'"
                            onmouseout="this.style.borderColor='#d1d5db';this.style.background='#fafafa'">
                            <i class="bi bi-patch-check" style="font-size:1.4rem;color:#9ca3af;display:block;margin-bottom:.3rem"></i>
                            <p style="font-size:.65rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;margin:0"
                                x-text="stampFile === 'No file chosen' ? 'UPLOAD STAMP' : stampFile"></p>
                            <p style="font-size:.6rem;color:#9ca3af;margin:.25rem 0 0">PNG or JPG — institution seal / stamp</p>
                        </label>
                        <input type="file" id="add_stamp" name="stamp" accept="image/*" class="sr-only"
                            @change="stampFile = $event.target.files[0]?.name || 'No file chosen'">
                    </div>

                    {{-- Letterhead --}}
                    <div>
                        <label for="add_letterhead"
                            style="display:block;border:2px dashed #d1d5db;border-radius:.875rem;padding:1.25rem;text-align:center;cursor:pointer;background:#fafafa;transition:all .15s"
                            onmouseover="this.style.borderColor='#528CBE';this.style.background='#f0f7ff'"
                            onmouseout="this.style.borderColor='#d1d5db';this.style.background='#fafafa'">
                            <i class="bi bi-file-earmark-image" style="font-size:1.4rem;color:#9ca3af;display:block;margin-bottom:.3rem"></i>
                            <p style="font-size:.65rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;margin:0"
                                x-text="letterheadFile === 'No file chosen' ? 'UPLOAD LETTERHEAD' : letterheadFile"></p>
                            <p style="font-size:.6rem;color:#9ca3af;margin:.25rem 0 0">PNG, JPG or PDF — institution letterhead</p>
                        </label>
                        <input type="file" id="add_letterhead" name="letterhead" accept="image/*,application/pdf" class="sr-only"
                            @change="letterheadFile = $event.target.files[0]?.name || 'No file chosen'">
                    </div>
                </div>

                <input type="hidden" name="courtcode" value="{{ $nextCode }}">
                @error('courtcode')<p class="text-xs text-red-500 mt-1 mb-3">{{ $message }}</p>@enderror

                {{-- Row 1: Short Name + Long Name --}}
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Short Name <span style="color:#ef4444">*</span>
                        </label>
                        <div style="position:relative">
                            <input type="text" name="shortName" required maxlength="20" placeholder="e.g. SCO"
                                value="{{ old('shortName') }}"
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-building" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                        @error('shortName')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Long Name <span style="color:#ef4444">*</span>
                        </label>
                        <div style="position:relative">
                            <input type="text" name="longName" required maxlength="100"
                                placeholder="e.g. Supreme Court of Somalia"
                                value="{{ old('longName') }}"
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-building" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                        @error('longName')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Row 2: Arabic Name + Court Type + Status --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Arabic Name
                        </label>
                        <div style="position:relative">
                            <input type="text" name="arabic_name" maxlength="150"
                                placeholder="اسم المؤسسة"
                                value="{{ old('arabic_name') }}"
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s;direction:rtl;text-align:right"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Court Type <span style="color:#ef4444">*</span>
                        </label>
                        <div style="position:relative">
                            <select name="type" required
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <option value="">Select Type</option>
                                @foreach($courtTypes as $ct)
                                    <option value="{{ $ct->name }}" {{ old('type') == $ct->name ? 'selected' : '' }}>{{ $ct->name }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                        @error('type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Status</label>
                        <div style="position:relative">
                            <select name="status"
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <option value="active"   {{ old('status','active') == 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                </div>

                {{-- Row 3: Telephone + Email + Address --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Telephone</label>
                        <div style="position:relative">
                            <input type="text" name="telephone" placeholder="+252 XXX XXX XXX" inputmode="numeric"
                                value="{{ old('telephone') }}"
                                oninput="this.value=this.value.replace(/\D/g,'')"
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-telephone" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Email</label>
                        <div style="position:relative">
                            <input type="email" name="email" placeholder="info@iecms.gov.so"
                                value="{{ old('email') }}"
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-envelope" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Address</label>
                        <div style="position:relative">
                            <input type="text" name="address" placeholder="e.g. Mogadishu, KM4"
                                value="{{ old('address') }}"
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-geo-alt" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                </div>

                {{-- Remarks --}}
                <div style="margin-bottom:1.5rem">
                    <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Remarks</label>
                    <textarea name="remarks" rows="3" placeholder="Any additional notes…"
                        style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;resize:none;transition:border-color .15s,box-shadow .15s"
                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">{{ old('remarks') }}</textarea>
                </div>

                {{-- Footer --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                    <a href="{{ route('court.index') }}"
                        style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;text-decoration:none;transition:background .15s"
                        onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        Cancel
                    </a>
                    <button type="submit"
                        style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer;box-shadow:0 4px 14px rgba(82,140,190,.4);transition:opacity .15s"
                        onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                        <i class="bi bi-check-circle-fill"></i> Register Court
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
