{{--
  Hero foto penuh (dipakai halaman Perusahaan, Tim, dan Produk).
  Letakkan di resources/views/pages/tentang/partials/hero-foto.blade.php

  Variabel:
    $baris1  teks baris pertama judul (teks biasa)
    $baris2  teks baris kedua judul (boleh berisi <em>kata tebal</em>)
    $tombol  teks tombol utama
    $href    tujuan tombol utama (mis. '#profil')
    $tiles   daftar tombol di bawah hero: [[label, href, filter(opsional)], ...]
             kalau "filter" diisi, klik tombol juga memilih filter dengan nama itu di halaman
    $gambar  (opsional) nama file di public/images/tentang/, default hero.jpg.
             Kalau file tidak ada, otomatis memakai hero.jpg.
    $heroFoto (opsional) URL foto penuh yang dikirim dari controller
--}}
@php
  $panah  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M8 7h9v9"/></svg>';
  $gambar = $gambar ?? 'hero.jpg';
  $foto   = $heroFoto ?? (file_exists(public_path('images/tentang/' . $gambar))
              ? asset('images/tentang/' . $gambar)
              : asset('images/tentang/hero.jpg'));
@endphp

<section class="ab-cover" style="--img:url('{{ $foto }}')">
  <div class="ab-cover-bg" aria-hidden="true"></div>

  <div class="ab-cover-inner">
    <div class="ab-cover-body">
      <h1 class="ab-h1" data-split>
        <span class="ab-hl">{{ $baris1 }} <span class="ab-rule"></span></span>
        <span class="ab-hl">{!! $baris2 !!}</span>
      </h1>

      <div class="ab-ctagroup" data-reveal>
        <a href="{{ $href }}" class="ab-btn lg">{{ $tombol }}</a>
        <a href="{{ $href }}" class="ab-btn icon lg" aria-label="{{ $tombol }}">{!! $panah !!}</a>
      </div>
    </div>

    <div class="ab-tiles" data-stagger>
      @foreach($tiles as $t)
        <a href="{{ $t[1] }}" class="ab-tile {{ $loop->first ? 'on' : '' }}"@if(!empty($t[2])) data-tile-filter="{{ $t[2] }}"@endif><span>{{ $t[0] }}</span>{!! $panah !!}</a>
      @endforeach
    </div>
  </div>
</section>