@extends('admin.admin_master')
@section('page_title', $appeal ? 'Wax ka Badal Rafcaanka' : 'Rafcaanka Dacwadaha')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
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
                    {{ $appeal ? 'Wax ka Badal Rafcaanka' : 'Rafcaanka Dacwadaha' }}
                </h1>
                <p style="font-size:.8125rem;color:#6b7280;margin:0">
                    Dacwada: <strong style="color:#374151">{{ $case->FileNo }}</strong>
                </p>
                @if($appeal && $appeal->status === 'Draft')
                    <p style="font-size:.75rem;color:#C07E15;margin:.3rem 0 0;display:flex;align-items:center;gap:.375rem">
                        <i class="bi bi-pencil-square"></i> Muswaadda ayaa la wax ka bedelayaa
                    </p>
                @endif
            </div>
            <a href="{{ route('criminal-case-appeal.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Dib ugu Noqo
            </a>
        </div>

        <form method="POST" action="{{ route('criminal-case-appeal.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="criminal_case_id" value="{{ $case->CMID }}">

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
                            <label class="form-lbl">Maxkamadda</label>
                            <div class="field-ro">{{ $case->court->longName ?? '—' }}</div>
                        </div>
                        <div>
                            <label class="form-lbl">Garsoore Madax</label>
                            <div class="field-ro">
                                @if($judge && $judge !== '—')
                                    <span style="font-size:.7rem;color:#6b7280;font-weight:500">Garsoore Madax &nbsp;·&nbsp;</span>{{ $judge }}
                                @else
                                    <span style="color:#9ca3af;font-size:.825rem">Aan la xukumin</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Appeal Details --}}
            <div class="sec-card">
                <div class="sec-hd">
                    <i class="bi bi-arrow-up-circle" style="color:#528CBE;font-size:.875rem"></i>
                    <span class="sec-title">Faahfaahinta Rafcaanka</span>
                </div>
                <div class="sec-bd">

                    {{-- Dhinac + Type + Date (one row) --}}
                    @php $savedParty = old('appealing_parties.0', $appeal?->appealing_parties[0] ?? ''); @endphp
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">

                        {{-- 1. Dhinaca Rafcaanka Gudbinaya --}}
                        <div>
                            <label class="form-lbl">Dhinaca Rafcaanka Gudbinaya <span class="req">*</span></label>
                            <div class="inp-wrap">
                                <select name="appealing_parties[]" class="form-sel">
                                    <option value="">— Dooro Dhinac —</option>
                                    @foreach($lagaParties as $rc)
                                        <option value="{{ $rc->party_id }}"
                                            {{ (string)$savedParty === (string)$rc->party_id ? 'selected' : '' }}>
                                            {{ $rc->party_name }}
                                            @if($rc->party_role) · {{ $rc->party_role }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down inp-ico-xs"></i>
                            </div>
                            @if($lagaParties->isEmpty())
                                <p style="margin:.25rem 0 0;font-size:.75rem;color:#9ca3af">
                                    <i class="bi bi-info-circle"></i> Dhinac laga xukumay lama helin.
                                </p>
                            @endif
                        </div>

                        {{-- 2. Nooca Rafcaanka --}}
                        <div>
                            <label class="form-lbl">Nooca Rafcaanka <span class="req">*</span></label>
                            <div class="inp-wrap">
                                <select name="appeal_type" class="form-sel">
                                    <option value="">— Dooro —</option>
                                    @foreach($caseStatus as $s)
                                        <option value="{{ $s->name }}"
                                            {{ old('appeal_type', $appeal?->appeal_type ?? $defaultStatus) === $s->name ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down inp-ico-xs"></i>
                            </div>
                        </div>

                        {{-- 3. Taariikhda Rafcaanka --}}
                        <div>
                            <label class="form-lbl">Taariikhda Rafcaanka <span class="req">*</span></label>
                            <div class="inp-wrap">
                                <input type="date" name="appeal_date"
                                    value="{{ old('appeal_date', $appeal?->appeal_date ? \Carbon\Carbon::parse($appeal->appeal_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                    class="form-inp">
                                <i class="bi bi-calendar3 inp-ico"></i>
                            </div>
                        </div>

                    </div>

                    {{-- Additional Notes --}}
                    <div>
                        <label class="form-lbl">
                            Faallo Dheeraad ah
                            <span class="sec-opt">(ikhtiyaari)</span>
                        </label>
                        <textarea name="additional_notes" class="form-textarea" rows="3"
                            placeholder="Faallo kale...">{{ old('additional_notes', $appeal->additional_notes ?? '') }}</textarea>
                    </div>

                    {{-- Attachment --}}
                    <div>
                        <label class="form-lbl">
                            <i class="bi bi-paperclip" style="color:#528CBE;margin-right:4px"></i>
                            Dukuumintiga Rafcaanka <span class="sec-opt">(ikhtiyaari — PDF, Word, JPG)</span>
                        </label>

                        @if($appeal?->attachment)
                            <div style="display:flex;align-items:center;gap:10px;padding:.625rem .875rem;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;margin-bottom:.5rem">
                                <i class="bi bi-file-earmark-check-fill" style="color:#0284c7;font-size:1.1rem;flex-shrink:0"></i>
                                <div style="flex:1;min-width:0">
                                    <p style="margin:0;font-size:.8rem;font-weight:600;color:#0c4a6e">Faylka Hadda:</p>
                                    <a href="{{ asset('storage/' . $appeal->attachment) }}" target="_blank"
                                        style="font-size:.8rem;color:#0284c7;word-break:break-all">
                                        {{ basename($appeal->attachment) }}
                                    </a>
                                </div>
                                <a href="{{ asset('storage/' . $appeal->attachment) }}" target="_blank" download
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
                                {{ $appeal?->attachment ? 'Bedel Faylka' : 'Dooro Faylka' }}
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
                <a href="{{ route('criminal-case-appeal.index') }}" class="btn-cancel">
                    <i class="bi bi-x-lg" style="font-size:.75rem"></i> Jooji
                </a>
                <div style="display:flex;gap:.75rem">
                    <button type="submit" name="status" value="Draft" class="btn-save"
                        style="background:white;color:#528CBE;border:1.5px solid #528CBE;box-shadow:none">
                        <i class="bi bi-floppy"></i>
                        {{ $appeal ? 'Cusboonaysii Muswaadda' : 'Keydi Muswaadda' }}
                    </button>
                    <button type="submit" name="status" value="Submitted" class="btn-submit">
                        <i class="bi bi-send"></i>
                        {{ $appeal ? 'Cusboonaysii & Gudbi' : 'Gudbi Rafcaanka' }}
                    </button>
                </div>
            </div>

        </form>
    </div>

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
