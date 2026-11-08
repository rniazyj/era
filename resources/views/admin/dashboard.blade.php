<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - ERA</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Caveat:wght@500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
      colors: { paper:'#faf9fc', lime:'#e9f56b', brand:'#34d3b0', ewforange:'#f97316' } } } };
  </script>
  <style>
    .scroll-rv { opacity: 0; transform: translateY(40px); }
    .table-row-item { opacity: 0; transform: translateY(20px); }
    .table-row-hover { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
    .table-row-hover:hover { transform: translateY(-2px); background-color: rgba(249, 115, 22, 0.03); }
  </style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

  <!-- WRAPPER UTAMA: Sidebar dan Konten Berjajar Samping -->
  <div class="flex min-h-screen flex-col lg:flex-row">

    <!-- Memanggil Sidebar -->
    @include('partials.sidebar')

    <!-- KONTEN UTAMA HALAMAN -->
    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-6 sm:pt-8 lg:pt-10 pb-20 sm:pb-24 space-y-6 sm:space-y-8 lg:space-y-10">

      <!-- Header Judul Halaman -->
      <div class="w-full max-w-6xl mx-auto scroll-rv flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 min-w-0">
        <div>
          <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">PANEL ADMINISTRATOR</span>
          <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2 break-words">Dashboard Pengawasan</h1>
        </div>
      </div>

      <!-- KARTU STATISTIK RINGKAS -->
      <div class="w-full max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 scroll-rv min-w-0">
        <div class="stat-card bg-white rounded-3xl border border-black/5 p-6 shadow-sm flex items-center justify-between cursor-pointer">
          <div>
            <p class="text-xs font-medium text-zinc-400">Total Pelamar Baru</p>
            <h3 class="text-3xl font-bold text-zinc-900 mt-1 count-target" data-target="{{ isset($pelamarBaruList) ? count($pelamarBaruList) : 2 }}">0</h3>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-orange-50 text-ewforange flex items-center justify-center text-xl font-bold">📥</div>
        </div>

        <div class="stat-card bg-white rounded-3xl border border-black/5 p-6 shadow-sm flex items-center justify-between cursor-pointer">
          <div>
            <p class="text-xs font-medium text-zinc-400">User / Pelamar Belum Lengkap</p>
            <h3 class="text-3xl font-bold text-zinc-900 mt-1 count-target" data-target="{{ isset($userBelumLengkapList) ? count($userBelumLengkapList) : 1 }}">0</h3>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl font-bold">⚠️</div>
        </div>
      </div>

      <!-- BAGIAN 1: PELAMAR BARU -->
      <div class="w-full max-w-6xl mx-auto scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 lg:p-10 shadow-sm space-y-4 sm:space-y-6 min-w-0">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 pb-4 border-b border-black/5 min-w-0">
          <div>
            <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">Daftar Pelamar Baru</h3>
            <p class="text-[11px] text-zinc-400 mt-0.5">Kandidat magang yang baru saja mengirimkan berkas pengajuan.</p>
          </div>
          <span class="text-xs bg-orange-50 text-ewforange font-bold px-3 py-1 rounded-full">Perlu Ditinjau</span>
        </div>

        <div class="w-full min-w-0 overflow-x-auto overscroll-x-contain">
          <table class="w-full min-w-[680px] text-left border-collapse">
            <thead>
              <tr class="border-b border-black/5 text-[11px] font-semibold text-zinc-400 uppercase">
                <th class="py-3 px-4">Nama Pelamar</th>
                <th class="py-3 px-4">Posisi</th>
                <th class="py-3 px-4">Kategori</th>
                <th class="py-3 px-4">Durasi Magang</th>
                <th class="py-3 px-4">Tanggal Kirim</th>
                <th class="py-3 px-4 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-black/5 text-xs">
              @forelse($pelamarBaruList ?? [] as $pelamar)
                <tr class="table-row-item table-row-hover">
                  <td class="py-4 px-4 font-bold text-zinc-900">{{ $pelamar->user->name ?? 'Nama Pelamar' }}</td>
                  <td class="py-4 px-4 text-zinc-600">{{ $pelamar->posisi->nama_posisi ?? 'Konsultan Bisnis Intern' }}</td>
                  <td class="py-4 px-4">
                    <span class="px-2.5 py-1 rounded-md font-semibold text-[10px] {{ ($pelamar->jenis ?? 'Magang') == 'Magang' ? 'bg-orange-50 text-ewforange' : 'bg-zinc-100 text-zinc-800' }}">
                      {{ $pelamar->jenis ?? 'Magang' }}
                    </span>
                  </td>
                  <td class="py-4 px-4 text-zinc-600">{{ $pelamar->durasi ?? '-' }}</td>
                  <td class="py-4 px-4 text-zinc-400">{{ $pelamar->created_at ? $pelamar->created_at->format('d M Y') : '-' }}</td>
                  <td class="py-4 px-4 text-center">
                    <a href="#" class="inline-block px-4 py-2 bg-zinc-900 hover:bg-black text-white text-[11px] font-medium rounded-full transition shadow-sm hover:scale-105">Selengkapnya</a>
                  </td>
                </tr>
              @empty
                <tr class="table-row-item table-row-hover">
                  <td class="py-4 px-4 font-bold text-zinc-900">Rania Shava</td>
                  <td class="py-4 px-4 text-zinc-600">Konsultan Bisnis Intern</td>
                  <td class="py-4 px-4">
                    <span class="bg-orange-50 text-ewforange px-2.5 py-1 rounded-md font-semibold text-[10px]">Magang</span>
                  </td>
                  <td class="py-4 px-4 text-zinc-600">3 Bulan (90 Hari)</td>
                  <td class="py-4 px-4 text-zinc-400">04 Nov 2026</td>
                  <td class="py-4 px-4 text-center">
                    <a href="#" class="inline-block px-4 py-2 bg-zinc-900 hover:bg-black text-white text-[11px] font-medium rounded-full transition shadow-sm hover:scale-105">Selengkapnya</a>
                  </td>
                </tr>
                <tr class="table-row-item table-row-hover">
                  <td class="py-4 px-4 font-bold text-zinc-900">Inez Vina</td>
                  <td class="py-4 px-4 text-zinc-600">UI/UX Designer Intern</td>
                  <td class="py-4 px-4">
                    <span class="bg-zinc-100 text-zinc-800 px-2.5 py-1 rounded-md font-semibold text-[10px]">Kerja</span>
                  </td>
                  <td class="py-4 px-4 text-zinc-600">Full-Time</td>
                  <td class="py-4 px-4 text-zinc-400">03 Nov 2026</td>
                  <td class="py-4 px-4 text-center">
                    <a href="#" class="inline-block px-4 py-2 bg-zinc-900 hover:bg-black text-white text-[11px] font-medium rounded-full transition shadow-sm hover:scale-105">Selengkapnya</a>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- BAGIAN 2: USER / PELAMAR DENGAN CV & BERKAS BELUM LENGKAP -->
      <div class="w-full max-w-6xl mx-auto scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 lg:p-10 shadow-sm space-y-4 sm:space-y-6 min-w-0">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 pb-4 border-b border-black/5 min-w-0">
          <div>
            <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">User & CV Belum Lengkap</h3>
            <p class="text-[11px] text-zinc-400 mt-0.5">Daftar akun yang mendaftar namun belum melengkapi profil atau berkas persyaratannya.</p>
          </div>
          <span class="text-xs bg-rose-50 text-rose-500 font-bold px-3 py-1 rounded-full">Perlu Diperhatikan</span>
        </div>

        <div class="w-full min-w-0 overflow-x-auto overscroll-x-contain">
          <table class="w-full min-w-[420px] text-left border-collapse">
            <thead>
              <tr class="border-b border-black/5 text-[11px] font-semibold text-zinc-400 uppercase">
                <th class="py-3 px-4">Nama Pengguna</th>
                <th class="py-3 px-4">Email</th>
                <th class="py-3 px-4">No. Telepon</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-black/5 text-xs">
              @forelse($userBelumLengkapList ?? [] as $user)
                <tr class="table-row-item table-row-hover">
                  <td class="py-4 px-4 font-bold text-zinc-900">{{ $user->name }}</td>
                  <td class="py-4 px-4 text-zinc-600">{{ $user->email }}</td>
                  <td class="py-4 px-4 text-zinc-600">{{ $user->no_telepon ?? '-' }}</td>
                </tr>
              @empty
                <tr class="table-row-item table-row-hover">
                  <td class="py-4 px-4 font-bold text-zinc-900">Budi Santoso</td>
                  <td class="py-4 px-4 text-zinc-600">budi@gmail.com</td>
                  <td class="py-4 px-4 text-zinc-600">081298765432</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

  <!-- GSAP & ScrollTrigger Script -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
  <script>
    gsap.registerPlugin(ScrollTrigger);

    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    window.scrollTo(0, 0);

    const $ = (s, c = document) => [...c.querySelectorAll(s)];

    // 1. Animasi Transisi Saat Halaman Di-scroll (ScrollTrigger)
    $('.scroll-rv').forEach((section) => {
      gsap.fromTo(section, 
        { y: 50, opacity: 0 },
        { 
          y: 0, 
          opacity: 1, 
          duration: 1, 
          ease: 'power3.out',
          scrollTrigger: {
            trigger: section,
            start: 'top 85%',
            toggleActions: 'play none none none'
          }
        }
      );
    });

    // 2. Animasi Baris Tabel Muncul Mengalir Saat Scroll
    $('.table-row-item').forEach((row, index) => {
      gsap.fromTo(row,
        { y: 20, opacity: 0 },
        { 
          y: 0, 
          opacity: 1, 
          duration: 0.6, 
          delay: (index % 3) * 0.1, 
          ease: 'power2.out',
          scrollTrigger: {
            trigger: row,
            start: 'top 90%',
            toggleActions: 'play none none none'
          }
        }
      );
    });

    // 3. Efek Angka Berjalan (Count-Up Animation untuk Statistik)
    $('.count-target').forEach(counter => {
      const target = parseInt(counter.dataset.target) || 0;
      const obj = { val: 0 };
      gsap.to(obj, {
        val: target,
        duration: 1.5,
        ease: 'power1.out',
        scrollTrigger: {
          trigger: counter,
          start: 'top 85%',
          toggleActions: 'play none none none'
        },
        onUpdate: () => {
          counter.textContent = Math.round(obj.val);
        }
      });
    });

    // 4. Efek Interaktif Hover pada Kartu Statistik
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