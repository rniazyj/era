<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'ERA')</title>

  {{-- Kalau datang dari halaman auth lain, tandai supaya halaman ini ikut geser masuk --}}
  <script>
    try {
      if (sessionStorage.getItem('era-slide') !== null) {
        document.documentElement.classList.add('slide-in');
        sessionStorage.removeItem('era-slide');
      }
    } catch (e) {}
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    /* ───────── Token ───────── */
    :root{
      --paper:#fff; --ink:#111; --orange:#f97316; --orange-d:#ea580c;
      --go-fill:var(--orange);      /* isi tombol utama saat di-hover: oranye yang sama dengan blok panah */
      --field:#f2f2f2;              /* abu muda netral untuk kolom input */
      --line:rgba(0,0,0,.12); --mute:rgba(0,0,0,.45); --err:#dc2626;
      --ease:cubic-bezier(.76,0,.24,1); --out:cubic-bezier(.16,1,.3,1);
      --r:6px; --base:120ms;
    }
    html.slide-in{ --base:200ms; }

    /* Warna gradasi didaftarkan supaya bisa "morph" halus antar halaman */
    @property --bg{ syntax:'<color>'; inherits:true; initial-value:#ffffff; }
    @property --c1{ syntax:'<color>'; inherits:true; initial-value:#ff8a3d; }
    @property --c2{ syntax:'<color>'; inherits:true; initial-value:#ffb35c; }
    @property --c3{ syntax:'<color>'; inherits:true; initial-value:#ffd9b8; }
    @property --c4{ syntax:'<color>'; inherits:true; initial-value:#ffe9d6; }

    *{ box-sizing:border-box; margin:0; padding:0; }
    html{ background:var(--paper); }
    body{
      min-height:100vh; background:var(--paper); color:#18181b;
      font-family:'Inter',system-ui,-apple-system,sans-serif; font-size:14px; line-height:1.55;
      -webkit-font-smoothing:antialiased; overflow-x:hidden;
    }
    a{ color:inherit; }
    ::selection{ background:var(--ink); color:#fff; }

    .stage{
      --bg:#ffffff; --c1:#ff8a3d; --c2:#ffb35c; --c3:#ffd9b8; --c4:#ffe9d6;
      transition:--bg 1.8s ease,--c1 1.8s ease,--c2 1.8s ease,--c3 1.8s ease,--c4 1.8s ease;
      position:relative; isolation:isolate; overflow:hidden; min-height:100vh; min-height:100svh; background:var(--paper);
    }
    .stage[data-palette="register"]{ --c1:#f97316; --c2:#fb923c; --c3:#fdba74; --c4:#ffd8b0; }
    .stage[data-palette="forgot"]  { --c1:#ea580c; --c2:#ff9d4d; --c3:#fed7aa; --c4:#fff1e0; }
    .stage[data-palette="reset"]   { --c1:#ff6a00; --c2:#ffa24d; --c3:#ffc896; --c4:#fff0e0; }

    /* ───────── Layar penuh ───────── */
    .board{
      position:relative; z-index:2; width:100%; min-height:100vh; min-height:100svh;
      display:grid; grid-template-columns:1fr; background:#fff;
      opacity:0; animation:fadein .9s var(--out) forwards; animation-delay:var(--base);
    }
    @media(min-width:900px){ .board{ grid-template-columns:minmax(18rem,32%) 1fr; } }
    @keyframes fadein{ from{ opacity:0; transform:translateY(24px); } to{ opacity:1; transform:none; } }

    /* Masuk dengan geser + pudar (dari halaman auth lain) */
    html.slide-in .board{ animation:slidein .9s var(--out) forwards; animation-delay:0ms; }
    @keyframes slidein{ from{ opacity:0; transform:translateX(36px); } to{ opacity:1; transform:none; } }

    .rise{ opacity:0; transform:translateY(16px); animation:rise .9s var(--out) forwards; animation-delay:calc(var(--base) + 150ms + var(--d,0)*70ms); }
    @keyframes rise{ to{ opacity:1; transform:none; } }

    /* ───────── Panel kiri: polos + gradasi bergerak ───────── */
    .panel{
      position:relative; overflow:hidden; background:var(--bg); min-height:6.5rem; padding:1.25rem 1.5rem;
      display:flex; flex-direction:column; justify-content:space-between; border-bottom:1px solid var(--line);
    }
    @media(min-width:900px){ .panel{ border-bottom:0; border-right:1px solid var(--line); padding:2.25rem; } }
    .blobs{ position:absolute; inset:-15%; z-index:0; pointer-events:none; transform:translate3d(calc(var(--mx,0)*-30px),calc(var(--my,0)*-30px),0); }
    .blobs i{ position:absolute; aspect-ratio:1; border-radius:50%; will-change:transform; }
    .b1{ width:85%; left:-10%; top:-6%;  background:radial-gradient(closest-side,var(--c1),transparent); animation:f1 19s ease-in-out infinite alternate; }
    .b2{ width:80%; left:38%; top:30%;   background:radial-gradient(closest-side,var(--c2),transparent); animation:f2 23s ease-in-out infinite alternate; }
    .b3{ width:90%; left:-24%; bottom:-14%; background:radial-gradient(closest-side,var(--c3),transparent); animation:f3 21s ease-in-out infinite alternate; }
    .b4{ width:70%; right:-18%; top:-4%; background:radial-gradient(closest-side,var(--c4),transparent); animation:f4 26s ease-in-out infinite alternate; }
    @keyframes f1{ to{ transform:translate(26%,18%) scale(1.15); } }
    @keyframes f2{ to{ transform:translate(-28%,-16%) scale(1.12); } }
    @keyframes f3{ to{ transform:translate(30%,-20%) scale(1.2); } }
    @keyframes f4{ to{ transform:translate(-22%,26%) scale(.9); } }
    .grain{
      position:absolute; inset:0; z-index:1; pointer-events:none; opacity:.12; mix-blend-mode:multiply;
      background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2' stitchTiles='stitch'/><feColorMatrix values='0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 .55 0'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>");
    }
    .logo{ position:relative; z-index:2; align-self:flex-start; font-size:1.125rem; font-weight:600; letter-spacing:-.02em; text-decoration:none; display:inline-flex; align-items:center; gap:.375rem; }
    .logo i{ width:.375rem; height:.375rem; border-radius:50%; background:var(--ink); }
    .tag{ position:relative; z-index:2; display:none; font-size:.8rem; font-weight:600; letter-spacing:-.01em; line-height:1.4; }
    .tag span{ color:rgba(0,0,0,.45); }
    @media(min-width:900px){ .tag{ display:block; } }

    /* ───────── Sisi kanan ───────── */
    .side{ display:flex; min-width:0; padding:clamp(1.25rem,3vw,2.25rem) clamp(1.25rem,5vw,4rem); }
    .inner{ width:100%; max-width:62rem; margin:0 auto; display:flex; flex-direction:column; }
    .topbar{ display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .chip{
      display:inline-flex; align-items:center; gap:.4rem; padding:.45rem .85rem; border-radius:4px; background:#f0f0f0;
      font-size:.75rem; font-weight:500; text-decoration:none; transition:background .3s,color .3s;
    }
    .chip:hover{ background:var(--ink); color:#fff; }
    .toplink{
      font-size:.75rem; font-weight:600; letter-spacing:.02em; text-decoration:none;
      padding-bottom:2px; border-bottom:1.5px solid var(--orange); transition:color .2s;
    }
    .toplink:hover{ color:var(--orange-d); }

    .body{ flex:1; display:grid; grid-template-columns:1fr; gap:2rem; align-items:center; padding:2.5rem 0; }
    @media(min-width:760px){ .body{ grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr); gap:3.5rem; } }

    .eyebrow{ font-size:10px; font-weight:600; letter-spacing:.2em; text-transform:uppercase; margin-bottom:.75rem; }
    .eyebrow .o{ color:var(--orange); }
    .head h1{ font-size:clamp(1.8rem,3.4vw,2.7rem); font-weight:600; letter-spacing:-.03em; line-height:1.1; }
    .head h1 .dim{ color:rgba(0,0,0,.27); }
    .lead{ margin-top:.875rem; font-size:.8125rem; color:rgba(0,0,0,.6); max-width:22rem; }

    .note{ border:1px solid var(--line); background:#fff; padding:.7rem .85rem; font-size:.78rem; margin-bottom:1rem; border-radius:var(--r); }
    .note.ok{ border-color:#86efac; background:#f0fdf4; color:#166534; }
    .note.bad{ border-color:#fca5a5; background:#fef2f2; color:#991b1b; }

    .fields{ display:grid; gap:.75rem; }
    .board.shake .fields{ animation:shake .55s var(--out) calc(var(--base) + 600ms); }
    @keyframes shake{ 20%{ transform:translateX(-8px); } 40%{ transform:translateX(7px); } 60%{ transform:translateX(-4px); } 80%{ transform:translateX(2px); } }
    .row{ display:grid; grid-template-columns:1fr; gap:.75rem; }
    .row.opts{ display:flex; align-items:center; justify-content:space-between; padding:0 .15rem; font-size:.78rem; }

    .cell{ position:relative; height:3.4rem; background:var(--field); border:1px solid var(--line); border-radius:var(--r); transition:border-color .3s,box-shadow .3s,background .3s; }
    .cell:hover{ border-color:rgba(0,0,0,.3); }
    .cell:focus-within{ background:#fff; border-color:var(--ink); box-shadow:0 0 0 3px rgba(249,115,22,.18); }
    .cell input{
      position:absolute; inset:0; width:100%; height:100%; padding:1.2rem 2.9rem 0 .9rem;
      border:0; background:transparent; outline:none; font:inherit; font-size:.875rem; color:var(--ink); border-radius:var(--r);
    }
    .cell label{
      position:absolute; left:.9rem; top:50%; transform:translateY(-50%); transform-origin:left;
      color:var(--mute); font-size:.84rem; pointer-events:none; transition:top .3s var(--out),transform .3s var(--out),color .3s;
    }
    .cell input:focus + label,
    .cell input:not(:placeholder-shown) + label{ top:.82rem; transform:translateY(-50%) scale(.76); }
    .cell input:-webkit-autofill + label{ top:.82rem; transform:translateY(-50%) scale(.76); }
    /* Autofill browser biasanya berwarna biru muda; paksa tetap abu netral */
    .cell input:-webkit-autofill,
    .cell input:-webkit-autofill:hover{ -webkit-box-shadow:0 0 0 1000px var(--field) inset; -webkit-text-fill-color:var(--ink); caret-color:var(--ink); }
    .cell input:-webkit-autofill:focus{ -webkit-box-shadow:0 0 0 1000px #fff inset; }
    .cell input:focus + label{ color:var(--orange-d); }
    .cell.bad{ border-color:var(--err); }
    .cell.bad label{ color:var(--err); }
    .eye{ position:absolute; right:.35rem; top:50%; transform:translateY(-50%); width:2.25rem; height:2.25rem; border:0; border-radius:4px; background:transparent; color:var(--mute); cursor:pointer; display:grid; place-items:center; transition:color .2s,background .2s; }
    .eye:hover{ color:var(--ink); background:rgba(0,0,0,.06); }
    .eye svg{ width:18px; height:18px; }
    .err{ margin:-.35rem 0 0; padding:0 .25rem; font-size:.72rem; color:var(--err); }

    .pwmeter{ display:flex; align-items:center; gap:.75rem; padding:0 .15rem; font-size:.72rem; color:var(--mute); }
    .pwmeter .bars{ display:flex; gap:4px; flex:0 0 6rem; }
    .pwmeter .bars i{ flex:1; height:4px; border-radius:2px; background:rgba(0,0,0,.1); transition:background .3s; }
    .pwmeter[data-s="1"] .bars i:nth-child(-n+1){ background:#ef4444; }
    .pwmeter[data-s="2"] .bars i:nth-child(-n+2){ background:#f59e0b; }
    .pwmeter[data-s="3"] .bars i:nth-child(-n+3){ background:#84cc16; }
    .pwmeter[data-s="4"] .bars i{ background:#22c55e; }
    .pwmeter .match{ margin-left:auto; font-style:normal; }
    .pwmeter .match.ok{ color:#15803d; } .pwmeter .match.no{ color:var(--err); }

    .chk{ display:flex; align-items:center; gap:.6rem; cursor:pointer; color:rgba(0,0,0,.6); }
    .chk input{ appearance:none; -webkit-appearance:none; width:1.05rem; height:1.05rem; border:1.5px solid var(--ink); border-radius:3px; display:grid; place-content:center; background:#fff; cursor:pointer; transition:background .2s,border-color .2s; }
    .chk input:checked{ background:var(--orange); border-color:var(--orange); }
    .chk input:checked::after{ content:''; width:7px; height:4px; border-left:2px solid #fff; border-bottom:2px solid #fff; transform:rotate(-45deg) translate(1px,-1px); }
    .u{ text-decoration:underline; text-decoration-thickness:1px; text-underline-offset:3px; transition:color .2s; }
    .u:hover{ color:var(--orange-d); }

    /* Tombol utama: hitam; saat hover oranye menyapu dari kiri sampai memenuhi tombol (menyatu dengan blok panah). Tidak ada gerakan lain. */
    .go{
      position:relative; display:flex; align-items:stretch; width:100%; height:3.4rem; margin-top:.25rem; padding:0;
      border:0; border-radius:var(--r); background:var(--ink); color:#fff;
      font:inherit; font-size:.875rem; font-weight:500; cursor:pointer; overflow:hidden; text-align:left;
      transition:transform .5s var(--out),box-shadow .5s var(--out);
    }
    .go:hover{ transform:translateY(-1px); box-shadow:0 14px 28px -14px rgba(249,115,22,.7); }
    .go:active{ transform:translateY(0); box-shadow:0 6px 14px -10px rgba(249,115,22,.7); }
    .go:focus-visible{ outline:2px solid var(--orange); outline-offset:3px; }
    .go::before{ content:''; position:absolute; inset:0; background:var(--go-fill); transform:translateX(-101%); transition:transform .65s var(--out); }
    .go:hover::before{ transform:none; }
    .go .t{ position:relative; z-index:1; flex:1; display:flex; align-items:center; padding:0 1.25rem; }
    .go .a{
      position:relative; z-index:1; flex:none; width:3.75rem; display:grid; place-items:center;
      background:var(--orange); color:#fff;
    }
    .go .a svg{ width:22px; height:22px; }
    .go.busy{ pointer-events:none; }
    .go.busy .a svg{ display:none; }
    .go.busy .a::after{ content:''; width:16px; height:16px; border:2px solid #fff; border-right-color:transparent; border-radius:50%; animation:spin .7s linear infinite; }
    @keyframes spin{ to{ transform:rotate(360deg); } }

    .foot{ margin-top:auto; padding-top:1rem; border-top:1px solid var(--line); font-size:.72rem; color:var(--mute); line-height:1.65; }

    /* ───────── Ke home: layar putih menyebar dari tengah ───────── */
    .veil{ position:fixed; inset:0; z-index:90; background:#fff; pointer-events:none; clip-path:circle(0% at 50% 50%); }

    @media (prefers-reduced-motion:reduce){
      *,*::before,*::after{ animation-duration:.01ms !important; animation-delay:0ms !important; transition-duration:.01ms !important; }
      .blobs i{ animation:none; }
    }
  </style>
</head>

<body>
  <div class="stage" id="frame" data-palette="@yield('palette', 'login')">

    <section class="board @if($errors->any()) shake @endif" id="board">

      {{-- Panel kiri: polos dengan gradasi bergerak --}}
      <aside class="panel">
        <div class="blobs"><i class="b1"></i><i class="b2"></i><i class="b3"></i><i class="b4"></i></div>
        <div class="grain"></div>
        <a href="{{ route('home') }}" class="logo" data-wipe="Home" aria-label="ERA, ke beranda">ERA<i></i></a>
        <p class="tag">Pilih. Kirim. Seleksi.<br><span>Berkarya.</span></p>
      </aside>

      {{-- Sisi kanan --}}
      <div class="side">
        <div class="inner">
          <div class="topbar rise" style="--d:0">
            <a href="{{ route('home') }}" class="chip" data-wipe="Home"><span aria-hidden="true">←</span> Kembali ke home</a>
            @yield('cta')
          </div>

          <div class="body">
            <div class="head rise" style="--d:1">
              <p class="eyebrow">@yield('eyebrow')</p>
              <h1>@yield('heading')</h1>
              <p class="lead">@yield('lead')</p>
            </div>

            <div class="form-col">
              @if(session('status'))
                <div class="note ok rise" style="--d:1">{{ session('status') }}</div>
              @endif

              @yield('content')
            </div>
          </div>

          @hasSection('footnote')
            <p class="foot rise" style="--d:8">@yield('footnote')</p>
          @endif
        </div>
      </div>
    </section>
  </div>

  {{-- Layar putih untuk transisi ke home --}}
  <div class="veil" id="veil" aria-hidden="true"></div>

  <script>
  (() => {
    const frame = document.getElementById('frame');
    const board = document.getElementById('board');
    const veil = document.getElementById('veil');
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const VARS = ['--bg', '--c1', '--c2', '--c3', '--c4'];
    let leaving = false, curAnim = null;

    /* 1. Gradasi: lanjut dari warna halaman sebelumnya, lalu morph ke warna halaman ini */
    try {
      const prev = JSON.parse(sessionStorage.getItem('era-pal') || 'null');
      if (prev && prev.length === VARS.length) {
        frame.style.transition = 'none';
        VARS.forEach((v, i) => frame.style.setProperty(v, prev[i]));
        void frame.offsetWidth;
        frame.style.transition = '';
        requestAnimationFrame(() => requestAnimationFrame(() => VARS.forEach(v => frame.style.removeProperty(v))));
      }
    } catch (e) {}

    /* 2. Pindah halaman: geser + pudar (antar halaman auth) atau layar putih dari tengah (ke home) */
    function leave(url, mode) {
      if (leaving) return;
      leaving = true;
      try {
        const cs = getComputedStyle(frame);
        sessionStorage.setItem('era-pal', JSON.stringify(VARS.map(v => cs.getPropertyValue(v).trim())));
        if (mode === 'slide') sessionStorage.setItem('era-slide', '1');
      } catch (e) {}
      const nav = () => { location.href = url; };
      if (reduce || !board.animate) { nav(); return; }

      if (mode === 'home') {
        curAnim = veil.animate(
          [{ clipPath: 'circle(0% at 50% 50%)' }, { clipPath: 'circle(80% at 50% 50%)' }],
          { duration: 850, easing: 'cubic-bezier(.76,0,.24,1)', fill: 'forwards' }
        );
      } else {
        curAnim = board.animate(
          [{ opacity: 1, transform: 'translateX(0)' }, { opacity: 0, transform: 'translateX(-36px)' }],
          { duration: 420, easing: 'cubic-bezier(.4,0,.2,1)', fill: 'forwards' }
        );
      }
      curAnim.finished.then(() => setTimeout(nav, 40)).catch(nav);
      setTimeout(nav, 1800);
    }

    document.addEventListener('click', e => {
      const a = e.target.closest('a[data-wipe]');
      if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || a.target === '_blank') return;
      const href = a.getAttribute('href');
      if (!href || href.startsWith('mailto:') || href.startsWith('#')) return;
      const dest = new URL(href, location.href);
      if (dest.origin !== location.origin) return;
      if (dest.pathname === location.pathname && dest.search === location.search && dest.hash) return;
      e.preventDefault();
      leave(dest.href, a.dataset.wipe === 'Home' ? 'home' : 'slide');
    });

    /* Kembali dari bfcache (tombol back) */
    addEventListener('pageshow', e => {
      if (!e.persisted) return;
      leaving = false;
      if (curAnim) { curAnim.cancel(); curAnim = null; }
      document.querySelectorAll('.go.busy').forEach(b => { b.classList.remove('busy'); b.disabled = false; });
    });

    /* 3. Gradasi panel mengikuti kursor */
    if (!reduce) {
      let tx = 0, ty = 0, cx = 0, cy = 0;
      addEventListener('pointermove', e => { tx = e.clientX / innerWidth - .5; ty = e.clientY / innerHeight - .5; });
      (function loop() {
        cx += (tx - cx) * .05; cy += (ty - cy) * .05;
        frame.style.setProperty('--mx', cx.toFixed(4));
        frame.style.setProperty('--my', cy.toFixed(4));
        requestAnimationFrame(loop);
      })();
    }

    /* 4. Tombol lihat/sembunyikan password */
    const EYE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
    const EYE_OFF = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.6 10.6 0 0 1 12 19c-6.4 0-10-7-10-7a17.7 17.7 0 0 1 4.1-4.9M9.9 5.2A9.7 9.7 0 0 1 12 5c6.4 0 10 7 10 7a17.8 17.8 0 0 1-2.2 3.2M3 3l18 18"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';
    document.querySelectorAll('[data-toggle-pw]').forEach(btn => {
      btn.innerHTML = EYE;
      btn.addEventListener('click', () => {
        const input = btn.closest('.cell').querySelector('input');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.innerHTML = show ? EYE_OFF : EYE;
        btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
      });
    });

    /* 5. Panah tombol utama: chevron ala iOS (kepala panah saja, tanpa badan) */
    const CHEV = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 4.5 16.5 12 9 19.5"/></svg>';
    document.querySelectorAll('.go .a').forEach(a => { a.innerHTML = CHEV; });

    /* 6. Indikator kekuatan password + cek kecocokan */
    const HINTS = ['Minimal 8 karakter', 'Masih lemah', 'Lumayan', 'Bagus', 'Kuat banget'];
    document.querySelectorAll('.pwmeter').forEach(meter => {
      const form = meter.closest('form');
      const pw = form.querySelector('input[name="password"]');
      const cf = form.querySelector('input[name="password_confirmation"]');
      const txt = meter.querySelector('[data-hint]');
      const match = meter.querySelector('[data-match]');
      const score = v => {
        if (!v) return 0;
        let s = 0;
        if (v.length >= 8) s++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
        if (/\d/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v)) s++;
        return v.length < 8 ? Math.min(s, 1) : Math.max(s, 1);
      };
      const update = () => {
        const s = score(pw.value);
        meter.dataset.s = s;
        txt.textContent = HINTS[s];
        if (!match || !cf) return;
        if (!cf.value) { match.textContent = ''; match.className = 'match'; return; }
        const ok = cf.value === pw.value;
        match.textContent = ok ? 'Cocok ✓' : 'Belum sama';
        match.className = 'match ' + (ok ? 'ok' : 'no');
      };
      pw.addEventListener('input', update);
      if (cf) cf.addEventListener('input', update);
      update();
    });

    /* 7. Status loading saat form dikirim */
    document.querySelectorAll('form[data-loading]').forEach(form => {
      form.addEventListener('submit', () => {
        const btn = form.querySelector('.go');
        if (!btn || !form.checkValidity()) return;
        const t = btn.querySelector('.t');
        if (t) t.textContent = btn.dataset.busy || 'Sebentar ya…';
        setTimeout(() => { btn.classList.add('busy'); btn.disabled = true; }, 0);
      });
    });
  })();
  </script>
</body>
</html>