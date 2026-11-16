{{-- resources/views/pages/tentang/perusahaan.blade.php --}}
@extends('pages.tentang.layout')

@section('title', 'Tentang Perusahaan')

{{-- Hero foto penuh sampai ke atas layar, jadi padding atas main dimatikan --}}
@section('main_class', 'no-pad')

@push('styles')
<style>
  /* Tambahan khusus foto (desain lain tidak diubah) */
  .ab-profil-foto { margin: 2.4rem 0 0; height: clamp(14rem, 32vw, 24rem); border-radius: 22px; overflow: hidden; background: var(--card-dark); }
  .ab-profil-foto img { width: 100%; height: 100%; object-fit: cover; transition: transform .8s; }
  .ab-profil-foto:hover img { transform: scale(1.04); }

  /* Logo instansi di kartu legalitas */
  .ab-legal-ico.logo { width: 3.6rem; height: 3.6rem; padding: .4rem; background: #fff; border: 1px solid var(--line); }
  .ab-legal-ico.logo img { width: 100%; height: 100%; object-fit: contain; }

  .ab-cabang-foto { margin: -1.5rem -1.5rem 1.2rem; height: 9rem; overflow: hidden; background: var(--card-dark); }
  .ab-cabang-foto img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s; }
  .ab-card:hover .ab-cabang-foto img { transform: scale(1.06); }

  .ab-city { position: relative; overflow: hidden; }
  .ab-city img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
  .ab-city::after { content: ""; position: absolute; inset: 0; background: linear-gradient(0deg, rgba(8,8,9,.45), rgba(8,8,9,.05) 60%); }
  .ab-city span { position: relative; z-index: 1; color: #fff; text-shadow: 0 2px 24px rgba(0,0,0,.35); }
</style>
@endpush

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

{{-- ============ HERO FOTO PENUH (judul & foto dari admin) ============ --}}
@include('pages.tentang.partials.hero-foto', [
  'baris1'  => $hero['baris1'],
  'baris2'  => $hero['baris2'],
  'tombol'  => 'Kenali Kami',
  'href'    => '#profil',
  'tiles'   => $tiles,
  'gambar'  => 'hero.jpg',
  'fotoUrl' => $hero['foto'],
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

    @if($profilFoto)
      <figure class="ab-profil-foto" data-reveal>
        <img src="{{ $profilFoto }}" alt="Kantor PT Equityworld Futures" loading="lazy">
      </figure>
    @endif
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
            <p>{{ $n->deskripsi }}</p>
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
              @if($l->fotoUrl())
                <div class="ab-legal-ico logo"><img src="{{ $l->fotoUrl() }}" alt="Logo {{ $l->lembaga }}" loading="lazy"></div>
              @else
                <div class="ab-legal-ico">{{ \Illuminate\Support\Str::limit($l->singkatan, 4, '') }}</div>
              @endif
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
          @php $fotoKantor = $c->fotoUrl('kantor-' . $c->getKey(), 900, 700); @endphp

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
                  <a class="ab-btn sm" href="{{ $c->link_peta }}" target="_blank" rel="noopener">Buka Google Maps</a>
                  <button type="button" class="ab-btn sm ghost" data-copy="{{ $c->nama_kantor }}, {{ $c->alamat }}">Salin alamat</button>
                </div>
              </div>
              <div class="ab-city" aria-hidden="true">
                <img src="{{ $fotoKantor }}" alt="" loading="lazy">
                <span>{{ mb_strtoupper(mb_substr((string) $c->kota, 0, 3)) }}</span>
              </div>
            </article>
          @else
            <article class="ab-card ab-cabang" data-cat="{{ $c->kota }}">
              <div class="ab-cabang-foto">
                <img src="{{ $fotoKantor }}" alt="{{ $c->nama_kantor }}" loading="lazy">
              </div>
              <div class="ab-cardtop">
                <span class="ab-badge">{{ $c->kota }}</span>
                <a class="ab-arrow" href="{{ $c->link_peta }}" target="_blank" rel="noopener" aria-label="Buka peta {{ $c->nama_kantor }}">{!! $panah !!}</a>
              </div>
              <h4>{{ $c->nama_kantor }}</h4>
              <p>{{ $c->alamat }}</p>
              <div class="ab-meta">
                @if(filled($c->telepon)) <span>☎ {{ $c->telepon }}</span> @endif
              </div>
              <div class="ab-row">
                <a class="ab-btn sm" href="{{ $c->link_peta }}" target="_blank" rel="noopener">Maps</a>
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