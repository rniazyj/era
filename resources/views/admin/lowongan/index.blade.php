@php
  $has = fn ($name) => \Illuminate\Support\Facades\Route::has($name);

  $createUrl = $has('admin.lowongan.create') ? route('admin.lowongan.create') : '#';
  $editUrl   = fn ($row) => $has('admin.lowongan.edit')
      ? route('admin.lowongan.edit', ['jenis' => strtolower($row->jenis), 'id' => $row->id])
      : '#';

  // Data dari AdminLowonganController@index (gabungan lowongan_kerja + program_magang)
  $rows = collect($items ?? [])->map(fn ($r) => (object) $r);

  $totalLowongan = $rows->where('jenis', 'Kerja')->count();
  $totalProgram  = $rows->where('jenis', 'Magang')->count();
  $totalDibuka   = $rows->where('status', 'dibuka')->count();
  $totalLain     = $rows->count() - $totalDibuka;

  $fmt = fn ($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d M Y') : '-';
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Kelola Lowongan - ERA</title>
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
  </style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

  <div class="flex min-h-screen flex-col lg:flex-row">

    @include('partials.sidebar')

    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-20 sm:pb-24 space-y-6 sm:space-y-8 lg:space-y-10">

      <div
        class="w-full max-w-6xl mx-auto space-y-6 sm:space-y-8 lg:space-y-10 min-w-0"
        x-data="{
          filter: 'Semua',
          q: '',
          visible(jenis, text) {
            return (this.filter === 'Semua' || this.filter === jenis) && text.includes(this.q.toLowerCase().trim());
          }
        }">

        <!-- HEADER HALAMAN -->
        <div class="scroll-rv flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 min-w-0">
          <div>
            <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">PANEL ADMINISTRATOR</span>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2 break-words">Kelola Lowongan</h1>
          </div>
          <a href="{{ $createUrl }}"
             class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-ewforange hover:bg-orange-600 text-white text-xs font-semibold rounded-full shadow-lg shadow-orange-500/30 transition hover:scale-105">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
            Tambah Lowongan
          </a>
        </div>

        @if(session('success'))
          <div class="scroll-rv rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-medium px-5 py-3">
            {{ session('success') }}
          </div>
        @endif

        <!-- KARTU STATISTIK -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 scroll-rv min-w-0">

          <div class="stat-card bg-white rounded-3xl border border-black/5 p-6 shadow-sm flex items-center justify-between cursor-pointer">
            <div>
              <p class="text-xs font-medium text-zinc-400">Total Lowongan</p>
              <h3 class="text-3xl font-bold text-zinc-900 mt-1 count-target" data-target="{{ $totalLowongan }}">0</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-ewforange flex items-center justify-center text-xl font-bold">💼</div>
          </div>

          <div class="stat-card bg-white rounded-3xl border border-black/5 p-6 shadow-sm flex items-center justify-between cursor-pointer">
            <div>
              <p class="text-xs font-medium text-zinc-400">Total Program</p>
              <h3 class="text-3xl font-bold text-zinc-900 mt-1 count-target" data-target="{{ $totalProgram }}">0</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center text-xl font-bold">🎓</div>
          </div>

          <div class="stat-card bg-white rounded-3xl border border-black/5 p-6 shadow-sm flex items-center justify-between cursor-pointer">
            <div>
              <p class="text-xs font-medium text-zinc-400">Sedang Dibuka</p>
              <h3 class="text-3xl font-bold text-zinc-900 mt-1 count-target" data-target="{{ $totalDibuka }}">0</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl font-bold">✅</div>
          </div>

          <div class="stat-card bg-white rounded-3xl border border-black/5 p-6 shadow-sm flex items-center justify-between cursor-pointer">
            <div>
              <p class="text-xs font-medium text-zinc-400">Draft / Ditutup</p>
              <h3 class="text-3xl font-bold text-zinc-900 mt-1 count-target" data-target="{{ $totalLain }}">0</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-500 flex items-center justify-center text-xl font-bold">🔒</div>
          </div>

        </div>

        <!-- DAFTAR LOWONGAN -->
        <div class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 lg:p-10 shadow-sm space-y-4 sm:space-y-6 min-w-0">

          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 pb-4 border-b border-black/5 min-w-0">
            <div>
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">Daftar Lowongan</h3>
              <p class="text-[11px] text-zinc-400 mt-0.5">Lowongan kerja dan program magang yang sudah dibuat.</p>
            </div>
            <span class="text-xs bg-orange-50 text-ewforange font-bold px-3 py-1 rounded-full self-start sm:self-auto">{{ $rows->count() }} Data</span>
          </div>

          <!-- Filter & Pencarian -->
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex flex-wrap gap-2">
              @foreach(['Semua', 'Magang', 'Kerja'] as $tab)
                <button type="button" @click="filter = '{{ $tab }}'"
                        :class="filter === '{{ $tab }}' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200'"
                        class="px-4 py-1.5 rounded-full text-[11px] font-semibold transition">
                  {{ $tab }}
                </button>
              @endforeach
            </div>
            <div class="relative w-full md:w-72">
              <svg class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
              <input type="text" x-model="q" placeholder="Cari judul lowongan..." class="field !pl-10 !rounded-full">
            </div>
          </div>

          <div class="w-full min-w-0 overflow-x-auto overscroll-x-contain">
            <table class="w-full min-w-[860px] text-left border-collapse">
              <thead>
                <tr class="border-b border-black/5 text-[11px] font-semibold text-zinc-400 uppercase">
                  <th class="py-3 px-4">Judul</th>
                  <th class="py-3 px-4">Kategori</th>
                  <th class="py-3 px-4">Pendaftaran</th>
                  <th class="py-3 px-4">Durasi</th>
                  <th class="py-3 px-4 text-center">Kuota</th>
                  <th class="py-3 px-4">Status</th>
                  <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black/5 text-xs">
                @forelse($rows as $row)
                  @php
                    $statusClass = match ($row->status) {
                      'dibuka'  => ['bg-emerald-50 text-emerald-600', 'bg-emerald-500'],
                      'ditutup' => ['bg-zinc-100 text-zinc-500', 'bg-zinc-400'],
                      default   => ['bg-amber-50 text-amber-600', 'bg-amber-500'],
                    };
                  @endphp
                  <tr class="table-row-item table-row-hover"
                      x-show="visible(@js($row->jenis), @js(strtolower($row->judul)))">
                    <td class="py-4 px-4">
                      <p class="font-bold text-zinc-900">{{ $row->judul }}</p>
                      <p class="text-[11px] text-zinc-400 mt-0.5 line-clamp-1 max-w-xs">{{ $row->deskripsi }}</p>
                    </td>
                    <td class="py-4 px-4">
                      <span class="px-2.5 py-1 rounded-md font-semibold text-[10px] {{ $row->jenis == 'Magang' ? 'bg-orange-50 text-ewforange' : 'bg-zinc-100 text-zinc-800' }}">
                        {{ $row->jenis }}
                      </span>
                    </td>
                    <td class="py-4 px-4 text-zinc-600 whitespace-nowrap">
                      {{ $fmt($row->tgl_buka) }}<br>
                      <span class="text-zinc-400">s/d {{ $fmt($row->tgl_tutup) }}</span>
                    </td>
                    <td class="py-4 px-4 text-zinc-600">
                      @if($row->jenis == 'Magang' && $row->durasi_hari)
                        {{ $row->durasi_hari }} Hari
                      @else
                        -
                      @endif
                    </td>
                    <td class="py-4 px-4 text-center font-semibold text-zinc-700">{{ $row->kuota ?? '-' }}</td>
                    <td class="py-4 px-4">
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold text-[10px] capitalize {{ $statusClass[0] }}">
                        <i class="w-1.5 h-1.5 rounded-full {{ $statusClass[1] }}"></i>
                        {{ $row->status }}
                      </span>
                    </td>
                    <td class="py-4 px-4 text-center">
                      <a href="{{ $editUrl($row) }}"
                         class="inline-flex items-center gap-1.5 px-4 py-2 bg-zinc-900 hover:bg-black text-white text-[11px] font-medium rounded-full transition shadow-sm hover:scale-105">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="py-12 text-center text-zinc-400">
                      Belum ada lowongan. Klik <span class="font-semibold text-ewforange">Tambah Lowongan</span> untuk membuat yang pertama.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
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
        gsap.to(card, { y: -6, scale: 1.02, duration: 0.3, ease: 'power2.out', overwrite: 'auto' });
      });
      card.addEventListener('pointerleave', () => {
        gsap.to(card, { y: 0, scale: 1, duration: 0.4, ease: 'power2.out', overwrite: 'auto' });
      });
    });
  </script>
</body>
</html>