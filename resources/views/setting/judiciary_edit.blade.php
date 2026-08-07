@extends('admin.admin_master')
@section('page_title', 'Edit Judiciary Registration')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Edit Judiciary Registration</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $court->shortName }} — {{ $court->longName }}</p>
        </div>
        <a href="{{ route('court.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200
                   text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        {{-- Header Bar --}}
        <div class="flex items-center gap-3 px-6 py-4" style="background:#F0B43C">
            <div style="width:38px;height:38px;background:rgba(255,255,255,0.15);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                <i class="bi bi-pencil-square" style="color:white;font-size:1rem"></i>
            </div>
            <h2 style="color:white;font-size:1rem;font-weight:700;margin:0">Update Court Details</h2>
        </div>

        <div class="p-6" x-data="{ logoFile: 'No file chosen', stampFile: 'No file chosen', lhFile: 'No file chosen' }">
            <form action="{{ route('court.update', $court->CAI) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Upload row: Logo + Stamp + Letterhead --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.5rem">

                    {{-- Logo Upload --}}
                    <div>
                        <label for="edit_logo"
                            style="display:block;border:2px dashed #d1d5db;border-radius:.875rem;padding:1.25rem;text-align:center;cursor:pointer;background:#fafafa;transition:all .15s;height:100%;box-sizing:border-box"
                            onmouseover="this.style.borderColor='#F0B43C';this.style.background='#fffbf0'"
                            onmouseout="this.style.borderColor='#d1d5db';this.style.background='#fafafa'">
                            <i class="bi bi-image" style="font-size:1.4rem;color:#9ca3af;display:block;margin-bottom:.4rem"></i>
                            <p style="font-size:.7rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;margin:0"
                                x-text="logoFile === 'No file chosen' ? 'UPDATE LOGO' : logoFile"></p>
                            <p style="font-size:.6rem;color:#9ca3af;margin:.25rem 0 0">Leave empty to keep current</p>
                            @if($court->logo)
                                <img src="{{ asset('storage/'.$court->logo) }}" style="height:40px;margin:.5rem auto 0;object-fit:contain;display:block">
                            @endif
                        </label>
                        <input type="file" id="edit_logo" name="logo" accept="image/*" class="sr-only"
                            @change="logoFile = $event.target.files[0]?.name || 'No file chosen'">
                    </div>

                    {{-- Stamp Upload --}}
                    <div>
                        <label for="edit_stamp"
                            style="display:block;border:2px dashed #d1d5db;border-radius:.875rem;padding:1.25rem;text-align:center;cursor:pointer;background:#fafafa;transition:all .15s;height:100%;box-sizing:border-box"
                            onmouseover="this.style.borderColor='#528CBE';this.style.background='#f0f6fc'"
                            onmouseout="this.style.borderColor='#d1d5db';this.style.background='#fafafa'">
                            <i class="bi bi-patch-check" style="font-size:1.4rem;color:#9ca3af;display:block;margin-bottom:.4rem"></i>
                            <p style="font-size:.7rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;margin:0"
                                x-text="stampFile === 'No file chosen' ? 'UPLOAD STAMP' : stampFile"></p>
                            <p style="font-size:.6rem;color:#9ca3af;margin:.25rem 0 0">PNG or JPG — institution seal / stamp</p>
                            @if($court->stamp)
                                <img src="{{ asset('storage/'.$court->stamp) }}" style="height:50px;margin:.5rem auto 0;object-fit:contain;display:block">
                            @endif
                        </label>
                        <input type="file" id="edit_stamp" name="stamp" accept="image/*" class="sr-only"
                            @change="stampFile = $event.target.files[0]?.name || 'No file chosen'">
                    </div>

                    {{-- Letterhead Upload (PDF or Image) --}}
                    <div>
                        <label for="edit_letterhead"
                            id="lh-label"
                            style="display:block;border:2px dashed #528CBE;border-radius:.875rem;padding:1.25rem;text-align:center;cursor:pointer;background:#f0f6fc;transition:all .15s;box-sizing:border-box"
                            onmouseover="this.style.borderColor='#1a4a82';this.style.background='#e8f0fa'"
                            onmouseout="this.style.borderColor='#528CBE';this.style.background='#f0f6fc'">
                            <i id="lh-icon" class="bi bi-file-earmark-image" style="font-size:1.4rem;color:#528CBE;display:block;margin-bottom:.4rem"></i>
                            <p id="lh-label-text" style="font-size:.7rem;font-weight:800;color:#1a4a82;text-transform:uppercase;letter-spacing:.08em;margin:0">
                                UPLOAD LETTERHEAD
                            </p>
                            <p style="font-size:.6rem;color:#528CBE;margin:.25rem 0 0">PDF, PNG or JPG — replaces HTML header in all documents</p>
                            @if($court->letterhead)
                                <img src="{{ asset('storage/'.$court->letterhead) }}" style="max-height:50px;margin:.5rem auto 0;object-fit:contain;display:block;max-width:100%">
                            @endif
                        </label>
                        <input type="file" id="edit_letterhead" name="letterhead" accept=".pdf,image/png,image/jpeg,image/webp" class="sr-only">
                        {{-- Hidden field receives the PNG converted from PDF --}}
                        <input type="hidden" id="letterhead_data" name="letterhead_data">
                        {{-- Preview canvas (hidden until PDF selected) --}}
                        <canvas id="lh-canvas" style="display:none;max-width:100%;margin:.5rem auto 0;border-radius:.5rem;border:1px solid #e5e7eb"></canvas>
                    </div>

                </div>

                {{-- Row 1: Code (locked) + Short Name + Long Name --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1.6fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Court Code
                            <span style="font-size:.55rem;color:#9ca3af;background:#f3f4f6;padding:.1rem .35rem;border-radius:.2rem;margin-left:.2rem;font-weight:700">LOCKED</span>
                        </label>
                        <div style="position:relative">
                            <input type="text" value="{{ $court->courtcode }}" disabled
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #e5e7eb;border-radius:.625rem;background:#f9fafb;color:#9ca3af;outline:none;box-sizing:border-box;font-family:monospace;font-weight:700;cursor:not-allowed">
                            <i class="bi bi-lock" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#d1d5db;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Short Name <span style="color:#ef4444">*</span>
                        </label>
                        <div style="position:relative">
                            <input type="text" name="shortName" required maxlength="20"
                                value="{{ old('shortName', $court->shortName) }}"
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
                                value="{{ old('longName', $court->longName) }}"
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
                                value="{{ old('arabic_name', $court->arabic_name) }}"
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
                                    <option value="{{ $ct->name }}"
                                        {{ old('type', $court->type) == $ct->name ? 'selected' : '' }}>
                                        {{ $ct->name }}
                                    </option>
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
                                <option value="active"   {{ old('status', $court->status) == 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $court->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                </div>

                {{-- Row 3: Telephone + Email + Website + Address --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1.4fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Telephone</label>
                        <div style="position:relative">
                            <input type="text" name="telephone" inputmode="numeric"
                                value="{{ old('telephone', $court->telephone) }}"
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
                            <input type="email" name="email"
                                value="{{ old('email', $court->email) }}"
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-envelope" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Website</label>
                        <div style="position:relative">
                            <input type="text" name="website" placeholder="www.iecms.gov.so"
                                value="{{ old('website', $court->website) }}"
                                style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-globe" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Address</label>
                        <div style="position:relative">
                            <input type="text" name="address"
                                value="{{ old('address', $court->address) }}"
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
                    <textarea name="remarks" rows="3"
                        style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;resize:none;transition:border-color .15s,box-shadow .15s"
                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">{{ old('remarks', $court->remarks) }}</textarea>
                </div>

                {{-- Footer --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                    <a href="{{ route('court.index') }}"
                        style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;text-decoration:none;transition:background .15s"
                        onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        Cancel
                    </a>
                    <button type="submit"
                        style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;background:#F0B43C;border:none;border-radius:.625rem;cursor:pointer;box-shadow:0 4px 14px rgba(240,180,60,.35);transition:opacity .15s"
                        onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                        <i class="bi bi-check-circle-fill"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- PDF.js: converts PDF letterhead to PNG client-side before upload --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
(function () {
    if (typeof pdfjsLib === 'undefined') return;
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const input   = document.getElementById('edit_letterhead');
    const hidden  = document.getElementById('letterhead_data');
    const canvas  = document.getElementById('lh-canvas');
    const label   = document.getElementById('lh-label-text');
    const icon    = document.getElementById('lh-icon');

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        label.textContent = file.name;

        if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) {
            icon.className = 'bi bi-file-earmark-pdf';
            icon.style.color = '#dc2626';
            label.textContent = 'Converting PDF…';

            const reader = new FileReader();
            reader.onload = function (e) {
                pdfjsLib.getDocument({ data: e.target.result }).promise.then(function (pdf) {
                    pdf.getPage(1).then(function (page) {
                        const scale    = 2;
                        const viewport = page.getViewport({ scale });
                        canvas.width   = viewport.width;
                        canvas.height  = viewport.height;
                        canvas.style.display = 'block';

                        page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise.then(function () {
                            // Crop to top 22% of the page — captures the letterhead header only
                            var cropH = Math.floor(canvas.height * 0.22);
                            var cropped = document.createElement('canvas');
                            cropped.width  = canvas.width;
                            cropped.height = cropH;
                            cropped.getContext('2d').drawImage(canvas, 0, 0, canvas.width, cropH, 0, 0, canvas.width, cropH);

                            // Show cropped preview
                            canvas.height = cropH;
                            canvas.getContext('2d').drawImage(cropped, 0, 0);
                            canvas.style.display = 'block';

                            hidden.value = cropped.toDataURL('image/png');
                            input.value  = '';
                            icon.className   = 'bi bi-check-circle-fill';
                            icon.style.color = '#16a34a';
                            label.textContent = 'Header cropped from PDF — ready to save';
                        });
                    });
                }).catch(function () {
                    label.textContent = 'PDF error — try another file';
                    icon.className = 'bi bi-x-circle-fill';
                    icon.style.color = '#dc2626';
                });
            };
            reader.readAsArrayBuffer(file);

        } else {
            // Regular image — show preview directly
            canvas.style.display = 'block';
            const ctx = canvas.getContext('2d');
            const img = new Image();
            img.onload = function () {
                canvas.width  = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);
                hidden.value = '';   // let the file input handle the upload
                icon.className = 'bi bi-check-circle-fill';
                icon.style.color = '#16a34a';
            };
            img.src = URL.createObjectURL(file);
        }
    });
})();
</script>
@endsection
