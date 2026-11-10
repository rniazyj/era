{{--
  Navbar ERA (dipakai bersama oleh semua halaman publik)

  Di halaman home (yang punya animasi splash ERA):
      @include('partials/navbar', ['splash' => true])

  Di halaman lain:
      @include('partials/navbar')

  Letakkan di resources/views/partials/navbar.blade.php
  Catatan: id "nav" / "nav-logo" dan class "nav-i" sengaja dipertahankan karena dipakai animasi GSAP di home.
  Menu "About" punya dropdown (Perusahaan, Tim, Produk) yang terbuka saat hover / fokus keyboard.
--}}
@php
  $splash = $splash ?? false;
  $user = auth()->user();
  $dashboardUrl = null;
  $profilUrl = null;
  $nama = null;
  $inisial = null;

  if ($user) {
    // Pelamar / user biasa tidak punya dashboard, jadi tetap null (link dashboard disembunyikan)
    if ($user->isAdmin()) {
      $dashboardUrl = route('admin.dashboard');
    } elseif ($user->isPimpinan()) {
      $dashboardUrl = route('pimpinan.dashboard');
    }
    // Halaman profil tiap role berbeda. Baru pelamar yang sudah punya halaman profil.
    // Nanti kalau admin/pimpinan sudah punya, isi $profilUrl di cabang masing-masing di atas.
    if (!$user->isAdmin() && !$user->isPimpinan()) {
      $profilUrl = route('pelamar.profil');
    }
    $nama = $user->name;
    $inisial = mb_strtoupper(mb_substr($nama, 0, 1));
  }

  // Isi dropdown "About"
  $aboutItems = [
    [__('Perusahaan'), __('Profil, legalitas & cabang'),  route('tentang'),         request()->routeIs('tentang')],
    [__('Tim'),        __('Orang di balik Equityworld'),  route('tentang.tim'),     request()->routeIs('tentang.tim')],
    [__('Produk'),     __('Produk & layanan'),            route('tentang.produk'), request()->routeIs('tentang.produk')],
  ];
@endphp

<style>
  .nv{ position:fixed; z-index:50; top:0; left:0; right:0; backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); background:rgba(250,249,252,.7); color:#18181b; font-size:.875rem; line-height:1.5; }
  .nv *{ box-sizing:border-box; }
  .nv-in{ max-width:80rem; margin:0 auto; padding:0 1.5rem; height:4rem; display:grid; grid-template-columns:1fr auto 1fr; align-items:center; }
  .nv-logo{ justify-self:start; font-size:1.125rem; font-weight:600; letter-spacing:-.025em; text-decoration:none; color:inherit; }
  .nv-links{ display:none; align-items:center; list-style:none; gap:2.5rem; margin:0; padding:0; }
  @media(min-width:768px){ .nv-links{ display:flex; } }
  .nv-links a,.nv-text{ text-decoration:none; color:inherit; transition:opacity .2s; }
  .nv-links a:hover,.nv-text:hover{ opacity:.6; }

  /* Dropdown About */
  .nv-dd{ position:relative; }
  .nv-dd-btn{ display:inline-flex; align-items:center; gap:.25rem; }
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

  /* Profil (setelah login) */
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

<header id="nav" class="nv">
  <nav class="nv-in">
    <a href="{{ route('home') }}" id="nav-logo" class="nv-logo" @if($splash) style="opacity:0" @endif>ERA</a>

    <ul class="nv-links nav-i">
<<<<<<< HEAD
      <li><a href="{{ route('home') }}">Home</a></li>
      <li><a href="{{ route('tentang') }}">About</a></li>
      <li><a href="{{ route('internship.index') }}">Internship</a></li>
      <li><a href="{{ route('lowongan.index') }}">Job</a></li>
=======
      <li><a href="{{ route('home') }}">{{ __('Beranda') }}</a></li>
      <li class="nv-dd">
        <a href="{{ route('tentang') }}" class="nv-dd-btn" aria-haspopup="true">{{ __('Tentang') }} <span>▾</span></a>
        <div class="nv-dd-menu">
          <div class="nv-dd-box">
            @foreach($aboutItems as [$label, $ket, $url, $aktif])
              <a href="{{ $url }}" class="{{ $aktif ? 'on' : '' }}">
                {{ $label }}
                <small>{{ $ket }}</small>
              </a>
            @endforeach
          </div>
        </div>
      </li>
      <li><a href="{{ route('program.index') }}">{{ __('Magang') }}</a></li>
      <li><a href="{{ route('lowongan.index') }}">{{ __('Lowongan') }}</a></li>
>>>>>>> vina
    </ul>

    <div class="nv-r nav-i">
      <a href="{{ route('bantuan') }}" class="nv-text nv-help">{{ __('Bantuan') }}</a>

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
              <a href="{{ $profilUrl }}" class="nv-item" role="menuitem">{{ __('Lihat profil') }}</a>
            @endif
            @if($dashboardUrl)
              <a href="{{ $dashboardUrl }}" class="nv-item" role="menuitem">{{ __('Dashboard') }}</a>
            @endif
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="nv-item out" role="menuitem">{{ __('Keluar') }}</button>
            </form>
          </div>
        </div>
      @else
        <a href="{{ route('login') }}" class="nv-pill">{{ __('Mulai Sekarang') }}</a>
      @endauth
    </div>
  </nav>
</header>

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