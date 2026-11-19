
@extends('pelamar.profil.layout')

@section('title', 'Detail Lamaran - ERA')

@section('konten')

@php
    $status = strtolower($lamaran->status_lamaran ?? 'diajukan');

    $statusLabel = match($status) {
        'diterima' => 'Diterima',
        'ditolak' => 'Ditolak',
        'diproses' => 'Sedang Diproses',
        default => 'Diajukan',
    };

    $statusClass = match($status) {
        'diterima' => 'diterima',
        'ditolak' => 'ditolak',
        'diproses' => 'diproses',
        default => 'diajukan',
    };

    $loaTersedia = (
        $lamaran->id_program &&
        $status === 'diterima' &&
        $lamaran->surat &&
        $lamaran->surat->status_penerbitan === 'terbit' &&
        filled($lamaran->surat->file_surat) &&
        \Illuminate\Support\Facades\Storage::disk('public')->exists($lamaran->surat->file_surat)
    );
@endphp

<style>
    [x-cloak] {
        display: none !important;
    }

    .detail-wrapper {
        margin-top: 1.25rem;
    }

    /* Ringkasan lamaran */
    .detail-summary {
        position: relative;
        padding: 1.4rem;
        border: 1px solid #f1f1f3;
        border-radius: 20px;
        background: linear-gradient(135deg, #fff, #fffaf5);
        overflow: hidden;
    }

    .detail-summary::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #fb923c, #ea580c);
    }

    .summary-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .summary-type {
        color: #ea580c;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-bottom: .4rem;
    }

    .summary-title {
        font-size: 1.3rem;
        line-height: 1.4;
        font-weight: 800;
        color: #18181b;
        overflow-wrap: anywhere;
    }

    .summary-number {
        color: #85858e;
        font-size: .78rem;
        margin-top: .4rem;
    }

    .summary-status {
        display: inline-flex;
        align-items: center;
        padding: .45rem .9rem;
        border-radius: 999px;
        font-size: .73rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .summary-status.diterima {
        background: #f0fdf4;
        color: #166534;
    }

    .summary-status.ditolak {
        background: #fef2f2;
        color: #991b1b;
    }

    .summary-status.diproses {
        background: #fffbeb;
        color: #92400e;
    }

    .summary-status.diajukan {
        background: #fff4ea;
        color: #ea580c;
    }

    /* Accordion */
    .detail-accordion-list {
        display: flex;
        flex-direction: column;
        gap: .75rem;
        margin-top: 1.25rem;
    }

    .detail-accordion {
        border: 1px solid #ededf0;
        border-radius: 17px;
        background: #fff;
        overflow: hidden;
        transition: border-color .25s ease, box-shadow .25s ease;
    }

    .detail-accordion:has(.accordion-trigger[aria-expanded="true"]) {
        border-color: #fed7aa;
        box-shadow: 0 5px 18px rgba(249, 115, 22, .055);
    }

    .accordion-trigger {
        width: 100%;
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: 1.1rem 1.2rem;
        border: none;
        background: transparent;
        text-align: left;
        cursor: pointer;
    }

    .accordion-icon {
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #fff4ea;
        color: #ea580c;
    }

    .accordion-icon svg {
        width: 21px;
        height: 21px;
    }

    .accordion-heading {
        flex: 1;
        min-width: 0;
    }

    .accordion-heading strong {
        display: block;
        color: #27272a;
        font-size: .9rem;
        font-weight: 800;
    }

    .accordion-heading span {
        display: block;
        color: #92929b;
        font-size: .74rem;
        margin-top: .2rem;
        line-height: 1.45;
    }

    .accordion-arrow {
        width: 19px;
        height: 19px;
        flex-shrink: 0;
        color: #92929b;
        transition: transform .3s ease, color .3s ease;
    }

    .accordion-trigger[aria-expanded="true"] .accordion-arrow {
        transform: rotate(180deg);
        color: #ea580c;
    }

    .accordion-content {
        padding: 0 1.2rem 1.25rem;
    }

    .accordion-divider {
        height: 1px;
        background: #f0f0f2;
        margin-bottom: 1.1rem;
    }

    /* Isi informasi */
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem 1.5rem;
    }

    .detail-field {
        min-width: 0;
    }

    .detail-field.full {
        grid-column: 1 / -1;
    }

    .detail-label {
        color: #92929b;
        font-size: .73rem;
        font-weight: 600;
        margin-bottom: .3rem;
    }

    .detail-value {
        color: #27272a;
        font-size: .85rem;
        font-weight: 700;
        line-height: 1.55;
        overflow-wrap: anywhere;
    }

    .detail-description {
        color: #55555f;
        font-size: .84rem;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    /* LOA */
    .loa-box {
        padding: 1rem;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        border-radius: 14px;
    }

    .loa-title {
        color: #166534;
        font-size: .88rem;
        font-weight: 800;
    }

    .loa-meta {
        color: #15803d;
        font-size: .75rem;
        margin-top: .3rem;
        line-height: 1.6;
    }

    .loa-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
        margin-top: 1rem;
    }

    .loa-actions a {
        text-decoration: none;
    }

    /* Kondisi kosong */
    .detail-empty {
        padding: 1rem;
        background: #fafafa;
        border: 1px dashed #e4e4e7;
        border-radius: 13px;
        color: #71717a;
        font-size: .82rem;
        line-height: 1.7;
    }


/* Animasi card accordion */
.detail-accordion {
    border: 1px solid #ededf0;
    border-radius: 17px;
    background: #fff;
    overflow: hidden;
    transition:
        border-color .35s ease,
        box-shadow .35s ease,
        transform .35s ease;
}

.detail-accordion:has(.accordion-trigger[aria-expanded="true"]) {
    border-color: #fed7aa;
    box-shadow: 0 7px 22px rgba(249, 115, 22, .08);
}

/* Efek hover */
@media (hover: hover) {
    .detail-accordion:hover {
        border-color: #fed7aa;
        transform: translateY(-2px);
    }
}

/* Animasi isi accordion */
.accordion-content {
    padding: 0 1.2rem 1.25rem;
    transform-origin: top center;
}

/* Gerakan ikon */
.accordion-arrow {
    transition:
        transform .4s cubic-bezier(.22, 1, .36, 1),
        color .3s ease;
}

.accordion-trigger[aria-expanded="true"] .accordion-arrow {
    transform: rotate(180deg);
    color: #ea580c;
}

@media (prefers-reduced-motion: reduce) {
    .detail-accordion,
    .accordion-arrow {
        transition: none !important;
    }
}

    @media (max-width: 600px) {
        .detail-summary {
            padding: 1.1rem;
        }

        .summary-title {
            font-size: 1.1rem;
        }

        .accordion-trigger {
            padding: .95rem;
            gap: .7rem;
        }

        .accordion-content {
            padding: 0 .95rem 1rem;
        }

        .accordion-icon {
            width: 38px;
            height: 38px;
        }

        .detail-grid {
            grid-template-columns: 1fr;
            gap: .9rem;
        }

        .detail-field.full {
            grid-column: auto;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .detail-accordion,
        .accordion-arrow {
            transition: none;
        }
    }

    /* Samakan font seluruh elemen halaman detail lamaran */
.detail-summary,
.detail-accordion-list,
.detail-accordion,
.detail-accordion button,
.detail-accordion span,
.detail-accordion p,
.detail-accordion h3,
.detail-accordion h4,
.detail-accordion div,
.detail-accordion a {
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}

.detail-accordion button {
    font-size: inherit;
}
</style>

<section class="card"
    x-data="{ openPanel: null }">

    {{-- HEADER HALAMAN --}}
    <div class="card-head">
        <div class="ico">
            <svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <path d="M14 2v6h6M8 13h8M8 17h8"/>
            </svg>
        </div>

        <div class="grow">
            <h3>Detail Lamaran</h3>
            <p>Informasi lengkap mengenai pengajuan lamaran kamu.</p>
        </div>

        <a href="{{ route('pelamar.profil.lamaran') }}"
            class="btn btn-outline btn-sm">
            Kembali
        </a>
    </div>

    <div class="detail-wrapper">

        {{-- RINGKASAN LAMARAN --}}
        <div class="detail-summary">
            <div class="summary-top">
                <div style="min-width:0">
                    <div class="summary-type">
                        {{ $lamaran->jenis_lamaran }}
                    </div>

                    <h4 class="summary-title">
                        {{ $lamaran->nama_posisi }}
                    </h4>

                    <div class="summary-number">
                        Nomor Lamaran: #{{ $lamaran->id_lamaran }}
                    </div>
                </div>

                <span class="summary-status {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>

        <div class="detail-accordion-list">

            {{-- INFORMASI PENGAJUAN --}}
            <div class="detail-accordion">

                <button type="button"
                    class="accordion-trigger"
                    @click="openPanel = openPanel === 'pengajuan' ? null : 'pengajuan'"
                    :aria-expanded="openPanel === 'pengajuan'">

                    <div class="accordion-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 6h13M8 12h13M8 18h13"/>
                            <path d="M3 6h.01M3 12h.01M3 18h.01"/>
                        </svg>
                    </div>

                    <div class="accordion-heading">
                        <strong>Informasi Pengajuan</strong>
                        <span>Rincian pengajuan dan proses seleksi lamaran.</span>
                    </div>

                    <svg class="accordion-arrow" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>

                <div class="accordion-content"
    x-show="openPanel === 'pengajuan'"
    x-collapse.duration.400ms
    x-cloak>

                    <div class="accordion-divider"></div>

                    <div class="detail-grid">

                        <div class="detail-field">
                            <div class="detail-label">Jenis Lamaran</div>
                            <div class="detail-value">
                                {{ $lamaran->jenis_lamaran }}
                            </div>
                        </div>

                        <div class="detail-field">
                            <div class="detail-label">Posisi / Program</div>
                            <div class="detail-value">
                                {{ $lamaran->nama_posisi }}
                            </div>
                        </div>

                        <div class="detail-field">
                            <div class="detail-label">Tanggal Pengajuan</div>
                            <div class="detail-value">
                                {{ $lamaran->tgl_pengajuan
                                    ? \Illuminate\Support\Carbon::parse($lamaran->tgl_pengajuan)->translatedFormat('d F Y')
                                    : '-' }}
                            </div>
                        </div>

                        @if($lamaran->id_program)
                            <div class="detail-field">
                                <div class="detail-label">Tanggal Mulai Magang</div>
                                <div class="detail-value">
                                    {{ $lamaran->tgl_mulai_magang
                                        ? \Illuminate\Support\Carbon::parse($lamaran->tgl_mulai_magang)->translatedFormat('d F Y')
                                        : '-' }}
                                </div>
                            </div>

                            <div class="detail-field">
                                <div class="detail-label">Tanggal Selesai Magang</div>
                                <div class="detail-value">
                                    {{ $lamaran->tgl_selesai_magang
                                        ? \Illuminate\Support\Carbon::parse($lamaran->tgl_selesai_magang)->translatedFormat('d F Y')
                                        : '-' }}
                                </div>
                            </div>

                            <div class="detail-field">
                                <div class="detail-label">Durasi Magang</div>
                                <div class="detail-value">
                                    {{ $lamaran->durasi_hari ? $lamaran->durasi_hari . ' hari' : '-' }}
                                </div>
                            </div>
                        @endif

                        <div class="detail-field">
                            <div class="detail-label">Tanggal Keputusan</div>
                            <div class="detail-value">
                                {{ $lamaran->tgl_keputusan
                                    ? \Illuminate\Support\Carbon::parse($lamaran->tgl_keputusan)->translatedFormat('d F Y')
                                    : 'Belum ada keputusan' }}
                            </div>
                        </div>

                        <div class="detail-field">
                            <div class="detail-label">Hasil Seleksi</div>
                            <div class="detail-value">
                                {{ $lamaran->hasil_seleksi ?: 'Belum ada hasil seleksi' }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- JADWAL INTERVIEW --}}
            <div class="detail-accordion">

                <button type="button"
                    class="accordion-trigger"
                    @click="openPanel = openPanel === 'interview' ? null : 'interview'"
                    :aria-expanded="openPanel === 'interview'">

                    <div class="accordion-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                    </div>

                    <div class="accordion-heading">
                        <strong>Jadwal Interview</strong>
                        <span>Informasi jadwal wawancara dari tim HR.</span>
                    </div>

                    <svg class="accordion-arrow" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>

                <div class="accordion-content"
    x-show="openPanel === 'interview'"
    x-collapse.duration.400ms
    x-cloak>

                    <div class="accordion-divider"></div>

                    @if($lamaran->jadwal)

                        @php
                            $jadwal = $lamaran->jadwal;
                        @endphp

                        <div class="detail-grid">

                            <div class="detail-field">
                                <div class="detail-label">Status Jadwal</div>
                                <div class="detail-value">
                                    {{ ucfirst($jadwal->status_jadwal ?? 'Terjadwal') }}
                                </div>
                            </div>

                            <div class="detail-field">
                                <div class="detail-label">Tanggal Interview</div>
                                <div class="detail-value">
                                    {{ $jadwal->tgl_interview
                                        ? \Illuminate\Support\Carbon::parse($jadwal->tgl_interview)->translatedFormat('l, d F Y')
                                        : '-' }}
                                </div>
                            </div>

                            <div class="detail-field">
                                <div class="detail-label">Jam Interview</div>
                                <div class="detail-value">
                                    {{ $jadwal->jam_mulai
                                        ? \Illuminate\Support\Carbon::parse($jadwal->jam_mulai)->format('H:i') . ' WIB'
                                        : '-' }}
                                </div>
                            </div>

                            <div class="detail-field full">
                                <div class="detail-label">Lokasi / Link Interview</div>
                                <div class="detail-value">
                                    {{ $jadwal->lokasi_atau_link ?: '-' }}
                                </div>
                            </div>

                            @if(filled($jadwal->catatan))
                                <div class="detail-field full">
                                    <div class="detail-label">Catatan Interview</div>
                                    <p class="detail-description">
                                        {{ $jadwal->catatan }}
                                    </p>
                                </div>
                            @endif

                        </div>

                    @else

                        <div class="detail-empty">
                            Jadwal interview belum ditentukan oleh tim HR.
                            Silakan periksa kembali halaman ini secara berkala.
                        </div>

                    @endif
                </div>
            </div>

            {{-- SURAT PENERIMAAN MAGANG --}}
            @if($lamaran->id_program && $status === 'diterima')
                <div class="detail-accordion">

                    <button type="button"
                        class="accordion-trigger"
                        @click="openPanel = openPanel === 'loa' ? null : 'loa'"
                        :aria-expanded="openPanel === 'loa'">

                        <div class="accordion-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <path d="M14 2v6h6M8 13h8M8 17h8"/>
                            </svg>
                        </div>

                        <div class="accordion-heading">
                            <strong>Surat Penerimaan Magang</strong>
                            <span>Letter of Acceptance (LOA) dari PT Equityworld Futures.</span>
                        </div>

                        <svg class="accordion-arrow" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div class="accordion-content"
    x-show="openPanel === 'loa'"
    x-collapse.duration.400ms
    x-cloak>

                        <div class="accordion-divider"></div>

                        @if($loaTersedia)

                            <div class="loa-box">
                                <div class="loa-title">
                                    <svg style="width:17px;height:17px;display:inline;vertical-align:middle;margin-right:.25rem"
                                        viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m5 12 4 4L19 6"/>
                                    </svg>
                                    LOA Berhasil Diterbitkan
                                </div>

                                <div class="loa-meta">
                                    Nomor Surat: {{ $lamaran->surat->nomor_surat }}
                                </div>

                                <div class="loa-meta">
                                    Tanggal Terbit:
                                    {{ $lamaran->surat->tgl_terbit
                                        ? \Illuminate\Support\Carbon::parse($lamaran->surat->tgl_terbit)->translatedFormat('d F Y')
                                        : '-' }}
                                </div>

                                <div class="loa-actions">

                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($lamaran->surat->file_surat) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="btn btn-outline btn-sm">
                                        Lihat Surat
                                    </a>

                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($lamaran->surat->file_surat) }}"
                                        download="LOA-Magang-{{ $lamaran->id_lamaran }}.pdf"
                                        class="btn btn-sm">
                                        Unduh PDF
                                    </a>

                                </div>
                            </div>

                        @else

                            <div class="detail-empty">
                                Selamat! Kamu telah dinyatakan diterima dalam program magang.
                                Surat Penerimaan Magang (LOA) sedang dipersiapkan oleh tim HR
                                atau belum diterbitkan. Silakan periksa kembali halaman ini secara berkala.
                            </div>

                        @endif
                    </div>
                </div>
            @endif

            {{-- CATATAN HR --}}
            @if(filled($lamaran->catatan_seleksi))
                <div class="detail-accordion">

                    <button type="button"
                        class="accordion-trigger"
                        @click="openPanel = openPanel === 'catatan' ? null : 'catatan'"
                        :aria-expanded="openPanel === 'catatan'">

                        <div class="accordion-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"/>
                            </svg>
                        </div>

                        <div class="accordion-heading">
                            <strong>Catatan dari HR</strong>
                            <span>Informasi tambahan terkait proses seleksi.</span>
                        </div>

                        <svg class="accordion-arrow" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div class="accordion-content"
    x-show="openPanel === 'catatan'"
    x-collapse.duration.400ms
    x-cloak>

                        <div class="accordion-divider"></div>

                        <p class="detail-description">
                            {{ $lamaran->catatan_seleksi }}
                        </p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</section>

@endsection
