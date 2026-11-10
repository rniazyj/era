@extends('pages.tentang.layout')

@section('title', 'Tentang Perusahaan')

{{-- Hero foto penuh sampai ke atas layar, jadi padding atas main dimatikan --}}
@section('main_class', 'no-pad')

@section('konten')

@php
  // Ikon panah ↗ dipakai berulang
  $panah = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M8 7h9v9"/></svg>';

  // Tombol di bawah hero = bagian-bagian yang ada di halaman ini
  $tiles = [
    ['Profil',      '#profil'],
    ['Visi & Misi', '#fondasi'],
  ];
  if ($nilai->isNotEmpty())     $tiles[] = ['Nilai Inti',    '#nilai'];
  if ($tonggak->isNotEmpty())   $tiles[] = ['Sejarah',       '#sejarah'];
  if ($legalitas->isNotEmpty()) $tiles[] = ['Legalitas',     '#legalitas'];
  if ($cabang->isNotEmpty())    $tiles[] = ['Kantor Cabang', '#cabang'];
@endphp

{{-- ============ HERO FOTO PENUH ============ --}}
{{-- Foto: public/images/tentang/hero.jpg (atau kirim $heroFoto dari controller). --}}
@include('pages.tentang.partials.hero-foto', [
  'baris1' => 'Mitra Tepercaya',
  'baris2' => 'Investasi <em>Berjangka</em>',
  'tombol' => 'Kenali Kami',
  'href'   => '#profil',
  'tiles'  => $tiles,
  'gambar' => 'hero.jpg',
])

{{-- ============ LAPISAN PUTIH (naik menimpa foto) ============ --}}
<div class="ab-sheet">

  {{-- ============ PROFIL PERUSAHAAN ============ --}}
  <section id="profil" class="ab-wrap ab-sec" style="padding-bottom:2rem">
    <div class="ab-profil">
      <div>
        <p class="ab-kicker" data-reveal>Tentang perusahaan</p>
        <h2 class="ab-h2" data-split>Mengenal lebih dekat <em>PT Equityworld Futures.</em></h2>
      </div>
      <div>
        <p class="ab-profil-text" data-reveal>{{ $profil ?? 'PT Equityworld Futures hadir sebagai mitra investasi yang mengutamakan kepercayaan dan profesionalisme di industri perdagangan berjangka.' }}</p>
        <div class="ab-chips" data-stagger>
          <span class="ab-chip"><span class="dot"></span> Operasional Aktif</span>
          <span class="ab-chip"><b>✓</b> Diawasi BAPPEBTI</span>
          <span class="ab-chip"><b>★</b> Anggota JFX &amp; KBI</span>
        </div>
      </div>
    </div>
  </section>

  {{-- ============ MARQUEE LEGALITAS ============ --}}
  @if($legalitas->isNotEmpty())
    <div class="ab-marquee" aria-hidden="true">
      <div class="ab-marquee-track">
        @for($n = 0; $n < 8; $n++)
          @foreach($legalitas as $l)
            <span>{{ $l->singkatan }} · {{ $l->lembaga }}</span>
          @endforeach
        @endfor
      </div>
    </div>
  @endif

  {{-- ============ STATISTIK ============ --}}
  @if($stat->isNotEmpty())
    <div class="ab-wrap">
      <div class="ab-stats" data-stagger>
        @foreach($stat as $s)
          <div class="ab-stat">
            <strong><span data-count="{{ $s['angka'] }}">{{ $s['angka'] }}</span>{{ $s['akhiran'] }}</strong>
            <span>{{ $s['label'] }}</span>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  {{-- ============ VISI & MISI ============ --}}
  <section id="fondasi" class="ab-wrap ab-sec">
    <div class="ab-sec-head">
      <p class="ab-kicker" data-reveal>Fondasi</p>
      <h2 class="ab-h2" data-split>Arah strategis <em>perusahaan.</em></h2>
    </div>

    <div class="ab-grid g2" data-stagger>
      <article class="ab-visi">
        <div class="ab-cardtop" style="margin:0">
          <span class="ab-tag">Visi kami</span>
          <span class="ab-arrow" aria-hidden="true">{!! $panah !!}</span>
        </div>
        <h3>{{ $visi ?? 'Visi perusahaan belum diisi.' }}</h3>
      </article>

      <article class="ab-misi">
        <div class="ab-cardtop">
          <span class="ab-tag">Misi strategis</span>
        </div>
        @if(count($misi))
          <ul>
            @foreach($misi as $m)
              <li>{{ $m }}</li>
            @endforeach
          </ul>
        @else
          <p>Misi perusahaan belum diisi.</p>
        @endif
      </article>
    </div>
  </section>

  {{-- ============ NILAI ============ --}}
  @if($nilai->isNotEmpty())
    <section id="nilai" class="ab-wrap" style="padding-bottom:4.5rem">
      <div class="ab-sec-head">
        <p class="ab-kicker" data-reveal>Nilai inti</p>
        <h2 class="ab-h2" data-split>Cara kami <em>bekerja.</em></h2>
      </div>

      <div class="ab-grid g4" data-stagger>
        @foreach($nilai as $n)
          <article class="ab-card">
            <div class="ab-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
            <h4>{{ $n->judul }}</h4>
            <p>{{ $n->isi_konten }}</p>
          </article>
        @endforeach
      </div>
    </section>
  @endif

  {{-- ============ SEJARAH ============ --}}
  @if($tonggak->isNotEmpty())
    <section id="sejarah" class="ab-wrap ab-sec" style="padding-top:1rem">
      <div class="ab-sec-head center">
        <p class="ab-kicker" data-reveal>Perjalanan</p>
        <h2 class="ab-h2" data-split>Tonggak <em>sejarah.</em></h2>
      </div>

      <div class="ab-tl">
        <span class="ab-tl-fill"></span>
        @foreach($tonggak as $t)
          <div class="ab-tl-item" data-reveal>
            <span class="node"></span>
            <div class="yr">{{ $t->tahun }}</div>
            <h4>{{ $t->judul }}</h4>
            @if(filled($t->deskripsi)) <p>{{ $t->deskripsi }}</p> @endif
          </div>
        @endforeach
      </div>
    </section>
  @endif

  {{-- ============ LEGALITAS ============ --}}
  @if($legalitas->isNotEmpty())
    <section id="legalitas" class="ab-wrap ab-sec">
      <div class="ab-sec-head center">
        <p class="ab-kicker" data-reveal>Legalitas &amp; Izin</p>
        <h2 class="ab-h2" data-split>Resmi, diawasi, dan <em>terverifikasi.</em></h2>
        <p data-reveal>Seluruh kegiatan usaha kami berjalan di bawah pengawasan lembaga resmi perdagangan berjangka di Indonesia.</p>
      </div>

      <div class="ab-note" data-reveal>
        <span class="ab-badge ok">✓ Dinaungi Kementerian Perdagangan</span>
      </div>

      <div class="ab-grid g3" data-stagger>
        @foreach($legalitas as $l)
          <article class="ab-card">
            <div class="ab-legal-top">
              <div class="ab-legal-ico">{{ \Illuminate\Support\Str::limit($l->singkatan, 4, '') }}</div>
              <span class="ab-badge ok">✓ Terverifikasi</span>
            </div>
            <h4>{{ $l->lembaga }}</h4>
            @if(filled($l->deskripsi)) <p>{{ $l->deskripsi }}</p> @endif

            <div class="ab-nomor">
              <span>{{ $l->nomor_izin }}</span>
              <button type="button" class="ab-copy" data-copy="{{ $l->nomor_izin }}">Salin</button>
            </div>
          </article>
        @endforeach
      </div>
    </section>
  @endif

  {{-- ============ KANTOR CABANG ============ --}}
  @if($cabang->isNotEmpty())
    <section id="cabang" class="ab-wrap ab-sec" style="padding-top:1rem">
      <div class="ab-sec-head center">
        <p class="ab-kicker" data-reveal>Jaringan Kantor · {{ $cabang->count() }} lokasi</p>
        <h2 class="ab-h2" data-split>Dekat dengan Anda di <em>setiap kota.</em></h2>
        <p data-reveal>Kunjungi kantor kami atau hubungi cabang terdekat untuk konsultasi langsung.</p>
      </div>

      @if($kota->count() > 1)
        <div class="ab-filters" data-reveal data-filter-group="#cabangGrid">
          <button type="button" class="on" data-filter="semua">Semua Cabang</button>
          @foreach($kota as $k)
            <button type="button" data-filter="{{ $k }}">{{ $k }}</button>
          @endforeach
        </div>
      @endif

      <div class="ab-grid g3" id="cabangGrid" data-stagger>
        @foreach($cabang as $c)
          @if($c->is_pusat)
            <article class="ab-card ab-cabang pusat" data-cat="{{ $c->kota }}">
              <div>
                <span class="ab-badge dark">Kantor Pusat</span>
                <h4 style="margin-top:1rem">{{ $c->nama_kantor }}</h4>
                <p style="margin-top:.5rem">{{ $c->alamat }}</p>
                <div class="ab-meta">
                  @if(filled($c->telepon)) <span>☎ {{ $c->telepon }}</span> @endif
                </div>
                <div class="ab-row">
                  @if(filled($c->link_peta))
                    <a class="ab-btn sm" href="{{ $c->link_peta }}" target="_blank" rel="noopener">Buka Google Maps</a>
                  @endif
                  <button type="button" class="ab-btn sm ghost" data-copy="{{ $c->nama_kantor }}, {{ $c->alamat }}">Salin alamat</button>
                </div>
              </div>
              <div class="ab-city" aria-hidden="true">{{ mb_strtoupper(mb_substr($c->kota, 0, 3)) }}</div>
            </article>
          @else
            <article class="ab-card ab-cabang" data-cat="{{ $c->kota }}">
              <div class="ab-cardtop">
                <span class="ab-badge">{{ $c->kota }}</span>
                @if(filled($c->link_peta))
                  <a class="ab-arrow" href="{{ $c->link_peta }}" target="_blank" rel="noopener" aria-label="Buka peta {{ $c->nama_kantor }}">{!! $panah !!}</a>
                @endif
              </div>
              <h4>{{ $c->nama_kantor }}</h4>
              <p>{{ $c->alamat }}</p>
              <div class="ab-meta">
                @if(filled($c->telepon)) <span>☎ {{ $c->telepon }}</span> @endif
              </div>
              <div class="ab-row">
                @if(filled($c->link_peta))
                  <a class="ab-btn sm" href="{{ $c->link_peta }}" target="_blank" rel="noopener">Maps</a>
                @endif
                <button type="button" class="ab-btn sm ghost" data-copy="{{ $c->nama_kantor }}, {{ $c->alamat }}">Salin alamat</button>
              </div>
            </article>
          @endif
        @endforeach
      </div>
    </section>
  @endif

  @include('pages.tentang.partials.cta')

</div>{{-- /.ab-sheet --}}

@endsection

@push('scripts')
<script>
(function () {
  if (!window.gsap || !document.documentElement.classList.contains('js')) return;

  // Garis sejarah terisi mengikuti scroll
  const tl = document.querySelector('.ab-tl');
  if (tl) {
    gsap.fromTo(tl.querySelector('.ab-tl-fill'), { scaleY: 0 }, {
      scaleY: 1, ease: 'none',
      scrollTrigger: { trigger: tl, start: 'top 65%', end: 'bottom 65%', scrub: .4 }
    });
    tl.querySelectorAll('.ab-tl-item').forEach(it => ScrollTrigger.create({
      trigger: it, start: 'top 65%',
      onEnter: () => it.classList.add('on'),
      onLeaveBack: () => it.classList.remove('on')
    }));
  }
})();
</script>
@endpush