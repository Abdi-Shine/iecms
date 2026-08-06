@extends('admin.admin_master')
@section('page_title', $judgment ? 'Wax ka Badal Xukunka' : 'Ku Dar Xukun')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
    <style>
        .ql-container { font-family: inherit; font-size: .875rem; min-height: 220px; }
        .ql-toolbar { border-radius: 8px 8px 0 0; background: #f9fafb; border-color: #d1d5db !important; }
        .ql-container.ql-snow { border-radius: 0 0 8px 8px; border-color: #d1d5db !important; }
        .ql-editor { min-height: 220px; line-height: 1.7; }
        .ql-word-count { font-size: .72rem; color: #9ca3af; text-align: right; margin-top: .25rem; }

        .jdg-readonly {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: .5rem .75rem;
            font-size: .875rem;
            color: #374151;
            width: 100%;
            box-sizing: border-box;
            cursor: default;
        }

        .jdg-judge-prefix {
            font-size: .75rem;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: .25rem;
        }

    </style>
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-wrap">

        {{-- Flash messages --}}
        @if(session('success'))
            <div
                style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#065f46;border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div
                style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#991b1b;border-radius:8px;padding:12px 16px;margin-bottom:16px">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Khalad ayaa jira:</strong>
                </div>
                <ul style="margin:0;padding-left:20px">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Page Header --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem">
            <div>
                <h1 style="font-size:1.25rem;font-weight:700;color:#111827;margin:0 0 .25rem">
                    {{ $judgment ? 'Wax ka Badal Xukunka' : 'Ku Dar Xukun' }}
                </h1>
                <p style="font-size:.8125rem;color:#6b7280;margin:0">
                    Dacwada: <strong style="color:#374151">{{ $case->FileNo }}</strong>
                </p>
                @if($judgment && $judgment->status === 'Draft')
                    <p style="font-size:.75rem;color:#C07E15;margin:.3rem 0 0;display:flex;align-items:center;gap:.375rem">
                        <i class="bi bi-pencil-square"></i> Muswaadda ayaa la wax ka bedelayaa
                    </p>
                @endif
            </div>
            <a href="{{ route('appeal-criminal-judgments.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Dib ugu Noqo
            </a>
        </div>

        <form method="POST" action="{{ $judgment ? route('appeal-criminal-judgments.update', $judgment->id) : route('appeal-criminal-judgments.store') }}"
            enctype="multipart/form-data">
            @csrf
            @if($judgment) @method('PUT') @endif
            <input type="hidden" name="criminal_case_id" value="{{ $case->ACMID }}">

            {{-- Section 1: Case Information --}}
            <div class="sec-card">
                <div class="sec-hd">
                    <i class="bi bi-info-circle" style="color:#528CBE;font-size:.875rem"></i>
                    <span class="sec-title">Macluumaadka Dacwada</span>
                </div>
                <div class="sec-bd">
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem">
                        <div>
                            <label class="form-lbl">Lambarka Dacwada</label>
                            <div class="jdg-readonly">{{ $case->FileNo }}</div>
                        </div>
                        <div>
                            <label class="form-lbl">Nooca Dacwada</label>
                            <div class="jdg-readonly">{{ $case->CaseType }}</div>
                        </div>
                        <div>
                            <label class="form-lbl">Maxkamadda</label>
                            <div class="jdg-readonly">{{ $case->court->longName ?? '—' }}</div>
                        </div>
                        <div>
                            <label class="form-lbl">Garsoore</label>
                            <div class="jdg-readonly">
                                @if($judge && $judge !== '—')
                                    <span style="font-size:.7rem;color:#6b7280;font-weight:500">Garsoore Madax
                                        &nbsp;·&nbsp;</span>{{ $judge }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Judgment Details --}}
            <div class="sec-card">
                <div class="sec-hd">
                    <i class="bi bi-gavel" style="color:#528CBE;font-size:.875rem"></i>
                    <span class="sec-title">Faahfaahinta Xukunka</span>
                </div>
                <div class="sec-bd">

                    {{-- Type · Date · Time · Outcome in one row --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1rem">
                        <div>
                            <label class="form-lbl">Nooca Xukunka <span class="req">*</span></label>
                            <div class="inp-wrap">
                                <select name="judgment_type" class="form-sel">
                                    <option value="">— Dooro —</option>
                                    @foreach(['Go\'aan', 'Qaraar', 'Xukun'] as $type)
                                        <option value="{{ $type }}" {{ old('judgment_type', $judgment->judgment_type ?? '') === $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down inp-ico-xs"></i>
                            </div>
                        </div>
                        <div>
                            <label class="form-lbl">Taariikhda Xukunka <span class="req">*</span></label>
                            <div class="inp-wrap">
                                <input type="date" name="judgment_date"
                                    value="{{ old('judgment_date', $judgment?->judgment_date ? \Carbon\Carbon::parse($judgment->judgment_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                    class="form-inp">
                                <i class="bi bi-calendar3 inp-ico"></i>
                            </div>
                        </div>
                        <div>
                            <label class="form-lbl">Wakhtiga Xukunka <span class="sec-opt">(ikhtiyaari)</span></label>
                            <div class="inp-wrap">
                                <input type="time" name="judgment_time"
                                    value="{{ old('judgment_time', $judgment?->judgment_time ? \Carbon\Carbon::parse($judgment->judgment_time)->format('H:i') : now()->format('H:i')) }}"
                                    class="form-inp">
                                <i class="bi bi-clock inp-ico"></i>
                            </div>
                        </div>
                        <div>
                            <label class="form-lbl">Natiijada Xukunka <span class="sec-opt">(ikhtiyaari)</span></label>
                            <div class="inp-wrap">
                                <select name="judgment_outcome" class="form-sel">
                                    <option value="">— Dooro —</option>
                                    @foreach(['La Guuleystay', 'La Diiday', 'Xal Dhexdhexaad ah', 'Dacwada La Joojiyay', 'Dacwada La Wareejiyay'] as $outcome)
                                        <option value="{{ $outcome }}" {{ old('judgment_outcome', $judgment->judgment_outcome ?? '') === $outcome ? 'selected' : '' }}>
                                            {{ $outcome }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down inp-ico-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- 30-day notice info --}}
                    <div style="background:#fff8e1;border:1px solid #f0b43c55;border-radius:8px;padding:.625rem .75rem;font-size:.8rem;color:#92600a;line-height:1.5;display:flex;align-items:center;gap:.5rem">
                        <i class="bi bi-info-circle-fill" style="flex-shrink:0;color:#C07E15"></i>
                        <span>Muddo 30 maalmood gudahood ayaa bilaabanaysa laga bilaabo maalinta dhinacyadu saxiixaan saxiixa dhijitaalka ah ee xukunka, <strong>ma aha</strong> taariikhda go'aanka xukunka la gaaray.</span>
                    </div>

                    {{-- Judgment Body (Quill rich-text) --}}
                    <div>
                        <label class="form-lbl">
                            Nuxurka Qoraalka <span class="req">*</span>
                            <span class="sec-opt">(Rich Text Editor — HTML Supported)</span>
                        </label>
                        <textarea name="verdict" id="ta_verdict" style="display:none">{{ old('verdict', $judgment->verdict ?? '') }}</textarea>
                        <div id="qe_verdict"></div>
                        <div class="ql-word-count" id="wc_verdict">0 ereyood</div>
                    </div>



                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <a href="{{ route('appeal-criminal-judgments.index') }}" class="btn-cancel">
                    <i class="bi bi-x-lg" style="font-size:.75rem"></i> Jooji
                </a>
                <div style="display:flex;gap:.75rem">
                    <button type="submit" name="status" value="Draft" class="btn-save"
                        style="background:white;color:#528CBE;border:1.5px solid #528CBE;box-shadow:none">
                        <i class="bi bi-floppy"></i>
                        {{ $judgment ? 'Cusboonaysii Muswaadda' : 'Keydi Muswaadda' }}
                    </button>
                    <button type="submit" name="status" value="Submitted" class="btn-submit">
                        <i class="bi bi-send"></i>
                        {{ $judgment ? 'Cusboonaysii & Gudbi' : 'Gudbi Xukunka' }}
                    </button>
                </div>
            </div>

        </form>

    </div>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    var ta  = document.getElementById('ta_verdict');
    var wc  = document.getElementById('wc_verdict');
    var q   = new Quill('#qe_verdict', {
        theme: 'snow',
        placeholder: 'Geli nuxurka garmaqalka...',
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

    if (ta.value) {
        q.root.innerHTML = ta.value;
    }

    function countWords(html) {
        var text = html.replace(/<[^>]*>/g, ' ').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
        return text ? text.split(' ').filter(Boolean).length : 0;
    }

    wc.textContent = countWords(q.root.innerHTML) + ' ereyood';

    q.on('text-change', function () {
        ta.value = q.root.innerHTML;
        wc.textContent = countWords(q.root.innerHTML) + ' ereyood';
    });

    document.querySelector('form').addEventListener('submit', function () {
        ta.value = q.root.innerHTML;
    });
})();
</script>

@endsection