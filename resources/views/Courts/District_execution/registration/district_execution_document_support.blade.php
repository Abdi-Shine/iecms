@extends('admin.admin_master')
@section('page_title', 'Supporting Documents')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
@endpush

@section('admin_main_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-wrap" x-data="documentManager">

        {{-- Page Header --}}
        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div>
                    <h1 class="page-title">Dukumintiyada</h1>
                    <p class="page-sub">Dacwada<span class="page-sub-accent">{{ $case->FileNo }}</span></p>
                </div>
            </div>
            <a href="{{ route('execution-registration.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Dib U Noqo
            </a>
        </div>

        {{-- Shared datalist for all document cards --}}
        <datalist id="execution-doc-types">
            @foreach($docTypes as $type)
                <option value="{{ $type->Aname }}">
            @endforeach
        </datalist>

        <form @submit.prevent="saveDocs" enctype="multipart/form-data">

            <template x-for="(doc, index) in documents" :key="index">
                <div :id="'doc-card-'+index" class="entry-card">

                    {{-- Card Header (click to expand/collapse) --}}
                    <div class="card-hd" :style="index % 2 === 0 ? 'background:#528CBE' : 'background:#0A284D'"
                         @click.self="doc.isOpen = !doc.isOpen" style="cursor:pointer">
                        <div class="card-hd-left" @click="doc.isOpen = !doc.isOpen" style="cursor:pointer;flex:1">
                            <div class="card-hd-icon"><i class="bi bi-file-earmark-text"></i></div>
                            <div>
                                <h2 class="card-hd-title">Dukumintiga <span x-text="index + 1"></span></h2>
                                <p class="card-hd-sub" x-text="doc.document_name || 'New Document'"></p>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px">
                            <i class="bi"
                               :class="doc.isOpen ? 'bi-chevron-up' : 'bi-chevron-down'"
                               style="color:rgba(255,255,255,0.7);font-size:.75rem;pointer-events:none"></i>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-bd" x-show="doc.isOpen" x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="form-row-4">

                            {{-- Document Name --}}
                            <div>
                                <label class="form-lbl">Magaca Dukumintiga <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <input type="text"
                                           x-model="doc.document_name"
                                           list="execution-doc-types"
                                           required
                                           placeholder="Raadi oo dooro Dukumintiga..."
                                           class="form-sel"
                                           style="appearance:none"
                                           @focus="$el.style.borderColor='#528CBE';$el.style.boxShadow='0 0 0 3px rgba(82,140,190,0.12)'"
                                           @blur="$el.style.borderColor='';$el.style.boxShadow=''">
                                    <i class="bi bi-search inp-ico-xs" style="pointer-events:none"></i>
                                </div>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="form-lbl">Sharaxaad</label>
                                <div class="inp-wrap">
                                    <input type="text" x-model="doc.description" placeholder="Short description..."
                                        class="form-inp">
                                    <i class="bi bi-card-text inp-ico"></i>
                                </div>
                            </div>

                            {{-- Document File --}}
                            <div>
                                <div class="lbl-row">
                                    <label class="form-lbl">File-ka Dukumintiga <span class="req">*</span></label>
                                    <template x-if="doc.file_url">
                                        <a :href="doc.file_url" target="_blank" class="lbl-link">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </template>
                                </div>
                                <label :for="'file_'+index" class="upload-box-field">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <span x-text="doc.file_name || 'Choose File'"></span>
                                </label>
                                <input type="file" :id="'file_'+index" @change="handleFile($event, index)"
                                    style="display:none">
                            </div>

                            {{-- Document Date --}}
                            <div>
                                <label class="form-lbl">Taariikhda Dukumintiga</label>
                                <div class="inp-wrap">
                                    <input type="date" x-model="doc.document_date" class="form-inp">
                                    <i class="bi bi-calendar3 inp-ico"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </template>

            {{-- Form Actions --}}
            <div class="form-actions">
                <button type="button" @click="addDoc" class="btn-add">
                    <i class="bi bi-plus-lg"></i>Ku Dar Dukumintiyada
                </button>
                <button type="submit" :disabled="isSaving" class="btn-save">
                    <span x-show="!isSaving" class="btn-icon-row">
                        <i class="bi bi-cloud-check-fill"></i>Kaydi Dukumintiyada
                    </span>
                    <span x-show="isSaving" class="btn-icon-row">
                        <i class="bi bi-arrow-repeat cf-spin"></i> Kaydin...
                    </span>
                </button>
            </div>

        </form>

        {{-- Document Summary --}}
        <div class="data-card">
            <div class="data-card-hd">
                <div class="data-card-hd-left">
                    <i class="bi bi-table"></i>
                    <span class="data-card-hd-title"> Dukumintiga Kooban</span>
                </div>
                <span class="data-card-count" x-text="documents.length + ' Total'"></span>
            </div>

            <div class="data-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:3.5rem">#</th>
                            <th>Magaca Dukumintiga</th>
                            <th>Sharaxaad</th>
                            <th>File-ka Dukumintiga</th>
                            <th>Taariikhda</th>
                            <th class="center">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(doc, index) in documents" :key="index">
                            <tr>
                                <td><span class="td-num" x-text="index + 1"></span></td>
                                <td class="td-bold" x-text="doc.document_name || '—'"></td>
                                <td x-text="doc.description || '—'"></td>
                                <td>
                                    <template x-if="doc.file_url || doc.file_name">
                                        <div style="display:flex; align-items:center; gap:0.5rem">
                                            <i class="bi bi-paperclip" style="color:#9ca3af"></i>
                                            <template x-if="doc.file_url">
                                                <a :href="doc.file_url" target="_blank" style="font-size:0.75rem; font-weight:600; color:#528CBE; text-decoration:underline" x-text="doc.file_name || 'Document Attached'"></a>
                                            </template>
                                            <template x-if="!doc.file_url">
                                                <span style="font-size:0.75rem; font-weight:600; color:#528CBE" x-text="doc.file_name || 'Document Attached'"></span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!doc.file_url && !doc.file_name">
                                        <span style="font-size:0.75rem; color:#9ca3af; font-style:italic">No File</span>
                                    </template>
                                </td>
                                <td class="td-date" x-text="doc.document_date || '—'"></td>
                                <td>
                                    <div class="td-actions">
                                        <template x-if="doc.file_url">
                                            <a :href="doc.file_url" target="_blank" title="View"
                                                class="act-btn act-btn-edit">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </template>
                                        <button type="button"
                                            @click="document.getElementById('doc-card-'+index).scrollIntoView({behavior:'smooth'})"
                                            title="Edit" class="act-btn act-btn-edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" @click="removeDoc(index)" title="Remove"
                                            class="act-btn act-btn-del">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="documents.length === 0">
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-ico"><i class="bi bi-file-earmark-x"></i></div>
                                        <p class="empty-msg">No documents attached to this case yet.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="data-card-ft">
                <p>Showing <span x-text="documents.length"></span> <span
                        x-text="documents.length === 1 ? 'document' : 'documents'"></span></p>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('documentManager', () => ({
                caseId: {{ $case->ECID }},
                documents: [],
                isSaving: false,

                init() { this.loadDocs(); },

                async loadDocs() {
                    try {
                        const res = await fetch(`/execution-case-documents/case/${this.caseId}`);
                        const data = await res.json();
                        if (data.length > 0) {
                            this.documents = data.map(d => ({
                                DID: d.DID,
                                document_name: d.document_name,
                                document_date: d.document_date,
                                description: d.description,
                                file_name: d.file_path ? 'Document Attached' : '',
                                file_url: d.file_path ? `/storage/${d.file_path}` : '',
                                file: null,
                                isOpen: false
                            }));
                        } else {
                            this.addDoc();
                        }
                    } catch (e) {
                        this.addDoc();
                    }
                },

                addDoc() {
                    this.documents.forEach(d => d.isOpen = false);
                    const today = new Date().toISOString().split('T')[0];
                    this.documents.push({
                        document_name: '', document_date: today, description: '',
                        file_name: '', file_url: '', file: null, isOpen: true
                    });
                },

                removeDoc(index) {
                    const doc = this.documents[index];
                    Swal.fire({
                        title: 'Ma hubtaa?', text: "Dukumentigan waa laga saarayaa nidaamka!", icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#DC2626',
                        confirmButtonText: 'Haa, tirtir!', cancelButtonText: 'Jooji'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            if (doc.DID) {
                                try {
                                    await fetch(`/execution-case-documents/${doc.DID}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        }
                                    });
                                } catch (e) {
                                    Swal.fire('Khalad!', 'Tirtirku ma suurtagelin.', 'error'); return;
                                }
                            }
                            this.documents.splice(index, 1);
                            if (this.documents.length === 0) this.addDoc();
                            Swal.fire('La tirtiray!', 'Dukumentiga waa laga saaray.', 'success');
                        }
                    });
                },

                handleFile(event, index) {
                    const file = event.target.files[0];
                    if (file) {
                        this.documents[index].file = file;
                        this.documents[index].file_name = file.name;
                    }
                },

                async saveDocs() {
                    this.isSaving = true;
                    try {
                        const formData = new FormData();
                        formData.append('case_id', this.caseId);
                        this.documents.forEach((d, i) => {
                            Object.keys(d).forEach(key => {
                                if (d[key] !== null) formData.append(`documents[${i}][${key}]`, d[key]);
                            });
                        });
                        const res = await fetch('/execution-case-documents', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            Swal.fire('Guul!', data.message, 'success').then(() => window.location.reload());
                        } else {
                            throw new Error(data.message || 'Error saving');
                        }
                    } catch (e) {
                        Swal.fire('Khalad!', e.message, 'error');
                    } finally {
                        this.isSaving = false;
                    }
                }
            }));
        });
    </script>

@endsection