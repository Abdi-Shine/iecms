@extends('admin.admin_master')
@section('page_title', 'Soo Celinta Faylka — IECMS')
@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="p-4 sm:p-6 w-full" x-data="returnFileManager()">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Soo Celinta Faylka</h1>
            </div>
            <a href="{{ route('family-return-file.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200
                                                  rounded-xl bg-white hover:bg-neutral-50 transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i> Ku Laabo Diiwanka
            </a>
        </div>

        {{-- Case Information Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-5">

            {{-- Card Header --}}
            <div class="px-6 py-4 border-b border-neutral-100" style="background:rgba(82,140,190,0.04)">
                <div class="flex items-center gap-2">
                    <i class="bi bi-info-circle text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Macluumaadka Dacwadda</span>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest mb-1">Lambarka Dacwadda</p>
                    <p class="text-sm font-bold" style="color:#528CBE">{{ $case->FileNo }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest mb-1">Nooca Dacwadda</p>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                        style="background:rgba(240,180,60,0.12);color:#C07E15">
                        {{ $case->CaseType }}
                    </span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest mb-1">Tarikhda Dacwadda</p>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                        style="background:rgba(240,180,60,0.12);color:#C07E15">
                        {{ \Carbon\Carbon::parse($case->OpenDate)->format('d/m/Y') }}
                    </span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest mb-1">Kaaliye Ku Qoran</p>
                    <p class="text-sm font-bold" style="color:#528CBE">
                        @if($clerk?->employee)
                            {{ $clerk->employee->EmpName }}
                            @if($case->court)
                                <span class="text-xs font-normal text-neutral-400 block">{{ $case->court->longName }}</span>
                            @endif
                        @else
                            <span class="text-neutral-400 font-normal text-xs">Aan La Xukumin</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($case->Remarks)
                <div class="px-6 pb-5">
                    <div class="rounded-xl px-4 py-3"
                        style="background:rgba(82,140,190,0.05);border:1px solid rgba(82,140,190,0.12)">
                        <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest mb-1">Nuxurka</p>
                        <p class="text-sm text-neutral-600 leading-relaxed">{{ $case->Remarks }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Return File Details Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden mb-5">

            {{-- Card Header --}}
            <div class="px-6 py-4 border-b border-neutral-100" style="background:rgba(82,140,190,0.04)">
                <div class="flex items-center gap-2">
                    <i class="bi bi-arrow-return-left text-sm" style="color:#528CBE"></i>
                    <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Faahfaahinta Soo Celinta</span>
                </div>
            </div>

            <div class="px-6 py-5">

                {{-- Documents --}}
                <div class="mb-6">
                    <label
                        style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                        Dukumeentiyada La Soo Celiyay
                    </label>
                    <p class="text-xs text-neutral-400 mb-3">
                        Ku qor magaca dukumeentiga iyo tiradiisa bogagga. Isticmaal badhanka si aad u ku darto ama u saarto safaf.
                    </p>

                    <div class="border border-neutral-200 rounded-xl overflow-hidden">
                        <table class="doc-table w-full bg-white">
                            <thead style="background:rgba(82,140,190,0.06)">
                                <tr>
                                    <th style="width:52%">Magaca Dukumeentiga</th>
                                    <th style="width:18%">Tirada Bogagga</th>
                                    <th style="width:20%">Xadka Bogagga</th>
                                    <th style="width:10%">Ficilada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(doc, idx) in documents" :key="idx">
                                    <tr>
                                        <td>
                                            <div style="position:relative">
                                                <input type="text" x-model="doc.name"
                                                    placeholder="Tusaale, Xukun Asalka, Warbixinta Maxkamadda..."
                                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.5rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                                <i class="bi bi-tag"
                                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="position:relative">
                                                <input type="number" x-model.number="doc.pages" min="0" placeholder="0"
                                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.5rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                                <i class="bi bi-hash"
                                                    style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <span x-text="pageRange(idx)"
                                                style="display:inline-block;padding:.3rem .75rem;font-size:.8rem;font-weight:700;font-family:monospace;background:rgba(82,140,190,0.08);color:#528CBE;border-radius:.5rem;border:1px solid rgba(82,140,190,0.2)">
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" @click="removeDoc(idx)"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                                onmouseover="this.style.background='#DC2626';this.style.borderColor='#DC2626';this.style.color='white'"
                                                onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                                <i class="bi bi-trash3 text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr style="background:rgba(82,140,190,0.06);border-top:2px solid rgba(82,140,190,0.2)">
                                    <td style="text-align:right;font-size:.78rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;padding:.55rem .875rem">
                                        Tirada Dukumeentiyada
                                    </td>
                                    <td style="font-size:.9rem;font-weight:800;color:#0A284D;padding:.55rem .875rem">
                                        <span x-text="documents.length"></span>
                                    </td>
                                    <td style="padding:.55rem .875rem;font-size:.78rem;font-weight:700;color:#6b7280">
                                        <span x-show="totalPages() > 0" x-text="'Bog 1 — ' + totalPages()"></span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <button type="button" @click="addDoc()"
                        class="mt-3 flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl border transition-all"
                        style="color:#528CBE;border-color:#528CBE;background:transparent"
                        onmouseover="this.style.background='#528CBE';this.style.color='white'"
                        onmouseout="this.style.background='transparent';this.style.color='#528CBE'">
                        <i class="bi bi-plus-lg"></i> Ku Dar Dukumeenti
                    </button>
                </div>

                {{-- Additional Notes --}}
                <div>
                    <label
                        style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                        Faallooyin Dheeraad Ah
                    </label>
                    <div style="position:relative">
                        <textarea x-model="additionalNotes" placeholder="Faallooyin ama xusus kale..."
                            style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;resize:vertical;min-height:90px;transition:border-color .15s,box-shadow .15s"
                            onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                            onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Buttons --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:.5rem;padding-bottom:1.5rem">
            <a href="{{ route('family-return-file.index') }}"
                style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;
                       border-radius:.625rem;background:white;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;transition:background .15s"
                onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                <i class="bi bi-x-lg" style="font-size:.75rem"></i> Jooji
            </a>
            <div class="flex items-center gap-2">
                <button type="button" @click="save('Draft')" :disabled="saving"
                    style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.5rem;font-size:.85rem;font-weight:700;
                           color:#528CBE;background:white;border:1.5px solid #528CBE;border-radius:.625rem;cursor:pointer;transition:opacity .15s"
                    :class="{'opacity-70 cursor-not-allowed': saving}"
                    onmouseover="if(!this.disabled)this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                    <i class="bi bi-floppy" x-show="!saving"></i>
                    <span x-text="saving ? 'Waa La Kaydiyaa...' : 'Keydi Qoraalka'"></span>
                </button>
                <button type="button" @click="save('Qaatay')" :disabled="saving"
                    style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;
                           color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer;
                           box-shadow:0 4px 14px rgba(82,140,190,.4);transition:opacity .15s"
                    :class="{'opacity-70 cursor-not-allowed': saving}"
                    onmouseover="if(!this.disabled)this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                    <i class="bi bi-send" x-show="!saving"></i>
                    <i class="bi bi-arrow-repeat" x-show="saving" style="display:none"></i>
                    <span x-text="saving ? 'Waa La Diraa...' : 'Keydi Mudeynta'"></span>
                </button>
            </div>
        </div>

    </div>

    <script>
        function returnFileManager() {
            const existing     = @json($existing);
            const caseDocuments = @json($caseDocuments);

            return {
                saving: false,
                caseId: {{ $case->FCID }},
                documents: (existing && existing.documents && existing.documents.length)
                    ? existing.documents
                    : (caseDocuments.length ? caseDocuments : [{ name: '', pages: 0 }]),
                additionalNotes: existing?.additional_notes ?? '',

                addDoc() {
                    this.documents.push({ name: '', pages: 0 });
                },

                removeDoc(idx) {
                    if (this.documents.length === 1) return;
                    this.documents.splice(idx, 1);
                },

                pageCount(p) {
                    const v = String(p ?? '').trim();
                    if (v.includes('-')) {
                        const [s, e] = v.split('-').map(Number);
                        return Math.max(0, (e || 0) - (s || 0) + 1);
                    }
                    return Math.max(0, parseInt(v) || 0);
                },

                pageRange(idx) {
                    let running = 0;
                    for (let i = 0; i < idx; i++) running += this.pageCount(this.documents[i].pages);
                    const count = this.pageCount(this.documents[idx].pages);
                    if (!count) return '—';
                    const start = running + 1, end = running + count;
                    return start === end ? String(start) : start + '-' + end;
                },

                totalPages() {
                    return this.documents.reduce((sum, doc) => sum + this.pageCount(doc.pages), 0);
                },

                async save(status) {
                    this.saving = true;
                    const isFinal = status === 'Qaatay';
                    try {
                        const res = await fetch('{{ route('family-return-file.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                family_case_id:   this.caseId,
                                documents:        this.documents,
                                additional_notes: this.additionalNotes,
                                status:           status,
                            }),
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            await Swal.fire({
                                title: 'La Keydsaday!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'Hagaag',
                                confirmButtonColor: '#528CBE',
                                timer: isFinal ? 1600 : undefined,
                                showConfirmButton: !isFinal,
                                showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
                                hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' },
                            });
                            if (isFinal) window.location.href = '{{ route('family-return-file.index') }}';
                        } else {
                            throw new Error(data.message || 'Save failed.');
                        }
                    } catch (e) {
                        Swal.fire({ title: 'Khalad!', text: e.message, icon: 'error', confirmButtonColor: '#DC2626' });
                    } finally {
                        this.saving = false;
                    }
                },
            };
        }
    </script>

@endsection
