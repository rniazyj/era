{{--
  Dropdown "Tentang" dengan panah, untuk dipasang di navbar menggantikan link Tentang biasa.
  Pemakaian:  @include('pages.tentang.partials.menu-tentang')
  Teks pemicu bisa diganti:  @include('pages.tentang.partials.menu-tentang', ['label' => 'About'])
--}}
@php
  $tnItems = [
    ['Perusahaan', 'Profil, legalitas & cabang', route('tentang'),        request()->routeIs('tentang')],
    ['Tim',        'Orang di balik Equityworld', route('tentang.tim'),    request()->routeIs('tentang.tim')],
    ['Produk',     'Produk & layanan',           route('tentang.produk'), request()->routeIs('tentang.produk')],
  ];
  $tnAktif = request()->routeIs('tentang*');
@endphp

@once
<style>
  .tn-dd { position: relative; display: inline-block; }
  .tn-trigger { display: inline-flex; align-items: center; gap: .35rem; font: inherit; color: inherit; background: none; border: 0; padding: 0; cursor: pointer; line-height: inherit; }
  .tn-trigger svg { width: 14px; height: 14px; transition: transform .25s ease; }
  .tn-dd.is-open .tn-trigger svg { transform: rotate(180deg); }

  .tn-menu { position: absolute; top: calc(100% + .7rem); left: 50%; z-index: 120; min-width: 15.5rem; padding: .4rem; border-radius: 18px; background: #fff; border: 1px solid rgba(23,56,44,.1); box-shadow: 0 16px 40px rgba(23,56,44,.14); opacity: 0; visibility: hidden; transform: translate(-50%, 6px); transition: opacity .2s ease, transform .2s ease, visibility .2s; text-align: left; }
  /* jembatan agar menu tidak menutup saat kursor melintasi celah */
  .tn-menu::before { content: ""; position: absolute; left: 0; right: 0; top: -.8rem; height: .8rem; }
  .tn-dd.is-open .tn-menu { opacity: 1; visibility: visible; transform: translate(-50%, 0); }

  .tn-menu a { display: flex; flex-direction: column; gap: .1rem; padding: .65rem .85rem; border-radius: 12px; text-decoration: none; color: #17382c; font-size: .88rem; font-weight: 600; line-height: 1.3; transition: background .2s; }
  .tn-menu a small { font-size: .72rem; font-weight: 500; color: #6f7d75; }
  .tn-menu a:hover { background: #f5f3ef; }
  .tn-menu a.on { background: #eaf6c2; }

  @media (hover: hover) and (min-width: 901px) {
    .tn-dd:hover .tn-menu { opacity: 1; visibility: visible; transform: translate(-50%, 0); }
    .tn-dd:hover .tn-trigger svg { transform: rotate(180deg); }
  }
  @media (max-width: 900px) {
    .tn-dd { display: block; }
    .tn-menu { position: static; display: none; min-width: 0; margin-top: .5rem; padding: .2rem 0 .2rem .8rem; border: 0; box-shadow: none; background: transparent; opacity: 1; visibility: visible; transform: none; }
    .tn-menu::before { display: none; }
    .tn-dd.is-open .tn-menu { display: block; transform: none; }
  }
</style>
@endonce

<div class="tn-dd {{ $tnAktif ? 'is-active' : '' }}" data-tn-dd>
  <button type="button" class="tn-trigger" aria-haspopup="true" aria-expanded="false">
    <span>{{ $label ?? 'Tentang' }}</span>
    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 8 5 5 5-5"/></svg>
  </button>

  <div class="tn-menu" role="menu">
    @foreach($tnItems as [$nama, $ket, $url, $aktif])
      <a role="menuitem" href="{{ $url }}" class="{{ $aktif ? 'on' : '' }}">
        {{ $nama }}
        <small>{{ $ket }}</small>
      </a>
    @endforeach
  </div>
</div>

@once
<script>
(function () {
  const all = () => [...document.querySelectorAll('[data-tn-dd]')];
  const set = (dd, open) => {
    dd.classList.toggle('is-open', open);
    const b = dd.querySelector('.tn-trigger');
    if (b) b.setAttribute('aria-expanded', open ? 'true' : 'false');
  };

  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('.tn-trigger');
    all().forEach(dd => {
      if (trigger && dd.contains(trigger)) set(dd, !dd.classList.contains('is-open'));
      else if (!dd.contains(e.target)) set(dd, false);
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') all().forEach(dd => set(dd, false));
  });
})();
</script>
@endonce