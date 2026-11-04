<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ERA - Membangun Masa Depan Karier &amp; Talenta</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Caveat:wght@500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
      colors: { paper:'#faf9fc', lime:'#e9f56b', brand:'#34d3b0' } } } };
  </script>
  <style>
/* Komponen & animasi pendukung landing.html */
.float-card{position:absolute;width:7rem;height:9rem;object-fit:cover;border-radius:1rem;box-shadow:0 16px 36px -14px rgba(0,0,0,.3)}
.bubble{position:absolute;padding:.2rem .65rem;border-radius:.75rem;font-size:.7rem;font-weight:600;z-index:5}
.avatar{width:100%;max-width:3.6rem;aspect-ratio:1;object-fit:cover;border-radius:1rem;margin-inline:auto;box-shadow:0 8px 20px -8px rgba(0,0,0,.3)}
#statement .w{opacity:.15}

/* ===== HERO (splash ERA + hero terang dengan foto yang membesar saat scroll) ===== */
body.lock{overflow:hidden}
.era-word{position:fixed;left:50%;top:50%;z-index:60;font-weight:600;letter-spacing:-.02em;font-size:clamp(3rem,9vw,5.5rem);line-height:1;transform:translate(-50%,-50%);transform-origin:center;pointer-events:none;white-space:nowrap}
.era-word .m{display:inline-block;overflow:hidden;vertical-align:top;padding-bottom:.08em}
.era-word .m>span{display:inline-block;transform:translateY(110%)}
.era-cap{position:fixed;left:50%;top:calc(50% + 3.6rem);transform:translateX(-50%);z-index:60;font-size:.7rem;letter-spacing:.22em;color:rgba(0,0,0,.45);opacity:0;white-space:nowrap}
#hero-h .w{display:inline-block;opacity:0;filter:blur(8px)}
.hero-card img{display:block;width:100%;height:100%;object-fit:cover}

/* ===== PILIHAN MAGANG / KERJA: tombol vertikal (tulisan menyamping) ===== */
[data-copy="full"]{opacity:0;visibility:hidden}
.vbtn{position:relative;display:flex;flex-direction:column;align-items:center;gap:.8rem;width:5.5rem;height:21rem;padding:.9rem 0 1.1rem;border-radius:9999px;background:#fff;border:1px solid rgba(0,0,0,.12);color:#111;cursor:pointer;transition:background .35s,color .35s,border-color .35s,box-shadow .35s}
@media(min-width:768px){.vbtn{width:6.75rem;height:27rem}}
.vbtn:hover{border-color:#111}
.vbtn-ico{flex:none;width:2.9rem;height:2.9rem;border-radius:9999px;background:#fff7ed;color:#ea580c;display:flex;align-items:center;justify-content:center;transition:background .35s,color .35s,transform .4s}
.vbtn-ico svg{width:1.45rem;height:1.45rem}
.vbtn:hover .vbtn-ico{transform:rotate(-12deg) scale(1.08)}
.vbtn-body{position:relative;flex:1;width:100%;min-height:0;overflow:hidden;display:flex;align-items:center;justify-content:center;-webkit-mask-image:linear-gradient(transparent,#000 14%,#000 86%,transparent);mask-image:linear-gradient(transparent,#000 14%,#000 86%,transparent)}
.vlabel,.vgrp span{writing-mode:vertical-rl;transform:rotate(180deg);white-space:nowrap;font-weight:600;font-size:1.2rem;letter-spacing:.14em;text-transform:uppercase}
@media(min-width:768px){.vlabel,.vgrp span{font-size:1.5rem}}
.vlabel{transition:opacity .3s}
.vtrack{position:absolute;top:0;left:0;right:0;display:flex;flex-direction:column;align-items:center;opacity:0;transition:opacity .3s;animation:vrun 7s linear infinite;animation-play-state:paused}
.vgrp{display:flex;flex-direction:column;align-items:center;gap:1.3rem;padding-bottom:1.3rem;flex:none}
.vgrp i{flex:none;width:.5rem;height:.5rem;border-radius:9999px;background:#f97316}
.vbtn:hover .vlabel,.vbtn:focus-visible .vlabel{opacity:0}
.vbtn:hover .vtrack,.vbtn:focus-visible .vtrack{opacity:1;animation-play-state:running}
.vbtn.active{background:#111;color:#fff;border-color:#111;box-shadow:0 24px 40px -18px rgba(249,115,22,.6)}
.vbtn.active .vbtn-ico{background:#f97316;color:#fff}
@keyframes vrun{to{transform:translateY(-50%)}}
@media (prefers-reduced-motion:reduce){.vtrack{animation:none}}

/* ===== PETA ALUR SELEKSI ===== */
#map-board{container-type:inline-size}
.map-stop{position:absolute;transform:translate(-50%,-50%);z-index:3}
.map-pin{position:relative;width:3rem;height:3rem;border-radius:9999px;background:#fff;color:#111;border:2px solid #111;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .3s,color .3s,border-color .3s}
@media(min-width:768px){.map-pin{width:3.75rem;height:3.75rem}}
.map-pin .ic{width:1.45rem;height:1.45rem}
@media(min-width:768px){.map-pin .ic{width:1.8rem;height:1.8rem}}
.map-pin:hover{border-color:#f97316}
.map-pin .num{position:absolute;top:-.5rem;right:-.5rem;width:1.35rem;height:1.35rem;border-radius:9999px;background:#111;color:#fff;border:2px solid #fff;font-size:.62rem;font-weight:700;display:flex;align-items:center;justify-content:center;transition:background .3s,color .3s}
.map-pin.reached{background:#f97316;border-color:#f97316;color:#fff}
.map-pin.reached .num{background:#fff;color:#f97316;border-color:#f97316}
.map-pin.current::before,.map-pin.current::after{content:"";position:absolute;inset:-2px;border-radius:9999px;border:2px solid #f97316;animation:ripple 2s ease-out infinite;pointer-events:none}
.map-pin.current::after{animation-delay:1s}
.map-pin.current .ic{animation:wiggle 1.8s ease-in-out infinite}
.map-pin .lbl{position:absolute;top:50%;transform:translateY(-50%);width:calc(38cqw - 2.6rem);max-width:13rem;padding:.5rem .65rem;border-radius:.9rem;background:rgba(255,255,255,.95);box-shadow:0 10px 24px -10px rgba(0,0,0,.35);color:rgba(0,0,0,.5);pointer-events:none;transition:color .3s,box-shadow .3s}
@media(min-width:768px){.map-pin .lbl{width:calc(38cqw - 3.2rem);padding:.65rem .9rem}}
.map-pin[data-side="r"] .lbl{left:calc(100% + .7rem);text-align:left}
.map-pin[data-side="l"] .lbl{right:calc(100% + .7rem);text-align:right}
.lbl small{display:block;margin-bottom:.15rem;font-size:.58rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:#f97316}
.lbl strong{display:block;font-size:clamp(.78rem,3.5cqw,1.2rem);font-weight:600;line-height:1.2}
.map-pin.current .lbl{color:#111;box-shadow:0 14px 30px -10px rgba(249,115,22,.55)}
.map-me{position:absolute;transform:translate(-50%,calc(-100% - 2.7rem));z-index:4;pointer-events:none}
.map-me span{position:relative;display:block;padding:.3rem .8rem;border-radius:9999px;background:#111;color:#fff;font-size:.72rem;font-weight:600;white-space:nowrap;box-shadow:0 6px 16px -4px rgba(0,0,0,.4);animation:bob 1.4s ease-in-out infinite}
@media(min-width:768px){.map-me span{font-size:.85rem;padding:.35rem .95rem}}
.map-me span::after{content:"";position:absolute;left:50%;top:100%;transform:translateX(-50%);border:5px solid transparent;border-top-color:#111}
.map-dot{position:absolute;left:50%;top:50%;width:.5rem;height:.5rem;margin:-.25rem;border-radius:9999px;background:#f97316;pointer-events:none}
.flow{animation:flow 2s linear infinite}
.drift{animation:drift 7s ease-in-out infinite alternate}
#route-prog{filter:drop-shadow(0 0 6px rgba(249,115,22,.55))}
@keyframes flow{to{stroke-dashoffset:-32}}
@keyframes drift{to{transform:translateY(10px)}}
@keyframes bob{50%{transform:translateY(-5px)}}
@keyframes ripple{0%{transform:scale(1);opacity:.8}100%{transform:scale(1.9);opacity:0}}
@keyframes wiggle{0%,100%{transform:rotate(0)}25%{transform:rotate(-12deg) scale(1.12)}75%{transform:rotate(12deg) scale(1.12)}}
@media (prefers-reduced-motion:reduce){.map-pin.current::before,.map-pin.current::after,.map-pin.current .ic{animation:none}}

/* ===== FOOTER INTERAKTIF (foto saja) ===== */
.ft-item{position:absolute;cursor:grab;touch-action:none;user-select:none;-webkit-user-select:none}
.ft-item.drag{cursor:grabbing}
.polaroid{background:#fff;padding:.4rem;border-radius:.25rem;border:1px solid rgba(0,0,0,.07);box-shadow:0 16px 36px -14px rgba(0,0,0,.35)}
.polaroid img{display:block;width:100%;height:100%;object-fit:cover;pointer-events:none}
.ft-link{display:inline-block;color:rgba(255,255,255,.8);transition:transform .25s,color .25s}
.ft-link:hover{color:#fb923c;transform:translateX(3px)}
</style>
</head>
<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

<!-- LOADER: wordmark ERA -->
<div class="era-word" id="era-word" aria-hidden="true"><span class="m"><span>E</span></span><span class="m"><span>R</span></span><span class="m"><span>A</span></span></div>
<div class="era-cap" id="era-cap">EQUITYWORLD RECRUITMENT ACCESS</div>

@include('partials.navbar', ['splash' => true])

<main>
<!-- 1. HERO -->
<section id="hero" class="relative h-screen min-h-[34rem] overflow-hidden bg-paper pt-16">
  <div id="hero-copy" class="relative z-10 flex flex-col items-center text-center px-6 pt-6 md:pt-10">
    <h1 id="hero-h" class="text-3xl md:text-5xl font-semibold tracking-tight leading-[1.1] max-w-4xl">Membangun Masa Depan Karier &amp; Talenta Bersama Kami.</h1>
    <p class="hero-sub mt-5 text-xs md:text-sm text-black/60 max-w-xl">Akses ribuan peluang karir profesional bergengsi serta program magang bersertifikat nasional. Dirancang untuk membentuk pemimpin inovatif dan talenta tangguh masa depan.</p>
    <div class="hero-sub mt-5 flex items-center gap-3">
      <a href="#showcase" class="px-6 py-2.5 rounded-full bg-orange-500 text-white text-xs font-medium hover:scale-105 transition">Eksplorasi Semua Program</a>
      <a href="#" class="px-6 py-2.5 rounded-full text-xs font-medium hover:scale-105 transition">Lihat Lowongan</a>
    </div>
  </div>

  <div id="card-l" class="hero-card absolute overflow-hidden rounded-3xl" style="left:-2vw;bottom:-10vh;width:26vw;height:28vh"><img src="https://picsum.photos/seed/pr1/600/500" alt=""></div>
  <div id="card-c" class="hero-card absolute overflow-hidden rounded-3xl" style="left:50%;bottom:-10vh;width:34vw;height:34vh">
    <img src="https://picsum.photos/seed/pr2/1600/1000" alt="">
    <div id="hero-cap" class="absolute inset-0 flex items-end p-6 md:p-12 bg-gradient-to-t from-black/55 via-transparent to-transparent opacity-0">
      <h2 class="text-2xl md:text-5xl font-semibold tracking-tight text-white max-w-xl leading-[1.08]">Satu portal untuk semua peluang karier.</h2>
    </div>
  </div>
  <div id="card-r" class="hero-card absolute overflow-hidden rounded-3xl" style="right:-3vw;bottom:-10vh;width:28vw;height:30vh"><img src="https://picsum.photos/seed/pr3/600/500" alt=""></div>
</section>

<!-- 5. STATEMENT + FLOATING CARDS -->
<section class="relative mt-24 min-h-[85vh] px-6 py-28 flex items-center justify-center overflow-hidden">
  <h2 id="statement" class="relative z-10 text-2xl md:text-4xl font-medium tracking-tight text-center max-w-3xl leading-snug">Akses ribuan peluang karir profesional bergengsi serta program magang bersertifikat nasional, dirancang untuk membentuk pemimpin inovatif dan talenta tangguh masa depan.</h2>
  <div class="absolute inset-0 hidden min-[1200px]:block pointer-events-none">
    <img class="float-card" data-speed="0.4"  style="left:2%;top:6%"   src="https://picsum.photos/seed/fl1/300/400" alt="">
    <img class="float-card" data-speed="0.8"  style="left:7%;top:38%"  src="https://picsum.photos/seed/fl2/300/400" alt="">
    <img class="float-card" data-speed="0.3"  style="left:3%;top:68%"  src="https://picsum.photos/seed/fl3/300/400" alt="">
    <img class="float-card" data-speed="0.7"  style="right:4%;top:14%" src="https://picsum.photos/seed/fl4/300/400" alt="">
    <img class="float-card" data-speed="0.5"  style="right:8%;top:56%" src="https://picsum.photos/seed/fl5/300/400" alt="">
    <span class="bubble bg-black text-white" style="left:4%;top:30.5%">98% Konversi</span>
    <span class="bubble bg-orange-500 text-white" style="right:6%;top:41%">150+ Posisi Aktif</span>
  </div>
</section>

<!-- 2. SHOWCASE: tombol vertikal di kiri (hover = teks berjalan + ganti penjelasan), teks di kanan -->
<section id="showcase" class="px-6 md:px-16 py-16 grid md:grid-cols-[2fr_3fr] gap-10 md:gap-16 items-center max-w-7xl mx-auto">
  <!-- Kiri: dua tombol tulisan menyamping -->
  <div class="rv flex flex-col items-center">
    <div class="flex gap-4 md:gap-6">
      <button type="button" class="vbtn" data-pick="intern" aria-pressed="true" aria-label="Program Magang">
        <span class="vbtn-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 2 8l10 5 10-5-10-5z"/><path d="M6 10.5V16c0 1.5 3 3 6 3s6-1.5 6-3v-5.5"/><path d="M22 8v6"/></svg></span>
        <span class="vbtn-body">
          <span class="vlabel">Program Magang</span>
          <span class="vtrack" aria-hidden="true">
            <span class="vgrp"><span>Program Magang</span><i></i><span>Program Magang</span><i></i></span>
            <span class="vgrp"><span>Program Magang</span><i></i><span>Program Magang</span><i></i></span>
          </span>
        </span>
      </button>
      <button type="button" class="vbtn" data-pick="full" aria-pressed="false" aria-label="Lowongan Kerja">
        <span class="vbtn-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 13h18"/><path d="M10 13v2h4v-2"/></svg></span>
        <span class="vbtn-body">
          <span class="vlabel">Lowongan Kerja</span>
          <span class="vtrack" aria-hidden="true">
            <span class="vgrp"><span>Lowongan Kerja</span><i></i><span>Lowongan Kerja</span><i></i></span>
            <span class="vgrp"><span>Lowongan Kerja</span><i></i><span>Lowongan Kerja</span><i></i></span>
          </span>
        </span>
      </button>
    </div>
    <p class="mt-5 text-[11px] text-black/40 text-center">Arahkan kursor ke tombol untuk berganti penjelasan</p>
  </div>

  <!-- Kanan: teks 2 versi, berganti saat tombol di kiri di-hover / diklik -->
  <div class="rv grid">
    <div data-copy="intern" style="grid-area:1/1">
      <p class="text-[10px] tracking-[.2em] font-semibold mb-3">PROGRAM MAGANG MAHASISWA</p>
      <h2 class="text-3xl md:text-5xl font-semibold tracking-tight leading-[1.1]">
        Magang,<br><span data-swap="Konversi.|Sertifikasi.|Karier." class="text-black/25 inline-block">Konversi.</span><br>
        <span class="text-orange-600">&amp;</span> <span class="text-orange-300">Non-Konversi</span>
      </h2>
      <p class="mt-4 text-sm text-black/60 max-w-md">Bangun pengalaman nyata sejak bangku kuliah lewat program magang bersertifikat nasional. Pilih jalur konversi dengan pengakuan SKS resmi atau non-konversi, lengkap dengan uang saku dan lingkungan kerja profesional.</p>
      <div class="mt-5 flex flex-wrap gap-2"><span class="px-3 py-1 rounded-full border border-black/10 text-xs text-black/60">Konversi SKS resmi</span><span class="px-3 py-1 rounded-full border border-black/10 text-xs text-black/60">Uang saku bulanan</span><span class="px-3 py-1 rounded-full border border-black/10 text-xs text-black/60">Sertifikat nasional</span></div>
    </div>
    <div data-copy="full" style="grid-area:1/1">
      <p class="text-[10px] tracking-[.2em] font-semibold mb-3">LOWONGAN KERJA PROFESIONAL</p>
      <h2 class="text-3xl md:text-5xl font-semibold tracking-tight leading-[1.1]">
        Karier,<br><span data-swap="Full-Time.|Profesional.|Berkembang." class="text-black/25 inline-block">Full-Time.</span><br>
        <span class="text-orange-600">&amp;</span> <span class="text-orange-300">Fresh Graduate</span>
      </h2>
      <p class="mt-4 text-sm text-black/60 max-w-md">Temukan lowongan kerja full-time dari perusahaan mitra terverifikasi, untuk fresh graduate hingga profesional berpengalaman. Gaji transparan, lokasi jelas, dan jenjang karier yang terstruktur.</p>
      <div class="mt-5 flex flex-wrap gap-2"><span class="px-3 py-1 rounded-full border border-black/10 text-xs text-black/60">Gaji transparan</span><span class="px-3 py-1 rounded-full border border-black/10 text-xs text-black/60">Mitra terverifikasi ISO</span><span class="px-3 py-1 rounded-full border border-black/10 text-xs text-black/60">Jenjang karier jelas</span></div>
    </div>
  </div>
</section>

<!-- 9. ALUR SELEKSI (teks di kiri, peta di kanan) -->
<section id="alur" class="px-6 md:px-16 py-20 max-w-7xl mx-auto">
  <div class="grid lg:grid-cols-[2fr_3fr] gap-10 lg:gap-16 items-center rv">
    <!-- Kiri: judul -->
    <div class="text-center lg:text-left">
      <p class="text-[10px] tracking-[.2em] font-semibold mb-3">PROSES <span class="text-orange-500">REKRUTMEN</span></p>
      <h2 class="text-3xl md:text-5xl font-semibold tracking-tight leading-[1.1]">Alur Seleksi yang<br><span class="text-black/25">Terstruktur &amp; Cepat</span></h2>
      <p class="mt-4 text-sm text-black/60 max-w-xl mx-auto lg:mx-0">Kami menghargai waktu dan komitmen Anda. Nikmati proses rekrutmen yang terstandarisasi, transparan, dan komunikatif dari awal hingga akhir.</p>
      <a href="#" class="inline-block mt-6 px-6 py-2.5 rounded-full bg-orange-500 text-white text-xs font-medium hover:scale-105 transition">Daftar</a>
    </div>

    <!-- Kanan: peta -->
    <div id="map-board" class="relative w-full max-w-2xl mx-auto aspect-[800/720] rounded-3xl border border-black/10 bg-white overflow-hidden" style="background-image:linear-gradient(rgba(0,0,0,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,.045) 1px,transparent 1px);background-size:44px 44px">
      <svg viewBox="0 0 800 720" class="absolute inset-0 w-full h-full" aria-hidden="true">
        <g class="drift" fill="#f4f4f5"><rect x="560" y="30" width="190" height="64" rx="14"/><rect x="30" y="220" width="110" height="130" rx="16"/><rect x="650" y="400" width="110" height="90" rx="14"/><rect x="40" y="590" width="170" height="70" rx="14"/><rect x="520" y="670" width="230" height="30" rx="12"/></g>
        <path d="M-20 530 C200 470 380 610 820 520" fill="none" stroke="#f1f1f1" stroke-width="28"/>
        <g class="drift" style="animation-delay:-3s" fill="#ececec"><circle cx="90" cy="130" r="10"/><circle cx="118" cy="150" r="7"/><circle cx="700" cy="330" r="11"/><circle cx="678" cy="354" r="7"/><circle cx="400" cy="60" r="8"/><circle cx="390" cy="670" r="9"/></g>
        <g transform="translate(62 62)"><circle r="18" fill="#fff" stroke="rgba(0,0,0,.15)"/><path d="M0 -10 L4.5 3.5 L0 0 L-4.5 3.5 Z" fill="#111"/></g>
        <path id="route" d="M310 110 C310 200 490 190 490 280 S310 360 310 450 S490 540 490 610" fill="none" stroke="#e7e5e4" stroke-width="26" stroke-linecap="round"/>
        <path class="flow" d="M310 110 C310 200 490 190 490 280 S310 360 310 450 S490 540 490 610" fill="none" stroke="#fff" stroke-width="3" stroke-dasharray="7 9" stroke-linecap="round"/>
        <path id="route-prog" d="M310 110 C310 200 490 190 490 280 S310 360 310 450 S490 540 490 610" fill="none" stroke="#f97316" stroke-width="7" stroke-linecap="round"/>
      </svg>

      <div class="map-stop"><button class="map-pin" aria-label="Langkah 01: Pilih Posisi Impian">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        <span class="num">1</span>
        <span class="lbl"><small>Langkah 01</small><strong>Pilih Posisi Impian</strong></span>
      </button></div>

      <div class="map-stop"><button class="map-pin" aria-label="Langkah 02: Kirim Berkas &amp; CV">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M12 17v-5"/><path d="m9.5 14 2.5-2.5 2.5 2.5"/></svg>
        <span class="num">2</span>
        <span class="lbl"><small>Langkah 02</small><strong>Kirim Berkas &amp; CV</strong></span>
      </button></div>

      <div class="map-stop"><button class="map-pin" aria-label="Langkah 03: Seleksi &amp; Wawancara">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1.1-4.6A8 8 0 1 1 21 12z"/><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"/></svg>
        <span class="num">3</span>
        <span class="lbl"><small>Langkah 03</small><strong>Seleksi &amp; Wawancara</strong></span>
      </button></div>

      <div class="map-stop"><button class="map-pin" aria-label="Langkah 04: Mulai Berkarya Bersama">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>
        <span class="num">4</span>
        <span class="lbl"><small>Langkah 04</small><strong>Mulai Berkarya Bersama</strong></span>
      </button></div>

      <div id="map-me" class="map-me"><span>Kamu di sini</span></div>
    </div>
  </div>
</section>

<!-- 7. COMMUNITY (avatar dibuat otomatis oleh script) -->
<section class="relative min-h-[85vh] py-20 overflow-hidden flex items-center justify-center">
  <div id="avatars" class="absolute inset-0 grid grid-cols-6 md:grid-cols-9 gap-4 p-4 content-between"></div>
  <div class="relative z-10 text-center bg-paper/85 backdrop-blur px-8 py-8 rounded-3xl rv">
    <h2 class="text-2xl md:text-4xl font-semibold tracking-tight">Siap Melangkah Menuju<br>Babak Baru Perjalanan<br>Karier Anda?</h2>
    <p class="mt-3 text-xs text-black/60 max-w-sm mx-auto">Bergabunglah dengan ribuan talenta yang telah menemukan ruang bertumbuh dan memberikan kontribusi nyata bagi ekosistem industri Indonesia.</p>
    <div class="mt-5 flex justify-center gap-3"><a href="#" class="px-5 py-2 rounded-full bg-orange-500 text-white text-xs font-medium">Daftar Sekarang</a><a href="#" class="px-5 py-2 rounded-full bg-black text-white text-xs font-medium">Jelajahi Posisi</a></div>
  </div>
</section>

</main>

<!-- 11. FOOTER: area foto putih (foto semua), bagian hitam full lebar tanpa lengkungan -->
<footer class="bg-paper">
  <div id="ft-top" class="relative z-0 h-[15rem] overflow-hidden bg-paper">
    <div class="ft-item polaroid" data-rot="-5" style="left:1%;bottom:-1.5rem;width:9.5rem;height:11rem"><img src="https://picsum.photos/seed/ft1/500/560" alt=""></div>
    <div class="ft-item polaroid hidden md:block" data-rot="3" style="left:12.5%;bottom:-.5rem;width:10rem;height:12.5rem"><img src="https://picsum.photos/seed/ft2/500/640" alt=""></div>
    <div class="ft-item polaroid" data-rot="-2" style="left:36%;bottom:-1.5rem;width:10.5rem;height:10.5rem"><img src="https://picsum.photos/seed/ft6/560/520" alt=""></div>
    <div class="ft-item polaroid hidden md:block" data-rot="4" style="left:25%;bottom:-2rem;width:9.5rem;height:10.5rem"><img src="https://picsum.photos/seed/ft7/500/560" alt=""></div>
    <div class="ft-item polaroid hidden md:block" data-rot="-3" style="left:48%;bottom:-.5rem;width:10rem;height:12.5rem"><img src="https://picsum.photos/seed/ft8/500/640" alt=""></div>
    <div class="ft-item polaroid hidden md:block" data-rot="3" style="left:60%;bottom:-1.5rem;width:10.5rem;height:10.5rem"><img src="https://picsum.photos/seed/ft3/520/520" alt=""></div>
    <div class="ft-item polaroid" data-rot="-4" style="left:71%;bottom:-.5rem;width:10rem;height:12.5rem"><img src="https://picsum.photos/seed/ft4/500/640" alt=""></div>
    <div class="ft-item polaroid hidden md:block" data-rot="5" style="left:85%;bottom:-1.5rem;width:9.5rem;height:11rem"><img src="https://picsum.photos/seed/ft5/500/560" alt=""></div>
  </div>

  <div class="relative z-10 bg-[#161616] text-white">
    <div class="max-w-[90rem] mx-auto px-6 md:px-10 py-6 md:py-8 grid md:grid-cols-2 gap-6 md:gap-8">
      <div class="flex flex-col justify-between gap-4">
        <span class="inline-flex items-center gap-1.5 text-lg font-semibold tracking-tight">ERA<i class="w-1.5 h-1.5 rounded-full bg-orange-500"></i></span>
        <p class="text-sm font-semibold leading-snug tracking-tight">Pilih. Kirim. Seleksi. <span class="text-white/45">Berkarya.</span></p>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-5 text-xs">
        <div><p class="text-[9px] tracking-[.18em] text-white/40 mb-2.5">NAVIGASI CEPAT</p><ul class="space-y-1.5"><li><a href="#" class="ft-link">Tentang KarierHub</a></li><li><a href="#" class="ft-link">Pusat Akun Kandidat</a></li><li><a href="#" class="ft-link">Panduan Seleksi &amp; Asesmen</a></li><li><a href="#" class="ft-link">Kebijakan Privasi</a></li></ul></div>
        <div><p class="text-[9px] tracking-[.18em] text-white/40 mb-2.5">PELUANG KARIR</p><ul class="space-y-1.5"><li><a href="#" class="ft-link">Lowongan Kerja Profesional</a></li><li><a href="#" class="ft-link">Program Magang Bersertifikat</a></li><li><a href="#" class="ft-link">Eksplorasi Perusahaan Mitra</a></li><li><a href="#" class="ft-link">Jalur Fresh Graduate</a></li></ul></div>
        <div><p class="text-[9px] tracking-[.18em] text-white/40 mb-2.5">HUBUNGI KAMI</p><ul class="space-y-1.5"><li><a href="#" class="ft-link">karir@karierhub.co.id</a></li><li><a href="#" class="ft-link">+62 (021) 5088-7200</a></li><li><a href="#" class="ft-link">Helpdesk Talenta</a></li><li><a href="#" class="ft-link">Kirim Email</a></li></ul></div>
      </div>
      <div class="md:col-span-2 pt-4 border-t border-white/10 flex flex-wrap items-center justify-between gap-2 text-[11px] text-white/40">
        <span>© 2024 KarierHub Nusantara. Seluruh hak cipta dilindungi.</span>
        <a href="#" class="ft-link !text-white/40 hover:!text-orange-400">Kembali ke atas ↑</a>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
gsap.registerPlugin(ScrollTrigger);

/* selalu mulai dari atas saat halaman di-load / di-refresh (tidak mengingat posisi scroll sebelumnya) */
if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
window.scrollTo(0, 0);
window.addEventListener('beforeunload', () => window.scrollTo(0, 0));
window.addEventListener('load', () => window.scrollTo(0, 0));
const $ = (s, c = document) => [...c.querySelectorAll(s)];

/* 1. HERO: splash ERA -> wordmark pindah ke navbar -> judul menyala kata per kata -> foto naik; scroll: foto tengah membesar */
document.body.classList.add('lock');
const eraWord = document.getElementById('era-word'), eraCap = document.getElementById('era-cap');
const navLogo = document.getElementById('nav-logo');

const heroH = document.getElementById('hero-h');
heroH.innerHTML = heroH.textContent.trim().split(/\s+/).map(w => `<span class="w">${w}</span>`).join(' ');

gsap.set('#card-c', { xPercent: -50 });
gsap.set('.nav-i', { y: -20, opacity: 0 });
gsap.set('.hero-sub', { y: 20, opacity: 0 });
gsap.set('.hero-card', { y: 260, opacity: 0 });

/* scroll: hero di-pin, teks naik & hilang, foto samping keluar, foto tengah membesar jadi kartu penuh */
gsap.timeline({ scrollTrigger: { trigger: '#hero', start: 'top top', end: '+=120%', scrub: .6, pin: true, anticipatePin: 1, invalidateOnRefresh: true } })
  .to('#hero-copy', { y: -90, opacity: 0, ease: 'none', duration: .5 }, 0)
  .to('#card-l', { xPercent: -130, ease: 'none', duration: .8 }, 0)
  .to('#card-r', { xPercent: 130, ease: 'none', duration: .8 }, 0)
  .to('#card-c', { width: () => innerWidth - 32, height: () => innerHeight - 80, bottom: 16, borderRadius: 28, ease: 'none', duration: 1 }, 0)
  .to('#hero-cap', { opacity: 1, ease: 'none', duration: .35 }, .65);

gsap.timeline({ defaults: { ease: 'power3.out' } })
  .to('.era-word .m>span', { y: 0, duration: .8, stagger: .12, delay: .2 })
  .to(eraCap, { opacity: 1, duration: .5 }, '-=.3')
  .to(eraCap, { opacity: 0, duration: .3 }, '+=.5')
  .add(() => {
    const a = eraWord.getBoundingClientRect(), b = navLogo.getBoundingClientRect();
    gsap.to(eraWord, { x: b.left + b.width / 2 - (a.left + a.width / 2), y: b.top + b.height / 2 - (a.top + a.height / 2),
      scale: b.height / a.height * 0.9, duration: 1.1, ease: 'power4.inOut',
      onComplete: () => { navLogo.style.opacity = 1; eraWord.style.display = 'none'; } });
  })
  .to('.nav-i', { y: 0, opacity: 1, stagger: .1, duration: .6 }, '<.9')
  .to('#hero-h .w', { opacity: 1, filter: 'blur(0px)', stagger: .09, duration: .6 }, '-=.6')
  .to('.hero-sub', { y: 0, opacity: 1, stagger: .12, duration: .6 }, '-=.2')
  .to('.hero-card', { y: 0, opacity: 1, duration: 1, stagger: .12 }, '-=.5')
  .add(() => { document.body.classList.remove('lock'); ScrollTrigger.refresh(); });

/* 2. Kata tengah pada judul berganti (semua elemen [data-swap]) */
$('[data-swap]').forEach(el => {
  const ws = el.dataset.swap.split('|'); let k = 0;
  setInterval(() => gsap.to(el, { y: -14, opacity: 0, duration: .3, onComplete: () => {
    el.textContent = ws[++k % ws.length];
    gsap.fromTo(el, { y: 14, opacity: 0 }, { y: 0, opacity: 1, duration: .3 });
  } }), 2200);
});

/* 5. Statement menyala kata per kata */
const st = $('#statement')[0];
st.innerHTML = st.textContent.trim().split(/\s+/).map(w => `<span class="w">${w}</span>`).join(' ');
gsap.to('#statement .w', { opacity: 1, stagger: .08, ease: 'none',
  scrollTrigger: { trigger: st, start: 'top 80%', end: 'bottom 45%', scrub: true } });

/* Parallax: elemen ber-atribut data-speed bergerak beda kecepatan */
$('[data-speed]').forEach(el => gsap.to(el, { y: -parseFloat(el.dataset.speed) * 220, ease: 'none',
  scrollTrigger: { trigger: el.closest('section'), start: 'top bottom', end: 'bottom top', scrub: true } }));

/* Reveal umum */
$('.rv').forEach(el => gsap.from(el, { y: 40, opacity: 0, duration: .9, ease: 'power3.out',
  scrollTrigger: { trigger: el, start: 'top 88%' } }));

/* 6. Pilihan Magang / Kerja: hover / klik / fokus tombol vertikal di kiri -> teks di kanan berganti */
let copyType = null;
function showCopy(type, instant) {
  if (copyType === type) return; copyType = type;
  $('[data-copy]').forEach(el => { const on = el.dataset.copy === type;
    gsap.to(el, { autoAlpha: on ? 1 : 0, y: on ? 0 : 12, duration: instant ? 0 : .45, overwrite: 'auto' }); });
}
const picks = $('.vbtn');
function choose(type, instant) {
  picks.forEach(b => { const on = b.dataset.pick === type; b.classList.toggle('active', on); b.setAttribute('aria-pressed', on);
    gsap.to(b, { scale: on ? 1.04 : .97, duration: instant ? 0 : .5, ease: 'back.out(2)', overwrite: 'auto' }); });
  showCopy(type, instant);
}
picks.forEach(b => {
  const go = () => choose(b.dataset.pick);
  b.addEventListener('pointerenter', go);
  b.addEventListener('focus', go);
  b.addEventListener('click', go);
});
choose('intern', true);

/* 7. Avatar komunitas (otomatis, ganti seed/jumlah sesuka hati) */
const av = $('#avatars')[0];
for (let i = 1; i <= 27; i++) {
  const im = document.createElement('img');
  im.className = 'avatar'; im.alt = ''; im.loading = 'lazy';
  im.src = `https://picsum.photos/seed/av${i}/120/120`;
  im.dataset.speed = (0.1 + (i % 5) * 0.12).toFixed(2);
  av.appendChild(im);
  gsap.to(im, { y: -parseFloat(im.dataset.speed) * 160, ease: 'none',
    scrollTrigger: { trigger: av, start: 'top bottom', end: 'bottom top', scrub: true } });
  if (!matchMedia('(prefers-reduced-motion:reduce)').matches)
    gsap.to(im, { xPercent: gsap.utils.random(-18, 18), yPercent: gsap.utils.random(-25, 25), rotation: gsap.utils.random(-8, 8),
      duration: gsap.utils.random(2.4, 4.2), delay: gsap.utils.random(0, 2), repeat: -1, yoyo: true, ease: 'sine.inOut' });
}

/* 9. Peta alur seleksi: rute tergambar, penanda berjalan, pin bisa diklik, auto-play, simbol & ripple di tiap tahap */
const route = $('#route')[0], prog = $('#route-prog')[0], routeLen = route.getTotalLength();
const stops = $('.map-stop'), pins = $('.map-pin'), me = $('#map-me')[0], mp = { p: 0 };
const VBW = 800, VBH = 720;
const targets = [[310, 110], [490, 280], [310, 450], [490, 610]];
const fr = targets.map(([tx, ty]) => { let best = 0, bd = 1e9;
  for (let L = 0; L <= routeLen; L += 2) { const q = route.getPointAtLength(L), d = (q.x - tx) ** 2 + (q.y - ty) ** 2; if (d < bd) { bd = d; best = L; } }
  return best / routeLen; });
fr[0] = 0; fr[fr.length - 1] = 1;
let step = 0, auto = null;
prog.style.strokeDasharray = routeLen; prog.style.strokeDashoffset = routeLen;
stops.forEach((s, i) => {
  const pt = route.getPointAtLength(fr[i] * routeLen);
  s.style.left = (pt.x / VBW * 100) + '%'; s.style.top = (pt.y / VBH * 100) + '%';
  pins[i].dataset.side = pt.x < VBW / 2 ? 'l' : 'r';   /* label selalu di sisi luar rute */
  pins[i].addEventListener('click', () => { stopAuto(); goStep(i); });
});
gsap.set(pins, { scale: 0 });
function renderMap() {
  const pt = route.getPointAtLength(mp.p * routeLen);
  me.style.left = (pt.x / VBW * 100) + '%'; me.style.top = (pt.y / VBH * 100) + '%';
  prog.style.strokeDashoffset = routeLen * (1 - mp.p);
}
function burst(i) {
  for (let k = 0; k < 12; k++) {
    const dot = document.createElement('i'); dot.className = 'map-dot'; stops[i].appendChild(dot);
    const a = k / 12 * Math.PI * 2, r = 40 + Math.random() * 26;
    gsap.fromTo(dot, { x: 0, y: 0, scale: 1, opacity: 1 }, { x: Math.cos(a) * r, y: Math.sin(a) * r, scale: 0, opacity: 0, duration: .8, ease: 'power2.out', onComplete: () => dot.remove() });
  }
  gsap.fromTo(pins[i], { scale: 1.35 }, { scale: 1, duration: .7, ease: 'elastic.out(1,.4)' });
}
function setStep(i) {
  pins.forEach((p, k) => { p.classList.toggle('reached', k <= i); p.classList.toggle('current', k === i); });
}
function goStep(i) {
  step = (i + pins.length) % pins.length;
  gsap.to(mp, { p: fr[step], duration: .9 + Math.abs(fr[step] - mp.p) * 2, ease: 'power2.inOut', onUpdate: renderMap, overwrite: true, onComplete: () => burst(step) });
  setStep(step);
}
const stopAuto = () => { clearInterval(auto); auto = null; };
const startAuto = () => { stopAuto(); auto = setInterval(() => goStep(step + 1), 4200); };
renderMap(); setStep(0);
ScrollTrigger.create({ trigger: '#alur', start: 'top 65%', once: true,
  onEnter: () => gsap.to(pins, { scale: 1, duration: .7, stagger: .14, ease: 'back.out(2)', onComplete: () => { burst(0); startAuto(); } }) });

/* 12. Footer interaktif: foto masuk dari bawah, hover mengangkat, bisa diseret */
const ftZone = $('#ft-top')[0], ftItems = $('.ft-item'); let zTop = 20;
ftItems.forEach(el => {
  const rot = parseFloat(el.dataset.rot || 0); let sx, sy, ox, oy, drag = false;
  gsap.set(el, { rotation: rot, y: 300, opacity: 0 });
  el.addEventListener('pointerenter', () => { if (drag) return; el.style.zIndex = ++zTop;
    gsap.to(el, { scale: 1.06, rotation: rot * .25, yPercent: -5, duration: .35, ease: 'power3.out', overwrite: 'auto' }); });
  el.addEventListener('pointerleave', () => { if (drag) return;
    gsap.to(el, { scale: 1, rotation: rot, yPercent: 0, duration: .5, ease: 'power3.out', overwrite: 'auto' }); });
  el.addEventListener('pointerdown', e => { drag = true; el.setPointerCapture(e.pointerId); el.classList.add('drag'); el.style.zIndex = ++zTop;
    sx = e.clientX; sy = e.clientY; ox = gsap.getProperty(el, 'x'); oy = gsap.getProperty(el, 'y');
    gsap.to(el, { scale: 1.1, rotation: 0, yPercent: 0, duration: .25, overwrite: 'auto' }); });
  el.addEventListener('pointermove', e => { if (!drag) return;
    const x = Math.min(ftZone.clientWidth - el.offsetLeft - el.offsetWidth * .5, Math.max(-el.offsetLeft - el.offsetWidth * .5, ox + e.clientX - sx));
    const y = Math.min(ftZone.clientHeight - el.offsetTop - el.offsetHeight * .4, Math.max(-el.offsetTop, oy + e.clientY - sy));
    gsap.set(el, { x, y }); });
  const end = () => { if (!drag) return; drag = false; el.classList.remove('drag');
    gsap.to(el, { scale: 1, rotation: rot, duration: .5, ease: 'back.out(2)', overwrite: 'auto' }); };
  el.addEventListener('pointerup', end); el.addEventListener('pointercancel', end);
});
ScrollTrigger.create({ trigger: ftZone, start: 'top 90%', once: true,
  onEnter: () => gsap.to(ftItems, { y: 0, opacity: 1, duration: 1, stagger: .1, ease: 'back.out(1.3)' }) });
</script>
</body>
</html>