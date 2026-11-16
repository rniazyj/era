
@extends('pelamar.profil.layout')

@section('title', 'Riwayat Lamaran - ERA')

@section('konten')
<section class="card" id="riwayat-lamaran">

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
    <div class="chips">
        @foreach([
            'semua' => 'Semua',
            'kerja' => 'Kerja',
            'magang' => 'Magang',
        ] as $key => $label)
            <a href="{{ route('pelamar.profil.lamaran', ['jenis' => $key]) }}"
                class="chip"
                style="{{ $jenis === $key ? 'background:#17171a;color:#fff;' : 'background:#f4f4f6;color:#3b3b44;' }} text-decoration:none;padding:.55rem 1rem;">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Daftar lamaran --}}
    @forelse($daftar as $row)
        @php
            $status = strtolower($row->status_lamaran ?? 'diajukan');

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

        <div class="item">
            <div class="item-top">

                <div style="min-width:0;flex:1">
                    <h4>{{ $row->nama_posisi }}</h4>

                    <div class="sub">
                        {{ $row->jenis_lamaran }}
                    </div>

                    <div class="meta">
                        Diajukan:
                        {{ $row->tgl_pengajuan
                            ? \Illuminate\Support\Carbon::parse($row->tgl_pengajuan)->translatedFormat('d F Y')
                            : '-' }}
                    </div>

                    <div style="margin-top:.65rem">
                        <span style="{{ $statusClass }}display:inline-flex;align-items:center;padding:.3rem .8rem;border-radius:99px;font-size:.72rem;font-weight:700">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <div class="item-actions">
                    <a href="{{ route('pelamar.profil.lamaran.show', $row->id_lamaran) }}"
                        class="btn btn-outline btn-sm">
                        Detail
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

            </div>
        </div>

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

    {{-- Pagination --}}
    @if($daftar->hasPages())
        <div style="margin-top:1.25rem">
            {{ $daftar->links() }}
        </div>
    @endif

</section>
@endsection
