@php
  $has = fn ($name) => \Illuminate\Support\Facades\Route::has($name);

  $detailUrl = fn ($row) => $has('admin.pelamar.show') ? route('admin.pelamar.show', $row->id) : '#';
  $statusUrl = fn ($row) => $has('admin.pelamar.status') ? route('admin.pelamar.status', $row->id) : '#';

  $statusList = $statusList ?? ['diajukan', 'diproses', 'diterima', 'ditolak'];

  // Data dari SeleksiController@index
  $rows = collect($items ?? [])->map(fn ($r) => (object) $r);

  $total    = $rows->count();
  $jumlah   = fn ($st) => $rows->where('status', $st)->count();

  $fmt = fn ($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d M Y') : '-';

  // Data ringkas untuk filter di browser
  $dataFilter = $rows->map(fn ($r) => [
      'status' => $r->status,
      'jenis'  => $r->jenis,
      't'      => strtolower($r->nama . ' ' . $r->posisi . ' ' . $r->email),
  ])->values();
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Daftar Pelamar - ERA</title>
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
    .table-row-item { opacity: 0; transform: translateY(20px); }
    .table-row-hover { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
    .table-row-hover:hover { transform: translateY(-2px); background-color: rgba(249, 115, 22, 0.03); }
    .field { width: 100%; border-radius: 0.75rem; border: 1px solid rgba(0,0,0,.1); background: #fff; padding: .6rem .9rem; font-size: .8rem; outline: none; transition: all .2s; }
    .field:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); }

    /* CARD STATISTIK */
.stat-card {
  position: relative;
  isolation: isolate;
  overflow: hidden;
  transition: box-shadow 0.35s ease;
  will-change: transform;
}

/* Orange */
.stat-card-orange {
  background: linear-gradient(
    125deg,
    #f97316,
    #fb923c,
    #ea580c,
    #f97316
  );
  background-size: 300% 300%;
  animation: orangeGradient 10s ease infinite;
}

/* Abu-abu */
.stat-card-gray {
  background: linear-gradient(
    125deg,
    #52525b,
    #71717a,
    #3f3f46,
    #52525b
  );
  background-size: 300% 300%;
  animation: grayGradient 10s ease infinite;
}

@keyframes orangeGradient {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

@keyframes grayGradient {
  0%, 100% { background-position: 100% 50%; }
  50% { background-position: 0% 50%; }
}

/* Lingkaran blur */
.stat-decoration {
  position: absolute;
  z-index: 0;
  border-radius: 50%;
  pointer-events: none;
  will-change: transform;
}

.stat-decoration-one {
  width: 190px;
  height: 190px;
  top: -65px;
  right: -55px;
  background: rgba(255, 255, 255, 0.30);
  filter: blur(12px);
  animation: circleMoveOne 12s ease-in-out infinite alternate;
}

.stat-decoration-two {
  width: 140px;
  height: 140px;
  bottom: -55px;
  right: 35px;
  background: rgba(255, 255, 255, 0.20);
  filter: blur(16px);
  animation: circleMoveTwo 15s ease-in-out infinite alternate;
}

@keyframes circleMoveOne {
  0%   { transform: translate(0, 0) scale(1); }
  25%  { transform: translate(-90px, 55px) scale(1.1); }
  50%  { transform: translate(-150px, 125px) scale(.95); }
  75%  { transform: translate(-60px, 180px) scale(1.15); }
  100% { transform: translate(-180px, 220px) scale(1); }
}

@keyframes circleMoveTwo {
  0%   { transform: translate(0, 0) scale(1); }
  25%  { transform: translate(-100px, -80px) scale(1.15); }
  50%  { transform: translate(-180px, -160px) scale(.9); }
  75%  { transform: translate(-70px, -220px) scale(1.1); }
  100% { transform: translate(-200px, -260px) scale(1); }
}

/* Isi card */
.stat-card-inner {
  position: relative;
  z-index: 1;
}

/* Icon */
.stat-icon {
  width: 48px;
  height: 48px;
  flex-shrink: 0;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  background: rgba(255, 255, 255, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.20);
}

/* Hover */
.stat-card:hover {
  box-shadow: 0 18px 35px rgba(39, 39, 42, 0.16);
}

@media (prefers-reduced-motion: reduce) {
  .stat-card-orange,
  .stat-card-gray,
  .stat-decoration-one,
  .stat-decoration-two {
    animation: none;
  }

  .stat-card {
    transition: none;
  }
}

/* FILTER TAB STATUS PELAMAR */
.filter-tabs {
    position: relative;
    display: inline-flex;
    align-items: center;
    flex-wrap: nowrap;
    gap: 4px;
    padding: 4px;
    background: #000000;
    border: 1px solid rgba(0, 0, 0, 0.025);
    border-radius: 9999px;
    isolation: isolate;
}

/* Indikator putih */
.filter-tab-indicator {
    position: absolute;
    z-index: 0;
    top: 4px;
    left: 4px;
    height: calc(100% - 8px);
    width: 0;
    border-radius: 9999px;
    background: #ffffff;
    box-shadow:
        0 1px 3px rgba(0, 0, 0, 0.08),
        0 1px 2px rgba(0, 0, 0, 0.04);
    pointer-events: none;
    will-change: transform, width;
}

/* Tombol tab */
.filter-tab {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 9px 15px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 600;
    color: #71717a;
    white-space: nowrap;
    cursor: pointer;
    transition: color 0.25s ease;
}

/* Tab aktif */
.filter-tab.active {
    color: #18181b;
}

/* Hover */
.filter-tab:not(.active):hover {
    color: #ffffff;
}

/* Klik */
.filter-tab:active {
    transform: scale(0.97);
}

/* Mobile */
@media (max-width: 640px) {
    .filter-tabs {
        width: 100%;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .filter-tabs::-webkit-scrollbar {
        display: none;
    }

    .filter-tab {
        padding: 9px 12px;
        flex-shrink: 0;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .filter-tab {
        transition: none;
    }
}
  </style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

  <div class="flex min-h-screen flex-col lg:flex-row">

    @include('partials.sidebar')

    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-20 sm:pb-24 space-y-6 sm:space-y-8 lg:space-y-10">

      <div
    class="w-full max-w-6xl mx-auto space-y-6 sm:space-y-8 lg:space-y-10 min-w-0"
    x-data="seleksiIndex(@js($dataFilter))"
>

        <!-- HEADER HALAMAN -->
        <div class="scroll-rv min-w-0">
          <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">PANEL ADMINISTRATOR</span>
          <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2 break-words">Daftar Pelamar</h1>
        </div>

        @if(session('success'))
          <div class="scroll-rv rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-medium px-5 py-3">
            {{ session('success') }}
          </div>
        @endif

        @if($errors->any())
          <div class="rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xs px-5 py-3 space-y-0.5">
            @foreach($errors->all() as $err)
              <p>• {{ $err }}</p>
            @endforeach
          </div>
        @endif

        <!-- KARTU STATISTIK -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 scroll-rv min-w-0">

  <!-- CARD 1: TOTAL PELAMAR -->
  <div class="stat-card stat-card-orange rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">

    <div class="stat-decoration stat-decoration-one"></div>
    <div class="stat-decoration stat-decoration-two"></div>

    <div class="stat-card-inner flex flex-col gap-5">

      <!-- Header -->
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <div class="stat-icon">
            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                 stroke-width="1.8" viewBox="0 0 24 24">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>

          <span class="text-xs font-semibold text-white/90">
            Total Pelamar
          </span>
        </div>

      </div>

      <!-- Angka -->
      <div>
        <h3 class="text-4xl sm:text-5xl font-bold tracking-tight count-target"
            data-target="{{ $total }}">0</h3>

      </div>

      <!-- Footer -->
      <div class="flex items-center justify-between gap-2 pt-4 border-t border-white/20">
        <span class="text-xs font-medium text-white/90">
          Semua lamaran
        </span>

        <span class="px-3 py-1.5 rounded-full bg-white text-orange-600 text-[10px] font-bold">
          Pelamar
        </span>
      </div>

    </div>
  </div>


  <!-- CARD 2: PERLU DITINJAU -->
  <div class="stat-card stat-card-gray rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">

    <div class="stat-decoration stat-decoration-one"></div>
    <div class="stat-decoration stat-decoration-two"></div>

    <div class="stat-card-inner flex flex-col gap-5">

      <!-- Header -->
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <div class="stat-icon">
            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                 stroke-width="1.8" viewBox="0 0 24 24">
              <path d="M12 8v4"/>
              <path d="M12 16h.01"/>
              <path d="M10.3 3.86 1.82 18.14A2 2 0 0 0 3.54 21h16.92a2 2 0 0 0 1.72-2.86L13.7 3.86a2 2 0 0 0-3.4 0Z"/>
            </svg>
          </div>

          <span class="text-xs font-semibold text-white/90">
            Perlu Ditinjau
          </span>
        </div>

      </div>

      <!-- Angka -->
      <div>
        <h3 class="text-4xl sm:text-5xl font-bold tracking-tight count-target"
            data-target="{{ $jumlah('diajukan') }}">0</h3>

      </div>

      <!-- Footer -->
      <div class="flex items-center justify-between gap-2 pt-4 border-t border-white/20">
        <span class="text-xs font-medium text-white/90">
          Menunggu review
        </span>

        <span class="px-3 py-1.5 rounded-full bg-white text-zinc-700 text-[10px] font-bold">
          Diajukan
        </span>
      </div>

    </div>
  </div>


  <!-- CARD 3: SEDANG DIPROSES -->
  <div class="stat-card stat-card-orange rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">

    <div class="stat-decoration stat-decoration-one"></div>
    <div class="stat-decoration stat-decoration-two"></div>

    <div class="stat-card-inner flex flex-col gap-5">

      <!-- Header -->
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <div class="stat-icon">
            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                 stroke-width="1.8" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="9"/>
              <path d="M12 7v5l3 2"/>
            </svg>
          </div>

          <span class="text-xs font-semibold text-white/90">
            Sedang Diproses
          </span>
        </div>

      </div>

      <!-- Angka -->
      <div>
        <h3 class="text-4xl sm:text-5xl font-bold tracking-tight count-target"
            data-target="{{ $jumlah('diproses') }}">0</h3>

      </div>

      <!-- Footer -->
      <div class="flex items-center justify-between gap-2 pt-4 border-t border-white/20">
        <span class="text-xs font-medium text-white/90">
          Tahap seleksi
        </span>

        <span class="px-3 py-1.5 rounded-full bg-white text-orange-600 text-[10px] font-bold">
          Diproses
        </span>
      </div>

    </div>
  </div>


  <!-- CARD 4: DITERIMA -->
  <div class="stat-card stat-card-gray rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">

    <div class="stat-decoration stat-decoration-one"></div>
    <div class="stat-decoration stat-decoration-two"></div>

    <div class="stat-card-inner flex flex-col gap-5">

      <!-- Header -->
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <div class="stat-icon">
            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                 stroke-width="1.8" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="9"/>
              <path d="m8 12 2.5 2.5L16 9"/>
            </svg>
          </div>

          <span class="text-xs font-semibold text-white/90">
            Diterima
          </span>
        </div>

      </div>

      <!-- Angka -->
      <div>
        <h3 class="text-4xl sm:text-5xl font-bold tracking-tight count-target"
            data-target="{{ $jumlah('diterima') }}">0</h3>

      </div>

      <!-- Footer -->
      <div class="flex items-center justify-between gap-2 pt-4 border-t border-white/20">
        <span class="text-xs font-medium text-white/90">
          Hasil seleksi
        </span>

        <span class="px-3 py-1.5 rounded-full bg-white text-zinc-700 text-[10px] font-bold">
          Diterima
        </span>
      </div>

    </div>
  </div>

</div>

        <!-- DAFTAR PELAMAR -->
        <div class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 lg:p-10 shadow-sm space-y-4 sm:space-y-6 min-w-0">

          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 pb-4 border-b border-black/5 min-w-0">
            <div>
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">Semua Lamaran</h3>
              <p class="text-[11px] text-zinc-400 mt-0.5">Lamaran lowongan kerja dan program magang yang masuk.</p>
            </div>
            <span class="text-xs bg-orange-50 text-ewforange font-bold px-3 py-1 rounded-full self-start sm:self-auto">{{ $total }} Lamaran</span>
          </div>

          <!-- FILTER STATUS -->
<div
    class="filter-tabs"
    x-ref="track"
    x-init="$nextTick(() => moveTab('Semua', false))"
>

    <!-- Indikator putih -->
    <div
        class="filter-tab-indicator"
        x-ref="indicator"
    ></div>

    <!-- Semua -->
    <button
        type="button"
        x-ref="tabSemua"
        @click="moveTab('Semua')"
        :class="{ 'active': fStatus === 'Semua' }"
        class="filter-tab"
    >
        Semua
        <span class="opacity-60">({{ $total }})</span>
    </button>

    <!-- Status lainnya -->
    @foreach($statusList as $st)

        <button
            type="button"
            x-ref="tab{{ ucfirst($st) }}"
            @click="moveTab('{{ ucfirst($st) }}')"
            :class="{ 'active': fStatus === '{{ $st }}' }"
            class="filter-tab"
        >
            {{ ucfirst($st) }}
            <span class="opacity-60">({{ $jumlah($st) }})</span>
        </button>

    @endforeach

</div>

          <!-- Filter jenis & pencarian -->
          <div class="flex flex-col md:flex-row md:items-center gap-3">
            <select x-model="fJenis" class="field md:w-48">
              <option value="Semua">Semua Jenis</option>
              <option value="Magang">Magang</option>
              <option value="Kerja">Kerja</option>
            </select>
            <div class="relative w-full md:flex-1 md:max-w-sm">
              <svg class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
              <input type="text" x-model="q" placeholder="Cari nama, email, atau posisi..." class="field !pl-10 !rounded-full">
            </div>
          </div>

          <div class="w-full min-w-0 overflow-x-auto overscroll-x-contain">
            <table class="w-full min-w-[820px] text-left border-collapse">
              <thead>
                <tr class="border-b border-black/5 text-[11px] font-semibold text-zinc-400 uppercase">
                  <th class="py-3 px-4">Nama</th>
                  <th class="py-3 px-4">Posisi</th>
                  <th class="py-3 px-4">Jenis</th>
                  <th class="py-3 px-4">Tgl Kirim</th>
                  <th class="py-3 px-4">Status</th>
                  <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black/5 text-xs">
                @forelse($rows as $row)
                  @php
                    $statusClass = match ($row->status) {
                      'diproses' => ['bg-sky-50 text-sky-600', 'bg-sky-500'],
                      'diterima' => ['bg-emerald-50 text-emerald-600', 'bg-emerald-500'],
                      'ditolak'  => ['bg-rose-50 text-rose-500', 'bg-rose-500'],
                      default    => ['bg-amber-50 text-amber-600', 'bg-amber-500'],
                    };
                  @endphp
                  <tr class="table-row-item table-row-hover"
                      x-show="visible(@js($row->status), @js($row->jenis), @js(strtolower($row->nama . ' ' . $row->posisi . ' ' . $row->email)))">
                    <td class="py-4 px-4">
                      <p class="font-bold text-zinc-900">{{ $row->nama }}</p>
                      <p class="text-[11px] text-zinc-400 mt-0.5">{{ $row->email }}</p>
                    </td>
                    <td class="py-4 px-4 text-zinc-600">{{ $row->posisi }}</td>
                    <td class="py-4 px-4">
                      <span class="px-2.5 py-1 rounded-md font-semibold text-[10px] {{ $row->jenis == 'Magang' ? 'bg-orange-50 text-ewforange' : 'bg-zinc-100 text-zinc-800' }}">
                        {{ $row->jenis }}
                      </span>
                    </td>
                    <td class="py-4 px-4 text-zinc-400 whitespace-nowrap">{{ $fmt($row->tgl) }}</td>
                    <td class="py-4 px-4">
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold text-[10px] capitalize {{ $statusClass[0] }}">
                        <i class="w-1.5 h-1.5 rounded-full {{ $statusClass[1] }}"></i>
                        {{ $row->status }}
                      </span>
                    </td>
                    <td class="py-4 px-4">
                      <div class="flex items-center justify-center gap-2">
                        <a href="{{ $detailUrl($row) }}"
                           class="inline-block px-4 py-2 bg-zinc-900 hover:bg-black text-white text-[11px] font-medium rounded-full transition shadow-sm hover:scale-105 whitespace-nowrap">
                          Selengkapnya
                        </a>

                        <!-- Ubah status -->
                        <form method="POST" action="{{ $statusUrl($row) }}">
                          @csrf
                          @method('PATCH')
                          <select name="status"
                                  @change="ubahStatus($el, @js($row->nama))"
                                  class="px-3 py-2 rounded-full border border-black/10 bg-white text-[11px] font-medium text-zinc-600 hover:border-ewforange focus:outline-none focus:border-ewforange cursor-pointer capitalize">
                            <option value="" selected disabled>Ubah status</option>
                            @foreach($statusList as $st)
                              @if($st !== $row->status)
                                <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                              @endif
                            @endforeach
                          </select>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="py-12 text-center text-zinc-400">
                      Belum ada pelamar yang mengirim lamaran.
                    </td>
                  </tr>
                @endforelse

                <!-- Tidak ada hasil filter -->
                @if($rows->count() > 0)
                  <tr x-show="hasil === 0" x-cloak>
                    <td colspan="6" class="py-12 text-center text-zinc-400">
                      Tidak ada lamaran yang cocok dengan filter atau pencarian.
                    </td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </main>
  </div>

  <script>
    // Komponen Alpine untuk filter, pencarian, dan ubah status
    document.addEventListener('alpine:init', () => {
    Alpine.data('seleksiIndex', (data) => ({

        // DATA
        data: data,

        // FILTER
        fStatus: 'Semua',
        fJenis: 'Semua',
        q: '',

        // ANIMASI TAB
        tabReady: false,

        moveTab(tab, animate = true) {

            const track = this.$refs.track;
            const indicator = this.$refs.indicator;
            const button = this.$refs['tab' + tab];

            if (!track || !indicator || !button) return;

            // Konversi label tab ke status database
            const status = tab === 'Semua'
                ? 'Semua'
                : tab.toLowerCase();

            // Update filter
            this.fStatus = status;

            // Posisi target
            const trackRect = track.getBoundingClientRect();
            const buttonRect = button.getBoundingClientRect();

            const targetX = buttonRect.left - trackRect.left - 4;
            const targetWidth = buttonRect.width;

            // Posisi awal
            if (!this.tabReady || !animate) {

                gsap.set(indicator, {
                    x: targetX,
                    width: targetWidth
                });

                this.tabReady = true;

                return;
            }

            // Posisi dan ukuran indikator sekarang
            const currentX = Number(
                gsap.getProperty(indicator, 'x')
            ) || 0;

            const currentWidth = Number(
                gsap.getProperty(indicator, 'width')
            ) || targetWidth;

            // Arah perpindahan
            const movingRight = targetX > currentX;

            // Ukuran pill saat memanjang
            const stretchWidth = movingRight
                ? targetX - currentX + targetWidth
                : currentX - targetX + currentWidth;

            // Posisi pill saat memanjang
            const stretchX = movingRight
                ? currentX
                : targetX;

            // Hentikan animasi sebelumnya
            gsap.killTweensOf(indicator);

            // Animasi memanjang lalu mengecil
            gsap.timeline()
                .to(indicator, {
                    x: stretchX,
                    width: stretchWidth,
                    duration: 0.35,
                    ease: 'power2.inOut'
                })
                .to(indicator, {
                    x: targetX,
                    width: targetWidth,
                    duration: 0.35,
                    ease: 'power3.out'
                });
        },

        // FILTER DATA
        visible(status, jenis, teks) {

            return (
                (this.fStatus === 'Semua' || this.fStatus === status)
                &&
                (this.fJenis === 'Semua' || this.fJenis === jenis)
                &&
                teks.includes(this.q.toLowerCase().trim())
            );
        },

        // JUMLAH HASIL FILTER
        get hasil() {

            return this.data.filter((d) =>
                this.visible(d.status, d.jenis, d.t)
            ).length;
        },

        // UBAH STATUS PELAMAR
        ubahStatus(el, nama) {

            if (!el.value) return;

            const label = el.options[el.selectedIndex].text;

            if (
                confirm(
                    'Ubah status lamaran ' +
                nama +
                ' menjadi "' +
                label +
                '"?'
                )
            ) {

                el.form.submit();

            } else {

                el.value = '';
            }
        },

    }));
});
  </script>

  <!-- GSAP & ScrollTrigger -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
  <script>
    gsap.registerPlugin(ScrollTrigger);

     // Animasi masuk isi card statistik
    gsap.fromTo(
      '.stat-card-inner',
      {
        y: 25,
        opacity: 0
      },
      {
        y: 0,
        opacity: 1,
        duration: 0.8,
        stagger: 0.15,
        ease: 'power3.out',
        delay: 0.2
      }
    );

    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    window.scrollTo(0, 0);

    const $ = (s, c = document) => [...c.querySelectorAll(s)];

    // 1. Animasi section saat scroll
    $('.scroll-rv').forEach((section) => {
      gsap.fromTo(section,
        { y: 50, opacity: 0 },
        { y: 0, opacity: 1, duration: 1, ease: 'power3.out',
          scrollTrigger: { trigger: section, start: 'top 85%', toggleActions: 'play none none none' } }
      );
    });

    // 2. Animasi baris tabel
    $('.table-row-item').forEach((row, index) => {
      gsap.fromTo(row,
        { y: 20, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.6, delay: (index % 3) * 0.1, ease: 'power2.out',
          scrollTrigger: { trigger: row, start: 'top 90%', toggleActions: 'play none none none' } }
      );
    });

    // 3. Count-up statistik
    $('.count-target').forEach(counter => {
      const target = parseInt(counter.dataset.target) || 0;
      const obj = { val: 0 };
      gsap.to(obj, {
        val: target, duration: 1.5, ease: 'power1.out',
        scrollTrigger: { trigger: counter, start: 'top 85%', toggleActions: 'play none none none' },
        onUpdate: () => { counter.textContent = Math.round(obj.val); }
      });
    });

    // 4. Hover kartu statistik
    $('.stat-card').forEach(card => {
      card.addEventListener('pointerenter', () => {
        gsap.to(card, {
          y: -6,
          scale: 1.02,
          duration: 0.3,
          ease: 'power2.out',
          overwrite: 'auto'
        });
      });

      card.addEventListener('pointerleave', () => {
        gsap.to(card, {
          y: 0,
          scale: 1,
          duration: 0.4,
          ease: 'power2.out',
          overwrite: 'auto'
        });
      });
    });
  </script>
</body>
</html>