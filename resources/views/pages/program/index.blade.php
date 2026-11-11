{{--
  Halaman Program Magang / Internship (route: program.index)
  Letakkan sebagai view index program (resources/views/pages/program/index.blade.php, sesuai yang dipakai ProgramController).

  - Navbar & footer diambil dari partials (partials.navbar, partials.footer).
  - Tidak ada hero lagi: halaman langsung mulai dari bagian putih.
  - Data program di bawah masih contoh statis. Ganti $daftarProgram dengan data dari database
    (field yang dipakai: id, judul, huruf, kategori, posisi, urgent, status, deskripsi).
    Tombol "Lamar" menuju route('program.show', id), jadi id harus cocok dengan data di database.
--}}
@php
  use Illuminate\Support\Str;

  $daftarProgram = [
    ['id' => 1, 'judul' => 'Data Analyst',            'huruf' => 'D', 'kategori' => 'Teknologi Informasi', 'posisi' => 2, 'urgent' => true,  'status' => 'Ditutup 3 hari lagi',  'deskripsi' => 'Menganalisa tren pasar derivatif dan data transaksi harian menggunakan Python dan alat visualisasi data.'],
    ['id' => 2, 'judul' => 'Administrasi Bisnis',     'huruf' => 'A', 'kategori' => 'Bisnis & Sosial',     'posisi' => 5, 'urgent' => false, 'status' => 'Tayang 2 hari lalu', 'deskripsi' => 'Praktik tata kelola dokumen operasional pialang, input data nasabah ke CRM, dan dukung layanan kantor.'],
    ['id' => 3, 'judul' => 'Full-Stack Developer',    'huruf' => 'F', 'kategori' => 'Teknologi Informasi', 'posisi' => 1, 'urgent' => false, 'status' => 'Tayang 4 hari lalu', 'deskripsi' => 'Membantu tim IT internal merancang sistem registrasi nasabah online berbasis Laravel dan React.'],
    ['id' => 4, 'judul' => 'Akuntansi',               'huruf' => 'A', 'kategori' => 'Bisnis & Sosial',     'posisi' => 4, 'urgent' => false, 'status' => 'Tayang 5 hari lalu', 'deskripsi' => 'Membantu penyusunan laporan harian kas, input mutasi segregated account, dan rekonsiliasi keuangan cabang.'],
    ['id' => 5, 'judul' => 'Manajemen',               'huruf' => 'M', 'kategori' => 'Bisnis & Sosial',     'posisi' => 6, 'urgent' => false, 'status' => 'Tayang 2 hari lalu', 'deskripsi' => 'Mempelajari alur manajerial divisi pemasaran derivatif, riset kompetitor, dan pengembangan strategi akuisisi klien.'],
    ['id' => 6, 'judul' => 'Hubungan Internasional',  'huruf' => 'H', 'kategori' => 'Bisnis & Sosial',     'posisi' => 2, 'urgent' => false, 'status' => 'Tayang hari ini',    'deskripsi' => 'Mempelajari dampak isu makroekonomi global terhadap pergerakan komoditas dan valuta asing (Forex).'],
  ];

  $totalPosisi    = array_sum(array_column($daftarProgram, 'posisi'));
  $maxPosisi      = max(array_column($daftarProgram, 'posisi'));
  $kategoriHitung = collect($daftarProgram)->groupBy('kategori')->map->count();

  $panah = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M7 17 17 7M8 7h9v9"/></svg>';
  $cek   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m5 12 5 5 9-10"/></svg>';
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Program Magang - PT Equityworld Futures</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
        fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'], head: ['Outfit', '"Plus Jakarta Sans"', 'sans-serif'] },
        colors: { ink: '#141414', sand: '#f6f5f3', ewforange: '#f97316' }
      } }
    };
  </script>
  {{-- Animasi aktif hanya kalau pengguna tidak meminta "reduced motion" --}}
  <script>if (!matchMedia('(prefers-reduced-motion: reduce)').matches) document.documentElement.classList.add('js');</script>
  <style>
    :root { --spring: cubic-bezier(.34, 1.56, .64, 1); }
    html.js .rv { opacity: 0; }
    html.js .prog { opacity: 0; }

    /* tombol: membal, tertekan, ada bayangan (tanpa bergerak mengikuti kursor) */
    .press { --sh: 234, 88, 12; transition: background .2s, color .2s, border-color .2s, box-shadow .25s, transform .45s var(--spring); -webkit-tap-highlight-color: transparent; }
    .press.dk { --sh: 20, 20, 20; }
    .press:hover { transform: scale(.96); box-shadow: 0 10px 22px -8px rgba(var(--sh), .55); }
    .press:active { transform: scale(.91); box-shadow: 0 2px 6px -2px rgba(var(--sh), .5), inset 0 2px 6px rgba(0,0,0,.18); transition-duration: .1s; }
    .press:focus-visible { outline: 2px solid #141414; outline-offset: 3px; }

    /* toolbar: indikator geser */
    .seg-btn { position: relative; z-index: 1; flex: none; padding: .55rem 1.1rem; border-radius: 99px; font-size: .8rem; font-weight: 600; color: #52525b; white-space: nowrap; transition: color .3s; cursor: pointer; }
    .seg-btn:hover { color: #141414; }
    .seg-btn.on { color: #fff; }
    #segInd { transition: transform .55s var(--spring), width .55s var(--spring); }

    /* kartu program: lingkaran oranye membesar dari tombol panah saat di-hover */
    .prog { position: relative; overflow: hidden; isolation: isolate; cursor: pointer; transition: color .45s, box-shadow .4s; }
    .prog::after { content: ""; position: absolute; right: 1.5rem; bottom: 1.5rem; width: 2.75rem; height: 2.75rem; border-radius: 50%; background: #f97316; transform: scale(0); transition: transform .75s cubic-bezier(.2,.8,.2,1); z-index: -1; }
    .prog:hover::after, .prog:focus-visible::after { transform: scale(28); }
    .prog:hover, .prog:focus-visible { box-shadow: 0 24px 50px -24px rgba(249,115,22,.65); outline: none; }
    .prog.feat:hover, .prog.feat:focus-visible { color: #141414; }
    .prog .p-muted { transition: color .45s; }
    .prog:hover .p-muted, .prog:focus-visible .p-muted { color: rgba(20,20,20,.72); }
    .prog .p-chip { transition: background .45s, border-color .45s, color .45s; }
    .prog:hover .p-chip, .prog:focus-visible .p-chip { background: rgba(255,255,255,.55); border-color: transparent; color: #141414; }
    .prog .p-arrow { transition: background .45s, color .45s, transform .55s var(--spring); }
    .prog:hover .p-arrow, .prog:focus-visible .p-arrow { background: #141414; color: #fff; transform: rotate(45deg); }
    .ring { stroke-dasharray: 113.1; transition: stroke .4s; }
    .js .ring { stroke-dashoffset: 113.1; }
    .ring-track { stroke: rgba(20,20,20,.1); transition: stroke .4s; }
    .prog.feat .ring-track { stroke: rgba(255,255,255,.18); }
    .prog:hover .ring { stroke: #141414; }
    .prog:hover .ring-track { stroke: rgba(20,20,20,.15); }
    @keyframes stroll-a { 0%, 100% { transform: translate(0,0) scale(1); } 40% { transform: translate(120px, 60px) scale(1.25); } 70% { transform: translate(-60px, 90px) scale(.8); } }
    @keyframes stroll-b { 0%, 100% { transform: translate(0,0) scale(1); } 40% { transform: translate(-110px, -70px) scale(1.3); } 70% { transform: translate(70px, -50px) scale(.75); } }

    /* modal */
    #modal { pointer-events: none; }
    #modal.open { pointer-events: auto; }
    #modalBg { opacity: 0; transition: opacity .35s; }
    #modal.open #modalBg { opacity: 1; }
    #modalPanel { opacity: 0; transform: translateY(24px) scale(.94); transition: opacity .3s, transform .55s var(--spring); }
    #modal.open #modalPanel { opacity: 1; transform: none; }

    /* alur: garis terisi */
    #tlFill { transform-origin: 50% 0; }
    .js #tlFill { transform: scaleY(0); }
    .tl-step .node { transition: background .4s, border-color .4s, color .4s, transform .5s var(--spring); }
    .tl-step.on .node, html:not(.js) .tl-step .node { background: #f97316; border-color: #f97316; color: #141414; transform: scale(1.1); }
    .tl-step .big { -webkit-text-stroke: 1.5px rgba(20,20,20,.18); color: transparent; transition: -webkit-text-stroke-color .4s, color .4s; }
    .tl-step.on .big, html:not(.js) .tl-step .big { -webkit-text-stroke-color: #f97316; }

    /* tab fitur */
    .tab-item { display: block; cursor: pointer; border-radius: 1.5rem; padding: 1.1rem 1.25rem; text-align: left; width: 100%; transition: background .35s; }
    .tab-item:hover, .tab-item.on { background: #f6f5f3; }
    .tab-item .t-desc { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .4s ease; }
    .tab-item.on .t-desc { grid-template-rows: 1fr; }
    .tab-item .t-in { display: block; min-height: 0; overflow: hidden; }
    .tab-item .t-no { transition: background .3s, color .3s; }
    .tab-item.on .t-no { background: #f97316; color: #141414; }
    .tab-bar { transform-origin: 0 50%; transform: scaleX(0); }
    .pane { position: absolute; inset: 0; opacity: 0; transform: translateY(18px) scale(.98); transition: opacity .5s, transform .65s var(--spring); pointer-events: none; }
    .pane.on { opacity: 1; transform: none; pointer-events: auto; }
    @keyframes floaty { 0%, 100% { transform: translateY(0) rotate(var(--r, 0deg)); } 50% { transform: translateY(-10px) rotate(var(--r, 0deg)); } }

    /* FAQ */
    .faq-card { transition: background .4s; }
    .faq-card.open { background: #f97316; }
    .faq-content { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .35s ease; }
    .faq-card.open .faq-content { grid-template-rows: 1fr; }
    .faq-inner { overflow: hidden; }
    .faq-card .plus { transition: transform .4s var(--spring), background .25s, color .25s; }
    .faq-card.open .plus { transform: rotate(45deg); background: #141414; color: #fff; }
    .faq-card.open .f-muted { color: rgba(20,20,20,.75); }

    @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } html { scroll-behavior: auto; } }
  </style>
</head>
<body class="overflow-x-hidden bg-white font-sans text-ink antialiased">

@include('partials.navbar')

<main class="relative z-10 bg-white pt-28">

  {{-- ============ PROGRAM (langsung tanpa hero) ============ --}}
  <section id="program" class="relative mx-auto max-w-7xl px-6 pb-24 md:px-10">
    <div class="rv mb-8 max-w-3xl">
      <p class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">Program magang · PT Equityworld Futures</p>
      <h1 class="mt-2 font-head text-4xl font-normal tracking-tight md:text-6xl">Pilih bidang yang <strong class="font-semibold text-ewforange">paling kamu minati.</strong></h1>
      <p class="mt-4 text-sm text-zinc-500">{{ count($daftarProgram) }} program · {{ $totalPosisi }} posisi tersedia · Surabaya, On-Site, Full-Time</p>
    </div>

    {{-- Toolbar yang menempel saat scroll --}}
    <div class="sticky top-[4.75rem] z-40 mb-8">
      <div class="flex flex-col gap-3 rounded-[1.75rem] border border-black/10 bg-white/85 p-2 shadow-[0_18px_40px_-20px_rgba(0,0,0,.25)] backdrop-blur-xl md:flex-row md:items-center md:justify-between md:rounded-full">
        <div id="seg" class="relative flex overflow-x-auto rounded-full bg-sand p-1" style="scrollbar-width:none">
          <span id="segInd" class="absolute bottom-1 left-0 top-1 w-0 rounded-full bg-ink"></span>
          <button type="button" class="seg-btn on" data-kat="semua">Semua Program ({{ count($daftarProgram) }})</button>
          @foreach($kategoriHitung as $nama => $jml)
            <button type="button" class="seg-btn" data-kat="{{ $nama }}">{{ $nama }} ({{ $jml }})</button>
          @endforeach
        </div>

        <label class="flex items-center gap-2 rounded-full bg-sand px-4 py-2.5 md:mr-2 md:w-72">
          <svg class="h-4 w-4 shrink-0 text-black/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
          <input id="q" type="text" class="w-full bg-transparent text-sm outline-none placeholder:text-black/35" placeholder="Cari program, mis. Developer" autocomplete="off">
        </label>
      </div>
    </div>

    {{-- Grid bento --}}
    <div id="grid" class="grid grid-flow-dense gap-4 sm:grid-cols-2 lg:grid-cols-3">
      @foreach($daftarProgram as $p)
        @php $off = round(113.1 * (1 - $p['posisi'] / $maxPosisi), 1); @endphp
        <article class="prog {{ $p['urgent'] ? 'feat bg-ink text-white lg:col-span-2 lg:row-span-2' : 'bg-sand' }} flex min-h-[18rem] flex-col rounded-[1.75rem] p-6 {{ $p['urgent'] ? 'lg:min-h-[36.5rem] lg:p-9' : '' }}"
                 tabindex="0" role="button" aria-label="Lihat detail program {{ $p['judul'] }}"
                 data-id="{{ $p['id'] }}" data-kat="{{ $p['kategori'] }}"
                 data-judul="{{ Str::lower($p['judul']) }}" data-desk="{{ Str::lower($p['deskripsi']) }}">

          @if($p['urgent'])
            <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
              <div class="absolute left-1/4 top-0 h-64 w-64 rounded-full bg-orange-500/30 blur-[80px]" style="animation: stroll-a 8s ease-in-out infinite"></div>
              <div class="absolute bottom-0 right-1/4 h-64 w-64 rounded-full bg-white/10 blur-[80px]" style="animation: stroll-b 10s ease-in-out infinite"></div>
            </div>
          @endif

          <div class="flex items-start justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
              <span class="p-chip rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-wider {{ $p['urgent'] ? 'border-white/20 bg-white/10 text-white' : 'border-ink/15 bg-white' }}">{{ $p['kategori'] }}</span>
              @if($p['urgent'])
                <span class="rounded-full bg-ewforange px-2.5 py-1 text-[10px] font-bold tracking-wider text-ink">URGENT</span>
              @endif
            </div>

            <div class="relative shrink-0 {{ $p['urgent'] ? 'h-20 w-20' : 'h-14 w-14' }}" title="{{ $p['posisi'] }} posisi tersedia">
              <svg viewBox="0 0 44 44" class="h-full w-full -rotate-90">
                <circle class="ring-track" cx="22" cy="22" r="18" fill="none" stroke-width="4.5"/>
                <circle class="ring" cx="22" cy="22" r="18" fill="none" stroke="#f97316" stroke-width="4.5" stroke-linecap="round" stroke-dashoffset="{{ $off }}" data-off="{{ $off }}"/>
              </svg>
              <span class="absolute inset-0 grid place-items-center text-center leading-none">
                <span><b class="font-head {{ $p['urgent'] ? 'text-2xl' : 'text-lg' }} font-medium">{{ $p['posisi'] }}</b><small class="p-muted {{ $p['urgent'] ? 'text-white/60' : 'text-zinc-500' }} mt-0.5 block text-[8px] font-semibold uppercase tracking-wider">posisi</small></span>
              </span>
            </div>
          </div>

          <div class="mt-auto pt-10">
            <div class="mb-3 grid h-11 w-11 place-items-center rounded-2xl font-head text-lg font-medium {{ $p['urgent'] ? 'bg-ewforange text-ink' : 'bg-ink text-white' }}">{{ $p['huruf'] }}</div>
            <h3 class="font-head font-medium tracking-tight {{ $p['urgent'] ? 'text-4xl md:text-5xl' : 'text-2xl' }}">{{ $p['judul'] }}</h3>
            <p class="p-muted mt-2.5 text-sm leading-relaxed {{ $p['urgent'] ? 'max-w-md text-white/65 md:text-base' : 'line-clamp-3 text-zinc-500' }}">{{ $p['deskripsi'] }}</p>

            <div class="mt-6 flex items-end justify-between gap-3">
              <span class="p-muted text-[11px] font-semibold {{ $p['urgent'] ? 'text-orange-300' : 'text-zinc-500' }}">{{ $p['status'] }}</span>
              <span class="p-arrow grid h-11 w-11 shrink-0 place-items-center rounded-full {{ $p['urgent'] ? 'bg-ewforange text-ink' : 'bg-ink text-white' }}">{!! $panah !!}</span>
            </div>
          </div>
        </article>
      @endforeach
    </div>

    <div id="empty" class="hidden rounded-[1.75rem] border-2 border-dashed border-black/10 px-6 py-16 text-center">
      <p class="font-head text-2xl font-medium">Belum ada program yang cocok.</p>
      <p class="mx-auto mt-2 max-w-xs text-sm text-zinc-500">Coba kata kunci lain atau pilih "Semua Program".</p>
      <button type="button" id="btnReset" class="press dk mt-6 rounded-full bg-ink px-6 py-2.5 text-sm font-semibold text-white">Atur ulang</button>
    </div>
  </section>

  {{-- ============ SYARAT & ALUR ============ --}}
  <section class="bg-sand py-24">
    <div class="mx-auto grid max-w-7xl gap-14 px-6 md:px-10 lg:grid-cols-[1fr_1.1fr]">
      <div class="lg:sticky lg:top-28 lg:self-start">
        <div class="rv">
          <p class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">Syarat &amp; alur</p>
          <h2 class="mt-2 font-head text-3xl font-normal tracking-tight md:text-5xl">Terbuka untuk kamu yang <strong class="font-semibold text-ewforange">siap belajar.</strong></h2>
          <p class="mt-4 max-w-md text-sm leading-relaxed text-zinc-500">Program magang kami terbuka bagi beberapa kalangan berikut. Seluruh prosesnya gratis, tanpa biaya akomodasi maupun transportasi.</p>
        </div>

        <div class="mt-8 grid gap-3">
          @foreach(['Mahasiswa semester 5 ke atas', 'Mahasiswa tingkat akhir yang sedang menyusun skripsi', 'Fresh graduate'] as $syarat)
            <div class="rv flex items-center gap-4 rounded-2xl bg-white p-4">
              <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-ewforange text-ink">{!! $cek !!}</span>
              <span class="text-sm font-semibold">{{ $syarat }}</span>
            </div>
          @endforeach
        </div>
      </div>

      <ol id="tl" class="relative space-y-6 pl-14">
        <div class="absolute bottom-3 left-[1.15rem] top-3 w-0.5 bg-zinc-200"><span id="tlFill" class="block h-full w-full bg-ewforange"></span></div>
        @foreach([
          ['Daftar & kirim CV',     'Buat akun, lengkapi profil, lalu kirim CV secara online lewat portal ini.'],
          ['Seleksi administrasi',  'Tim HR menyaring berkas dan kecocokan profilmu dengan program yang dipilih.'],
          ['Tes & wawancara',       'Tes tertulis, lalu wawancara dengan HR dan divisi terkait (User).'],
          ['Pengumuman hasil',      'Hasil final diumumkan lewat portal dan email resmi perusahaan, estimasi 7 sampai 14 hari kerja.'],
        ] as $i => [$jdl, $isi])
          <li class="tl-step rv relative rounded-[1.75rem] bg-white p-6">
            <span class="node absolute -left-14 top-6 grid h-10 w-10 place-items-center rounded-full border-2 border-zinc-300 bg-white font-head text-base font-medium text-zinc-500">{{ $i + 1 }}</span>
            <span class="big pointer-events-none absolute right-5 top-2 select-none font-head text-6xl font-semibold">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
            <h3 class="relative font-head text-xl font-medium tracking-tight">{{ $jdl }}</h3>
            <p class="relative mt-1.5 max-w-sm text-sm leading-relaxed text-zinc-500">{{ $isi }}</p>
          </li>
        @endforeach
      </ol>
    </div>
  </section>

  {{-- ============ FITUR (tab otomatis berganti) ============ --}}
  <section class="mx-auto max-w-7xl px-6 py-24 md:px-10">
    <div class="rv mb-12 max-w-2xl">
      <p class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">Fitur pendukung</p>
      <h2 class="mt-2 font-head text-3xl font-normal tracking-tight md:text-5xl">Semua kebutuhan magangmu, <strong class="font-semibold text-ewforange">satu akun.</strong></h2>
    </div>

    <div id="tabs" class="grid items-stretch gap-6 lg:grid-cols-[1fr_1.2fr]">
      <div class="grid content-start gap-2">
        @foreach([
          ['Pencocokan posisi berbasis AI & keahlian', 'Sistem cerdas membaca resume, riwayat studi, dan portofolio kamu untuk merekomendasikan program dengan kecocokan tertinggi.'],
          ['Kelola dokumen praktis',                   'Simpan portofolio, CV, dan berkas lamaran di satu akun untuk melamar ke banyak program tanpa ribet.'],
          ['Filter cerdas & bebas bias',               'Penyaringan awal murni berdasarkan keahlian, bukan identitas, untuk budaya kerja yang setara.'],
          ['Integrasi portofolio & sertifikasi',       'Tautkan GitHub, LinkedIn, Dribbble, atau lampirkan lisensi WPPE/WAK/WMI langsung ke profil pelamar.'],
        ] as $i => [$jdl, $isi])
          <button type="button" class="tab-item {{ $i === 0 ? 'on' : '' }}" data-tab="{{ $i }}">
            <span class="flex items-center gap-3">
              <span class="t-no grid h-8 w-8 shrink-0 place-items-center rounded-full bg-sand font-head text-sm font-medium">{{ $i + 1 }}</span>
              <span class="font-head text-lg font-medium tracking-tight">{{ $jdl }}</span>
            </span>
            <span class="t-desc"><span class="t-in"><span class="block pb-1 pl-11 pt-2 text-sm leading-relaxed text-zinc-500">{{ $isi }}</span></span></span>
            <span class="mt-3 block h-0.5 overflow-hidden rounded-full bg-zinc-200"><i class="tab-bar block h-full w-full bg-ewforange"></i></span>
          </button>
        @endforeach
      </div>

      <div class="relative h-[26rem] overflow-hidden rounded-[2rem] lg:h-auto lg:min-h-[28rem]">
        {{-- 1 --}}
        <div class="pane on grid place-items-center bg-ewforange p-8" data-pane="0">
          <div class="w-full max-w-xs rounded-3xl bg-white p-5 shadow-xl" style="animation: floaty 6s ease-in-out infinite">
            <div class="flex items-center gap-4">
              <svg viewBox="0 0 44 44" class="h-16 w-16 -rotate-90"><circle cx="22" cy="22" r="18" fill="none" stroke="#f1f1f3" stroke-width="5"/><circle cx="22" cy="22" r="18" fill="none" stroke="#f97316" stroke-width="5" stroke-linecap="round" stroke-dasharray="113.1" stroke-dashoffset="2.3"/></svg>
              <div><p class="font-head text-3xl font-medium leading-none">98%</p><p class="mt-1 text-[11px] font-semibold text-zinc-500">Kecocokan profil</p></div>
            </div>
            <p class="mt-4 text-xs font-semibold">UI/UX Product Designer</p>
            <p class="text-[11px] text-zinc-500">PT Equityworld</p>
            <div class="mt-3 flex gap-2 text-[10px] font-semibold"><span class="rounded-full bg-sand px-2.5 py-1">Figma Skills</span><span class="rounded-full bg-sand px-2.5 py-1">Design System</span></div>
          </div>
        </div>
        {{-- 2 --}}
        <div class="pane flex items-center justify-center gap-4 bg-ink p-8" data-pane="1">
          @foreach([['DOC', '#93c5fd', '-6deg', '0s'], ['PDF', '#fca5a5', '3deg', '.6s'], ['IMG', '#86efac', '-2deg', '1.2s']] as [$tipe, $warna, $rot, $delay])
            <div class="grid h-32 w-24 place-items-center rounded-2xl text-sm font-bold text-ink shadow-xl" style="background:{{ $warna }};--r:{{ $rot }};animation: floaty 5s ease-in-out {{ $delay }} infinite">{{ $tipe }}</div>
          @endforeach
        </div>
        {{-- 3 --}}
        <div class="pane flex flex-wrap content-center justify-center gap-3 bg-sand p-8" data-pane="2">
          @foreach(['Kemampuan Logika', 'Portofolio', 'Keahlian Teknis', 'Tanpa Batas Usia'] as $n => $tag)
            <span class="rounded-full border border-ink/10 bg-white px-5 py-3 text-sm font-semibold shadow-sm {{ $n === 3 ? 'text-rose-500' : '' }}" style="animation: floaty {{ 5 + $n }}s ease-in-out {{ $n * .4 }}s infinite">{{ $tag }}</span>
          @endforeach
        </div>
        {{-- 4 --}}
        <div class="pane flex flex-col justify-center gap-5 border border-black/10 bg-white p-8" data-pane="3">
          <div class="flex gap-3">
            <span class="grid h-14 w-14 place-items-center rounded-2xl bg-ink text-xs font-bold text-white">GH</span>
            <span class="grid h-14 w-14 place-items-center rounded-2xl bg-blue-100 text-xs font-bold text-blue-600">IN</span>
            <span class="grid h-14 w-14 place-items-center rounded-2xl bg-pink-100 text-xs font-bold text-pink-600">DR</span>
          </div>
          <div class="grid gap-2 sm:grid-cols-3">
            <div class="rounded-2xl bg-sand p-3"><p class="text-[10px] text-zinc-500">Status verifikasi</p><p class="mt-1 text-xs font-bold text-green-600">✓ Otomatis diterima</p></div>
            <div class="rounded-2xl bg-sand p-3"><p class="text-[10px] text-zinc-500">Sertifikasi</p><p class="mt-1 text-xs font-bold">Mitra terverifikasi</p></div>
            <div class="rounded-2xl bg-sand p-3"><p class="text-[10px] text-zinc-500">Perekrutan</p><p class="mt-1 text-xs font-bold">3x lebih cepat</p></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ============ FAQ ============ --}}
  <section class="mx-auto max-w-5xl px-6 pb-24 md:px-10">
    <div class="rv mb-10 text-center">
      <p class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">FAQ</p>
      <h2 class="mt-2 font-head text-3xl font-normal tracking-tight md:text-5xl">Yang sering <strong class="font-semibold text-ewforange">ditanyakan.</strong></h2>
    </div>

    <div class="grid items-start gap-4 md:grid-cols-2">
      @foreach([
        ['Bagaimana tahapan rekrutmen magang di kantor kami?', 'Proses dimulai dari pendaftaran CV secara online di portal ini. Jika lolos seleksi administrasi, Anda akan diundang untuk tes tertulis dan wawancara dengan HR dan divisi terkait (User). Hasil final diumumkan melalui portal ini dan email resmi perusahaan.'],
        ['Berapa lama estimasi proses seleksi hingga penawaran?', 'Standar proses seleksi kami memakan waktu 7 hingga 14 hari kerja terhitung dari tanggal penutupan pendaftaran.'],
        ['Apakah mahasiswa akhir atau fresh graduate dapat melamar?', 'Tentu. Program magang kami terbuka bagi mahasiswa semester 5 ke atas, mahasiswa tingkat akhir yang sedang menyusun skripsi, maupun fresh graduate.'],
        ['Apakah seluruh proses rekrutmen ini dipungut biaya?', 'Tidak. Seluruh proses rekrutmen PT Equityworld Futures bersifat 100% gratis. Kami tidak pernah meminta biaya akomodasi, transportasi, atau biaya apa pun kepada kandidat.'],
      ] as $i => [$tanya, $jawab])
        <div class="faq-card rv rounded-[1.75rem] bg-sand {{ $i === 0 ? 'open' : '' }}">
          <button type="button" class="faq-btn flex w-full items-center gap-4 p-6 text-left" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
            <span class="flex-1 font-head text-lg font-medium leading-snug tracking-tight">{{ $tanya }}</span>
            <span class="plus grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white text-xl leading-none">+</span>
          </button>
          <div class="faq-content"><div class="faq-inner"><p class="f-muted px-6 pb-6 text-sm leading-relaxed text-zinc-500">{{ $jawab }}</p></div></div>
        </div>
      @endforeach
    </div>

    <div class="rv mt-6 flex flex-col items-center justify-between gap-4 rounded-[1.75rem] border border-black/10 p-6 md:flex-row">
      <div class="text-center md:text-left">
        <p class="font-head text-xl font-medium">Punya pertanyaan khusus?</p>
        <p class="mt-1 text-sm text-zinc-500">Tim Rekrutmen EWF siap membantu menanyakan status aplikasi atau informasi lainnya.</p>
      </div>
      <div class="flex gap-2">
        <a href="#" class="press dk rounded-full bg-ink px-6 py-3 text-xs font-semibold text-white">Konsultasi via WhatsApp</a>
        <a href="#" class="press dk rounded-full border border-black/15 px-6 py-3 text-xs font-semibold">Kirim email</a>
      </div>
    </div>
  </section>

  {{-- ============ CTA ============ --}}
  <section class="mx-auto max-w-7xl px-6 pb-24 md:px-10">
    <div class="rv relative overflow-hidden rounded-[2rem] bg-ink px-8 py-20 text-center text-white">
      <div class="pointer-events-none absolute left-1/4 top-0 h-64 w-64 rounded-full bg-orange-500/30 blur-[80px]" style="animation: stroll-a 8s ease-in-out infinite" aria-hidden="true"></div>
      <div class="pointer-events-none absolute bottom-0 right-1/4 h-64 w-64 rounded-full bg-white/10 blur-[80px]" style="animation: stroll-b 10s ease-in-out infinite" aria-hidden="true"></div>

      <h2 class="relative mx-auto max-w-2xl font-head text-3xl font-light leading-tight tracking-tight md:text-5xl">Siap mengambil langkah baru <strong class="font-semibold text-ewforange">dalam kariermu?</strong></h2>
      <p class="relative mx-auto mt-4 max-w-md text-sm text-white/60">Bergabunglah bersama talenta berbakat yang mewujudkan impian karier profesional di PT Equityworld Futures.</p>
      <div class="relative mt-9 flex flex-wrap justify-center gap-3">
        <a href="{{ route('register') }}" class="press rounded-full bg-ewforange px-8 py-3.5 text-sm font-semibold text-ink">Daftar Akun</a>
        <a href="{{ route('lowongan.index') }}" class="press dk rounded-full border border-white/25 px-8 py-3.5 text-sm font-semibold text-white">Lihat Lowongan Kerja</a>
      </div>
    </div>
  </section>
</main>

{{-- ============ MODAL DETAIL PROGRAM ============ --}}
<div id="modal" class="fixed inset-0 z-[70] grid place-items-center p-4" aria-hidden="true">
  <div id="modalBg" class="absolute inset-0 bg-ink/55 backdrop-blur-sm"></div>
  <div id="modalPanel" class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-[2rem] bg-white p-7 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="pmJudul">
    <button type="button" id="pmClose" class="press dk absolute right-5 top-5 grid h-10 w-10 place-items-center rounded-full bg-sand" aria-label="Tutup">
      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
    </button>

    <div class="flex items-center gap-4 pr-12">
      <div id="pmHuruf" class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-ink font-head text-2xl font-medium text-white"></div>
      <div>
        <span id="pmUrgent" class="mb-1 hidden rounded-full bg-ewforange px-2 py-0.5 text-[10px] font-bold tracking-wider text-ink">URGENT</span>
        <h3 id="pmJudul" class="font-head text-2xl font-medium tracking-tight"></h3>
        <p id="pmKat" class="text-xs font-semibold text-zinc-500"></p>
      </div>
    </div>

    <p id="pmDesk" class="mt-6 text-sm leading-relaxed text-zinc-600"></p>

    <dl class="mt-6 grid grid-cols-2 gap-3 text-sm">
      <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Posisi tersedia</dt><dd id="pmPosisi" class="mt-1 font-head text-lg font-medium"></dd></div>
      <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Status</dt><dd id="pmStatus" class="mt-1 font-semibold"></dd></div>
      <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Lokasi</dt><dd class="mt-1 font-semibold">Surabaya (On-Site)</dd></div>
      <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Tipe</dt><dd class="mt-1 font-semibold">Full-Time</dd></div>
    </dl>

    <p class="mt-4 rounded-2xl border border-black/10 px-4 py-3 text-xs leading-relaxed text-zinc-600">Terbuka untuk mahasiswa semester 5 ke atas, mahasiswa tingkat akhir, dan fresh graduate. Proses rekrutmen 100% gratis.</p>

    <a id="pmLamar" href="#" class="press mt-6 flex items-center justify-center gap-2 rounded-full bg-ewforange px-8 py-3.5 text-sm font-semibold text-ink">Lihat Detail &amp; Lamar {!! $panah !!}</a>
  </div>
</div>

{{-- GSAP dimuat SEBELUM footer, supaya skrip interaktif di footer (kalau ada) menemukan gsap --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<div class="relative z-30 bg-white">
  @include('partials.footer')
</div>

<script>
(function () {
  const root = document.documentElement;
  const $  = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];
  const hasGsap = !!(window.gsap && window.ScrollTrigger) && root.classList.contains('js');

  const PROGRAMS = @json($daftarProgram);
  const LAMAR    = @json(route('program.show', ['id' => '__ID__']));

  /* ===== Toolbar: indikator geser ===== */
  const seg = $('#seg'), ind = $('#segInd'), segBtns = $$('.seg-btn');
  const moveInd = (b, anim = true) => {
    if (!anim) ind.style.transition = 'none';
    ind.style.width = b.offsetWidth + 'px';
    ind.style.transform = 'translateX(' + b.offsetLeft + 'px)';
    if (!anim) requestAnimationFrame(() => ind.style.transition = '');
  };
  const activeBtn = () => segBtns.find(b => b.classList.contains('on'));
  moveInd(activeBtn(), false);
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(() => moveInd(activeBtn(), false));
  addEventListener('resize', () => moveInd(activeBtn(), false));

  /* ===== Filter & cari ===== */
  const grid = $('#grid'), cards = $$('.prog', grid), empty = $('#empty');
  const state = { q: '', kat: 'semua' };

  function apply(animate = true) {
    const q = state.q.trim().toLowerCase();
    const vis = cards.filter(c =>
      (state.kat === 'semua' || c.dataset.kat === state.kat) &&
      (!q || c.dataset.judul.includes(q) || c.dataset.desk.includes(q)));
    cards.forEach(c => c.classList.toggle('hidden', !vis.includes(c)));
    empty.classList.toggle('hidden', vis.length > 0);

    if (hasGsap && animate) {
      gsap.killTweensOf(vis);
      gsap.fromTo(vis, { opacity: 0, y: 24, scale: .97 }, { opacity: 1, y: 0, scale: 1, duration: .55, stagger: .07, ease: 'power3.out' });
    } else {
      vis.forEach(c => c.style.opacity = 1);
    }
  }

  segBtns.forEach(b => b.addEventListener('click', () => {
    segBtns.forEach(x => x.classList.toggle('on', x === b));
    moveInd(b);
    state.kat = b.dataset.kat; apply();
  }));
  $('#q').addEventListener('input', e => { state.q = e.target.value; apply(); });
  $('#btnReset').addEventListener('click', () => {
    state.q = ''; state.kat = 'semua'; $('#q').value = '';
    segBtns.forEach(x => x.classList.toggle('on', x.dataset.kat === 'semua')); moveInd(segBtns[0]); apply();
  });

  /* ===== Modal detail ===== */
  const modal = $('#modal'); let lastTrigger = null;
  function openModal(id, trigger) {
    const p = PROGRAMS.find(x => x.id == id); if (!p) return;
    lastTrigger = trigger;
    $('#pmHuruf').textContent = p.huruf;
    $('#pmJudul').textContent = p.judul;
    $('#pmKat').textContent   = p.kategori;
    $('#pmDesk').textContent  = p.deskripsi;
    $('#pmPosisi').textContent = p.posisi + ' posisi';
    $('#pmStatus').textContent = p.status;
    $('#pmUrgent').classList.toggle('hidden', !p.urgent);
    $('#pmLamar').href = LAMAR.replace('__ID__', p.id);
    modal.classList.add('open'); modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    setTimeout(() => $('#pmClose').focus(), 250);
  }
  function closeModal() {
    modal.classList.remove('open'); modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (lastTrigger) lastTrigger.focus({ preventScroll: true });
  }
  cards.forEach(c => {
    c.addEventListener('click', () => openModal(c.dataset.id, c));
    c.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openModal(c.dataset.id, c); } });
  });
  $('#pmClose').addEventListener('click', closeModal);
  $('#modalBg').addEventListener('click', closeModal);
  addEventListener('keydown', e => { if (e.key === 'Escape' && modal.classList.contains('open')) closeModal(); });

  /* ===== FAQ ===== */
  $$('.faq-btn').forEach(btn => btn.addEventListener('click', () => {
    const card = btn.closest('.faq-card'), open = card.classList.contains('open');
    $$('.faq-card').forEach(c => { c.classList.remove('open'); c.querySelector('.faq-btn').setAttribute('aria-expanded', 'false'); });
    if (!open) { card.classList.add('open'); btn.setAttribute('aria-expanded', 'true'); }
  }));

  /* ===== Tab fitur ===== */
  const tabs = $$('.tab-item'), panes = $$('.pane'), bars = $$('.tab-bar');
  let cur = 0, barTween = null, hovering = false;
  function setTab(i) {
    cur = i;
    tabs.forEach((t, k) => t.classList.toggle('on', k === i));
    panes.forEach((p, k) => p.classList.toggle('on', k === i));
    if (!hasGsap) return;
    if (barTween) barTween.kill();
    gsap.set(bars, { scaleX: 0 });
    barTween = gsap.to(bars[i], { scaleX: 1, duration: 5.5, ease: 'none', onComplete: () => setTab((cur + 1) % tabs.length) });
    if (hovering) barTween.pause();
  }
  tabs.forEach((t, k) => t.addEventListener('click', () => setTab(k)));
  $('#tabs').addEventListener('mouseenter', () => { hovering = true;  if (barTween) barTween.pause(); });
  $('#tabs').addEventListener('mouseleave', () => { hovering = false; if (barTween) barTween.resume(); });

  /* ===== Animasi ===== */
  if (!hasGsap) { root.classList.remove('js'); cards.forEach(c => c.style.opacity = 1); return; }
  gsap.registerPlugin(ScrollTrigger);

  // Saat reload, tunggu splash ERA selesai dulu. Pindah halaman biasa: langsung jalan.
  const whenReady = fn => (window.eraSplash && window.eraSplash.active && !window.eraSplash.revealed)
    ? document.addEventListener('era:reveal', fn, { once: true })
    : fn();

  whenReady(() => {
    // Reveal umum
    gsap.set('.rv', { y: 30 });
    ScrollTrigger.batch('.rv', {
      start: 'top 90%', once: true,
      onEnter: els => gsap.to(els, { opacity: 1, y: 0, duration: .85, stagger: .09, ease: 'power3.out' })
    });

    // Kartu program muncul bergantian, cincin kuota terisi
    gsap.set(cards, { y: 36 });
    ScrollTrigger.create({
      trigger: grid, start: 'top 92%', once: true,
      onEnter: () => {
        gsap.to(cards.filter(c => !c.classList.contains('hidden')), { opacity: 1, y: 0, duration: .8, stagger: .1, ease: 'power3.out' });
        $$('.ring').forEach(r => gsap.fromTo(r, { strokeDashoffset: 113.1 }, { strokeDashoffset: +r.dataset.off, duration: 1.4, delay: .3, ease: 'power3.out' }));
      }
    });

    // Alur: garis terisi, langkah menyala
    gsap.fromTo('#tlFill', { scaleY: 0 }, { scaleY: 1, ease: 'none', scrollTrigger: { trigger: '#tl', start: 'top 70%', end: 'bottom 60%', scrub: .4 } });
    $$('.tl-step').forEach(s => ScrollTrigger.create({
      trigger: s, start: 'top 68%',
      onEnter: () => s.classList.add('on'), onLeaveBack: () => s.classList.remove('on')
    }));

    // Tab fitur mulai berputar saat terlihat
    ScrollTrigger.create({ trigger: '#tabs', start: 'top 80%', once: true, onEnter: () => setTab(0) });
  });
})();
</script>
</body>
</html>