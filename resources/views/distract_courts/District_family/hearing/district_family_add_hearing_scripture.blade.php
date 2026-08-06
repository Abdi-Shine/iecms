@extends('admin.admin_master')
@section('page_title', $scripture ? 'Wax ka Badal Garmaqalka' : 'Ku Dar Garmaqalka')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-wrap">

        {{-- Flash messages --}}
        @if(session('success'))
            <div
                style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#065f46;border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px">
                <i class="bi bi-check-circle-fill" style="font-size:1rem"></i>
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
        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-journal-text"></i></div>
                <div>
                    <h1 class="page-title">{{ $scripture ? 'Wax ka Badal Garmaqalka' : 'Ku Dar Garmaqalka' }}</h1>
                    <p class="page-sub">
                        @if($scripture)
                            Lambarka Foomka: <span class="page-sub-accent">{{ $scripture->form_id }}</span>
                            &nbsp;·&nbsp; Dacwada: <span class="page-sub-accent">{{ $case->FileNo }}</span>
                        @else
                            Dacwada: <span class="page-sub-accent">{{ $case->FileNo }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <a href="{{ route('family-hearings.scripture') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Dib ugu Noqo
            </a>
        </div>

        <form method="POST"
            action="{{ $scripture ? route('family-hearings.scripture.update', $scripture->id) : route('family-hearings.scripture.store') }}">
            @csrf
            @if($scripture) @method('PUT') @endif
            <input type="hidden" name="family_case_id" value="{{ $case->FCID }}">

            {{-- District Court Information --}}
            <div class="sec-card">
                <div class="sec-hd">
                    <i class="bi bi-building" style="color:#528CBE;font-size:.875rem"></i>
                    <span class="sec-title">Macluumaadka Maxkamadda Degmada</span>
                </div>
                <div class="sec-bd">
                    <div class="info-grid">
                        <div class="info-field">
                            <span class="info-lbl">Maxkamadda Degmada</span>
                            <span class="info-val">{{ $case->court->longName ?? '—' }}</span>
                        </div>
                        <div class="info-field">
                            <span class="info-lbl">Lambarka Dacwada</span>
                            <span class="info-val">{{ $case->FileNo }}</span>
                        </div>
                        <div>
                            <label class="form-lbl">Lambarka Fadhiga <span class="req">*</span></label>
                            <div class="inp-wrap">
                                <input type="number" name="session_number" min="1"
                                    value="{{ old('session_number', $scripture->session_number ?? 1) }}" required
                                    class="form-inp">
                                <i class="bi bi-hash inp-ico"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 1: Hearing Information --}}
            <div class="sec-card">
                <div class="sec-hd">
                    <div class="sec-num"><span>1</span></div>
                    <span class="sec-title">Macluumaadka Mudeynta</span>
                </div>
                <div class="sec-bd">

                    @php
                        $hearingMap = $hearings->mapWithKeys(fn($h) => [
                            $h->id => [
                                'date' => $h->hearing_date->format('Y-m-d'),
                                'time' => $h->hearing_time ? \Carbon\Carbon::parse($h->hearing_time)->format('H:i') : '',
                            ]
                        ])->toJson();
                    @endphp

                    <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:1rem;align-items:start;">
                        <div>
                            <label class="form-lbl">Xidh Jadwalka Mudeynta <span class="req">*</span></label>
                            <div class="inp-wrap">
                                <select name="hearing_id" id="hearing_id_select" class="form-sel"
                                    onchange="fillFromSchedule(this.value)">
                                    <option value="">— Dooro Jadwalka Mudeynta —</option>
                                    @foreach($hearings as $h)
                                        <option value="{{ $h->id }}" {{ old('hearing_id', $scripture->hearing_id ?? '') == $h->id ? 'selected' : '' }}>
                                            {{ $h->hearing_date->format('d/m/Y') }}
                                            @if($h->hearing_time) · {{ \Carbon\Carbon::parse($h->hearing_time)->format('H:i') }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down inp-ico-xs"></i>
                            </div>
                            @if($hearings->isEmpty())
                                <p class="form-note">Mudeyn lama helin dacwadan. Marka hore jadwal u samee mudeyn.</p>
                            @else
                                <p class="form-note" style="color:#6b7280">Doorashada jadwalku waxay si toos ah u buuxin doontaa taariikhda iyo saacadda.</p>
                            @endif
                        </div>
                        <div>
                            <label class="form-lbl">Taariikhda Fadhiga</label>
                            <div class="inp-wrap">
                                <input type="date" name="hearing_date" id="hearing_date"
                                    value="{{ old('hearing_date', $scripture?->hearing_date ? \Carbon\Carbon::parse($scripture->hearing_date)->format('Y-m-d') : '') }}"
                                    class="form-inp">
                                <i class="bi bi-calendar3 inp-ico"></i>
                            </div>
                        </div>
                        <div>
                            <label class="form-lbl">Saacadda Fadhiga</label>
                            <div class="inp-wrap">
                                <input type="time" name="hearing_time" id="hearing_time"
                                    value="{{ old('hearing_time', $scripture->hearing_time ?? '') }}" class="form-inp">
                                <i class="bi bi-clock inp-ico"></i>
                            </div>
                        </div>
                    </div>

                    <script>
                        var hearingMap = {!! $hearingMap !!};

                        function fillFromSchedule(id) {
                            var h = hearingMap[id];
                            if (h) {
                                document.getElementById('hearing_date').value = h.date;
                                document.getElementById('hearing_time').value = h.time;
                            } else {
                                document.getElementById('hearing_date').value = '';
                                document.getElementById('hearing_time').value = '';
                            }
                        }

                        // Auto-fill on page load if a value is already selected (edit mode)
                        document.addEventListener('DOMContentLoaded', function () {
                            var sel = document.getElementById('hearing_id_select');
                            @if(!$scripture && !old('hearing_id'))
                                if (sel.value) fillFromSchedule(sel.value);
                            @endif
                                                        });
                    </script>

                </div>
            </div>

            {{-- Section 2: Court Details --}}
            <div class="sec-card">
                <div class="sec-hd">
                    <div class="sec-num"><span>2</span></div>
                    <span class="sec-title">Faahfaahinta Maxkamadda</span>
                </div>
                <div class="sec-bd">
                    <div class="form-row-2">
                        <div>
                            <label class="form-lbl"> Hoolka Dacwada</label>
                            <div class="inp-wrap">
                                <select name="courtroom" class="form-sel">
                                    <option value="">— Dooro Hoolka —</option>
                                    @foreach(['Hoolka 1aad', 'Hoolka 2aad', 'Hoolka 3aad', 'Hoolka 4aad', 'Hoolka 5aad', 'Hoolka 6aad', 'Hoolka 7aad'] as $room)
                                        <option value="{{ $room }}" {{ old('courtroom', $scripture->courtroom ?? '') === $room ? 'selected' : '' }}>
                                            {{ $room }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down inp-ico-xs"></i>
                            </div>
                        </div>
                        <div>
                            <label class="form-lbl">Fadhiga Garmaqalka</label>
                            <div class="inp-wrap">
                                <select name="hearing_type" class="form-sel">
                                    <option value="">— Dooro —</option>
                                    @foreach(['Fadhiga 1aad', 'Fadhiga 2aad', 'Fadhiga 3aad', 'Fadhiga 4aad', 'Fadhiga 5aad', 'Fadhiga 6aad', 'Fadhiga 7aad'] as $type)
                                        <option value="{{ $type }}" {{ old('hearing_type', $scripture->hearing_type ?? '') === $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down inp-ico-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Body Content --}}
            <div class="sec-card">
                <div class="sec-hd">
                    <div class="sec-num"><span>3</span></div>
                    <span class="sec-title">Qoraalka Garmaqalka</span>
                </div>
                <div class="sec-bd">
                    <div>
                        <label class="form-lbl">Nuxurka Qoraalka <span class="req">*</span>
                            <span class="sec-opt">(Rich text editor — HTML supported)</span>
                        </label>
                        <textarea name="body_content" id="ta_body_content" style="display:none">{{ old('body_content', $scripture->body_content ?? '') }}</textarea>
                        <div id="qe_body_content"></div>
                        <div class="ql-word-count" id="wc_body_content">0 ereyood</div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <a href="{{ route('family-hearings.scripture') }}" class="btn-cancel">
                    <i class="bi bi-x-lg" style="font-size:.75rem"></i> Jooji
                </a>
                <div style="display:flex;gap:.75rem">
                    <button type="submit" name="status" value="Draft" class="btn-save"
                        style="background:white;color:#528CBE;border:1.5px solid #528CBE;box-shadow:none">
                        <i class="bi bi-floppy"></i>
                        {{ $scripture ? 'Cusboonaysii Qoraalka' : 'Keydi Qoraalka' }}
                    </button>
                    <button type="submit" name="status" value="Submitted" class="btn-submit">
                        <i class="bi bi-send"></i>
                        {{ $scripture ? 'Cusboonaysii & Gudbi' : 'Gudbi Garmaqalka' }}
                    </button>
                </div>
            </div>

        </form>

    </div>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    var ta  = document.getElementById('ta_body_content');
    var wc  = document.getElementById('wc_body_content');

    var q = new Quill('#qe_body_content', {
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
            ],
            history: { delay: 500, maxStack: 100, userOnly: true }
        }
    });

    if (ta && ta.value.trim()) q.root.innerHTML = ta.value;

    function updateWordCount() {
        var words = q.getText().trim().split(/\s+/).filter(Boolean).length;
        if (wc) wc.textContent = words + ' ereyood';
    }
    updateWordCount();

    q.on('text-change', function () {
        if (ta) ta.value = q.root.innerHTML === '<p><br></p>' ? '' : q.root.innerHTML;
        updateWordCount();
    });

    document.querySelector('form').addEventListener('submit', function () {
        if (ta) ta.value = q.root.innerHTML === '<p><br></p>' ? '' : q.root.innerHTML;
    });
})();
</script>

@endsection