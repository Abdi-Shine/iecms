@extends('admin.admin_master')
@section('page_title', $closeCase ? 'Wax ka Badal Xidhitaanka' : 'Xidhitaanka Dacwadaha')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="p-4 sm:p-6 w-full">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">
                    {{ $closeCase ? 'Wax ka Badal Xidhitaanka' : 'Xidhitaanka Dacwadaha' }}
                </h1>
                @if($closeCase && $closeCase->status === 'Draft')
                    <p class="text-xs mt-1 flex items-center gap-1.5" style="color:#C07E15">
                        <i class="bi bi-pencil-square"></i> Muswaadda ayaa la wax ka bedelayaa
                    </p>
                @endif
            </div>
            <a href="{{ route('close_case.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200
                                                          rounded-xl bg-white hover:bg-neutral-50 transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i> Dib ugu Noqo
            </a>
        </div>

        {{-- Error messages --}}
        @if($errors->any())
            <div class="mb-5 rounded-xl px-4 py-3 flex items-start gap-3"
                style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2)">
                <i class="bi bi-exclamation-triangle-fill mt-0.5 flex-shrink-0" style="color:#DC2626"></i>
                <ul class="text-sm font-medium list-disc list-inside" style="color:#991b1b;margin:0;padding:0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('close_case.store') }}"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="civil_case_id" value="{{ $case->CRID }}">

            {{-- ── Section 1: Case Info ── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-5">
                <div class="px-6 py-4 border-b border-neutral-100" style="background:rgba(82,140,190,0.04)">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-info-circle text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Macluumaadka
                            Dacwadda</span>
                    </div>
                </div>
                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest mb-1">Lambarka Dacwadda
                        </p>
                        <p class="text-sm font-bold" style="color:#528CBE">{{ $case->FileNo }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest mb-1">Nooca Dacwadda</p>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                            style="background:rgba(240,180,60,0.12);color:#C07E15">{{ $case->CaseType }}</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest mb-1">Maxkamadda</p>
                        <p class="text-sm font-bold text-neutral-700">{{ $case->court->longName ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest mb-1">Garsoore Madax</p>
                        <p class="text-sm font-bold text-neutral-700">
                            @if($judge && $judge !== '—')
                                {{ $judge }}
                            @else
                                <span class="text-neutral-400 font-normal text-xs">Aan la xukumin</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Section 2: Judgment Details ── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-5">
                <div class="px-6 py-4 border-b border-neutral-100" style="background:rgba(82,140,190,0.04)">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-gavel text-sm" style="color:#528CBE"></i>
                        <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Faahfaahinta
                            Xukunka</span>
                    </div>
                </div>
                <div class="px-6 py-5 flex flex-col gap-5">

                    {{-- Type + Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                                Nooca Oodista <span style="color:#DC2626">*</span>
                            </label>
                            <div style="position:relative">
                                <select name="judgment_type"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="">— Dooro —</option>
                                    @foreach($caseStatus as $s)
                                        <option value="{{ $s->name }}" {{ old('judgment_type', $closeCase->judgment_type ?? '') === $s->name ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down"
                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.75rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                                Taariikhda Go,aanka <span style="color:#DC2626">*</span>
                            </label>
                            <div style="position:relative">
                                <input type="date" name="judgment_date"
                                    value="{{ old('judgment_date', $closeCase?->judgment_date ? \Carbon\Carbon::parse($closeCase->judgment_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-calendar3"
                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Orders (Rich Text) --}}
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Gabagabada Go,aanka
                            <span style="font-size:.68rem;font-weight:500;color:#9ca3af;text-transform:none;letter-spacing:0;margin-left:.3rem">(RICH TEXT EDITOR — HTML SUPPORTED)</span>
                        </label>
                        <textarea name="decision_body" id="ta_orders" style="display:none">{{ old('decision_body', $closeCase->decision_body ?? '') }}</textarea>
                        <div id="qe_orders"></div>
                        <div class="ql-word-count" id="wc_orders">0 ereyood</div>
                    </div>

                </div>
            </div>

            {{-- ── Form Actions ── --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:1.5rem">
                <a href="{{ route('close_case.index') }}" style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;
                                                              border-radius:.625rem;background:white;cursor:pointer;text-decoration:none;display:inline-flex;
                                                              align-items:center;gap:.4rem;transition:background .15s"
                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                    <i class="bi bi-x-lg" style="font-size:.75rem"></i> Jooji
                </a>
                <div style="display:flex;gap:.75rem">
                    <button type="submit" name="status" value="Draft"
                        style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.5rem;font-size:.85rem;font-weight:700;
                                                                   color:#528CBE;background:white;border:1.5px solid #528CBE;border-radius:.625rem;cursor:pointer;transition:opacity .15s"
                        onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                        <i class="bi bi-floppy"></i>
                        {{ $closeCase ? 'Cusboonaysii Muswaadda' : 'Keydi Muswaadda' }}
                    </button>
                    <button type="submit" name="status" value="Submitted"
                        style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;
                                                                   color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer;
                                             `                      box-shadow:0 4px 14px rgba(82,140,190,.4);transition:opacity .15s" onmouseover="this.style.opacity='.88'"
                        onmouseout="this.style.opacity='1'">
                        <i class="bi bi-send"></i>
                        {{ $closeCase ? 'Cusboonaysii & Gudbi' : 'Gudbi Go,aanka' }}
                    </button>
                </div>
            </div>

        </form>

    </div>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    var ta = document.getElementById('ta_orders');
    var wc = document.getElementById('wc_orders');

    var q = new Quill('#qe_orders', {
        theme: 'snow',
        placeholder: 'Geli amarrada maxkamaddu bixisay...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ header: [1, 2, 3, false] }],
                [{ align: [] }],
                [{ list: 'bullet' }, { list: 'ordered' }],
                [{ indent: '-1' }, { indent: '+1' }],
                ['clean']
            ]
        }
    });

    if (ta && ta.value.trim()) q.root.innerHTML = ta.value;

    function updateWC() {
        var words = q.getText().trim().split(/\s+/).filter(Boolean).length;
        if (wc) wc.textContent = words + ' ereyood';
    }
    updateWC();

    q.on('text-change', function () {
        if (ta) ta.value = q.root.innerHTML === '<p><br></p>' ? '' : q.root.innerHTML;
        updateWC();
    });

    document.querySelector('form').addEventListener('submit', function () {
        if (ta) ta.value = q.root.innerHTML === '<p><br></p>' ? '' : q.root.innerHTML;
    });
})();
</script>

@endsection