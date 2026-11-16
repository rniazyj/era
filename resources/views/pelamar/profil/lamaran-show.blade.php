
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
        'diterima' => 'background:#f0fdf4;color:#166534;',
        'ditolak' => 'background:#fef2f2;color:#991b1b;',
        'diproses' => 'background:#fffbeb;color:#92400e;',
        default => 'background:#fff4ea;color:#ea580c;',
    };
@endphp

<section class="card">

    <div class="card-head">
        <div class="ico">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

    {{-- Ringkasan --}}
    <div class="item">
        <h4>{{ $lamaran->nama_posisi }}</h4>

        <div class="sub">
            {{ $lamaran->jenis_lamaran }}
        </div>

        <div class="meta">
            Nomor Lamaran: #{{ $lamaran->id_lamaran }}
        </div>

        <div style="margin-top:.65rem">
            <span style="{{ $statusClass }}display:inline-flex;align-items:center;padding:.35rem .85rem;border-radius:99px;font-size:.75rem;font-weight:700">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    {{-- Informasi pengajuan --}}
    <div style="margin-top:1.5rem">
        <div class="card-head">
            <div class="grow">
                <h3>Informasi Pengajuan</h3>
                <p>Rincian waktu dan jenis pengajuan.</p>
            </div>
        </div>

        <div class="item">
            <div class="meta">Jenis Lamaran</div>
            <h4>{{ $lamaran->jenis_lamaran }}</h4>
        </div>

        <div class="item">
            <div class="meta">Posisi / Program</div>
            <h4>{{ $lamaran->nama_posisi }}</h4>
        </div>

        <div class="item">
            <div class="meta">Tanggal Pengajuan</div>
            <h4>
                {{ $lamaran->tgl_pengajuan
                    ? \Illuminate\Support\Carbon::parse($lamaran->tgl_pengajuan)->translatedFormat('d F Y')
                    : '-' }}
            </h4>
        </div>

        @if($lamaran->id_program)
            <div class="item">
                <div class="meta">Tanggal Mulai Magang</div>
                <h4>
                    {{ $lamaran->tgl_mulai_magang
                        ? \Illuminate\Support\Carbon::parse($lamaran->tgl_mulai_magang)->translatedFormat('d F Y')
                        : '-' }}
                </h4>
            </div>

            <div class="item">
                <div class="meta">Tanggal Selesai Magang</div>
                <h4>
                    {{ $lamaran->tgl_selesai_magang
                        ? \Illuminate\Support\Carbon::parse($lamaran->tgl_selesai_magang)->translatedFormat('d F Y')
                        : '-' }}
                </h4>
            </div>

            <div class="item">
                <div class="meta">Durasi Magang</div>
                <h4>{{ $lamaran->durasi_hari ? $lamaran->durasi_hari . ' hari' : '-' }}</h4>
            </div>
        @endif

        <div class="item">
            <div class="meta">Tanggal Keputusan</div>
            <h4>
                {{ $lamaran->tgl_keputusan
                    ? \Illuminate\Support\Carbon::parse($lamaran->tgl_keputusan)->translatedFormat('d F Y')
                    : 'Belum ada keputusan' }}
            </h4>
        </div>

        <div class="item">
            <div class="meta">Hasil Seleksi</div>
            <h4>{{ $lamaran->hasil_seleksi ?: 'Belum ada hasil seleksi' }}</h4>
        </div>
    </div>

    {{-- Catatan HR --}}
    @if(filled($lamaran->catatan_seleksi))
        <div style="margin-top:1.5rem">
            <div class="card-head">
                <div class="grow">
                    <h3>Catatan dari HR</h3>
                    <p>Informasi tambahan terkait proses seleksi.</p>
                </div>
            </div>

            <div class="item">
                <p class="desc">{{ $lamaran->catatan_seleksi }}</p>
            </div>
        </div>
    @endif

</section>
@endsection
