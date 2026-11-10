@php
  $has = fn ($name) => \Illuminate\Support\Facades\Route::has($name);

  $backUrl   = $has('admin.pelamar.index') ? route('admin.pelamar.index') : '#';
  $statusUrl = route('admin.pelamar.status', $lamaran->id_lamaran);
  $jadwalUrl = route('admin.pelamar.jadwal', $lamaran->id_lamaran);

  $user     = $lamaran->pelamar;
  $nama     = $user?->name ?? '-';
  $isMagang = ! empty($lamaran->id_program);
  $jenis    = $isMagang ? 'Magang' : 'Kerja';
  $posisi   = $isMagang
      ? ($lamaran->program?->nama_program ?? '-')
      : ($lamaran->lowongan?->judul ?? '-');

  $status = $lamaran->status_lamaran;

  $fmt = fn ($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d F Y') : '-';
  $isi = fn ($v) => filled($v) ? $v : '-';
  $link = fn ($u) => filled($u) ? (preg_match('~^https?://~i', $u) ? $u : 'https://' . $u) : null;

  $statusClass = fn ($st) => match ($st) {
    'diproses' => ['bg-sky-50 text-sky-600', 'bg-sky-500'],
    'diterima' => ['bg-emerald-50 text-emerald-600', 'bg-emerald-500'],
    'ditolak'  => ['bg-rose-50 text-rose-500', 'bg-rose-500'],
    default    => ['bg-amber-50 text-amber-600', 'bg-amber-500'],
  };
  $sc = $statusClass($status);

  $verifClass = fn ($st) => match (strtolower((string) $st)) {
    'valid', 'diterima', 'terverifikasi', 'disetujui' => 'bg-emerald-50 text-emerald-600',
    'ditolak', 'tidak valid'                          => 'bg-rose-50 text-rose-500',
    default                                           => 'bg-amber-50 text-amber-600',
  };

  // Nilai awal form jadwal (kalau validasi gagal pakai input lama)
  $jTgl    = old('tgl_interview', $jadwal?->tgl_interview ? \Carbon\Carbon::parse($jadwal->tgl_interview)->format('Y-m-d') : '');
  $jJam    = old('jam_mulai', $jadwal?->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '');
  $jLokasi = old('lokasi_atau_link', $jadwal?->lokasi_atau_link ?? '');
  $jCatat  = old('catatan', $jadwal?->catatan ?? '');

  $inisial = mb_strtoupper(mb_substr($nama, 0, 1));
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Detail Pelamar - ERA</title>
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
    .field { width: 100%; border-radius: 0.75rem; border: 1px solid rgba(0,0,0,.1); background: #fff; padding: .6rem .9rem; font-size: .8rem; outline: none; transition: all .2s; }
    .field:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); }
  </style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

  <div class="flex min-h-screen flex-col lg:flex-row">

    @include('partials.sidebar')

    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-20 sm:pb-24 space-y-6 sm:space-y-8">

      <div class="w-full max-w-6xl mx-auto space-y-6 sm:space-y-8 min-w-0">

        <!-- HEADER HALAMAN -->
        <div class="scroll-rv min-w-0">
          <a href="{{ $backUrl }}"
             class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-zinc-400 hover:text-ewforange transition mb-3">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Pelamar
          </a>

          <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div class="min-w-0">
              <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">DETAIL PELAMAR</span>
              <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2 break-words">{{ $nama }}</h1>
              <p class="text-xs text-zinc-500 mt-1.5 break-words">
                Melamar sebagai <span class="font-semibold text-zinc-700">{{ $posisi }}</span>
              </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <span class="px-2.5 py-1 rounded-md font-semibold text-[10px] {{ $isMagang ? 'bg-orange-50 text-ewforange' : 'bg-zinc-100 text-zinc-800' }}">{{ $jenis }}</span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-semibold text-[11px] capitalize {{ $sc[0] }}">
                <i class="w-1.5 h-1.5 rounded-full {{ $sc[1] }}"></i>
                {{ $status }}
              </span>
            </div>
          </div>
        </div>

        @if(session('success'))
          <div class="scroll-rv rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-medium px-5 py-3">
            {{ session('success') }}
          </div>
        @endif

        @if($errors->any())
          <div class="rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xs px-5 py-3 space-y-0.5">
            @foreach($errors->all() as $err)
              <p>• {{ $err }}</p>
            @endforeach
          </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-start">

          <!-- ================= KOLOM KIRI ================= -->
          <div class="lg:col-span-2 space-y-6 sm:space-y-8 min-w-0">

            <!-- RINGKASAN LAMARAN -->
            <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm space-y-4">
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase pb-3 border-b border-black/5">Ringkasan Lamaran</h3>

              <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                <div>
                  <dt class="text-[11px] font-semibold text-zinc-400">Posisi / Program</dt>
                  <dd class="mt-1 font-semibold text-zinc-900 break-words">{{ $posisi }}</dd>
                </div>
                <div>
                  <dt class="text-[11px] font-semibold text-zinc-400">Tanggal Pengajuan</dt>
                  <dd class="mt-1 font-semibold text-zinc-900">{{ $fmt($lamaran->tgl_pengajuan) }}</dd>
                </div>

                @if($isMagang)
                  <div>
                    <dt class="text-[11px] font-semibold text-zinc-400">Periode Magang yang Diajukan</dt>
                    <dd class="mt-1 font-semibold text-zinc-900">
                      {{ $fmt($lamaran->tgl_mulai_magang) }} s/d {{ $fmt($lamaran->tgl_selesai_magang) }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-[11px] font-semibold text-zinc-400">Durasi</dt>
                    <dd class="mt-1 font-semibold text-zinc-900">{{ $lamaran->durasi_hari ? $lamaran->durasi_hari . ' Hari' : '-' }}</dd>
                  </div>
                @endif

                @if(in_array($status, ['diterima', 'ditolak'], true))
                  <div>
                    <dt class="text-[11px] font-semibold text-zinc-400">Tanggal Keputusan</dt>
                    <dd class="mt-1 font-semibold text-zinc-900">{{ $fmt($lamaran->tgl_keputusan) }}</dd>
                  </div>
                  <div>
                    <dt class="text-[11px] font-semibold text-zinc-400">Catatan Seleksi</dt>
                    <dd class="mt-1 text-zinc-700 whitespace-pre-line break-words">{{ $isi($lamaran->catatan_seleksi) }}</dd>
                  </div>
                @endif
              </dl>
            </section>

            <!-- DATA PELAMAR -->
            <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm space-y-5">
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase pb-3 border-b border-black/5">Data Pelamar</h3>

              <div class="flex items-center gap-4">
                <div class="relative w-16 h-16 rounded-full bg-orange-500 text-white text-xl font-bold flex items-center justify-center shrink-0 overflow-hidden shadow-md">
                  <span>{{ $inisial }}</span>
                  @if($profil && filled($profil->foto))
                    <img src="{{ asset('storage/' . $profil->foto) }}" alt="Foto {{ $nama }}"
                         class="absolute inset-0 w-full h-full object-cover"
                         onerror="this.remove()">
                  @endif
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-bold text-zinc-900 break-words">{{ $nama }}</p>
                  <p class="text-xs text-zinc-400 break-words">{{ $isi($user?->email) }}</p>
                </div>
              </div>

              <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                <div>
                  <dt class="text-[11px] font-semibold text-zinc-400">No. Telepon</dt>
                  <dd class="mt-1 font-semibold text-zinc-900">{{ $isi($user?->no_telepon) }}</dd>
                </div>
                <div>
                  <dt class="text-[11px] font-semibold text-zinc-400">Jenis Kelamin</dt>
                  <dd class="mt-1 font-semibold text-zinc-900">{{ $isi($profil?->jenis_kelamin) }}</dd>
                </div>
                <div>
                  <dt class="text-[11px] font-semibold text-zinc-400">Tempat, Tanggal Lahir</dt>
                  <dd class="mt-1 font-semibold text-zinc-900">
                    {{ $isi($profil?->tempat_lahir) }}, {{ $fmt($profil?->tgl_lahir) }}
                  </dd>
                </div>
                <div>
                  <dt class="text-[11px] font-semibold text-zinc-400">Kota</dt>
                  <dd class="mt-1 font-semibold text-zinc-900">{{ $isi($profil?->kota) }}</dd>
                </div>
                <div class="sm:col-span-2">
                  <dt class="text-[11px] font-semibold text-zinc-400">Alamat</dt>
                  <dd class="mt-1 font-semibold text-zinc-900 whitespace-pre-line break-words">{{ $isi($profil?->alamat) }}</dd>
                </div>
                <div>
                  <dt class="text-[11px] font-semibold text-zinc-400">LinkedIn</dt>
                  <dd class="mt-1 font-semibold break-all">
                    @if($link($profil?->linkedin_url))
                      <a href="{{ $link($profil->linkedin_url) }}" target="_blank" rel="noopener" class="text-ewforange hover:underline">{{ $profil->linkedin_url }}</a>
                    @else
                      <span class="text-zinc-900">-</span>
                    @endif
                  </dd>
                </div>
                <div>
                  <dt class="text-[11px] font-semibold text-zinc-400">Portofolio</dt>
                  <dd class="mt-1 font-semibold break-all">
                    @if($link($profil?->portofolio_url))
                      <a href="{{ $link($profil->portofolio_url) }}" target="_blank" rel="noopener" class="text-ewforange hover:underline">{{ $profil->portofolio_url }}</a>
                    @else
                      <span class="text-zinc-900">-</span>
                    @endif
                  </dd>
                </div>
              </dl>

              @if(! $profil)
                <p class="text-[11px] text-zinc-400">Pelamar ini belum melengkapi profilnya.</p>
              @endif
            </section>

            <!-- RIWAYAT PENDIDIKAN (CV) -->
            <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm space-y-4">
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase pb-3 border-b border-black/5">Riwayat Pendidikan (CV)</h3>

              @forelse($akademik as $a)
                <div class="rounded-2xl bg-paper border border-black/5 p-4 text-xs">
                  <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-1">
                    <p class="font-bold text-zinc-900 break-words">{{ $a->institusi }}</p>
                    <span class="text-[11px] text-zinc-400 shrink-0">
                      {{ $a->tahun_masuk ?? '?' }} – {{ $a->tahun_lulus ?? 'sekarang' }}
                    </span>
                  </div>
                  <p class="text-zinc-600 mt-0.5">
                    {{ $a->jenjang }}@if(filled($a->jurusan)) · {{ $a->jurusan }}@endif
                    @if(filled($a->ipk)) · IPK {{ number_format((float) $a->ipk, 2) }}@endif
                  </p>
                  @if(filled($a->deskripsi))
                    <p class="text-zinc-500 mt-2 whitespace-pre-line break-words">{{ $a->deskripsi }}</p>
                  @endif
                </div>
              @empty
                <p class="text-xs text-zinc-400">Pelamar belum mengisi riwayat pendidikan di CV.</p>
              @endforelse
            </section>

            <!-- BERKAS PERSYARATAN -->
            <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 md:p-8 shadow-sm space-y-4">
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase pb-3 border-b border-black/5">Berkas Persyaratan</h3>

              @foreach($daftarBerkas as $row)
                @php
                  $s = $row['syarat'];
                  $b = $row['berkas'];
                @endphp
                <div class="rounded-2xl bg-paper border border-black/5 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                  <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-ewforange flex items-center justify-center text-base shrink-0">📄</div>
                    <div class="min-w-0">
                      <p class="text-xs font-bold text-zinc-900 break-words">
                        {{ $s->nama_berkas }}
                        @if($s->wajib)<span class="text-rose-500">*</span>@endif
                      </p>
                      @if($b)
                        <p class="text-[11px] text-green-600 font-semibold truncate" title="{{ $b->nama_file }}">✔ {{ $b->nama_file }}</p>
                      @else
                        <p class="text-[11px] text-zinc-400">Format {{ strtoupper($s->format_file) }}</p>
                      @endif
                    </div>
                  </div>

                  @if($b)
                    <span class="self-start sm:self-auto shrink-0 px-2.5 py-1 rounded-full font-semibold text-[10px] capitalize {{ $verifClass($b->status_verifikasi) }}">
                      {{ $b->status_verifikasi }}
                    </span>
                  @else
                    <span class="self-start sm:self-auto shrink-0 px-2.5 py-1 rounded-full font-semibold text-[10px] {{ $s->wajib ? 'bg-rose-50 text-rose-500' : 'bg-zinc-100 text-zinc-500' }}">
                      {{ $s->wajib ? 'Belum diunggah' : 'Opsional' }}
                    </span>
                  @endif
                </div>
              @endforeach

              @foreach($berkasLain as $b)
                <div class="rounded-2xl bg-paper border border-black/5 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                  <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0">📎</div>
                    <div class="min-w-0">
                      <p class="text-xs font-bold text-zinc-900">Berkas Lainnya</p>
                      <p class="text-[11px] text-green-600 font-semibold truncate" title="{{ $b->nama_file }}">✔ {{ $b->nama_file }}</p>
                    </div>
                  </div>
                  <span class="self-start sm:self-auto shrink-0 px-2.5 py-1 rounded-full font-semibold text-[10px] capitalize {{ $verifClass($b->status_verifikasi) }}">
                    {{ $b->status_verifikasi }}
                  </span>
                </div>
              @endforeach

              @if($daftarBerkas->isEmpty() && $berkasLain->isEmpty())
                <p class="text-xs text-zinc-400">Belum ada berkas yang diunggah, dan belum ada persyaratan berkas untuk {{ $isMagang ? 'program' : 'lowongan' }} ini.</p>
              @endif
            </section>

          </div>

          <!-- ================= KOLOM KANAN ================= -->
          <div class="space-y-6 sm:space-y-8 min-w-0">

            <!-- STATUS LAMARAN -->
            <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 shadow-sm space-y-4">
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase pb-3 border-b border-black/5">Status Penerimaan</h3>

              <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-zinc-400">Status saat ini</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-semibold text-[11px] capitalize {{ $sc[0] }}">
                  <i class="w-1.5 h-1.5 rounded-full {{ $sc[1] }}"></i>
                  {{ $status }}
                </span>
              </div>

              <form method="POST" action="{{ $statusUrl }}" class="space-y-3">
                @csrf
                @method('PATCH')

                @if(in_array($status, ['diajukan', 'diproses'], true))
                  <div>
                    <label for="catatan" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">
                      Catatan seleksi <span class="font-normal text-zinc-400">(opsional, tersimpan saat menerima atau menolak)</span>
                    </label>
                    <textarea id="catatan" name="catatan" rows="3" maxlength="1000"
                              placeholder="Contoh: Profil sesuai kebutuhan tim..."
                              class="field resize-none">{{ old('catatan', $lamaran->catatan_seleksi) }}</textarea>
                  </div>

                  <div class="space-y-2">
                    @if($status === 'diajukan')
                      <button type="submit" name="status" value="diproses"
                              onclick="return confirm('Tandai lamaran ini sebagai Diproses?')"
                              class="w-full px-5 py-2.5 rounded-full bg-sky-50 hover:bg-sky-100 text-sky-600 text-xs font-bold transition">
                        Proses Lamaran
                      </button>
                    @endif

                    <button type="submit" name="status" value="diterima"
                            onclick="return confirm('Terima lamaran {{ addslashes($nama) }}?')"
                            class="w-full px-5 py-2.5 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold shadow-lg shadow-emerald-500/20 transition">
                      Terima
                    </button>

                    <button type="submit" name="status" value="ditolak"
                            onclick="return confirm('Tolak lamaran {{ addslashes($nama) }}?')"
                            class="w-full px-5 py-2.5 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-500 text-xs font-bold transition">
                      Tolak
                    </button>
                  </div>
                @else
                  <p class="text-xs text-zinc-500">
                    Keputusan sudah diambil pada <span class="font-semibold text-zinc-700">{{ $fmt($lamaran->tgl_keputusan) }}</span>.
                    Kalau perlu ditinjau ulang, kembalikan status ke Diproses.
                  </p>

                  <button type="submit" name="status" value="diproses"
                          onclick="return confirm('Batalkan keputusan dan kembalikan ke Diproses?')"
                          class="w-full px-5 py-2.5 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-bold transition">
                    Ubah Keputusan
                  </button>
                @endif
              </form>
            </section>

            <!-- JADWAL INTERVIEW -->
            <section class="scroll-rv bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-4 sm:p-6 shadow-sm space-y-4">
              <h3 class="text-sm font-bold tracking-wide text-zinc-900 uppercase pb-3 border-b border-black/5">Jadwal Interview</h3>

              @if($jadwal)
                <div class="rounded-2xl bg-sky-50 border border-sky-100 p-4 text-xs space-y-1.5">
                  <p class="font-bold text-sky-700">
                    {{ $fmt($jadwal->tgl_interview) }}, pukul {{ substr($jadwal->jam_mulai, 0, 5) }}
                  </p>
                  <p class="text-sky-700 break-all">
                    @if(preg_match('~^https?://~i', $jadwal->lokasi_atau_link))
                      <a href="{{ $jadwal->lokasi_atau_link }}" target="_blank" rel="noopener" class="underline">{{ $jadwal->lokasi_atau_link }}</a>
                    @else
                      {{ $jadwal->lokasi_atau_link }}
                    @endif
                  </p>
                  @if(filled($jadwal->catatan))
                    <p class="text-sky-600 whitespace-pre-line break-words">{{ $jadwal->catatan }}</p>
                  @endif
                  <p class="text-[10px] font-semibold uppercase tracking-wide text-sky-500 pt-1">{{ $jadwal->status_jadwal }}</p>
                </div>
              @else
                <p class="text-xs text-zinc-400">Belum ada jadwal interview untuk pelamar ini.</p>
              @endif

              <form method="POST" action="{{ $jadwalUrl }}" class="space-y-3">
                @csrf

                <p class="text-[11px] font-semibold text-zinc-500">{{ $jadwal ? 'Ubah jadwal' : 'Atur jadwal' }}</p>

                <div>
                  <label for="tgl_interview" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Tanggal <span class="text-rose-500">*</span></label>
                  <input type="date" id="tgl_interview" name="tgl_interview" value="{{ $jTgl }}"
                         min="{{ now()->toDateString() }}" required class="field">
                </div>

                <div>
                  <label for="jam_mulai" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                  <input type="time" id="jam_mulai" name="jam_mulai" value="{{ $jJam }}" required class="field">
                </div>

                <div>
                  <label for="lokasi_atau_link" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Lokasi atau Link <span class="text-rose-500">*</span></label>
                  <input type="text" id="lokasi_atau_link" name="lokasi_atau_link" value="{{ $jLokasi }}"
                         maxlength="255" required placeholder="Contoh: Ruang Meeting 2 / link Google Meet" class="field">
                </div>

                <div>
                  <label for="jadwal_catatan" class="block text-[11px] font-semibold text-zinc-500 mb-1.5">Catatan <span class="font-normal text-zinc-400">(opsional)</span></label>
                  <textarea id="jadwal_catatan" name="catatan" rows="2" maxlength="1000"
                            placeholder="Contoh: Bawa laptop dan portofolio." class="field resize-none">{{ $jCatat }}</textarea>
                </div>

                <button type="submit"
                        class="w-full px-5 py-2.5 rounded-full bg-ewforange hover:bg-orange-600 text-white text-xs font-bold shadow-lg shadow-orange-500/30 transition">
                  {{ $jadwal ? 'Perbarui Jadwal' : 'Simpan Jadwal' }}
                </button>
              </form>
            </section>

          </div>
        </div>

      </div>
    </main>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script>
    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    window.scrollTo(0, 0);

    gsap.fromTo('.scroll-rv',
      { y: 40, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.9, ease: 'power3.out', stagger: 0.1 }
    );
  </script>
</body>
</html>