 <!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tentang Perusahaan - PT Equityworld Futures</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { 
        theme: { 
            extend: {
                fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
                colors: { paper:'#faf9fc', lime:'#e9f56b', brand:'#34d3b0', ewforange:'#f97316' } 
            } 
        } 
    };
  </script>
  <style>
    /* Utility pendukung animasi GSAP */
    .rv { opacity: 0; transform: translateY(30px); }
    .floating-badge { animation: float 6s ease-in-out infinite; }
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }
  </style>
</head>
<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

@include('partials.navbar')

<main>
<!-- 1. HERO SECTION -->
<section class="relative min-h-[90vh] pt-32 px-6 flex items-center justify-center overflow-hidden">
  <!-- Watermark Text Background -->
  <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0">
    <h1 class="text-[12vw] font-bold text-black/[0.03] select-none tracking-tighter">EQUITYWORLD</h1>
  </div>
  
  <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center relative z-10 w-full">
    <div class="rv">
      <h1 class="text-4xl md:text-6xl font-semibold tracking-tight leading-[1.1] max-w-lg">
        Mengenal Lebih Dekat <span class="text-ewforange">PT Equityworld Futures.</span>
      </h1>
      <p class="mt-6 text-sm text-black/60 max-w-md leading-relaxed">
        PT Equityworld Futures ("EWF") hadir sebagai mitra investasi yang mengutamakan kepercayaan dan profesionalisme di industri perdagangan berjangka. Dengan fokus pada kualitas layanan dan edukasi berkelanjutan, EWF membantu nasabah menavigasi peluang pasar secara lebih terarah dan aman.
      </p>
      <a href="#fondasi" class="inline-block mt-8 px-8 py-3 rounded-full bg-ewforange text-white text-sm font-medium hover:bg-orange-600 hover:scale-105 transition">
        Jelajahi Profil Perusahaan
      </a>
    </div>
    
    <!-- Hero Image Component -->
    <div class="relative h-[28rem] md:h-[36rem] flex items-center justify-center rv">
      <div class="relative w-full max-w-sm h-full rounded-[2rem] overflow-hidden shadow-2xl border-4 border-white">
        <img src="https://picsum.photos/seed/building1/600/800" alt="Gedung EWF" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
        <!-- Floating UI Badges -->
        <div class="floating-badge absolute top-6 right-6 bg-white/90 backdrop-blur px-4 py-2 rounded-xl text-xs font-semibold shadow-lg flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-500"></span> Operasional Aktif
        </div>
        <div class="floating-badge absolute bottom-10 left-[-2rem] bg-white/90 backdrop-blur px-5 py-3 rounded-xl text-sm font-semibold shadow-lg" style="animation-delay: -3s;">
            <span class="text-ewforange">★</span> Top Pialang 2024
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 2. FONDASI & ARAH STRATEGIS -->
<section id="fondasi" class="px-6 md:px-16 py-24 bg-white/50">
  <div class="max-w-7xl mx-auto">
    <h2 class="text-3xl md:text-4xl font-semibold tracking-tight mb-10 rv">Fondasi &amp; Arah Strategis Perusahaan</h2>
    
    <!-- Visi Misi Box -->
    <div class="grid md:grid-cols-2 gap-6 rv">
      <div class="bg-ewforange text-white rounded-3xl p-10 flex flex-col justify-center">
        <span class="text-white/70 text-xs font-bold tracking-widest mb-4">VISI KAMI</span>
        <h3 class="text-2xl md:text-3xl font-medium leading-snug">"Menjadi perusahaan pialang berjangka yang unggul dan terpercaya, dengan standar operasional yang modern, transparan, serta memberikan nilai strategis bagi perkembangan industri dan perekonomian nasional."</h3>
      </div>
      <div class="bg-zinc-900 text-zinc-300 rounded-3xl p-10">
        <span class="text-zinc-500 text-xs font-bold tracking-widest mb-6 block">MISI STRATEGIS</span>
        <ul class="space-y-4 text-sm leading-relaxed">
          <li class="flex gap-3"><span class="text-ewforange">✓</span> Menyediakan layanan perdagangan berjangka yang profesional, aman, dan berintegritas tinggi.</li>
          <li class="flex gap-3"><span class="text-ewforange">✓</span> Mengembangkan kualitas sumber daya manusia untuk menghadirkan layanan yang kompeten dan berdaya saing.</li>
          <li class="flex gap-3"><span class="text-ewforange">✓</span> Mengimplementasikan tata kelola perusahaan yang transparan dan akuntabel.</li>
          <li class="flex gap-3"><span class="text-ewforange">✓</span> Memperluas akses edukasi serta literasi perdagangan berjangka kepada masyarakat.</li>
          <li class="flex gap-3"><span class="text-ewforange">✓</span> Mengoptimalkan teknologi untuk mendukung operasional dan pengalaman nasabah secara efisien.</li>
        </ul>
      </div>
    </div>

    <!-- 4 Core Values -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 rv">
      <div class="bg-white p-6 rounded-2xl border border-black/5 shadow-sm hover:shadow-md transition">
        <div class="text-ewforange font-bold text-xl mb-2">01</div>
        <h4 class="font-semibold text-sm mb-2">Integritas Profesional</h4>
        <p class="text-xs text-black/50">Menegakkan standar etika yang tinggi serta menjaga konsistensi tindakan dan keputusan dalam setiap aktivitas perusahaan.</p>
      </div>
      <div class="bg-white p-6 rounded-2xl border border-black/5 shadow-sm hover:shadow-md transition">
        <div class="text-ewforange font-bold text-xl mb-2">02</div>
        <h4 class="font-semibold text-sm mb-2">Fokus pada Nasabah</h4>
        <p class="text-xs text-black/50">Mengutamakan kebutuhan dan tujuan investasi nasabah melalui layanan yang responsif, akurat, dan transparan.</p>
      </div>
      <div class="bg-white p-6 rounded-2xl border border-black/5 shadow-sm hover:shadow-md transition">
        <div class="text-ewforange font-bold text-xl mb-2">03</div>
        <h4 class="font-semibold text-sm mb-2">Disiplin Kepatuhan</h4>
        <p class="text-xs text-black/50">Menjalankan seluruh proses bisnis sesuai kerangka regulasi dengan kontrol internal yang kuat dan berkelanjutan.</p>
      </div>
      <div class="bg-white p-6 rounded-2xl border border-black/5 shadow-sm hover:shadow-md transition">
        <div class="text-ewforange font-bold text-xl mb-2">04</div>
        <h4 class="font-semibold text-sm mb-2">Inovasi Berkelanjutan</h4>
        <p class="text-xs text-black/50">Mendorong pengembangan teknologi, produk, dan proses kerja untuk menghadirkan pengalaman transaksi yang semakin efisien dan kompetitif.</p>
      </div>
    </div>
  </div>
</section>

<!-- 3. LEGALITAS & IZIN OPERASIONAL -->
<section class="px-6 md:px-16 py-24 max-w-7xl mx-auto">
  <div class="flex items-center justify-between mb-10 rv">
    <h2 class="text-3xl md:text-4xl font-semibold tracking-tight">Legalitas &amp; Izin Operasional Resmi</h2>
    <span class="hidden md:inline-block px-4 py-1.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Dinaungi KEMENDAG</span>
  </div>
  
  <div class="grid md:grid-cols-3 gap-5 rv">
    <!-- Card Izin 1 -->
    <div class="bg-white p-6 rounded-2xl border border-black/10 hover:border-ewforange transition">
      <div class="flex justify-between items-start mb-4">
        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold">B</div>
        <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded">Terverifikasi</span>
      </div>
      <h4 class="font-semibold text-sm mb-2">Badan Pengawas Perdagangan Berjangka Komoditi (BAPPEBTI)</h4>
      <p class="text-xs text-black/50">Ditetapkan berdasarkan Keputusan Kepala BAPPEBTI Nomor 850/BAPPEBTI/SI/12/2005.</p>
    </div>
    <!-- Card Izin 2 -->
    <div class="bg-white p-6 rounded-2xl border border-black/10 hover:border-ewforange transition">
      <div class="flex justify-between items-start mb-4">
        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-ewforange font-bold">J</div>
        <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded">Terverifikasi</span>
      </div>
      <h4 class="font-semibold text-sm mb-2">Jakarta Futures Exchange (JFX) / Bursa Berjangka Jakarta</h4>
      <p class="text-xs text-black/50">Terdaftar sebagai Anggota Bursa Berjangka Jakarta dengan Nomor SPAB-143/BBJ/09/05.</p>
    </div>
    <!-- Card Izin 3 -->
    <div class="bg-white p-6 rounded-2xl border border-black/10 hover:border-ewforange transition">
      <div class="flex justify-between items-start mb-4">
        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600 font-bold">K</div>
        <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded">Terverifikasi</span>
      </div>
      <h4 class="font-semibold text-sm mb-2">PT Kliring Berjangka Indonesia (Persero)</h4>
      <p class="text-xs text-black/50">Resmi terdaftar sebagai anggota Kliring Berjangka Indonesia dengan Nomor 36/AK-KBI/IV/2006.</p>
    </div>
    <!-- Bisa ditambah sesuai gambar referensi -->
  </div>
</section>

<!-- 4. KANTOR CABANG -->
<section class="px-6 md:px-16 py-24 bg-white/30">
  <div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4 rv">
      <h2 class="text-3xl md:text-4xl font-semibold tracking-tight">7 Kantor Cabang</h2>
      <div class="flex gap-2">
        <button class="px-4 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded-full">Semua Cabang</button>
        <button class="px-4 py-1.5 bg-white border border-black/10 text-xs font-medium rounded-full hover:bg-black/5">Jakarta</button>
        <button class="px-4 py-1.5 bg-white border border-black/10 text-xs font-medium rounded-full hover:bg-black/5">Surabaya</button>
      </div>
    </div>

    <!-- Featured Branch (Pusat/Utama) -->
    <div class="bg-white rounded-3xl overflow-hidden border border-black/5 shadow-sm grid md:grid-cols-[2fr_3fr] mb-6 rv">
      <div class="h-64 md:h-auto">
        <img src="https://picsum.photos/seed/hq/800/600" alt="Sahid Sudirman Center" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
      </div>
      <div class="p-8 md:p-10 flex flex-col justify-center relative">
        <span class="absolute top-8 right-8 text-[10px] font-bold tracking-widest text-ewforange bg-orange-50 px-3 py-1 rounded-full">KANTOR PUSAT</span>
        <h3 class="text-2xl font-semibold mb-4 pr-24">Sahid Sudirman Center Lt. 9</h3>
        <p class="text-sm text-black/60 mb-6 max-w-md">Jl. Jenderal Sudirman No.86, RT.10/RW.11, Karet Tengsin, Kecamatan Tanah Abang, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10220</p>
        <div class="flex gap-3">
          <button class="px-5 py-2.5 bg-ewforange text-white text-xs font-medium rounded-full hover:bg-orange-600 transition">Lihat Google Maps</button>
        </div>
      </div>
    </div>

    <!-- Branch Grid -->
    <div class="grid md:grid-cols-3 gap-5 rv">
      <!-- Cabang 1 -->
      <div class="bg-white rounded-2xl overflow-hidden border border-black/5">
        <img src="https://picsum.photos/seed/br1/500/300" class="w-full h-40 object-cover transition-transform duration-500 hover:scale-110" alt="">
        <div class="p-5">
          <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">SURABAYA</span>
          <h4 class="font-semibold text-sm mt-3 mb-1">Trillium Office Residence Lt. 1</h4>
          <p class="text-xs text-black/50">Jl. Pemuda No. 108 - 116, Surabaya</p>
        </div>
      </div>
      <!-- Cabang 2 -->
      <div class="bg-white rounded-2xl overflow-hidden border border-black/5">
        <img src="https://picsum.photos/seed/br2/500/300" class="w-full h-40 object-cover transition-transform duration-500 hover:scale-110" alt="">
        <div class="p-5">
          <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">JAKARTA</span>
          <h4 class="font-semibold text-sm mt-3 mb-1">Cyber 2 Tower Lt. 19</h4>
          <p class="text-xs text-black/50">Jl. H. R. Rasuna Said, Jakarta Selatan</p>
        </div>
      </div>
      <!-- Cabang 3 -->
      <div class="bg-white rounded-2xl overflow-hidden border border-black/5">
        <img src="https://picsum.photos/seed/br3/500/300" class="w-full h-40 object-cover transition-transform duration-500 hover:scale-110" alt="">
        <div class="p-5">
          <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">MEDAN</span>
          <h4 class="font-semibold text-sm mt-3 mb-1">Forum Nine Building Lt. 8</h4>
          <p class="text-xs text-black/50">Jl. Imam Bonjol No. 9, Medan</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 5. PRODUK UNGGULAN -->
<section class="px-6 md:px-16 py-24 max-w-7xl mx-auto text-center">
  <h2 class="text-3xl md:text-4xl font-semibold tracking-tight mb-4 rv">Dua Belas Produk Unggulan</h2>
  <div class="flex justify-center gap-2 mb-10 rv">
    <button class="px-4 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded-full">Semua Produk</button>
    <button class="px-4 py-1.5 bg-white border border-black/10 text-xs font-medium rounded-full hover:bg-black/5">Logam Mulia</button>
    <button class="px-4 py-1.5 bg-white border border-black/10 text-xs font-medium rounded-full hover:bg-black/5">Forex</button>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-left rv">
    <!-- Produk Emas -->
    <div class="bg-white p-6 rounded-2xl border border-black/5">
      <div class="w-8 h-8 rounded bg-orange-100 text-ewforange flex items-center justify-center font-bold text-xs mb-4">XAU</div>
      <h4 class="font-semibold text-sm mb-2">Kontrak Emas Loco London (XAU/USD)</h4>
      <p class="text-xs text-black/50 leading-relaxed">Produk investasi berbasis pergerakan harga emas dunia yang diperdagangkan secara online dengan satuan troy ounce.</p>
    </div>
    <!-- Produk Forex -->
    <div class="bg-white p-6 rounded-2xl border border-black/5">
      <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mb-4">EUR</div>
      <h4 class="font-semibold text-sm mb-2">Euro vs US Dollar (EUR/USD)</h4>
      <p class="text-xs text-black/50 leading-relaxed">Pasangan mata uang paling likuid di dunia yang menawarkan spread kompetitif dan peluang trading 2 arah.</p>
    </div>
    <!-- Produk Index -->
    <div class="bg-white p-6 rounded-2xl border border-black/5">
      <div class="w-8 h-8 rounded bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs mb-4">HKG</div>
      <h4 class="font-semibold text-sm mb-2">Hang Seng Index</h4>
      <p class="text-xs text-black/50 leading-relaxed">Perdagangan indeks saham gabungan bursa Hong Kong, cocok untuk diversifikasi portofolio jangka pendek.</p>
    </div>
    <!-- Produk Lainnya (Placeholder) -->
    <div class="bg-white p-6 rounded-2xl border border-black/5">
      <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mb-4">GBP</div>
      <h4 class="font-semibold text-sm mb-2">Great Britain Pound (GBP/USD)</h4>
      <p class="text-xs text-black/50 leading-relaxed">Perdagangan mata uang Poundsterling terhadap Dollar AS dengan volatilitas tinggi.</p>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-black/5">
      <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mb-4">JPY</div>
      <h4 class="font-semibold text-sm mb-2">Japanese Yen (USD/JPY)</h4>
      <p class="text-xs text-black/50 leading-relaxed">Pasangan mata uang utama untuk pasar sesi Asia dengan stabilitas pergerakan harian.</p>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-black/5">
      <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mb-4">AUD</div>
      <h4 class="font-semibold text-sm mb-2">Australian Dollar (AUD/USD)</h4>
      <p class="text-xs text-black/50 leading-relaxed">Mata uang komoditas yang sangat dipengaruhi oleh pergerakan harga komoditas global.</p>
    </div>
  </div>
</section>

</main>

@include('partials.footer')

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
  gsap.registerPlugin(ScrollTrigger);

  // Animasi reveal saat scroll (mirip halaman home)
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
</script>
</body>
</html>