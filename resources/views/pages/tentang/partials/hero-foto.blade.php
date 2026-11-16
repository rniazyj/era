{{--
  resources/views/pages/tentang/partials/hero-foto.blade.php
  Parameter: baris1 (teks biasa), baris2 (HTML, boleh <em>), tombol, href, tiles, gambar, fotoUrl (opsional dari admin)
  tiles: [['Label', '#anchor', 'kunci-filter-opsional'], ...]
  Urutan foto: fotoUrl (unggahan admin) -> public/images/tentang/{gambar} -> hero.jpg -> foto contoh online.
--}}
@php
  $gambar = $gambar ?? 'hero.jpg';
  $src    = $fotoUrl ?? null;

  if (! $src) {
    foreach ([$gambar, 'hero.jpg'] as $f) {
      if (is_file(public_path('images/tentang/' . $f))) { $src = asset('images/tentang/' . $f); break; }
    }
  }
  $src = $src ?: 'https://picsum.photos/seed/equityworld-' . pathinfo($gambar, PATHINFO_FILENAME) . '/1920/1200';

  $panah = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M8 7h9v9"/></svg>';
@endphp

<section class="ab-cover">
  <div class="ab-cover-bg" style="--img: url('{{ $src }}')"></div>

  <div class="ab-cover-inner">
    <div class="ab-cover-body">
      <h1 class="ab-h1" data-split>
        <span class="ab-hl">{{ $baris1 }}</span>
        <span class="ab-hl">{!! $baris2 !!}<i class="ab-rule"></i></span>
      </h1>

      <div class="ab-ctagroup">
        <a class="ab-btn lg" href="{{ $href }}">{{ $tombol }}</a>
        <a class="ab-btn icon lg" href="{{ $href }}" aria-label="{{ $tombol }}">{!! $panah !!}</a>
      </div>
    </div>

    @if(!empty($tiles))
      <div class="ab-tiles" data-stagger>
        @foreach($tiles as $t)
          <a class="ab-tile {{ (($t[2] ?? null) === 'semua') ? 'on' : '' }}"
             href="{{ $t[1] }}"
             @if(isset($t[2])) data-tile-filter="{{ $t[2] }}" @endif>
            <span>{{ $t[0] }}</span>{!! $panah !!}
          </a>
        @endforeach
      </div>
    @endif
  </div>
</section>