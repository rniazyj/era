@php
  $f = fn ($k) => $filter[$k] ?? '';
  $num = fn ($n) => number_format((int) $n, 0, ',', '.');

  $roleLabel = [
    'admin_hr' => ['Admin / HR', 'bg-orange-50 text-ewforange'],
    'pimpinan' => ['Pimpinan', 'bg-sky-50 text-sky-600'],
    'pelamar'  => ['Pelamar', 'bg-zinc-100 text-zinc-700'],
  ];

  $adaFilter = collect($filter)->filter(fn ($v) => filled($v))->isNotEmpty();
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Log Aktivitas - Galeri Magang</title>
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
    .field { width: 100%; border-radius: 0.75rem; border: 1px solid rgba(0,0,0,.1); background: #fff; padding: .6rem .9rem; font-size: .8rem; outline: none; transition: all .2s; }
    .field:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); }

    /* ===== KARTU STATISTIK (gradasi bergerak) ===== */
    .stat-card { position: relative; isolation: isolate; overflow: hidden; transition: box-shadow 0.35s ease; will-change: transform; }
    .stat-card:hover { box-shadow: 0 18px 35px rgba(39, 39, 42, 0.16); }

    .stat-card-orange {
      background: linear-gradient(125deg, #f97316, #fb923c, #ea580c, #f97316);
      background-size: 300% 300%;
      animation: orangeGradient 8s ease infinite;
    }
    .stat-card-gray {
      background: linear-gradient(125deg, #52525b, #71717a, #3f3f46, #52525b);
      background-size: 300% 300%;
      animation: grayGradient 8s ease infinite;
    }
    @keyframes orangeGradient { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
    @keyframes grayGradient   { 0%, 100% { background-position: 100% 50%; } 50% { background-position: 0% 50%; } }

    .stat-decoration { position: absolute; z-index: 0; pointer-events: none; border-radius: 50%; will-change: transform; }
    .stat-decoration-one {
      width: 190px; height: 190px; top: -65px; right: -55px;
      background: rgba(255, 255, 255, 0.30); filter: blur(12px);
      animation: circleMoveOne 12s ease-in-out infinite alternate;
    }
    .stat-decoration-two {
      width: 140px; height: 140px; bottom: -55px; right: 35px;
      background: rgba(255, 255, 255, 0.20); filter: blur(16px);
      animation: circleMoveTwo 15s ease-in-out infinite alternate;
    }
    @keyframes circleMoveOne {
      0%   { transform: translate(0, 0) scale(1); }
      25%  { transform: translate(-90px, 55px) scale(1.1); }
      50%  { transform: translate(-150px, 125px) scale(0.95); }
      75%  { transform: translate(-60px, 180px) scale(1.15); }
      100% { transform: translate(-180px, 220px) scale(1); }
    }
    @keyframes circleMoveTwo {
      0%   { transform: translate(0, 0) scale(1); }
      25%  { transform: translate(-100px, -80px) scale(1.15); }
      50%  { transform: translate(-180px, -160px) scale(0.9); }
      75%  { transform: translate(-70px, -220px) scale(1.1); }
      100% { transform: translate(-200px, -260px) scale(1); }
    }
    .stat-icon {
      width: 48px; height: 48px; border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
      background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.2);
      color: white; flex-shrink: 0;
    }
    .stat-card-inner { opacity: 0; transform: translateY(18px); }

    @media (prefers-reduced-motion: reduce) {
      .stat-card-orange, .stat-card-gray, .stat-decoration-one, .stat-decoration-two { animation: none; }
      .stat-card { transition: none; }
    }
  </style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

  <div class="flex min-h-screen flex-col lg:flex-row">

    @include('partials.sidebar')

    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-20 sm:pb-24 space-y-6 sm:space-y-8 lg:space-y-10">

      <div class="w-full max-w-6xl mx-auto space-y-6 sm:space-y-8 lg:space-y-10 min-w-0">

        <!-- HEADER HALAMAN -->
        <div class="scroll-rv min-w-0">
          <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">PANEL ADMINISTRATOR</span>
          <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2 break-words">Log Aktivitas</h1>
          <p class="text-[11px] text-zinc-400 mt-1">Catatan siapa melakukan apa dan kapan, untuk semua pengguna. Log bersifat hanya-baca.</p>
        </div>

        @if($errors->any())
          <div class="rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xs px-5 py-3 space-y-0.5">
            @foreach($errors->all() as $err)
              <p>• {{ $err }}</p>
            @endforeach
          </div>
        @endif

        <!-- PERINGATAN IP MENCURIGAKAN -->
        @if($ipMencurigakan->isNotEmpty())
          <div class="scroll-rv rounded-2xl bg-rose-50 border border-rose-100 px-5 py-4 text-xs text-rose-600">
            <p class="font-bold">Aktivitas login mencurigakan</p>
            <p class="mt-1">
              Ada alamat IP dengan {{ $ambang }} kali login gagal atau lebih dalam {{ $menit }} menit terakhir:
            </p>
            <ul class="mt-2 flex flex-wrap gap-2">
              @foreach($ipMencurigakan as $ip)
                <li class="px-3 py-1 rounded-full bg-white border border-rose-100 font-mono font-semibold">
                  {{ $ip->alamat_ip }} · {{ $num($ip->jumlah) }}×
                </li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- KARTU STATISTIK -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 scroll-rv min-w-0">

          <!-- Aktivitas hari ini -->
          <div class="stat-card stat-card-orange rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">
            <div class="stat-decoration stat-decoration-one"></div>
            <div class="stat-decoration stat-decoration-two"></div>
            <div class="stat-card-inner relative z-10 flex flex-col gap-5">
              <div class="flex items-center gap-3 min-w-0">
                <div class="stat-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-white/90">Aktivitas Hari Ini</span>
              </div>
              <h3 class="text-3xl sm:text-4xl font-bold tracking-tight count-target" data-target="{{ $stat['hariIni'] }}">0</h3>
              <div class="flex items-center justify-between gap-2 pt-4 border-t border-white/20">
                <span class="text-xs font-medium text-white/90">Sejak 00.00</span>
                <span class="px-3 py-1.5 rounded-full bg-white text-orange-600 text-[10px] font-bold">Semua role</span>
              </div>
            </div>
          </div>

          <!-- Login gagal 24 jam -->
          <div class="stat-card stat-card-gray rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">
            <div class="stat-decoration stat-decoration-one"></div>
            <div class="stat-decoration stat-decoration-two"></div>
            <div class="stat-card-inner relative z-10 flex flex-col gap-5">
              <div class="flex items-center gap-3 min-w-0">
                <div class="stat-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-white/90">Login Gagal</span>
              </div>
              <h3 class="text-3xl sm:text-4xl font-bold tracking-tight count-target" data-target="{{ $stat['gagalLogin'] }}">0</h3>
              <div class="flex items-center justify-between gap-2 pt-4 border-t border-white/20">
                <span class="text-xs font-medium text-white/90">24 jam terakhir</span>
                <span class="px-3 py-1.5 rounded-full bg-white text-zinc-700 text-[10px] font-bold">Autentikasi</span>
              </div>
            </div>
          </div>

          <!-- Akun terkunci -->
          <div class="stat-card stat-card-orange rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">
            <div class="stat-decoration stat-decoration-one"></div>
            <div class="stat-decoration stat-decoration-two"></div>
            <div class="stat-card-inner relative z-10 flex flex-col gap-5">
              <div class="flex items-center gap-3 min-w-0">
                <div class="stat-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-white/90">Akun Terkunci</span>
              </div>
              <h3 class="text-3xl sm:text-4xl font-bold tracking-tight count-target" data-target="{{ $stat['terkunci'] }}">0</h3>
              <div class="flex items-center justify-between gap-2 pt-4 border-t border-white/20">
                <span class="text-xs font-medium text-white/90">Saat ini</span>
                <span class="px-3 py-1.5 rounded-full bg-white text-orange-600 text-[10px] font-bold">Akun</span>
              </div>
            </div>
          </div>

          <!-- Pengguna aktif -->
          <div class="stat-card stat-card-gray rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">
            <div class="stat-decoration stat-decoration-one"></div>
            <div class="stat-decoration stat-decoration-two"></div>
            <div class="stat-card-inner relative z-10 flex flex-col gap-5">
              <div class="flex items-center gap-3 min-w-0">
                <div class="stat-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-white/90">Pengguna Aktif</span>
              </div>
              <h3 class="text-3xl sm:text-4xl font-bold tracking-tight count-target" data-target="{{ $stat['penggunaAktif'] }}">0</h3>
              <div class="flex items-center justify-between gap-2 pt-4 border-t border-white/20">
                <span class="text-xs font-medium text-white/90">Hari ini</span>
                <span class="px-3 py-1.5 rounded-full bg-white text-zinc-700 text-[10px] font-bold">Pengguna unik</span>
              </div>
            </div>
          </div>

        </div>

        <!-- FILTER & TABEL LOG -->
        <div class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 lg:p-10 shadow-sm space-y-4 sm:space-y-6 min-w-0">

          
<div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 pb-4 border-b border-black/5 min-w-0">

    <div>
        <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">
            Riwayat Aktivitas
        </h3>
        <p class="text-[11px] text-zinc-400 mt-0.5">
            Diurutkan dari yang terbaru.
        </p>

        <span class="inline-block mt-2 text-xs bg-orange-50 text-ewforange font-bold px-3 py-1 rounded-full">
            {{ $num($logs->total()) }} log{{ $adaFilter ? ' (terfilter)' : '' }}
        </span>
    </div>

    <div class="flex flex-wrap items-center gap-2">

        <a href="{{ route('admin.log.export.excel', request()->query()) }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition">

            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <path d="M14 2v6h6"/>
                <path d="M8 13h8M8 17h8"/>
            </svg>

            Export Excel
        </a>

        <a href="{{ route('admin.log.export.pdf', request()->query()) }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold transition">

            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <path d="M14 2v6h6"/>
                <path d="M8 13h2a2 2 0 0 1 0 4H8v-4z"/>
                <path d="M15 17v-4h2"/>
            </svg>

            Export PDF
        </a>

    </div>
</div>


          <!-- Filter -->
          <form method="GET" action="{{ route('admin.log') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">

            <div class="lg:col-span-2">
              <label for="q" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Cari</label>
              <input type="text" id="q" name="q" value="{{ $f('q') }}" maxlength="100"
                     placeholder="Nama, email, aksi, atau IP..." class="field">
            </div>

            <div>
              <label for="role" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Role</label>
              <select id="role" name="role" class="field">
                <option value="">Semua role</option>
                <option value="admin_hr" @selected($f('role') === 'admin_hr')>Admin / HR</option>
                <option value="pimpinan" @selected($f('role') === 'pimpinan')>Pimpinan</option>
                <option value="pelamar"  @selected($f('role') === 'pelamar')>Pelamar</option>
                <option value="tamu"     @selected($f('role') === 'tamu')>Tidak dikenal</option>
              </select>
            </div>

            <div>
              <label for="modul" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Modul</label>
              <select id="modul" name="modul" class="field">
                <option value="">Semua modul</option>
                @foreach($daftarModul as $m)
                  <option value="{{ $m }}" @selected($f('modul') === $m)>{{ $m }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="status" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Hasil</label>
              <select id="status" name="status" class="field">
                <option value="">Semua hasil</option>
                <option value="berhasil" @selected($f('status') === 'berhasil')>Berhasil</option>
                <option value="gagal"    @selected($f('status') === 'gagal')>Gagal</option>
              </select>
            </div>

            <div class="flex items-end gap-2">
              <button type="submit"
                      class="flex-1 px-5 py-2.5 rounded-full bg-zinc-900 hover:bg-black text-white text-xs font-semibold transition">
                Terapkan
              </button>
              @if($adaFilter)
                <a href="{{ route('admin.log') }}"
                   class="px-4 py-2.5 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-semibold transition">
                  Reset
                </a>
              @endif
            </div>

            <div class="lg:col-span-2 grid grid-cols-2 gap-3">
              <div>
                <label for="dari" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Dari tanggal</label>
                <input type="date" id="dari" name="dari" value="{{ $f('dari') }}" class="field">
              </div>
              <div>
                <label for="sampai" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Sampai tanggal</label>
                <input type="date" id="sampai" name="sampai" value="{{ $f('sampai') }}" class="field">
              </div>
            </div>
          </form>

          <!-- Tabel -->
          <div class="w-full min-w-0 overflow-x-auto overscroll-x-contain">
            <table class="w-full min-w-[820px] text-left border-collapse">
              <thead>
                <tr class="border-b border-black/5 text-[11px] font-semibold text-zinc-400 uppercase">
                  <th class="py-3 px-4">Waktu</th>
                  <th class="py-3 px-4">Siapa</th>
                  <th class="py-3 px-4">Aksi</th>
                  <th class="py-3 px-4">Modul</th>
                  <th class="py-3 px-4">Hasil</th>
                  <th class="py-3 px-4">IP</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black/5 text-xs">
                @forelse($logs as $log)
                  @php
                    $pengguna = $log->pengguna;
                    $namaRole = $pengguna?->role?->nama_role;
                    $label    = $roleLabel[$namaRole] ?? null;
                    $gagal    = strtolower($log->status_aksi) !== 'berhasil';
                  @endphp
                  <tr class="hover:bg-orange-50/40 transition-colors">
                    <td class="py-3.5 px-4 whitespace-nowrap">
                      <p class="font-semibold text-zinc-700">{{ $log->waktu?->locale('id')->translatedFormat('d M Y') }}</p>
                      <p class="text-[11px] text-zinc-400 font-mono">{{ $log->waktu?->format('H:i:s') }}</p>
                    </td>
                    <td class="py-3.5 px-4">
                      @if($pengguna)
                        <p class="font-bold text-zinc-900">{{ $pengguna->name }}</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                          <span class="text-[11px] text-zinc-400">{{ $pengguna->email }}</span>
                          @if($label)
                            <span class="px-2 py-0.5 rounded-md font-semibold text-[9px] {{ $label[1] }}">{{ $label[0] }}</span>
                          @endif
                        </div>
                      @else
                        <p class="font-semibold text-zinc-400">Tidak dikenal</p>
                      @endif
                    </td>
                    <td class="py-3.5 px-4 text-zinc-700 max-w-md break-words">{{ $log->aktivitas }}</td>
                    <td class="py-3.5 px-4">
                      <span class="px-2.5 py-1 rounded-md bg-zinc-100 text-zinc-700 font-semibold text-[10px]">{{ $log->modul }}</span>
                    </td>
                    <td class="py-3.5 px-4">
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold text-[10px] capitalize {{ $gagal ? 'bg-rose-50 text-rose-500' : 'bg-emerald-50 text-emerald-600' }}">
                        <i class="w-1.5 h-1.5 rounded-full {{ $gagal ? 'bg-rose-500' : 'bg-emerald-500' }}"></i>
                        {{ $log->status_aksi }}
                      </span>
                    </td>
                    <td class="py-3.5 px-4 text-zinc-500 font-mono text-[11px] whitespace-nowrap">{{ $log->alamat_ip ?? '-' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="py-12 text-center text-zinc-400">
                      {{ $adaFilter ? 'Tidak ada log yang cocok dengan filter.' : 'Belum ada aktivitas yang tercatat.' }}
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Paginasi -->
          @if($logs->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-black/5">
              <p class="text-[11px] text-zinc-400">
                Menampilkan {{ $num($logs->firstItem()) }}–{{ $num($logs->lastItem()) }} dari {{ $num($logs->total()) }} log
              </p>

              <div class="flex items-center gap-1.5">
                @if($logs->onFirstPage())
                  <span class="px-3 py-1.5 rounded-full bg-zinc-100 text-zinc-300 text-[11px] font-semibold">←</span>
                @else
                  <a href="{{ $logs->previousPageUrl() }}" class="px-3 py-1.5 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-semibold transition">←</a>
                @endif

                @foreach($logs->getUrlRange(max(1, $logs->currentPage() - 2), min($logs->lastPage(), $logs->currentPage() + 2)) as $page => $url)
                  <a href="{{ $url }}"
                     class="px-3 py-1.5 rounded-full text-[11px] font-semibold transition {{ $page === $logs->currentPage() ? 'bg-zinc-900 text-white' : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-700' }}">
                    {{ $page }}
                  </a>
                @endforeach

                @if($logs->hasMorePages())
                  <a href="{{ $logs->nextPageUrl() }}" class="px-3 py-1.5 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-semibold transition">→</a>
                @else
                  <span class="px-3 py-1.5 rounded-full bg-zinc-100 text-zinc-300 text-[11px] font-semibold">→</span>
                @endif
              </div>
            </div>
          @endif
        </div>

      </div>
    </main>
  </div>

  <!-- GSAP & ScrollTrigger -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
  <script>
    gsap.registerPlugin(ScrollTrigger);

    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    window.scrollTo(0, 0);

    const $ = (s, c = document) => [...c.querySelectorAll(s)];

    // 1. Animasi masuk isi kartu statistik
    gsap.fromTo('.stat-card-inner',
      { y: 25, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.8, stagger: 0.15, ease: 'power3.out', delay: 0.2 }
    );

    // 2. Animasi section saat scroll
    $('.scroll-rv').forEach((section) => {
      gsap.fromTo(section,
        { y: 50, opacity: 0 },
        { y: 0, opacity: 1, duration: 1, ease: 'power3.out',
          scrollTrigger: { trigger: section, start: 'top 85%', toggleActions: 'play none none none' } }
      );
    });

    // 3. Count-up angka statistik
    $('.count-target').forEach(counter => {
      const target = parseInt(counter.dataset.target) || 0;
      const obj = { val: 0 };

      gsap.to(obj, {
        val: target, duration: 1.5, ease: 'power1.out',
        scrollTrigger: { trigger: counter, start: 'top 90%', toggleActions: 'play none none none' },
        onUpdate: () => { counter.textContent = Math.round(obj.val); }
      });
    });

    // 4. Hover kartu statistik
    $('.stat-card').forEach(card => {
      card.addEventListener('pointerenter', () => {
        gsap.to(card, { y: -6, scale: 1.015, duration: 0.35, ease: 'power2.out', overwrite: 'auto' });
      });
      card.addEventListener('pointerleave', () => {
        gsap.to(card, { y: 0, scale: 1, duration: 0.4, ease: 'power2.out', overwrite: 'auto' });
      });
    });
  </script>
</body>
</html>