@extends('admin.admin_master')
@section('page_title', 'Archive — Approval Stamp')

@section('admin_main_content')

@php
    /** @var \Illuminate\Support\Collection $hearings */
    /** @var \Illuminate\Support\Collection $stampSigs */
    $stampSigs ??= collect();
@endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="p-4 sm:p-6 w-full">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Codsiga Summadda Archiifka</h1>
                <p class="text-sm text-neutral-500 mt-0.5">Codsiyada summadda ee Kaaliyaha gudbiyay — kuwa sugaya
                    oggolaanshaha Deeqa Maamulka</p>
            </div>
        </div>

        {{-- Stats --}}
        @php
            $statTotal    = $hearings->count();
            $statApproved = $hearings->filter(fn($h) => isset($stampSigs[$h->id]) && $stampSigs[$h->id]->count() >= 2)->count();
            $statPending  = $hearings->filter(fn($h) => isset($stampSigs[$h->id]) && $stampSigs[$h->id]->count() === 1)->count();
        @endphp
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">
            <div style="background:white;border-radius:1rem;padding:1.25rem 1.5rem;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.06)">
                <p style="font-size:.7rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;margin:0 0 .4rem">Wadarta</p>
                <p style="font-size:1.75rem;font-weight:800;color:#111827;margin:0">{{ $statTotal }}</p>
            </div>
            <div style="background:white;border-radius:1rem;padding:1.25rem 1.5rem;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.06)">
                <p style="font-size:.7rem;font-weight:800;color:#10b981;text-transform:uppercase;letter-spacing:.07em;margin:0 0 .4rem">La Ogolaaday</p>
                <p style="font-size:1.75rem;font-weight:800;color:#10b981;margin:0">{{ $statApproved }}</p>
            </div>
            <div style="background:white;border-radius:1rem;padding:1.25rem 1.5rem;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.06)">
                <p style="font-size:.7rem;font-weight:800;color:#d97706;text-transform:uppercase;letter-spacing:.07em;margin:0 0 .4rem">Sugaya</p>
                <p style="font-size:1.75rem;font-weight:800;color:#d97706;margin:0">{{ $statPending }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div
            style="background:white;border-radius:1rem;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06)">

            {{-- Table header bar --}}
            <div style="display:flex;align-items:center;gap:.75rem;padding:1rem 1.5rem;border-bottom:1px solid #f3f4f6">
                <div
                    style="width:36px;height:36px;background:#528CBE;border-radius:.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-archive" style="color:white;font-size:.95rem"></i>
                </div>
                <h2 style="font-size:.95rem;font-weight:700;color:#111827;margin:0">Liiska Mudeymaha</h2>
            </div>

            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:.85rem">
                    <thead>
                        <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb">
                            <th
                                style="padding:.75rem 1.25rem;text-align:left;font-size:.68rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em">
                                #</th>
                            <th
                                style="padding:.75rem 1.25rem;text-align:left;font-size:.68rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em">
                                Summada Dacwadda</th>
                            <th
                                style="padding:.75rem 1.25rem;text-align:left;font-size:.68rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em">
                                Nooca</th>
                            <th
                                style="padding:.75rem 1.25rem;text-align:left;font-size:.68rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em">
                                Taariikhda</th>
                            <th
                                style="padding:.75rem 1.25rem;text-align:left;font-size:.68rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em">
                                Maxkamadda</th>
                            <th
                                style="padding:.75rem 1.25rem;text-align:left;font-size:.68rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em">
                                Codsadaha</th>
                            <th
                                style="padding:.75rem 1.25rem;text-align:left;font-size:.68rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em">
                                Xaalada</th>
                            <th
                                style="padding:.75rem 1.25rem;text-align:right;font-size:.68rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hearings as $i => $h)
                            @php
                                $sigs = $stampSigs[$h->id] ?? collect();
                                $sigCount = $sigs->count();
                                $isApproved = $sigCount >= 2;
                                $isPending = $sigCount === 1;
                                $requesterSig = $sigs->firstWhere('role', 'kaaliye');
                                $requesterName = $requesterSig?->signer?->EmpName ?? '—';
                            @endphp
                            <tr style="border-bottom:1px solid #f3f4f6" onmouseover="this.style.background='#f9fafb'"
                                onmouseout="this.style.background='white'">
                                <td style="padding:.875rem 1.25rem;color:#9ca3af;font-size:.8rem">{{ $i + 1 }}</td>
                                <td style="padding:.875rem 1.25rem">
                                    <span style="font-weight:700;color:#111827">{{ $h->civilCase->FileNo ?? '—' }}</span>
                                </td>
                                <td style="padding:.875rem 1.25rem;color:#374151">{{ $h->civilCase->CaseType ?? '—' }}</td>
                                <td style="padding:.875rem 1.25rem;color:#374151">
                                    {{ $h->hearing_date->format('d/m/Y') }}
                                </td>
                                <td style="padding:.875rem 1.25rem;color:#374151">
                                    {{ $h->civilCase->court->shortName ?? '—' }}
                                </td>
                                <td style="padding:.875rem 1.25rem">
                                    <div style="font-weight:600;color:#111827;font-size:.83rem">{{ $requesterName }}</div>
                                    @if($requesterSig)
                                        <div style="font-size:.72rem;color:#9ca3af;margin-top:1px">
                                            {{ \Carbon\Carbon::parse($requesterSig->signed_at)->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding:.875rem 1.25rem">
                                    @if($isApproved)
                                        <span
                                            style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .75rem;font-size:.72rem;font-weight:700;color:#065f46;background:rgba(16,185,129,.1);border-radius:999px">
                                            <i class="bi bi-patch-check-fill"></i> La Ogolaaday
                                        </span>
                                    @elseif($isPending)
                                        <span
                                            style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .75rem;font-size:.72rem;font-weight:700;color:#92400e;background:rgba(245,158,11,.1);border-radius:999px">
                                            <i class="bi bi-hourglass-split"></i> Sugaya
                                        </span>
                                    @else
                                        <span
                                            style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .75rem;font-size:.72rem;font-weight:700;color:#6b7280;background:#f3f4f6;border-radius:999px">
                                            <i class="bi bi-circle"></i> Aan La Saxiixin
                                        </span>
                                    @endif
                                </td>
                                <td style="padding:.875rem 1.25rem;text-align:right">
                                    <a href="{{ route('hearings.approval.stamp', $h->id) }}"
                                        style="display:inline-flex;align-items:center;gap:.4rem;padding:.4rem 1rem;font-size:.78rem;font-weight:700;color:white;background:#528CBE;border-radius:.5rem;text-decoration:none;transition:opacity .15s"
                                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                        <i class="bi bi-eye"></i> Arag
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding:3rem;text-align:center;color:#9ca3af">
                                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem"></i>
                                    Wax codsi summad ah lama helin — Kaaliyaha kama gudbinin wax codsi ah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

@endsection