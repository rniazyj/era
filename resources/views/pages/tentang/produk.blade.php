@extends('pages.tentang.layout')

@section('title', 'Produk & Layanan')

{{-- Hero foto penuh sampai ke atas layar, jadi padding atas main dimatikan --}}
@section('main_class', 'no-pad')

@section('konten')

@php
  use Illuminate\Support\Str;

  // Tombol di bawah hero: "Semua Produk" + tiap kategori (maks. 5). Klik = pilih filter kategori di bawah.
  $tiles = [['Semua Produk', '#produk', 'semua']];
  foreach ($kategori->filter()->take(5) as $k) {
    $tiles[] = [$k, '#produk', $k];
  }
@endphp

{{-- ============ HERO FOTO PENUH ============ --}}
{{-- Foto: public/images/tentang/produk.jpg (kalau tidak ada, otomatis memakai hero.jpg) --}}
@include('pages.tentang.partials.hero-foto', [
  'baris1' => 'Produk & Layanan',
  'baris2' => '<em>Unggulan Kami.</em>',
  'tombol' => 'Lihat Produk',
  'href'   => '#produk',
  'tiles'  => $tiles,
  'gambar' => 'produk.jpg',
])

{{-- ============ LAPISAN PUTIH (naik menimpa foto) ============ --}}
<div class="ab-sheet">

  <section id="produk" class="ab-wrap ab-sec">
    <div class="ab-sec-head center">
      <p class="ab-kicker" data-reveal>Produk &amp; Layanan</p>
      <h2 class="ab-h2" data-split>Pilihan untuk setiap <em>strategi investasi.</em></h2>
      <p data-reveal>Beragam produk dan layanan perdagangan berjangka yang kami kelola secara transparan dan profesional.</p>
    </div>

    @if($produk->isEmpty())
      <div class="ab-empty" data-reveal>Produk dan layanan akan segera hadir.</div>
    @else
      @if($kategori->filter()->count() > 1)
        <div class="ab-filters" data-reveal data-filter-group="#produkGrid">
          <button type="button" class="on" data-filter="semua">Semua</button>
          @foreach($kategori->filter() as $k)
            <button type="button" data-filter="{{ $k }}">{{ $k }}</button>
          @endforeach
        </div>
      @endif

      <div class="ab-grid g3" id="produkGrid" data-stagger>
        @foreach($produk as $p)
          @php
            // Nama kolom dicek berurutan, jadi tetap jalan walau nama kolom di tabelmu sedikit berbeda
            $nama  = $p->nama_produk ?? $p->nama ?? $p->nama_layanan ?? $p->judul ?? 'Produk';
            $kode  = $p->kode ?? $p->kode_produk ?? $p->singkatan ?? $p->simbol
                     ?? Str::upper(Str::limit(preg_replace('/[^A-Za-z0-9]/', '', $nama), 3, ''));
            $desk  = $p->deskripsi ?? $p->isi_konten ?? $p->keterangan ?? null;
            $kat   = $p->kategori ?? '';
            $warna = $p->warna ?? $p->warna_aksen ?? '#f97316';
            if (! preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $warna)) $warna = '#f97316';

            // Garis grafik dekoratif, dibuat tetap (sama setiap kali) dari nama produk
            $seed = crc32($nama);
            $pts = [];
            for ($i = 0; $i < 8; $i++) {
              $seed = ($seed * 1103515245 + 12345) & 0x7fffffff;
              $pts[] = 8 + ($seed % 30);
            }
            $garis = 'M0 ' . $pts[0];
            for ($i = 1; $i < 8; $i++) $garis .= ' L' . round($i * (200 / 7)) . ' ' . $pts[$i];
          @endphp

          <article class="ab-card ab-produk" data-cat="{{ $kat }}" style="--c: {{ $warna }}">
            <div class="ab-cardtop">
              <div class="ab-kode">{{ Str::limit($kode, 4, '') }}</div>
              @if(filled($kat)) <span class="ab-badge">{{ $kat }}</span> @endif
            </div>
            <h4>{{ $nama }}</h4>
            @if(filled($desk)) <p>{{ $desk }}</p> @else <p style="padding-bottom:2.4rem"></p> @endif

            <svg class="ab-spark" viewBox="0 0 200 48" preserveAspectRatio="none" aria-hidden="true">
              <path d="{{ $garis }}" pathLength="220"/>
            </svg>
          </article>
        @endforeach
      </div>
    @endif
  </section>

  @include('pages.tentang.partials.cta')

</div>{{-- /.ab-sheet --}}

@endsection