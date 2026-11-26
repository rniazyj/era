@php
  $has = fn ($name) => \Illuminate\Support\Facades\Route::has($name);

  $createKerja  = $has('admin.lowongan.create') ? route('admin.lowongan.create', ['jenis' => 'kerja']) : '#';
  $createMagang = $has('admin.lowongan.create') ? route('admin.lowongan.create', ['jenis' => 'magang']) : '#';
  $editUrl   = fn ($row) => $has('admin.lowongan.edit')
      ? route('admin.lowongan.edit', ['jenis' => strtolower($row->jenis), 'id' => $row->id])
      : '#';

  // Data dari AdminLowonganController@index (gabungan lowongan_kerja + program_magang)
  $rows = collect($items ?? [])->map(fn ($r) => (object) $r);

  $totalLowongan = $rows->where('jenis', 'Kerja')->count();
  $totalProgram  = $rows->where('jenis', 'Magang')->count();
  $totalDibuka   = $rows->where('status', 'dibuka')->count();
  $totalLain     = $rows->count() - $totalDibuka;

  $fmt   = fn ($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d M Y') : '-';
  $today = now()->toDateString();

  $stats = [
    ['warna' => 'orange', 'label' => 'Total Lowongan', 'angka' => $totalLowongan, 'ket' => 'Lowongan kerja', 'badge' => 'Kerja',
     'icon' => '<rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M3 12h18M10 12v2h4v-2"/>'],
    ['warna' => 'gray', 'label' => 'Total Program', 'angka' => $totalProgram, 'ket' => 'Program magang', 'badge' => 'Magang',
     'icon' => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3.5 3 8.5 3 12 0v-5"/>'],
    ['warna' => 'orange', 'label' => 'Sedang Dibuka', 'angka' => $totalDibuka, 'ket' => 'Status aktif', 'badge' => 'Dibuka',
     'icon' => '<circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>'],
    ['warna' => 'gray', 'label' => 'Draft / Ditutup', 'angka' => $totalLain, 'ket' => 'Status lainnya', 'badge' => 'Draft / Ditutup',
     'icon' => '<rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/>'],
  ];

  // Data ringkas untuk filter di browser
  $dataFilter = $rows->map(fn ($r) => [
    'jenis'  => $r->jenis,
    'status' => $r->status,
    'text'   => strtolower($r->judul),
  ])->values();
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Kelola Lowongan - Galeri Magang</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Caveat:wght@500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
      colors: { paper:'#faf9fc', lime:'#e9f56b', brand:'#34d3b0', ewforange:'#f97316' } } } };
  </script>
  <style>
    [x-cloak] { display: none !important; }
    .scroll-rv { opacity: 0; transform: translateY(40px); }
    .table-row-hover { transition: background-color .25s ease; }
    .table-row-hover:hover { background-color: rgba(249, 115, 22, 0.04); }
    .field { width: 100%; border-radius: 0.75rem; border: 1px solid rgba(0,0,0,.1); background: #fff; padding: .6rem .9rem; font-size: .8rem; outline: none; transition: all .2s; }
    .field:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); }

    /* ---------- kartu statistik ---------- */
    .stat-card { position: relative; isolation: isolate; overflow: hidden; transition: box-shadow .35s ease; will-change: transform; }
    .stat-card-orange { background: linear-gradient(125deg, #f97316, #fb923c, #ea580c, #f97316); background-size: 300% 300%; animation: orangeGradient 10s ease infinite; }
    .stat-card-gray   { background: linear-gradient(125deg, #52525b, #71717a, #3f3f46, #52525b); background-size: 300% 300%; animation: grayGradient 10s ease infinite; }
    @keyframes orangeGradient { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
    @keyframes grayGradient   { 0%, 100% { background-position: 100% 50%; } 50% { background-position: 0% 50%; } }
    .stat-decoration { position: absolute; z-index: 0; border-radius: 50%; pointer-events: none; will-change: transform; }
    .stat-decoration-one { width: 190px; height: 190px; top: -65px; right: -55px; background: rgba(255,255,255,.30); filter: blur(12px); animation: circleMoveOne 12s ease-in-out infinite alternate; }
    .stat-decoration-two { width: 140px; height: 140px; bottom: -55px; right: 35px; background: rgba(255,255,255,.20); filter: blur(16px); animation: circleMoveTwo 15s ease-in-out infinite alternate; }
    @keyframes circleMoveOne { 0% { transform: translate(0,0) scale(1); } 25% { transform: translate(-90px,55px) scale(1.1); } 50% { transform: translate(-150px,125px) scale(.95); } 75% { transform: translate(-60px,180px) scale(1.15); } 100% { transform: translate(-180px,220px) scale(1); } }
    @keyframes circleMoveTwo { 0% { transform: translate(0,0) scale(1); } 25% { transform: translate(-100px,-80px) scale(1.15); } 50% { transform: translate(-180px,-160px) scale(.9); } 75% { transform: translate(-70px,-220px) scale(1.1); } 100% { transform: translate(-200px,-260px) scale(1); } }
    .stat-card-inner { position: relative; z-index: 1; }
    .stat-icon { width: 48px; height: 48px; flex-shrink: 0; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #fff; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.20); }
    .stat-card:hover { box-shadow: 0 18px 35px rgba(39,39,42,.16); }
    @media (prefers-reduced-motion: reduce) {
      .stat-card-orange, .stat-card-gray, .stat-decoration-one, .stat-decoration-two { animation: none; }
      .stat-card { transition: none; }
    }

    /* ---------- tab filter ---------- */
    .filter-tabs { position: relative; display: inline-flex; align-items: center; gap: 4px; padding: 4px; background: #000; border: 1px solid rgba(0,0,0,.025); border-radius: 9999px; isolation: isolate; }
    .filter-tab-indicator { position: absolute; z-index: 0; top: 4px; left: 4px; height: calc(100% - 8px); width: 0; border-radius: 9999px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04); pointer-events: none; will-change: transform, width; }
    .filter-tab { position: relative; z-index: 1; display: inline-flex; align-items: center; justify-content: center; padding: 9px 18px; border-radius: 9999px; font-size: 11px; font-weight: 600; color: #71717a; white-space: nowrap; cursor: pointer; transition: color .25s ease; }
    .filter-tab.active { color: #18181b; }
    .filter-tab:not(.active):hover { color: #fff; }
    .filter-tab:active { transform: scale(.97); }
    @media (prefers-reduced-motion: reduce) { .filter-tab { transition: none; } }
  </style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

  <div class="flex min-h-screen flex-col lg:flex-row">

    @include('partials.sidebar')

    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-20 sm:pb-24 space-y-6 sm:space-y-8 lg:space-y-10">

      <div class="w-full max-w-6xl mx-auto space-y-6 sm:space-y-8 lg:space-y-10 min-w-0"
           x-data="lowonganIndex(@js($dataFilter))">

        <!-- HEADER HALAMAN -->
        <div class="scroll-rv relative z-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 min-w-0">
          <div>
            <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">PANEL ADMINISTRATOR</span>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2 break-words">Kelola Lowongan</h1>
            <p class="text-xs text-zinc-500 mt-1">Lowongan kerja dan program magang dalam satu tempat.</p>
          </div>

          <!-- Tombol tambah dengan pilihan jenis -->
          <div class="relative z-[100] self-start sm:self-auto" @click.outside="addOpen = false">
            <button type="button" @click="addOpen = !addOpen"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-ewforange hover:bg-orange-600 text-white text-xs font-semibold rounded-full shadow-lg shadow-orange-500/30 transition hover:scale-105">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
              Tambah Baru
              <svg class="w-3 h-3 transition" :class="addOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="addOpen" x-cloak x-transition.origin.top.right
                 class="absolute right-0 mt-2 w-64 rounded-2xl bg-white border border-black/10 shadow-xl p-2 z-[110]">
              <a href="{{ $createMagang }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-orange-50 transition">
                <span class="text-lg">🎓</span>
                <span><span class="block text-xs font-bold">Program Magang</span><span class="block text-[11px] text-zinc-500">Dengan silabus &amp; periode magang</span></span>
              </a>
              <a href="{{ $createKerja }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-orange-50 transition">
                <span class="text-lg">💼</span>
                <span><span class="block text-xs font-bold">Lowongan Kerja</span><span class="block text-[11px] text-zinc-500">Dengan kualifikasi pelamar</span></span>
              </a>
            </div>
          </div>
        </div>

        @if(session('success'))
          <div class="scroll-rv rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-medium px-5 py-3">✓ {{ session('success') }}</div>
        @endif
        @if(session('warning'))
          <div class="scroll-rv rounded-2xl bg-amber-50 border border-amber-100 text-amber-700 text-xs font-medium px-5 py-3">⚠ {{ session('warning') }}</div>
        @endif
        @if(session('error'))
          <div class="scroll-rv rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xs font-medium px-5 py-3">{{ session('error') }}</div>
        @endif

        <!-- KARTU STATISTIK -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 scroll-rv min-w-0">
          @foreach($stats as $s)
            <div class="stat-card stat-card-{{ $s['warna'] }} rounded-3xl p-5 sm:p-6 shadow-sm text-white min-w-0">
              <div class="stat-decoration stat-decoration-one"></div>
              <div class="stat-decoration stat-decoration-two"></div>

              <div class="stat-card-inner flex flex-col gap-5">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="stat-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $s['icon'] !!}</svg>
                  </div>
                  <span class="text-xs font-semibold text-white/90">{{ $s['label'] }}</span>
                </div>

                <h3 class="text-4xl font-bold tracking-tight count-target" data-target="{{ $s['angka'] }}">0</h3>

                <div class="flex items-center justify-between gap-2 pt-4 border-t border-white/20">
                  <span class="text-xs font-medium text-white/90">{{ $s['ket'] }}</span>
                  <span class="px-3 py-1.5 rounded-full bg-white {{ $s['warna'] === 'orange' ? 'text-orange-600' : 'text-zinc-700' }} text-[10px] font-bold">{{ $s['badge'] }}</span>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <!-- DAFTAR LOWONGAN -->
        <div class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm space-y-5 min-w-0">

          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 pb-4 border-b border-black/5 min-w-0">
            <div>
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">Daftar Lowongan</h3>
              <p class="text-[11px] text-zinc-400 mt-0.5">Klik <b>Edit</b> pada baris untuk mengubah data.</p>
            </div>
            <span class="text-xs bg-orange-50 text-ewforange font-bold px-3 py-1 rounded-full self-start sm:self-auto">
              <span x-text="visibleCount"></span> / {{ $rows->count() }} Data
            </span>
          </div>

          <!-- FILTER & PENCARIAN -->
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div class="filter-tabs self-start" x-ref="track" x-init="$nextTick(() => moveTab('Semua', false))">
              <div class="filter-tab-indicator" x-ref="indicator"></div>
              <button type="button" x-ref="tabSemua"  @click="moveTab('Semua')"  :class="{ 'active': filter === 'Semua' }"  class="filter-tab">Semua</button>
              <button type="button" x-ref="tabMagang" @click="moveTab('Magang')" :class="{ 'active': filter === 'Magang' }" class="filter-tab">Magang</button>
              <button type="button" x-ref="tabKerja"  @click="moveTab('Kerja')"  :class="{ 'active': filter === 'Kerja' }"  class="filter-tab">Kerja</button>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
              <select x-model="status" class="field sm:w-44 !rounded-full">
                <option value="Semua">Semua status</option>
                @foreach($statusList as $st)
                  <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                @endforeach
              </select>

              <div class="relative w-full sm:w-72">
                <svg class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
                <input type="text" x-model="q" placeholder="Cari judul lowongan..." class="field !pl-10 !rounded-full">
              </div>
            </div>
          </div>

          <div class="w-full min-w-0 overflow-x-auto overscroll-x-contain">
            <table class="w-full min-w-[880px] text-left border-collapse">
              <thead>
                <tr class="border-b border-black/5 text-[11px] font-semibold text-zinc-400 uppercase">
                  <th class="py-3 px-4">Judul</th>
                  <th class="py-3 px-4">Pendaftaran</th>
                  <th class="py-3 px-4">Pelaksanaan</th>
                  <th class="py-3 px-4 text-center">Kuota</th>
                  <th class="py-3 px-4 text-center">Pelamar</th>
                  <th class="py-3 px-4">Status</th>
                  <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black/5 text-xs">
                @foreach($rows as $row)
                  @php
                    $statusClass = match ($row->status) {
                      'dibuka'  => ['bg-emerald-50 text-emerald-600', 'bg-emerald-500'],
                      'ditutup' => ['bg-zinc-100 text-zinc-500', 'bg-zinc-400'],
                      default   => ['bg-amber-50 text-amber-600', 'bg-amber-500'],
                    };
                    $lewat = $row->status === 'dibuka' && $row->tgl_tutup && $row->tgl_tutup < $today;
                  @endphp
                  <tr class="table-row-item table-row-hover" x-show="visible({{ $loop->index }})">
                    <td class="py-4 px-4 max-w-xs">
                      <p class="font-bold text-zinc-900 break-words">{{ $row->judul }}</p>
                      <span class="inline-block mt-1.5 px-2.5 py-0.5 rounded-md font-semibold text-[10px] {{ $row->jenis == 'Magang' ? 'bg-orange-50 text-ewforange' : 'bg-zinc-100 text-zinc-800' }}">
                        {{ $row->jenis == 'Magang' ? '🎓 Magang' : '💼 Kerja' }}
                      </span>
                    </td>
                    <td class="py-4 px-4 text-zinc-600 whitespace-nowrap">
                      {{ $fmt($row->tgl_buka) }}<br>
                      <span class="text-zinc-400">s/d {{ $fmt($row->tgl_tutup) }}</span>
                    </td>
                    <td class="py-4 px-4 text-zinc-600 whitespace-nowrap">
                      @if($row->jenis == 'Magang' && $row->tgl_mulai)
                        {{ $fmt($row->tgl_mulai) }}<br>
                        <span class="text-zinc-400">s/d {{ $fmt($row->tgl_selesai) }}
                          @if($row->durasi_hari) · {{ $row->durasi_hari }} hari @endif
                        </span>
                      @else
                        <span class="text-zinc-300">-</span>
                      @endif
                    </td>
                    <td class="py-4 px-4 text-center">
    <span class="font-semibold text-zinc-700">
        {{ $row->sisa_kuota }}
    </span>
    <span class="text-zinc-400">
        / {{ $row->kuota }}
    </span>
</td>
                    <td class="py-4 px-4 text-center">
                      <span class="inline-block min-w-[2rem] px-2 py-1 rounded-full text-[11px] font-bold {{ $row->jumlah_pelamar > 0 ? 'bg-orange-50 text-ewforange' : 'bg-zinc-100 text-zinc-400' }}">
                        {{ $row->jumlah_pelamar }}
                      </span>
                    </td>
                    <td class="py-4 px-4">
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold text-[10px] capitalize {{ $statusClass[0] }}">
                        <i class="w-1.5 h-1.5 rounded-full {{ $statusClass[1] }}"></i>
                        {{ $row->status }}
                      </span>
                      @if($lewat)
                        <p class="mt-1.5 text-[10px] font-semibold text-amber-600">⚠ Lewat tanggal tutup</p>
                      @endif
                    </td>
                    <td class="py-4 px-4 text-center">
                      <a href="{{ $editUrl($row) }}"
                         class="inline-flex items-center gap-1.5 px-4 py-2 bg-zinc-900 hover:bg-black text-white text-[11px] font-medium rounded-full transition shadow-sm hover:scale-105">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            @if($rows->isEmpty())
              <div class="py-14 text-center text-zinc-400 text-sm">
                <p class="text-3xl mb-2">📭</p>
                Belum ada lowongan.<br>
                Klik <span class="font-semibold text-ewforange">Tambah Baru</span> untuk membuat yang pertama.
              </div>
            @else
              <div x-show="visibleCount === 0" x-cloak class="py-14 text-center text-zinc-400 text-sm">
                <p class="text-3xl mb-2">🔍</p>
                Tidak ada data yang cocok dengan filter atau pencarian.
                <button type="button" @click="reset()" class="block mx-auto mt-3 text-xs font-semibold text-ewforange hover:underline">Reset filter</button>
              </div>
            @endif
          </div>
        </div>

      </div>
    </main>
  </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('lowonganIndex', (rows) => ({
        rows: rows,
        filter: 'Semua',
        status: 'Semua',
        q: '',
        tabReady: false,
        addOpen: false,

        visible(i) {
          const r = this.rows[i];
          if (!r) return false;
          return (this.filter === 'Semua' || this.filter === r.jenis)
              && (this.status === 'Semua' || this.status === r.status)
              && r.text.includes(this.q.toLowerCase().trim());
        },

        get visibleCount() {
          return this.rows.filter((_, i) => this.visible(i)).length;
        },

        reset() {
          this.q = '';
          this.status = 'Semua';
          this.moveTab('Semua');
        },

        moveTab(tab, animate = true) {
          const track = this.$refs.track;
          const indicator = this.$refs.indicator;
          const button = this.$refs['tab' + tab];
          if (!track || !indicator || !button) return;

          this.filter = tab;

          const targetX = button.getBoundingClientRect().left - track.getBoundingClientRect().left - 4;
          const targetWidth = button.getBoundingClientRect().width;

          if (typeof gsap === 'undefined') {
            indicator.style.transform = 'translateX(' + targetX + 'px)';
            indicator.style.width = targetWidth + 'px';
            this.tabReady = true;
            return;
          }

          if (!this.tabReady || !animate) {
            gsap.set(indicator, { x: targetX, width: targetWidth });
            this.tabReady = true;
            return;
          }

          const currentX = Number(gsap.getProperty(indicator, 'x')) || 0;
          const currentWidth = Number(gsap.getProperty(indicator, 'width')) || targetWidth;
          const movingRight = targetX > currentX;
          const stretchWidth = movingRight ? targetX - currentX + targetWidth : currentX - targetX + currentWidth;
          const stretchX = movingRight ? currentX : targetX;

          gsap.killTweensOf(indicator);
          gsap.timeline()
            .to(indicator, { x: stretchX, width: stretchWidth, duration: 0.35, ease: 'power2.inOut' })
            .to(indicator, { x: targetX, width: targetWidth, duration: 0.35, ease: 'power3.out' });
        },
      }));
    });
  </script>

  <!-- GSAP & ScrollTrigger -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
  <script>
    gsap.registerPlugin(ScrollTrigger);

    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    window.scrollTo(0, 0);

    const $ = (s, c = document) => [...c.querySelectorAll(s)];

    // Animasi masuk isi kartu statistik
    gsap.fromTo('.stat-card-inner', { y: 25, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.8, stagger: 0.15, ease: 'power3.out', delay: 0.2 });

    // Animasi section saat scroll
    $('.scroll-rv').forEach((section) => {
      gsap.fromTo(section, { y: 50, opacity: 0 },
        { y: 0, opacity: 1, duration: 1, ease: 'power3.out',
          scrollTrigger: { trigger: section, start: 'top 85%', toggleActions: 'play none none none' } });
    });

    // Count-up statistik
    $('.count-target').forEach((counter) => {
      const target = parseInt(counter.dataset.target) || 0;
      const obj = { val: 0 };
      gsap.to(obj, {
        val: target, duration: 1.5, ease: 'power1.out',
        scrollTrigger: { trigger: counter, start: 'top 85%', toggleActions: 'play none none none' },
        onUpdate: () => { counter.textContent = Math.round(obj.val); }
      });
    });

    // Hover kartu statistik
    $('.stat-card').forEach((card) => {
      card.addEventListener('pointerenter', () => gsap.to(card, { y: -6, scale: 1.02, duration: 0.3, ease: 'power2.out', overwrite: 'auto' }));
      card.addEventListener('pointerleave', () => gsap.to(card, { y: 0, scale: 1, duration: 0.4, ease: 'power2.out', overwrite: 'auto' }));
    });
  </script>
</body>
</html>