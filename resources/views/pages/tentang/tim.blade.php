@extends('pages.tentang.layout')

@section('title', 'Tim Kami')

{{-- Hero foto penuh sampai ke atas layar, jadi padding atas main dimatikan --}}
@section('main_class', 'no-pad')

@section('konten')

@php
  // Tombol di bawah hero: "Semua Tim" + tiap divisi (maks. 5). Klik = pilih filter divisi di bawah.
  $tiles = [['Semua Tim', '#tim', 'semua']];
  foreach ($divisi->take(5) as $d) {
    $tiles[] = [$d, '#tim', $d];
  }
@endphp

{{-- ============ HERO FOTO PENUH ============ --}}
{{-- Foto: public/images/tentang/tim.jpg (kalau tidak ada, otomatis memakai hero.jpg) --}}
@include('pages.tentang.partials.hero-foto', [
  'baris1' => 'Orang-orang di Balik',
  'baris2' => '<em>Equityworld.</em>',
  'tombol' => 'Kenali Tim',
  'href'   => '#tim',
  'tiles'  => $tiles,
  'gambar' => 'tim.jpg',
])

{{-- ============ LAPISAN PUTIH (naik menimpa foto) ============ --}}
<div class="ab-sheet">

  <section id="tim" class="ab-wrap ab-sec">
    <div class="ab-sec-head center">
      <p class="ab-kicker" data-reveal>Tim Kami</p>
      <h2 class="ab-h2" data-split>Orang-orang yang menjaga <em>kepercayaan Anda.</em></h2>
      <p data-reveal>Tim profesional yang berdedikasi menjaga kepercayaan nasabah dan mendorong pertumbuhan perusahaan.</p>
    </div>

    @if($tim->isEmpty())
      <div class="ab-empty" data-reveal>Profil tim akan segera hadir.</div>
    @else
      @if($divisi->count() > 1)
        <div class="ab-filters" data-reveal data-filter-group="#timGrid">
          <button type="button" class="on" data-filter="semua">Semua</button>
          @foreach($divisi as $d)
            <button type="button" data-filter="{{ $d }}">{{ $d }}</button>
          @endforeach
        </div>
      @endif

      <div class="ab-grid g4" id="timGrid" data-stagger>
        @foreach($tim as $o)
          @php
            $inisial = collect(preg_split('/\s+/', trim($o->nama)))->filter()->take(2)->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->implode('');
          @endphp
          <article class="ab-card ab-tim" data-cat="{{ $o->divisi }}">
            <div class="ab-avatar">
              @if(filled($o->foto))
                <img src="{{ asset('storage/' . ltrim($o->foto, '/')) }}" alt="{{ $o->nama }}" loading="lazy">
              @else
                {{ $inisial }}
              @endif
            </div>
            <div class="ab-tim-body">
              <span class="ab-badge">{{ $o->divisi }}</span>
              <h4 style="margin-top:.7rem">{{ $o->nama }}</h4>
              <div class="jab">{{ $o->jabatan }}</div>

              @if(filled($o->bio))
                <div class="ab-tim-bio"><div><p>{{ $o->bio }}</p></div></div>
              @endif

              @if(filled($o->linkedin_url))
                <a class="ab-li" href="{{ $o->linkedin_url }}" target="_blank" rel="noopener" aria-label="LinkedIn {{ $o->nama }}">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.4 20.5h-3.6v-5.6c0-1.3 0-3-1.8-3s-2.1 1.4-2.1 2.9v5.7H9.3V9h3.4v1.6c.5-.9 1.6-1.8 3.4-1.8 3.6 0 4.3 2.4 4.3 5.5v6.2ZM5.3 7.4a2.1 2.1 0 1 1 0-4.1 2.1 2.1 0 0 1 0 4.1Zm1.8 13.1H3.6V9h3.5v11.5Z"/></svg>
                </a>
              @endif
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </section>

  @include('pages.tentang.partials.cta')

</div>{{-- /.ab-sheet --}}

@endsection