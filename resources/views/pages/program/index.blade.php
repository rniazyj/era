<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Program Magang - PT Equityworld Futures</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { 
        theme: { 
            extend: {
                fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
                colors: { paper:'#faf9fc', ewforange:'#f97316' } 
            } 
        } 
    };
  </script>
  <style>
    @keyframes stroll-orange {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(250px, 120px) scale(1.3); }
  66% { transform: translate(-150px, 180px) scale(0.7); }
  100% { transform: translate(0px, 0px) scale(1); }
}

@keyframes stroll-blue {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(-250px, -180px) scale(1.4); }
  66% { transform: translate(180px, -120px) scale(0.6); }
  100% { transform: translate(0px, 0px) scale(1); }
}

.animate-stroll-orange {
  animation: stroll-orange 7s ease-in-out infinite;
}

.animate-stroll-blue {
  animation: stroll-blue 9s ease-in-out infinite;
}
    /* Utility pendukung animasi GSAP */
    .rv { opacity: 0; transform: translateY(30px); }
    /* Transisi untuk FAQ Accordion */
    .faq-content { display: grid; grid-template-rows: 0fr; transition: grid-template-rows 0.3s ease; }
    .faq-content.open { grid-template-rows: 1fr; }
    .faq-inner { overflow: hidden; }
    .faq-icon { transition: transform 0.3s ease; }
    .faq-btn.active .faq-icon { transform: rotate(180deg); }
  </style>
</head>
<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

<!-- NAVBAR -->
<header class="fixed top-0 inset-x-0 z-50 backdrop-blur-md bg-paper/70 border-b border-black/5">
  <nav class="max-w-7xl mx-auto px-6 h-16 grid grid-cols-[1fr_auto_1fr] items-center">
    <a href="{{ route('home') }}" class="text-sm font-bold text-black tracking-wide">LOGO ERA</a>
    <ul class="hidden md:flex items-center gap-10 text-sm font-medium">
      <li><a href="{{ route('home') }}" class="text-black/50 hover:text-black transition">Home</a></li>
      <li><a href="{{ route('tentang') }}" class="text-black/50 hover:text-black transition">About</a></li>
      <li><a href="#" class="text-black">Internship</a></li>
      <li><a href="{{ route('lowongan.index') }}" class="text-black/50 hover:text-black transition">Job</a></li>
    </ul>
    <div class="col-start-3 justify-self-end">
      <a href="#" class="inline-block px-5 py-1.5 rounded-full border border-black text-sm hover:bg-black hover:text-white transition">Get Started</a>
    </div>
  </nav>
</header>

<main>
<!-- 1. HERO & PENCARIAN -->
<section class="pt-40 pb-20 px-6 max-w-5xl mx-auto text-center rv">
  <h1 class="text-4xl md:text-6xl font-semibold tracking-tight leading-tight">
    <span class="italic">
    Temukan Program Magang<br>
    </span>
    <span class="text-ewforange relative inline-block">
      Yang Kamu Inginkan
      <svg class="absolute -bottom-2 left-0 w-full h-3 text-orange-200" viewBox="0 0 100 20" preserveAspectRatio="none"><path d="M0,10 Q50,20 100,10" stroke="currentColor" stroke-width="4" fill="transparent"/></svg>
    </span>
  </h1>
  
  <!-- Search Bar Card -->
  <div class="mt-12 bg-white rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-black/5 p-2 flex flex-col md:flex-row items-center gap-2 max-w-4xl mx-auto relative z-10">
    <div class="flex-1 flex items-center px-4 py-2 w-full md:border-r border-black/10">
      <span class="text-black/40 mr-3">🔍</span>
      <div class="text-left w-full">
        <label class="text-[10px] font-semibold text-black/70 tracking-wider block">POSISI / KEAHLIAN</label>
        <input type="text" placeholder="Contoh: Full-Stack Developer" class="w-full text-sm outline-none bg-transparent placeholder-black/30 mt-0.5 font-medium">
      </div>
    </div>
    <div class="flex-1 flex items-center px-4 py-2 w-full">
      <span class="text-black/40 mr-3">📍</span>
      <div class="text-left w-full">
        <label class="text-[10px] font-semibold text-black/70 tracking-wider block">LOKASI</label>
        <label class="w-full text-sm outline-none bg-transparent placeholder-black/90 mt-0.5 font-medium">Trillium Office & Residence, Surabaya</label>
      </div>
    </div>
    <button class="w-full md:w-auto px-8 py-4 md:py-3.5 bg-ewforange text-white text-sm font-semibold rounded-full hover:bg-orange-600 transition flex-shrink-0">
      Cari Lowongan →
    </button>
  </div>
</section>

<!-- 2. EKSPLORASI PROGRAM MAGANG -->
<section class="px-6 md:px-16 py-16 max-w-7xl mx-auto border-t border-black/5">
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 rv">
    <div>
      <h2 class="text-2xl md:text-3xl font-semibold tracking-tight">Eksplorasi Program Magang</h2>
      <p class="text-sm text-black/50 mt-1">Temukan program magang yang sesuai dengan minat dan keahlianmu.</p>
    </div>
    <div class="flex gap-2 text-sm">
      <button class="px-4 py-2 bg-zinc-900 text-white rounded-full font-medium">Semua Program (6)</button>
      <button class="px-4 py-2 bg-white border border-black/10 rounded-full font-medium hover:bg-black/5 text-black/60">Teknologi Informasi (2)</button>
      <button class="px-4 py-2 bg-white border border-black/10 rounded-full font-medium hover:bg-black/5 text-black/60">Bisnis & Sosial (4)</button>
    </div>
  </div>

  <div class="grid md:grid-cols-3 gap-5 rv">
    <!-- Highlighted Card (Data Analyst) -->
    <div class="bg-zinc-900 text-white rounded-3xl p-6 flex flex-col justify-between shadow-lg relative overflow-hidden group">
      <div class="absolute top-0 right-0 w-32 h-32 bg-ewforange/20 blur-3xl rounded-full -mr-10 -mt-10"></div>
      <div>
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl bg-ewforange text-white flex items-center justify-center font-bold text-lg">D</div>
          <span class="text-xs font-medium text-white/60">2 Posisi</span>
        </div>
        <h3 class="text-lg font-semibold mb-2">Data Analyst</h3>
        <span class="inline-block px-2 py-1 bg-white/10 text-orange-300 text-[10px] font-semibold rounded mb-3">URGENT</span>
        <p class="text-xs text-white/60 leading-relaxed">Menganalisa tren pasar derivatif dan data transaksi harian menggunakan Python dan alat visualisasi data.</p>
      </div>
      <div class="mt-6 border-t border-white/10 pt-4">
        <div class="flex gap-3 text-[11px] text-white/70 mb-4">
          <span>📍 Surabaya (On-Site)</span>
          <span>⏱ Full-Time</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-[10px] text-white/50">Ditutup 3 Hari Lagi</span>
          <button class="px-4 py-1.5 bg-ewforange text-white text-xs font-semibold rounded-full hover:scale-105 transition">Lamar Posisi</button>
        </div>
      </div>
    </div>

    <!-- Regular Card 1 -->
    <div class="bg-white rounded-3xl p-6 flex flex-col justify-between border border-black/5 hover:border-ewforange/30 transition shadow-sm hover:shadow-md">
      <div>
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center font-bold text-lg">A</div>
          <span class="text-xs font-medium text-black/40">5 Posisi</span>
        </div>
        <h3 class="text-lg font-semibold mb-2 text-zinc-900">Administrasi Bisnis</h3>
        <p class="text-xs text-black/50 leading-relaxed mb-3">Praktik tata kelola dokumen operasional pialang, input data nasabah ke CRM, dan dukung layanan kantor.</p>
      </div>
      <div class="mt-4 border-t border-black/5 pt-4">
        <div class="flex gap-3 text-[11px] text-black/60 mb-4">
          <span>📍 Surabaya (On-Site)</span>
          <span>⏱ Full-Time</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-[10px] text-black/40">Tayang 2 Hari Lalu</span>
          <button class="px-4 py-1.5 bg-orange-50 text-ewforange text-xs font-semibold rounded-full hover:bg-orange-100 transition">Lamar Posisi</button>
        </div>
      </div>
    </div>

    <!-- Regular Card 2 -->
    <div class="bg-white rounded-3xl p-6 flex flex-col justify-between border border-black/5 hover:border-ewforange/30 transition shadow-sm hover:shadow-md">
      <div>
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center font-bold text-lg">F</div>
          <span class="text-xs font-medium text-black/40">1 Posisi</span>
        </div>
        <h3 class="text-lg font-semibold mb-2 text-zinc-900">Full-Stack Developer</h3>
        <p class="text-xs text-black/50 leading-relaxed mb-3">Membantu tim IT internal merancang sistem registrasi nasabah online berbasis Laravel dan React.</p>
      </div>
      <div class="mt-4 border-t border-black/5 pt-4">
        <div class="flex gap-3 text-[11px] text-black/60 mb-4">
          <span>📍 Surabaya (On-Site)</span>
          <span>⏱ Full-Time</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-[10px] text-black/40">Tayang 4 Hari Lalu</span>
          <button class="px-4 py-1.5 bg-orange-50 text-ewforange text-xs font-semibold rounded-full hover:bg-orange-100 transition">Lamar Posisi</button>
        </div>
      </div>
    </div>

    <!-- Regular Card 3 -->
    <div class="bg-white rounded-3xl p-6 flex flex-col justify-between border border-black/5 hover:border-ewforange/30 transition shadow-sm hover:shadow-md">
      <div>
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center font-bold text-lg">A</div>
          <span class="text-xs font-medium text-black/40">4 Posisi</span>
        </div>
        <h3 class="text-lg font-semibold mb-2 text-zinc-900">Akuntansi</h3>
        <p class="text-xs text-black/50 leading-relaxed mb-3">Membantu penyusunan laporan harian kas, input mutasi segregated account, dan rekonsiliasi keuangan cabang.</p>
      </div>
      <div class="mt-4 border-t border-black/5 pt-4">
        <div class="flex gap-3 text-[11px] text-black/60 mb-4">
          <span>📍 Surabaya (On-Site)</span>
          <span>⏱ Full-Time</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-[10px] text-black/40">Tayang 5 Hari Lalu</span>
          <button class="px-4 py-1.5 bg-orange-50 text-ewforange text-xs font-semibold rounded-full hover:bg-orange-100 transition">Lamar Posisi</button>
        </div>
      </div>
    </div>

    <!-- Regular Card 4 -->
    <div class="bg-white rounded-3xl p-6 flex flex-col justify-between border border-black/5 hover:border-ewforange/30 transition shadow-sm hover:shadow-md">
      <div>
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center font-bold text-lg">M</div>
          <span class="text-xs font-medium text-black/40">6 Posisi</span>
        </div>
        <h3 class="text-lg font-semibold mb-2 text-zinc-900">Manajemen</h3>
        <p class="text-xs text-black/50 leading-relaxed mb-3">Mempelajari alur manajerial divisi pemasaran derivatif, riset kompetitor, dan pengembangan strategi akuisisi klien.</p>
      </div>
      <div class="mt-4 border-t border-black/5 pt-4">
        <div class="flex gap-3 text-[11px] text-black/60 mb-4">
          <span>📍 Surabaya (On-Site)</span>
          <span>⏱ Full-Time</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-[10px] text-black/40">Tayang 2 Hari Lalu</span>
          <button class="px-4 py-1.5 bg-orange-50 text-ewforange text-xs font-semibold rounded-full hover:bg-orange-100 transition">Lamar Posisi</button>
        </div>
      </div>
    </div>

    <!-- Regular Card 5 -->
    <div class="bg-white rounded-3xl p-6 flex flex-col justify-between border border-black/5 hover:border-ewforange/30 transition shadow-sm hover:shadow-md">
      <div>
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center font-bold text-lg">H</div>
          <span class="text-xs font-medium text-black/40">2 Posisi</span>
        </div>
        <h3 class="text-lg font-semibold mb-2 text-zinc-900">Hubungan Internasional</h3>
        <p class="text-xs text-black/50 leading-relaxed mb-3">Mempelajari dampak isu makroekonomi global terhadap pergerakan komoditas dan valuta asing (Forex).</p>
      </div>
      <div class="mt-4 border-t border-black/5 pt-4">
        <div class="flex gap-3 text-[11px] text-black/60 mb-4">
          <span>📍 Surabaya (On-Site)</span>
          <span>⏱ Full-Time</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-[10px] text-black/40">Tayang Hari Ini</span>
          <button class="px-4 py-1.5 bg-orange-50 text-ewforange text-xs font-semibold rounded-full hover:bg-orange-100 transition">Lamar Posisi</button>
        </div>
      </div>
    </div>
  </div>
  
  <div class="text-right mt-8 rv">
    <a href="#" class="text-sm font-semibold text-black hover:text-ewforange transition">Lihat Semua Posisi →</a>
  </div>
</section>

<!-- 3. FITUR PENDUKUNG KARIER (Bento Grid) -->
<section class="px-6 md:px-16 py-20 bg-black/5">
  <div class="max-w-7xl mx-auto">
    <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-center mb-12 rv">Fitur Pendukung Karier Anda</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
      <!-- Feature 1: AI Match (Span 2) -->
      <div class="bg-white p-8 rounded-3xl shadow-sm border border-black/5 md:col-span-2 flex flex-col justify-between rv">
        <div>
          <div class="w-10 h-10 bg-orange-100 text-ewforange rounded-full flex items-center justify-center mb-5 text-lg">🎯</div>
          <h3 class="text-xl font-semibold mb-2">Pencocokan Posisi Berbasis AI & Keahlian</h3>
          <p class="text-sm text-black/50 max-w-md">Sistem cerdas kami membaca resume, riwayat studi, dan portofolio Anda untuk merekomendasikan lowongan yang memiliki tingkat kecocokan tertinggi.</p>
        </div>
        <div class="mt-8 p-4 bg-paper rounded-2xl border border-black/5 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-ewforange text-white rounded-lg flex items-center justify-center font-bold text-xs">98%</div>
            <div><p class="text-xs font-semibold">Kecocokan Profil Terverifikasi</p><p class="text-[10px] text-black/50">UI/UX Product Designer • PT Equityworld</p></div>
          </div>
          <div class="hidden md:flex gap-2 text-[10px] font-medium text-black/40">
            <span class="px-2 py-1 bg-white rounded">Figma Skills</span>
            <span class="px-2 py-1 bg-white rounded">Design System</span>
          </div>
        </div>
      </div>
      
      <!-- Feature 2: Document -->
      <div class="bg-white p-8 rounded-3xl shadow-sm border border-black/5 flex flex-col justify-between rv">
        <div>
          <h3 class="text-xl font-semibold mb-2">Kelola Dokumen Praktis</h3>
          <p class="text-sm text-black/50">Simpan portofolio, CV, dan berkas lamaran di satu akun untuk melamar ke puluhan posisi tanpa ribet.</p>
        </div>
        <div class="mt-8 flex gap-3">
          <div class="w-12 h-16 bg-blue-50 rounded-lg flex items-center justify-center text-blue-400 font-bold text-xs shadow-inner">DOC</div>
          <div class="w-12 h-16 bg-red-50 rounded-lg flex items-center justify-center text-red-400 font-bold text-xs shadow-inner -mt-3">PDF</div>
          <div class="w-12 h-16 bg-green-50 rounded-lg flex items-center justify-center text-green-400 font-bold text-xs shadow-inner">IMG</div>
        </div>
      </div>
      
      <!-- Feature 3: Filter -->
      <div class="bg-white p-8 rounded-3xl shadow-sm border border-black/5 rv">
        <div class="w-10 h-10 bg-zinc-100 text-zinc-600 rounded-full flex items-center justify-center mb-5 text-lg">▤</div>
        <h3 class="text-xl font-semibold mb-2">Filter Cerdas & Bebas Bias</h3>
        <p class="text-sm text-black/50 mb-6">Proses penyaringan awal murni berdasarkan keahlian, bukan identitas. Kami fokus pada kualitas bakat untuk budaya kerja yang setara.</p>
        <div class="flex gap-2 flex-wrap text-[10px] font-semibold text-black/60">
          <span class="px-3 py-1.5 bg-paper rounded-full border border-black/5">Kemampuan Logika</span>
          <span class="px-3 py-1.5 bg-paper rounded-full border border-black/5">Portofolio</span>
          <span class="px-3 py-1.5 bg-paper rounded-full border border-black/5 text-rose-500">Tanpa Batas Usia</span>
        </div>
      </div>
      
      <!-- Feature 4: Integration (Span 2) -->
      <div class="bg-white p-8 rounded-3xl shadow-sm border border-black/5 md:col-span-2 flex flex-col justify-between rv">
        <div class="flex justify-between items-start">
          <div>
            <div class="w-10 h-10 bg-indigo-100 text-indigo-500 rounded-full flex items-center justify-center mb-5 text-lg">🔗</div>
            <h3 class="text-xl font-semibold mb-2">Integrasi Akun Portofolio & Sertifikasi</h3>
            <p class="text-sm text-black/50 max-w-sm">Tautkan akun GitHub, LinkedIn, Dribbble, atau lampirkan lisensi WPPE/WAK/WMI langsung ke profil pelamar Anda.</p>
          </div>
          <div class="hidden md:flex gap-2">
            <div class="w-8 h-8 bg-zinc-100 rounded flex items-center justify-center text-[10px] font-bold">GH</div>
            <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center text-[10px] font-bold text-blue-600">IN</div>
            <div class="w-8 h-8 bg-pink-100 rounded flex items-center justify-center text-[10px] font-bold text-pink-600">DR</div>
          </div>
        </div>
        <div class="mt-8 grid grid-cols-3 gap-3">
          <div class="bg-paper p-3 rounded-xl border border-black/5"><p class="text-[10px] text-black/40">Status Verifikasi</p><p class="text-xs font-semibold text-green-600 mt-1">✓ Otomatis Diterima</p></div>
          <div class="bg-paper p-3 rounded-xl border border-black/5"><p class="text-[10px] text-black/40">Sertifikasi Keahlian</p><p class="text-xs font-semibold text-black mt-1">Mitra Terverifikasi</p></div>
          <div class="bg-paper p-3 rounded-xl border border-black/5"><p class="text-[10px] text-black/40">Proses Perekrutan</p><p class="text-xs font-semibold text-black mt-1">3x Lebih Cepat</p></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 4. FAQ & KONTAK -->
<section class="px-6 md:px-16 py-24 max-w-7xl mx-auto">
  <div class="grid md:grid-cols-[1.5fr_1fr] gap-12">
    <!-- FAQs -->
    <div class="rv">
      <h2 class="text-3xl font-semibold tracking-tight mb-2">Pertanyaan Yang Sering Diajukan</h2>
      <p class="text-sm text-black/50 mb-8">Informasi lengkap seputar alur rekrutmen dan prosedur seleksi PT Equityworld Futures.</p>
      
      <div class="space-y-4">
        <!-- FAQ 1 -->
        <div class="border border-black/10 rounded-2xl overflow-hidden bg-white">
          <button class="faq-btn w-full px-6 py-4 flex items-center justify-between text-left focus:outline-none">
            <span class="font-semibold text-sm">Bagaimana tahapan rekrutmen magang di Kantor Kami?</span>
            <span class="faq-icon text-black/40">▼</span>
          </button>
          <div class="faq-content">
            <div class="faq-inner px-6 pb-4 text-xs text-black/60 leading-relaxed">
              Proses dimulai dari pendaftaran CV secara online di portal ini. Jika lolos seleksi administrasi, Anda akan diundang untuk tes tertulis dan wawancara dengan HR & divisi terkait (User). Hasil final akan diumumkan melalui portal ini dan email resmi perusahaan.
            </div>
          </div>
        </div>
        <!-- FAQ 2 -->
        <div class="border border-black/10 rounded-2xl overflow-hidden bg-white">
          <button class="faq-btn w-full px-6 py-4 flex items-center justify-between text-left focus:outline-none">
            <span class="font-semibold text-sm">Berapa lama estimasi proses seleksi hingga penawaran kerja?</span>
            <span class="faq-icon text-black/40">▼</span>
          </button>
          <div class="faq-content">
            <div class="faq-inner px-6 pb-4 text-xs text-black/60 leading-relaxed">
              Standar proses seleksi kami memakan waktu 7 hingga 14 hari kerja terhitung dari tanggal penutupan pendaftaran lowongan.
            </div>
          </div>
        </div>
        <!-- FAQ 3 -->
        <div class="border border-black/10 rounded-2xl overflow-hidden bg-white">
          <button class="faq-btn w-full px-6 py-4 flex items-center justify-between text-left focus:outline-none">
            <span class="font-semibold text-sm">Apakah mahasiswa akhir atau fresh graduate dapat melamar?</span>
            <span class="faq-icon text-black/40">▼</span>
          </button>
          <div class="faq-content">
            <div class="faq-inner px-6 pb-4 text-xs text-black/60 leading-relaxed">
              Tentu. Program magang kami sangat terbuka bagi mahasiswa semester 5 ke atas, mahasiswa tingkat akhir yang sedang menyusun skripsi, maupun fresh graduate.
            </div>
          </div>
        </div>
        <!-- FAQ 4 -->
        <div class="border border-black/10 rounded-2xl overflow-hidden bg-white">
          <button class="faq-btn w-full px-6 py-4 flex items-center justify-between text-left focus:outline-none">
            <span class="font-semibold text-sm">Apakah seluruh proses rekrutmen ini dipungut biaya?</span>
            <span class="faq-icon text-black/40">▼</span>
          </button>
          <div class="faq-content">
            <div class="faq-inner px-6 pb-4 text-xs text-black/60 leading-relaxed">
              Tidak. Seluruh proses rekrutmen PT Equityworld Futures bersifat 100% GRATIS. Kami tidak pernah meminta biaya akomodasi, transportasi, atau biaya apa pun kepada kandidat.
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Contact Card -->
    <div class="bg-orange-50 rounded-3xl p-8 text-center flex flex-col justify-center border border-orange-100 rv">
      <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto text-xl shadow-sm mb-4">📞</div>
      <h3 class="text-xl font-semibold mb-2">Punya Pertanyaan Khusus?</h3>
      <p class="text-xs text-black/50 mb-8 max-w-xs mx-auto">Tim Rekrutmen EWF siap membantu. Hubungi kami untuk menanyakan status aplikasi atau informasi detail lainnya.</p>
      <button class="w-full py-3 bg-zinc-900 text-white rounded-xl text-xs font-semibold mb-3 hover:bg-black transition">Konsultasi via WhatsApp</button>
      <button class="w-full py-3 bg-white border border-black/10 text-black rounded-xl text-xs font-semibold hover:bg-black/5 transition">Kirim Pertanyaan Email</button>
    </div>
  </div>
</section>

<!-- 5. CTA SECTION -->
<section class="px-6 md:px-16 pb-24 max-w-7xl mx-auto">
  <div class="bg-zinc-900 rounded-3xl py-20 px-8 text-center text-white relative overflow-hidden rv">
    <!-- Abstract blurred shapes -->
<div class="animate-stroll-orange absolute top-0 left-1/4 w-64 h-64 bg-orange-500/30 blur-[80px] rounded-full mix-blend-screen pointer-events-none"></div>
<div class="animate-stroll-blue absolute bottom-0 right-1/4 w-64 h-64 bg-blue-500/30 blur-[80px] rounded-full mix-blend-screen pointer-events-none"></div>
    
    <div class="flex justify-center gap-4 text-white/30 text-xs font-semibold mb-8 flex-wrap max-w-lg mx-auto pointer-events-none">
      <span>Data Analyst</span><span>Administrasi</span><span>Developer</span><span>Akuntansi</span><span>Manajemen</span><span>PR</span>
    </div>
    
    <h2 class="text-3xl md:text-5xl font-semibold tracking-tight relative z-10 leading-tight">
      Siap Mengambil Langkah Baru<br>
      <span class="text-ewforange">Dalam Karier Anda?</span>
    </h2>
    <p class="mt-4 text-sm text-white/60 max-w-md mx-auto relative z-10">Bergabunglah dengan ribuan talenta berbakat yang telah mewujudkan impian karier profesional bersama PT Equityworld Futures.</p>
    
    <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4 relative z-10">
      <a href="{{ route('register') }}" class="px-8 py-3.5 bg-ewforange text-white text-sm font-semibold rounded-full hover:bg-orange-600 transition">Daftar Akun</a>
    </div>
  </div>
</section>
</main>

<!-- FOOTER -->
<footer class="px-6 md:px-16 py-12 max-w-7xl mx-auto grid md:grid-cols-[2fr_1fr_1fr_1fr] gap-8 text-xs text-black/60 border-t border-black/5">
  <div>
    <p class="text-sm font-bold text-ewforange mb-2 tracking-wide flex items-center gap-2">
      <span class="w-5 h-5 bg-ewforange rounded text-white flex items-center justify-center text-[10px]">E</span>
      EQUITYWORLD
    </p>
    <p class="max-w-xs mt-3 leading-relaxed">Portal karir resmi terintegrasi PT Equityworld Futures. Memberikan peluang karir tanpa batas di industri pialang berjangka untuk talenta profesional dan generasi penggerak bangsa.</p>
    <a href="#" class="inline-flex mt-4 items-center gap-1 text-blue-600 font-semibold bg-blue-50 px-2 py-1 rounded">✓ Diawasi oleh BAPPEBTI</a>
  </div>
  <div>
    <p class="text-sm font-semibold text-black mb-4">Peluang Karier</p>
    <ul class="space-y-3">
      <li><a href="#" class="hover:text-black">Cari Lowongan Kerja</a></li>
      <li><a href="#" class="hover:text-black">Program Magang Nasional</a></li>
      <li><a href="#" class="hover:text-black">Fresh Graduate</a></li>
      <li><a href="#" class="hover:text-black">Tenaga Profesional</a></li>
    </ul>
  </div>
  <div>
    <p class="text-sm font-semibold text-black mb-4">Perusahaan</p>
    <ul class="space-y-3">
      <li><a href="{{ route('tentang') }}" class="hover:text-black">Tentang Kami</a></li>
      <li><a href="#" class="hover:text-black">Budaya Kerja</a></li>
      <li><a href="#" class="hover:text-black">Pusat Bantuan Pelamar</a></li>
      <li><a href="#" class="hover:text-black">Kebijakan Privasi</a></li>
    </ul>
  </div>
  <div>
    <p class="text-sm font-semibold text-black mb-4">Hubungi Kami</p>
    <ul class="space-y-3">
      <li class="flex items-start gap-2"><span>📍</span> Sahid Sudirman Center Lt. 9, Jl. Jend. Sudirman Kav. 86, Jakarta Pusat 10220</li>
      <li class="flex items-center gap-2"><span>✉</span> karir@equityworld.co.id</li>
      <li class="flex items-center gap-2"><span>📞</span> +62 (21) 2788-9200</li>
    </ul>
  </div>
  <div class="col-span-full border-t border-black/5 pt-6 flex flex-col md:flex-row justify-between items-center mt-4">
    <p>© 2026 PT Equityworld Futures. Hak Cipta Dilindungi Undang-Undang.</p>
    <div class="flex gap-4 mt-2 md:mt-0">
      <a href="#" class="hover:text-black">Syarat & Ketentuan</a>
      <a href="#" class="hover:text-black">Keamanan Platform</a>
    </div>
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
  gsap.registerPlugin(ScrollTrigger);

  // Animasi reveal saat scroll
  const revealElements = document.querySelectorAll('.rv');
  revealElements.forEach((el) => {
    gsap.to(el, {
      y: 0,
      opacity: 1,
      duration: 0.8,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: el,
        start: 'top 85%',
      }
    });
  });

  // Logika interaksi FAQ Accordion
  const faqBtns = document.querySelectorAll('.faq-btn');
  faqBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      const content = this.nextElementSibling;
      const isOpen = content.classList.contains('open');
      
      // Tutup semua accordion yang terbuka
      document.querySelectorAll('.faq-content').forEach(c => c.classList.remove('open'));
      document.querySelectorAll('.faq-btn').forEach(b => b.classList.remove('active'));
      
      // Jika yang di-klik sebelumnya belum terbuka, maka buka
      if (!isOpen) {
        content.classList.add('open');
        this.classList.add('active');
      }
    });
  });
</script>
</body>
</html>