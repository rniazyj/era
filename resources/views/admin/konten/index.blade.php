{{--
  resources/views/admin/konten/index.blade.php
  Halaman depan Konten Website: 3 halaman situs + bagian-bagiannya.
  Memakai @extends('layouts.dashboard') dengan @section('title') dan @section('content'); sesuaikan bila layoutmu beda.
--}}
@extends('layouts.dashboard')

@section('title', 'Konten Website')

@section('content')
@include('admin.konten._gaya')

<div class="kw">
  <div class="kw-head">
    <div>
      <h1>Konten Website</h1>
      <p>Ubah tulisan dan foto di halaman Tentang. Pilih halaman, pilih bagiannya, lalu edit. Perubahan langsung tampil di situs setelah disimpan.</p>
    </div>
  </div>

  @include('admin.konten._pesan')

  <div class="kw-steps">
    <div class="kw-step"><b>1</b><div><strong>Pilih halaman</strong><span>Perusahaan, Tim, atau Produk.</span></div></div>
    <div class="kw-step"><b>2</b><div><strong>Pilih bagian</strong><span>Nomor mengikuti urutan tampil di halaman.</span></div></div>
    <div class="kw-step"><b>3</b><div><strong>Edit &amp; simpan</strong><span>Tombol "Lihat di situs" untuk mengecek hasilnya.</span></div></div>
  </div>

  <div class="kw-pages">
    @foreach($halaman as $kode => $h)
      <section class="kw-pcard">
        <header class="kw-pcard-head">
          <div>
            <h2>{{ $h['nama'] }}</h2>
            <p>{{ $h['ringkas'] }}</p>
            <code>{{ $h['path'] }}</code>
          </div>
          <a class="kw-btn ghost sm" href="{{ $h['url'] }}" target="_blank" rel="noopener">Lihat halaman ↗</a>
        </header>

        <ol class="kw-list">
          @foreach($h['bagian'] as $b)
            <li>
              <a class="kw-item" href="{{ $b['url'] }}">
                <span class="kw-no">{{ $b['nomor'] }}</span>
                <span class="kw-item-t">
                  <strong>{{ $b['nama'] }}</strong>
                  <small>{{ $b['bantuan'] }}</small>
                </span>
                <span class="kw-badge {{ $b['ok'] ? 'ok' : 'off' }}">{{ $b['status'] }}</span>
                <span class="kw-edit">Edit →</span>
              </a>
            </li>
          @endforeach
        </ol>
      </section>
    @endforeach
  </div>
</div>
@endsection