@extends('admin.admin_master')
@section('page_title', $hearing ? 'Wax ka bedel Mudeynta' : 'Mudee Dacwada')

@push('styles')
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-day.selected, .flatpickr-day.selected:hover { background:#528CBE; border-color:#528CBE; }
        .flatpickr-time input:hover, .flatpickr-time .flatpickr-am-pm:hover { background:#f3f4f6; }
        .fp-input-visible {
            width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;
            border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;
            cursor:pointer;transition:border-color .15s,box-shadow .15s;
        }
        .fp-input-visible:focus { border-color:#528CBE; box-shadow:0 0 0 3px rgba(82,140,190,.15); }
        .ql-container { font-family: inherit; font-size: .875rem; border-radius: 0 0 8px 8px; }
        .ql-toolbar { border-radius: 8px 8px 0 0; background: #f9fafb; border-color: #e5e7eb; }
        .ql-container.ql-snow { border-color: #e5e7eb; min-height: 120px; }
        .ql-editor { min-height: 100px; line-height: 1.65; }
        .ql-editor.ql-blank::before { color: #9ca3af; font-style: normal; }
        .ql-word-count { font-size: .72rem; color: #9ca3af; text-align: right;
                         padding: 3px 10px; background: #f9fafb; border: 1px solid #e5e7eb;
                         border-top: none; border-radius: 0 0 8px 8px; }
    </style>
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- .ci, .cl, .req, .info-card, .card-header, .card-title, .card-body styles in resources/css/app.css --}}

    <div class="p-4 sm:p-6 w-full">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">
                    {{ $hearing ? 'Wax ka bedel Mudeynta' : 'Mudee Dacwada' }}
                </h1>
                <p class="text-sm text-neutral-500 mt-0.5">
                    @if($hearing)
                        Wax ka bedelida mudeynta ee
                        <span class="font-semibold text-primary">{{ $hearing->familyCase->FileNo ?? '—' }}</span>
                    @elseif($selectedCase)
                        Mudeynta dacwada ee
                        <span class="font-semibold text-primary">{{ $selectedCase->FileNo }}</span>
                    @else
                        Geli xogta hoose si aad u mudeeyso dacwad cusub
                    @endif
                </p>
            </div>
            <a href="{{ route('appeal-family-hearings.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200
                                      rounded-xl bg-white hover:bg-neutral-50 transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i> Ku noqo Jadwalka
            </a>
        </div>

        <form method="POST" action="{{ $hearing ? route('appeal-family-hearings.update', $hearing->id) : route('appeal-family-hearings.store') }}"
            id="hearing-form" enctype="multipart/form-data">
            @csrf
            @if($hearing) @method('PUT') @endif

            {{-- Case Selection Card --}}
            <div class="info-card">
                <div class="card-header">
                    <i class="bi bi-folder2-open text-primary"></i>
                    <span class="card-title">Macluumaadka Dacwada</span>
                </div>
                <div class="card-body">
                    @if(!$selectedCase)
                        <div class="mb-4">
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Faylka
                                Dacwada <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <select name="family_case_id" id="f-case" required
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="">— Dooro Dacwad —</option>
                                    @foreach($cases as $c)
                                        <option value="{{ $c->AFCID }}" {{ ($hearing && $hearing->family_case_id == $c->AFCID) ? 'selected' : '' }}>
                                            {{ $c->FileNo }} — {{ $c->CaseType }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down"
                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="family_case_id" value="{{ $selectedCase->AFCID }}">
                    @endif

                    @if($selectedCase)
                        <div class="flex items-center gap-3">
                            <div class="flex-1 rounded-xl p-3 kpi-icon-primary">
                                <p class="text-[10px] font-bold uppercase text-neutral-400 mb-1">Lambarka Faylka</p>
                                <p class="text-sm font-bold text-neutral-700">{{ $selectedCase->FileNo }}</p>
                            </div>
                            <div class="flex-1 rounded-xl p-3 kpi-icon-gold">
                                <p class="text-[10px] font-bold uppercase text-neutral-400 mb-1">Nooca Dacwada</p>
                                <p class="text-sm font-bold text-neutral-700">{{ $selectedCase->CaseType ?? '—' }}</p>
                            </div>
                            <div class="flex-1 rounded-xl p-3" style="background:rgba(10,40,77,0.08)">
                                <p class="text-[10px] font-bold uppercase text-neutral-400 mb-1">Hoosaadka</p>
                                <p class="text-sm font-bold text-neutral-700">{{ $selectedCase->SubCase ?? '—' }}</p>
                            </div>
                            <div class="flex-1 rounded-xl p-3 kpi-icon-success">
                                <p class="text-[10px] font-bold uppercase text-neutral-400 mb-1">Xaalada Dacwada</p>
                                @php
                                    $caseStatus = $selectedCase->Status ?? '—';
                                    $statusColor = $caseStatus === 'Mudeyn'
                                        ? 'background:#dbeafe;color:#1d4ed8;'
                                        : 'background:#f3f4f6;color:#374151;';
                                @endphp
                                <span
                                    style="display:inline-block;font-size:.72rem;font-weight:700;padding:.15rem .6rem;border-radius:.4rem;{{ $statusColor }}">
                                    {{ $caseStatus }}
                                </span>
                            </div>
                        </div>

                        {{-- Status process notice --}}
                        @if(isset($statuses) && $statuses->isNotEmpty())
                            <div class="mt-3 flex items-center gap-2 rounded-lg px-3 py-2"
                                style="background:#eff6ff;border:1px solid #bfdbfe;">
                                <i class="bi bi-info-circle text-blue-500 text-xs"></i>
                                <p class="text-[11px] text-blue-700 font-medium">
                                    Marka aad kaydiso mudeynta xaaladda "La mudeeyay", xaaladda dacwadu si toos ah ugu bedeli
                                    doontaa <strong>Mudeyn</strong>.
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Hearing Details Card --}}
            <div class="info-card">
                <div class="card-header">
                    <i class="bi bi-calendar3-week text-primary"></i>
                    <span class="card-title">Faahfaahinta Mudeynta</span>
                </div>
                <div class="card-body">
                    @php
                        $endTimeValue = '';
                        if ($hearing && $hearing->hearing_time && $hearing->duration) {
                            [$durH, $durM] = array_pad(explode(':', $hearing->duration), 2, 0);
                            $endTimeValue = \Carbon\Carbon::parse($hearing->hearing_time)
                                ->addHours((int) $durH)->addMinutes((int) $durM)
                                ->format('H:i');
                        }
                    @endphp
                    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1.25rem;align-items:start;">

                        {{-- Hearing Date --}}
                        <div>
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Taariikhda
                                Mudeyga <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <input type="text" name="hearing_date" id="f-date" required readonly autocomplete="off"
                                    placeholder="Dooro taariikhda"
                                    value="{{ $hearing ? $hearing->hearing_date->format('Y-m-d') : date('Y-m-d') }}"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s;cursor:pointer"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-calendar3"
                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>

                        {{-- Start Time --}}
                        <div>
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Waqtiga
                                Bilowga <span style="color:#ef4444">*</span>

                            </label>
                            <div style="position:relative">
                                <input type="text" name="hearing_time" id="f-time" required readonly autocomplete="off"
                                    placeholder="Dooro waqtiga"
                                    value="{{ $hearing ? substr($hearing->hearing_time, 0, 5) : '' }}"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s;cursor:pointer"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-clock"
                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>

                        </div>

                        {{-- End Time --}}
                        <div>
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Waqtiga
                                Dhamaadka <span style="color:#ef4444">*</span>

                            </label>
                            <div style="position:relative">
                                <input type="text" name="end_time" id="f-end-time" required readonly autocomplete="off"
                                    placeholder="Dooro waqtiga"
                                    value="{{ $endTimeValue }}"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s;cursor:pointer"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-clock"
                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>

                        </div>

                        {{-- Duration — auto-calculated from Start/End Time --}}
                        <div>
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Muddada</label>
                            <div style="position:relative">
                                <input type="text" name="duration" id="f-duration" readonly
                                    placeholder="HH:MM"
                                    value="{{ $hearing->duration ?? '' }}"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#f9fafb;color:#111827;outline:none;box-sizing:border-box;cursor:default">
                                <i class="bi bi-clock-history"
                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                            <p class="text-[10px] text-neutral-400 mt-1">Si toos ah ayaa loo xisaabiyaa</p>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label
                                style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Xaalada
                                <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <select name="status" id="f-status" required
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    @php
                                        $statusLabels = [
                                            'Scheduled' => 'La mudeeyay',
                                            'Completed' => 'Dhameystiran',
                                            'Postponed' => 'Dib u dhacay',
                                            'Cancelled' => 'La joojiyay'
                                        ];
                                    @endphp
                                    @foreach(['Scheduled', 'Completed', 'Postponed', 'Cancelled'] as $st)
                                        <option value="{{ $st }}" {{ ($hearing && $hearing->status === $st) || (!$hearing && $st === 'Scheduled') ? 'selected' : '' }}>
                                            {{ $statusLabels[$st] }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down"
                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>

                        {{-- Hearing Purpose — Quill rich text (full width) --}}
                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                                Ujeedada Mudeynta
                            </label>
                            <textarea name="hearing_purpose" id="ta_purpose" style="display:none">{{ $hearing->hearing_purpose ?? '' }}</textarea>
                            <div id="qe_purpose"></div>
                            <div class="ql-word-count" id="wc_purpose">0 ereyood</div>
                        </div>
                        @php $caseCourt = $selectedCase->court->longName ?? ($hearing->courtroom ?? ''); @endphp
                        <input type="hidden" name="courtroom" value="{{ $caseCourt }}">

                    </div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="flex items-center justify-between gap-3 mt-2">
                <a href="{{ route('appeal-family-hearings.index') }}" class="px-6 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200
                                          rounded-xl bg-white hover:bg-neutral-50 transition-all">
                    Jooji
                </a>
                <div style="display:flex;gap:.75rem;">
                    <button type="submit" name="action" value="draft"
                        style="display:inline-flex;align-items:center;gap:.4rem;padding:.625rem 1.25rem;font-size:.875rem;font-weight:600;color:#2563eb;background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:.75rem;transition:all .2s;cursor:pointer;"
                        onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                        <i class="bi bi-floppy"></i>
                        {{ $hearing ? 'Keydi Oo Dib U Eeg' : 'Keydi Qoraalka' }}
                    </button>
                    <button type="submit" name="action" value="submit"
                        class="btn-primary px-7 py-2.5 text-sm rounded-xl shadow hover:opacity-90 transition-all">
                        {{ $hearing ? 'Cusbooneysii Mudeynta' : 'Keydi Mudeynta' }}
                    </button>
                </div>
            </div>

        </form>

        {{-- Existing Hearings Table --}}
        <div class="info-card mt-6" id="hearings-table-card" style="display:none">
            <div class="card-header flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-calendar3-week text-primary"></i>
                    <span class="card-title">Mudeymaha Hore</span>
                    <span id="hearings-count" class="text-xs font-bold text-white px-2 py-0.5 rounded-full"
                        style="background:#528CBE"></span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:rgba(82,140,190,0.06)">
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-10">#</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Taariikhda
                            </th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Waqtiga</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Muddada</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Ujeedada</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                Xaalada</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">
                                Ficilada</th>
                        </tr>
                    </thead>
                    <tbody id="hearings-tbody" class="divide-y divide-neutral-100"></tbody>
                </table>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            const statusLabels = { Scheduled: 'La mudeeyay', Completed: 'Dhameystiran', Postponed: 'Dib u dhacay', Cancelled: 'La joojiyay' };
            const statusStyles = {
                Scheduled: 'background:rgba(82,140,190,0.12);color:#1d4ed8',
                Completed: 'background:rgba(16,185,129,0.12);color:#059669',
                Postponed: 'background:rgba(240,180,60,0.15);color:#C07E15',
                Cancelled: 'background:rgba(239,68,68,0.12);color:#b91c1c',
            };
            const currentEditId = {{ isset($hearing) ? $hearing->id : 'null' }};

            function computeDuration() {
                const start = document.getElementById('f-time').value;
                const end = document.getElementById('f-end-time').value;
                const durationInput = document.getElementById('f-duration');
                if (!start || !end) { durationInput.value = ''; return; }
                const [sh, sm] = start.split(':').map(Number);
                const [eh, em] = end.split(':').map(Number);
                let diff = (eh * 60 + em) - (sh * 60 + sm);
                if (diff < 0) diff += 24 * 60;
                const hh = String(Math.floor(diff / 60)).padStart(2, '0');
                const mm = String(diff % 60).padStart(2, '0');
                durationInput.value = `${hh}:${mm}`;
            }
            flatpickr('#f-date', {
                dateFormat: 'Y-m-d',
                altInput: true, altFormat: 'd/m/Y', altInputClass: 'fp-input-visible',
                defaultDate: document.getElementById('f-date').value || null,
                disableMobile: true,
            });
            flatpickr('#f-time', {
                enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: false,
                altInput: true, altFormat: 'h:i K', altInputClass: 'fp-input-visible',
                defaultDate: document.getElementById('f-time').value || null,
                disableMobile: true,
                onChange: computeDuration,
            });
            flatpickr('#f-end-time', {
                enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: false,
                altInput: true, altFormat: 'h:i K', altInputClass: 'fp-input-visible',
                defaultDate: document.getElementById('f-end-time').value || null,
                disableMobile: true,
                onChange: computeDuration,
            });
            computeDuration();

            function formatDate(d) {
                const dt = new Date(d);
                return dt.toLocaleDateString('en-GB');
            }

            function renderHearings(hearings) {
                const tbody = document.getElementById('hearings-tbody');
                const card = document.getElementById('hearings-table-card');
                const badge = document.getElementById('hearings-count');
                tbody.innerHTML = '';

                if (!hearings.length) { card.style.display = 'none'; return; }

                card.style.display = '';
                badge.textContent = hearings.length;

                hearings.forEach((h, i) => {
                    const isEdit = h.id === currentEditId;
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-neutral-50 transition-colors' + (isEdit ? ' outline outline-2 outline-[#528CBE]' : '');
                    tr.innerHTML = `
                                    <td class="px-5 py-3 text-xs font-bold text-neutral-400">${String(i + 1).padStart(2, '0')}</td>
                                    <td class="px-5 py-3 font-semibold text-neutral-700">${formatDate(h.hearing_date)}</td>
                                    <td class="px-5 py-3 text-neutral-600">${(h.hearing_time || '').substring(0, 5)}</td>
                                    <td class="px-5 py-3 text-neutral-500">${h.duration || '—'}</td>
                                    <td class="px-5 py-3 text-neutral-500 max-w-[200px] truncate">${h.hearing_purpose || '—'}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                              style="${statusStyles[h.status] || 'background:#f3f4f6;color:#374151'}">
                                            ${statusLabels[h.status] || h.status}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="/appeal-family-hearings/${h.id}/edit"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                               onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                               onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                                <i class="bi bi-pencil-square text-xs"></i>
                                            </a>
                                            <button type="button"
                                                    onclick="deleteHearing(${h.id})"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                                    onmouseover="this.style.background='#ef4444';this.style.borderColor='#ef4444';this.style.color='white'"
                                                    onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                                <i class="bi bi-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>`;
                    tbody.appendChild(tr);
                });
            }

            function loadHearings(caseId) {
                if (!caseId) { document.getElementById('hearings-table-card').style.display = 'none'; return; }
                fetch(`/hearings/case/${caseId}/json`)
                    .then(r => r.json())
                    .then(renderHearings)
                    .catch(() => { });
            }

            function deleteHearing(id) {
                if (!confirm('Ma hubtaa inaad tirtirto mudeyntan?')) return;
                fetch(`/hearings/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                }).then(r => {
                    if (r.ok) {
                        const caseId = document.getElementById('f-case')?.value;
                        if (caseId) loadHearings(caseId);
                    }
                });
            }

            // Load on page ready (edit mode or create-with-caseId)
            @if(isset($caseHearings) && $caseHearings->isNotEmpty())
                renderHearings(@json($caseHearings));
            @elseif(isset($selectedCase))
                loadHearings({{ $selectedCase->AFCID }});
            @endif

                        // Reload when user picks a different case from the dropdown
                        const caseSelect = document.getElementById('f-case');
            if (caseSelect) {
                caseSelect.addEventListener('change', () => loadHearings(caseSelect.value));
            }
        </script>

    </div>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    var ta = document.getElementById('ta_purpose');
    var wc = document.getElementById('wc_purpose');

    var q = new Quill('#qe_purpose', {
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