{{--
  Halaman Lowongan Kerja (route: lowongan.index)
  Letakkan sebagai view index lowongan (mis. resources/views/pages/lowongan/index.blade.php, sesuai yang dipakai LowonganController).

  - Navbar & footer diambil dari partials (partials.navbar, partials.footer).
  - Foto hero: public/images/karier/lowongan.jpg  (kalau belum ada, otomatis memakai public/images/tentang/hero.jpg)
  - Data lowongan di bawah masih contoh statis. Ganti $daftarLowongan dengan data dari database
    (field yang dipakai: id, judul, huruf, kategori, posisi, urgent, status, gaji, gaji_max, deskripsi).
    Tombol "Lamar" menuju route('lowongan.show', id), jadi id harus cocok dengan data di database.
--}}
@php
  use Illuminate\Support\Str;

  $foto = file_exists(public_path('images/karier/lowongan.jpg'))
      ? asset('images/karier/lowongan.jpg')
      : asset('images/tentang/hero.jpg');

  $daftarLowongan = [
    ['id' => 1, 'judul' => 'Konsultan Bisnis',  'huruf' => 'K', 'kategori' => 'Bisnis & Sales Strategis', 'posisi' => 2, 'urgent' => true,  'status' => 'Ditutup 3 hari lagi',  'gaji' => 'Rp 4 - 10 Jt', 'gaji_max' => 10, 'deskripsi' => 'Menganalisa tren pasar derivatif dan data transaksi harian untuk portofolio klien secara profesional.'],
    ['id' => 2, 'judul' => 'Asisten Manajer',   'huruf' => 'A', 'kategori' => 'Bisnis & Sales Strategis', 'posisi' => 3, 'urgent' => false, 'status' => 'Tayang 2 hari lalu', 'gaji' => 'Rp 4 - 6 Jt',  'gaji_max' => 6,  'deskripsi' => 'Praktik tata kelola tim pemasaran, edukasi nasabah VIP, dan dukung kelancaran strategi operasional kantor cabang.'],
    ['id' => 3, 'judul' => 'Security',          'huruf' => 'S', 'kategori' => 'Operasional & SDM',        'posisi' => 2, 'urgent' => false, 'status' => 'Tayang 4 hari lalu', 'gaji' => 'Rp 2 - 3 Jt',  'gaji_max' => 3,  'deskripsi' => 'Menjaga keamanan dan ketertiban area kantor, serta mengarahkan tamu atau klien VIP sesuai standar prosedur.'],
    ['id' => 4, 'judul' => 'Cleaning Service',  'huruf' => 'C', 'kategori' => 'Operasional & SDM',        'posisi' => 2, 'urgent' => false, 'status' => 'Tayang 5 hari lalu', 'gaji' => 'Rp 2 - 3 Jt',  'gaji_max' => 3,  'deskripsi' => 'Merawat kebersihan seluruh fasilitas kantor cabang dan membantu kelancaran kegiatan acara internal operasional.'],
    ['id' => 5, 'judul' => 'HRD',               'huruf' => 'H', 'kategori' => 'Operasional & SDM',        'posisi' => 1, 'urgent' => false, 'status' => 'Tayang 2 hari lalu', 'gaji' => 'Rp 4 - 6 Jt',  'gaji_max' => 6,  'deskripsi' => 'Memimpin proses rekrutmen karyawan baru, pelatihan, dan evaluasi kinerja tim operasional sesuai regulasi perusahaan.'],
  ];

  $totalPosisi = array_sum(array_column($daftarLowongan, 'posisi'));
  $kategoriHitung = collect($daftarLowongan)->groupBy('kategori')->map->count();

  $panah = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M7 17 17 7M8 7h9v9"/></svg>';
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lowongan Kerja - PT Equityworld Futures</title>
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
    html.js .job { opacity: 0; }

    /* hero foto penuh: foto diam, lapisan putih naik menimpa */
    .cover-bg { position: fixed; inset: 0; z-index: 0; background-image: var(--img), radial-gradient(60rem 30rem at 85% 8%, rgba(249,115,22,.35), transparent 60%), linear-gradient(135deg, #1f1f22, #0b0b0c); background-size: cover; background-position: center; background-repeat: no-repeat; will-change: transform; }
    .cover-bg::after { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(8,8,9,.55) 0%, rgba(8,8,9,.15) 55%, rgba(8,8,9,.05) 100%), linear-gradient(0deg, rgba(8,8,9,.4), transparent 40%); }

    /* tombol: membal, tertekan, ada bayangan (tanpa bergerak mengikuti kursor) */
    .press { --sh: 234, 88, 12; transition: background .2s, color .2s, border-color .2s, box-shadow .25s, transform .45s var(--spring); -webkit-tap-highlight-color: transparent; }
    .press.dk { --sh: 20, 20, 20; }
    .press:hover { transform: scale(.96); box-shadow: 0 10px 22px -8px rgba(var(--sh), .55); }
    .press:active { transform: scale(.91); box-shadow: 0 2px 6px -2px rgba(var(--sh), .5), inset 0 2px 6px rgba(0,0,0,.18); transition-duration: .1s; }
    .press:focus-visible { outline: 2px solid #141414; outline-offset: 3px; }

    /* filter */
    .chip { display: flex; align-items: center; justify-content: space-between; gap: .75rem; width: 100%; padding: .6rem .9rem; border-radius: 14px; border: 1px solid rgba(20,20,20,.1); background: #fff; font-size: .82rem; font-weight: 600; color: #3f3f46; text-align: left; cursor: pointer; }
    .chip small { font-size: .7rem; font-weight: 700; padding: .1rem .5rem; border-radius: 99px; background: #f6f5f3; color: #71717a; }
    .chip:hover { border-color: #f97316; }
    .chip.on { background: #141414; border-color: #141414; color: #fff; }
    .chip.on small { background: #f97316; color: #141414; }
    .switch { position: relative; width: 2.75rem; height: 1.6rem; border-radius: 99px; background: #d4d4d8; transition: background .25s; flex: none; }
    .switch::after { content: ""; position: absolute; top: .2rem; left: .2rem; width: 1.2rem; height: 1.2rem; border-radius: 50%; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.3); transition: transform .35s var(--spring); }
    .switch[aria-checked="true"] { background: #f97316; }
    .switch[aria-checked="true"]::after { transform: translateX(1.15rem); }

    /* baris lowongan: tinta hitam menyapu dari kiri saat di-hover */
    .job { position: relative; overflow: hidden; isolation: isolate; cursor: pointer; transition: color .35s; }
    .job::before { content: ""; position: absolute; inset: 0; background: #141414; transform: scaleX(0); transform-origin: left; transition: transform .55s cubic-bezier(.2,.8,.2,1); z-index: -1; }
    .job:hover::before, .job:focus-visible::before { transform: scaleX(1); }
    .job:hover, .job:focus-visible { color: #fff; outline: none; }
    .job .j-muted { transition: color .35s; }
    .job:hover .j-muted, .job:focus-visible .j-muted { color: rgba(255,255,255,.7); }
    .job .j-chip { transition: background .35s, border-color .35s, color .35s; }
    .job:hover .j-chip, .job:focus-visible .j-chip { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.22); color: #fff; }
    .job .j-arrow { transition: background .35s, color .35s, transform .5s var(--spring); }
    .job:hover .j-arrow, .job:focus-visible .j-arrow { background: #f97316; color: #141414; transform: rotate(45deg); }

    /* drawer detail */
    #drawer { pointer-events: none; }
    #drawer.open { pointer-events: auto; }
    #drawerBg { opacity: 0; transition: opacity .35s; }
    #drawer.open #drawerBg { opacity: 1; }
    #drawerPanel { transform: translateX(100%); transition: transform .55s cubic-bezier(.2,.9,.2,1); }
    #drawer.open #drawerPanel { transform: none; }

    /* alur seleksi */
    #flowFill { transform-origin: 0 50%; }
    .js #flowFill { transform: scaleX(0); }
    .flow-step .dot { transition: background .4s, color .4s, border-color .4s, transform .5s var(--spring); }
    .flow-step.on .dot, html:not(.js) .flow-step .dot { background: #f97316; border-color: #f97316; color: #141414; transform: scale(1.08); }

    /* FAQ */
    .faq-content { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .35s ease; }
    .faq-content.open { grid-template-rows: 1fr; }
    .faq-inner { overflow: hidden; }
    .faq-btn .plus { transition: transform .35s var(--spring), background .25s, color .25s; }
    .faq-btn.active .plus { transform: rotate(45deg); background: #f97316; color: #141414; }

    /* marquee & animasi dekoratif */
    @keyframes mq { to { transform: translateX(-50%); } }
    .mq-track { display: flex; width: max-content; animation: mq 38s linear infinite; }
    .mq:hover .mq-track { animation-play-state: paused; }
    @keyframes cue { 0% { transform: translateY(-100%); } 100% { transform: translateY(100%); } }
    @keyframes floaty { 0%, 100% { transform: translateY(0) rotate(var(--r, 0deg)); } 50% { transform: translateY(-10px) rotate(var(--r, 0deg)); } }
    @keyframes blip { 0% { box-shadow: 0 0 0 0 rgba(249,115,22,.6); } 100% { box-shadow: 0 0 0 10px rgba(249,115,22,0); } }

    @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } html { scroll-behavior: auto; } }
  </style>
</head>
<body class="overflow-x-hidden bg-white font-sans text-ink antialiased">

@include('partials.navbar')

<main class="relative z-10">

  {{-- ============ HERO FOTO PENUH ============ --}}
  <section class="relative flex min-h-screen flex-col text-white">
    <div class="cover-bg" style="--img:url('{{ $foto }}')" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto flex w-full max-w-[84rem] flex-1 flex-col px-6 pb-44 pt-32">
      <div class="mt-auto max-w-3xl" id="heroText">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-3.5 py-1.5 text-[11px] font-semibold uppercase tracking-[.1em] backdrop-blur-md">
          <i class="h-2 w-2 rounded-full bg-ewforange" style="animation: blip 1.8s infinite"></i>
          {{ count($daftarLowongan) }} lowongan · {{ $totalPosisi }} posisi terbuka
        </span>
        <h1 class="mt-6 font-head text-[clamp(2.7rem,6.6vw,5.6rem)] font-light leading-[1.04] tracking-tight">
          <span class="block">Temukan Karier Impian</span>
          <span class="block">Yang <strong class="font-semibold">Tepat</strong> Untukmu</span>
        </h1>
        <p class="mt-6 max-w-md text-base text-white/80">Peluang karier penuh waktu dengan benefit eksklusif di industri pialang berjangka.</p>
      </div>

      <div class="absolute bottom-24 right-6 hidden flex-col items-center gap-2 text-[10px] font-semibold uppercase tracking-[.25em] text-white/70 md:flex">
        Gulir
        <span class="block h-12 w-px overflow-hidden bg-white/25"><i class="block h-full w-full bg-ewforange" style="animation: cue 1.8s ease-in-out infinite"></i></span>
      </div>
    </div>
  </section>

  {{-- ============ LAPISAN PUTIH ============ --}}
  <div class="relative z-20 -mt-9 rounded-t-[2rem] bg-white pt-28 shadow-[0_-18px_50px_rgba(0,0,0,.12)]" id="sheet">

    {{-- Pencarian melayang di tepi foto --}}
    <div class="absolute inset-x-0 top-0 z-30 -translate-y-1/2 px-4">
      <div class="rv mx-auto flex max-w-4xl flex-col gap-2 rounded-[2rem] border border-black/5 bg-white p-2 shadow-[0_24px_60px_-18px_rgba(0,0,0,.4)] md:flex-row md:items-center md:rounded-full">
        <label class="flex flex-1 items-center gap-3 px-5 py-2 md:border-r md:border-black/10">
          <svg class="h-5 w-5 shrink-0 text-black/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
          <span class="block w-full">
            <span class="block text-[10px] font-semibold tracking-wider text-black/60">POSISI / KEAHLIAN</span>
            <input type="text" class="q-input w-full bg-transparent text-sm font-medium outline-none placeholder:text-black/30" placeholder="Contoh: Asisten Manajer" autocomplete="off">
          </span>
        </label>
        <div class="flex flex-1 items-center gap-3 px-5 py-2">
          <svg class="h-5 w-5 shrink-0 text-black/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
          <span class="block">
            <span class="block text-[10px] font-semibold tracking-wider text-black/60">LOKASI</span>
            <span class="block text-sm font-medium">Trillium Office &amp; Residence, Surabaya</span>
          </span>
        </div>
        <button type="button" id="btnCari" class="press rounded-full bg-ewforange px-8 py-3.5 text-sm font-semibold text-ink hover:bg-orange-600">Cari Lowongan</button>
      </div>
    </div>

    {{-- ============ DAFTAR LOWONGAN ============ --}}
    <section id="daftar" class="mx-auto max-w-7xl scroll-mt-24 px-6 pb-24 md:px-10">
      <div class="rv mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">Eksplorasi lowongan</p>
          <h2 class="mt-2 font-head text-3xl font-normal tracking-tight md:text-5xl">Posisi yang <strong class="font-semibold text-ewforange">sedang dibuka.</strong></h2>
        </div>
        <p class="max-w-xs text-sm text-zinc-500">Klik salah satu posisi untuk melihat ringkasan tanpa meninggalkan halaman ini.</p>
      </div>

      <div class="grid gap-8 lg:grid-cols-[17rem_1fr]">
        {{-- Filter --}}
        <aside class="rv lg:sticky lg:top-24 lg:self-start">
          <div class="rounded-[1.75rem] bg-sand p-5">
            <label class="mb-4 flex items-center gap-2 rounded-full bg-white px-4 py-2.5">
              <svg class="h-4 w-4 shrink-0 text-black/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
              <input type="text" class="q-input w-full bg-transparent text-sm outline-none placeholder:text-black/30" placeholder="Cari posisi..." autocomplete="off">
            </label>

            <p class="mb-2 text-[11px] font-bold uppercase tracking-[.12em] text-zinc-500">Kategori</p>
            <div class="grid gap-2" id="katList">
              <button type="button" class="chip press dk on" data-kat="semua">Semua posisi <small>{{ count($daftarLowongan) }}</small></button>
              @foreach($kategoriHitung as $nama => $jml)
                <button type="button" class="chip press dk" data-kat="{{ $nama }}">{{ $nama }} <small>{{ $jml }}</small></button>
              @endforeach
            </div>

            <div class="mt-5 flex items-center justify-between gap-3 rounded-2xl bg-white px-4 py-3">
              <span class="text-sm font-semibold">Hanya yang urgent</span>
              <button type="button" id="swUrgent" class="switch press dk" role="switch" aria-checked="false" aria-label="Hanya yang urgent"></button>
            </div>

            <label class="mt-3 block">
              <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[.12em] text-zinc-500">Urutkan</span>
              <select id="sortSel" class="w-full cursor-pointer rounded-2xl border border-black/10 bg-white px-4 py-2.5 text-sm font-medium outline-none focus:border-ewforange">
                <option value="rek">Rekomendasi</option>
                <option value="gaji">Gaji tertinggi</option>
                <option value="posisi">Posisi terbanyak</option>
              </select>
            </label>

            <button type="button" id="btnReset" class="mt-4 w-full text-center text-xs font-semibold text-zinc-500 underline-offset-4 hover:text-ewforange hover:underline">Atur ulang filter</button>
          </div>

          <div class="mt-4 flex items-start gap-3 rounded-[1.5rem] border border-black/10 p-4 text-xs leading-relaxed text-zinc-600">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-ewforange text-ink">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>
            </span>
            <span><strong class="text-ink">Rekrutmen 100% gratis.</strong> Kami tidak pernah meminta biaya akomodasi, transportasi, atau biaya apa pun kepada kandidat.</span>
          </div>
        </aside>

        {{-- Daftar --}}
        <div>
          <p class="mb-4 text-sm text-zinc-500">Menampilkan <strong class="text-ink" id="jmlTampil">{{ count($daftarLowongan) }}</strong> dari {{ count($daftarLowongan) }} lowongan</p>

          <div class="grid gap-3" id="jobList">
            @foreach($daftarLowongan as $j)
              <article class="job rounded-[1.75rem] {{ $j['urgent'] ? 'bg-ewforange' : 'bg-sand' }}"
                       tabindex="0" role="button" aria-label="Lihat ringkasan {{ $j['judul'] }}"
                       data-id="{{ $j['id'] }}" data-idx="{{ $loop->index }}"
                       data-judul="{{ Str::lower($j['judul']) }}" data-desk="{{ Str::lower($j['deskripsi']) }}"
                       data-kat="{{ $j['kategori'] }}" data-urgent="{{ $j['urgent'] ? 1 : 0 }}"
                       data-gaji="{{ $j['gaji_max'] }}" data-posisi="{{ $j['posisi'] }}">
                <div class="flex items-center gap-4 p-4 sm:gap-6 sm:p-5">
                  <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl font-head text-xl font-medium sm:h-14 sm:w-14 {{ $j['urgent'] ? 'bg-ink text-white' : 'bg-ewforange text-ink' }}">{{ $j['huruf'] }}</div>

                  <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                      <h3 class="font-head text-lg font-medium tracking-tight sm:text-xl">{{ $j['judul'] }}</h3>
                      @if($j['urgent'])
                        <span class="rounded-full bg-ink px-2 py-0.5 text-[10px] font-bold tracking-wider text-ewforange">URGENT</span>
                      @endif
                    </div>
                    <p class="j-muted mt-1 line-clamp-1 text-sm {{ $j['urgent'] ? 'text-ink/70' : 'text-zinc-500' }}">{{ $j['deskripsi'] }}</p>
                    <div class="mt-2.5 flex flex-wrap gap-1.5 text-[11px] font-semibold">
                      <span class="j-chip rounded-full border border-ink/15 bg-white/70 px-2.5 py-1">{{ $j['kategori'] }}</span>
                      <span class="j-chip rounded-full border border-ink/15 bg-white/70 px-2.5 py-1">Surabaya · On-Site</span>
                      <span class="j-chip rounded-full border border-ink/15 bg-white/70 px-2.5 py-1">Full-Time</span>
                    </div>
                  </div>

                  <div class="hidden shrink-0 text-right sm:block">
                    <p class="font-head text-lg font-medium">{{ $j['gaji'] }}</p>
                    <p class="j-muted text-[11px] {{ $j['urgent'] ? 'text-ink/70' : 'text-zinc-500' }}">per bulan · {{ $j['posisi'] }} posisi</p>
                    <p class="j-muted mt-1 text-[11px] font-semibold {{ $j['urgent'] ? 'text-ink' : 'text-zinc-400' }}">{{ $j['status'] }}</p>
                  </div>

                  <span class="j-arrow grid h-10 w-10 shrink-0 place-items-center rounded-full bg-white text-ink">{!! $panah !!}</span>
                </div>
              </article>
            @endforeach
          </div>

          <div id="emptyState" class="hidden rounded-[1.75rem] border-2 border-dashed border-black/10 px-6 py-16 text-center">
            <p class="font-head text-2xl font-medium">Belum ada yang cocok.</p>
            <p class="mx-auto mt-2 max-w-xs text-sm text-zinc-500">Coba kata kunci lain atau atur ulang filter untuk melihat semua posisi.</p>
            <button type="button" id="btnReset2" class="press dk mt-6 rounded-full bg-ink px-6 py-2.5 text-sm font-semibold text-white">Atur ulang filter</button>
          </div>
        </div>
      </div>
    </section>

    {{-- ============ ALUR SELEKSI ============ --}}
    <section class="bg-sand py-24">
      <div class="mx-auto max-w-7xl px-6 md:px-10">
        <div class="rv mx-auto mb-14 max-w-2xl text-center">
          <p class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">Alur seleksi</p>
          <h2 class="mt-2 font-head text-3xl font-normal tracking-tight md:text-5xl">Dari daftar sampai <strong class="font-semibold text-ewforange">pengumuman.</strong></h2>
          <p class="mt-4 text-sm text-zinc-500">Estimasi 7 sampai 14 hari kerja sejak pendaftaran lowongan ditutup.</p>
        </div>

        <ol class="relative grid gap-10 md:grid-cols-4 md:gap-6" id="flow">
          <div class="absolute left-6 right-6 top-6 hidden h-0.5 bg-zinc-200 md:block"><span id="flowFill" class="block h-full bg-ewforange"></span></div>
          @foreach([
            ['Daftar & kirim CV',     'Buat akun lalu kirim CV secara online lewat portal ini.'],
            ['Seleksi administrasi',  'Tim HR menyaring berkas dan kecocokan profilmu.'],
            ['Tes & wawancara',       'Tes tertulis, lalu wawancara dengan HR dan divisi terkait (User).'],
            ['Pengumuman',            'Hasil final diumumkan lewat portal dan email resmi perusahaan.'],
          ] as $i => [$jdl, $isi])
            <li class="flow-step rv relative flex gap-5 md:block">
              <span class="dot relative z-10 grid h-12 w-12 shrink-0 place-items-center rounded-full border-2 border-zinc-300 bg-white font-head text-lg font-medium text-zinc-500">{{ $i + 1 }}</span>
              <div class="md:mt-5">
                <h3 class="font-head text-xl font-medium tracking-tight">{{ $jdl }}</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-zinc-500">{{ $isi }}</p>
              </div>
            </li>
          @endforeach
        </ol>
      </div>
    </section>

    {{-- ============ FITUR (kartu bertumpuk) ============ --}}
    <section class="mx-auto max-w-6xl px-6 py-24 md:px-10">
      <div class="rv mb-12 max-w-2xl">
        <p class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">Fitur pendukung</p>
        <h2 class="mt-2 font-head text-3xl font-normal tracking-tight md:text-5xl">Karier lebih mudah <strong class="font-semibold text-ewforange">dari satu akun.</strong></h2>
      </div>

      <div class="space-y-8 pb-6">
        {{-- 1 --}}
        <article class="stack-card sticky grid min-h-[24rem] gap-8 overflow-hidden rounded-[2rem] bg-ewforange p-8 md:grid-cols-2 md:p-12" style="top:5.5rem;transform-origin:50% 0">
          <div class="flex flex-col justify-between">
            <div>
              <span class="rounded-full bg-ink px-3 py-1 text-[10px] font-bold tracking-wider text-white">01</span>
              <h3 class="mt-5 font-head text-3xl font-medium leading-tight tracking-tight md:text-4xl">Pencocokan posisi berbasis AI &amp; keahlian</h3>
            </div>
            <p class="mt-6 max-w-sm text-sm leading-relaxed text-ink/75">Sistem cerdas membaca resume, riwayat studi, dan portofolio Anda untuk merekomendasikan lowongan dengan tingkat kecocokan tertinggi.</p>
          </div>
          <div class="grid place-items-center">
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
        </article>

        {{-- 2 --}}
        <article class="stack-card sticky grid min-h-[24rem] gap-8 overflow-hidden rounded-[2rem] bg-ink p-8 text-white md:grid-cols-2 md:p-12" style="top:6.5rem;transform-origin:50% 0">
          <div class="flex flex-col justify-between">
            <div>
              <span class="rounded-full bg-ewforange px-3 py-1 text-[10px] font-bold tracking-wider text-ink">02</span>
              <h3 class="mt-5 font-head text-3xl font-medium leading-tight tracking-tight md:text-4xl">Kelola dokumen praktis</h3>
            </div>
            <p class="mt-6 max-w-sm text-sm leading-relaxed text-white/65">Simpan portofolio, CV, dan berkas lamaran di satu akun untuk melamar ke banyak posisi tanpa ribet.</p>
          </div>
          <div class="flex items-center justify-center gap-4">
            @foreach([['DOC', '#93c5fd', '-6deg', '0s'], ['PDF', '#fca5a5', '3deg', '.6s'], ['IMG', '#86efac', '-2deg', '1.2s']] as [$tipe, $warna, $rot, $delay])
              <div class="grid h-28 w-20 place-items-center rounded-2xl text-sm font-bold text-ink shadow-xl" style="background:{{ $warna }};--r:{{ $rot }};animation: floaty 5s ease-in-out {{ $delay }} infinite">{{ $tipe }}</div>
            @endforeach
          </div>
        </article>

        {{-- 3 --}}
        <article class="stack-card sticky grid min-h-[24rem] gap-8 overflow-hidden rounded-[2rem] bg-sand p-8 md:grid-cols-2 md:p-12" style="top:7.5rem;transform-origin:50% 0">
          <div class="flex flex-col justify-between">
            <div>
              <span class="rounded-full bg-ewforange px-3 py-1 text-[10px] font-bold tracking-wider text-ink">03</span>
              <h3 class="mt-5 font-head text-3xl font-medium leading-tight tracking-tight md:text-4xl">Filter cerdas &amp; bebas bias</h3>
            </div>
            <p class="mt-6 max-w-sm text-sm leading-relaxed text-zinc-500">Penyaringan awal murni berdasarkan keahlian, bukan identitas. Kami fokus pada kualitas bakat untuk budaya kerja yang setara.</p>
          </div>
          <div class="flex flex-wrap content-center gap-3">
            @foreach(['Kemampuan Logika', 'Portofolio', 'Keahlian Teknis', 'Tanpa Batas Usia'] as $n => $tag)
              <span class="rounded-full border border-ink/10 bg-white px-4 py-2.5 text-sm font-semibold shadow-sm {{ $n === 3 ? 'text-rose-500' : '' }}" style="animation: floaty {{ 5 + $n }}s ease-in-out {{ $n * .4 }}s infinite">{{ $tag }}</span>
            @endforeach
          </div>
        </article>

        {{-- 4 --}}
        <article class="stack-card sticky grid min-h-[24rem] gap-8 overflow-hidden rounded-[2rem] border border-black/10 bg-white p-8 shadow-xl md:grid-cols-2 md:p-12" style="top:8.5rem;transform-origin:50% 0">
          <div class="flex flex-col justify-between">
            <div>
              <span class="rounded-full bg-ink px-3 py-1 text-[10px] font-bold tracking-wider text-white">04</span>
              <h3 class="mt-5 font-head text-3xl font-medium leading-tight tracking-tight md:text-4xl">Integrasi akun portofolio &amp; sertifikasi</h3>
            </div>
            <p class="mt-6 max-w-sm text-sm leading-relaxed text-zinc-500">Tautkan akun GitHub, LinkedIn, Dribbble, atau lampirkan lisensi WPPE/WAK/WMI langsung ke profil pelamar Anda.</p>
          </div>
          <div class="flex flex-col justify-center gap-4">
            <div class="flex gap-3">
              <span class="grid h-12 w-12 place-items-center rounded-2xl bg-ink text-xs font-bold text-white">GH</span>
              <span class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-100 text-xs font-bold text-blue-600">IN</span>
              <span class="grid h-12 w-12 place-items-center rounded-2xl bg-pink-100 text-xs font-bold text-pink-600">DR</span>
            </div>
            <div class="grid gap-2 sm:grid-cols-3">
              <div class="rounded-2xl bg-sand p-3"><p class="text-[10px] text-zinc-500">Status verifikasi</p><p class="mt-1 text-xs font-bold text-green-600">✓ Otomatis diterima</p></div>
              <div class="rounded-2xl bg-sand p-3"><p class="text-[10px] text-zinc-500">Sertifikasi</p><p class="mt-1 text-xs font-bold">Mitra terverifikasi</p></div>
              <div class="rounded-2xl bg-sand p-3"><p class="text-[10px] text-zinc-500">Perekrutan</p><p class="mt-1 text-xs font-bold">3x lebih cepat</p></div>
            </div>
          </div>
        </article>
      </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section class="mx-auto max-w-7xl px-6 pb-24 md:px-10">
      <div class="grid gap-12 lg:grid-cols-[1fr_1.4fr]">
        <div class="lg:sticky lg:top-28 lg:self-start">
          <div class="rv">
            <p class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">FAQ</p>
            <h2 class="mt-2 font-head text-3xl font-normal tracking-tight md:text-5xl">Pertanyaan yang <strong class="font-semibold text-ewforange">sering diajukan.</strong></h2>
            <p class="mt-4 text-sm text-zinc-500">Informasi seputar alur rekrutmen dan prosedur seleksi PT Equityworld Futures.</p>
          </div>

          <div class="rv mt-8 rounded-[1.75rem] bg-ink p-6 text-white">
            <p class="font-head text-xl font-medium">Punya pertanyaan khusus?</p>
            <p class="mt-1.5 text-xs leading-relaxed text-white/60">Tim Rekrutmen EWF siap membantu menanyakan status aplikasi atau informasi lainnya.</p>
            <div class="mt-5 flex flex-wrap gap-2">
              <a href="#" class="press rounded-full bg-ewforange px-5 py-2.5 text-xs font-semibold text-ink">WhatsApp</a>
              <a href="#" class="press dk rounded-full border border-white/25 px-5 py-2.5 text-xs font-semibold text-white">Kirim email</a>
            </div>
          </div>
        </div>

        <div class="rv divide-y divide-black/10 border-y border-black/10">
          @foreach([
            ['Bagaimana tahapan rekrutmen di kantor kami?', 'Proses dimulai dari pendaftaran CV secara online di portal ini. Jika lolos seleksi administrasi, Anda akan diundang untuk tes tertulis dan wawancara dengan HR dan divisi terkait (User). Hasil final diumumkan melalui portal ini dan email resmi perusahaan.'],
            ['Berapa lama estimasi proses seleksi hingga penawaran kerja?', 'Standar proses seleksi kami memakan waktu 7 hingga 14 hari kerja terhitung dari tanggal penutupan pendaftaran lowongan.'],
            ['Apakah mahasiswa akhir atau fresh graduate dapat melamar?', 'Tentu. Lowongan kerja kami terbuka untuk fresh graduate hingga profesional berpengalaman sesuai kualifikasi pada deskripsi pekerjaan.'],
            ['Apakah seluruh proses rekrutmen ini dipungut biaya?', 'Tidak. Seluruh proses rekrutmen PT Equityworld Futures bersifat 100% gratis. Kami tidak pernah meminta biaya akomodasi, transportasi, atau biaya apa pun kepada kandidat.'],
          ] as $i => [$tanya, $jawab])
            <div>
              <button type="button" class="faq-btn flex w-full items-center gap-4 py-5 text-left {{ $i === 0 ? 'active' : '' }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                <span class="font-head text-sm font-medium text-ewforange">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="flex-1 text-[15px] font-semibold">{{ $tanya }}</span>
                <span class="plus grid h-8 w-8 shrink-0 place-items-center rounded-full bg-sand text-lg leading-none">+</span>
              </button>
              <div class="faq-content {{ $i === 0 ? 'open' : '' }}">
                <div class="faq-inner"><p class="pb-6 pl-10 pr-12 text-sm leading-relaxed text-zinc-500">{{ $jawab }}</p></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="mx-auto max-w-7xl px-6 pb-24 md:px-10">
      <div class="rv relative overflow-hidden rounded-[2rem] bg-ewforange py-16 text-center">
        <div class="mq pointer-events-none absolute inset-x-0 top-5 overflow-hidden" aria-hidden="true">
          <div class="mq-track">
            @for($n = 0; $n < 8; $n++)
              @foreach($daftarLowongan as $j)
                <span class="mx-6 whitespace-nowrap font-head text-5xl font-light uppercase tracking-tight text-ink/10 md:text-7xl">{{ $j['judul'] }} ✦</span>
              @endforeach
            @endfor
          </div>
        </div>

        <div class="relative px-6 pt-16">
          <h2 class="mx-auto max-w-2xl font-head text-3xl font-light leading-tight tracking-tight md:text-5xl">Siap mengambil langkah baru <strong class="font-semibold">dalam karier Anda?</strong></h2>
          <p class="mx-auto mt-4 max-w-md text-sm text-ink/70">Bergabunglah bersama talenta berbakat yang mewujudkan impian karier profesional di PT Equityworld Futures.</p>
          <div class="mt-9 flex flex-wrap justify-center gap-3">
            <a href="{{ route('register') }}" class="press dk rounded-full bg-ink px-8 py-3.5 text-sm font-semibold text-white">Daftar Akun</a>
            <a href="{{ route('program.index') }}" class="press dk rounded-full border border-ink/25 bg-white/40 px-8 py-3.5 text-sm font-semibold text-ink">Lihat Program Magang</a>
          </div>
        </div>
      </div>
    </section>
  </div>
</main>

<<<<<<< HEAD
@include('partials.footer')
=======
{{-- ============ DRAWER RINGKASAN LOWONGAN ============ --}}
<div id="drawer" class="fixed inset-0 z-[70]" aria-hidden="true">
  <div id="drawerBg" class="absolute inset-0 bg-ink/50 backdrop-blur-sm"></div>
  <aside id="drawerPanel" class="absolute right-0 top-0 flex h-full w-full max-w-md flex-col bg-white shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="dwJudul">
    <div class="flex items-center justify-between px-6 py-5">
      <span class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">Ringkasan lowongan</span>
      <button type="button" id="dwClose" class="press dk grid h-10 w-10 place-items-center rounded-full bg-sand" aria-label="Tutup">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </div>
>>>>>>> vina

    <div class="flex-1 overflow-y-auto px-6 pb-6">
      <div class="dw-i flex items-center gap-4">
        <div id="dwHuruf" class="grid h-16 w-16 place-items-center rounded-2xl bg-ewforange font-head text-2xl font-medium text-ink"></div>
        <div>
          <span id="dwUrgent" class="mb-1 hidden rounded-full bg-ink px-2 py-0.5 text-[10px] font-bold tracking-wider text-ewforange">URGENT</span>
          <h3 id="dwJudul" class="font-head text-2xl font-medium tracking-tight"></h3>
          <p id="dwKat" class="text-xs font-semibold text-zinc-500"></p>
        </div>
      </div>

      <p id="dwDesk" class="dw-i mt-6 text-sm leading-relaxed text-zinc-600"></p>

      <dl class="dw-i mt-6 grid grid-cols-2 gap-3 text-sm">
        <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Gaji</dt><dd id="dwGaji" class="mt-1 font-head text-lg font-medium"></dd></div>
        <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Posisi tersedia</dt><dd id="dwPosisi" class="mt-1 font-head text-lg font-medium"></dd></div>
        <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Lokasi</dt><dd class="mt-1 font-semibold">Surabaya (On-Site)</dd></div>
        <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Tipe kerja</dt><dd class="mt-1 font-semibold">Full-Time</dd></div>
      </dl>

      <p id="dwStatus" class="dw-i mt-4 rounded-2xl border border-black/10 px-4 py-3 text-xs font-semibold text-zinc-600"></p>
      <p class="dw-i mt-4 text-xs leading-relaxed text-zinc-500">Proses rekrutmen 100% gratis. Kami tidak pernah meminta biaya apa pun kepada kandidat.</p>
    </div>

    <div class="border-t border-black/10 p-6">
      <a id="dwLamar" href="#" class="press flex items-center justify-center gap-2 rounded-full bg-ewforange px-8 py-3.5 text-sm font-semibold text-ink">Lihat Detail &amp; Lamar {!! $panah !!}</a>
    </div>
  </aside>
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

  const JOBS   = @json($daftarLowongan);
  const LAMAR  = @json(route('lowongan.show', ['id' => '__ID__']));

  /* ===== Filter, cari, urutkan ===== */
  const list = $('#jobList'), rows = $$('.job', list), empty = $('#emptyState'), jml = $('#jmlTampil');
  const state = { q: '', kat: 'semua', urgent: false, sort: 'rek' };
  let shown = rows.length;

  function apply(animate = true) {
    const q = state.q.trim().toLowerCase();
    const vis = rows.filter(r =>
      (!q || r.dataset.judul.includes(q) || r.dataset.desk.includes(q)) &&
      (state.kat === 'semua' || r.dataset.kat === state.kat) &&
      (!state.urgent || r.dataset.urgent === '1'));
    const val = { rek: r => +r.dataset.idx, gaji: r => -r.dataset.gaji, posisi: r => -r.dataset.posisi }[state.sort];
    vis.sort((a, b) => (val(a) - val(b)) || (a.dataset.idx - b.dataset.idx));

    rows.forEach(r => r.classList.toggle('hidden', !vis.includes(r)));
    vis.forEach(r => list.appendChild(r));
    empty.classList.toggle('hidden', vis.length > 0);

    if (hasGsap && animate) {
      gsap.killTweensOf(vis);
      gsap.fromTo(vis, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: .5, stagger: .06, ease: 'power3.out' });
      const o = { v: shown };
      gsap.to(o, { v: vis.length, duration: .5, onUpdate: () => jml.textContent = Math.round(o.v) });
    } else {
      vis.forEach(r => r.style.opacity = 1);
      jml.textContent = vis.length;
    }
    shown = vis.length;
  }

  $$('.q-input').forEach(inp => inp.addEventListener('input', () => {
    state.q = inp.value;
    $$('.q-input').forEach(o => { if (o !== inp) o.value = inp.value; });
    apply();
  }));
  $('#btnCari').addEventListener('click', () => { $('#daftar').scrollIntoView({ behavior: 'smooth' }); apply(); });

  $$('#katList .chip').forEach(b => b.addEventListener('click', () => {
    $$('#katList .chip').forEach(x => x.classList.toggle('on', x === b));
    state.kat = b.dataset.kat; apply();
  }));
  const sw = $('#swUrgent');
  sw.addEventListener('click', () => { state.urgent = !state.urgent; sw.setAttribute('aria-checked', state.urgent); apply(); });
  $('#sortSel').addEventListener('change', e => { state.sort = e.target.value; apply(); });

  const reset = () => {
    Object.assign(state, { q: '', kat: 'semua', urgent: false, sort: 'rek' });
    $$('.q-input').forEach(i => i.value = '');
    $$('#katList .chip').forEach(x => x.classList.toggle('on', x.dataset.kat === 'semua'));
    sw.setAttribute('aria-checked', 'false'); $('#sortSel').value = 'rek'; apply();
  };
  $('#btnReset').addEventListener('click', reset);
  $('#btnReset2').addEventListener('click', reset);

  /* ===== Drawer ringkasan ===== */
  const drawer = $('#drawer'); let lastTrigger = null;
  function openDrawer(id, trigger) {
    const j = JOBS.find(x => x.id == id); if (!j) return;
    lastTrigger = trigger;
    $('#dwHuruf').textContent = j.huruf;
    $('#dwJudul').textContent = j.judul;
    $('#dwKat').textContent   = j.kategori;
    $('#dwDesk').textContent  = j.deskripsi;
    $('#dwGaji').textContent  = j.gaji + ' / bulan';
    $('#dwPosisi').textContent = j.posisi + ' posisi';
    $('#dwStatus').textContent = j.status;
    $('#dwUrgent').classList.toggle('hidden', !j.urgent);
    $('#dwLamar').href = LAMAR.replace('__ID__', j.id);
    drawer.classList.add('open'); drawer.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    if (hasGsap) gsap.fromTo($$('.dw-i'), { opacity: 0, y: 18 }, { opacity: 1, y: 0, duration: .5, stagger: .07, delay: .15, ease: 'power3.out' });
    setTimeout(() => $('#dwClose').focus(), 300);
  }
  function closeDrawer() {
    drawer.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (lastTrigger) lastTrigger.focus({ preventScroll: true });
  }
  rows.forEach(r => {
    r.addEventListener('click', () => openDrawer(r.dataset.id, r));
    r.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openDrawer(r.dataset.id, r); } });
  });
  $('#dwClose').addEventListener('click', closeDrawer);
  $('#drawerBg').addEventListener('click', closeDrawer);
  addEventListener('keydown', e => { if (e.key === 'Escape' && drawer.classList.contains('open')) closeDrawer(); });

  /* ===== FAQ ===== */
  $$('.faq-btn').forEach(btn => btn.addEventListener('click', () => {
    const content = btn.parentElement.querySelector('.faq-content');
    const open = content.classList.contains('open');
    $$('.faq-content').forEach(c => c.classList.remove('open'));
    $$('.faq-btn').forEach(b => { b.classList.remove('active'); b.setAttribute('aria-expanded', 'false'); });
    if (!open) { content.classList.add('open'); btn.classList.add('active'); btn.setAttribute('aria-expanded', 'true'); }
  }));

  /* ===== Animasi ===== */
  if (!hasGsap) { root.classList.remove('js'); rows.forEach(r => r.style.opacity = 1); return; }
  gsap.registerPlugin(ScrollTrigger);

  // Hero
  gsap.fromTo('.cover-bg', { scale: 1.08 }, { scale: 1, duration: 2.2, ease: 'power2.out' });
  gsap.from('#heroText > *', { y: 46, opacity: 0, duration: 1, stagger: .12, ease: 'power3.out', delay: .15 });

  // Reveal umum
  gsap.set('.rv', { y: 30 });
  ScrollTrigger.batch('.rv', {
    start: 'top 90%', once: true,
    onEnter: els => gsap.to(els, { opacity: 1, y: 0, duration: .85, stagger: .09, ease: 'power3.out' })
  });

  // Baris lowongan muncul bergantian
  gsap.set(rows, { y: 34 });
  ScrollTrigger.create({
    trigger: list, start: 'top 85%', once: true,
    onEnter: () => gsap.to(rows.filter(r => !r.classList.contains('hidden')), { opacity: 1, y: 0, duration: .8, stagger: .1, ease: 'power3.out' })
  });

  // Alur seleksi: garis terisi, langkah menyala
  gsap.fromTo('#flowFill', { scaleX: 0 }, { scaleX: 1, ease: 'none', scrollTrigger: { trigger: '#flow', start: 'top 75%', end: 'bottom 60%', scrub: .4 } });
  $$('.flow-step').forEach(s => ScrollTrigger.create({
    trigger: s, start: 'top 72%',
    onEnter: () => s.classList.add('on'), onLeaveBack: () => s.classList.remove('on')
  }));

  // Kartu fitur bertumpuk: kartu di belakang mengecil saat kartu berikutnya menimpa
  const cards = $$('.stack-card');
  cards.forEach((c, i) => {
    if (i === cards.length - 1) return;
    gsap.to(c, { scale: .94, ease: 'none', scrollTrigger: { trigger: cards[i + 1], start: 'top 85%', end: 'top 25%', scrub: true } });
  });
})();
</script>
</body>
</html>