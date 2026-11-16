{{--
  Tombol "Atur Jadwal" + modal untuk halaman detail pelamar (admin).
  Pasang di tempat tombol "Buat Surat LOA" yang lama:

      @include('admin.pelamar._atur-jadwal', ['lamaran' => $lamaran])

  Variabel: $lamaran (model Lamaran). Memakai <dialog> bawaan browser, jadi tidak butuh Alpine/JS tambahan.
  Form dikirim ke route admin.pelamar.jadwal (AdminJadwalController@simpanUntukLamaran).
--}}
@php
  $jd      = $lamaran->jadwal;                     // jadwal yang sudah ada (kalau ada)
  $idDlg   = 'dlgJadwal' . $lamaran->id_lamaran;
  $formId  = 'jadwal-' . $lamaran->id_lamaran;
  $pakaiOld = old('_form') === $formId;
  $nilai = fn (string $f, $default = null) => $pakaiOld ? old($f) : $default;
  $sudahAda = $jd && $jd->status_jadwal === \App\Models\JadwalInterview::TERJADWAL;
@endphp

<button type="button" onclick="document.getElementById('{{ $idDlg }}').showModal()"
        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold shadow-sm transition">
  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
  {{ $sudahAda ? 'Ubah Jadwal' : 'Atur Jadwal' }}
</button>

@if($sudahAda)
  <p class="mt-2 text-[11px] text-zinc-500">
    Terjadwal: <b>{{ $jd->tgl_interview->locale('id')->translatedFormat('d M Y') }}</b>, pukul <b>{{ $jd->jam }}</b>
  </p>
@endif

<dialog id="{{ $idDlg }}" class="rounded-3xl p-0 w-[min(34rem,calc(100%-2rem))] shadow-2xl text-zinc-900 backdrop:bg-black/40">
  <form method="POST" action="{{ route('admin.pelamar.jadwal', $lamaran->id_lamaran) }}" class="p-6 sm:p-8 space-y-5" novalidate>
    @csrf
    <input type="hidden" name="_form" value="{{ $formId }}">

    <div class="flex items-start justify-between gap-4">
      <div>
        <h2 class="text-lg font-semibold tracking-tight">{{ $sudahAda ? 'Ubah Jadwal Interview' : 'Atur Jadwal Interview' }}</h2>
        <p class="text-xs text-zinc-400 mt-0.5">{{ $lamaran->pelamar->name ?? 'Pelamar' }} · {{ $lamaran->nama_posisi }}</p>
      </div>
      <button type="button" onclick="this.closest('dialog').close()" class="text-zinc-400 hover:text-zinc-900 text-xl leading-none" aria-label="Tutup">×</button>
    </div>

    @if($pakaiOld && $errors->any())
      <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 text-xs px-3 py-2">
        <ul class="list-disc ml-4">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
      </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold mb-1.5" for="{{ $idDlg }}tgl">Tanggal</label>
        <input id="{{ $idDlg }}tgl" type="date" name="tgl_interview" min="{{ now()->toDateString() }}"
               value="{{ $nilai('tgl_interview', $jd?->tgl_interview?->toDateString()) }}"
               class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500">
      </div>
      <div>
        <label class="block text-xs font-semibold mb-1.5" for="{{ $idDlg }}jam">Jam mulai</label>
        <input id="{{ $idDlg }}jam" type="time" name="jam_mulai"
               value="{{ $nilai('jam_mulai', $jd?->jam) }}"
               class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500">
      </div>
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1.5" for="{{ $idDlg }}lok">Lokasi atau tautan meeting</label>
      <input id="{{ $idDlg }}lok" type="text" name="lokasi_atau_link" maxlength="255"
             value="{{ $nilai('lokasi_atau_link', $jd?->lokasi_atau_link) }}"
             placeholder="Contoh: Ruang Meeting Lt. 9 atau https://meet.google.com/…"
             class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500">
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1.5" for="{{ $idDlg }}cat">Catatan untuk pelamar (opsional)</label>
      <textarea id="{{ $idDlg }}cat" name="catatan" rows="3" maxlength="1000"
                placeholder="Contoh: Harap hadir 10 menit lebih awal dan membawa CV cetak."
                class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500">{{ $nilai('catatan', $jd?->catatan) }}</textarea>
    </div>

    <div class="flex justify-end gap-2 pt-1">
      <button type="button" onclick="this.closest('dialog').close()" class="px-5 py-2.5 rounded-full border border-black/10 text-xs font-semibold text-zinc-600 hover:border-zinc-900 transition">Batal</button>
      <button type="submit" class="px-6 py-2.5 rounded-full bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold transition">Simpan Jadwal</button>
    </div>
  </form>
</dialog>

{{-- Gagal validasi -> buka kembali modal ini --}}
@if($pakaiOld && $errors->any())
  <script>document.getElementById('{{ $idDlg }}').showModal();</script>
@endif