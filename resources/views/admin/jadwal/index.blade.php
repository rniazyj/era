<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jadwal Interview - Galeri Magang</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  {{-- Alpine.js: dibutuhkan partials/sidebar.blade.php (lipat/buka sidebar & drawer mobile) --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
  <script>
    tailwind.config = { theme: { extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
      colors: { paper:'#faf9fc', lime:'#e9f56b', brand:'#34d3b0', ewforange:'#f97316' } } } };
  </script>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

@php
  $tab = [
    'mendatang'  => 'Mendatang',
    'hariini'    => 'Hari ini',
    'terlewat'   => 'Perlu tindak lanjut',
    'selesai'    => 'Selesai',
    'dibatalkan' => 'Dibatalkan',
    'semua'      => 'Semua',
  ];
  $namaHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

  // Helper URL (menjaga pencarian & mode tampilan)
  $url = fn (array $p = []) => route('admin.jadwal.index', array_filter(array_merge(['q' => $cari, 'bulan' => $bulan->format('Y-m'), 'tampil' => $tampil], $p), fn ($v) => $v !== null && $v !== ''));

  $prev = $bulan->copy()->subMonth()->format('Y-m');
  $next = $bulan->copy()->addMonth()->format('Y-m');
@endphp

<div class="flex min-h-screen flex-col lg:flex-row">

  @include('partials.sidebar')

  <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-6 sm:pt-8 lg:pt-10 pb-20 sm:pb-24 space-y-6 sm:space-y-8">

    {{-- Header --}}
    <div class="w-full max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div>
        <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">Panel Administrator</span>
        <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2">Jadwal Interview</h1>
        <p class="text-xs text-zinc-400 mt-1">Atur, ubah, dan pantau jadwal interview semua pelamar.</p>
      </div>
      <div class="flex items-center gap-3">
        <button type="button" onclick="bukaFormJadwal({mode:'baru'})" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-ewforange hover:bg-orange-600 text-white text-xs font-semibold shadow-sm transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
          Atur Jadwal Baru
        </button>
      </div>
    </div>

    {{-- Notifikasi (error validasi tampil di dalam modal) --}}
    <div class="w-full max-w-6xl mx-auto space-y-3 empty:hidden">
      @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-xs font-semibold px-4 py-3">✓ {{ session('success') }}</div>
      @endif
      @if(session('warning'))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 text-amber-800 text-xs font-semibold px-4 py-3">⚠ {{ session('warning') }}</div>
      @endif
    </div>

    {{-- Statistik --}}
    <div class="w-full max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-4">
      <a href="{{ $url(['tampil' => 'hariini']) . '#daftar' }}" class="rounded-2xl bg-white border border-black/5 p-5 shadow-sm hover:border-ewforange/40 transition">
        <p class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wide">Hari ini</p>
        <p class="mt-1 text-3xl font-bold tracking-tight">{{ $stat['hariini'] }}</p>
      </a>
      <a href="{{ $url(['tampil' => 'mendatang']) . '#daftar' }}" class="rounded-2xl bg-white border border-black/5 p-5 shadow-sm hover:border-ewforange/40 transition">
        <p class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wide">7 hari ke depan</p>
        <p class="mt-1 text-3xl font-bold tracking-tight">{{ $stat['pekan'] }}</p>
      </a>
      <a href="{{ $url(['tampil' => 'terlewat']) . '#daftar' }}" class="rounded-2xl bg-white border border-black/5 p-5 shadow-sm hover:border-amber-300 transition">
        <p class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wide">Perlu tindak lanjut</p>
        <p class="mt-1 text-3xl font-bold tracking-tight {{ $stat['terlewat'] > 0 ? 'text-amber-600' : '' }}">{{ $stat['terlewat'] }}</p>
        <p class="text-[11px] text-zinc-400">Tanggal sudah lewat, belum ditandai selesai</p>
      </a>
    </div>

    {{-- ====================================================================== --}}
    {{-- TAMPILAN KALENDER                                                      --}}
    {{-- ====================================================================== --}}

      {{-- Toolbar bulan --}}
      <div class="w-full max-w-6xl mx-auto flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div class="flex items-center gap-2">
          <a href="{{ $url(['bulan' => $prev]) }}" aria-label="Bulan sebelumnya" class="w-9 h-9 grid place-items-center rounded-full bg-white border border-black/10 hover:border-ewforange hover:text-ewforange transition">‹</a>
          <h2 class="min-w-[9.5rem] text-center text-base sm:text-lg font-bold tracking-tight">{{ $bulan->copy()->locale('id')->translatedFormat('F Y') }}</h2>
          <a href="{{ $url(['bulan' => $next]) }}" aria-label="Bulan berikutnya" class="w-9 h-9 grid place-items-center rounded-full bg-white border border-black/10 hover:border-ewforange hover:text-ewforange transition">›</a>
          <a href="{{ $url(['bulan' => today()->format('Y-m')]) }}" class="ml-1 px-4 py-2 rounded-full bg-white border border-black/10 text-xs font-semibold text-zinc-600 hover:border-ewforange hover:text-ewforange transition">Hari ini</a>
        </div>

        <form method="GET" action="{{ route('admin.jadwal.index') }}" class="flex gap-2">
          <input type="hidden" name="bulan" value="{{ $bulan->format('Y-m') }}">
          <input type="hidden" name="tampil" value="{{ $tampil }}">
          <input type="search" name="q" value="{{ $cari }}" placeholder="Cari nama pelamar…" class="w-full lg:w-64 rounded-full border border-black/10 bg-white px-4 py-2 text-xs focus:outline-none focus:border-ewforange">
          <button class="px-4 py-2 rounded-full bg-zinc-900 text-white text-xs font-semibold">Cari</button>
        </form>
      </div>

      <div class="w-full max-w-6xl mx-auto grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_24rem] gap-6 items-start">

        {{-- Grid kalender --}}
        <div class="rounded-3xl bg-white border border-black/5 shadow-sm p-3 sm:p-5">
          <div class="grid grid-cols-7 mb-2">
            @foreach($namaHari as $i => $h)
              <div class="text-center text-[10px] sm:text-[11px] font-bold uppercase tracking-wide py-1 {{ $i >= 5 ? 'text-rose-400' : 'text-zinc-400' }}">{{ $h }}</div>
            @endforeach
          </div>

          <div class="grid grid-cols-7 gap-1 sm:gap-1.5">
            @foreach(\Carbon\CarbonPeriod::create($awalGrid, $akhirGrid) as $d)
              @php
                $tgl      = $d->toDateString();
                $items    = $kalender->get($tgl, collect());
                $diBulan  = $d->isSameMonth($bulan);
                $adalahIni = $tgl === $hariIni;
                $lalu     = $tgl < $hariIni;
                $label    = $d->copy()->locale('id')->translatedFormat('l, d F Y');
                $butuh    = $items->contains(fn ($x) => $x->terlewat);
              @endphp
              <button type="button"
                      class="sel-hari relative text-left rounded-xl sm:rounded-2xl border p-1.5 sm:p-2 min-h-[3.75rem] sm:min-h-[6.5rem] flex flex-col gap-1 transition hover:border-ewforange/60 hover:bg-orange-50/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-ewforange
                             {{ $diBulan ? 'bg-white border-black/5' : 'bg-zinc-50/60 border-transparent text-zinc-400' }}"
                      data-tgl="{{ $tgl }}" data-label="{{ $label }}" data-lalu="{{ $lalu ? 1 : 0 }}" data-jumlah="{{ $items->count() }}"
                      aria-label="{{ $label }}, {{ $items->count() }} jadwal" aria-pressed="false">

                <span class="inline-flex items-center justify-center w-6 h-6 sm:w-7 sm:h-7 rounded-full text-[11px] sm:text-xs font-bold
                             {{ $adalahIni ? 'bg-ewforange text-white' : ($diBulan ? 'text-zinc-800' : 'text-zinc-400') }}">{{ $d->day }}</span>

                @if($butuh)
                  <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-amber-500" title="Ada jadwal yang perlu tindak lanjut"></span>
                @endif

                {{-- Desktop: chip jam + nama --}}
                <div class="hidden sm:flex flex-col gap-1 w-full">
                  @foreach($items->take(2) as $x)
                    <span class="truncate rounded-md px-1.5 py-0.5 text-[10px] font-semibold {{ $x->chip_kelas }}">{{ $x->jam }} {{ $x->lamaran->pelamar->name ?? 'Pelamar' }}</span>
                  @endforeach
                  @if($items->count() > 2)
                    <span class="text-[10px] font-semibold text-zinc-400 px-1">+{{ $items->count() - 2 }} lagi</span>
                  @endif
                </div>

                {{-- Mobile: titik --}}
                @if($items->isNotEmpty())
                  <div class="flex sm:hidden flex-wrap gap-0.5">
                    @foreach($items->take(4) as $x)
                      <span class="w-1.5 h-1.5 rounded-full {{ $x->titik_kelas }}"></span>
                    @endforeach
                  </div>
                @endif
              </button>
            @endforeach
          </div>

          {{-- Legenda --}}
          <div class="flex flex-wrap gap-x-4 gap-y-1 mt-4 text-[11px] text-zinc-500">
            <span class="inline-flex items-center gap-1.5"><i class="w-2 h-2 rounded-full bg-orange-500"></i>Terjadwal</span>
            <span class="inline-flex items-center gap-1.5"><i class="w-2 h-2 rounded-full bg-amber-500"></i>Terlewat (perlu tindak lanjut)</span>
            <span class="inline-flex items-center gap-1.5"><i class="w-2 h-2 rounded-full bg-emerald-500"></i>Selesai</span>
            <span class="inline-flex items-center gap-1.5"><i class="w-2 h-2 rounded-full bg-zinc-300"></i>Dibatalkan</span>
          </div>
        </div>

        {{-- Panel agenda tanggal terpilih --}}
        <aside id="panelAgenda" class="rounded-3xl bg-white border border-black/5 shadow-sm p-4 sm:p-5 xl:sticky xl:top-6 space-y-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <p class="text-[10px] font-bold tracking-[.18em] text-zinc-400 uppercase">Agenda</p>
              <h3 id="panelJudul" class="text-sm font-bold text-zinc-900 mt-0.5">—</h3>
              <p id="panelHitung" class="text-[11px] text-zinc-400 mt-0.5"></p>
            </div>
            <button type="button" id="btnTambahHari" class="shrink-0 px-3 py-1.5 rounded-full bg-orange-50 text-orange-600 text-[11px] font-semibold hover:bg-orange-100 transition">+ Jadwal</button>
          </div>

          <div id="panelIsi" class="space-y-3">
            @foreach($kalender as $tgl => $items)
              <div class="agenda-hari hidden space-y-3" data-tgl="{{ $tgl }}">
                @foreach($items as $j)
                  @include('admin.jadwal._kartu', ['j' => $j, 'kompak' => true])
                @endforeach
              </div>
            @endforeach

            <div id="panelKosong" class="hidden rounded-2xl border border-dashed border-black/10 px-4 py-8 text-center">
              <p class="text-xs font-semibold text-zinc-600">Tidak ada interview di tanggal ini</p>
              <p class="text-[11px] text-zinc-400 mt-1">Klik "+ Jadwal" untuk menambahkan.</p>
            </div>
          </div>
        </aside>
      </div>

      <script>
        (function () {
          const sel    = [...document.querySelectorAll('.sel-hari')];
          const isi    = [...document.querySelectorAll('.agenda-hari')];
          const judul  = document.getElementById('panelJudul');
          const hitung = document.getElementById('panelHitung');
          const kosong = document.getElementById('panelKosong');
          const tambah = document.getElementById('btnTambahHari');
          const panel  = document.getElementById('panelAgenda');
          let aktif = null;

          function pilih(tgl, gulir) {
            const sel1 = sel.find(s => s.dataset.tgl === tgl);
            if (!sel1) return;
            aktif = tgl;

            sel.forEach(s => {
              const on = s === sel1;
              s.setAttribute('aria-pressed', on ? 'true' : 'false');
              s.classList.toggle('ring-2', on);
              s.classList.toggle('ring-ewforange', on);
              s.classList.toggle('bg-orange-50/60', on);
            });

            let ada = 0;
            isi.forEach(b => {
              const cocok = b.dataset.tgl === tgl;
              b.classList.toggle('hidden', !cocok);
              if (cocok) ada = b.children.length;
            });

            judul.textContent  = sel1.dataset.label;
            hitung.textContent = ada ? ada + ' jadwal' : '';
            kosong.classList.toggle('hidden', ada > 0);
            tambah.classList.toggle('hidden', sel1.dataset.lalu === '1');   // tidak bisa menjadwal di masa lalu

            if (gulir && window.innerWidth < 1280) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }

          sel.forEach(s => s.addEventListener('click', () => pilih(s.dataset.tgl, true)));
          tambah.addEventListener('click', () => bukaFormJadwal({ mode: 'baru', tgl: aktif }));

          pilih(@json($pilih), false);
        })();
      </script>

    {{-- ====================================================================== --}}
    {{-- DAFTAR INTERVIEW (di bawah kalender)                                   --}}
    {{-- ====================================================================== --}}
    <div id="daftar" class="w-full max-w-6xl mx-auto scroll-mt-6">
      <h2 class="text-lg font-bold tracking-tight">Daftar Interview</h2>
      <p class="text-xs text-zinc-400 mt-0.5">Semua jadwal, dikelompokkan per tanggal. Gunakan tab untuk menyaring.</p>
    </div>

      {{-- Filter & pencarian --}}
      <div class="w-full max-w-6xl mx-auto">
        <div class="flex flex-wrap gap-2">
          @foreach($tab as $kode => $label)
            <a href="{{ $url(['tampil' => $kode]) . '#daftar' }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold border transition {{ $tampil === $kode ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-600 border-black/10 hover:border-ewforange hover:text-ewforange' }}">{{ $label }}</a>
          @endforeach
        </div>
      </div>

      {{-- Agenda per tanggal --}}
      <div class="w-full max-w-6xl mx-auto space-y-8">
        @forelse($perTanggal as $tgl => $items)
          @php $c = \Carbon\Carbon::parse($tgl)->locale('id'); @endphp
          <section>
            <div class="flex items-center gap-3 mb-3">
              <h2 class="text-sm font-bold text-zinc-900">{{ $c->translatedFormat('l, d F Y') }}</h2>
              @if($c->isToday()) <span class="text-[10px] font-bold bg-orange-50 text-ewforange px-2.5 py-1 rounded-full">Hari ini</span>
              @elseif($c->isTomorrow()) <span class="text-[10px] font-bold bg-zinc-100 text-zinc-600 px-2.5 py-1 rounded-full">Besok</span> @endif
              <span class="flex-1 h-px bg-black/5"></span>
              <span class="text-[11px] text-zinc-400">{{ $items->count() }} jadwal</span>
            </div>

            <div class="space-y-3">
              @foreach($items as $j)
                @include('admin.jadwal._kartu', ['j' => $j])
              @endforeach
            </div>
          </section>
        @empty
          <div class="rounded-3xl border border-dashed border-black/10 bg-white px-6 py-14 text-center">
            <p class="text-sm font-semibold text-zinc-700">Belum ada jadwal pada tampilan ini</p>
            <p class="text-xs text-zinc-400 mt-1">Klik "Atur Jadwal Baru" untuk menjadwalkan interview pelamar.</p>
          </div>
        @endforelse
      </div>

  </main>
</div>

{{-- Modal form tunggal (juga dipakai halaman detail pelamar) --}}
@include('admin.jadwal._form-jadwal', ['lamaranOpsi' => $lamaranOpsi])

</body>
</html>