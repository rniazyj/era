{{--
  Navbar ERA (dipakai bersama oleh semua halaman publik)

  Cara pakai di semua halaman (tanpa parameter):
      @include('partials.navbar')

  Letakkan di resources/views/partials/navbar.blade.php

  Splash ERA:
  - Sekarang ada di dalam navbar ini, jadi berlaku di SEMUA halaman utama.
  - Hanya tampil saat halaman di-RELOAD (refresh). Pindah halaman lewat link tidak memunculkan splash.
  - Tidak tampil kalau pengguna mengaktifkan "kurangi animasi" di perangkatnya.
  - Halaman yang punya animasi sendiri (misal home) bisa menunggu event "era:reveal" (lihat bagian bawah).

  Catatan: id "nav" / "nav-logo" dan class "nav-i" sengaja dipertahankan.
--}}
@php
  $user = auth()->user();
  $dashboardUrl = null;
  $profilUrl = null;
  $nama = null;
  $inisial = null;

  if ($user) {
    // Pelamar / user biasa tidak punya dasbor, jadi tetap null (tautan dasbor disembunyikan)
    if ($user->isAdmin()) {
      $dashboardUrl = route('admin.dashboard');
    } elseif ($user->isPimpinan()) {
      $dashboardUrl = route('pimpinan.dashboard');
    }
    // Baru pelamar yang sudah punya halaman profil.
    // Nanti kalau admin/pimpinan sudah punya, isi $profilUrl di cabang masing-masing di atas.
    if (!$user->isAdmin() && !$user->isPimpinan()) {
      $profilUrl = route('pelamar.profil');
    }
    $nama = $user->name;
    $inisial = mb_strtoupper(mb_substr($nama, 0, 1));
  }

  // Format item: [judul, keterangan, url, aktif?]
  $tentangItems = [
    ['Perusahaan', 'Profil, legalitas & cabang', route('tentang'),         request()->routeIs('tentang')],
    ['Tim',        'Orang di balik Equityworld', route('tentang.tim'),     request()->routeIs('tentang.tim')],
    ['Produk',     'Produk & layanan',           route('tentang.produk'),  request()->routeIs('tentang.produk')],
  ];

  $karierItems = [
    ['Lowongan Kerja', 'Posisi full-time & fresh graduate', route('lowongan.index'), request()->routeIs('lowongan.*')],
    ['Program Magang', 'Magang konversi & non-konversi',    route('program.index'),  request()->routeIs('program.*', 'internship.*')],
  ];

  // Menu dropdown di navbar. 'url' null = pemicu saja (tidak membuka halaman).
  $menus = [
    ['label' => 'Tentang', 'url' => route('tentang'), 'items' => $tentangItems],
    ['label' => 'Karier',  'url' => null,             'items' => $karierItems],
  ];
@endphp

{{-- Tandai halaman reload sedini mungkin supaya tidak ada kedipan --}}
<script>
  (() => {
    const nav = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
    const reload = nav ? nav.type === 'reload' : !!(performance.navigation && performance.navigation.type === 1);
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    window.eraSplash = { active: reload && !reduce, revealed: false };
    if (window.eraSplash.active) document.documentElement.classList.add('era-splash');
  })();
</script>

<style>
  /* ===== Splash ERA (hanya aktif saat html.era-splash) ===== */
  html.era-splash{ overflow:hidden; }
  .era-word,.era-cap{ display:none; }
  html.era-splash .era-word,html.era-splash .era-cap{ display:block; }
  .era-word{ position:fixed; left:50%; top:50%; z-index:60; font-weight:600; letter-spacing:-.02em; font-size:clamp(3rem,9vw,5.5rem); line-height:1; color:#18181b; transform:translate(-50%,-50%); transform-origin:center; pointer-events:none; white-space:nowrap; }
  .era-word .m{ display:inline-block; overflow:hidden; vertical-align:top; padding-bottom:.08em; }
  .era-word .m>span{ display:inline-block; transform:translateY(110%); }
  .era-cap{ position:fixed; left:50%; top:calc(50% + 3.6rem); transform:translateX(-50%); z-index:60; font-size:.7rem; letter-spacing:.22em; color:rgba(0,0,0,.45); opacity:0; white-space:nowrap; pointer-events:none; }
  /* Latar putih menutup halaman selama splash */
  html.era-splash body::before{ content:""; position:fixed; inset:0; z-index:55; background:#faf9fc; }
  html.era-splash .nv{ z-index:70; background:transparent; backdrop-filter:none; -webkit-backdrop-filter:none; }
  html.era-splash #nav-logo{ opacity:0; }
  .nav-i{ transition:opacity .6s ease, transform .6s ease; }
  html.era-splash:not(.era-nav-in) .nav-i{ opacity:0; transform:translateY(-20px); }
  html.era-splash .nav-i + .nav-i{ transition-delay:.1s; }

  /* ===== Navbar ===== */
  .nv{ position:fixed; z-index:50; top:0; left:0; right:0; backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); background:rgba(250,249,252,.7); color:#18181b; font-size:.875rem; line-height:1.5; }
  .nv *{ box-sizing:border-box; }
  .nv-in{ max-width:80rem; margin:0 auto; padding:0 1.5rem; height:4rem; display:grid; grid-template-columns:1fr auto 1fr; align-items:center; }
  .nv-logo{ justify-self:start; font-size:1.125rem; font-weight:600; letter-spacing:-.025em; text-decoration:none; color:inherit; }
  .nv-links{ display:none; align-items:center; list-style:none; gap:2.5rem; margin:0; padding:0; }
  @media(min-width:768px){ .nv-links{ display:flex; } }
  .nv-links a,.nv-text{ text-decoration:none; color:inherit; transition:opacity .2s; }
  .nv-links a:hover,.nv-text:hover{ opacity:.6; }

  /* Dropdown (Tentang & Karier) */
  .nv-dd{ position:relative; }
  .nv-dd-btn{ display:inline-flex; align-items:center; gap:.25rem; background:none; border:0; padding:0; font:inherit; color:inherit; cursor:pointer; text-decoration:none; transition:opacity .2s; }
  .nv-dd-btn:hover{ opacity:.6; }
  .nv-dd-btn.on{ font-weight:600; }
  .nv-dd-btn span{ font-size:10px; display:inline-block; transition:transform .25s; }
  .nv-dd:hover .nv-dd-btn span,.nv-dd:focus-within .nv-dd-btn span{ transform:rotate(180deg); }
  .nv-dd-menu{
    position:absolute; left:50%; top:100%; min-width:15rem; padding-top:.75rem;
    opacity:0; visibility:hidden; transform:translate(-50%,6px);
    transition:opacity .2s,transform .2s,visibility 0s .2s;
  }
  .nv-dd:hover .nv-dd-menu,.nv-dd:focus-within .nv-dd-menu{ opacity:1; visibility:visible; transform:translate(-50%,0); transition:opacity .2s,transform .2s; }
  .nv-dd-box{ background:#fff; border-radius:.9rem; border:1px solid rgba(0,0,0,.06); box-shadow:0 20px 40px -16px rgba(0,0,0,.28); padding:.35rem; }
  .nv-links .nv-dd-box a{ display:flex; flex-direction:column; gap:.1rem; padding:.6rem .8rem; border-radius:.6rem; opacity:1; font-size:.82rem; font-weight:600; color:#18181b; line-height:1.35; }
  .nv-links .nv-dd-box a small{ font-size:.7rem; font-weight:500; color:rgba(0,0,0,.5); }
  .nv-links .nv-dd-box a:hover{ background:#f4f4f5; opacity:1; }
  .nv-links .nv-dd-box a.on{ background:#fff1e4; }

  .nv-r{ grid-column:3; justify-self:end; display:flex; align-items:center; gap:1.25rem; }
  @media(max-width:639px){ .nv-help{ display:none; } }

  .nv-pill{
    display:inline-flex; align-items:center; gap:.5rem; padding:.375rem 1.25rem; border-radius:9999px; border:1px solid #111;
    background:transparent; color:inherit; font:inherit; text-decoration:none; cursor:pointer;
    transition:background .3s,color .3s,border-color .3s;
  }
  .nv-pill:hover{ background:#111; color:#fff; }

  /* Profil (setelah masuk) */
  .nv-user{ position:relative; }
  .nv-prof{ padding:.25rem .875rem .25rem .25rem; }
  .nv-av{ width:1.75rem; height:1.75rem; border-radius:50%; background:#f97316; color:#fff; display:grid; place-items:center; font-size:.78rem; font-weight:600; flex:none; }
  .nv-name{ max-width:9rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .nv-caret{ width:12px; height:12px; flex:none; transition:transform .3s; }
  .nv-user.open .nv-caret{ transform:rotate(180deg); }
  @media(max-width:520px){ .nv-name{ display:none; } .nv-prof{ padding-right:.5rem; } }

  .nv-menu{
    position:absolute; right:0; top:calc(100% + .6rem); min-width:13rem; padding:.4rem;
    background:#fff; border:1px solid rgba(0,0,0,.1); border-radius:.9rem; box-shadow:0 24px 48px -20px rgba(0,0,0,.3);
    opacity:0; visibility:hidden; transform:translateY(-6px); transition:opacity .2s,transform .2s,visibility 0s .2s;
  }
  .nv-user.open .nv-menu{ opacity:1; visibility:visible; transform:none; transition:opacity .2s,transform .2s; }
  .nv-menu-h{ padding:.55rem .7rem .65rem; border-bottom:1px solid rgba(0,0,0,.08); margin-bottom:.35rem; }
  .nv-menu-h b{ display:block; font-size:.82rem; font-weight:600; }
  .nv-menu-h span{ display:block; font-size:.72rem; color:rgba(0,0,0,.5); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:12rem; }
  .nv-item{
    display:block; width:100%; padding:.55rem .7rem; border:0; border-radius:.55rem; background:transparent; color:#111;
    font:inherit; font-size:.82rem; text-align:left; text-decoration:none; cursor:pointer; transition:background .2s,color .2s;
  }
  .nv-item:hover{ background:#f2f2f2; }
  .nv-item.out:hover{ background:#fef2f2; color:#b91c1c; }
</style>

{{-- Splash ERA (hanya terlihat saat reload) --}}
<div class="era-word" id="era-word" aria-hidden="true"><span class="m"><span>E</span></span><span class="m"><span>R</span></span><span class="m"><span>A</span></span></div>
<div class="era-cap" id="era-cap" aria-hidden="true">EQUITYWORLD RECRUITMENT ACCESS</div>

<header id="nav" class="nv">
  <nav class="nv-in" aria-label="Navigasi utama">
    <a href="{{ route('home') }}" id="nav-logo" class="nv-logo">ERA</a>

    <ul class="nv-links nav-i">
      <li><a href="{{ route('home') }}">Beranda</a></li>

      @foreach($menus as $menu)
        @php $menuAktif = collect($menu['items'])->contains(fn ($it) => $it[3]); @endphp
        <li class="nv-dd">
          @if($menu['url'])
            <a href="{{ $menu['url'] }}" class="nv-dd-btn {{ $menuAktif ? 'on' : '' }}" aria-haspopup="true">{{ $menu['label'] }} <span>▾</span></a>
          @else
            <button type="button" class="nv-dd-btn {{ $menuAktif ? 'on' : '' }}" aria-haspopup="true">{{ $menu['label'] }} <span>▾</span></button>
          @endif
          <div class="nv-dd-menu">
            <div class="nv-dd-box">
              @foreach($menu['items'] as [$label, $ket, $url, $aktif])
                <a href="{{ $url }}" class="{{ $aktif ? 'on' : '' }}">
                  {{ $label }}
                  <small>{{ $ket }}</small>
                </a>
              @endforeach
            </div>
          </div>
        </li>
      @endforeach
    </ul>

    <div class="nv-r nav-i">
      <a href="{{ route('bantuan') }}" class="nv-text nv-help">Bantuan</a>

      @auth
        <div class="nv-user" id="nvUser">
          <button type="button" class="nv-pill nv-prof" id="nvBtn" aria-haspopup="true" aria-expanded="false" aria-controls="nvMenu">
            <span class="nv-av" aria-hidden="true">{{ $inisial }}</span>
            <span class="nv-name">{{ $nama }}</span>
            <svg class="nv-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 9l7 7 7-7"/></svg>
          </button>

          <div class="nv-menu" id="nvMenu" role="menu">
            <div class="nv-menu-h">
              <b>{{ $nama }}</b>
              <span>{{ $user->email }}</span>
            </div>
            @if($profilUrl)
              <a href="{{ $profilUrl }}" class="nv-item" role="menuitem">Lihat Profil</a>
            @endif
            @if($dashboardUrl)
              <a href="{{ $dashboardUrl }}" class="nv-item" role="menuitem">Dasbor</a>
            @endif
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="nv-item out" role="menuitem">Keluar</button>
            </form>
          </div>
        </div>
      @else
        <a href="{{ route('login') }}" class="nv-pill">Mulai Sekarang</a>
      @endauth
    </div>
  </nav>
</header>

{{-- Animasi splash: jalan hanya kalau window.eraSplash.active (halaman di-reload) --}}
<script>
  (() => {
    if (!window.eraSplash || !window.eraSplash.active) return;
    const root = document.documentElement;
    const word = document.getElementById('era-word');
    const cap = document.getElementById('era-cap');
    const logo = document.getElementById('nav-logo');
    const wait = ms => new Promise(r => setTimeout(r, ms));
    const fire = name => document.dispatchEvent(new CustomEvent(name));

    (async () => {
      await wait(200);
      word.querySelectorAll('.m>span').forEach((l, i) =>
        l.animate([{ transform: 'translateY(110%)' }, { transform: 'translateY(0)' }],
          { duration: 800, delay: i * 120, easing: 'cubic-bezier(.22,1,.36,1)', fill: 'forwards' }));
      await wait(900);
      cap.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 500, fill: 'forwards' });
      await wait(1000);
      cap.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 300, fill: 'forwards' });
      await wait(300);

      // Wordmark berpindah ke posisi logo di navbar
      const a = word.getBoundingClientRect(), b = logo.getBoundingClientRect();
      const dx = b.left + b.width / 2 - (a.left + a.width / 2);
      const dy = b.top + b.height / 2 - (a.top + a.height / 2);
      const s = b.height / a.height * 0.9;
      const move = word.animate([
        { transform: 'translate(-50%,-50%) translate(0,0) scale(1)' },
        { transform: `translate(-50%,-50%) translate(${dx}px,${dy}px) scale(${s})` }
      ], { duration: 1100, easing: 'cubic-bezier(.76,0,.24,1)', fill: 'forwards' });

      await wait(900);
      window.eraSplash.revealed = true;
      root.classList.add('era-nav-in');   // menu navbar muncul
      fire('era:reveal');                 // halaman (misal home) mulai animasinya

      try { await move.finished; } catch (e) {}
      word.style.display = 'none';
      root.classList.remove('era-splash', 'era-nav-in');
      fire('era:done');
    })();
  })();
</script>

@auth
<script>
  (() => {
    const box = document.getElementById('nvUser');
    const btn = document.getElementById('nvBtn');
    if (!box || !btn) return;
    const set = open => { box.classList.toggle('open', open); btn.setAttribute('aria-expanded', String(open)); };
    btn.addEventListener('click', e => { e.stopPropagation(); set(!box.classList.contains('open')); });
    document.addEventListener('click', e => { if (!box.contains(e.target)) set(false); });
    addEventListener('keydown', e => { if (e.key === 'Escape') set(false); });
  })();
</script>
@endauth