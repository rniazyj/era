
@extends('pelamar.profil.layout')

@section('title', 'Riwayat Lamaran - ERA')

@section('konten')

<style>
    /* ==============================
       RIWAYAT LAMARAN - TICKET CARD
    ============================== */

    .riwayat-list {
        display: grid;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    /* Header oranye */
    .ticket-header {
    position: relative;
    z-index: 0;
    min-height: 76px;
    padding: 1rem 1.35rem 1.8rem;
    background: linear-gradient(120deg, #fb923c, #f97316, #ea580c);
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    color: white;
}

    .ticket-type {
        font-size: .9rem;
        font-weight: 800;
        letter-spacing: .025em;
        display: flex;
        align-items: center;
        gap: .55rem;
    }

    .ticket-type svg {
        width: 19px;
        height: 19px;
        flex-shrink: 0;
    }

    .ticket-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .4rem .85rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 800;
        white-space: nowrap;
        background: rgba(255,255,255,.94);
    }

    .ticket-status.diterima {
        color: #15803d;
    }

    .ticket-status.ditolak {
        color: #b91c1c;
    }

    .ticket-status.diproses {
        color: #a16207;
    }

    .ticket-status.diajukan {
        color: #c2410c;
    }

    /* Isi card */
    .ticket-body {
    position: relative;
    z-index: 1;
    margin-top: -20px;
    padding: 1.35rem 1.35rem 1.1rem;
    background: #fff;
    border-radius: 24px 24px 0 0;
}

    .ticket-position {
        color: #18181b;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.45;
        margin-bottom: .35rem;
        overflow-wrap: anywhere;
    }

    .ticket-date {
        color: #85858e;
        font-size: .8rem;
        display: flex;
        align-items: center;
        gap: .45rem;
    }

    .ticket-date svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    /* Notifikasi LOA */
    .ticket-loa {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        margin-top: 1rem;
        padding: .8rem .9rem;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 13px;
    }

    .ticket-loa-row {
    display: flex;
    align-items: stretch;
    gap: .85rem;
    width: 100%;
    margin-top: 1rem;
}

.ticket-notifications {
    flex: 1;
    min-width: 0;
    width: 100%;
}

.ticket-notifications .ticket-loa,
.ticket-notifications .ticket-interview {
    width: 100%;
    box-sizing: border-box;
}

.ticket-notifications .ticket-loa {
    margin-top: 0;
}

.ticket-notifications .ticket-interview {
    margin-top: .65rem;
}

.ticket-loa-row .ticket-loa {
    flex: 1;
    min-width: 0;
    margin-top: 0;
}

.ticket-loa-row .ticket-detail {
    flex-shrink: 0;
    align-self: flex-end;
    margin-left: auto;
}

@media (max-width: 480px) {
    .ticket-loa-row {
        gap: .5rem;
    }

    .ticket-loa-row .ticket-detail {
        padding: .6rem .75rem;
        font-size: .72rem;
    }
}

    .ticket-loa svg {
        width: 19px;
        height: 19px;
        color: #15803d;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .ticket-loa strong {
        display: block;
        color: #166534;
        font-size: .78rem;
        font-weight: 800;
        margin-bottom: .2rem;
    }

    .ticket-loa span {
        display: block;
        color: #15803d;
        font-size: .73rem;
        line-height: 1.5;
    }

    /* Jadwal interview singkat */
    .ticket-interview {
        display: flex;
        align-items: center;
        gap: .65rem;
        margin-top: .85rem;
        padding: .75rem .9rem;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 13px;
    }

    .ticket-interview svg {
        width: 19px;
        height: 19px;
        color: #ea580c;
        flex-shrink: 0;
    }

    .ticket-interview-content {
        min-width: 0;
    }

    .ticket-interview strong {
        display: block;
        color: #c2410c;
        font-size: .76rem;
        font-weight: 800;
    }

    .ticket-interview span {
        display: block;
        color: #9a3412;
        font-size: .73rem;
        margin-top: .15rem;
    }

    .ticket-detail {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .65rem 1.15rem;
        background: #ffffff;
        border: 1px solid #000000;
        color: black;
        text-decoration: none;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        transition: background .25s ease, transform .25s ease;
    }

    .ticket-detail:hover {
        background: #ea580c;
        color: white;
        transform: translateX(3px);
    }

    .ticket-detail svg {
        width: 15px;
        height: 15px;
    }

    /* =========================================
   ANIMASI CARD RIWAYAT LAMARAN
========================================= */

.riwayat-ticket {
    position: relative;
    background: #fff;
    border: 1px solid rgba(0,0,0,.045);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(24, 24, 27, .045);

    opacity: 0;
    animation: ticketFadeUp .7s cubic-bezier(.2, .7, .3, 1) both;
    animation-delay: calc(var(--card-index, 0) * 120ms);

    transition:
        transform .35s cubic-bezier(.2, .7, .3, 1),
        box-shadow .35s ease;
}

.riwayat-ticket:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 35px rgba(249, 115, 22, .14);
}

/* Animasi muncul dari bawah */
@keyframes ticketFadeUp {
    from {
        opacity: 0;
        transform: translateY(25px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Header oranye ikut berubah saat card di-hover */
.ticket-header {
    transition: filter .35s ease;
}

.riwayat-ticket:hover .ticket-header {
    filter: brightness(1.06);
}

/* Tombol detail */
.ticket-detail {
    transition:
        background .3s ease,
        color .3s ease,
        transform .3s ease,
        box-shadow .3s ease;
}

.ticket-detail:hover {
    background: #000000;
    color: white;
    transform: translateX(4px);
    box-shadow: 0 5px 14px rgba(234, 88, 12, .2);
}

/* Animasi filter */
.riwayat-filter .chip {
    transition: transform .25s ease, background .25s ease;
}

.riwayat-filter .chip:hover {
    transform: translateY(-3px);
}

/* Menghormati pengaturan reduce motion */
@media (prefers-reduced-motion: reduce) {
    .riwayat-ticket {
        opacity: 1;
        animation: none;
        transition: none;
    }

    .riwayat-ticket:hover,
    .ticket-detail:hover,
    .riwayat-filter .chip:hover {
        transform: none;
    }

    .ticket-header,
    .ticket-detail {
        transition: none;
    }
}

    /* Filter */
    .riwayat-filter {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
        margin-top: 1.25rem;
    }

    .riwayat-filter .chip {
        transition: all .25s ease;
    }

    .riwayat-filter .chip:hover {
        transform: translateY(-2px);
    }

    /* Responsif */
    @media (max-width: 480px) {
        .riwayat-ticket {
            border-radius: 19px;
        }

        .ticket-header {
    transition: filter .35s ease;
}

        .ticket-body {
            padding: 1rem;
        }

        .ticket-position {
            font-size: 1.02rem;
        }

        .ticket-type {
            font-size: .8rem;
        }

        .ticket-status {
            font-size: .67rem;
            padding: .35rem .7rem;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .riwayat-ticket {
            animation: none;
            transition: none;
        }

        .riwayat-ticket:hover {
            transform: none;
        }
    }
</style>

<section class="card" id="riwayat-lamaran">

    {{-- Header halaman --}}
    <div class="card-head">
        <div class="ico">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 6h13M8 12h13M8 18h13"/>
                <path d="M3 6h.01M3 12h.01M3 18h.01"/>
            </svg>
        </div>

        <div class="grow">
            <h3>Riwayat Lamaran</h3>
            <p>Pantau perkembangan lamaran kerja dan program magang yang telah kamu ajukan.</p>
        </div>
    </div>

    {{-- Filter jenis lamaran --}}
    <div class="riwayat-filter">
        @foreach([
            'semua' => 'Semua',
            'kerja' => 'Kerja',
            'magang' => 'Magang',
        ] as $key => $label)

            <a href="{{ route('pelamar.profil.lamaran', ['jenis' => $key]) }}"
                class="chip"
                style="{{ $jenis === $key
                    ? 'background:#000000;color:#fff;border-color:#000000;'
                    : 'background:#f4f4f6;color:#3b3b44;' }}
                    text-decoration:none;padding:.55rem 1rem;">

                {{ $label }}

            </a>
        @endforeach
    </div>

    {{-- Daftar lamaran --}}
    <div class="riwayat-list">

        @forelse($daftar as $row)

            @php
                $status = strtolower($row->status_lamaran ?? 'diajukan');

                $statusLabel = match($status) {
                    'diterima' => 'Diterima',
                    'ditolak' => 'Ditolak',
                    'diproses' => 'Diproses',
                    default => 'Diajukan',
                };

                $isMagang = !empty($row->id_program);

                $loaTerbit = (
                    $isMagang &&
                    $status === 'diterima' &&
                    $row->surat &&
                    $row->surat->status_penerbitan === 'terbit' &&
                    filled($row->surat->file_surat) &&
                    \Illuminate\Support\Facades\Storage::disk('public')->exists($row->surat->file_surat)
                );

                $interviewTerjadwal = (
                    $row->jadwal &&
                    $row->jadwal->status_jadwal === 'terjadwal'
                );
            @endphp

            <article class="riwayat-ticket"
                style="--card-index: {{ $loop->index }}">

                {{-- BAGIAN HEADER ORANYE --}}
                <div class="ticket-header">

                    <div class="ticket-type">

                        @if($isMagang)
                            <svg viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18"/>
                                <path d="M5 21V7l8-4v18"/>
                                <path d="M19 21V11l-6-4"/>
                                <path d="M9 9v.01M9 12v.01M9 15v.01M9 18v.01"/>
                            </svg>
                            Magang
                        @else
                            <svg viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="7" width="18" height="14" rx="2"/>
                                <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                <path d="M3 12h18M10 12v2h4v-2"/>
                            </svg>
                            Kerja
                        @endif

                    </div>

                    <span class="ticket-status {{ $status }}">
                        {{ $statusLabel }}
                    </span>

                </div>

                {{-- BAGIAN PUTIH --}}
                <div class="ticket-body">

                    {{-- Posisi --}}
                    <h4 class="ticket-position">
                        {{ $row->nama_posisi }}
                    </h4>

                    {{-- Tanggal pengajuan --}}
                    <div class="ticket-date">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>

                        Diajukan:
                        {{ $row->tgl_pengajuan
                            ? \Illuminate\Support\Carbon::parse($row->tgl_pengajuan)->translatedFormat('d F Y')
                            : '-' }}
                    </div>

                    
{{-- Notifikasi LOA, Jadwal Interview, dan tombol Detail --}}
<div class="ticket-loa-row">

    <div class="ticket-notifications">

        {{-- Notifikasi LOA --}}
        @if($loaTerbit)
            <div class="ticket-loa">

                <svg viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <path d="M14 2v6h6"/>
                    <path d="m9 15 2 2 2 2 4-4"/>
                </svg>

                <div>
                    <strong>LOA Sudah Diterbitkan!</strong>
                    <span>
                        Surat penerimaan magang kamu sudah tersedia.
                        Silakan buka detail untuk melihat dan mengunduh LOA.
                    </span>
                </div>

            </div>
        @endif

        {{-- Notifikasi Jadwal Interview --}}
        @if($interviewTerjadwal)
            <div class="ticket-interview">

                <svg viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>

                <div class="ticket-interview-content">
                    <strong>Interview Terjadwal</strong>

                    <span>
                        {{ $row->jadwal->tgl_interview
                            ? \Illuminate\Support\Carbon::parse($row->jadwal->tgl_interview)->translatedFormat('d F Y')
                            : '-' }}

                        @if($row->jadwal->jam_mulai)
                            · {{ \Illuminate\Support\Carbon::parse($row->jadwal->jam_mulai)->format('H:i') }} WIB
                        @endif
                    </span>
                </div>

            </div>
        @endif

    </div>

    {{-- Tombol Detail --}}
    <a href="{{ route('pelamar.profil.lamaran.show', $row->id_lamaran) }}"
        class="ticket-detail">

        Detail

        <svg viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>

    </a>

</div>


                </div>

            </article>

        @empty

            <div class="empty">

                <svg width="48" height="48" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round"
                    style="margin:0 auto .8rem;color:#c4c4cc">

                    <rect x="4" y="3" width="16" height="18" rx="2"/>
                    <path d="M8 8h8M8 12h8M8 16h4"/>

                </svg>

                @if($jenis === 'kerja')
                    <p>Belum ada riwayat lamaran pekerjaan.</p>

                @elseif($jenis === 'magang')
                    <p>Belum ada riwayat pendaftaran program magang.</p>

                @else
                    <p>Kamu belum mengajukan lamaran kerja maupun magang.</p>

                    <p style="margin-top:.35rem">
                        Silakan lihat lowongan kerja atau program magang yang tersedia.
                    </p>
                @endif

            </div>

        @endforelse

    </div>

    {{-- Pagination --}}
    @if($daftar->hasPages())
        <div style="margin-top:1.5rem">
            {{ $daftar->links() }}
        </div>
    @endif

</section>

@endsection
