{{--
  resources/views/admin/konten/daftar.blade.php
  Daftar isi satu bagian (mis. Legalitas, Kantor cabang, Anggota tim).
  Memakai @extends('layouts.dashboard') dengan @section('title') dan @section('content'); sesuaikan bila layoutmu beda.
--}}
@extends('layouts.dashboard')

@section('title', $cfg['nama'] . ' - Konten Website')

@section('content')
@include('admin.konten._gaya')

@php
  use Illuminate\Support\Str;
  $punyaFoto = isset($cfg['field']['foto']);
  $logo      = ($cfg['field']['foto']['gaya'] ?? null) === 'logo';
@endphp

<div class="kw">
  <nav class="kw-crumb" aria-label="Lokasi">
    <a href="{{ route('admin.konten.index') }}">Konten Website</a> ›
    <span>{{ $halaman['nama'] }}</span> ›
    <b>{{ $cfg['nama'] }}</b>
  </nav>

  <div class="kw-head">
    <div>
      <h1>{{ $cfg['nama'] }}</h1>
      <p>{{ $cfg['bantuan'] }}</p>
    </div>
  </div>

  @include('admin.konten._pesan')

  <div class="kw-layout">
    @include('admin.konten._menu')

    <div>
      <div class="kw-where">
        <span>Bagian ini tampil di <strong>{{ $halaman['nama'] }}</strong>.</span>
        <a class="kw-btn ghost sm" href="{{ $urlSitus }}" target="_blank" rel="noopener">Lihat di situs ↗</a>
      </div>

      <div class="kw-card">
        <div class="kw-card-head">
          <div>
            <h2>Daftar {{ Str::lower($cfg['nama']) }} <span class="kw-badge">{{ $rows->count() }}</span></h2>
            <p>Angka "Urutan" yang lebih kecil tampil lebih dulu. Data yang "Disembunyikan" tidak muncul di situs.</p>
          </div>
          <a class="kw-btn" href="{{ route('admin.konten.create', $tipe) }}">+ Tambah {{ Str::lower($cfg['nama']) }}</a>
        </div>

        @if($rows->isEmpty())
          <div class="kw-empty">
            <strong>Belum ada data</strong>
            Klik tombol "Tambah" di atas untuk mengisi bagian ini.
          </div>
        @else
          <div class="kw-table-wrap">
            <table class="kw-table">
              <thead>
                <tr>
                  @if($punyaFoto)<th style="width:4.5rem">{{ $logo ? 'Logo' : 'Foto' }}</th>@endif
                  <th>Isi</th>
                  <th style="width:5.5rem">Urutan</th>
                  <th style="width:9rem">Status</th>
                  <th style="width:11rem"></th>
                </tr>
              </thead>
              <tbody>
                @foreach($rows as $r)
                  <tr>
                    @if($punyaFoto)
                      <td>
                        @if($r->fotoUrl())
                          <img class="kw-thumb {{ $logo ? 'logo' : '' }}" src="{{ $r->fotoUrl() }}" alt="" loading="lazy">
                        @else
                          <span class="kw-thumb">{{ Str::upper(Str::substr((string) ($logo ? $r->label : $r->judul), 0, 2)) }}</span>
                        @endif
                      </td>
                    @endif

                    <td>
                      <span class="utama">{{ $r->{$cfg['utama']} ?: '(belum diisi)' }}</span>
                      @if(!empty($cfg['sub']) && filled($r->{$cfg['sub']}))
                        <span class="sub">{{ $r->{$cfg['sub']} }}</span>
                      @endif
                      @if(!empty($cfg['badge']) && filled($r->{$cfg['badge']}))
                        <span class="kw-badge" style="margin-top:.35rem">{{ $r->{$cfg['badge']} }}</span>
                      @endif
                      @if($tipe === 'cabang' && $r->is_utama)
                        <span class="kw-badge or" style="margin-top:.35rem">Kantor pusat</span>
                      @endif
                    </td>

                    <td>{{ $r->urutan }}</td>
                    <td><span class="kw-badge {{ $r->aktif ? 'ok' : 'off' }}">{{ $r->aktif ? 'Tayang' : 'Disembunyikan' }}</span></td>

                    <td>
                      <div class="kw-aksi">
                        <a class="kw-btn ghost sm" href="{{ route('admin.konten.edit', [$tipe, $r->getKey()]) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.konten.destroy', [$tipe, $r->getKey()]) }}"
                              onsubmit="return confirm('Hapus data ini? Tindakan ini tidak bisa dibatalkan.')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="kw-btn danger sm">Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection