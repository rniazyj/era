@php
  $isEdit = ! empty($item);
  $jenis  = $isEdit ? $item['jenis'] : old('jenis', 'Magang');

  $val = fn ($key, $default = '') =>
    old($key, $isEdit ? ($item[$key] ?? $default) : $default);

  $silabusUrl  = $isEdit ? ($item['silabus_url'] ?? null) : null;
  $silabusNama = $isEdit ? ($item['silabus_nama'] ?? '') : '';

  $action = $isEdit
    ? route('admin.lowongan.update', [
        'jenis' => strtolower($item['jenis']),
        'id' => $item['id']
      ])
    : route('admin.lowongan.store');

  $backUrl = route('admin.lowongan.index');

  // Persyaratan lama dari database
  $persyaratanAwal = $isEdit
    ? ($item['persyaratan'] ?? [])
    : [];

  // Kalau validasi gagal, tampilkan input persyaratan sebelumnya
  $persyaratanLama = old('persyaratan');

  if (is_array($persyaratanLama)) {
    $persyaratanAwal = array_values($persyaratanLama);
  }

  // Form baru minimal memiliki satu baris
  if (empty($persyaratanAwal)) {
    $persyaratanAwal = [
      [
        'nama_berkas' => '',
        'format_file' => 'pdf',
        'wajib' => 1,
      ],
    ];
  }

  $init = [
    'jenis' => $jenis,

    'hasExisting' => (bool) $silabusUrl,
    'existingUrl' => $silabusUrl,
    'existingName' => $silabusNama,

    'persyaratan' => array_values($persyaratanAwal),

    'values' => [
      'judul'       => (string) $val('judul'),
      'deskripsi'   => (string) $val('deskripsi'),
      'kualifikasi' => (string) $val('kualifikasi'),
      'silabus'     => (string) $val('silabus'),
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
  <title>{{ $isEdit ? 'Edit' : 'Tambah' }} Lowongan - ERA</title>
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
    .upload-item { transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
    .field { width: 100%; border-radius: 0.75rem; border: 1px solid rgba(0,0,0,.1); background: #fff; padding: .65rem .95rem; font-size: .8rem; outline: none; transition: all .2s; }
    .field:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); }
  </style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

  <div class="flex min-h-screen flex-col lg:flex-row">

    @include('partials.sidebar')

    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-20 sm:pb-24 space-y-6 sm:space-y-8">

      <div class="w-full max-w-4xl mx-auto space-y-6 sm:space-y-8 min-w-0"
           x-data="lowonganForm(@js($init))">

        <!-- HEADER HALAMAN -->
        <div class="scroll-rv min-w-0">
          <a href="{{ $backUrl }}"
             class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-zinc-400 hover:text-ewforange transition mb-3">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Kelola Lowongan
          </a>
          <div>
            <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">
              {{ $isEdit ? 'EDIT ' . strtoupper($jenis) : 'LOWONGAN BARU' }}
            </span>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2 break-words">
              {{ $isEdit ? 'Edit Data' : 'Tambah Lowongan' }}
            </h1>
          </div>
        </div>

        <!-- FORM -->
        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" novalidate
              @submit="onSubmit($event)"
              class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 shadow-sm min-w-0">
          @csrf
          @if($isEdit)
            @method('PUT')
          @endif

          <div class="p-4 sm:p-6 md:p-8 lg:p-10 space-y-8">

            <!-- Peringatan: ada kolom yang belum diisi (validasi di browser) -->
            <div x-show="submitted && errorCount > 0" x-cloak x-transition
                 class="flex items-start gap-3 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xs px-4 py-3">
              <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
              <p>
                Masih ada <b x-text="errorCount"></b> kolom yang belum diisi atau belum sesuai.
                Semua kolom wajib diisi, silakan periksa kolom yang ditandai merah.
              </p>
            </div>

            <!-- Error dari server (cadangan kalau validasi browser terlewat) -->
            @if($errors->any())
              <div class="rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xs px-4 py-3 space-y-0.5">
                @foreach($errors->all() as $err)
                  <p>• {{ $err }}</p>
                @endforeach
              </div>
            @endif

            <!-- SEKSI: INFORMASI UTAMA -->
            <section class="space-y-4">
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase pb-3 border-b border-black/5">Informasi Utama</h3>

              @if($isEdit)
                <div>
                  <label class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Jenis</label>
                  <span class="inline-block px-3 py-1.5 rounded-md font-semibold text-[11px] {{ $jenis == 'Magang' ? 'bg-orange-50 text-ewforange' : 'bg-zinc-100 text-zinc-800' }}">
                    {{ $jenis == 'Magang' ? 'Magang (Program)' : 'Kerja (Lowongan)' }}
                  </span>
                  <p class="text-[11px] text-zinc-400 mt-1.5">Jenis tidak bisa diubah setelah data dibuat.</p>
                </div>
              @else
                <div>
                  <label class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Jenis <span class="text-rose-500">*</span></label>
                  <select name="jenis" x-model="jenis" class="field sm:max-w-xs">
                    <option value="Magang">Magang (Program)</option>
                    <option value="Kerja">Kerja (Lowongan)</option>
                  </select>
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
                      </div>
                    </div>

                    <button type="button" x-ref="btnRemove" x-show="uploadState === 'done'" x-cloak
                            @click="resetFile()" aria-label="Batalkan file"
                            class="ml-3 text-zinc-400 hover:text-red-500 font-bold text-sm px-2">✕</button>
                  </div>
                </div>

                <p x-show="fileError" x-cloak x-text="fileError" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
                <p x-show="errors.silabus" x-cloak x-text="errors.silabus" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
              </div>
            </section>

            <!-- SEKSI: PERSYARATAN BERKAS -->
<section class="space-y-4">

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-black/5">

    <div>
      <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase">
        Persyaratan Berkas
      </h3>

      <p class="text-[11px] text-zinc-400 mt-1">
        Tentukan dokumen yang harus dikumpulkan oleh pelamar.
      </p>
    </div>

    <button
      type="button"
      @click="tambahPersyaratan()"
      class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-orange-50 text-ewforange hover:bg-orange-100 text-xs font-bold transition"
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
          d="M12 5v14m-7-7h14"
        />
      </svg>

      Tambah Berkas
    </button>

  </div>

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

            <select
              :name="'persyaratan[' + index + '][format_file]'"
              x-model="item.format_file"
              class="field"
            >
              <option value="pdf">PDF</option>
              <option value="doc">DOC</option>
              <option value="docx">DOCX</option>
            </select>

          </div>

          <!-- Wajib -->
          <div>

            <label
              class="block text-[11px] font-semibold text-zinc-500 mb-1.5"
            >
              Status Persyaratan
            </label>

            <select
              :name="'persyaratan[' + index + '][wajib]'"
              x-model="item.wajib"
              class="field"
            >
              <option value="1">Wajib</option>
              <option value="0">Opsional</option>
            </select>

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
                    </div>
                  </div>

                  <p x-show="errors.kuota" x-cloak x-text="errors.kuota" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
                </div>

                <div>
                  <label for="field-status" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Status <span class="text-rose-500">*</span></label>
                  <select id="field-status" name="status" x-model="f.status"
                          :class="{ '!border-rose-400': errors.status }"
                          class="field capitalize">
                    @foreach($statusList as $st)
                      <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                    @endforeach
                  </select>
                  <p x-show="errors.status" x-cloak x-text="errors.status" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
                </div>
              </div>
            </section>

            <!-- SEKSI: PERIODE PENDAFTARAN -->
            <section class="space-y-4">
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase pb-3 border-b border-black/5">Periode Pendaftaran</h3>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="field-tgl_buka" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Tanggal Buka <span class="text-rose-500">*</span></label>
                  <input type="date" id="field-tgl_buka" name="tgl_buka" x-model="f.tgl_buka"
                         :class="{ '!border-rose-400': errors.tgl_buka }"
                         class="field">
                  <p x-show="errors.tgl_buka" x-cloak x-text="errors.tgl_buka" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
                </div>
                <div>
                  <label for="field-tgl_tutup" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Tanggal Tutup <span class="text-rose-500">*</span></label>
                  <input type="date" id="field-tgl_tutup" name="tgl_tutup" x-model="f.tgl_tutup" :min="f.tgl_buka"
                         :class="{ '!border-rose-400': errors.tgl_tutup }"
                         class="field">
                  <p x-show="errors.tgl_tutup" x-cloak x-text="errors.tgl_tutup" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
                </div>
              </div>
            </section>

            <!-- SEKSI: PERIODE MAGANG (khusus magang) -->
            <section class="space-y-4" x-show="jenis === 'Magang'">
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase pb-3 border-b border-black/5">Periode Pelaksanaan Magang</h3>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="field-tgl_mulai" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Tanggal Mulai Magang <span class="text-rose-500">*</span></label>
                  <input type="date" id="field-tgl_mulai" name="tgl_mulai" x-model="f.tgl_mulai"
                         :class="{ '!border-rose-400': errors.tgl_mulai }"
                         class="field">
                  <p x-show="errors.tgl_mulai" x-cloak x-text="errors.tgl_mulai" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
                </div>
                <div>
                  <label for="field-tgl_selesai" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Tanggal Selesai Magang <span class="text-rose-500">*</span></label>
                  <input type="date" id="field-tgl_selesai" name="tgl_selesai" x-model="f.tgl_selesai" :min="f.tgl_mulai"
                         :class="{ '!border-rose-400': errors.tgl_selesai }"
                         class="field">
                  <p x-show="errors.tgl_selesai" x-cloak x-text="errors.tgl_selesai" class="text-[11px] font-medium text-rose-500 mt-1.5"></p>
                </div>
              </div>
              <p class="text-[11px] text-zinc-400">Durasi (hari) dihitung otomatis dari tanggal mulai sampai selesai.</p>
            </section>

          </div>

          <!-- TOMBOL AKSI -->
          <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3 px-4 sm:px-6 md:px-8 lg:px-10 py-4 border-t border-black/5">
            <a href="{{ $backUrl }}"
               class="px-5 py-2.5 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-semibold text-center transition">
              Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-full bg-ewforange hover:bg-orange-600 text-white text-xs font-semibold shadow-lg shadow-orange-500/30 transition hover:scale-105">
              {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Lowongan' }}
            </button>
          </div>
        </form>

      </div>
    </main>
  </div>

  <script>
    // Komponen Alpine untuk validasi form dan animasi upload silabus
    document.addEventListener('alpine:init', () => {
      Alpine.data('lowonganForm', (init) => ({
        jenis: init.jenis,
        f: init.values,

        // Persyaratan berkas
        persyaratan: init.persyaratan || [],

        // Silabus
        hasExisting: init.hasExisting, // true kalau file silabus lama sudah ada (mode edit)
        existingUrl: init.existingUrl,
        existingName: init.existingName,
        file: null,
        fileName: '',
        fileError: '',
        uploadState: init.hasExisting ? 'existing' : 'idle', // idle | uploading | done | existing
        progress: 0,
        timer: null,

        // Kuota
        kuotaNol: false,

        errors: {},
        submitted: false,
        order: [
    'judul',
    'deskripsi',
    'kualifikasi',
    'silabus',
    'persyaratan',
    'kuota',
    'status',
    'tgl_buka',
    'tgl_tutup',
    'tgl_mulai',
    'tgl_selesai'
],

        init() {
          // Setelah user pernah klik Simpan, cek ulang otomatis tiap ada perubahan
          this.$watch('f', () => this.revalidate());
          this.$watch('jenis', () => this.revalidate());
        },

        get errorCount() {
          return Object.keys(this.errors).length;
        },

        revalidate() {
          if (this.submitted) this.validate();
        },

        hapusErrorKuota() {
          if (this.errors.kuota) {
            const { kuota, ...sisa } = this.errors;
            this.errors = sisa;
          }
        },

        // ---------- KUOTA ----------
        onKuotaInput(e) {
          let bersih = e.target.value.replace(/[^0-9]/g, '');
          const diawaliNol = bersih.startsWith('0');
          bersih = bersih.replace(/^0+/, ''); // buang angka nol di depan

          e.target.value = bersih;
          this.f.kuota = bersih;
          this.kuotaNol = diawaliNol && bersih === '';

          if (this.kuotaNol) {
            // Langsung beri peringatan, tidak perlu menunggu klik Simpan
            this.errors = { ...this.errors, kuota: 'Kuota tidak boleh 0. Isi angka positif (minimal 1).' };
          } else if (!this.submitted) {
            this.hapusErrorKuota();
          }
        },

        // Panah atas/bawah: tambah atau kurangi kuota (minimal 1, maksimal 99999)
        stepKuota(delta) {
          const sekarang = parseInt(this.f.kuota, 10);
          let baru = isNaN(sekarang) ? 1 : sekarang + delta;
          baru = Math.min(99999, Math.max(1, baru));

          this.f.kuota = String(baru);
          this.kuotaNol = false;
          this.hapusErrorKuota();
        },

        // ---------- UPLOAD SILABUS ----------
        checkFile(file) {
          const ext = file.name.split('.').pop().toLowerCase();
          if (!['pdf', 'doc', 'docx'].includes(ext)) {
            return 'Silabus harus berupa file PDF, DOC, atau DOCX.';
          }
          if (file.size > 5 * 1024 * 1024) {
            return 'Ukuran file silabus maksimal 5 MB.';
          }
          return '';
        },

        onFile(e) {
          const input = e.target;
          const file = input.files[0] || null;
          if (!file) return; // dialog dibatalkan

          const err = this.checkFile(file);
          if (err) {
            input.value = '';
            this.fileError = err;
            return;
          }

          this.fileError = '';
          this.file = file;
          this.fileName = file.name;
          this.revalidate();
          this.animateUpload();
        },

        animateUpload() {
          clearInterval(this.timer);
          this.progress = 0;

          // Tanpa GSAP: langsung ke status selesai
          if (typeof gsap === 'undefined') {
            this.uploadState = 'done';
            return;
          }

          // 1. Tombol UPLOAD mengecil dan hilang
          gsap.to(this.$refs.btnUpload, {
            scale: 0.7,
            opacity: 0,
            duration: 0.5,
            ease: 'power2.inOut',
            onComplete: () => {
              // 2. Progress bar muncul
              this.uploadState = 'uploading';
              this.$nextTick(() => {
                gsap.fromTo(this.$refs.progressWrap,
                  { opacity: 0, scaleX: 0.7 },
                  { opacity: 1, scaleX: 1, duration: 0.7, ease: 'power3.out' });
              });

              // 3. Progress terisi sampai 100%
              this.timer = setInterval(() => {
                this.progress = Math.min(100, this.progress + 2);

                if (this.progress >= 100) {
                  clearInterval(this.timer);

                  setTimeout(() => {
                    // 4. Progress bar memudar, lalu tampil nama file dan tombol batal
                    gsap.to(this.$refs.progressWrap, {
                      opacity: 0,
                      duration: 0.4,
                      onComplete: () => {
                        this.uploadState = 'done';
                        this.$nextTick(() => {
                          gsap.fromTo(this.$refs.btnRemove,
                            { opacity: 0, scale: 0.3 },
                            { opacity: 1, scale: 1, duration: 0.5, ease: 'back.out(1.5)' });
                        });
                      },
                    });
                  }, 500);
                }
              }, 50);
            },
          });
        },

        resetFile() {
          clearInterval(this.timer);
          this.$refs.fileInput.value = '';
          this.file = null;
          this.fileName = '';
          this.fileError = '';
          this.progress = 0;
          this.uploadState = this.hasExisting ? 'existing' : 'idle';
          this.revalidate();

          this.$nextTick(() => {
            if (typeof gsap !== 'undefined') {
              gsap.fromTo(this.$refs.btnUpload,
                { scale: 0.7, opacity: 0 },
                { scale: 1, opacity: 1, duration: 0.6, ease: 'back.out(1.2)' });
            }
          });
        },

// ---------- PERSYARATAN BERKAS ----------

tambahPersyaratan() {
    this.persyaratan.push({
        nama_berkas: '',
        format_file: 'pdf',
        wajib: 1,
    });

    this.revalidate();
},

hapusPersyaratan(index) {
    this.persyaratan.splice(index, 1);

    this.revalidate();
},

        // ---------- VALIDASI ----------
        validate() {
          const e = {};
          const f = this.f;
          const kosong = (v) => v === null || v === undefined || String(v).trim() === '';
          const magang = this.jenis === 'Magang';

          // Judul / nama program (maks 100 karakter)
          if (kosong(f.judul)) {
            e.judul = magang ? 'Nama program magang wajib diisi.' : 'Judul lowongan wajib diisi.';
          } else if (f.judul.length > 100) {
            e.judul = 'Maksimal 100 karakter.';
          }

          if (kosong(f.deskripsi)) e.deskripsi = 'Deskripsi wajib diisi.';

          if (!magang && kosong(f.kualifikasi)) e.kualifikasi = 'Kualifikasi wajib diisi.';

          // Silabus: wajib, kecuali file lama sudah ada dan tidak diganti
          if (magang && !this.file && !this.hasExisting) {
            e.silabus = 'Silabus wajib diunggah.';
          }

          // Kuota: hanya angka positif, tidak boleh 0
          if (kosong(f.kuota)) {
            e.kuota = this.kuotaNol
              ? 'Kuota tidak boleh 0. Isi angka positif (minimal 1).'
              : 'Kuota wajib diisi.';
          } else if (!/^[0-9]+$/.test(f.kuota) || parseInt(f.kuota, 10) < 1) {
            e.kuota = 'Kuota harus berupa angka positif (minimal 1).';
          }

          if (kosong(f.status)) e.status = 'Status wajib dipilih.';

          if (kosong(f.tgl_buka)) e.tgl_buka = 'Tanggal buka wajib diisi.';
          if (kosong(f.tgl_tutup)) {
            e.tgl_tutup = 'Tanggal tutup wajib diisi.';
          } else if (!kosong(f.tgl_buka) && f.tgl_tutup < f.tgl_buka) {
            e.tgl_tutup = 'Tanggal tutup tidak boleh sebelum tanggal buka.';
          }

          if (magang) {
            if (kosong(f.tgl_mulai)) e.tgl_mulai = 'Tanggal mulai magang wajib diisi.';
            if (kosong(f.tgl_selesai)) {
              e.tgl_selesai = 'Tanggal selesai magang wajib diisi.';
            } else if (!kosong(f.tgl_mulai) && f.tgl_selesai < f.tgl_mulai) {
              e.tgl_selesai = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
            }
          }

          // Persyaratan berkas
if (!Array.isArray(this.persyaratan) || this.persyaratan.length === 0) {
    e.persyaratan = 'Minimal harus ada satu persyaratan berkas.';
} else {
    this.persyaratan.forEach((item, index) => {
        if (!item.nama_berkas || !String(item.nama_berkas).trim()) {
            e['persyaratan_' + index] =
                'Nama berkas persyaratan ke-' + (index + 1) + ' wajib diisi.';
        }

        if (!item.format_file) {
            e['persyaratan_' + index] =
                'Format file persyaratan ke-' + (index + 1) + ' wajib dipilih.';
        }
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
          }
        },

        focusFirstError() {
    const key = this.order.find((k) => this.errors[k]);

    if (key === 'persyaratan') {
        const el = document.querySelector(
            '[name="persyaratan[0][nama_berkas]"]'
        );

        if (el) {
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            el.focus({ preventScroll: true });
        }

        return;
    }

    if (!key) {
        const requirementKey = Object.keys(this.errors).find(
            k => k.startsWith('persyaratan_')
        );

        if (requirementKey) {
            const index = requirementKey.replace('persyaratan_', '');

            const el = document.querySelector(
                `[name="persyaratan[${index}][nama_berkas]"]`
            );

            if (el) {
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                el.focus({ preventScroll: true });
            }

            return;
        }
    }

    if (!key) return;

    this.$nextTick(() => {
        const el = document.getElementById('field-' + key);

        if (el) {
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            el.focus({ preventScroll: true });
        }
    });
},
      }));
    });
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script>
    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    window.scrollTo(0, 0);

    gsap.fromTo('.scroll-rv',
      { y: 40, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.9, ease: 'power3.out', stagger: 0.12 }
    );
  </script>
</body>
</html>