@php

  $num = fn ($n, $dec = 0) => number_format((float) $n, $dec, ',', '.');



  $waktu = $d['waktu'];

  $surat = $d['surat'];

  $kirim = $d['kirim'];



  // Warna status lamaran: [warna batang, kelas label]

  $warnaStatus = [

    'diajukan' => ['bg-amber-500', 'text-amber-600'],

    'diproses' => ['bg-sky-500', 'text-sky-600'],

    'diterima' => ['bg-emerald-500', 'text-emerald-600'],

    'ditolak'  => ['bg-rose-500', 'text-rose-500'],

  ];



  $warnaKategori = [

    'berhasil' => ['bg-emerald-50 text-emerald-600', 'bg-emerald-500'],

    'gagal'    => ['bg-rose-50 text-rose-500', 'bg-rose-500'],

    'menunggu' => ['bg-amber-50 text-amber-600', 'bg-amber-500'],

  ];



  // Donut persentase diterima (keliling lingkaran r=52)

  $keliling      = 2 * M_PI * 52;

  $donutDiterima = $d['persenDiterima'] ?? 0;

  $donutOffset   = $keliling * (1 - $donutDiterima / 100);



  // Donut persentase pengiriman berhasil

  $donutKirim       = $kirim['persenBerhasil'] ?? 0;

  $donutKirimOffset = $keliling * (1 - $donutKirim / 100);



  // Skala grafik tren

  $maxTren = max(1, collect($d['tren'])->max('total'));

  $maxStatus = max(1, max($d['perStatus']));

@endphp

<!DOCTYPE html>

<html lang="id" class="scroll-smooth">

<head>

  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Dashboard Pimpinan - ERA</title>

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

    .field { border-radius: 9999px; border: 1px solid rgba(0,0,0,.1); background: #fff; padding: .55rem 1rem; font-size: .75rem; font-weight: 500; outline: none; transition: all .2s; cursor: pointer; }

    .field:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); }



    /* ===== KARTU STATISTIK (gradasi bergerak) ===== */

    .stat-card {

      position: relative;

      isolation: isolate;

      overflow: hidden;

      transition: box-shadow 0.35s ease;

      will-change: transform;

    }



    .stat-card:hover {

      box-shadow: 0 18px 35px rgba(39, 39, 42, 0.16);

    }



    /* Gradasi bergerak: orange */

    .stat-card-orange {

      background: linear-gradient(125deg, #f97316, #fb923c, #ea580c, #f97316);

      background-size: 300% 300%;

      animation: orangeGradient 8s ease infinite;

    }



    /* Gradasi bergerak: abu-abu */

    .stat-card-gray {

      background: linear-gradient(125deg, #52525b, #71717a, #3f3f46, #52525b);

      background-size: 300% 300%;

      animation: grayGradient 8s ease infinite;

    }



    @keyframes orangeGradient {

      0%, 100% { background-position: 0% 50%; }

      50%      { background-position: 100% 50%; }

    }



    @keyframes grayGradient {

      0%, 100% { background-position: 100% 50%; }

      50%      { background-position: 0% 50%; }

    }



    /* Lingkaran dekorasi: putih transparan dan buram */

    .stat-decoration {

      position: absolute;

      z-index: 0;

      pointer-events: none;

      border-radius: 50%;

      background: rgba(255, 255, 255, 0.28);

      filter: blur(12px);

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



    /* Ikon kartu */

    .stat-icon {

      width: 48px;

      height: 48px;

      border-radius: 16px;

      display: flex;

      align-items: center;

      justify-content: center;

      background: rgba(255, 255, 255, 0.18);

      border: 1px solid rgba(255, 255, 255, 0.2);

      color: white;

      flex-shrink: 0;

    }



    /* Isi kartu muncul dengan animasi (diatur GSAP) */

    .stat-card-inner {

      opacity: 0;

      transform: translateY(18px);

    }



    /* Hormati pengaturan "kurangi gerakan" */

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

  </style>

</head>



<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">



  <div class="flex min-h-screen flex-col lg:flex-row">



    @include('partials.sidebar')



    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-20 sm:pb-24 space-y-6 sm:space-y-8 lg:space-y-10">



      <div class="w-full max-w-6xl mx-auto space-y-6 sm:space-y-8 lg:space-y-10 min-w-0">



        <!-- HEADER HALAMAN -->

        <div class="scroll-rv flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 min-w-0">

          <div>

            <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">PANEL PIMPINAN</span>

            <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2 break-words">Dashboard Pimpinan</h1>

            <p class="text-[11px] text-zinc-400 mt-1">Data per {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</p>

          </div>



          <form method="GET" action="{{ route('pimpinan.dashboard') }}" class="flex items-center gap-2">

            <label for="periode" class="text-[11px] font-semibold text-zinc-400">Periode</label>

            <select id="periode" name="periode" class="field" onchange="this.form.submit()">

              @foreach($opsiPeriode as $kode => $label)

                <option value="{{ $kode }}" @selected($periode === (string) $kode)>{{ $label }}</option>

              @endforeach

            </select>

          </form>

        </div>



        <!-- KARTU STATISTIK UTAMA -->

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 scroll-rv min-w-0">



          <!-- CARD 1: TOTAL PENDAFTAR (orange) -->

          <div class="stat-card stat-card-orange rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">



            <div class="stat-decoration stat-decoration-one"></div>

            <div class="stat-decoration stat-decoration-two"></div>



            <div class="stat-card-inner relative z-10 flex flex-col gap-5">



              <div class="flex items-center gap-3 min-w-0">

                <div class="stat-icon">

                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"

                       stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>

                    <circle cx="9" cy="7" r="4"/>

                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>

                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>

                  </svg>

                </div>

                <span class="text-xs sm:text-sm font-semibold text-white/90">Total Pendaftar</span>

              </div>



              <div>

                <h3 class="text-3xl sm:text-4xl font-bold tracking-tight count-target" data-target="{{ $d['totalLamaran'] }}">0</h3>

              </div>



              <div class="flex flex-wrap items-center justify-between gap-2 pt-4 border-t border-white/20">

                <span class="text-xs font-medium text-white/90">{{ $num($d['pelamarUnik']) }} pelamar</span>

                <span class="px-3 py-1.5 rounded-full bg-white text-orange-600 text-[10px] font-bold">

                  Magang {{ $num($d['totalMagang']) }} · Kerja {{ $num($d['totalKerja']) }}

                </span>

              </div>



            </div>

          </div>



          <!-- CARD 2: PERSENTASE DITERIMA (abu-abu) -->

          <div class="stat-card stat-card-gray rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">



            <div class="stat-decoration stat-decoration-one"></div>

            <div class="stat-decoration stat-decoration-two"></div>



            <div class="stat-card-inner relative z-10 flex flex-col gap-5">



              <div class="flex items-center gap-3 min-w-0">

                <div class="stat-icon">

                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"

                       stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>

                    <polyline points="22 4 12 14.01 9 11.01"/>

                  </svg>

                </div>

                <span class="text-xs sm:text-sm font-semibold text-white/90">Persentase Diterima</span>

              </div>



              <div>

                @if($d['persenDiterima'] !== null)

                  <h3 class="text-3xl sm:text-4xl font-bold tracking-tight count-target"

                      data-target="{{ $d['persenDiterima'] }}" data-decimals="1" data-suffix="%">0%</h3>

                @else

                  <h3 class="text-3xl sm:text-4xl font-bold tracking-tight text-white/60">-</h3>

                @endif

              </div>



              <div class="flex flex-wrap items-center justify-between gap-2 pt-4 border-t border-white/20">

                <span class="text-xs font-medium text-white/90">

                  {{ $num($d['diterima']) }} dari {{ $num($d['diputuskan']) }} diputuskan

                </span>

                <span class="px-3 py-1.5 rounded-full bg-white text-zinc-700 text-[10px] font-bold">Diterima</span>

              </div>



            </div>

          </div>



          <!-- CARD 3: WAKTU PROSES (orange) -->

          <div class="stat-card stat-card-orange rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">



            <div class="stat-decoration stat-decoration-one"></div>

            <div class="stat-decoration stat-decoration-two"></div>



            <div class="stat-card-inner relative z-10 flex flex-col gap-5">



              <div class="flex items-center gap-3 min-w-0">

                <div class="stat-icon">

                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"

                       stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                    <circle cx="12" cy="12" r="10"/>

                    <polyline points="12 6 12 12 16 14"/>

                  </svg>

                </div>

                <span class="text-xs sm:text-sm font-semibold text-white/90">Rata-rata Waktu Proses</span>

              </div>



              <div>

                @if($waktu['rata'] !== null)

                  <h3 class="text-3xl sm:text-4xl font-bold tracking-tight count-target"

                      data-target="{{ $waktu['rata'] }}" data-decimals="1" data-suffix=" hari">0 hari</h3>

                @else

                  <h3 class="text-3xl sm:text-4xl font-bold tracking-tight text-white/60">-</h3>

                @endif

              </div>



              <div class="flex flex-wrap items-center justify-between gap-2 pt-4 border-t border-white/20">

                <span class="text-xs font-medium text-white/90">{{ $num($waktu['jumlah']) }} lamaran diputuskan</span>

                <span class="px-3 py-1.5 rounded-full bg-white text-orange-600 text-[10px] font-bold">

                  Pengajuan → Keputusan

                </span>

              </div>



            </div>

          </div>



          <!-- CARD 4: SURAT TERBIT & PENGIRIMAN (abu-abu) -->

          <div class="stat-card stat-card-gray rounded-3xl p-5 sm:p-6 shadow-sm cursor-pointer text-white min-w-0">



            <div class="stat-decoration stat-decoration-one"></div>

            <div class="stat-decoration stat-decoration-two"></div>



            <div class="stat-card-inner relative z-10 flex flex-col gap-5">



              <div class="flex items-center gap-3 min-w-0">

                <div class="stat-icon">

                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"

                       stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>

                    <polyline points="22,6 12,13 2,6"/>

                  </svg>

                </div>

                <span class="text-xs sm:text-sm font-semibold text-white/90">Surat Terbit</span>

              </div>



              <div>

                <h3 class="text-3xl sm:text-4xl font-bold tracking-tight count-target" data-target="{{ $surat['terbit'] }}">0</h3>

              </div>



              <div class="flex flex-wrap items-center justify-between gap-2 pt-4 border-t border-white/20">

                <span class="text-xs font-medium text-white/90">

                  {{ $num($kirim['berhasil']) }} terkirim · {{ $num($kirim['gagal']) }} gagal

                </span>

                <span class="px-3 py-1.5 rounded-full bg-white text-zinc-700 text-[10px] font-bold">

                  {{ $num($kirim['total']) }} pengiriman

                </span>

              </div>



            </div>

          </div>



        </div>



        <!-- TREN & STATUS LAMARAN -->

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">



          <!-- Tren pendaftar -->

          <section class="scroll-rv lg:col-span-2 bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm min-w-0">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 border-b border-black/5">

              <div>

                <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">Tren Pendaftar</h3>

                <p class="text-[11px] text-zinc-400 mt-0.5">6 bulan terakhir, tidak terpengaruh filter periode.</p>

              </div>

              <div class="flex items-center gap-3 text-[11px] text-zinc-500">

                <span class="inline-flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-sm bg-ewforange"></i>Magang</span>

                <span class="inline-flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-sm bg-zinc-300"></i>Kerja</span>

              </div>

            </div>



            <div class="flex items-end gap-2 sm:gap-4 mt-6">

              @foreach($d['tren'] as $t)

                @php $tinggi = $t['total'] > 0 ? max(6, round($t['total'] / $maxTren * 100)) : 0; @endphp

                <div class="flex-1 min-w-0 flex flex-col items-center gap-2" title="{{ $t['bulan'] }}: {{ $t['total'] }} pendaftar (Magang {{ $t['magang'] }}, Kerja {{ $t['kerja'] }})">

                  <span class="text-[11px] font-bold text-zinc-700">{{ $t['total'] }}</span>

                  <div class="w-full h-40 flex flex-col justify-end items-center">

                    @if($tinggi > 0)

                      <div class="bar-v w-full max-w-[48px] flex flex-col rounded-t-xl overflow-hidden"

                           data-pct="{{ $tinggi }}" style="height: {{ $tinggi }}%">

                        <div class="bg-zinc-300" style="flex: {{ $t['kerja'] }} 1 0%"></div>

                        <div class="bg-ewforange" style="flex: {{ $t['magang'] }} 1 0%"></div>

                      </div>

                    @else

                      <div class="w-full max-w-[48px] h-0.5 bg-zinc-200"></div>

                    @endif

                  </div>

                  <span class="text-[11px] font-semibold text-zinc-400">{{ $t['label'] }}</span>

                </div>

              @endforeach

            </div>

          </section>



          <!-- Status lamaran -->

          <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm min-w-0">

            <div class="pb-4 border-b border-black/5">

              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">Status Lamaran</h3>

              <p class="text-[11px] text-zinc-400 mt-0.5">Posisi seluruh lamaran saat ini.</p>

            </div>



            <div class="space-y-4 mt-5">

              @foreach($d['perStatus'] as $st => $n)

                @php $lebar = $n > 0 ? max(4, round($n / $maxStatus * 100)) : 0; @endphp

                <div>

                  <div class="flex items-center justify-between text-xs mb-1.5">

                    <span class="font-semibold capitalize {{ $warnaStatus[$st][1] }}">{{ $st }}</span>

                    <span class="font-bold text-zinc-700">{{ $num($n) }}</span>

                  </div>

                  <div class="w-full h-2.5 rounded-full bg-zinc-100 overflow-hidden">

                    @if($lebar > 0)

                      <div class="bar-h h-full rounded-full {{ $warnaStatus[$st][0] }}" data-pct="{{ $lebar }}" style="width: {{ $lebar }}%"></div>

                    @endif

                  </div>

                </div>

              @endforeach

            </div>

          </section>

        </div>



        <!-- PERSENTASE DITERIMA & WAKTU PROSES -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 items-stretch">



          <!-- Persentase diterima -->

          <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm min-w-0">

            <div class="pb-4 border-b border-black/5">

              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">Persentase Diterima</h3>

              <p class="text-[11px] text-zinc-400 mt-0.5">Hanya menghitung lamaran yang sudah diputuskan (diterima atau ditolak).</p>

            </div>



            <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 mt-6">

              <div class="relative w-36 h-36 shrink-0">

                <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">

                  <circle cx="60" cy="60" r="52" fill="none" stroke="#f4f4f5" stroke-width="12"/>

                  <circle class="donut-arc" cx="60" cy="60" r="52" fill="none" stroke="#f97316" stroke-width="12"

                          stroke-linecap="round"

                          stroke-dasharray="{{ $keliling }}" stroke-dashoffset="{{ $donutOffset }}"

                          data-c="{{ $keliling }}" data-final="{{ $donutOffset }}"/>

                </svg>

                <div class="absolute inset-0 flex flex-col items-center justify-center">

                  <span class="text-2xl font-bold text-zinc-900">

                    {{ $d['persenDiterima'] !== null ? $num($d['persenDiterima'], 1) . '%' : '-' }}

                  </span>

                  <span class="text-[10px] font-semibold text-zinc-400">diterima</span>

                </div>

              </div>



              <dl class="w-full grid grid-cols-3 sm:grid-cols-1 gap-3 text-xs">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5">

                  <dt class="inline-flex items-center gap-1.5 text-zinc-500"><i class="w-2 h-2 rounded-full bg-emerald-500"></i>Diterima</dt>

                  <dd class="font-bold text-zinc-900">{{ $num($d['diterima']) }}</dd>

                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5">

                  <dt class="inline-flex items-center gap-1.5 text-zinc-500"><i class="w-2 h-2 rounded-full bg-rose-500"></i>Ditolak</dt>

                  <dd class="font-bold text-zinc-900">{{ $num($d['ditolak']) }}</dd>

                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5">

                  <dt class="inline-flex items-center gap-1.5 text-zinc-500"><i class="w-2 h-2 rounded-full bg-zinc-300"></i>Belum diputuskan</dt>

                  <dd class="font-bold text-zinc-900">{{ $num($d['belumDiputuskan']) }}</dd>

                </div>

              </dl>

            </div>

          </section>



          <!-- Waktu proses -->

          <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm min-w-0">

            <div class="pb-4 border-b border-black/5">

              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">Waktu Proses</h3>

              <p class="text-[11px] text-zinc-400 mt-0.5">Lama dari tanggal pengajuan sampai keputusan diambil.</p>

            </div>



            @if($waktu['rata'] !== null)

              <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">

                <div class="rounded-2xl bg-orange-50 p-4 text-center">

                  <p class="text-xl font-bold text-ewforange">{{ $num($waktu['rata'], 1) }}</p>

                  <p class="text-[10px] font-semibold text-zinc-500 mt-1">Rata-rata (hari)</p>

                </div>

                <div class="rounded-2xl bg-paper border border-black/5 p-4 text-center">

                  <p class="text-xl font-bold text-zinc-900">{{ $num($waktu['median'], 1) }}</p>

                  <p class="text-[10px] font-semibold text-zinc-500 mt-1">Median (hari)</p>

                </div>

                <div class="rounded-2xl bg-paper border border-black/5 p-4 text-center">

                  <p class="text-xl font-bold text-zinc-900">{{ $num($waktu['min']) }}</p>

                  <p class="text-[10px] font-semibold text-zinc-500 mt-1">Tercepat (hari)</p>

                </div>

                <div class="rounded-2xl bg-paper border border-black/5 p-4 text-center">

                  <p class="text-xl font-bold text-zinc-900">{{ $num($waktu['max']) }}</p>

                  <p class="text-[10px] font-semibold text-zinc-500 mt-1">Terlama (hari)</p>

                </div>

              </div>

            @else

              <p class="text-xs text-zinc-400 mt-6">Belum ada lamaran yang diputuskan pada periode ini.</p>

            @endif



            <div class="mt-5 rounded-2xl border px-4 py-3 text-xs {{ $d['lewatSla'] > 0 ? 'bg-amber-50 border-amber-100 text-amber-700' : 'bg-zinc-50 border-zinc-200 text-zinc-600' }}">

              <span class="font-bold">{{ $num($d['belumDiputuskan']) }} lamaran belum diputuskan.</span>

              @if($d['lewatSla'] > 0)

                {{ $num($d['lewatSla']) }} di antaranya sudah menunggu lebih dari {{ $d['slaHari'] }} hari.

              @else

                Tidak ada yang menunggu lebih dari {{ $d['slaHari'] }} hari.

              @endif

            </div>

          </section>

        </div>



        <!-- SURAT & PENGIRIMAN -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 items-stretch">



          <!-- Surat penerimaan -->

          <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm min-w-0">

            <div class="pb-4 border-b border-black/5">

              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">Surat Penerimaan</h3>

              <p class="text-[11px] text-zinc-400 mt-0.5">Surat terbit adalah surat yang statusnya bukan draft.</p>

            </div>



            <div class="grid grid-cols-3 gap-3 mt-6">

              <div class="rounded-2xl bg-orange-50 p-4 text-center">

                <p class="text-xl font-bold text-ewforange">{{ $num($surat['terbit']) }}</p>

                <p class="text-[10px] font-semibold text-zinc-500 mt-1">Terbit</p>

              </div>

              <div class="rounded-2xl bg-paper border border-black/5 p-4 text-center">

                <p class="text-xl font-bold text-zinc-900">{{ $num($surat['draft']) }}</p>

                <p class="text-[10px] font-semibold text-zinc-500 mt-1">Draft</p>

              </div>

              <div class="rounded-2xl bg-paper border border-black/5 p-4 text-center">

                <p class="text-xl font-bold text-zinc-900">{{ $num($surat['total']) }}</p>

                <p class="text-[10px] font-semibold text-zinc-500 mt-1">Total Surat</p>

              </div>

            </div>



            @if(count($surat['perStatus']) > 0)

              <ul class="mt-5 space-y-2 text-xs">

                @foreach($surat['perStatus'] as $s)

                  <li class="flex items-center justify-between rounded-xl bg-paper border border-black/5 px-4 py-2.5">

                    <span class="font-semibold capitalize text-zinc-600">{{ $s['status'] }}</span>

                    <span class="font-bold text-zinc-900">{{ $num($s['n']) }}</span>

                  </li>

                @endforeach

              </ul>

            @else

              <p class="text-xs text-zinc-400 mt-5">Belum ada surat penerimaan pada periode ini.</p>

            @endif



            <div class="mt-5 rounded-2xl border px-4 py-3 text-xs {{ $surat['diterimaBelumSurat'] > 0 ? 'bg-amber-50 border-amber-100 text-amber-700' : 'bg-emerald-50 border-emerald-100 text-emerald-700' }}">

              @if($surat['diterimaBelumSurat'] > 0)

                <span class="font-bold">{{ $num($surat['diterimaBelumSurat']) }} pelamar diterima</span> belum memiliki surat yang terbit.

              @else

                Semua pelamar yang diterima sudah memiliki surat yang terbit.

              @endif

            </div>

          </section>



          <!-- Pengiriman -->

          <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm min-w-0">

            <div class="pb-4 border-b border-black/5">

              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">Pengiriman</h3>

              <p class="text-[11px] text-zinc-400 mt-0.5">Pengiriman surat dan pesan kepada pelamar.</p>

            </div>



            <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 mt-6">

              <div class="relative w-32 h-32 shrink-0">

                <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">

                  <circle cx="60" cy="60" r="52" fill="none" stroke="#f4f4f5" stroke-width="12"/>

                  <circle class="donut-arc" cx="60" cy="60" r="52" fill="none" stroke="#10b981" stroke-width="12"

                          stroke-linecap="round"

                          stroke-dasharray="{{ $keliling }}" stroke-dashoffset="{{ $donutKirimOffset }}"

                          data-c="{{ $keliling }}" data-final="{{ $donutKirimOffset }}"/>

                </svg>

                <div class="absolute inset-0 flex flex-col items-center justify-center">

                  <span class="text-xl font-bold text-zinc-900">

                    {{ $kirim['persenBerhasil'] !== null ? $num($kirim['persenBerhasil'], 1) . '%' : '-' }}

                  </span>

                  <span class="text-[10px] font-semibold text-zinc-400">berhasil</span>

                </div>

              </div>



              <dl class="w-full grid grid-cols-3 sm:grid-cols-1 gap-3 text-xs">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5">

                  <dt class="inline-flex items-center gap-1.5 text-zinc-500"><i class="w-2 h-2 rounded-full bg-emerald-500"></i>Berhasil</dt>

                  <dd class="font-bold text-zinc-900">{{ $num($kirim['berhasil']) }}</dd>

                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5">

                  <dt class="inline-flex items-center gap-1.5 text-zinc-500"><i class="w-2 h-2 rounded-full bg-rose-500"></i>Gagal</dt>

                  <dd class="font-bold text-zinc-900">{{ $num($kirim['gagal']) }}</dd>

                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5">

                  <dt class="inline-flex items-center gap-1.5 text-zinc-500"><i class="w-2 h-2 rounded-full bg-amber-500"></i>Menunggu</dt>

                  <dd class="font-bold text-zinc-900">{{ $num($kirim['menunggu']) }}</dd>

                </div>

              </dl>

            </div>



            @if(count($kirim['perStatus']) > 0)

              <div class="mt-5 flex flex-wrap gap-2">

                @foreach($kirim['perStatus'] as $s)

                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold text-[10px] capitalize {{ $warnaKategori[$s['kategori']][0] }}">

                    <i class="w-1.5 h-1.5 rounded-full {{ $warnaKategori[$s['kategori']][1] }}"></i>

                    {{ $s['status'] }} · {{ $num($s['n']) }}

                  </span>

                @endforeach

              </div>

            @else

              <p class="text-xs text-zinc-400 mt-5">Belum ada pengiriman pada periode ini.</p>

            @endif



            @if(count($kirim['perKanal']) > 0)

              <div class="mt-4 pt-4 border-t border-black/5">

                <p class="text-[11px] font-semibold text-zinc-400 mb-2">Per kanal</p>

                <div class="flex flex-wrap gap-2">

                  @foreach($kirim['perKanal'] as $k)

                    <span class="px-3 py-1 rounded-full bg-zinc-100 text-zinc-700 text-[11px] font-semibold capitalize">

                      {{ $k['kanal'] }} · {{ $num($k['n']) }}

                    </span>

                  @endforeach

                </div>

              </div>

            @endif

          </section>

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



    // 3. Count-up angka statistik (mendukung desimal dan akhiran seperti % atau " hari")

    $('.count-target').forEach(counter => {

      const target   = parseFloat(counter.dataset.target) || 0;

      const decimals = parseInt(counter.dataset.decimals) || 0;

      const suffix   = counter.dataset.suffix || '';

      const obj = { val: 0 };



      gsap.to(obj, {

        val: target, duration: 1.5, ease: 'power1.out',

        scrollTrigger: { trigger: counter, start: 'top 90%', toggleActions: 'play none none none' },

        onUpdate: () => {

          counter.textContent = obj.val.toFixed(decimals).replace('.', ',') + suffix;

        }

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



    // 5. Donut: lingkaran terisi dari kosong sampai persentasenya

    $('.donut-arc').forEach(arc => {

      const keliling = parseFloat(arc.dataset.c);

      const akhir    = parseFloat(arc.dataset.final);



      gsap.fromTo(arc,

        { strokeDashoffset: keliling },

        { strokeDashoffset: akhir, duration: 1.4, ease: 'power2.out',

          scrollTrigger: { trigger: arc, start: 'top 90%', toggleActions: 'play none none none' } }

      );

    });



    // 6. Batang horizontal (status lamaran)

    $('.bar-h').forEach(bar => {

      gsap.fromTo(bar,

        { width: '0%' },

        { width: bar.dataset.pct + '%', duration: 1.1, ease: 'power3.out',

          scrollTrigger: { trigger: bar, start: 'top 92%', toggleActions: 'play none none none' } }

      );

    });



    // 7. Animasi batang vertikal (tren pendaftar)
    // Batang tumbuh dari bawah secara berurutan, dengan efek overshoot yang halus.
    // Tinggi akhir tetap mengikuti data-pct; warna, ukuran, dan layout tidak diubah.
    $('.bar-v').forEach((bar, i) => {
      gsap.set(bar, {
        height: '0%',
        scaleY: 0.08,
        transformOrigin: '50% 100%',
        opacity: 0.65,
        willChange: 'transform, height, opacity'
      });

      gsap.to(bar, {
        height: bar.dataset.pct + '%',
        scaleY: 1,
        opacity: 1,
        duration: 1.25,
        delay: i * 0.14,
        ease: 'back.out(1.35)',
        overwrite: 'auto',
        scrollTrigger: {
          trigger: bar,
          start: 'top 95%',
          toggleActions: 'play none none none'
        },
        onComplete: () => {
          gsap.set(bar, { clearProps: 'willChange' });
        }
      });
    });
  </script>

</body>

</html>
