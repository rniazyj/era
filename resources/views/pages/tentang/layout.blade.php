<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Tentang Kami') - PT Equityworld Futures</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  {{-- Animasi aktif hanya kalau pengguna tidak meminta "reduced motion" --}}
  <script>if (!matchMedia('(prefers-reduced-motion: reduce)').matches) document.documentElement.classList.add('js');</script>
  <style>
    :root {
      --nav-h: 4rem;
      --bg: #ffffff;
      --card: #f6f5f3;
      --card-dark: #ecebe8;
      --ink: #141414;
      --ink-soft: #3f3f46;
      --muted: #71717a;
      --orange: #f97316;
      --orange-hover: #ea580c;
      --orange-dark: #c2570c;
      --orange-soft: #fff1e4;
      --line: rgba(20,20,20,.1);
      --shadow: 0 1px 2px rgba(20,20,20,.04), 0 8px 24px rgba(20,20,20,.06);
      --head: 'Outfit', 'Plus Jakarta Sans', system-ui, sans-serif;
      --spring: cubic-bezier(.34, 1.56, .64, 1);
    }

    /* Reset hanya untuk isi halaman (nol specificity), supaya navbar & footer dari partials tidak ikut terganggu */
    :where(.ab-main) *, :where(.ab-main) *::before, :where(.ab-main) *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :where(.ab-main) a { color: inherit; }
    :where(.ab-main) svg { display: block; }
    :where(.ab-foot) *, :where(.ab-foot) *::before, :where(.ab-foot) *::after { box-sizing: border-box; }

    html { scroll-behavior: smooth; }
    [hidden] { display: none !important; }
    [id] { scroll-margin-top: calc(var(--nav-h) + 1.5rem); }
    body {
      margin: 0; background: var(--bg); color: var(--ink);
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif; font-size: 16px; line-height: 1.5;
      -webkit-font-smoothing: antialiased; overflow-x: hidden;
    }
    .ab-wrap { max-width: 1160px; margin: 0 auto; padding-inline: 1.5rem; }
    .ab-main { padding-top: calc(var(--nav-h) + .5rem); position: relative; z-index: 1; font-size: 15px; line-height: 1.65; }
    .ab-main.no-pad { padding-top: 0; }

    /* progress scroll */
    .ab-progress { position: fixed; top: 0; left: 0; right: 0; height: 3px; z-index: 60; background: var(--orange); transform: scaleX(0); transform-origin: 0 50%; pointer-events: none; }

    /* ---------- elemen kecil ---------- */
    .ab-tag, .ab-badge { display: inline-flex; align-items: center; gap: .35rem; padding: .3rem .8rem; border-radius: 99px; border: 1px solid rgba(20,20,20,.2); background: #fff; font-size: .66rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--ink); line-height: 1.3; }
    .ab-badge.ok { background: var(--orange-soft); border-color: transparent; color: var(--orange-dark); }
    .ab-badge.dark { background: var(--ink); border-color: var(--ink); color: #fff; }
    .ab-arrow { flex: none; width: 2.1rem; height: 2.1rem; border-radius: 50%; background: #fff; display: grid; place-items: center; color: var(--ink); transition: background .25s, color .25s; }
    .ab-arrow svg { width: 13px; height: 13px; transition: transform .3s; }
    .ab-card:hover .ab-arrow svg, .ab-visi:hover .ab-arrow svg { transform: rotate(45deg); }
    .ab-arrow:hover { background: var(--ink); color: #fff; }
    .ab-cardtop { display: flex; align-items: center; justify-content: space-between; gap: .6rem; margin-bottom: 1.4rem; }

    /* ---------- tipografi ---------- */
    .ab-h1 { font-family: var(--head); font-size: clamp(2.3rem, 5vw, 4rem); font-weight: 300; letter-spacing: -.03em; line-height: 1.08; margin-top: 1.2rem; color: var(--ink); }
    .ab-h2 { font-family: var(--head); font-size: clamp(1.8rem, 3.2vw, 2.6rem); font-weight: 400; letter-spacing: -.025em; line-height: 1.12; color: var(--ink); }
    .ab-h1 em, .ab-h2 em, .ab-cta h2 em { font-style: normal; font-weight: 600; color: var(--orange); }
    .ab-lead { margin-top: 1.2rem; max-width: 34rem; font-size: 1rem; color: var(--muted); }
    .ab-kicker { font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--orange-dark); margin-bottom: .7rem; }
    .ab-sec { padding-block: 4.5rem; }
    .ab-sec-head { max-width: 40rem; margin-bottom: 2.2rem; }
    .ab-sec-head p { margin-top: .8rem; color: var(--muted); }
    .ab-sec-head.center { text-align: center; margin-inline: auto; }

    /* judul: kata muncul satu per satu */
    .w { display: inline-block; overflow: hidden; vertical-align: top; padding-bottom: .16em; margin-bottom: -.16em; }
    .w .i { display: inline-block; }
    .js .w .i { transform: translateY(115%); }
    .js [data-reveal] { opacity: 0; }
    .js [data-stagger] > * { opacity: 0; }

    /* ---------- tombol: membal, tertekan, ada bayangan (tanpa bergerak mengikuti kursor) ---------- */
    .ab-actions { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; margin-top: 1.8rem; }
    .ab-btn { --sh: 234, 88, 12; display: inline-flex; align-items: center; justify-content: center; gap: .5rem; padding: .75rem 1.35rem; border-radius: 99px; font: inherit; font-weight: 600; font-size: .85rem; text-decoration: none; cursor: pointer; border: 1px solid transparent; background: var(--orange); color: var(--ink); }
    .ab-btn svg { width: 14px; height: 14px; }
    .ab-btn:hover { background: var(--orange-hover); }
    .ab-btn.lg { padding: .95rem 1.8rem; font-size: .92rem; }
    .ab-btn.icon { width: 2.75rem; height: 2.75rem; padding: 0; }
    .ab-btn.icon.lg { width: 3.1rem; height: 3.1rem; }
    .ab-btn.ghost { --sh: 20, 20, 20; background: #fff; color: var(--ink); border-color: var(--line); }
    .ab-btn.ghost:hover { background: #fff; border-color: var(--ink); }
    .ab-btn.sm { padding: .5rem 1rem; font-size: .78rem; }

    .ab-btn, .ab-tile, .ab-copy, .ab-filters button {
      transition: background .2s, color .2s, border-color .2s, box-shadow .25s, transform .45s var(--spring);
      -webkit-tap-highlight-color: transparent;
    }
    .ab-tile, .ab-copy, .ab-filters button { --sh: 20, 20, 20; }
    .ab-btn:hover, .ab-tile:hover, .ab-copy:hover, .ab-filters button:hover {
      transform: scale(.96);
      box-shadow: 0 10px 22px -8px rgba(var(--sh), .55);
    }
    .ab-btn:active, .ab-tile:active, .ab-copy:active, .ab-filters button:active {
      transform: scale(.91);
      box-shadow: 0 2px 6px -2px rgba(var(--sh), .5), inset 0 2px 6px rgba(0,0,0,.18);
      transition-duration: .1s;
    }
    .ab-btn:focus-visible, .ab-tile:focus-visible, .ab-filters button:focus-visible { outline: 2px solid var(--ink); outline-offset: 3px; }

    /* ---------- hero foto penuh: foto diam, konten putih naik menimpa ---------- */
    .ab-cover { position: relative; z-index: 1; min-height: 100vh; min-height: 100svh; display: flex; flex-direction: column; color: #fff; }
    .ab-cover-bg { position: fixed; inset: 0; z-index: 0; background-image: var(--img), radial-gradient(60rem 30rem at 85% 8%, rgba(249,115,22,.35), transparent 60%), linear-gradient(135deg, #1f1f22, #0b0b0c); background-size: cover; background-position: center; background-repeat: no-repeat; will-change: transform; }
    .ab-cover-bg::after { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(8,8,9,.5) 0%, rgba(8,8,9,.16) 50%, rgba(8,8,9,0) 100%), linear-gradient(0deg, rgba(8,8,9,.35), transparent 35%); }
    .ab-cover-inner { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; width: 100%; max-width: 1320px; margin: 0 auto; padding: calc(var(--nav-h) + 2rem) 1.5rem 4.4rem; }
    .ab-cover-body { margin-top: auto; padding: 3rem 0 2.4rem; max-width: 46rem; }
    .ab-cover .ab-h1 { margin-top: 0; color: #fff; font-size: clamp(2.6rem, 6vw, 4.7rem); line-height: 1.1; text-shadow: 0 2px 28px rgba(0,0,0,.25); }
    .ab-cover .ab-h1 em { color: #fff; font-weight: 600; }
    .ab-hl { display: block; }
    .ab-rule { display: inline-block; vertical-align: middle; width: clamp(4rem, 12vw, 9rem); height: 2px; margin-left: 1.2rem; background: var(--orange); transform-origin: 0 50%; }
    .js .ab-rule { transform: scaleX(0); }
    .ab-ctagroup { display: inline-flex; align-items: center; gap: .4rem; margin-top: 2rem; }

    .ab-tiles { display: grid; grid-template-columns: repeat(auto-fit, minmax(9.5rem, 1fr)); gap: .7rem; }
    .ab-tile { display: flex; align-items: center; justify-content: space-between; gap: .6rem; padding: .95rem 1.15rem; border-radius: 16px; background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.3); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); color: #fff; text-decoration: none; font-size: .68rem; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; }
    .ab-tile svg { flex: none; width: 13px; height: 13px; }
    .ab-tile:hover { background: var(--orange); border-color: var(--orange); color: var(--ink); }
    .ab-tile.on { background: #fff; color: var(--ink); border-color: #fff; }
    .ab-tile.on:hover { background: var(--orange); border-color: var(--orange); }

    /* lapisan putih yang naik menutupi foto */
    .ab-sheet { position: relative; z-index: 2; background: var(--bg); border-radius: 32px 32px 0 0; margin-top: -2.2rem; box-shadow: 0 -18px 50px rgba(0,0,0,.12); padding-top: .5rem; }

    /* ---------- profil ---------- */
    .ab-profil { display: grid; grid-template-columns: 1fr 1.1fr; gap: 3rem; align-items: start; }
    .ab-profil-text { font-size: 1.05rem; line-height: 1.75; color: var(--ink-soft); white-space: pre-line; }
    .ab-chips { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1.6rem; }
    .ab-chip { display: inline-flex; align-items: center; gap: .45rem; padding: .45rem .9rem; border-radius: 99px; background: var(--card); font-size: .78rem; font-weight: 600; color: var(--ink-soft); }
    .ab-chip b { color: var(--orange); }
    .ab-chip .dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.2); }

    /* ---------- marquee legalitas ---------- */
    .ab-marquee { overflow: hidden; border-block: 1px solid var(--line); padding: 1rem 0; margin-block: 1rem 3rem; -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent); mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent); }
    .ab-marquee-track { display: flex; gap: 3rem; width: max-content; animation: abMq 40s linear infinite; }
    .ab-marquee:hover .ab-marquee-track { animation-play-state: paused; }
    .ab-marquee span { display: inline-flex; align-items: center; gap: 3rem; font-weight: 700; font-size: .82rem; letter-spacing: .08em; text-transform: uppercase; white-space: nowrap; color: var(--ink-soft); }
    .ab-marquee span::after { content: "✦"; color: var(--orange); }
    @keyframes abMq { to { transform: translateX(-50%); } }

    /* ---------- statistik ---------- */
    .ab-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 1rem; }
    .ab-stat { padding: 1.6rem; border-radius: 22px; background: var(--card); }
    .ab-stat:nth-child(2) { background: var(--orange); }
    .ab-stat > * { position: relative; }
    .ab-stat strong { display: block; font-family: var(--head); font-size: clamp(2.2rem, 4vw, 3.1rem); font-weight: 400; letter-spacing: -.03em; line-height: 1; color: var(--ink); }
    .ab-stat > span { display: block; margin-top: .6rem; font-size: .82rem; font-weight: 500; color: var(--ink-soft); }

    /* ---------- kartu umum + cahaya mengikuti kursor ---------- */
    .ab-grid { display: grid; gap: 1rem; }
    .ab-grid.g2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .ab-grid.g3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .ab-grid.g4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .ab-card { position: relative; padding: 1.5rem; border-radius: 22px; background: var(--card); overflow: hidden; transition: background .25s; }
    .ab-card:hover { background: var(--card-dark); }
    .ab-card > * { position: relative; }
    .ab-card h4 { font-size: 1.05rem; font-weight: 600; letter-spacing: -.01em; margin-bottom: .45rem; color: var(--ink); }
    .ab-card p { font-size: .88rem; color: var(--muted); }
    .ab-num { width: 2.4rem; height: 2.4rem; border-radius: 50%; display: grid; place-items: center; background: var(--orange); color: var(--ink); font-weight: 700; font-size: .8rem; margin-bottom: 1.4rem; transition: transform .4s var(--spring); }
    .ab-card:hover .ab-num { transform: rotate(-12deg) scale(1.12); }

    .spot { position: relative; }
    .spot::before { content: ""; position: absolute; inset: 0; z-index: 0; pointer-events: none; opacity: 0; transition: opacity .3s; background: radial-gradient(340px circle at var(--mx, 50%) var(--my, 50%), rgba(249,115,22,.16), transparent 45%); }
    .spot:hover::before { opacity: 1; }
    .ab-stat:nth-child(2)::before, .ab-visi::before, .ab-cabang.pusat::before { background: radial-gradient(340px circle at var(--mx, 50%) var(--my, 50%), rgba(255,255,255,.4), transparent 45%); }

    /* ---------- filter ---------- */
    .ab-filters { display: flex; flex-wrap: wrap; gap: .45rem; justify-content: center; margin-bottom: 1.8rem; }
    .ab-filters button { font: inherit; font-weight: 600; font-size: .8rem; padding: .5rem 1.05rem; border-radius: 99px; border: 1px solid var(--line); background: #fff; color: var(--ink-soft); cursor: pointer; }
    .ab-filters button:hover { border-color: var(--orange); color: var(--orange-dark); }
    .ab-filters button.on { background: var(--ink); border-color: var(--ink); color: #fff; }

    /* ---------- visi & misi ---------- */
    .ab-visi { position: relative; overflow: hidden; padding: 2rem; border-radius: 22px; background: var(--orange); display: flex; flex-direction: column; justify-content: space-between; gap: 2rem; min-height: 22rem; }
    .ab-visi::after { content: ""; position: absolute; width: 20rem; height: 20rem; border-radius: 50%; right: -8rem; bottom: -9rem; background: rgba(255,255,255,.22); animation: abDrift 9s ease-in-out infinite alternate; }
    @keyframes abDrift { to { transform: translate(-2.5rem, -2rem) scale(1.12); } }
    .ab-visi > * { position: relative; z-index: 1; }
    .ab-visi .ab-tag { background: rgba(255,255,255,.6); border-color: transparent; }
    .ab-visi h3 { font-family: var(--head); font-size: clamp(1.4rem, 2.4vw, 1.9rem); font-weight: 400; line-height: 1.3; letter-spacing: -.02em; color: var(--ink); max-width: 30rem; }
    .ab-misi { position: relative; padding: 2rem; border-radius: 22px; background: var(--card); }
    .ab-misi > * { position: relative; }
    .ab-misi ul { list-style: none; display: grid; counter-reset: m; }
    .ab-misi li { counter-increment: m; display: flex; gap: .9rem; align-items: flex-start; padding: .85rem 0; border-bottom: 1px solid var(--line); font-size: .9rem; color: var(--ink-soft); transition: transform .25s, color .25s; }
    .ab-misi li:hover { transform: translateX(6px); color: var(--ink); }
    .ab-misi li:first-child { padding-top: 0; }
    .ab-misi li:last-child { border-bottom: 0; padding-bottom: 0; }
    .ab-misi li::before { content: counter(m, decimal-leading-zero); flex: none; width: 1.9rem; height: 1.9rem; border-radius: 50%; display: grid; place-items: center; background: var(--orange); color: var(--ink); font-weight: 700; font-size: .68rem; }

    /* ---------- timeline ---------- */
    .ab-tl { position: relative; max-width: 44rem; margin: 0 auto; padding-left: 3rem; }
    .ab-tl::before { content: ""; position: absolute; left: .9rem; top: .4rem; bottom: .4rem; width: 2px; background: var(--card-dark); }
    .ab-tl-fill { position: absolute; left: .9rem; top: .4rem; bottom: .4rem; width: 2px; background: var(--orange); transform-origin: 50% 0; }
    html:not(.js) .ab-tl-fill { transform: scaleY(1); }
    .ab-tl-item { position: relative; padding-bottom: 2.2rem; }
    .ab-tl-item:last-child { padding-bottom: 0; }
    .ab-tl-item .node { position: absolute; left: -3rem; top: .2rem; width: 1.85rem; height: 1.85rem; border-radius: 50%; display: grid; place-items: center; background: #fff; border: 2px solid var(--card-dark); transition: border-color .3s; }
    .ab-tl-item .node::after { content: ""; width: 8px; height: 8px; border-radius: 50%; background: #d4d4d8; transition: background .3s; }
    .ab-tl-item.on .node, html:not(.js) .ab-tl-item .node { border-color: var(--orange); }
    .ab-tl-item.on .node { animation: abPing 1.4s ease-out 1; }
    @keyframes abPing { 0% { box-shadow: 0 0 0 0 rgba(249,115,22,.5); } 100% { box-shadow: 0 0 0 12px rgba(249,115,22,0); } }
    .ab-tl-item.on .node::after, html:not(.js) .ab-tl-item .node::after { background: var(--orange); }
    .ab-tl-item .yr { display: inline-block; padding: .15rem .7rem; border-radius: 99px; background: var(--orange); color: var(--ink); font-size: .74rem; font-weight: 700; letter-spacing: .04em; }
    .ab-tl-item h4 { font-size: 1.15rem; font-weight: 600; letter-spacing: -.015em; margin: .5rem 0 .3rem; }
    .ab-tl-item p { color: var(--muted); font-size: .9rem; max-width: 34rem; }

    /* ---------- legalitas ---------- */
    .ab-legal-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.2rem; }
    .ab-legal-ico { width: 3rem; height: 3rem; border-radius: 14px; display: grid; place-items: center; font-weight: 700; font-size: .76rem; color: var(--ink); background: var(--orange); transition: transform .4s var(--spring); }
    .ab-card:hover .ab-legal-ico { transform: rotate(-8deg) scale(1.08); }
    .ab-nomor { margin-top: 1rem; display: flex; align-items: center; justify-content: space-between; gap: .6rem; padding: .5rem .5rem .5rem 1rem; border-radius: 99px; background: #fff; font-size: .78rem; font-weight: 600; color: var(--ink-soft); word-break: break-word; }
    .ab-copy { flex: none; font: inherit; font-weight: 600; font-size: .72rem; padding: .4rem .85rem; border-radius: 99px; border: 0; background: var(--ink); color: #fff; cursor: pointer; }
    .ab-copy:hover { background: var(--orange); color: var(--ink); }
    .ab-note { display: flex; justify-content: center; margin-bottom: 1.6rem; }

    /* ---------- cabang ---------- */
    .ab-cabang.pusat { grid-column: 1 / -1; display: grid; grid-template-columns: 1.2fr 1fr; gap: 2rem; align-items: center; padding: 2rem; background: var(--orange); }
    .ab-cabang.pusat:hover { background: var(--orange); }
    .ab-cabang.pusat p, .ab-cabang.pusat .ab-meta { color: rgba(20,20,20,.75); }
    .ab-cabang.pusat h4 { font-family: var(--head); font-size: 1.6rem; font-weight: 500; letter-spacing: -.02em; }
    .ab-cabang.pusat .ab-btn { --sh: 20, 20, 20; background: var(--ink); color: #fff; }
    .ab-cabang.pusat .ab-btn:hover { background: #000; }
    .ab-cabang.pusat .ab-btn.ghost { background: #fff; color: var(--ink); border-color: transparent; }
    .ab-city { height: 100%; min-height: 10rem; border-radius: 18px; display: grid; place-items: center; font-family: var(--head); font-size: 4rem; font-weight: 300; letter-spacing: -.04em; color: var(--ink); background: rgba(255,255,255,.4); }
    .ab-meta { display: flex; flex-direction: column; gap: .3rem; margin: .9rem 0 1.2rem; font-size: .84rem; color: var(--muted); }
    .ab-row { display: flex; flex-wrap: wrap; gap: .5rem; }

    /* ---------- produk ---------- */
    .ab-kode { width: 3rem; height: 3rem; border-radius: 14px; display: grid; place-items: center; font-weight: 700; font-size: .78rem; color: var(--c, var(--orange-dark)); background: color-mix(in srgb, var(--c, var(--orange)) 14%, #fff); transition: transform .4s var(--spring); }
    .ab-card:hover .ab-kode { transform: translateY(-4px) rotate(-6deg); }
    .ab-spark { position: absolute !important; right: 0; bottom: 0; width: 55%; height: 3.2rem; opacity: .45; }
    .ab-spark path { fill: none; stroke: var(--c, var(--orange)); stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; stroke-dasharray: 220; stroke-dashoffset: 220; transition: stroke-dashoffset 1.1s ease; vector-effect: non-scaling-stroke; }
    .ab-card:hover .ab-spark path { stroke-dashoffset: 0; }
    html:not(.js) .ab-spark path { stroke-dashoffset: 0; }
    .ab-produk p { padding-bottom: 2.4rem; }

    /* ---------- tim ---------- */
    .ab-tim { padding: 0; text-align: center; }
    .ab-avatar { height: 14rem; display: grid; place-items: center; font-family: var(--head); font-size: 3.4rem; font-weight: 300; letter-spacing: -.03em; color: var(--orange-dark); background: var(--orange-soft); overflow: hidden; }
    .ab-avatar img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s; }
    .ab-tim:hover .ab-avatar img { transform: scale(1.07); }
    .ab-tim-body { padding: 1.2rem 1.3rem 1.4rem; }
    .ab-tim-body h4 { margin-bottom: .15rem; }
    .ab-tim-body .jab { font-size: .85rem; font-weight: 500; color: var(--ink-soft); }
    .ab-tim-bio { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .4s ease; }
    .ab-tim-bio > div { overflow: hidden; }
    .ab-tim-bio p { padding-top: .8rem; font-size: .82rem; }
    .ab-tim:hover .ab-tim-bio { grid-template-rows: 1fr; }
    .ab-li { display: inline-grid; place-items: center; width: 2rem; height: 2rem; border-radius: 50%; background: #fff; color: var(--ink); margin-top: .8rem; transition: background .2s, color .2s; }
    .ab-li:hover { background: #0a66c2; color: #fff; }
    .ab-li svg { width: 14px; height: 14px; }
    .ab-empty { text-align: center; padding: 3.5rem 1rem; color: var(--muted); border: 1.5px dashed var(--line); border-radius: 22px; background: var(--card); }

    /* ---------- cta ---------- */
    .ab-cta { position: relative; overflow: hidden; margin: 2rem 0 4.5rem; padding: 3.5rem 2rem; border-radius: 28px; text-align: center; color: #fff; background: var(--ink); }
    .ab-cta::before { content: ""; position: absolute; width: 26rem; height: 26rem; border-radius: 50%; right: -9rem; top: -12rem; background: rgba(249,115,22,.22); animation: abDrift 8s ease-in-out infinite alternate; }
    .ab-cta > * { position: relative; }
    .ab-cta h2 { font-family: var(--head); font-size: clamp(1.8rem, 3.4vw, 2.7rem); font-weight: 300; letter-spacing: -.03em; line-height: 1.12; max-width: 38rem; margin: 0 auto; }
    .ab-cta p { margin: 1rem auto 0; max-width: 32rem; color: rgba(255,255,255,.7); }
    .ab-cta .ab-actions { justify-content: center; }
    .ab-cta .ab-btn.ghost { background: transparent; color: #fff; border-color: rgba(255,255,255,.35); }
    .ab-cta .ab-btn.ghost:hover { border-color: #fff; background: transparent; }

    /* ---------- footer (dari partials) & toast ---------- */
    /* Pembungkus ini memastikan footer berada di atas foto hero yang fixed */
    .ab-foot { position: relative; z-index: 3; background: var(--bg); }
    .ab-toast { position: fixed; left: 50%; bottom: 1.5rem; z-index: 300; translate: -50% 160%; padding: .65rem 1.2rem; border-radius: 99px; background: var(--ink); color: #fff; font-weight: 600; font-size: .85rem; transition: translate .35s ease; }
    .ab-toast.show { translate: -50% 0; }

    /* ---------- responsive ---------- */
    @media (max-width: 960px) {
      .ab-grid.g3, .ab-grid.g4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .ab-cabang.pusat { grid-template-columns: 1fr; }
      .ab-profil { grid-template-columns: 1fr; gap: 1.4rem; }
    }
    @media (max-width: 640px) {
      .ab-wrap { padding-inline: 1.1rem; }
      .ab-cover-inner { padding-inline: 1.1rem; }
      .ab-tiles { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .ab-grid.g2, .ab-grid.g3, .ab-grid.g4 { grid-template-columns: 1fr; }
      .ab-sec { padding-block: 3.2rem; }
      .ab-visi, .ab-misi { padding: 1.6rem; }
      .ab-cta { padding: 2.8rem 1.4rem; }
      .ab-sheet { border-radius: 24px 24px 0 0; }
      .ab-rule { display: none; }
    }
    @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } html { scroll-behavior: auto; } }
  </style>
  @stack('styles')
</head>
<body>

<div class="ab-progress" id="abProgress"></div>

@include('partials.navbar')

<main class="ab-main @yield('main_class')">
  @yield('konten')
</main>

{{-- GSAP dimuat SEBELUM footer, supaya skrip interaktif di footer (kalau ada) menemukan gsap --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<div class="ab-foot">
  @include('partials.footer')
</div>

<div class="ab-toast" id="abToast" role="status"></div>

<script>
(function () {
  const root = document.documentElement;
  const $  = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];

  // ===== Toast & salin (tidak butuh GSAP) =====
  const toast = $('#abToast');
  let toastTimer;
  const showToast = (msg) => {
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 1800);
  };
  document.addEventListener('click', (e) => {
    const b = e.target.closest('[data-copy]');
    if (!b) return;
    const teks = b.dataset.copy;
    (navigator.clipboard ? navigator.clipboard.writeText(teks) : Promise.reject())
      .then(() => showToast('Disalin ke clipboard'))
      .catch(() => {
        const t = document.createElement('textarea'); t.value = teks; document.body.appendChild(t);
        t.select(); document.execCommand('copy'); t.remove(); showToast('Disalin ke clipboard');
      });
  });

  // ===== Filter (tim, cabang, produk) — tanpa GSAP pun tetap jalan =====
  const tilesF = $$('.ab-tile[data-tile-filter]');
  $$('[data-filter-group]').forEach(group => {
    const target = $(group.dataset.filterGroup);
    if (!target) return;
    const btns  = $$('[data-filter]', group);
    const items = $$('[data-cat]', target);

    btns.forEach(btn => btn.addEventListener('click', () => {
      btns.forEach(b => b.classList.toggle('on', b === btn));
      const f    = btn.dataset.filter;
      tilesF.forEach(t => t.classList.toggle('on', t.dataset.tileFilter === f));
      const show = items.filter(i => f === 'semua' || i.dataset.cat === f);
      const hide = items.filter(i => !show.includes(i));
      const anim = window.gsap && root.classList.contains('js');

      if (!anim) { hide.forEach(i => i.hidden = true); show.forEach(i => i.hidden = false); return; }

      gsap.killTweensOf(items);
      gsap.to(hide, { opacity: 0, scale: .94, duration: .22, onComplete: () => hide.forEach(i => i.hidden = true) });
      show.forEach(i => i.hidden = false);
      gsap.fromTo(show, { opacity: 0, y: 26, scale: .96 }, { opacity: 1, y: 0, scale: 1, duration: .55, stagger: .06, ease: 'power3.out' });
    }));
  });

  // Tombol di bawah hero yang bertanda filter: ikut memilih filter di halaman
  tilesF.forEach(t => t.addEventListener('click', () => {
    const btn = $$('[data-filter-group] [data-filter]').find(b => b.dataset.filter === t.dataset.tileFilter);
    if (btn) btn.click();
  }));

  // ===== Animasi (butuh GSAP & tidak reduced-motion) =====
  if (!window.gsap || !window.ScrollTrigger || !root.classList.contains('js')) {
    root.classList.remove('js');
    return;
  }
  gsap.registerPlugin(ScrollTrigger);

  // Progress scroll
  ScrollTrigger.create({ start: 0, end: 'max', onUpdate: s => gsap.set('#abProgress', { scaleX: s.progress }) });

  // Judul: kata muncul satu per satu
  const splitWords = (el) => {
    const walk = (node) => {
      [...node.childNodes].forEach(n => {
        if (n.nodeType === 3) {
          const frag = document.createDocumentFragment();
          n.textContent.split(/(\s+)/).forEach(part => {
            if (!part) return;
            if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(' ')); return; }
            const w = document.createElement('span'); w.className = 'w';
            const i = document.createElement('span'); i.className = 'i'; i.textContent = part;
            w.appendChild(i); frag.appendChild(w);
          });
          node.replaceChild(frag, n);
        } else if (n.nodeType === 1) walk(n);
      });
    };
    walk(el);
    return $$('.i', el);
  };
  $$('[data-split]').forEach(el => {
    const words = splitWords(el);
    ScrollTrigger.create({
      trigger: el, start: 'top 92%', once: true,
      onEnter: () => gsap.to(words, { y: 0, duration: 1, stagger: .07, ease: 'power4.out', delay: .1 })
    });
  });

  // Reveal & stagger saat scroll
  const reveals = $$('[data-reveal]');
  gsap.set(reveals, { y: 30 });
  ScrollTrigger.batch(reveals, {
    start: 'top 92%', once: true,
    onEnter: (els) => gsap.to(els, { opacity: 1, y: 0, duration: .85, stagger: .09, ease: 'power3.out' })
  });
  $$('[data-stagger]').forEach(box => {
    const kids  = [...box.children];
    // Tombol (tile) hanya fade, supaya efek tekan/membal CSS-nya tidak bentrok dengan transform GSAP
    const tiles = kids.filter(k => k.classList.contains('ab-tile'));
    const rest  = kids.filter(k => !k.classList.contains('ab-tile'));
    gsap.set(rest, { y: 34, scale: .97 });
    ScrollTrigger.create({
      trigger: box, start: 'top 90%', once: true,
      onEnter: () => {
        gsap.to(tiles, { opacity: 1, duration: .8, stagger: .08, ease: 'power2.out' });
        gsap.to(rest, { opacity: 1, y: 0, scale: 1, duration: .8, stagger: .08, ease: 'power3.out' });
      }
    });
  });

  // Angka berjalan
  $$('[data-count]').forEach(el => {
    const end = parseFloat(el.dataset.count) || 0, o = { v: 0 };
    el.textContent = '0';
    ScrollTrigger.create({
      trigger: el, start: 'top 92%', once: true,
      onEnter: () => gsap.to(o, { v: end, duration: 1.8, ease: 'power2.out', onUpdate: () => el.textContent = Math.round(o.v) })
    });
  });

  // ===== Hero foto penuh (halaman yang memakainya) =====
  const bg = $('.ab-cover-bg');
  if (bg) gsap.fromTo(bg, { scale: 1.08 }, { scale: 1, duration: 2.2, ease: 'power2.out' });   // zoom-out halus, lalu diam

  const rule = $('.ab-rule');
  if (rule) gsap.to(rule, { scaleX: 1, duration: 1.1, ease: 'power3.out', delay: .9 });         // garis di samping judul memanjang

  const sheet = $('.ab-sheet');
  if (sheet) {                                                                                  // sudut lapisan putih perlahan rata
    gsap.fromTo(sheet, { borderTopLeftRadius: 32, borderTopRightRadius: 32 }, {
      borderTopLeftRadius: 0, borderTopRightRadius: 0, ease: 'none',
      scrollTrigger: { trigger: sheet, start: 'top 85%', end: 'top 8%', scrub: true }
    });
  }

  // ===== Interaksi kursor pada kartu (hanya perangkat dengan mouse) =====
  if (matchMedia('(hover: hover)').matches) {
    const cards = [...new Set($$('.ab-card, .ab-stat, .ab-visi, .ab-misi, [data-tilt]'))];
    cards.forEach(el => {
      el.classList.add('spot');
      gsap.set(el, { transformPerspective: 900 });
      const rx = gsap.quickTo(el, 'rotationX', { duration: .5, ease: 'power3' });
      const ry = gsap.quickTo(el, 'rotationY', { duration: .5, ease: 'power3' });
      el.addEventListener('mousemove', e => {
        const r = el.getBoundingClientRect();
        const px = (e.clientX - r.left) / r.width, py = (e.clientY - r.top) / r.height;
        const k = r.width > 600 ? 2.5 : 6;
        ry((px - .5) * k * 2);
        rx(-(py - .5) * k * 2);
        el.style.setProperty('--mx', (e.clientX - r.left) + 'px');
        el.style.setProperty('--my', (e.clientY - r.top) + 'px');
      });
      el.addEventListener('mouseleave', () => { rx(0); ry(0); });
    });
  }

  // Skrip khusus tiap halaman dijalankan setelah blok ini dan memakai gsap yang sama.
})();
</script>
@stack('scripts')

</body>
</html>