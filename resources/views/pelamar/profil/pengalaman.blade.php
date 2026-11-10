@extends('pelamar.profil.layout')

@section('title', 'Pengalaman - ERA')

@php
  $tgl = fn ($d) => $d ? \Carbon\Carbon::parse($d)->locale('id')->translatedFormat('d M Y') : null;

  $rentang = function ($row) use ($tgl) {
    $selesai = $row->masih_berjalan ? 'Sekarang' : $tgl($row->tgl_selesai);
    return trim($tgl($row->tgl_mulai) . ($selesai ? ' - ' . $selesai : ''));
  };

  $baru = [
    'organisasi'  => new \App\Models\PengalamanOrganisasi(),
    'profesional' => new \App\Models\PengalamanProfesional(),
    'proyek'      => new \App\Models\Proyek(),
  ];

  $bagian = [
    'profesional' => [
      'judul' => 'Pengalaman Profesional',
      'sub'   => 'Pekerjaan, magang, atau freelance',
      'data'  => $profesional,
      'pk'    => 'id_pengalaman',
      'icon'  => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
    ],
    'organisasi' => [
      'judul' => 'Pengalaman Organisasi',
      'sub'   => 'Tampil di bagian "Pengalaman Organisasi" pada CV',
      'data'  => $organisasi,
      'pk'    => 'id_organisasi',
      'icon'  => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
    ],
    'proyek' => [
      'judul' => 'Pengalaman Proyek',
      'sub'   => 'Tampil di bagian "Pengalaman Proyek" pada CV',
      'data'  => $proyek,
      'pk'    => 'id_proyek',
      'icon'  => '<path d="m16 18 6-6-6-6M8 6l-6 6 6 6"/>',
    ],
  ];
@endphp

@section('konten')
<div class="stack">
  @foreach($bagian as $jenis => $b)
    <section class="card" id="{{ $jenis }}">
      <div class="card-head">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $b['icon'] !!}</svg></div>
        <div class="grow">
          <h3>{{ $b['judul'] }}</h3>
          <p>{{ $b['sub'] }}</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="tambah-{{ $jenis }}">+ Tambah</button>
      </div>

      <div class="collapse form-box {{ old('_form') === "tambah-{$jenis}" ? 'open' : '' }}" id="tambah-{{ $jenis }}" style="margin-top:0;margin-bottom:1rem">
        @include('pelamar.profil.partials.form-entri', ['jenis' => $jenis, 'row' => $baru[$jenis]])
      </div>

      @forelse($b['data'] as $row)
        @php $editId = "edit-{$jenis}-" . $row->{$b['pk']}; @endphp
        <div class="item">
          <div class="item-top">
            <div style="min-width:0">
              @if($jenis === 'organisasi')
                <h4>{{ $row->jabatan }}</h4>
                <div class="sub">{{ $row->nama_organisasi }}</div>
                <div class="meta">{{ filled($row->kategori) ? $row->kategori . ' · ' : '' }}{{ $rentang($row) }}</div>
              @elseif($jenis === 'profesional')
                <h4>{{ $row->posisi }}</h4>
                <div class="sub">{{ $row->nama_perusahaan }}{{ filled($row->jenis_kerja) ? ' · ' . $row->jenis_kerja : '' }}{{ filled($row->lokasi) ? ' · ' . $row->lokasi : '' }}</div>
                <div class="meta">{{ $rentang($row) }}</div>
              @else
                <h4>{{ $row->nama_proyek }}</h4>
                @if(filled($row->peran)) <div class="sub">{{ $row->peran }}</div> @endif
                @if(filled($row->teknologi)) <div class="meta">Teknologi: {{ $row->teknologi }}</div> @endif
                @if(filled($row->tautan_proyek)) <div class="meta"><a href="{{ $row->tautan_proyek }}" target="_blank" rel="noopener" style="color:var(--orange-dark)">{{ $row->tautan_proyek }}</a></div> @endif
              @endif

              @php $isi = $jenis === 'profesional' ? $row->deskripsi_tugas : $row->deskripsi; @endphp
              @if(filled($isi)) <div class="desc">{{ $isi }}</div> @endif
            </div>

            <div class="item-actions">
              <button type="button" class="btn btn-outline btn-sm" data-toggle="{{ $editId }}">Edit</button>
              <form method="POST" action="{{ route('pelamar.profil.pengalaman.destroy', [$jenis, $row->{$b['pk']}]) }}" onsubmit="return confirm('Hapus data ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
              </form>
            </div>
          </div>

          <div class="collapse form-box {{ old('_form') === $editId ? 'open' : '' }}" id="{{ $editId }}">
            @include('pelamar.profil.partials.form-entri', ['jenis' => $jenis, 'row' => $row])
          </div>
        </div>
      @empty
        <p class="empty">Belum ada data. Klik "+ Tambah" untuk mengisi.</p>
      @endforelse
    </section>
  @endforeach
</div>
@endsection