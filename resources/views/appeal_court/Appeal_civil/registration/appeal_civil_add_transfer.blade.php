@extends('admin.admin_master')
@section('page_title', $transfer ? 'Wax ka Badal Wareejinta' : 'Wareejinta Dacwadda')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-wrap">

        {{-- Flash / Errors --}}
        @if(session('success'))
            <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#065f46;border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#991b1b;border-radius:8px;padding:12px 16px;margin-bottom:16px">
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
                    {{ $transfer ? 'Wax ka Badal Wareejinta' : 'Wareejinta Dacwadda' }}
                </h1>
                <p style="font-size:.8125rem;color:#6b7280;margin:0">
                    Dacwada: <strong style="color:#374151">{{ $case->FileNo }}</strong>
                </p>
                @if($transfer && $transfer->status === 'Draft')
                    <p style="font-size:.75rem;color:#C07E15;margin:.3rem 0 0;display:flex;align-items:center;gap:.375rem">
                        <i class="bi bi-pencil-square"></i> Muswaadda ayaa la wax ka bedelayaa
                    </p>
                @endif
            </div>
            <a href="{{ route('appeal-transfer.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Dib ugu Noqo
            </a>
        </div>

        <form method="POST" action="{{ route('appeal-transfer.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="civil_case_id" value="{{ $case->ACID }}">

            {{-- Section 1: Case Information --}}
            <div class="sec-card">
                <div class="sec-hd">
                    <i class="bi bi-info-circle" style="color:#528CBE;font-size:.875rem"></i>
                    <span class="sec-title">Macluumaadka Dacwadda</span>
                </div>
                <div class="sec-bd">
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem">
                        <div>
                            <label class="form-lbl">Lambarka Dacwadda</label>
                            <div class="field-ro" style="color:#528CBE;font-weight:700">{{ $case->FileNo }}</div>
                        </div>
                        <div>
                            <label class="form-lbl">Nooca Dacwada</label>
                            <div class="field-ro">{{ $case->CaseType }}</div>
                        </div>
                        <div>
                            <label class="form-lbl">Xaalada Dacwada</label>
                            <div class="field-ro">{{ $case->Status }}</div>
                        </div>
                        <div>
                            <label class="form-lbl">Garsoore Madax</label>
                            <div class="field-ro">
                                @if($judge && $judge !== '—')
                                    {{ $judge }}
                                @else
                                    <span style="color:#9ca3af;font-size:.825rem">Aan la xukumin</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Transfer Details --}}
            <div class="sec-card">
                <div class="sec-hd">
                    <i class="bi bi-arrow-left-right" style="color:#528CBE;font-size:.875rem"></i>
                    <span class="sec-title">Faahfaahinta Wareejinta</span>
                </div>
                <div class="sec-bd">

                    {{-- From Court + To Court + Date --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">

                        <div>
                            <label class="form-lbl">Maxkamadda Hadda (Isha)</label>
                            <div class="field-ro" style="color:#528CBE;font-weight:600">
                                {{ $case->court?->longName ?? $case->GradeCourt }}
                            </div>
                        </div>

                        <div>
                            <label class="form-lbl">Maxkamadda Cusub <span class="req">*</span></label>
                            <div class="inp-wrap">
                                <select name="to_court" class="form-sel">
                                    <option value="">— Dooro Maxkamad —</option>
                                    @foreach($courts as $c)
                                        @if($c->courtcode !== $case->GradeCourt)
                                            <option value="{{ $c->courtcode }}"
                                                {{ old('to_court', $transfer?->to_court) === $c->courtcode ? 'selected' : '' }}>
                                                {{ $c->longName }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down inp-ico-xs"></i>
                            </div>
                        </div>

                        <div>
                            <label class="form-lbl">Taariikhda Wareejinta <span class="req">*</span></label>
                            <div class="inp-wrap">
                                <input type="date" name="transfer_date"
                                    value="{{ old('transfer_date', $transfer?->transfer_date ? \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                    class="form-inp">
                                <i class="bi bi-calendar3 inp-ico"></i>
                            </div>
                        </div>

                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="form-lbl">
                            Faallo Dheeraad ah
                            <span class="sec-opt">(ikhtiyaari)</span>
                        </label>
                        <textarea name="notes" id="ta_notes" style="display:none">{{ old('notes', $transfer?->notes ?? '') }}</textarea>
                        <div id="qe_notes"></div>
                        <div class="ql-word-count" id="wc_notes">0 ereyood</div>
                    </div>

                    {{-- Attachment --}}
                    <div>
                        <label class="form-lbl">
                            <i class="bi bi-paperclip" style="color:#528CBE;margin-right:4px"></i>
                            Dukuumintiga Wareejinta <span class="sec-opt">(ikhtiyaari — PDF, Word, JPG)</span>
                        </label>

                        @if($transfer?->attachment)
                            <div style="display:flex;align-items:center;gap:10px;padding:.625rem .875rem;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;margin-bottom:.5rem">
                                <i class="bi bi-file-earmark-check-fill" style="color:#0284c7;font-size:1.1rem;flex-shrink:0"></i>
                                <div style="flex:1;min-width:0">
                                    <p style="margin:0;font-size:.8rem;font-weight:600;color:#0c4a6e">Faylka Hadda:</p>
                                    <a href="{{ asset('storage/' . $transfer->attachment) }}" target="_blank"
                                        style="font-size:.8rem;color:#0284c7;word-break:break-all">
                                        {{ basename($transfer->attachment) }}
                                    </a>
                                </div>
                                <a href="{{ asset('storage/' . $transfer->attachment) }}" target="_blank" download
                                    style="flex-shrink:0;font-size:.8rem;color:#0284c7;font-weight:600">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        @endif

                        <label style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;
                                      padding:1.5rem 1rem;border:2px dashed #d1d5db;border-radius:8px;cursor:pointer;
                                      background:#fafafa;transition:border-color .15s"
                            onmouseover="this.style.borderColor='#528CBE'" onmouseout="this.style.borderColor='#d1d5db'">
                            <i class="bi bi-cloud-arrow-up" style="font-size:1.75rem;color:#9ca3af"></i>
                            <span style="font-size:.875rem;font-weight:600;color:#374151">
                                {{ $transfer?->attachment ? 'Bedel Faylka' : 'Dooro Faylka' }}
                            </span>
                            <span style="font-size:.75rem;color:#9ca3af">PDF, DOC, DOCX, JPG, PNG — Max 10MB</span>
                            <input type="file" name="attachment" id="attachment"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                style="display:none" onchange="showFileName(this)">
                        </label>
                        <p id="attachment-filename" style="margin:.375rem 0 0;font-size:.8rem;color:#528CBE;font-weight:600;display:none">
                            <i class="bi bi-check-circle-fill"></i> <span></span>
                        </p>
                    </div>

                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <a href="{{ route('appeal-transfer.index') }}" class="btn-cancel">
                    <i class="bi bi-x-lg" style="font-size:.75rem"></i> Jooji
                </a>
                <div style="display:flex;gap:.75rem">
                    <button type="submit" name="status" value="Draft" class="btn-save"
                        style="background:white;color:#528CBE;border:1.5px solid #528CBE;box-shadow:none">
                        <i class="bi bi-floppy"></i>
                        {{ $transfer ? 'Cusboonaysii Muswaadda' : 'Keydi Muswaadda' }}
                    </button>
                    <button type="submit" name="status" value="Submitted" class="btn-submit">
                        <i class="bi bi-send"></i>
                        {{ $transfer ? 'Cusboonaysii & Gudbi' : 'Gudbi Wareejinta' }}
                    </button>
                </div>
            </div>

        </form>
    </div>

    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
    (function () {
        var ta = document.getElementById('ta_notes');
        var wc = document.getElementById('wc_notes');

        var q = new Quill('#qe_notes', {
            theme: 'snow',
            placeholder: 'Qeex ujeedada mudeyntaan...',
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

    <script>
        function showFileName(input) {
            var el   = document.getElementById('attachment-filename');
            var span = el.querySelector('span');
            if (input.files && input.files[0]) {
                span.textContent = input.files[0].name;
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        }
    </script>

@endsection
