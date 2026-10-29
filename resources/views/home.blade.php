<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ERA - Membangun Masa Depan Karier &amp; Talenta</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
      colors: { paper:'#faf9fc', lime:'#e9f56b', brand:'#34d3b0' } } } };
  </script>
  <style>
/* Komponen & animasi pendukung landing.html */
.fan-card{position:absolute;width:7.5rem;height:10rem;object-fit:cover;border-radius:1rem;box-shadow:0 18px 40px -12px rgba(0,0,0,.35);border:2px solid #fff}
.stack-card{position:absolute;width:8.5rem;height:11rem;object-fit:cover;border-radius:1rem;border:2px solid #fff;box-shadow:0 14px 30px -10px rgba(0,0,0,.3);transform:translate(calc(var(--i)*18px),calc(var(--i)*-8px));transition:transform .5s ease}
.stack-card:hover{transform:translate(calc(var(--i)*18px),calc(var(--i)*-8px - 12px))}
.float-card{position:absolute;width:8.5rem;height:11rem;object-fit:cover;border-radius:1rem;box-shadow:0 16px 36px -14px rgba(0,0,0,.3)}
.bubble{position:absolute;padding:.2rem .65rem;border-radius:.75rem;font-size:.7rem;font-weight:600;z-index:5}
.ico{width:3rem;height:3rem;border-radius:9999px;background:#fff;box-shadow:0 6px 16px -6px rgba(0,0,0,.25);display:flex;align-items:center;justify-content:center;transition:transform .3s}
.ico:hover{transform:translateY(-6px) rotate(8deg)}
.tab-btn.active{background:#111;color:#fff}
.m-tab{border-bottom:2px solid transparent;color:rgba(0,0,0,.45);transition:.3s}
.m-tab.active{border-color:#111;color:#111}
.creator img{transition:transform .4s ease}.creator:hover img{transform:translateY(-8px)}
.avatar{width:100%;max-width:3.6rem;aspect-ratio:1;object-fit:cover;border-radius:1rem;margin-inline:auto;box-shadow:0 8px 20px -8px rgba(0,0,0,.3)}
.marquee{animation:mq 28s linear infinite}.marquee:hover{animation-play-state:paused}
@keyframes mq{to{transform:translateX(-50%)}}
.chip{display:inline-block;width:3rem;height:3rem;border-radius:.75rem;background:#f43f5e}
.orb{position:absolute;width:2.6rem;height:2.6rem;border-radius:9999px;background:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 6px rgba(255,255,255,.5)}
#statement .w{opacity:.15}
@media (prefers-reduced-motion:reduce){.marquee{animation:none}}
</style>
</head>
<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

<!-- NAVBAR -->
<header class="fixed top-0 inset-x-0 z-50 backdrop-blur-md bg-paper/70">
  <nav class="max-w-7xl mx-auto px-6 h-16 grid grid-cols-[1fr_auto_1fr] items-center">
    <a href="#" class="text-sm font-medium">LOGO ERA</a>
    <ul class="hidden md:flex items-center gap-10 text-sm">
      <li><a href="#" class="hover:opacity-60">Home</a></li>
      <li><a href="#" class="hover:opacity-60">About</a></li>
      <li><a href="#" class="hover:opacity-60">Internship</a></li>
      <li><a href="#" class="hover:opacity-60">Job</a></li>
    </ul>
    <div class="col-start-3 justify-self-end">
      <a href="#" class="inline-block px-5 py-1.5 rounded-full border border-black text-sm hover:bg-black hover:text-white transition">Get Started</a>
    </div>
  </nav>
</header>

<main>
<!-- 1. HERO -->
<section class="min-h-screen pt-32 px-6 flex flex-col items-center text-center">
  <h1 class="hero-title text-4xl md:text-6xl font-semibold tracking-tight leading-[1.08] max-w-3xl">Membangun Masa Depan Karier &amp; Talenta Bersama Kami.</h1>
  <div id="fan" class="relative mt-14 h-72 w-full max-w-3xl flex items-center justify-center cursor-pointer">
    <img class="fan-card" src="https://picsum.photos/seed/pr1/300/400" alt="">
    <img class="fan-card" src="https://picsum.photos/seed/pr2/300/400" alt="">
    <img class="fan-card" src="https://picsum.photos/seed/pr3/300/400" alt="">
    <img class="fan-card" src="https://picsum.photos/seed/pr4/300/400" alt="">
    <img class="fan-card" src="https://picsum.photos/seed/pr5/300/400" alt="">
    <img class="fan-card" src="https://picsum.photos/seed/pr6/300/400" alt="">
    <img class="fan-card" src="https://picsum.photos/seed/pr7/300/400" alt="">
  </div>
  <p class="hero-sub mt-8 text-xs md:text-sm text-black/60 max-w-xl">Akses ribuan peluang karir profesional bergengsi serta program magang bersertifikat nasional. Dirancang untuk membentuk pemimpin inovatif dan talenta tangguh masa depan.</p>
  <a href="#showcase" class="hero-sub mt-5 px-6 py-2.5 rounded-full bg-black text-white text-xs font-medium hover:scale-105 transition">Eksplorasi Semua Program ↗</a>
</section>

<!-- 2. SHOWCASE / SELL / ACQUIRE -->
<section id="showcase" class="px-6 md:px-16 py-28 grid md:grid-cols-2 gap-10 items-center max-w-7xl mx-auto">
  <div>
    <p class="text-[10px] tracking-[.2em] font-semibold mb-3">PROGRAM MAGANG MAHASISWA</p>
    <h2 class="text-5xl md:text-7xl font-semibold tracking-tight leading-[1.05]">
      Magang,<br><span id="swap" class="text-black/25 inline-block">Konversi.</span><br>
      <span class="text-rose-800">&amp;</span> <span class="text-rose-300">Non-Konversi</span>
    </h2>
    <a href="#" class="inline-block mt-8 px-6 py-2.5 rounded-full bg-black text-white text-xs font-medium hover:scale-105 transition">Jelajahi Posisi Sekarang →</a>
  </div>
  <div class="relative h-80 flex items-center justify-center" data-speed="0.15">
    <img class="stack-card" style="--i:0" src="https://picsum.photos/seed/st1/300/400" alt="">
    <img class="stack-card" style="--i:1" src="https://picsum.photos/seed/st2/300/400" alt="">
    <img class="stack-card" style="--i:2" src="https://picsum.photos/seed/st3/300/400" alt="">
    <img class="stack-card" style="--i:3" src="https://picsum.photos/seed/st4/300/400" alt="">
    <div class="absolute -bottom-2 -left-2 md:left-4 bg-amber-100 text-amber-900 rounded-xl px-4 py-3 text-xs font-semibold leading-tight z-10 shadow">BATCH 2025/1<br><span class="text-sm">Pendaftaran Terbuka</span></div>
  </div>
</section>

<!-- 3. GATEWAY -->
<section class="px-6 md:px-16 py-24 max-w-7xl mx-auto">
  <p class="text-[10px] tracking-[.2em] font-semibold mb-2">TERAKREDITASI</p>
  <h2 class="text-5xl md:text-7xl font-semibold tracking-tight mb-10">Standar Rekrutmen Terbuka &amp; Bebas Biaya</h2>
  <div id="gw" class="relative overflow-hidden h-[26rem] md:h-[36rem] bg-orange-500">
    <img src="https://picsum.photos/seed/gateway/1600/900" alt="" class="w-full h-full object-cover">
    <div class="absolute left-6 bottom-6 px-4 py-2 rounded-2xl bg-white/90 backdrop-blur text-xs leading-tight"><span class="text-[10px] tracking-widest text-black/50">HELPDESK TALENTA</span><br><b class="text-sm">021-5088-7200</b></div>
    <div class="absolute right-6 bottom-6 flex gap-2">
      <button class="w-9 h-9 rounded-full bg-white/90 hover:bg-white">‹</button>
      <button class="w-9 h-9 rounded-full bg-white/90 hover:bg-white">›</button>
    </div>
  </div>
</section>

<!-- 4. TRUSTED -->
<section class="px-6 md:px-16 py-20 max-w-7xl mx-auto">
  <h3 class="text-3xl font-semibold text-black/30 mb-10">Dipercaya</h3>
  <div class="logos flex flex-wrap items-center justify-between gap-6 text-black/40 font-semibold text-lg">
    <span>Digital Nusantara</span><span>Transformasi IT</span><span>Intelijen Pasar</span><span>Bursa Talenta</span><span>KarierHub</span><span>ISO Terverifikasi</span>
  </div>
</section>

<!-- 5. STATEMENT + FLOATING CARDS -->
<section class="relative min-h-screen px-6 py-32 flex items-center justify-center overflow-hidden">
  <h2 id="statement" class="relative z-10 text-3xl md:text-5xl font-medium tracking-tight text-center max-w-4xl leading-tight">Akses ribuan peluang karir profesional bergengsi serta program magang bersertifikat nasional, dirancang untuk membentuk pemimpin inovatif dan talenta tangguh masa depan.</h2>
  <div class="absolute inset-0 hidden md:block pointer-events-none">
    <img class="float-card" data-speed="0.4"  style="left:6%;top:8%"  src="https://picsum.photos/seed/fl1/300/400" alt="">
    <img class="float-card" data-speed="0.8"  style="left:22%;top:58%" src="https://picsum.photos/seed/fl2/300/400" alt="">
    <img class="float-card" data-speed="0.3"  style="left:42%;top:4%"  src="https://picsum.photos/seed/fl3/300/400" alt="">
    <img class="float-card" data-speed="0.7"  style="left:62%;top:62%" src="https://picsum.photos/seed/fl4/300/400" alt="">
    <img class="float-card" data-speed="0.5"  style="left:78%;top:10%" src="https://picsum.photos/seed/fl5/300/400" alt="">
    <span class="bubble bg-black text-white" style="left:30%;top:22%">98% Konversi</span>
    <span class="bubble bg-blue-600 text-white" style="left:70%;top:34%">150+ Posisi Aktif</span>
  </div>
</section>

<!-- 6. POSISI & REKRUTMEN TERPOPULER -->
<section class="px-6 md:px-16 py-24 max-w-7xl mx-auto grid md:grid-cols-2 gap-16 items-center">
  <div class="rv">
    <h2 class="text-4xl md:text-5xl font-semibold tracking-tight leading-tight">Posisi dan Rekrutmen<br>Terpopuler</h2>
    <a href="#" class="inline-block mt-6 px-4 py-1.5 rounded-full bg-black/5 text-xs font-medium">Lihat Semua 18 Lowongan Aktif →</a>
    <div class="mt-14 flex flex-wrap gap-4 max-w-xs text-xl">
      <span class="ico">✎</span><span class="ico">◎</span><span class="ico">⊗</span><span class="ico">♜</span><span class="ico">▤</span><span class="ico">✦</span>
    </div>
  </div>
  <div class="rv">
    <div class="flex items-center justify-between bg-white rounded-t-2xl px-4 py-3">
      <div class="flex gap-1 flex-wrap">
        <button class="tab-btn active px-3 py-1 rounded-full text-xs font-medium" data-tab="all">Semua (18)</button>
        <button class="tab-btn px-3 py-1 rounded-full text-xs font-medium" data-tab="intern">Program Magang (10)</button>
        <button class="tab-btn px-3 py-1 rounded-full text-xs font-medium" data-tab="full">Full-Time (8)</button>
      </div>
    </div>
    <div id="jobs" class="grid grid-cols-1 gap-3 bg-zinc-900 p-3 rounded-b-2xl">
      <article class="job bg-white rounded-xl p-4" data-type="intern">
        <div class="flex items-center justify-between text-[10px]"><span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold">PROGRAM MAGANG</span><span class="text-black/50">Ditutup 5 Hari Lagi</span></div>
        <h4 class="mt-2 font-semibold text-sm">UI/UX Product Designer Intern</h4><p class="text-xs text-black/50">Divisi Produk Digital Nusantara</p>
        <ul class="mt-2 text-xs space-y-0.5"><li>◎ Jakarta Selatan (Hybrid)</li><li>▤ Uang Saku Rp 4.200.000 / bln</li><li>✦ Konversi 20 SKS Resmi</li></ul>
        <div class="mt-3 flex items-center justify-between"><span class="text-[11px] text-black/50">Semester 5 – 8</span><button class="px-3 py-1 rounded-full bg-black text-white text-[11px]">Lamar Cepat</button></div>
      </article>
      <article class="job bg-white rounded-xl p-4" data-type="full">
        <div class="flex items-center justify-between text-[10px]"><span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold">LOWONGAN KERJA</span><span class="text-black/50">Tayang Kemarin</span></div>
        <h4 class="mt-2 font-semibold text-sm">Senior Cloud &amp; Backend Engineer</h4><p class="text-xs text-black/50">Direktorat Transformasi IT</p>
        <ul class="mt-2 text-xs space-y-0.5"><li>◎ Sudirman, Jakarta (WFO)</li><li>▤ Rp 18.000.000 – 24.000.000</li><li>✦ Mitra Terverifikasi ISO</li></ul>
        <div class="mt-3 flex items-center justify-between"><span class="text-[11px] text-black/50">Pengalaman 4+ Thn</span><button class="px-3 py-1 rounded-full bg-black text-white text-[11px]">Lamar Cepat</button></div>
      </article>
      <article class="job bg-white rounded-xl p-4" data-type="intern">
        <div class="flex items-center justify-between text-[10px]"><span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold">PROGRAM MAGANG</span><span class="text-black/50">Ditutup 7 Hari Lagi</span></div>
        <h4 class="mt-2 font-semibold text-sm">Business &amp; Data Analyst Intern</h4><p class="text-xs text-black/50">Unit Intelijen Pasar</p>
        <ul class="mt-2 text-xs space-y-0.5"><li>◎ Remote / Jarak Jauh</li><li>▤ Uang Saku Rp 4.000.000 / bln</li><li>✦ Laptop Fasilitas Kantor</li></ul>
        <div class="mt-3 flex items-center justify-between"><span class="text-[11px] text-black/50">Terbuka Fresh Grad</span><button class="px-3 py-1 rounded-full bg-black text-white text-[11px]">Lamar Cepat</button></div>
      </article>
    </div>
  </div>
</section>

<!-- 7. COMMUNITY (avatar dibuat otomatis oleh landing.js) -->
<section class="relative min-h-screen py-24 overflow-hidden flex items-center justify-center">
  <div id="avatars" class="absolute inset-0 grid grid-cols-6 md:grid-cols-9 gap-4 p-4 content-between"></div>
  <div class="relative z-10 text-center bg-paper/85 backdrop-blur px-8 py-8 rounded-3xl rv">
    <h2 class="text-3xl md:text-5xl font-semibold tracking-tight">Siap Melangkah Menuju<br>Babak Baru Perjalanan<br>Karier Anda?</h2>
    <p class="mt-3 text-xs text-black/60 max-w-sm mx-auto">Bergabunglah dengan ribuan talenta yang telah menemukan ruang bertumbuh dan memberikan kontribusi nyata bagi ekosistem industri Indonesia.</p>
    <div class="mt-5 flex justify-center gap-3"><a href="#" class="px-5 py-2 rounded-full bg-orange-500 text-white text-xs font-medium">Daftar Sekarang</a><a href="#" class="px-5 py-2 rounded-full bg-black text-white text-xs font-medium">Jelajahi Posisi</a></div>
  </div>
</section>

<!-- 8. STORY -->
<section class="px-6 md:px-16 py-24 max-w-7xl mx-auto text-center">
  <p class="text-[10px] tracking-[.2em] font-semibold">PENDAFTARAN <span class="text-indigo-600">TERBUKA</span></p>
  <h2 class="mt-3 text-4xl md:text-6xl font-semibold tracking-tight rv">Program Magang<br>&amp; Lowongan Kerja</h2>
  <div class="mt-14 grid md:grid-cols-2 gap-6 text-left">
    <article class="rv rounded-3xl bg-white p-8 min-h-[28rem] relative overflow-hidden">
      <span class="text-xs">▶ Play Video</span>
      <img class="absolute left-1/2 top-16 -translate-x-1/2 w-40 h-52 object-cover rounded-xl -rotate-6 shadow-xl" src="https://picsum.photos/seed/story1/300/400" alt="">
      <span class="bubble bg-blue-600 text-white" style="right:2.5rem;top:3.5rem">BATCH 2025/1</span>
      <div class="absolute bottom-8 left-8 right-8">
        <h3 class="font-semibold">Program Magang Mahasiswa</h3>
        <p class="text-xs text-black/60 mt-1">Konversi &amp; Non-Konversi. Uang saku hingga Rp 4.200.000 / bln dengan konversi SKS resmi.</p>
        <button class="mt-3 px-3 py-1 rounded-full border border-black/10 text-xs">Jelajahi Posisi Sekarang</button>
      </div>
    </article>
    <article class="rv rounded-3xl overflow-hidden bg-white">
      <div class="h-72 flex items-center justify-center bg-[#2540ff]"><div class="w-32 h-32 rounded-full bg-white/90 flex items-center justify-center"><div class="w-12 h-12 rounded-full bg-blue-700"></div></div></div>
      <div class="p-8">
        <h3 class="font-semibold">Lowongan Kerja</h3>
        <p class="text-xs text-black/60 mt-1">Posisi full-time untuk fresh graduate hingga profesional berpengalaman 4+ tahun.</p>
        <button class="mt-3 px-3 py-1 rounded-full border border-black/10 text-xs">Lihat Lowongan</button>
      </div>
    </article>
  </div>
</section>

<!-- 9. ALUR SELEKSI -->
<section class="px-6 md:px-16 py-24 max-w-7xl mx-auto">
  <div class="flex items-start justify-between gap-6 rv">
    <div>
      <p class="text-[10px] tracking-[.2em] font-semibold mb-3">PROSES <span class="text-fuchsia-500">REKRUTMEN</span></p>
      <h2 class="text-4xl md:text-6xl font-semibold tracking-tight leading-[1.05]">Alur Seleksi yang<br><span class="text-black/25">Terstruktur &amp; Cepat</span></h2>
      <p class="mt-4 text-sm text-black/60 max-w-xl">Kami menghargai waktu dan komitmen Anda. Nikmati proses rekrutmen yang terstandarisasi, transparan, dan komunikatif dari awal hingga akhir.</p>
    </div>
    <button class="hidden md:block px-4 py-1.5 rounded-full bg-fuchsia-500 text-white text-xs font-medium">Daftar</button>
  </div>
  <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-4 border-b border-black/10">
    <button class="m-tab active text-left pb-3 text-sm font-medium" data-n="01" data-title="Pilih Posisi Impian" data-text="Eksplorasi daftar program magang atau lowongan kerja profesional yang relevan dengan minat, portofolio, dan keahlian Anda." data-img="step1">01 · Pilih Posisi Impian</button>
    <button class="m-tab text-left pb-3 text-sm font-medium" data-n="02" data-title="Kirim Berkas &amp; CV" data-text="Unggah resume digital, portofolio terbaru, dan surat pengantar melalui portal daring terintegrasi dalam beberapa menit." data-img="step2">02 · Kirim Berkas &amp; CV</button>
    <button class="m-tab text-left pb-3 text-sm font-medium" data-n="03" data-title="Seleksi &amp; Wawancara" data-text="Ikuti tes kompetensi teknis, studi kasus interaktif, serta sesi dialog dua arah dengan User Lead dan Tim People." data-img="step3">03 · Seleksi &amp; Wawancara</button>
    <button class="m-tab text-left pb-3 text-sm font-medium" data-n="04" data-title="Mulai Berkarya Bersama" data-text="Terima penawaran resmi, onboarding komprehensif, dan mulailah perjalanan bertumbuh bersama tim berdedikasi tinggi." data-img="step4">04 · Mulai Berkarya Bersama</button>
    </div>
  <div class="mt-8 grid md:grid-cols-2 gap-5 items-stretch rv">
    <img id="step-img" class="w-full h-72 md:h-full object-cover rounded-2xl" src="https://picsum.photos/seed/step1/700/500" alt="">
    <div class="rounded-2xl bg-white p-8 flex flex-col justify-end min-h-[18rem] border-t-4 border-orange-500">
      <span id="step-n" class="w-10 h-10 rounded-lg bg-orange-500 text-white font-semibold flex items-center justify-center mb-4">01</span>
      <h3 id="step-title" class="text-2xl font-semibold">Pilih Posisi Impian</h3>
      <p id="step-text" class="mt-2 text-sm text-black/60">Eksplorasi daftar program magang atau lowongan kerja profesional yang relevan dengan minat, portofolio, dan keahlian Anda.</p>
    </div>
  </div>
</section>

<!-- 10. MEMBERSHIP + MARQUEE -->
<section class="px-6 md:px-16 py-24 max-w-7xl mx-auto">
  <div class="flex flex-wrap items-end justify-between gap-6 rv">
    <div class="max-w-sm"><h2 class="text-4xl font-semibold tracking-tight">Sertifikasi Talenta Nasional</h2><p class="mt-2 text-xs text-black/60">Portal karir korporat resmi dan inkubasi talenta magang nasional.</p></div>
    <div class="flex gap-4">
      <div class="rounded-2xl px-5 py-4 w-40 bg-white"><p class="text-3xl font-semibold">98%</p><p class="text-[11px] opacity-70 leading-tight mt-1">Konversi Magang ke Karyawan</p></div>
      <div class="rounded-2xl px-5 py-4 w-40 bg-orange-500 text-white"><p class="text-3xl font-semibold">150+</p><p class="text-[11px] opacity-90 leading-tight mt-1">Posisi Aktif, Konsultan Bisnis dan Lainnya</p></div>
      <div class="rounded-2xl px-5 py-4 w-40 bg-white"><p class="text-3xl font-semibold">2024</p><p class="text-[11px] opacity-70 leading-tight mt-1">Top Employer, Sertifikasi Talenta Nasional</p></div>
    </div>
  </div>
  <div class="relative mt-10 rounded-3xl bg-lime py-24 overflow-hidden">
    <div class="marquee flex items-center gap-10 whitespace-nowrap text-5xl md:text-7xl font-semibold tracking-tight w-max">
      <span>Pemimpin inovatif</span><i class="chip"></i><span>Talenta tangguh</span><i class="chip"></i><span>Magang bersertifikat</span><i class="chip"></i>
      <span>Pemimpin inovatif</span><i class="chip"></i><span>Talenta tangguh</span><i class="chip"></i><span>Magang bersertifikat</span><i class="chip"></i>
    </div>
    <span class="orb" data-speed="0.6" style="left:5%;top:15%">⬡</span><span class="orb" data-speed="0.3" style="left:22%;top:65%">◆</span>
    <span class="orb" data-speed="0.8" style="left:45%;top:8%">✦</span><span class="orb" data-speed="0.4" style="left:70%;top:70%">⚙</span>
    <span class="orb" data-speed="0.7" style="left:90%;top:20%">✎</span>
  </div>
</section>

<!-- 11. FOOTER CARDS -->
<section class="px-6 md:px-16 pb-24 max-w-7xl mx-auto grid md:grid-cols-2 gap-6">
  <article class="rv relative rounded-3xl overflow-hidden min-h-[28rem] p-8 flex flex-col justify-end text-white bg-rose-700">
    <img class="absolute inset-0 w-full h-full object-cover" src="https://picsum.photos/seed/meet/800/900" alt="">
    <div class="absolute inset-0 bg-gradient-to-t from-rose-800/90 to-transparent"></div>
    <div class="relative"><h3 class="text-3xl font-semibold leading-tight">Peluang<br>Karir</h3><p class="text-xs mt-2 max-w-xs text-white/85 leading-relaxed">Lowongan Kerja Profesional<br>Program Magang Bersertifikat<br>Eksplorasi Perusahaan Mitra<br>Jalur Fresh Graduate</p><button class="mt-4 px-4 py-1.5 rounded-full bg-white text-black text-xs font-medium">Lihat Lowongan</button></div>
  </article>
  <article class="rv relative rounded-3xl overflow-hidden min-h-[28rem] p-8 flex flex-col justify-end bg-white">
    <img class="absolute inset-0 w-full h-full object-cover opacity-90" src="https://picsum.photos/seed/archive/800/900" alt="">
    <div class="absolute inset-0 bg-gradient-to-t from-white to-transparent"></div>
    <div class="relative"><h3 class="text-3xl font-semibold leading-tight">Hubungi<br>Kami</h3><p class="text-xs mt-2 max-w-xs text-black/70 leading-relaxed">Divisi Akuisisi Talenta &amp; Kemitraan<br>Gedung Bursa Talenta Nusantara Lt. 18<br>Jl. Jenderal Sudirman Kav. 52–53, Jakarta Selatan<br>karir@karierhub.co.id · +62 (021) 5088-7200</p><button class="mt-4 px-4 py-1.5 rounded-full bg-black text-white text-xs font-medium">Kirim Email</button></div>
  </article>
</section>
</main>

<footer class="px-6 md:px-16 py-12 max-w-7xl mx-auto grid md:grid-cols-3 gap-8 text-xs text-black/60">
  <div><p class="text-sm font-medium text-black mb-2">ERA</p><p>Portal karir korporat resmi dan inkubasi talenta magang nasional. Menghubungkan potensi terbaik Indonesia dengan peluang industri terpercaya.</p></div>
  <div><p class="text-sm font-medium text-black mb-2">Navigasi Cepat</p><p class="leading-relaxed">Tentang KarierHub<br>Pusat Akun Kandidat<br>Panduan Seleksi &amp; Asesmen<br>Kebijakan Privasi</p></div>
  <div class="md:text-right self-end">© 2024 KarierHub Nusantara. Seluruh hak cipta dilindungi.</div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
gsap.registerPlugin(ScrollTrigger);
const $ = (s, c = document) => [...c.querySelectorAll(s)];

/* 1. HERO: judul muncul, kartu menumpuk lalu menyebar; hover = menyebar lebih lebar */
const cards = $('#fan .fan-card'), mid = (cards.length - 1) / 2;
const spread = (k) => cards.forEach((c, i) =>
  gsap.to(c, { x: (i - mid) * 62 * k, rotation: (i - mid) * 4 * k, y: Math.abs(i - mid) * 6 * k, duration: .7, ease: 'power3.out' }));
gsap.set(cards, { x: 0, rotation: 0, y: 60, opacity: 0 });
gsap.from('.hero-title', { y: 40, opacity: 0, duration: 1, ease: 'power3.out' });
gsap.to(cards, { opacity: 1, duration: .6, stagger: .06, delay: .3, onComplete: () => spread(1) });
gsap.from('.hero-sub', { y: 20, opacity: 0, delay: 1.1, stagger: .15 });
const fan = $('#fan')[0];
fan.addEventListener('mouseenter', () => spread(1.4));
fan.addEventListener('mouseleave', () => spread(1));

/* 2. Kata "Sell." berganti */
const words = ['Konversi.', 'Sertifikasi.', 'Karier.'], swap = $('#swap')[0];
let wi = 0;
setInterval(() => gsap.to(swap, { y: -14, opacity: 0, duration: .3, onComplete: () => {
  swap.textContent = words[++wi % words.length];
  gsap.fromTo(swap, { y: 14, opacity: 0 }, { y: 0, opacity: 1, duration: .3 });
} }), 2200);

/* 3. Gateway: gambar membesar saat di-scroll */
gsap.fromTo('#gw', { scale: .82, borderRadius: 56 }, { scale: 1, borderRadius: 24, ease: 'none',
  scrollTrigger: { trigger: '#gw', start: 'top 95%', end: 'top 25%', scrub: true } });

/* 4. Logo Trusted muncul berurutan */
gsap.from('.logos span', { y: 20, opacity: 0, stagger: .1, scrollTrigger: { trigger: '.logos', start: 'top 85%' } });

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

/* 6. Filter lowongan: Semua / Program Magang / Full-Time */
$('.tab-btn').forEach(b => b.addEventListener('click', () => {
  $('.tab-btn').forEach(x => x.classList.remove('active')); b.classList.add('active');
  $('.job').forEach(c => c.classList.toggle('hidden', b.dataset.tab !== 'all' && c.dataset.type !== b.dataset.tab));
  gsap.from('.job:not(.hidden)', { y: 20, opacity: 0, stagger: .08, duration: .4 });
}));

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
}

/* 9. Tab alur seleksi: ganti gambar & teks langkah */
$('.m-tab').forEach(t => t.addEventListener('click', () => {
  $('.m-tab').forEach(x => x.classList.remove('active')); t.classList.add('active');
  $('#step-n')[0].textContent = t.dataset.n;
  $('#step-title')[0].textContent = t.dataset.title.replace('&amp;', '&');
  $('#step-text')[0].textContent = t.dataset.text;
  $('#step-img')[0].src = `https://picsum.photos/seed/${t.dataset.img}/700/500`;
  gsap.from('#step-img, #step-n, #step-title, #step-text', { y: 16, opacity: 0, stagger: .06, duration: .4 });
}));
</script>
</body>
</html>