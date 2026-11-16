@php
  $isEdit = ! empty($item);
  $jenis  = $isEdit ? $item['jenis'] : old('jenis', $jenisAwal ?? 'Magang');

  $val = fn ($key, $default = '') =>
    old($key, $isEdit ? ($item[$key] ?? $default) : $default);

  $silabusUrl  = $isEdit ? ($item['silabus_url'] ?? null) : null;
  $silabusNama = $isEdit ? ($item['silabus_nama'] ?? '') : '';

  $action = $isEdit
    ? route('admin.lowongan.update', ['jenis' => strtolower($item['jenis']), 'id' => $item['id']])
    : route('admin.lowongan.store');

  $backUrl = route('admin.lowongan.index');

  // Persyaratan: dari database (edit), atau input sebelumnya kalau validasi gagal
  $persyaratanAwal = $isEdit ? ($item['persyaratan'] ?? []) : [];
  $persyaratanLama = old('persyaratan');
  if (is_array($persyaratanLama)) {
    $persyaratanAwal = array_values($persyaratanLama);
  }

  $persyaratanAwal = collect($persyaratanAwal)->map(fn ($p) => [
    'id'          => $p['id_persyaratan'] ?? null,
    'nama_berkas' => $p['nama_berkas'] ?? '',
    'format_file' => $p['format_file'] ?? 'pdf',
    'wajib'       => (int) (bool) ($p['wajib'] ?? 1),
  ])->values()->all();

  // Error dari server (kalau validasi di server gagal), satu pesan per kolom
  $serverErrors = collect($errors->messages())->map(fn ($m) => $m[0])->all();

  $init = [
    'isEdit'       => $isEdit,
    'jenis'        => $jenis,
    'statusList'   => array_values($statusList),
    'serverErrors' => $serverErrors,

<<<<<<< HEAD
    'statusOptions' => array_values($statusList),

    'hasExisting' => (bool) $silabusUrl,
    'existingUrl' => $silabusUrl,
=======
    'hasExisting'  => (bool) $silabusUrl,
    'existingUrl'  => $silabusUrl,
>>>>>>> vina
    'existingName' => $silabusNama,

    'persyaratan'  => $persyaratanAwal,

    'values' => [
      'judul'       => (string) $val('judul'),
      'deskripsi'   => (string) $val('deskripsi'),
      'kualifikasi' => (string) $val('kualifikasi'),
      'kuota'       => (string) $val('kuota', 1),
      'status'      => (string) $val('status', 'draft'),
      'tgl_buka'    => (string) $val('tgl_buka'),
      'tgl_tutup'   => (string) $val('tgl_tutup'),
      'tgl_mulai'   => (string) $val('tgl_mulai'),
      'tgl_selesai' => (string) $val('tgl_selesai'),
    ],
  ];
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $isEdit ? 'Edit' : 'Tambah' }} {{ $jenis === 'Magang' ? 'Program Magang' : 'Lowongan Kerja' }} - ERA</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Caveat:wght@500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
      colors: { paper:'#faf9fc', lime:'#e9f56b', brand:'#34d3b0', ewforange:'#f97316' } } } };
  </script>
  <style>
    [x-cloak] { display: none !important; }
    .field { width: 100%; border-radius: .75rem; border: 1px solid rgba(0,0,0,.12); background: #fff; padding: .7rem .95rem; font-size: .85rem; outline: none; transition: border-color .2s, box-shadow .2s; }
    .field:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); }
<<<<<<< HEAD

/* CUSTOM ANIMATED DROPDOWN */
.custom-dropdown-panel {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  z-index: 50;
  width: 100%;
  padding: 7px;
  background: #fff;
  border: 1px solid rgba(0, 0, 0, .08);
  border-radius: 15px;
  box-shadow: 0 16px 40px rgba(24, 24, 27, .13);
  transform-origin: top center;
}

.custom-dropdown-option {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  width: 100%;
  padding: 10px 12px;
  border: 0;
  border-radius: 10px;
  background: transparent;
  color: #52525b;
  font-size: 12px;
  font-weight: 600;
  text-align: left;
  cursor: pointer;
  transition: background .2s ease, color .2s ease, transform .2s ease;
}

.custom-dropdown-option:hover {
  background: #fff7ed;
  color: #ea580c;
  transform: translateX(2px);
}

.custom-dropdown-option.selected {
  background: #fff1e6;
  color: #ea580c;
}

@media (prefers-reduced-motion: reduce) {
  .custom-dropdown-option {
    transition: none;
  }
}

=======
    .field::placeholder { color: #a1a1aa; }
    .card { background: #fff; border: 1px solid rgba(0,0,0,.06); border-radius: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,.03); }
    .card-head { padding: 1.1rem 1.4rem; border-bottom: 1px solid rgba(0,0,0,.06); }
    .card-body { padding: 1.4rem; }
    .lbl { display: block; margin-bottom: .4rem; font-size: .75rem; font-weight: 600; color: #3f3f46; }
    .hint { margin-top: .4rem; font-size: .72rem; color: #a1a1aa; }
    .err { margin-top: .4rem; font-size: .72rem; font-weight: 600; color: #e11d48; }
    .chip { display: inline-flex; align-items: center; gap: .3rem; padding: .35rem .75rem; border-radius: 9999px; border: 1px solid rgba(0,0,0,.1); background: #fff; font-size: .72rem; font-weight: 600; color: #52525b; transition: all .2s; }
    .chip:hover:not(:disabled) { border-color: #f97316; color: #c2570c; background: #fff7ed; }
    .chip:disabled { opacity: .45; cursor: not-allowed; }
    .pill { padding: .35rem .8rem; border-radius: 9999px; font-size: .7rem; font-weight: 700; border: 1px solid rgba(0,0,0,.1); background: #fff; color: #71717a; transition: all .2s; }
    .pill.on { background: #18181b; border-color: #18181b; color: #fff; }
    .step-btn { display: grid; place-items: center; width: 2.6rem; border-radius: .75rem; border: 1px solid rgba(0,0,0,.12); background: #fff; font-size: 1.1rem; font-weight: 600; color: #52525b; transition: all .2s; }
    .step-btn:hover { border-color: #f97316; color: #f97316; background: #fff7ed; }
>>>>>>> vina
  </style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

  <div class="flex min-h-screen flex-col lg:flex-row">

    @include('partials.sidebar')

    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-28 lg:pb-16">

      <div class="w-full max-w-6xl mx-auto min-w-0" x-data="lowonganForm(@js($init))">

        <!-- HEADER -->
        <div class="mb-6">
          <a href="{{ $backUrl }}"
             class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-zinc-400 hover:text-ewforange transition mb-3">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Kelola Lowongan
          </a>
          <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
              <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase"
                    x-text="isEdit ? 'Edit data' : 'Data baru'"></span>
              <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2 break-words"
                  x-text="(isEdit ? 'Edit ' : 'Tambah ') + (jenis === 'Magang' ? 'Program Magang' : 'Lowongan Kerja')"></h1>
              <p class="text-xs text-zinc-500 mt-1">
                Isi semua kolom bertanda <span class="text-rose-500 font-bold">*</span>. Panel kanan menunjukkan apa yang masih kurang.
              </p>
            </div>
          </div>
        </div>

        <!-- Peringatan ringkas kalau ada kolom bermasalah -->
        <div x-show="submitted && errorCount > 0" x-cloak x-transition
             class="mb-5 flex items-start gap-3 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xs px-4 py-3">
          <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
          <p><b x-text="errorCount"></b> kolom belum diisi atau belum sesuai. Kolom yang bermasalah ditandai merah.</p>
        </div>

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" novalidate
              @submit="onSubmit($event)">
          @csrf
          @if($isEdit)
            @method('PUT')
          @endif

          <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_21rem] items-start">

            <!-- ================= KOLOM KIRI ================= -->
            <div class="space-y-6 min-w-0">

              <!-- 1. JENIS -->
              <section class="card">
                <div class="card-head">
                  <h2 class="text-sm font-bold text-zinc-900">1. Jenis</h2>
                  <p class="text-[11px] text-zinc-400 mt-0.5">
                    {{ $isEdit ? 'Jenis tidak bisa diubah setelah data dibuat.' : 'Pilih dulu apakah ini lowongan pekerjaan atau program magang.' }}
                  </p>
                </div>
<<<<<<< HEAD
              @else
                <div>
                  <label class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Jenis <span class="text-rose-500">*</span></label>
                  
<div class="relative w-full sm:max-w-xs"
     x-data="{ open: false }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

  <input type="hidden" name="jenis" :value="jenis">

  <button type="button"
          @click="open = !open"
          :aria-expanded="open.toString()"
          class="field flex items-center justify-between gap-3 text-left cursor-pointer">

    <span x-text="jenis === 'Magang' ? 'Magang (Program)' : 'Kerja (Lowongan)'"></span>

    <svg class="w-4 h-4 shrink-0 text-zinc-400 transition-transform duration-300"
         :class="{ 'rotate-180': open }"
         viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="m6 9 6 6 6-6"/>
    </svg>
  </button>

  <div x-cloak
       x-show="open"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
       x-transition:enter-end="opacity-100 translate-y-0 scale-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-y-0 scale-100"
       x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
       class="custom-dropdown-panel">

    <button type="button"
            @click="jenis = 'Magang'; open = false"
            class="custom-dropdown-option"
            :class="{ 'selected': jenis === 'Magang' }">

      <span>Magang (Program)</span>

      <svg x-show="jenis === 'Magang'"
           class="w-4 h-4 text-orange-600"
           viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="m5 12 4 4L19 6"/>
      </svg>
    </button>

    <button type="button"
            @click="jenis = 'Kerja'; open = false"
            class="custom-dropdown-option"
            :class="{ 'selected': jenis === 'Kerja' }">

      <span>Kerja (Lowongan)</span>

      <svg x-show="jenis === 'Kerja'"
           class="w-4 h-4 text-orange-600"
           viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="m5 12 4 4L19 6"/>
      </svg>
    </button>

  </div>
</div>

                </div>
              @endif

              <!-- Judul / Nama Program (maks 100 karakter) -->
              <div>
                <div class="flex items-center justify-between mb-1.5">
                  <label for="field-judul" class="block text-[11px] font-semibold text-zinc-500">
                    <span x-text="jenis === 'Magang' ? 'Nama Program Magang' : 'Judul Lowongan'"></span>
                    <span class="text-rose-500">*</span>
                  </label>
                  <span class="text-[11px] text-zinc-400"><span x-text="f.judul.length"></span>/100</span>
                </div>
                <input type="text" id="field-judul" name="judul" x-model="f.judul" maxlength="100"
                       :placeholder="jenis === 'Magang' ? 'Contoh: Konsultan Bisnis Intern' : 'Contoh: UI/UX Designer'"
                       :class="{ '!border-rose-400': errors.judul }"
                       class="field">
                <p x-show="errors.judul" x-cloak x-text="errors.judul" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
              </div>

              <!-- Deskripsi -->
              <div>
                <label for="field-deskripsi" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Deskripsi <span class="text-rose-500">*</span></label>
                <textarea id="field-deskripsi" name="deskripsi" x-model="f.deskripsi" rows="4"
                          placeholder="Jelaskan tugas dan tanggung jawab..."
                          :class="{ '!border-rose-400': errors.deskripsi }"
                          class="field resize-y"></textarea>
                <p x-show="errors.deskripsi" x-cloak x-text="errors.deskripsi" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
              </div>

              <!-- Khusus Kerja: Kualifikasi -->
              <div x-show="jenis === 'Kerja'">
                <label for="field-kualifikasi" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Kualifikasi <span class="text-rose-500">*</span></label>
                <textarea id="field-kualifikasi" name="kualifikasi" x-model="f.kualifikasi" rows="4"
                          placeholder="Contoh: S1 semua jurusan, pengalaman 1 tahun..."
                          :class="{ '!border-rose-400': errors.kualifikasi }"
                          class="field resize-y"></textarea>
                <p x-show="errors.kualifikasi" x-cloak x-text="errors.kualifikasi" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
              </div>

              <!-- Khusus Magang: Silabus (upload file, wajib) -->
              <div x-show="jenis === 'Magang'">
                <label for="field-silabus" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Silabus <span class="text-rose-500">*</span></label>
                <div id="field-silabus" tabindex="-1"
                     :class="{ '!border-rose-400': errors.silabus }"
                     class="upload-item bg-paper rounded-2xl p-4 border border-black/5 flex flex-col md:flex-row items-center justify-between gap-4 outline-none">

                  <div class="flex items-center gap-3.5 w-full md:w-1/2 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-ewforange flex items-center justify-center font-bold text-base shrink-0">📄</div>
                    <div class="min-w-0">
                      <p class="text-xs font-bold text-zinc-900">Silabus <span class="text-rose-500">*</span></p>

                      <p x-show="uploadState === 'idle' || uploadState === 'uploading'"
                         class="text-[10px] text-zinc-400 truncate">Format PDF/DOC/DOCX (Maks. 5MB)</p>

                      <p x-show="uploadState === 'done'" x-cloak :title="fileName"
                         class="text-[10px] text-green-600 font-semibold truncate">
                        ✔ <span x-text="fileName"></span> (Siap disimpan)
                      </p>

                      <p x-show="uploadState === 'existing'" x-cloak
                         class="text-[10px] text-green-600 font-semibold truncate">
                        ✔ <a :href="existingUrl" target="_blank" class="underline hover:text-ewforange" x-text="existingName"></a> (File saat ini)
                      </p>
                    </div>
                  </div>

                  <div class="w-full md:w-1/2 flex items-center justify-end relative">
                    <input type="file" id="input-silabus" name="silabus" accept=".pdf,.doc,.docx"
                           x-ref="fileInput" @change="onFile($event)" class="hidden">

                    <button type="button" x-ref="btnUpload"
                            x-show="uploadState === 'idle' || uploadState === 'existing'"
                            @click="$refs.fileInput.click()"
                            class="px-6 py-2.5 bg-zinc-500 hover:bg-ewforange/80 text-white text-xs font-bold rounded-full shadow-md flex items-center gap-2 transition hover:scale-105">
                      <span x-text="uploadState === 'existing' ? '☁️ GANTI FILE' : '☁️ UPLOAD'"></span>
                    </button>

                    <div x-ref="progressWrap" x-show="uploadState === 'uploading'" x-cloak class="w-full">
                      <div class="w-full bg-zinc-200 h-3 rounded-full overflow-hidden shadow-inner">
                        <div class="bg-ewforange h-full transition-all duration-300" :style="'width:' + progress + '%'"></div>
=======
                <div class="card-body">
                  @if($isEdit)
                    <div class="inline-flex items-center gap-3 rounded-2xl px-4 py-3 {{ $jenis === 'Magang' ? 'bg-orange-50 text-ewforange' : 'bg-zinc-100 text-zinc-800' }}">
                      <span class="text-lg">{{ $jenis === 'Magang' ? '🎓' : '💼' }}</span>
                      <div>
                        <p class="text-sm font-bold">{{ $jenis === 'Magang' ? 'Program Magang' : 'Lowongan Kerja' }}</p>
                        <p class="text-[11px] opacity-70">{{ $jenis === 'Magang' ? 'Peserta magang, ada silabus & periode pelaksanaan' : 'Karyawan, ada kualifikasi' }}</p>
>>>>>>> vina
                      </div>
                    </div>
                  @else
                    <div class="grid sm:grid-cols-2 gap-3">
                      <label class="cursor-pointer">
                        <input type="radio" name="jenis" value="Magang" x-model="jenis" class="peer sr-only">
                        <div class="rounded-2xl border-2 border-black/10 p-4 transition peer-checked:border-ewforange peer-checked:bg-orange-50 hover:border-orange-300">
                          <p class="text-lg">🎓</p>
                          <p class="text-sm font-bold mt-1">Program Magang</p>
                          <p class="text-[11px] text-zinc-500">Ada silabus dan periode pelaksanaan.</p>
                        </div>
                      </label>
                      <label class="cursor-pointer">
                        <input type="radio" name="jenis" value="Kerja" x-model="jenis" class="peer sr-only">
                        <div class="rounded-2xl border-2 border-black/10 p-4 transition peer-checked:border-ewforange peer-checked:bg-orange-50 hover:border-orange-300">
                          <p class="text-lg">💼</p>
                          <p class="text-sm font-bold mt-1">Lowongan Kerja</p>
                          <p class="text-[11px] text-zinc-500">Ada kualifikasi pelamar.</p>
                        </div>
                      </label>
                    </div>
                  @endif
                </div>
              </section>

              <!-- 2. INFORMASI UTAMA -->
              <section class="card">
                <div class="card-head">
                  <h2 class="text-sm font-bold text-zinc-900">2. Informasi utama</h2>
                  <p class="text-[11px] text-zinc-400 mt-0.5">Bagian ini yang pertama dibaca pelamar di halaman publik.</p>
                </div>
                <div class="card-body space-y-5">

                  <!-- Judul -->
                  <div>
                    <div class="flex items-center justify-between">
                      <label for="field-judul" class="lbl !mb-1.5">
                        <span x-text="jenis === 'Magang' ? 'Nama program magang' : 'Judul lowongan'"></span>
                        <span class="text-rose-500">*</span>
                      </label>
                      <span class="text-[11px] text-zinc-400"><span x-text="f.judul.length"></span>/100</span>
                    </div>
                    <input type="text" id="field-judul" name="judul" x-model="f.judul" maxlength="100"
                           :placeholder="jenis === 'Magang' ? 'Contoh: Konsultan Bisnis Intern' : 'Contoh: UI/UX Designer'"
                           :class="{ '!border-rose-400': errors.judul }" class="field">
                    <p x-show="errors.judul" x-cloak x-text="errors.judul" class="err"></p>
                  </div>

                  <!-- Deskripsi -->
                  <div>
                    <label for="field-deskripsi" class="lbl">
                      <span x-text="jenis === 'Magang' ? 'Deskripsi program' : 'Deskripsi pekerjaan'"></span>
                      <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="field-deskripsi" name="deskripsi" x-model="f.deskripsi" rows="6"
                              :placeholder="jenis === 'Magang'
                                ? 'Jelaskan apa yang akan dipelajari dan dikerjakan peserta magang...'
                                : 'Satu tugas per baris, contoh:\nMendesain antarmuka aplikasi web\nMembuat prototype di Figma'"
                              :class="{ '!border-rose-400': errors.deskripsi }" class="field resize-y leading-relaxed"></textarea>
                    <p x-show="jenis === 'Kerja'" class="hint">💡 Tulis <b>satu poin per baris</b>. Di halaman lowongan, tiap baris otomatis tampil sebagai poin berpeluru.</p>
                    <p x-show="errors.deskripsi" x-cloak x-text="errors.deskripsi" class="err"></p>
                  </div>

                  <!-- Kualifikasi (Kerja) -->
                  <div x-show="jenis === 'Kerja'">
                    <label for="field-kualifikasi" class="lbl">Kualifikasi <span class="text-rose-500">*</span></label>
                    <textarea id="field-kualifikasi" name="kualifikasi" x-model="f.kualifikasi" rows="5"
                              placeholder="Satu syarat per baris, contoh:&#10;S1 semua jurusan&#10;Pengalaman minimal 1 tahun"
                              :class="{ '!border-rose-400': errors.kualifikasi }" class="field resize-y leading-relaxed"></textarea>
                    <p class="hint">💡 Satu syarat per baris, tampil sebagai poin di halaman lowongan.</p>
                    <p x-show="errors.kualifikasi" x-cloak x-text="errors.kualifikasi" class="err"></p>
                  </div>
                </div>
              </section>

              <!-- 3. SILABUS (Magang) -->
              <section class="card" x-show="jenis === 'Magang'">
                <div class="card-head">
                  <h2 class="text-sm font-bold text-zinc-900">3. Silabus <span class="text-rose-500">*</span></h2>
                  <p class="text-[11px] text-zinc-400 mt-0.5">Materi program yang bisa diunduh calon peserta. PDF, DOC, atau DOCX, maksimal 5 MB.</p>
                </div>
                <div class="card-body">
                  <input type="file" name="silabus" accept=".pdf,.doc,.docx" x-ref="fileInput" @change="onFile($event)" class="hidden">

                  <div id="field-silabus" tabindex="-1"
                       @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="onDrop($event)"
                       :class="[
                         dragging ? 'border-ewforange bg-orange-50' : (errors.silabus ? 'border-rose-400 bg-rose-50/40' : 'border-black/15 bg-paper'),
                       ]"
                       class="rounded-2xl border-2 border-dashed p-5 text-center transition outline-none">

                    <!-- File baru dipilih -->
                    <template x-if="file">
                      <div class="flex flex-wrap items-center justify-between gap-3 text-left">
                        <div class="flex items-center gap-3 min-w-0">
                          <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 grid place-items-center text-lg shrink-0">📄</div>
                          <div class="min-w-0">
                            <p class="text-sm font-semibold truncate" x-text="fileName"></p>
                            <p class="text-[11px] text-emerald-600 font-medium">Siap diunggah · <span x-text="fileSize"></span></p>
                          </div>
                        </div>
                        <div class="flex gap-2">
                          <button type="button" @click="$refs.fileInput.click()" class="chip">Ganti</button>
                          <button type="button" @click="resetFile()" class="chip hover:!border-rose-300 hover:!text-rose-600 hover:!bg-rose-50">Batalkan</button>
                        </div>
                      </div>
                    </template>

                    <!-- File lama (edit) -->
                    <template x-if="!file && hasExisting">
                      <div class="flex flex-wrap items-center justify-between gap-3 text-left">
                        <div class="flex items-center gap-3 min-w-0">
                          <div class="w-11 h-11 rounded-xl bg-orange-50 text-ewforange grid place-items-center text-lg shrink-0">📄</div>
                          <div class="min-w-0">
                            <p class="text-sm font-semibold truncate" x-text="existingName"></p>
                            <a :href="existingUrl" target="_blank" class="text-[11px] text-ewforange font-semibold hover:underline">Lihat file saat ini ↗</a>
                          </div>
                        </div>
                        <button type="button" @click="$refs.fileInput.click()" class="chip">Ganti file</button>
                      </div>
                    </template>

                    <!-- Kosong -->
                    <template x-if="!file && !hasExisting">
                      <div>
                        <p class="text-2xl">☁️</p>
                        <p class="text-sm font-semibold mt-1">Tarik file ke sini</p>
                        <p class="text-[11px] text-zinc-400 mb-3">atau</p>
                        <button type="button" @click="$refs.fileInput.click()"
                                class="px-5 py-2.5 rounded-full bg-zinc-900 hover:bg-black text-white text-xs font-semibold transition">Pilih file</button>
                      </div>
                    </template>
                  </div>

                  <p x-show="fileError" x-cloak x-text="fileError" class="err"></p>
                  <p x-show="errors.silabus" x-cloak x-text="errors.silabus" class="err"></p>
                </div>
              </section>

              <!-- 4. PERSYARATAN BERKAS -->
              <section class="card" id="sec-persyaratan">
                <div class="card-head flex flex-wrap items-center justify-between gap-3">
                  <div>
                    <h2 class="text-sm font-bold text-zinc-900"><span x-text="jenis === 'Magang' ? '4' : '3'"></span>. Persyaratan berkas</h2>
                    <p class="text-[11px] text-zinc-400 mt-0.5">Dokumen yang harus diunggah pelamar saat mendaftar.</p>
                  </div>
                  <span class="text-[11px] font-semibold text-zinc-500 bg-zinc-100 rounded-full px-3 py-1" x-text="persyaratan.length + ' berkas'"></span>
                </div>

                <div class="card-body space-y-4">

<<<<<<< HEAD
  <!-- Error jumlah persyaratan -->
  <p
    x-show="errors.persyaratan"
    x-cloak
    x-text="errors.persyaratan"
    class="text-[11px] font-medium text-rose-500"
  ></p>

  <!-- Daftar persyaratan -->
  <div class="space-y-3">

    <template x-for="(item, index) in persyaratan" :key="index">

      <div
        class="rounded-2xl border border-black/5 bg-paper p-4 sm:p-5 space-y-4"
      >

        <!-- Header baris -->
        <div class="flex items-center justify-between gap-3">

          <div class="flex items-center gap-2">

            <span
              class="w-7 h-7 rounded-lg bg-orange-50 text-ewforange flex items-center justify-center text-xs font-bold"
              x-text="index + 1"
            ></span>

            <span class="text-xs font-bold text-zinc-800">
              Dokumen Persyaratan
            </span>

          </div>

          <button
            type="button"
            @click="hapusPersyaratan(index)"
            :disabled="persyaratan.length <= 1"
            :class="persyaratan.length <= 1
              ? 'text-zinc-300 cursor-not-allowed'
              : 'text-zinc-400 hover:text-rose-500'"
            class="text-xs font-bold transition"
            aria-label="Hapus persyaratan"
          >
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14"
              />
            </svg>
          </button>

        </div>

        <!-- Nama berkas -->
        <div>

          <label
            class="block text-[11px] font-semibold text-zinc-500 mb-1.5"
          >
            Nama Berkas
            <span class="text-rose-500">*</span>
          </label>

          <input
            type="text"
            :name="'persyaratan[' + index + '][nama_berkas]'"
            x-model="item.nama_berkas"
            maxlength="255"
            placeholder="Contoh: Curriculum Vitae (CV)"
            class="field"
            :class="{
              '!border-rose-400':
                errors['persyaratan_' + index]
            }"
          >

          <p
            x-show="errors['persyaratan_' + index]"
            x-cloak
            x-text="errors['persyaratan_' + index]"
            class="text-[11px] font-medium text-rose-500 mt-1.5"
          ></p>

        </div>

        <!-- Format file dan wajib -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

          <!-- Format file -->
          <div>

            <label
              class="block text-[11px] font-semibold text-zinc-500 mb-1.5"
            >
              Format File
              <span class="text-rose-500">*</span>
            </label>

            
<div class="relative"
     x-data="{ open: false }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

  <input type="hidden"
         :name="'persyaratan[' + index + '][format_file]'"
         :value="item.format_file">

  <button type="button"
          @click="open = !open"
          :aria-expanded="open.toString()"
          class="field flex items-center justify-between gap-3 text-left cursor-pointer">

    <span class="uppercase" x-text="item.format_file"></span>

    <svg class="w-4 h-4 shrink-0 text-zinc-400 transition-transform duration-300"
         :class="{ 'rotate-180': open }"
         viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="m6 9 6 6 6-6"/>
    </svg>
  </button>

  <div x-cloak
       x-show="open"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
       x-transition:enter-end="opacity-100 translate-y-0 scale-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-y-0 scale-100"
       x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
       class="custom-dropdown-panel">

    <template x-for="format in ['pdf', 'doc', 'docx']" :key="format">
      <button type="button"
              @click="item.format_file = format; open = false"
              class="custom-dropdown-option"
              :class="{ 'selected': item.format_file === format }">

        <span class="uppercase" x-text="format"></span>

        <svg x-show="item.format_file === format"
             class="w-4 h-4 text-orange-600"
             viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="m5 12 4 4L19 6"/>
        </svg>
      </button>
    </template>

  </div>
</div>

          </div>

          <!-- Wajib -->
          <div>

            <label
              class="block text-[11px] font-semibold text-zinc-500 mb-1.5"
            >
              Status Persyaratan
            </label>

            
<div class="relative"
     x-data="{ open: false }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

  <input type="hidden"
         :name="'persyaratan[' + index + '][wajib]'"
         :value="item.wajib">

  <button type="button"
          @click="open = !open"
          :aria-expanded="open.toString()"
          class="field flex items-center justify-between gap-3 text-left cursor-pointer">

    <span x-text="String(item.wajib) === '1' ? 'Wajib' : 'Opsional'"></span>

    <svg class="w-4 h-4 shrink-0 text-zinc-400 transition-transform duration-300"
         :class="{ 'rotate-180': open }"
         viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="m6 9 6 6 6-6"/>
    </svg>
  </button>

  <div x-cloak
       x-show="open"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
       x-transition:enter-end="opacity-100 translate-y-0 scale-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-y-0 scale-100"
       x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
       class="custom-dropdown-panel">

    <button type="button"
            @click="item.wajib = '1'; open = false"
            class="custom-dropdown-option"
            :class="{ 'selected': String(item.wajib) === '1' }">

      <span>Wajib</span>

      <svg x-show="String(item.wajib) === '1'"
           class="w-4 h-4 text-orange-600"
           viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="m5 12 4 4L19 6"/>
      </svg>
    </button>

    <button type="button"
            @click="item.wajib = '0'; open = false"
            class="custom-dropdown-option"
            :class="{ 'selected': String(item.wajib) === '0' }">

      <span>Opsional</span>

      <svg x-show="String(item.wajib) === '0'"
           class="w-4 h-4 text-orange-600"
           viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="m5 12 4 4L19 6"/>
      </svg>
    </button>

  </div>
</div>

          </div>

        </div>

      </div>

    </template>

  </div>

  <p class="text-[11px] text-zinc-400">
    Pelamar akan diminta mengunggah dokumen sesuai persyaratan
    yang ditentukan. Format file mengikuti pilihan admin.
  </p>

</section>

            <!-- SEKSI: KUOTA & STATUS -->
            <section class="space-y-4">
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase pb-3 border-b border-black/5">Kuota & Status</h3>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kuota (hanya angka positif, tidak boleh 0, ada panah atas/bawah) -->
                <div>
                  <label for="field-kuota" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Kuota <span class="text-rose-500">*</span></label>

                  <div class="relative">
                    <input type="text" id="field-kuota" name="kuota" x-model="f.kuota"
                           inputmode="numeric" maxlength="5" placeholder="Contoh: 3"
                           @keydown="if (['-', '+', 'e', 'E', '.', ','].includes($event.key)) $event.preventDefault()"
                           @keydown.arrow-up.prevent="stepKuota(1)"
                           @keydown.arrow-down.prevent="stepKuota(-1)"
                           @input="onKuotaInput($event)"
                           :class="{ '!border-rose-400': errors.kuota }"
                           class="field !pr-10">

                    <div class="absolute right-1.5 top-1/2 -translate-y-1/2 flex flex-col">
                      <button type="button" tabindex="-1" @click="stepKuota(1)" aria-label="Tambah kuota"
                              class="h-4 w-6 flex items-center justify-center text-zinc-400 hover:text-ewforange transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                      </button>
                      <button type="button" tabindex="-1" @click="stepKuota(-1)" aria-label="Kurangi kuota"
                              class="h-4 w-6 flex items-center justify-center text-zinc-400 hover:text-ewforange transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                      </button>
=======
                  <!-- Tambah cepat -->
                  <div>
                    <p class="text-[11px] font-semibold text-zinc-500 mb-2">Tambah cepat:</p>
                    <div class="flex flex-wrap gap-2">
                      <template x-for="p in presets" :key="p">
                        <button type="button" class="chip" :disabled="sudahAda(p)" @click="tambahPreset(p)">
                          <span x-text="sudahAda(p) ? '✓' : '+'"></span> <span x-text="p"></span>
                        </button>
                      </template>
>>>>>>> vina
                    </div>
                  </div>

                  <p x-show="errors.persyaratan" x-cloak x-text="errors.persyaratan" class="err"></p>

<<<<<<< HEAD
                <div>
                  <label for="field-status" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Status <span class="text-rose-500">*</span></label>
                  
<div class="relative"
     x-data="{ open: false }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

  <input type="hidden" id="field-status"
         name="status" :value="f.status">

  <button type="button"
          @click="open = !open"
          :aria-expanded="open.toString()"
          :class="{ '!border-rose-400': errors.status }"
          class="field flex items-center justify-between gap-3 text-left cursor-pointer capitalize">

    <span x-text="f.status"></span>

    <svg class="w-4 h-4 shrink-0 text-zinc-400 transition-transform duration-300"
         :class="{ 'rotate-180': open }"
         viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="m6 9 6 6 6-6"/>
    </svg>
  </button>

  <div x-cloak
       x-show="open"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
       x-transition:enter-end="opacity-100 translate-y-0 scale-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-y-0 scale-100"
       x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
       class="custom-dropdown-panel">

    <template x-for="statusOption in statusOptions" :key="statusOption">
      <button type="button"
              @click="f.status = statusOption; open = false; revalidate()"
              class="custom-dropdown-option capitalize"
              :class="{ 'selected': f.status === statusOption }">

        <span x-text="statusOption"></span>

        <svg x-show="f.status === statusOption"
             class="w-4 h-4 text-orange-600"
             viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="m5 12 4 4L19 6"/>
        </svg>
      </button>
    </template>

  </div>
</div>

                  <p x-show="errors.status" x-cloak x-text="errors.status" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
                </div>
              </div>
            </section>
=======
                  <!-- Daftar -->
                  <div class="space-y-3">
                    <template x-for="(item, index) in persyaratan" :key="item._k">
                      <div class="rounded-2xl border bg-paper p-4"
                           :class="errors['persyaratan_' + index] ? 'border-rose-300' : 'border-black/5'">
>>>>>>> vina

                        <input type="hidden" :name="'persyaratan[' + index + '][id_persyaratan]'" :value="item.id ?? ''">
                        <input type="hidden" :name="'persyaratan[' + index + '][format_file]'" :value="item.format_file">
                        <input type="hidden" :name="'persyaratan[' + index + '][wajib]'" :value="item.wajib ? 1 : 0">

                        <div class="flex items-start gap-3">
                          <span class="w-7 h-7 mt-1.5 shrink-0 rounded-lg bg-orange-50 text-ewforange grid place-items-center text-xs font-bold" x-text="index + 1"></span>

                          <div class="flex-1 min-w-0 space-y-3">
                            <input type="text" :name="'persyaratan[' + index + '][nama_berkas]'"
                                   x-model="item.nama_berkas" maxlength="255"
                                   placeholder="Nama berkas, contoh: Curriculum Vitae (CV)"
                                   :class="{ '!border-rose-400': errors['persyaratan_' + index] }" class="field">

                            <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                              <!-- Format -->
                              <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-semibold text-zinc-500 mr-1">Format</span>
                                <template x-for="fmt in ['pdf', 'doc', 'docx']" :key="fmt">
                                  <button type="button" class="pill" :class="{ 'on': item.format_file === fmt }"
                                          @click="item.format_file = fmt" x-text="fmt.toUpperCase()"></button>
                                </template>
                              </div>

                              <!-- Wajib / opsional -->
                              <button type="button" role="switch" :aria-checked="item.wajib" @click="item.wajib = !item.wajib"
                                      class="flex items-center gap-2 text-[11px] font-semibold text-zinc-600">
                                <span class="relative inline-block w-9 h-5 rounded-full transition" :class="item.wajib ? 'bg-ewforange' : 'bg-zinc-300'">
                                  <span class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform" :class="item.wajib ? 'translate-x-4' : ''"></span>
                                </span>
                                <span x-text="item.wajib ? 'Wajib' : 'Opsional'"></span>
                              </button>
                            </div>

                            <p x-show="errors['persyaratan_' + index]" x-cloak x-text="errors['persyaratan_' + index]" class="err !mt-0"></p>
                          </div>

                          <!-- Aksi baris -->
                          <div class="flex flex-col gap-1 shrink-0">
                            <button type="button" @click="geser(index, -1)" :disabled="index === 0" aria-label="Naikkan"
                                    class="w-7 h-7 rounded-lg grid place-items-center text-zinc-400 hover:bg-white hover:text-zinc-800 disabled:opacity-30 disabled:cursor-not-allowed transition">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                            </button>
                            <button type="button" @click="geser(index, 1)" :disabled="index === persyaratan.length - 1" aria-label="Turunkan"
                                    class="w-7 h-7 rounded-lg grid place-items-center text-zinc-400 hover:bg-white hover:text-zinc-800 disabled:opacity-30 disabled:cursor-not-allowed transition">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <button type="button" @click="hapusPersyaratan(index)" :disabled="persyaratan.length <= 1" aria-label="Hapus"
                                    class="w-7 h-7 rounded-lg grid place-items-center text-zinc-400 hover:bg-rose-50 hover:text-rose-500 disabled:opacity-30 disabled:cursor-not-allowed transition">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14"/></svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    </template>
                  </div>

                  <button type="button" @click="tambahPersyaratan()"
                          class="w-full rounded-2xl border-2 border-dashed border-black/15 py-3 text-xs font-bold text-zinc-500 hover:border-ewforange hover:text-ewforange hover:bg-orange-50 transition">
                    + Tambah berkas lain
                  </button>
                </div>
              </section>

              <!-- 5. PERIODE PELAKSANAAN (Magang) -->
              <section class="card" x-show="jenis === 'Magang'">
                <div class="card-head">
                  <h2 class="text-sm font-bold text-zinc-900">5. Periode pelaksanaan magang</h2>
                  <p class="text-[11px] text-zinc-400 mt-0.5">Kapan peserta magang benar-benar bekerja. Durasi dihitung otomatis.</p>
                </div>
                <div class="card-body space-y-4">
                  <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                      <label for="field-tgl_mulai" class="lbl">Tanggal mulai <span class="text-rose-500">*</span></label>
                      <input type="date" id="field-tgl_mulai" name="tgl_mulai" x-model="f.tgl_mulai"
                             :class="{ '!border-rose-400': errors.tgl_mulai }" class="field">
                      <p x-show="errors.tgl_mulai" x-cloak x-text="errors.tgl_mulai" class="err"></p>
                    </div>
                    <div>
                      <label for="field-tgl_selesai" class="lbl">Tanggal selesai <span class="text-rose-500">*</span></label>
                      <input type="date" id="field-tgl_selesai" name="tgl_selesai" x-model="f.tgl_selesai" :min="f.tgl_mulai"
                             :class="{ '!border-rose-400': errors.tgl_selesai }" class="field">
                      <p x-show="errors.tgl_selesai" x-cloak x-text="errors.tgl_selesai" class="err"></p>
                    </div>
                  </div>

                  <div class="flex flex-wrap items-center gap-2" x-show="f.tgl_mulai">
                    <span class="text-[11px] font-semibold text-zinc-500">Durasi cepat:</span>
                    <button type="button" class="chip" @click="f.tgl_selesai = addMonths(f.tgl_mulai, 1)">1 bulan</button>
                    <button type="button" class="chip" @click="f.tgl_selesai = addMonths(f.tgl_mulai, 3)">3 bulan</button>
                    <button type="button" class="chip" @click="f.tgl_selesai = addMonths(f.tgl_mulai, 6)">6 bulan</button>
                  </div>

                  <div x-show="durasi" x-cloak class="rounded-xl bg-emerald-50 text-emerald-700 text-xs font-semibold px-4 py-2.5">
                    Durasi magang: <span x-text="durasi + ' hari'"></span>
                    <span x-show="durasi >= 30" class="font-normal">(± <span x-text="Math.round(durasi / 30)"></span> bulan)</span>
                  </div>
                </div>
              </section>
            </div>

            <!-- ================= KOLOM KANAN ================= -->
            <aside class="space-y-6 min-w-0 lg:sticky lg:top-6 lg:max-h-[calc(100vh-3rem)] lg:overflow-y-auto lg:pr-1">

              <!-- Status & kuota -->
              <section class="card">
                <div class="card-head"><h2 class="text-sm font-bold text-zinc-900">Publikasi</h2></div>
                <div class="card-body space-y-5">

                  <div id="field-status" tabindex="-1" class="outline-none">
                    <p class="lbl">Status <span class="text-rose-500">*</span></p>
                    <input type="hidden" name="status" :value="f.status">
                    <div class="space-y-2">
                      <template x-for="st in statusList" :key="st">
                        <button type="button" @click="f.status = st"
                                class="w-full flex items-start gap-3 rounded-xl border-2 px-3.5 py-2.5 text-left transition"
                                :class="f.status === st ? 'border-ewforange bg-orange-50' : 'border-black/10 hover:border-orange-200'">
                          <span class="mt-1 w-2.5 h-2.5 rounded-full shrink-0" :class="meta(st).dot"></span>
                          <span class="min-w-0">
                            <span class="block text-xs font-bold" x-text="meta(st).label"></span>
                            <span class="block text-[11px] text-zinc-500" x-text="meta(st).desc"></span>
                          </span>
                        </button>
                      </template>
                    </div>
                    <p x-show="errors.status" x-cloak x-text="errors.status" class="err"></p>
                    <p x-show="tutupLewat" x-cloak class="mt-2 rounded-lg bg-amber-50 text-amber-700 text-[11px] font-medium px-3 py-2">
                      ⚠ Status "Dibuka", tetapi tanggal tutup sudah lewat. Pelamar tidak akan bisa mendaftar.
                    </p>
                  </div>

                  <div>
                    <label for="field-kuota" class="lbl">Kuota <span class="text-rose-500">*</span></label>
                    <div class="flex gap-2">
                      <button type="button" class="step-btn" @click="stepKuota(-1)" aria-label="Kurangi kuota">−</button>
                      <input type="text" id="field-kuota" name="kuota" x-model="f.kuota"
                             inputmode="numeric" maxlength="5" placeholder="1"
                             @keydown="if (['-', '+', 'e', 'E', '.', ','].includes($event.key)) $event.preventDefault()"
                             @keydown.arrow-up.prevent="stepKuota(1)"
                             @keydown.arrow-down.prevent="stepKuota(-1)"
                             @input="onKuotaInput($event)"
                             :class="{ '!border-rose-400': errors.kuota }"
                             class="field text-center font-semibold">
                      <button type="button" class="step-btn" @click="stepKuota(1)" aria-label="Tambah kuota">+</button>
                    </div>
                    <p class="hint">Jumlah orang yang akan diterima.</p>
                    <p x-show="errors.kuota" x-cloak x-text="errors.kuota" class="err"></p>
                  </div>
                </div>
              </section>

              <!-- Periode pendaftaran -->
              <section class="card">
                <div class="card-head">
                  <h2 class="text-sm font-bold text-zinc-900">Periode pendaftaran</h2>
                  <p class="text-[11px] text-zinc-400 mt-0.5">Kapan pelamar bisa mendaftar.</p>
                </div>
                <div class="card-body space-y-4">
                  <div>
                    <label for="field-tgl_buka" class="lbl">Dibuka <span class="text-rose-500">*</span></label>
                    <input type="date" id="field-tgl_buka" name="tgl_buka" x-model="f.tgl_buka"
                           :class="{ '!border-rose-400': errors.tgl_buka }" class="field">
                    <button type="button" class="chip mt-2" @click="f.tgl_buka = hariIni()">Hari ini</button>
                    <p x-show="errors.tgl_buka" x-cloak x-text="errors.tgl_buka" class="err"></p>
                  </div>

                  <div>
                    <label for="field-tgl_tutup" class="lbl">Ditutup <span class="text-rose-500">*</span></label>
                    <input type="date" id="field-tgl_tutup" name="tgl_tutup" x-model="f.tgl_tutup" :min="f.tgl_buka"
                           :class="{ '!border-rose-400': errors.tgl_tutup }" class="field">
                    <div class="flex flex-wrap gap-1.5 mt-2">
                      <button type="button" class="chip" @click="f.tgl_tutup = addDays(f.tgl_buka, 14)">+14 hari</button>
                      <button type="button" class="chip" @click="f.tgl_tutup = addDays(f.tgl_buka, 30)">+30 hari</button>
                      <button type="button" class="chip" @click="f.tgl_tutup = addDays(f.tgl_buka, 60)">+60 hari</button>
                    </div>
                    <p x-show="errors.tgl_tutup" x-cloak x-text="errors.tgl_tutup" class="err"></p>
                    <p x-show="mulaiSebelumTutup" x-cloak class="mt-2 rounded-lg bg-amber-50 text-amber-700 text-[11px] font-medium px-3 py-2">
                      ⚠ Magang dimulai sebelum pendaftaran ditutup. Pastikan ini disengaja.
                    </p>
                  </div>
                </div>
              </section>

              <!-- Kelengkapan + simpan (desktop) -->
              <section class="card">
                <div class="card-body space-y-4">
                  <div>
                    <div class="flex items-center justify-between text-xs font-bold">
                      <span>Kelengkapan</span>
                      <span :class="persen === 100 ? 'text-emerald-600' : 'text-ewforange'" x-text="persen + '%'"></span>
                    </div>
                    <div class="h-2 rounded-full bg-zinc-100 mt-2 overflow-hidden">
                      <div class="h-full rounded-full transition-all duration-500" :class="persen === 100 ? 'bg-emerald-500' : 'bg-ewforange'" :style="'width:' + persen + '%'"></div>
                    </div>
                  </div>

                  <ul class="space-y-1.5">
                    <template x-for="c in checks" :key="c.key">
                      <li>
                        <button type="button" @click="goTo(c.key)" class="w-full flex items-center gap-2.5 text-left text-xs rounded-lg px-2 py-1 hover:bg-zinc-50 transition">
                          <span class="w-4 h-4 rounded-full grid place-items-center text-[9px] font-bold shrink-0"
                                :class="c.ok ? 'bg-emerald-500 text-white' : 'bg-zinc-200 text-transparent'">✓</span>
                          <span :class="c.ok ? 'text-zinc-500' : 'text-zinc-800 font-semibold'" x-text="c.label"></span>
                        </button>
                      </li>
                    </template>
                  </ul>

                  <div class="hidden lg:flex flex-col gap-2 pt-2 border-t border-black/5">
                    <button type="submit" :disabled="saving"
                            class="w-full px-6 py-3 rounded-full bg-ewforange hover:bg-orange-600 disabled:opacity-60 text-white text-xs font-semibold shadow-lg shadow-orange-500/30 transition">
                      <span x-text="saving ? 'Menyimpan…' : '{{ $isEdit ? 'Simpan perubahan' : 'Simpan' }}'"></span>
                    </button>
                    <a href="{{ $backUrl }}" class="w-full text-center px-6 py-3 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-semibold transition">Batal</a>
                    <p x-show="dirty && !saving" x-cloak class="text-center text-[11px] text-amber-600 font-medium">Ada perubahan yang belum disimpan.</p>
                  </div>
                </div>
              </section>
            </aside>
          </div>

          <!-- Bar simpan (mobile / tablet) -->
          <div class="lg:hidden fixed bottom-0 inset-x-0 z-30 flex gap-2 px-4 py-3 bg-white/95 backdrop-blur border-t border-black/10">
            <a href="{{ $backUrl }}" class="px-5 py-3 rounded-full bg-zinc-100 text-zinc-700 text-xs font-semibold">Batal</a>
            <button type="submit" :disabled="saving"
                    class="flex-1 px-6 py-3 rounded-full bg-ewforange disabled:opacity-60 text-white text-xs font-semibold shadow-lg shadow-orange-500/30">
              <span x-text="saving ? 'Menyimpan…' : '{{ $isEdit ? 'Simpan perubahan' : 'Simpan' }}'"></span>
            </button>
          </div>
        </form>

      </div>
    </main>
  </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('lowonganForm', (cfg) => ({
        isEdit: cfg.isEdit,
        jenis: cfg.jenis,
        f: cfg.values,
        statusList: cfg.statusList,
        persyaratan: [],
        uid: 0,

<<<<<<< HEAD
        statusOptions: init.statusOptions || [],

        // Persyaratan berkas
        persyaratan: init.persyaratan || [],
=======
        presets: [
          'Curriculum Vitae (CV)', 'Surat Lamaran', 'KTP', 'Ijazah / Transkrip Nilai',
          'Pas Foto', 'Portofolio', 'Sertifikat Pendukung', 'Surat Keterangan Aktif Kuliah',
        ],
        statusMeta: {
          draft:   { label: 'Draft',   desc: 'Disimpan dulu, belum tampil di situs.', dot: 'bg-amber-500' },
          dibuka:  { label: 'Dibuka',  desc: 'Tampil di situs dan bisa dilamar.',      dot: 'bg-emerald-500' },
          ditutup: { label: 'Ditutup', desc: 'Tidak menerima lamaran lagi.',           dot: 'bg-zinc-400' },
        },
>>>>>>> vina

        // Silabus
        hasExisting: cfg.hasExisting,
        existingUrl: cfg.existingUrl,
        existingName: cfg.existingName,
        file: null,
        fileName: '',
        fileSize: '',
        fileError: '',
        dragging: false,

        kuotaNol: false,
        errors: {},
        submitted: false,
        saving: false,
        dirty: false,

        order: ['judul', 'deskripsi', 'kualifikasi', 'silabus', 'persyaratan', 'kuota', 'status',
                'tgl_buka', 'tgl_tutup', 'tgl_mulai', 'tgl_selesai'],

        init() {
          this.persyaratan = (cfg.persyaratan || []).map((p) => this.baru(p));
          if (!this.persyaratan.length) this.persyaratan.push(this.baru());

          // Error dari server: tampilkan langsung di kolom yang bersangkutan
          const se = cfg.serverErrors || {};
          if (Object.keys(se).length) {
            const e = {};
            Object.keys(se).forEach((k) => {
              const m = k.match(/^persyaratan\.(\d+)\./);
              e[m ? 'persyaratan_' + m[1] : k] = se[k];
            });
            this.errors = e;
            this.submitted = true;
          }

          const ubah = () => { this.dirty = true; this.revalidate(); };
          this.$watch('f', ubah);
          this.$watch('jenis', ubah);
          this.$watch('persyaratan', ubah);

          window.addEventListener('beforeunload', (ev) => {
            if (this.dirty && !this.saving) { ev.preventDefault(); ev.returnValue = ''; }
          });
        },

        meta(st) {
          return this.statusMeta[st] || { label: st.charAt(0).toUpperCase() + st.slice(1), desc: '', dot: 'bg-zinc-400' };
        },

        get errorCount() { return Object.keys(this.errors).length; },
        revalidate() { if (this.submitted) this.validate(); },

        // ---------- TANGGAL ----------
        pad(n) { return String(n).padStart(2, '0'); },
        iso(d) { return d.getFullYear() + '-' + this.pad(d.getMonth() + 1) + '-' + this.pad(d.getDate()); },
        hariIni() { return this.iso(new Date()); },
        addDays(str, n) {
          const d = str ? new Date(str + 'T00:00:00') : new Date();
          d.setDate(d.getDate() + n);
          return this.iso(d);
        },
        addMonths(str, n) {
          const d = new Date(str + 'T00:00:00');
          d.setMonth(d.getMonth() + n);
          d.setDate(d.getDate() - 1);
          return this.iso(d);
        },
        get durasi() {
          if (!this.f.tgl_mulai || !this.f.tgl_selesai) return null;
          const a = new Date(this.f.tgl_mulai + 'T00:00:00');
          const b = new Date(this.f.tgl_selesai + 'T00:00:00');
          const n = Math.round((b - a) / 86400000) + 1;
          return n > 0 ? n : null;
        },
        get tutupLewat() {
          return this.f.status === 'dibuka' && this.f.tgl_tutup && this.f.tgl_tutup < this.hariIni();
        },
        get mulaiSebelumTutup() {
          return this.jenis === 'Magang' && this.f.tgl_mulai && this.f.tgl_tutup && this.f.tgl_mulai < this.f.tgl_tutup;
        },

        // ---------- KUOTA ----------
        hapusErrorKuota() {
          if (this.errors.kuota) { const { kuota, ...sisa } = this.errors; this.errors = sisa; }
        },
        onKuotaInput(e) {
          let bersih = e.target.value.replace(/[^0-9]/g, '');
          const diawaliNol = bersih.startsWith('0');
          bersih = bersih.replace(/^0+/, '');
          e.target.value = bersih;
          this.f.kuota = bersih;
          this.kuotaNol = diawaliNol && bersih === '';

          if (this.kuotaNol) {
            this.errors = { ...this.errors, kuota: 'Kuota tidak boleh 0. Isi angka positif (minimal 1).' };
          } else if (!this.submitted) {
            this.hapusErrorKuota();
          }
        },
        stepKuota(delta) {
          const sekarang = parseInt(this.f.kuota, 10);
          let baru = isNaN(sekarang) ? 1 : sekarang + delta;
          baru = Math.min(99999, Math.max(1, baru));
          this.f.kuota = String(baru);
          this.kuotaNol = false;
          this.hapusErrorKuota();
        },

        // ---------- SILABUS ----------
        checkFile(file) {
          const ext = file.name.split('.').pop().toLowerCase();
          if (!['pdf', 'doc', 'docx'].includes(ext)) return 'Silabus harus berupa file PDF, DOC, atau DOCX.';
          if (file.size > 5 * 1024 * 1024) return 'Ukuran file silabus maksimal 5 MB.';
          return '';
        },
        ukuran(b) { return b < 1048576 ? Math.max(1, Math.round(b / 1024)) + ' KB' : (b / 1048576).toFixed(1) + ' MB'; },
        terima(file) {
          this.fileError = '';
          this.file = file;
          this.fileName = file.name;
          this.fileSize = this.ukuran(file.size);
          this.dirty = true;
          this.revalidate();
        },
        onFile(e) {
          const file = e.target.files[0];
          if (!file) return;
          const err = this.checkFile(file);
          if (err) { e.target.value = ''; this.fileError = err; return; }
          this.terima(file);
        },
        onDrop(e) {
          this.dragging = false;
          const file = e.dataTransfer.files[0];
          if (!file) return;
          const err = this.checkFile(file);
          if (err) { this.fileError = err; return; }
          const dt = new DataTransfer();
          dt.items.add(file);
          this.$refs.fileInput.files = dt.files;
          this.terima(file);
        },
        resetFile() {
          this.$refs.fileInput.value = '';
          this.file = null;
          this.fileName = '';
          this.fileSize = '';
          this.fileError = '';
          this.revalidate();
        },

        // ---------- PERSYARATAN ----------
        baru(p) {
          return {
            _k: ++this.uid,
            id: p && p.id ? p.id : null,
            nama_berkas: p ? (p.nama_berkas || '') : '',
            format_file: p ? (p.format_file || 'pdf') : 'pdf',
            wajib: p ? (p.wajib === true || p.wajib === 1 || p.wajib === '1') : true,
          };
        },
        sudahAda(nama) {
          return this.persyaratan.some((p) => p.nama_berkas.trim().toLowerCase() === nama.toLowerCase());
        },
        tambahPersyaratan() { this.persyaratan.push(this.baru()); },
        tambahPreset(nama) {
          if (this.sudahAda(nama)) return;
          const kosong = this.persyaratan.findIndex((p) => !p.nama_berkas.trim());
          const format = nama === 'Portofolio' || nama.startsWith('Sertifikat') ? 'pdf' : 'pdf';
          if (kosong >= 0) {
            this.persyaratan[kosong].nama_berkas = nama;
            this.persyaratan[kosong].format_file = format;
          } else {
            this.persyaratan.push({ ...this.baru(), nama_berkas: nama, format_file: format });
          }
        },
        hapusPersyaratan(i) {
          if (this.persyaratan.length <= 1) return;
          this.persyaratan.splice(i, 1);
        },
        geser(i, arah) {
          const j = i + arah;
          if (j < 0 || j >= this.persyaratan.length) return;
          const a = this.persyaratan.slice();
          [a[i], a[j]] = [a[j], a[i]];
          this.persyaratan = a;
        },

        // ---------- KELENGKAPAN ----------
        get checks() {
          const m = this.jenis === 'Magang', f = this.f;
          const ok = (v) => String(v ?? '').trim() !== '';
          const list = [
            { key: 'judul', label: m ? 'Nama program' : 'Judul lowongan', ok: ok(f.judul) },
            { key: 'deskripsi', label: 'Deskripsi', ok: ok(f.deskripsi) },
          ];
          if (!m) list.push({ key: 'kualifikasi', label: 'Kualifikasi', ok: ok(f.kualifikasi) });
          if (m) list.push({ key: 'silabus', label: 'Silabus', ok: !!this.file || this.hasExisting });
          list.push({ key: 'persyaratan', label: 'Persyaratan berkas', ok: this.persyaratan.length > 0 && this.persyaratan.every((p) => ok(p.nama_berkas)) });
          list.push({ key: 'kuota', label: 'Kuota', ok: /^[0-9]+$/.test(String(f.kuota)) && parseInt(f.kuota, 10) >= 1 });
          list.push({ key: 'tgl_buka', label: 'Periode pendaftaran', ok: ok(f.tgl_buka) && ok(f.tgl_tutup) });
          if (m) list.push({ key: 'tgl_mulai', label: 'Periode magang', ok: ok(f.tgl_mulai) && ok(f.tgl_selesai) });
          return list;
        },
        get persen() {
          const c = this.checks;
          return Math.round((c.filter((x) => x.ok).length / c.length) * 100);
        },
        goTo(key) {
          const el = key === 'persyaratan'
            ? document.getElementById('sec-persyaratan')
            : document.getElementById('field-' + key);
          if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (el.focus) el.focus({ preventScroll: true });
          }
        },

        // ---------- VALIDASI ----------
        validate() {
          const e = {};
          const f = this.f;
          const kosong = (v) => v === null || v === undefined || String(v).trim() === '';
          const magang = this.jenis === 'Magang';

          if (kosong(f.judul)) e.judul = magang ? 'Nama program magang wajib diisi.' : 'Judul lowongan wajib diisi.';
          else if (f.judul.length > 100) e.judul = 'Maksimal 100 karakter.';

          if (kosong(f.deskripsi)) e.deskripsi = 'Deskripsi wajib diisi.';
          if (!magang && kosong(f.kualifikasi)) e.kualifikasi = 'Kualifikasi wajib diisi.';
          if (magang && !this.file && !this.hasExisting) e.silabus = 'Silabus wajib diunggah.';

          if (kosong(f.kuota)) {
            e.kuota = this.kuotaNol ? 'Kuota tidak boleh 0. Isi angka positif (minimal 1).' : 'Kuota wajib diisi.';
          } else if (!/^[0-9]+$/.test(f.kuota) || parseInt(f.kuota, 10) < 1) {
            e.kuota = 'Kuota harus berupa angka positif (minimal 1).';
          }

          if (kosong(f.status)) e.status = 'Status wajib dipilih.';

          if (kosong(f.tgl_buka)) e.tgl_buka = 'Tanggal buka wajib diisi.';
          if (kosong(f.tgl_tutup)) e.tgl_tutup = 'Tanggal tutup wajib diisi.';
          else if (!kosong(f.tgl_buka) && f.tgl_tutup < f.tgl_buka) e.tgl_tutup = 'Tanggal tutup tidak boleh sebelum tanggal buka.';

          if (magang) {
            if (kosong(f.tgl_mulai)) e.tgl_mulai = 'Tanggal mulai magang wajib diisi.';
            if (kosong(f.tgl_selesai)) e.tgl_selesai = 'Tanggal selesai magang wajib diisi.';
            else if (!kosong(f.tgl_mulai) && f.tgl_selesai < f.tgl_mulai) e.tgl_selesai = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
          }

          if (!this.persyaratan.length) {
            e.persyaratan = 'Minimal harus ada satu persyaratan berkas.';
          } else {
            this.persyaratan.forEach((p, i) => {
              if (kosong(p.nama_berkas)) e['persyaratan_' + i] = 'Nama berkas persyaratan ke-' + (i + 1) + ' wajib diisi.';
            });
          }

          this.errors = e;
          return Object.keys(e).length === 0;
        },

        onSubmit(event) {
          this.submitted = true;
          if (!this.validate()) {
            event.preventDefault();
            this.focusFirstError();
            return;
          }
          this.saving = true;
          this.dirty = false;
        },

        focusFirstError() {
          const rank = (k) => {
            const i = this.order.indexOf(k.startsWith('persyaratan_') ? 'persyaratan' : k);
            return i < 0 ? 99 : i;
          };
          const key = Object.keys(this.errors).sort((a, b) => rank(a) - rank(b))[0];
          if (!key) return;

          this.$nextTick(() => {
            let el;
            if (key.startsWith('persyaratan_')) el = document.querySelector('[name="persyaratan[' + key.split('_')[1] + '][nama_berkas]"]');
            else if (key === 'persyaratan') el = document.getElementById('sec-persyaratan');
            else el = document.getElementById('field-' + key);

            if (el) {
              el.scrollIntoView({ behavior: 'smooth', block: 'center' });
              if (el.focus) el.focus({ preventScroll: true });
            }
          });
        },
      }));
    });
  </script>
</body>
</html>