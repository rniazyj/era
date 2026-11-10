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

  <!-- CARD 1: TOTAL PELAMAR BARU -->
  <div class="stat-card stat-card-orange rounded-3xl p-5 sm:p-7 shadow-sm cursor-pointer text-white min-w-0">

    <!-- Dekorasi background -->
    <div class="stat-decoration stat-decoration-one"></div>
    <div class="stat-decoration stat-decoration-two"></div>

    <div class="stat-card-inner relative z-10 flex flex-col gap-5">

      <!-- Header card -->
      <div class="flex items-center justify-between gap-3">

        <div class="flex items-center gap-3 min-w-0">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="24" height="24"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>

          <span class="text-xs sm:text-sm font-semibold text-white/90">
            Total Pelamar Baru
          </span>
        </div>

      </div>

      <!-- Angka statistik -->
      <div>
        <h3
          class="text-4xl sm:text-5xl font-bold tracking-tight count-target"
          data-target="{{ isset($pelamarBaruList) ? count($pelamarBaruList) : 0 }}">
          0
        </h3>

      </div>

      <!-- Footer card -->
      <div class="flex items-center justify-between gap-3 pt-4 border-t border-white/20">

        <span class="text-xs font-medium text-white/90">
          Perlu ditinjau
        </span>

        <span class="px-3 py-1.5 rounded-full bg-white text-orange-600 text-[10px] font-bold">
          Pelamar
        </span>

      </div>

    </div>
  </div>


  <!-- CARD 2: CV BELUM LENGKAP -->
  <div class="stat-card stat-card-gray rounded-3xl p-5 sm:p-7 shadow-sm cursor-pointer text-white min-w-0">

    <!-- Dekorasi background -->
    <div class="stat-decoration stat-decoration-one"></div>
    <div class="stat-decoration stat-decoration-two"></div>

    <div class="stat-card-inner relative z-10 flex flex-col gap-5">

      <!-- Header card -->
      <div class="flex items-center justify-between gap-3">

        <div class="flex items-center gap-3 min-w-0">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="24" height="24"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <path d="M14 2v6h6"/>
              <path d="M8 13h8"/>
              <path d="M8 17h5"/>
            </svg>
          </div>

          <span class="text-xs sm:text-sm font-semibold text-white/90">
            CV Belum Lengkap
          </span>
        </div>

      </div>

      <!-- Angka statistik -->
      <div>
        <h3
          class="text-4xl sm:text-5xl font-bold tracking-tight count-target"
          data-target="{{ isset($userBelumLengkapList) ? count($userBelumLengkapList) : 0 }}">
          0
        </h3>

      </div>

      <!-- Footer card -->
      <div class="flex items-center justify-between gap-3 pt-4 border-t border-white/20">

        <span class="text-xs font-medium text-white/90">
          Perlu dilengkapi
        </span>

        <span class="px-3 py-1.5 rounded-full bg-white text-zinc-700 text-[10px] font-bold">
          Profil & CV
        </span>

      </div>

    </div>
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

    // Animasi masuk untuk card statistik
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
      gsap.to(card, {
        y: -6,
        scale: 1.015,
        duration: 0.35,
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