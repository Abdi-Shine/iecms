@extends('admin.admin_master')
@section('page_title', 'Courts Integration — Receive Cases')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/court-forms.css') }}">
@endpush

@section('admin_main_content')
    <div class="page-wrap">

        <div class="page-hd">
            <div class="page-hd-left">
                <div class="page-hd-icon"><i class="bi bi-box-arrow-in-left"></i></div>
                <div>
                    <h1 class="page-title">Dacwadaha La Helay</h1>
                    <p class="page-sub">Dacwadaha laga helay maxkamadaha kale</p>
                </div>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div style="display:flex;gap:0;border-bottom:2px solid #e5e7eb;margin-bottom:1.5rem">
            <a href="{{ route('transfer.index') }}"
               style="display:inline-flex;align-items:center;gap:.45rem;padding:.65rem 1.25rem;font-size:.82rem;font-weight:700;text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-2px;color:#6b7280"
               onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#6b7280'">
                <i class="bi bi-arrow-left-right"></i> Diyaarka Wareejinta
            </a>
            <a href="{{ route('courtsintergration.transfer') }}"
               style="display:inline-flex;align-items:center;gap:.45rem;padding:.65rem 1.25rem;font-size:.82rem;font-weight:700;text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-2px;color:#6b7280"
               onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#6b7280'">
                <i class="bi bi-box-arrow-right"></i> La Wareejiyay
            </a>
            <a href="{{ route('courtsintergration.recived') }}"
               style="display:inline-flex;align-items:center;gap:.45rem;padding:.65rem 1.25rem;font-size:.82rem;font-weight:700;text-decoration:none;border-bottom:2px solid #528CBE;margin-bottom:-2px;color:#528CBE">
                <i class="bi bi-box-arrow-in-left"></i> La Helay
            </a>
        </div>

        <div class="data-card">
            <form action="{{ route('courtsintergration.recived') }}" method="GET" class="reg-filter">
                {{-- Page size --}}
                <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                    <span>Show</span>
                    <select name="per_page" onchange="this.form.submit()"
                        class="px-3 py-1.5 text-sm font-semibold border border-neutral-300 rounded-full bg-white text-neutral-700
                               focus:outline-none focus:border-[#528CBE] cursor-pointer">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="reg-search-wrap">
                    <i class="bi bi-search reg-search-ico"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Raadi lambarka faylka..."
                        class="reg-search-inp">
                </div>
                <button type="submit" class="act-btn act-btn-edit" title="Search" style="width:auto;padding:0 .9rem;gap:.4rem;">
                    <i class="bi bi-search"></i> Search
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('courtsintergration.recived') }}" class="act-btn" title="Clear" style="width:auto;padding:0 .9rem;gap:.4rem;">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </form>

            <div class="reg-table-bar">
                <div class="reg-table-bar-left">
                    <i class="bi bi-table"></i>
                    <span class="reg-table-bar-title">Diiwaanka Dhagaysiga</span>
                </div>
                <span class="reg-table-count">{{ $records->total() }} diiwaan</span>
            </div>

            <div class="data-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:3.5rem">T.T</th>
                            <th>Lambarka Faylka</th>
                            <th>Nooca Dacwada</th>
                            <th>Maxkamadda Ka</th>
                            <th>Maxkamadda Loo</th>
                            <th>Taariikhda</th>
                            <th class="center" style="width:6rem">Ficilada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $i => $t)
                            <tr>
                                <td><span class="td-num">{{ str_pad($records->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}</span></td>
                                <td><span class="badge-file">{{ $t->civilCase?->FileNo ?? '—' }}</span></td>
                                <td><span class="badge-type">{{ $t->civilCase?->CaseType ?? '—' }}</span></td>
                                <td class="td-text">{{ $t->fromCourt?->longName ?? '—' }}</td>
                                <td class="td-text">{{ $t->toCourt?->longName ?? '—' }}</td>
                                <td class="td-date">{{ $t->transfer_date?->format('d/m/Y') ?? '—' }}</td>
                                <td>
                                    <div class="td-actions">
                                        @if($t->civilCase)
                                            <a href="{{ route('civil-registration.show', $t->civilCase->CRID) }}"
                                               title="Arag Dacwada" class="act-btn act-btn-edit">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-ico"><i class="bi bi-inbox-fill"></i></div>
                                        <p class="empty-msg">Dacwad la helay lama helin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="data-card-ft">
                <p>Muujinaya <span>{{ $records->count() }}</span> diiwaan ({{ $records->total() }} wadarta)</p>
            </div>

            <div class="px-6 py-4 border-t border-neutral-100">
                {{ $records->links() }}
            </div>
        </div>
    </div>
@endsection
