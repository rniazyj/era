<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jadwal Interview - Galeri Magang</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
      colors: { paper:'#faf9fc', lime:'#e9f56b', brand:'#34d3b0', ewforange:'#f97316' } } } };
  </script>
  <style>
    dialog::backdrop { background: rgba(24,24,27,.45); backdrop-filter: blur(3px); }
    dialog[open] { animation: dlgIn .25s ease both; }
    @keyframes dlgIn { from { opacity: 0; transform: translateY(12px) scale(.98); } to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) { dialog[open] { animation: none; } }
  </style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

@php
  $tab = [
    'mendatang'  => 'Mendatang',
    'hariini'    => 'Hari ini',
    'terlewat'   => 'Perlu tindak lanjut',
    'selesai'    => 'Selesai',
    'dibatalkan' => 'Dibatalkan',
    'semua'      => 'Semua',
  ];
@endphp

<div class="flex min-h-screen flex-col lg:flex-row">

  @include('partials.sidebar')

  <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-6 sm:pt-8 lg:pt-10 pb-20 sm:pb-24 space-y-6 sm:space-y-8">

    {{-- Header --}}
    <div class="w-full max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div>
        <span class="text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">Panel Administrator</span>
        <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold tracking-tight mt-2">Jadwal Interview</h1>
        <p class="text-xs text-zinc-400 mt-1">Atur, ubah, dan pantau jadwal interview semua pelamar.</p>
      </div>
      <button type="button" id="btnBaru" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-ewforange hover:bg-orange-600 text-white text-xs font-semibold shadow-sm transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        Atur Jadwal Baru
      </button>
    </div>

    {{-- Notifikasi --}}
    <div class="w-full max-w-6xl mx-auto space-y-3 empty:hidden">
      @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-xs font-semibold px-4 py-3">✓ {{ session('success') }}</div>
      @endif
      @if(session('warning'))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 text-amber-800 text-xs font-semibold px-4 py-3">⚠ {{ session('warning') }}</div>
      @endif
      @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-800 text-xs px-4 py-3">
          <p class="font-semibold">Jadwal belum tersimpan:</p>
          <ul class="list-disc ml-5 mt-1">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif
    </div>

    {{-- Statistik --}}
    <div class="w-full max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-4">
      <a href="{{ route('admin.jadwal.index', ['tampil' => 'hariini']) }}" class="rounded-2xl bg-white border border-black/5 p-5 shadow-sm hover:border-ewforange/40 transition">
        <p class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wide">Hari ini</p>
        <p class="mt-1 text-3xl font-bold tracking-tight">{{ $stat['hariini'] }}</p>
      </a>
      <a href="{{ route('admin.jadwal.index', ['tampil' => 'mendatang']) }}" class="rounded-2xl bg-white border border-black/5 p-5 shadow-sm hover:border-ewforange/40 transition">
        <p class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wide">7 hari ke depan</p>
        <p class="mt-1 text-3xl font-bold tracking-tight">{{ $stat['pekan'] }}</p>
      </a>
      <a href="{{ route('admin.jadwal.index', ['tampil' => 'terlewat']) }}" class="rounded-2xl bg-white border border-black/5 p-5 shadow-sm hover:border-amber-300 transition">
        <p class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wide">Perlu tindak lanjut</p>
        <p class="mt-1 text-3xl font-bold tracking-tight {{ $stat['terlewat'] > 0 ? 'text-amber-600' : '' }}">{{ $stat['terlewat'] }}</p>
        <p class="text-[11px] text-zinc-400">Tanggal sudah lewat, belum ditandai selesai</p>
      </a>
    </div>

    {{-- Filter & pencarian --}}
    <div class="w-full max-w-6xl mx-auto flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
      <div class="flex flex-wrap gap-2">
        @foreach($tab as $kode => $label)
          <a href="{{ route('admin.jadwal.index', array_filter(['tampil' => $kode, 'q' => $cari])) }}"
             class="px-4 py-1.5 rounded-full text-xs font-semibold border transition {{ $tampil === $kode ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-600 border-black/10 hover:border-ewforange hover:text-ewforange' }}">{{ $label }}</a>
        @endforeach
      </div>
      <form method="GET" action="{{ route('admin.jadwal.index') }}" class="flex gap-2">
        <input type="hidden" name="tampil" value="{{ $tampil }}">
        <input type="search" name="q" value="{{ $cari }}" placeholder="Cari nama pelamar…" class="w-full lg:w-64 rounded-full border border-black/10 bg-white px-4 py-2 text-xs focus:outline-none focus:border-ewforange">
        <button class="px-4 py-2 rounded-full bg-zinc-900 text-white text-xs font-semibold">Cari</button>
      </form>
    </div>

    {{-- Agenda per tanggal --}}
    <div class="w-full max-w-6xl mx-auto space-y-8">
      @forelse($perTanggal as $tgl => $items)
        @php $c = \Carbon\Carbon::parse($tgl)->locale('id'); @endphp
        <section>
          <div class="flex items-center gap-3 mb-3">
            <h2 class="text-sm font-bold text-zinc-900">{{ $c->translatedFormat('l, d F Y') }}</h2>
            @if($c->isToday()) <span class="text-[10px] font-bold bg-orange-50 text-ewforange px-2.5 py-1 rounded-full">Hari ini</span>
            @elseif($c->isTomorrow()) <span class="text-[10px] font-bold bg-zinc-100 text-zinc-600 px-2.5 py-1 rounded-full">Besok</span> @endif
            <span class="flex-1 h-px bg-black/5"></span>
            <span class="text-[11px] text-zinc-400">{{ $items->count() }} jadwal</span>
          </div>

          <div class="space-y-3">
            @foreach($items as $j)
              @php
                $l    = $j->lamaran;
                $nama = $l->pelamar->name ?? 'Pelamar';
                $badge = $j->terlewat
                  ? ['Terlewat', 'bg-amber-50 text-amber-700']
                  : match ($j->status_jadwal) {
                      \App\Models\JadwalInterview::SELESAI    => ['Selesai', 'bg-emerald-50 text-emerald-700'],
                      \App\Models\JadwalInterview::DIBATALKAN => ['Dibatalkan', 'bg-zinc-100 text-zinc-500'],
                      default                                 => ['Terjadwal', 'bg-orange-50 text-ewforange'],
                    };
                $payload = [
                  'id_lamaran' => $j->id_lamaran,
                  'nama'       => $nama . ' · ' . ($l->nama_posisi ?? '-'),
                  'tgl'        => $j->tgl_interview->toDateString(),
                  'jam'        => $j->jam,
                  'lokasi'     => $j->lokasi_atau_link,
                  'catatan'    => $j->catatan,
                ];
              @endphp

              <article class="flex flex-col md:flex-row md:items-center gap-4 bg-white rounded-2xl border border-black/5 p-4 sm:p-5 shadow-sm">
                <div class="flex md:flex-col items-center md:justify-center gap-2 md:w-20 shrink-0 md:border-r md:border-black/5 md:pr-4">
                  <span class="text-xl font-bold tracking-tight">{{ $j->jam }}</span>
                  <span class="text-[10px] text-zinc-400 uppercase tracking-wide">WIB</span>
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-sm font-bold text-zinc-900">{{ $nama }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $badge[1] }}">{{ $badge[0] }}</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ ($l->jenis_lamaran ?? 'Magang') === 'Magang' ? 'bg-orange-50 text-ewforange' : 'bg-zinc-100 text-zinc-700' }}">{{ $l->jenis_lamaran ?? '-' }}</span>
                  </div>
                  <p class="text-xs text-zinc-500 mt-0.5">{{ $l->nama_posisi ?? '-' }}</p>
                  <p class="text-xs text-zinc-600 mt-2 break-all">
                    @if($j->adalah_tautan)
                      <a href="{{ $j->lokasi_atau_link }}" target="_blank" rel="noopener" class="text-ewforange font-semibold hover:underline">Buka tautan meeting ↗</a>
                    @else
                      📍 {{ $j->lokasi_atau_link }}
                    @endif
                  </p>
                  @if(filled($j->catatan)) <p class="text-[11px] text-zinc-400 mt-1">{{ $j->catatan }}</p> @endif
                </div>

                <div class="flex flex-wrap items-center gap-2 md:justify-end">
                  <a href="{{ route('admin.pelamar.show', $j->id_lamaran) }}" class="px-3 py-1.5 rounded-full border border-black/10 text-[11px] font-semibold text-zinc-600 hover:border-zinc-900 transition">Detail</a>

                  <button type="button" class="btn-ubah px-3 py-1.5 rounded-full bg-zinc-900 text-white text-[11px] font-semibold hover:bg-black transition"
                          data-jadwal="{{ json_encode($payload) }}">Ubah</button>

                  @if($j->status_jadwal === \App\Models\JadwalInterview::TERJADWAL)
                    <form method="POST" action="{{ route('admin.jadwal.status', $j->id_jadwal) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="status" value="{{ \App\Models\JadwalInterview::SELESAI }}">
                      <button class="px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold hover:bg-emerald-100 transition">Selesai</button>
                    </form>
                    <form method="POST" action="{{ route('admin.jadwal.status', $j->id_jadwal) }}" onsubmit="return confirm('Batalkan jadwal interview {{ addslashes($nama) }}?')">
                      @csrf @method('PATCH')
                      <input type="hidden" name="status" value="{{ \App\Models\JadwalInterview::DIBATALKAN }}">
                      <button class="px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-semibold hover:bg-amber-100 transition">Batalkan</button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('admin.jadwal.status', $j->id_jadwal) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="status" value="{{ \App\Models\JadwalInterview::TERJADWAL }}">
                      <button class="px-3 py-1.5 rounded-full border border-black/10 text-[11px] font-semibold text-zinc-600 hover:border-ewforange hover:text-ewforange transition">Aktifkan lagi</button>
                    </form>
                  @endif

                  <form method="POST" action="{{ route('admin.jadwal.destroy', $j->id_jadwal) }}" onsubmit="return confirm('Hapus jadwal ini secara permanen?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 rounded-full bg-rose-50 text-rose-600 text-[11px] font-semibold hover:bg-rose-100 transition" aria-label="Hapus jadwal {{ $nama }}">Hapus</button>
                  </form>
                </div>
              </article>
            @endforeach
          </div>
        </section>
      @empty
        <div class="rounded-3xl border border-dashed border-black/10 bg-white px-6 py-14 text-center">
          <p class="text-sm font-semibold text-zinc-700">Belum ada jadwal pada tampilan ini</p>
          <p class="text-xs text-zinc-400 mt-1">Klik "Atur Jadwal Baru" untuk menjadwalkan interview pelamar.</p>
        </div>
      @endforelse
    </div>

  </main>
</div>

{{-- ============ MODAL: ATUR / UBAH JADWAL ============ --}}
<dialog id="dlgJadwal" class="rounded-3xl p-0 w-[min(34rem,calc(100%-2rem))] shadow-2xl text-zinc-900">
  <form method="POST" action="{{ route('admin.jadwal.store') }}" class="p-6 sm:p-8 space-y-5" novalidate>
    @csrf
    <input type="hidden" name="_form" value="jadwal">

    <div class="flex items-start justify-between gap-4">
      <div>
        <h2 id="dlgJudul" class="text-lg font-semibold tracking-tight">Atur Jadwal Interview</h2>
        <p class="text-xs text-zinc-400 mt-0.5">Satu pelamar memiliki satu jadwal. Menyimpan ulang berarti menjadwal ulang.</p>
      </div>
      <button type="button" class="tutup text-zinc-400 hover:text-zinc-900 text-xl leading-none" aria-label="Tutup">×</button>
    </div>

    {{-- Mode baru: pilih pelamar. Mode ubah: nama saja --}}
    <div id="blokPilih">
      <label class="block text-xs font-semibold mb-1.5" for="f_lamaran">Pelamar</label>
      <select id="f_lamaran" name="id_lamaran" class="w-full rounded-xl border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:border-ewforange">
        <option value="">— Pilih pelamar —</option>
        @foreach($lamaranOpsi as $lm)
          <option value="{{ $lm->id_lamaran }}" @selected(old('id_lamaran') == $lm->id_lamaran)>
            {{ $lm->pelamar->name ?? 'Pelamar' }} — {{ $lm->nama_posisi }} ({{ $lm->jenis_lamaran }})
          </option>
        @endforeach
      </select>
      @if($lamaranOpsi->isEmpty()) <p class="text-[11px] text-zinc-400 mt-1">Semua lamaran sudah punya jadwal aktif.</p> @endif
    </div>
    <div id="blokNama" class="hidden">
      <p class="text-xs font-semibold mb-1.5">Pelamar</p>
      <p id="f_nama" class="rounded-xl bg-zinc-50 px-3 py-2.5 text-sm font-medium"></p>
      <input type="hidden" id="f_id" name="id_lamaran" disabled>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold mb-1.5" for="f_tgl">Tanggal</label>
        <input id="f_tgl" type="date" name="tgl_interview" min="{{ now()->toDateString() }}" value="{{ old('tgl_interview') }}" class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-ewforange">
      </div>
      <div>
        <label class="block text-xs font-semibold mb-1.5" for="f_jam">Jam mulai</label>
        <input id="f_jam" type="time" name="jam_mulai" value="{{ old('jam_mulai') }}" class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-ewforange">
      </div>
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1.5" for="f_lokasi">Lokasi atau tautan meeting</label>
      <input id="f_lokasi" type="text" name="lokasi_atau_link" maxlength="255" value="{{ old('lokasi_atau_link') }}" placeholder="Contoh: Ruang Meeting Lt. 9 atau https://meet.google.com/…" class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-ewforange">
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1.5" for="f_catatan">Catatan untuk pelamar (opsional)</label>
      <textarea id="f_catatan" name="catatan" rows="3" maxlength="1000" placeholder="Contoh: Harap hadir 10 menit lebih awal dan membawa CV cetak." class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-ewforange">{{ old('catatan') }}</textarea>
    </div>

    <div class="flex justify-end gap-2 pt-1">
      <button type="button" class="tutup px-5 py-2.5 rounded-full border border-black/10 text-xs font-semibold text-zinc-600 hover:border-zinc-900 transition">Batal</button>
      <button type="submit" class="px-6 py-2.5 rounded-full bg-ewforange hover:bg-orange-600 text-white text-xs font-semibold transition">Simpan Jadwal</button>
    </div>
  </form>
</dialog>

<script>
  (function () {
    const dlg = document.getElementById('dlgJadwal');
    const $   = (id) => document.getElementById(id);
    const pilih = $('f_lamaran'), blokPilih = $('blokPilih'), blokNama = $('blokNama'), hid = $('f_id');

    function mode(baru) {
      blokPilih.classList.toggle('hidden', !baru);
      blokNama.classList.toggle('hidden', baru);
      pilih.disabled = !baru;
      hid.disabled = baru;
      $('dlgJudul').textContent = baru ? 'Atur Jadwal Interview' : 'Ubah Jadwal Interview';
    }

    $('btnBaru').addEventListener('click', () => {
      mode(true);
      pilih.value = ''; $('f_tgl').value = ''; $('f_jam').value = ''; $('f_lokasi').value = ''; $('f_catatan').value = '';
      dlg.showModal();
    });

    document.querySelectorAll('.btn-ubah').forEach(btn => btn.addEventListener('click', () => {
      const d = JSON.parse(btn.dataset.jadwal);
      mode(false);
      hid.value = d.id_lamaran; $('f_nama').textContent = d.nama;
      $('f_tgl').value = d.tgl; $('f_jam').value = d.jam; $('f_lokasi').value = d.lokasi || ''; $('f_catatan').value = d.catatan || '';
      dlg.showModal();
    }));

    dlg.querySelectorAll('.tutup').forEach(b => b.addEventListener('click', () => dlg.close()));
    dlg.addEventListener('click', (e) => { if (e.target === dlg) dlg.close(); });   // klik area gelap = tutup

    // Gagal validasi -> buka kembali form dengan isian sebelumnya
    @if($errors->any() && old('_form') === 'jadwal')
      @php
        $oldId   = (int) old('id_lamaran');
        $lmLama  = $oldId ? \App\Models\Lamaran::with(['pelamar', 'lowongan', 'program'])->find($oldId) : null;
        $modeBaru = ! $lmLama || $lamaranOpsi->contains('id_lamaran', $oldId);
      @endphp
      @if($modeBaru)
        mode(true);
      @else
        mode(false);
        hid.value = {{ $oldId }};
        $('f_nama').textContent = @json(($lmLama->pelamar->name ?? 'Pelamar') . ' · ' . $lmLama->nama_posisi);
      @endif
      dlg.showModal();
    @endif
  })();
</script>

</body>
</html>