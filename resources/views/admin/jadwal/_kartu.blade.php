{{--
  KARTU SATU JADWAL.
  Variabel: $j (JadwalInterview, relasi lamaran.pelamar sudah di-load), $kompak (opsional, true = untuk panel samping kalender)
--}}
@php
  $kompak = $kompak ?? false;
  $l      = $j->lamaran;
  $nama   = $l->pelamar->name ?? 'Pelamar';
  $jenis  = $l->jenis_lamaran ?? '-';
  $payload = [
    'mode'       => 'tetap',
    'id_lamaran' => $j->id_lamaran,
    'nama'       => $nama . ' · ' . ($l->nama_posisi ?? '-'),
    'tgl'        => $j->tgl_interview->toDateString(),
    'jam'        => $j->jam,
    'lokasi'     => $j->lokasi_atau_link,
    'catatan'    => $j->catatan,
  ];
@endphp

<article class="flex flex-col {{ $kompak ? '' : 'md:flex-row md:items-center' }} gap-4 bg-white rounded-2xl border border-black/5 p-4 {{ $kompak ? '' : 'sm:p-5' }} shadow-sm">

  {{-- Jam --}}
  <div class="flex {{ $kompak ? '' : 'md:flex-col md:justify-center md:w-20 md:border-r md:border-black/5 md:pr-4' }} items-baseline gap-2 shrink-0">
    <span class="text-xl font-bold tracking-tight">{{ $j->jam }}</span>
    <span class="text-[10px] text-zinc-400 uppercase tracking-wide">WIB</span>
  </div>

  {{-- Info --}}
  <div class="min-w-0 flex-1">
    <div class="flex flex-wrap items-center gap-2">
      <h3 class="text-sm font-bold text-zinc-900">{{ $nama }}</h3>
      <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $j->status_kelas }}">{{ $j->status_label }}</span>
      <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $jenis === 'Magang' ? 'bg-orange-50 text-orange-600' : 'bg-zinc-100 text-zinc-700' }}">{{ $jenis }}</span>
    </div>
    <p class="text-xs text-zinc-500 mt-0.5">{{ $l->nama_posisi ?? '-' }}</p>
    <p class="text-xs text-zinc-600 mt-2 break-all">
      @if($j->adalah_tautan)
        <a href="{{ $j->lokasi_atau_link }}" target="_blank" rel="noopener" class="text-orange-600 font-semibold hover:underline">Buka tautan meeting ↗</a>
      @else
        📍 {{ $j->lokasi_atau_link }}
      @endif
    </p>
    @if(filled($j->catatan)) <p class="text-[11px] text-zinc-400 mt-1">{{ $j->catatan }}</p> @endif
  </div>

  {{-- Aksi --}}
  <div class="flex flex-wrap items-center gap-2 {{ $kompak ? '' : 'md:justify-end' }}">
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
        <button class="px-3 py-1.5 rounded-full border border-black/10 text-[11px] font-semibold text-zinc-600 hover:border-orange-500 hover:text-orange-600 transition">Aktifkan lagi</button>
      </form>
    @endif

    <form method="POST" action="{{ route('admin.jadwal.destroy', $j->id_jadwal) }}" onsubmit="return confirm('Hapus jadwal ini secara permanen?')">
      @csrf @method('DELETE')
      <button class="px-3 py-1.5 rounded-full bg-rose-50 text-rose-600 text-[11px] font-semibold hover:bg-rose-100 transition" aria-label="Hapus jadwal {{ $nama }}">Hapus</button>
    </form>
  </div>
</article>