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

@include('partials.navbar')

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
  <div class="flex flex-col items-center text-center gap-6 mb-10 rv">

  <div>
    <h2 class="text-2xl md:text-3xl font-semibold tracking-tight">
      Eksplorasi Program Magang
    </h2>

    <p class="text-sm text-black/50 mt-2">
      Temukan program magang yang sesuai dengan minat dan keahlianmu.
    </p>
  </div>

</div>

  
<div class="grid md:grid-cols-3 gap-5 rv">

  @forelse ($programs as $program)

    <div class="bg-white rounded-3xl p-6 flex flex-col justify-between border border-black/5 hover:border-ewforange/30 transition shadow-sm hover:shadow-md">

      <div>
        <div class="flex justify-between items-start mb-4">

          <div class="w-10 h-10 rounded-xl bg-orange-50 text-ewforange flex items-center justify-center font-bold text-lg">
            {{ strtoupper(substr($program->nama_program, 0, 1)) }}
          </div>

          <span class="text-xs font-medium text-black/40">
            {{ $program->kuota ?? 0 }} Posisi
          </span>

        </div>

        <h3 class="text-lg font-semibold mb-2 text-zinc-900">
          {{ $program->nama_program }}
        </h3>

        <p class="text-xs text-black/50 leading-relaxed mb-3">
          {{ $program->deskripsi }}
        </p>
      </div>

      <div class="mt-4 border-t border-black/5 pt-4">

    {{-- Lokasi dan jenis magang --}}
    <div class="flex gap-3 text-[11px] text-black/60 mb-4">
        <span>📍 Surabaya (On-Site)</span>
        <span>⏱ Magang</span>
    </div>

    {{-- Tanggal dan tombol --}}
    <div class="flex items-center justify-between gap-3">

        {{-- Tanggal penutupan --}}
        <span class="text-[10px] text-black/40">
            @if ($program->tgl_tutup)
                Ditutup {{ \Carbon\Carbon::parse($program->tgl_tutup)->translatedFormat('d F Y') }}
            @else
                Jadwal belum tersedia
            @endif
        </span>

        <a
    href="{{ route('internship.show', ['id' => $program->id_program]) }}"
    class="inline-flex items-center justify-center px-4 py-2
           bg-orange-50 text-ewforange text-xs font-semibold
           rounded-full hover:bg-orange-100 transition whitespace-nowrap"
>
    Lihat Detail
</a>

        {{-- Guest --}}
        @if (!auth()->check())

            <a
                href="{{ route('login') }}"
                style="display: inline-flex;"
                class="px-4 py-2 bg-orange-100 text-ewforange text-xs font-semibold rounded-full hover:bg-orange-50 transition whitespace-nowrap"
            >
                Lamar Posisi
            </a>

        {{-- Pelamar --}}
        @elseif (auth()->user()->role && auth()->user()->role->nama_role === 'pelamar')

            <a
                href="{{ route('pelamar.lamaran.create', ['id_program' => $program->id_program]) }}"
                style="display: inline-flex;"
                class="px-4 py-2 bg-orange-100 text-ewforange text-xs font-semibold rounded-full hover:bg-orange-50 transition whitespace-nowrap"
            >
                Lamar Posisi
            </a>

        @endif

    </div>

</div>
  @empty

    <div class="md:col-span-3 bg-white rounded-3xl p-10 text-center border border-black/5">

      <div class="text-4xl mb-4">📋</div>

      <h3 class="text-lg font-semibold text-zinc-900">
        Belum Ada Program Magang
      </h3>

      <p class="text-sm text-black/50 mt-2">
        Saat ini belum tersedia program magang yang dibuka.
        Silakan cek kembali secara berkala.
      </p>

    </div>

  @endforelse

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

{{-- 5. CTA SECTION --}}
@guest
<section class="px-6 md:px-16 pb-24 max-w-7xl mx-auto">
  <div class="bg-zinc-900 rounded-3xl py-20 px-8 text-center text-white relative overflow-hidden rv">

    {{-- Abstract blurred shapes --}}
    <div class="animate-stroll-orange absolute top-0 left-1/4 w-64 h-64 bg-orange-500/30 blur-[80px] rounded-full mix-blend-screen pointer-events-none"></div>

    <div class="animate-stroll-blue absolute bottom-0 right-1/4 w-64 h-64 bg-blue-500/30 blur-[80px] rounded-full mix-blend-screen pointer-events-none"></div>

    <div class="flex justify-center gap-4 text-white/30 text-xs font-semibold mb-8 flex-wrap max-w-lg mx-auto pointer-events-none">
      <span>Data Analyst</span>
      <span>Administrasi</span>
      <span>Developer</span>
      <span>Akuntansi</span>
      <span>Manajemen</span>
      <span>PR</span>
    </div>

    <h2 class="text-3xl md:text-5xl font-semibold tracking-tight relative z-10 leading-tight">
      Siap Mengambil Langkah Baru<br>
      <span class="text-ewforange">Dalam Karier Anda?</span>
    </h2>

    <p class="mt-4 text-sm text-white/60 max-w-md mx-auto relative z-10">
      Bergabunglah dengan ribuan talenta berbakat yang telah mewujudkan impian karier profesional bersama PT Equityworld Futures.
    </p>

    <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4 relative z-10">
      <a
        href="{{ route('register') }}"
        class="px-8 py-3.5 bg-ewforange text-white text-sm font-semibold rounded-full hover:bg-orange-600 transition"
      >
        Daftar Akun
      </a>
    </div>

  </div>
</section>
@endguest

</div>
  </div>
</section>
</main>

@include('partials.footer')

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