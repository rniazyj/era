<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulir Lamaran - ERA</title>
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
    .rv { opacity: 0; transform: translateY(30px); }
    .upload-item { transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
  </style>
</head>
<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden pt-12 pb-24 px-6">

<main class="max-w-4xl mx-auto">
  <!-- Header Info Posisi -->
  <div class="mb-10 rv text-center">
    <span class="block mx-auto text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full w-max">FORMULIR PENGAJUAN</span>
    <h1 class="text-3xl md:text-5xl font-semibold tracking-tight mt-3">Konsultan Bisnis Intern</h1>
    <p class="text-xs text-black/60 mt-2">Lengkapi periode magang dan unggah berkas persyaratan melalui daftar di bawah ini.</p>
  </div>

  <!-- Form Utama -->
  <form action="{{ route('pelamar.lamaran.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)" class="bg-white rounded-3xl border border-black/5 p-8 md:p-12 shadow-sm rv space-y-8">
    @csrf

    <!-- Bagian 1: Periode Magang & Hitung Otomatis -->
    <div>
      <h3 class="text-sm font-bold tracking-wide text-zinc-900 mb-4 pb-2 border-b border-black/5">1. PERIODE WAKTU MAGANG</h3>
      <div class="grid md:grid-cols-3 gap-5">
        <div>
          <label class="block text-xs font-semibold text-black/70 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
          <input type="date" name="tanggal_mulai" id="tanggal_mulai" required class="w-full px-4 py-3 rounded-xl border border-black/10 text-xs focus:outline-none focus:border-black transition">
        </div>
        <div>
          <label class="block text-xs font-semibold text-black/70 mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
          <input type="date" name="tanggal_selesai" id="tanggal_selesai" required class="w-full px-4 py-3 rounded-xl border border-black/10 text-xs focus:outline-none focus:border-black transition">
        </div>
        <div>
          <label class="block text-xs font-semibold text-black/70 mb-2 flex items-center justify-between">
            <span>Durasi Magang</span>
            <span class="text-[9px] text-zinc-400 font-normal">Otomatis Terisi</span>
          </label>
          <input type="text" name="durasi" id="durasi" placeholder="Otomatis terisi..." readonly class="w-full px-4 py-3 rounded-xl border border-black/10 bg-zinc-50 text-xs text-zinc-600 cursor-not-allowed">
        </div>
      </div>
    </div>

    <!-- Bagian 2: Unggah Berkas Berbentuk List dengan Progress Bar -->
    <div>
      <h3 class="text-sm font-bold tracking-wide text-zinc-900 mb-4 pb-2 border-b border-black/5">2. UNGGAH BERKAS PERSYARATAN</h3>
      
      <div class="space-y-4">
        
        <!-- ITEM 1: Proposal Magang -->
        <div class="upload-item bg-paper rounded-2xl p-4 border border-black/5 flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-3.5 w-full md:w-1/3">
            <div class="w-10 h-10 rounded-xl bg-orange-50 text-ewforange flex items-center justify-center font-bold text-base shrink-0">📄</div>
            <div class="truncate">
              <p class="text-xs font-bold text-zinc-900">Proposal Magang <span class="text-red-500">*</span></p>
              <p class="text-[10px] text-zinc-400 file-info-proposal">Format PDF/DOC (Maks. 2MB)</p>
            </div>
          </div>

          <div class="w-full md:w-2/3 flex items-center justify-end relative">
            <!-- Atribut required dihapus dari sini agar validasi kustom JavaScript bisa berjalan -->
            <input type="file" name="proposal_magang" id="input-proposal" accept=".pdf,.doc,.docx" class="hidden" onchange="handleFileSelected(this, 'proposal')">
            
            <button type="button" onclick="document.getElementById('input-proposal').click()" id="btn-upload-proposal" class="px-6 py-2.5 bg-zinc-500 hover:bg-ewforange/80 text-white text-xs font-bold rounded-full shadow-md flex items-center gap-2 transition hover:scale-105">
              <span>☁️ UPLOAD</span>
            </button>

            <div id="progress-container-proposal" class="hidden w-full items-center">
              <div class="w-full bg-zinc-200 h-3 rounded-full overflow-hidden shadow-inner">
                <div id="progress-bar-proposal" class="bg-ewforange h-full w-0 transition-all duration-300"></div>
              </div>
            </div>

            <button type="button" onclick="resetFile('proposal')" id="btn-remove-proposal" class="hidden ml-3 text-zinc-400 hover:text-red-500 font-bold text-sm px-2">✕</button>
          </div>
        </div>

        <!-- ITEM 2: Scan KTP -->
        <div class="upload-item bg-paper rounded-2xl p-4 border border-black/5 flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-3.5 w-full md:w-1/3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base shrink-0">🪪</div>
            <div class="truncate">
              <p class="text-xs font-bold text-zinc-900">Scan / Foto KTP <span class="text-red-500">*</span></p>
              <p class="text-[10px] text-zinc-400 file-info-ktp">Format PDF/JPG/PNG</p>
            </div>
          </div>

          <div class="w-full md:w-2/3 flex items-center justify-end relative">
            <input type="file" name="ktp" id="input-ktp" accept=".pdf,.jpg,.png" class="hidden" onchange="handleFileSelected(this, 'ktp')">
            
            <button type="button" onclick="document.getElementById('input-ktp').click()" id="btn-upload-ktp" class="px-6 py-2.5 bg-zinc-500 hover:bg-ewforange/80 text-white text-xs font-bold rounded-full shadow-md flex items-center gap-2 transition hover:scale-105">
              <span>☁️ UPLOAD</span>
            </button>

            <div id="progress-container-ktp" class="hidden w-full items-center">
              <div class="w-full bg-zinc-200 h-3 rounded-full overflow-hidden shadow-inner">
                <div id="progress-bar-ktp" class="bg-ewforange h-full w-0 transition-all duration-300"></div>
              </div>
            </div>

            <button type="button" onclick="resetFile('ktp')" id="btn-remove-ktp" class="hidden ml-3 text-zinc-400 hover:text-red-500 font-bold text-sm px-2">✕</button>
          </div>
        </div>

        <!-- ITEM 3: Surat Lamaran -->
        <div class="upload-item bg-paper rounded-2xl p-4 border border-black/5 flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-3.5 w-full md:w-1/3">
            <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center font-bold text-base shrink-0">✉️</div>
            <div class="truncate">
              <p class="text-xs font-bold text-zinc-900">Surat Lamaran <span class="text-red-500">*</span></p>
              <p class="text-[10px] text-zinc-400 file-info-surat">Format PDF/DOC (Maks. 2MB)</p>
            </div>
          </div>

          <div class="w-full md:w-2/3 flex items-center justify-end relative">
            <input type="file" name="surat_lamaran" id="input-surat" accept=".pdf,.doc,.docx" class="hidden" onchange="handleFileSelected(this, 'surat')">
            
            <button type="button" onclick="document.getElementById('input-surat').click()" id="btn-upload-surat" class="px-6 py-2.5 bg-zinc-500 hover:bg-ewforange/80 text-white text-xs font-bold rounded-full shadow-md flex items-center gap-2 transition hover:scale-105">
              <span>☁️ UPLOAD</span>
            </button>

            <div id="progress-container-surat" class="hidden w-full items-center">
              <div class="w-full bg-zinc-200 h-3 rounded-full overflow-hidden shadow-inner">
                <div id="progress-bar-surat" class="bg-ewforange h-full w-0 transition-all duration-300"></div>
              </div>
            </div>

            <button type="button" onclick="resetFile('surat')" id="btn-remove-surat" class="hidden ml-3 text-zinc-400 hover:text-red-500 font-bold text-sm px-2">✕</button>
          </div>
        </div>

      </div>
    </div>

    <!-- Tombol Kirim Lamaran -->
    <div class="pt-4 flex items-center justify-end gap-4">
      <a href="{{ route('lowongan.index') }}" class="px-6 py-3 rounded-full text-xs font-medium text-black/60 hover:text-black transition">Batal</a>
      
      <button type="submit" id="submit-btn" class="relative overflow-hidden group px-8 py-3.5 rounded-full bg-zinc-900 text-white text-xs font-medium transition-all duration-300 hover:bg-black hover:scale-105 shadow-lg flex items-center gap-2">
        <span class="relative z-10">Kirim</span>
        <span class="relative z-10 transition-transform duration-300 group-hover:translate-x-1">→</span>
      </button>
    </div>

  </form>
</main>

<!-- GSAP Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
  gsap.registerPlugin(ScrollTrigger);

  gsap.utils.toArray('.rv').forEach((el, i) => {
    gsap.fromTo(el, 
      { y: 30, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.8, delay: i * 0.15, ease: 'power3.out' }
    );
  });

  function handleFileSelected(input, type) {
    if (input.files && input.files[0]) {
      const fileName = input.files[0].name;
      const btnUpload = document.getElementById(`btn-upload-${type}`);
      const progressContainer = document.getElementById(`progress-container-${type}`);
      const progressBar = document.getElementById(`progress-bar-${type}`);
      const fileInfo = document.querySelector(`.file-info-${type}`);
      const btnRemove = document.getElementById(`btn-remove-${type}`);

      gsap.to(btnUpload, {
        scale: 0.7,
        opacity: 0,
        duration: 0.5,
        ease: 'power2.inOut',
        onComplete: () => {
          btnUpload.style.display = 'none';

          progressContainer.style.display = 'flex';
          gsap.fromTo(progressContainer, { opacity: 0, scaleX: 0.7 }, { opacity: 1, scaleX: 1, duration: 0.7, ease: 'power3.out' });

          let currentProgress = 0;
          const interval = setInterval(() => {
            currentProgress += 2; 
            if (currentProgress >= 100) {
              currentProgress = 100;
              clearInterval(interval);

              setTimeout(() => {
                gsap.to(progressContainer, {
                  opacity: 0,
                  duration: 0.4,
                  onComplete: () => {
                    progressContainer.style.display = 'none';
                    fileInfo.innerHTML = `<span class="text-green-600 font-semibold">✔ ${fileName} (Berhasil diunggah)</span>`;
                    
                    btnRemove.style.display = 'block';
                    gsap.fromTo(btnRemove, { opacity: 0, scale: 0.3 }, { opacity: 1, scale: 1, duration: 0.5, ease: 'back.out(1.5)' });
                  }
                });
              }, 500);
            }

            progressBar.style.width = currentProgress + '%';
          }, 50); 
        }
      });
    }
  }

  function resetFile(type) {
    const input = document.getElementById(`input-${type}`);
    const btnUpload = document.getElementById(`btn-upload-${type}`);
    const progressBar = document.getElementById(`progress-bar-${type}`);
    const fileInfo = document.querySelector(`.file-info-${type}`);
    const btnRemove = document.getElementById(`btn-remove-${type}`);

    input.value = '';
    btnRemove.style.display = 'none';

    if (type === 'proposal') fileInfo.textContent = 'Format PDF/DOC (Maks. 2MB)';
    if (type === 'ktp') fileInfo.textContent = 'Format PDF/JPG/PNG';
    if (type === 'surat') fileInfo.textContent = 'Format PDF/DOC (Maks. 2MB)';

    progressBar.style.width = '0%';

    btnUpload.style.display = 'flex';
    gsap.fromTo(btnUpload, { scale: 0.7, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.6, ease: 'back.out(1.2)' });
  }

  // Fungsi Validasi Kustom Sebelum Form Dikirim
  function validateForm(event) {
    const proposal = document.getElementById('input-proposal').files.length;
    const ktp = document.getElementById('input-ktp').files.length;
    const surat = document.getElementById('input-surat').files.length;

    if (!proposal || !ktp || !surat) {
      event.preventDefault(); // Menghentikan form agar tidak terkirim
      alert('⚠️ Peringatan: Mohon lengkapi dan unggah semua berkas persyaratan (Proposal Magang, Scan KTP, dan Surat Lamaran) terlebih dahulu!');
      return false;
    }
    return true;
  }

  const tglMulai = document.getElementById('tanggal_mulai');
  const tglSelesai = document.getElementById('tanggal_selesai');
  const inputDurasi = document.getElementById('durasi');

  function hitungDurasiMagang() {
    if (tglMulai.value && tglSelesai.value) {
      const start = new Date(tglMulai.value);
      const end = new Date(tglSelesai.value);

      if (end >= start) {
        let diffTime = Math.abs(end - start);
        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        let months = Math.floor(diffDays / 30);
        let remainingDays = diffDays % 30;

        let hasil = '';
        if (months > 0) hasil += `${months} Bulan `;
        if (remainingDays > 0 || months === 0) hasil += `(${diffDays} Hari)`;
        
        inputDurasi.value = hasil.trim();
      } else {
        inputDurasi.value = "Tanggal selesai tidak valid";
      }
    }
  }

  tglMulai.addEventListener('change', hitungDurasiMagang);
  tglSelesai.addEventListener('change', hitungDurasiMagang);
</script>
</body>
</html>