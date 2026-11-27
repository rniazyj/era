<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Galeri Magang</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Caveat:wght@500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
      colors: { paper:'#faf9fc', lime:'#e9f56b', brand:'#34d3b0', ewforange:'#f97316' } } } };
  </script>
  <style>
  /* Background card statistik */
  .stat-card {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    transition:
      transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
      box-shadow 0.35s ease;
    will-change: transform;
  }

  /* Gradient bergerak */
  .stat-card-orange {
    background: linear-gradient(
      125deg,
      #f97316,
      #fb923c,
      #ea580c,
      #f97316
    );
    background-size: 300% 300%;
    animation: orangeGradient 8s ease infinite;
  }

  .stat-card-gray {
    background: linear-gradient(
      125deg,
      #52525b,
      #71717a,
      #3f3f46,
      #52525b
    );
    background-size: 300% 300%;
    animation: grayGradient 8s ease infinite;
  }

  @keyframes orangeGradient {
    0%, 100% {
      background-position: 0% 50%;
    }
    50% {
      background-position: 100% 50%;
    }
  }

  @keyframes grayGradient {
    0%, 100% {
      background-position: 100% 50%;
    }
    50% {
      background-position: 0% 50%;
    }
  }

  /* Container dekorasi */
.stat-decoration {
  position: absolute;
  z-index: 0;
  pointer-events: none;
  border-radius: 50%;

  /* Putih transparan dan blur */
  background: rgba(255, 255, 255, 0.28);
  filter: blur(12px);

  /* Animasi bergerak */
  will-change: transform;
}

/* Lingkaran pertama */
.stat-decoration-one {
  width: 190px;
  height: 190px;

  top: -65px;
  right: -55px;

  background: rgba(255, 255, 255, 0.30);
  filter: blur(12px);

  animation: circleMoveOne 12s ease-in-out infinite alternate;
}

/* Lingkaran kedua */
.stat-decoration-two {
  width: 140px;
  height: 140px;

  bottom: -55px;
  right: 35px;

  background: rgba(255, 255, 255, 0.20);
  filter: blur(16px);

  animation: circleMoveTwo 15s ease-in-out infinite alternate;
}

/* Gerakan lingkaran pertama */
@keyframes circleMoveOne {
  0% {
    transform: translate(0, 0) scale(1);
  }

  25% {
    transform: translate(-90px, 55px) scale(1.1);
  }

  50% {
    transform: translate(-150px, 125px) scale(0.95);
  }

  75% {
    transform: translate(-60px, 180px) scale(1.15);
  }

  100% {
    transform: translate(-180px, 220px) scale(1);
  }
}

/* Gerakan lingkaran kedua */
@keyframes circleMoveTwo {
  0% {
    transform: translate(0, 0) scale(1);
  }

  25% {
    transform: translate(-100px, -80px) scale(1.15);
  }

  50% {
    transform: translate(-180px, -160px) scale(0.9);
  }

  75% {
    transform: translate(-70px, -220px) scale(1.1);
  }

  100% {
    transform: translate(-200px, -260px) scale(1);
  }
}

/* Menghormati pengaturan reduced motion */
@media (prefers-reduced-motion: reduce) {
  .stat-decoration-one,
  .stat-decoration-two {
    animation: none;
  }
}

  /* Icon card */
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

  /* Animasi hover */
  .stat-card:hover {
    transform: translateY(-6px) scale(1.015);
    box-shadow: 0 18px 35px rgba(39, 39, 42, 0.16);
  }

  /* Animasi saat card muncul */
  .stat-card-inner {
    opacity: 0;
    transform: translateY(18px);
  }

  @media (prefers-reduced-motion: reduce) {
    .stat-card-orange,
    .stat-card-gray {
      animation: none;
    }

    .stat-card {
      transition: none;
    }
  }

    .dashboard-card { min-height: 11rem; border: 1px solid rgba(24,24,27,.07); background: rgba(255,255,255,.96); box-shadow: 0 8px 24px rgba(24,24,27,.04); }
    .dashboard-card:hover { border-color: rgba(249,115,22,.18); }
    .quick-action { min-height: 3.8rem; transition: transform .2s ease, border-color .2s ease, background .2s ease; }
    .quick-action:hover { transform: translateY(-2px); border-color: rgba(249,115,22,.25); background: #fffaf7; }
    .trend-bar { width: 100%; max-height: 110px; border-radius: 999px 999px 8px 8px; background: #f97316; transition: height .5s ease; }
    .pipeline-track { background: #f4f4f5; }
</style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

  <!-- WRAPPER UTAMA: Sidebar dan Konten Berjajar Samping -->
  <div class="flex min-h-screen flex-col lg:flex-row">

    <!-- Memanggil Sidebar -->
    @include('partials.sidebar')

    <!-- KONTEN UTAMA DASHBOARD BARU -->
    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-20 space-y-6">
      @php
        $totalStatus = max(1, collect($ringkasanStatus)->sum());
        $diajukan = (int) ($ringkasanStatus['diajukan'] ?? 0);
        $diproses = (int) ($ringkasanStatus['diproses'] ?? $pelamarDiproses);
        $diterima = (int) ($ringkasanStatus['diterima'] ?? $pelamarDiterima);
        $nilaiMaxTrend = max(1, collect($trenLamaran)->max('jumlah'));
      @endphp

      <div class="w-full max-w-6xl mx-auto flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-end">
        <div>
          <span class="inline-flex rounded-full bg-orange-50 px-3 py-1 text-[10px] font-bold uppercase tracking-[.2em] text-ewforange">Panel Administrator</span>
          <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Dashboard Pengawasan</h1>
          <p class="mt-1 text-xs text-zinc-400">Ringkasan aktivitas rekrutmen dan tindakan yang perlu diperhatikan.</p>
        </div>
        <div class="grid w-full grid-cols-2 gap-2 sm:w-auto lg:flex lg:items-center">
          <a href="{{ route('admin.lowongan.index') }}" class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-full border border-zinc-200 bg-white px-4 text-xs font-bold text-zinc-700 transition hover:border-orange-300 hover:text-orange-600">Kelola Lowongan</a>
          <a href="{{ route('admin.pelamar.index') }}" class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-full bg-zinc-900 px-4 text-xs font-bold text-white transition hover:bg-black">Tinjau Pelamar</a>
        </div>
      </div>

      <div class="mx-auto grid w-full max-w-6xl grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('admin.pelamar.index') }}" class="dashboard-card group relative overflow-hidden rounded-3xl p-5">
          <div class="flex items-start justify-between">
            <span class="grid h-10 w-10 place-items-center rounded-2xl bg-orange-50 text-orange-600">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm10 1v6m3-3h-6"/></svg>
            </span>
            <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-bold text-orange-600">Perlu tinjau</span>
          </div>
          <p class="mt-5 text-[11px] font-bold uppercase tracking-wide text-zinc-400">Lamaran Baru</p>
          <p class="mt-1 text-4xl font-bold tracking-tight text-zinc-900">{{ $totalPelamarBaru }}</p>
        </a>

        <a href="{{ route('admin.jadwal.index') }}" class="dashboard-card group rounded-3xl p-5">
          <div class="flex items-start justify-between">
            <span class="grid h-10 w-10 place-items-center rounded-2xl bg-emerald-50 text-emerald-600">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </span>
            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Hari ini</span>
          </div>
          <p class="mt-5 text-[11px] font-bold uppercase tracking-wide text-zinc-400">Jadwal Interview</p>
          <p class="mt-1 text-4xl font-bold tracking-tight text-zinc-900">{{ $jadwalHariIni }}</p>
        </a>

        <a href="{{ route('admin.lowongan.index') }}" class="dashboard-card group rounded-3xl p-5">
          <div class="flex items-start justify-between">
            <span class="grid h-10 w-10 place-items-center rounded-2xl bg-sky-50 text-sky-600">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 12h18"/></svg>
            </span>
            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-[10px] font-bold text-sky-700">Aktif</span>
          </div>
          <p class="mt-5 text-[11px] font-bold uppercase tracking-wide text-zinc-400">Lowongan & Program</p>
          <p class="mt-1 text-4xl font-bold tracking-tight text-zinc-900">{{ $lowonganAktif }}</p>
        </a>

        <a href="{{ route('admin.jadwal.index') }}" class="dashboard-card group rounded-3xl p-5">
          <div class="flex items-start justify-between">
            <span class="grid h-10 w-10 place-items-center rounded-2xl bg-amber-50 text-amber-600">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </span>
            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700">Tindak lanjut</span>
          </div>
          <p class="mt-5 text-[11px] font-bold uppercase tracking-wide text-zinc-400">Jadwal Terlewat</p>
          <p class="mt-1 text-4xl font-bold tracking-tight text-zinc-900">{{ $jadwalPerluTindakLanjut }}</p>
        </a>
      </div>

      <div class="mx-auto grid w-full max-w-6xl grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1.55fr)_minmax(18rem,.75fr)]">
        <section class="dashboard-card rounded-3xl p-5 sm:p-6">
          <div class="flex items-start justify-between gap-3">
            <div><p class="text-sm font-bold text-zinc-900">Lamaran 7 Hari Terakhir</p><p class="mt-1 text-[11px] text-zinc-400">Jumlah pengajuan pelamar per hari</p></div>
            <span class="rounded-full bg-zinc-100 px-3 py-1 text-[10px] font-bold text-zinc-600">{{ collect($trenLamaran)->sum('jumlah') }} lamaran</span>
          </div>
          <div class="mt-6 flex h-36 items-end gap-2 sm:gap-4">
            @foreach($trenLamaran as $item)
              @php $tinggi = $item['jumlah'] > 0 ? max(18, round(($item['jumlah'] / $nilaiMaxTrend) * 100)) : 8; @endphp
              <div class="flex h-full flex-1 flex-col justify-end gap-2 text-center">
                <span class="text-[10px] font-bold text-zinc-500">{{ $item['jumlah'] }}</span>
                <div class="flex h-[105px] items-end rounded-t-xl bg-zinc-50 px-1">
                  <div class="trend-bar" style="height: {{ $tinggi }}%"></div>
                </div>
                <span class="text-[10px] font-semibold text-zinc-400">{{ $item['label'] }}</span>
              </div>
            @endforeach
          </div>
        </section>

        <section class="dashboard-card rounded-3xl p-5 sm:p-6">
          <div class="flex items-start justify-between"><div><p class="text-sm font-bold text-zinc-900">Status Lamaran</p><p class="mt-1 text-[11px] text-zinc-400">Progres seluruh lamaran</p></div></div>
          @php
            $pDiajukan = round(($diajukan / $totalStatus) * 100);
            $pDiproses = round(($diproses / $totalStatus) * 100);
            $pDiterima = round(($diterima / $totalStatus) * 100);
          @endphp
          <div class="mt-6 flex justify-center">
            <div class="relative grid h-40 w-40 place-items-center rounded-full" style="background: conic-gradient(#f97316 0 {{ $pDiajukan }}%, #38bdf8 {{ $pDiajukan }}% {{ $pDiajukan + $pDiproses }}%, #10b981 {{ $pDiajukan + $pDiproses }}% 100%)">
              <div class="grid h-28 w-28 place-items-center rounded-full bg-white text-center"><div><p class="text-3xl font-bold text-zinc-900">{{ $totalStatus }}</p><p class="text-[10px] font-semibold text-zinc-400">Total Lamaran</p></div></div>
            </div>
          </div>
          <div class="mt-5 grid grid-cols-3 gap-2 text-center">
            <div><span class="mx-auto block h-2 w-2 rounded-full bg-orange-500"></span><p class="mt-1 text-lg font-bold">{{ $diajukan }}</p><p class="text-[10px] text-zinc-400">Diajukan</p></div>
            <div><span class="mx-auto block h-2 w-2 rounded-full bg-sky-400"></span><p class="mt-1 text-lg font-bold">{{ $diproses }}</p><p class="text-[10px] text-zinc-400">Diproses</p></div>
            <div><span class="mx-auto block h-2 w-2 rounded-full bg-emerald-500"></span><p class="mt-1 text-lg font-bold">{{ $diterima }}</p><p class="text-[10px] text-zinc-400">Diterima</p></div>
          </div>
        </section>
      </div>

      <div class="mx-auto grid w-full max-w-6xl grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1.55fr)_minmax(18rem,.75fr)]">
        <section class="dashboard-card rounded-3xl p-5 sm:p-6">
          <div class="flex items-center justify-between gap-3"><div><p class="text-sm font-bold text-zinc-900">Jadwal Mendatang</p><p class="mt-1 text-[11px] text-zinc-400">{{ $jadwalPekanIni }} interview dalam 7 hari ke depan</p></div><a href="{{ route('admin.jadwal.index') }}" class="text-[11px] font-bold text-orange-600 hover:text-orange-700">Lihat semua</a></div>
          <div class="mt-4 divide-y divide-zinc-100">
            @forelse($jadwalTerbaru as $jadwal)
              @php $pelamar = $jadwal->lamaran->pelamar ?? null; $posisi = $jadwal->lamaran->nama_posisi ?? '-'; $inisial = mb_strtoupper(mb_substr(trim($pelamar->name ?? 'P'), 0, 1)); @endphp
              <a href="{{ route('admin.jadwal.index') }}" class="flex items-center gap-3 py-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-zinc-900 text-xs font-bold text-white">{{ $inisial }}</span>
                <span class="min-w-0 flex-1"><span class="block truncate text-xs font-bold text-zinc-800">{{ $pelamar->name ?? 'Pelamar' }}</span><span class="block truncate text-[11px] text-zinc-400">{{ $posisi }}</span></span>
                <span class="text-right"><span class="block text-xs font-bold text-zinc-700">{{ $jadwal->jam }}</span><span class="block text-[10px] text-zinc-400">{{ $jadwal->tgl_interview->locale('id')->translatedFormat('d M') }}</span></span>
              </a>
            @empty
              <div class="py-8 text-center text-xs text-zinc-400">Belum ada jadwal mendatang.</div>
            @endforelse
          </div>
        </section>

        <section class="dashboard-card rounded-3xl p-5 sm:p-6">
          <p class="text-sm font-bold text-zinc-900">Aksi Cepat</p>
          <p class="mt-1 text-[11px] text-zinc-400">Menu yang sering membutuhkan tindakan</p>
          <div class="mt-4 space-y-2">
            <a href="{{ route('admin.pelamar.index') }}" class="quick-action flex items-center gap-3 rounded-2xl border border-zinc-100 px-3 py-3"><span class="grid h-9 w-9 place-items-center rounded-xl bg-orange-50 text-orange-600"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm10 1v6m3-3h-6"/></svg></span><span class="flex-1 text-xs font-bold text-zinc-700">Tinjau Pelamar Baru</span><span class="text-xs font-bold text-orange-600">{{ $totalPelamarBaru }}</span></a>
            <a href="{{ route('admin.jadwal.index') }}" class="quick-action flex items-center gap-3 rounded-2xl border border-zinc-100 px-3 py-3"><span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span><span class="flex-1 text-xs font-bold text-zinc-700">Atur Jadwal</span><svg class="h-4 w-4 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
            <a href="{{ route('admin.lowongan.index') }}" class="quick-action flex items-center gap-3 rounded-2xl border border-zinc-100 px-3 py-3"><span class="grid h-9 w-9 place-items-center rounded-xl bg-sky-50 text-sky-600"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></span><span class="flex-1 text-xs font-bold text-zinc-700">Kelola Lowongan</span><svg class="h-4 w-4 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
            <a href="{{ route('admin.konten.index') }}" class="quick-action flex items-center gap-3 rounded-2xl border border-zinc-100 px-3 py-3"><span class="grid h-9 w-9 place-items-center rounded-xl bg-violet-50 text-violet-600"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4z"/></svg></span><span class="flex-1 text-xs font-bold text-zinc-700">Perbarui Konten Website</span><svg class="h-4 w-4 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
          </div>
        </section>
      </div>

      <section class="dashboard-card mx-auto w-full max-w-6xl rounded-3xl p-5 sm:p-6">
        <div class="flex items-center justify-between gap-3"><div><p class="text-sm font-bold text-zinc-900">Pelamar Baru</p><p class="mt-1 text-[11px] text-zinc-400">Lamaran terbaru yang masih menunggu tinjauan</p></div><a href="{{ route('admin.pelamar.index') }}" class="text-[11px] font-bold text-orange-600 hover:text-orange-700">Buka daftar</a></div>
        <div class="mt-4 overflow-x-auto">
          <table class="w-full min-w-[680px] text-left text-xs">
            <thead><tr class="border-b border-zinc-100 text-[10px] uppercase tracking-wide text-zinc-400"><th class="pb-3">Pelamar</th><th class="pb-3">Posisi</th><th class="pb-3">Kategori</th><th class="pb-3">Tanggal</th><th class="pb-3 text-right">Aksi</th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
              @forelse($pelamarBaruList as $lamaran)
                @php $namaPosisi = $lamaran->id_program ? ($lamaran->program->nama_program ?? '-') : ($lamaran->lowongan->judul ?? '-'); $jenis = $lamaran->id_program ? 'Magang' : 'Kerja'; @endphp
                <tr><td class="py-3 font-bold text-zinc-800">{{ $lamaran->pelamar->name ?? 'Nama tidak ditemukan' }}</td><td class="py-3 text-zinc-600">{{ $namaPosisi }}</td><td class="py-3"><span class="rounded-full {{ $jenis === 'Magang' ? 'bg-orange-50 text-orange-600' : 'bg-zinc-100 text-zinc-700' }} px-2.5 py-1 text-[10px] font-bold">{{ $jenis }}</span></td><td class="py-3 text-zinc-400">{{ $lamaran->tgl_pengajuan ? $lamaran->tgl_pengajuan->format('d M Y') : '-' }}</td><td class="py-3 text-right"><a href="{{ route('admin.pelamar.show', $lamaran->id_lamaran) }}" class="rounded-full bg-zinc-900 px-3 py-1.5 text-[10px] font-bold text-white hover:bg-black">Lihat</a></td></tr>
              @empty
                <tr><td colspan="5" class="py-10 text-center text-zinc-400">Belum ada pelamar baru.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</body>
</html>