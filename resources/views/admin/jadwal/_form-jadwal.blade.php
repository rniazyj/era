{{--
  MODAL FORM JADWAL (tunggal, dipakai halaman Jadwal & halaman detail pelamar).
  Aman di-@include berkali-kali: isinya hanya dirender sekali per halaman (@once).

  Cara buka dari tombol apa pun:
    1) Tombol dengan class "btn-ubah" + atribut data-jadwal='{json}'   (ditangani otomatis)
    2) JavaScript: bukaFormJadwal({ mode: 'baru', tgl: '2026-11-25' })

  Isi JSON / argumen:
    mode    : 'baru'  -> pilih pelamar lewat dropdown (khusus halaman Jadwal)
              'tetap' -> pelamar sudah pasti (nama ditampilkan saja)
    id_lamaran, nama, tgl (Y-m-d), jam (H:i), lokasi, catatan
    action  : (opsional) URL tujuan form. Kosong = route admin.jadwal.store

  Variabel opsional: $lamaranOpsi (daftar lamaran untuk dropdown mode 'baru').
--}}
@once
@php
  $lamaranOpsi = $lamaranOpsi ?? collect();
  $adaErr      = $errors->any() && old('_form') === 'jadwal';
@endphp

<style>
  #dlgJadwal::backdrop { background: rgba(24,24,27,.45); backdrop-filter: blur(3px); }
  #dlgJadwal[open] { animation: dlgJadwalIn .25s ease both; }
  @keyframes dlgJadwalIn { from { opacity: 0; transform: translateY(12px) scale(.98); } to { opacity: 1; transform: none; } }
  @media (prefers-reduced-motion: reduce) { #dlgJadwal[open] { animation: none; } }
</style>

<dialog id="dlgJadwal" class="rounded-3xl p-0 w-[min(34rem,calc(100%-2rem))] shadow-2xl text-zinc-900">
  <form id="formJadwal" method="POST" action="{{ route('admin.jadwal.store') }}" class="p-6 sm:p-8 space-y-5" novalidate>
    @csrf
    <input type="hidden" name="_form"   value="jadwal">
    <input type="hidden" name="_mode"   id="f_mode"   value="{{ old('_mode', 'baru') }}">
    <input type="hidden" name="_nama"   id="f_nama_h" value="{{ old('_nama') }}">
    <input type="hidden" name="_action" id="f_action" value="{{ old('_action') }}">

    <div class="flex items-start justify-between gap-4">
      <div>
        <h2 id="dlgJudul" class="text-lg font-semibold tracking-tight">Atur Jadwal Interview</h2>
        <p class="text-xs text-zinc-400 mt-0.5">Satu pelamar memiliki satu jadwal. Menyimpan ulang berarti menjadwal ulang.</p>
      </div>
      <button type="button" class="tutup text-zinc-400 hover:text-zinc-900 text-xl leading-none" aria-label="Tutup">×</button>
    </div>

    @if($adaErr)
      <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 text-xs px-3 py-2">
        <p class="font-semibold">Jadwal belum tersimpan:</p>
        <ul class="list-disc ml-4 mt-1">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
      </div>
    @endif

    {{-- Mode baru: pilih pelamar --}}
    <div id="blokPilih">
      <label class="block text-xs font-semibold mb-1.5" for="f_lamaran">Pelamar</label>
      <select id="f_lamaran" name="id_lamaran" class="w-full rounded-xl border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500">
        <option value="">— Pilih pelamar —</option>
        @foreach($lamaranOpsi as $lm)
          <option value="{{ $lm->id_lamaran }}">
            {{ $lm->pelamar->name ?? 'Pelamar' }} — {{ $lm->nama_posisi }} ({{ $lm->jenis_lamaran }})
          </option>
        @endforeach
      </select>
      @if($lamaranOpsi->isEmpty())
        <p class="text-[11px] text-zinc-400 mt-1">Semua lamaran sudah punya jadwal aktif.</p>
      @endif
    </div>

    {{-- Mode tetap: nama saja --}}
    <div id="blokNama" class="hidden">
      <p class="text-xs font-semibold mb-1.5">Pelamar</p>
      <p id="f_nama" class="rounded-xl bg-zinc-50 px-3 py-2.5 text-sm font-medium"></p>
      <input type="hidden" id="f_id" name="id_lamaran" disabled>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold mb-1.5" for="f_tgl">Tanggal</label>
        <input id="f_tgl" type="date" name="tgl_interview" min="{{ now()->toDateString() }}" class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500">
      </div>
      <div>
        <label class="block text-xs font-semibold mb-1.5" for="f_jam">Jam mulai</label>
        <input id="f_jam" type="time" name="jam_mulai" class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500">
      </div>
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1.5" for="f_lokasi">Lokasi atau tautan meeting</label>
      <input id="f_lokasi" type="text" name="lokasi_atau_link" maxlength="255" placeholder="Contoh: Ruang Meeting Lt. 9 atau https://meet.google.com/…" class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500">
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1.5" for="f_catatan">Catatan untuk pelamar (opsional)</label>
      <textarea id="f_catatan" name="catatan" rows="3" maxlength="1000" placeholder="Contoh: Harap hadir 10 menit lebih awal dan membawa CV cetak." class="w-full rounded-xl border border-black/10 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500"></textarea>
    </div>

    <div class="flex justify-end gap-2 pt-1">
      <button type="button" class="tutup px-5 py-2.5 rounded-full border border-black/10 text-xs font-semibold text-zinc-600 hover:border-zinc-900 transition">Batal</button>
      <button type="submit" class="px-6 py-2.5 rounded-full bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold transition">Simpan Jadwal</button>
    </div>
  </form>
</dialog>

<script>
  (function () {
    const STORE_URL = {!! json_encode(route('admin.jadwal.store')) !!};
    const dlg  = document.getElementById('dlgJadwal');
    const form = document.getElementById('formJadwal');
    const $    = (id) => document.getElementById(id);

    window.bukaFormJadwal = function (d) {
      d = d || {};
      const baru = (d.mode || 'baru') === 'baru';

      $('blokPilih').classList.toggle('hidden', !baru);
      $('blokNama').classList.toggle('hidden', baru);
      $('f_lamaran').disabled = !baru;
      $('f_id').disabled = baru;
      $('dlgJudul').textContent = (baru || !d.tgl) ? 'Atur Jadwal Interview' : 'Ubah Jadwal Interview';

      $('f_lamaran').value = baru ? (d.id_lamaran || '') : '';
      $('f_id').value      = d.id_lamaran || '';
      $('f_nama').textContent = d.nama || '';

      $('f_tgl').value     = d.tgl || '';
      $('f_jam').value     = d.jam || '';
      $('f_lokasi').value  = d.lokasi || '';
      $('f_catatan').value = d.catatan || '';

      // Simpan konteks supaya form bisa dibuka lagi persis sama kalau validasi gagal
      const action = (typeof d.action === 'string' && d.action.charAt(0) === '/') || (d.action || '').indexOf(location.origin) === 0
        ? d.action : '';
      form.action = action || STORE_URL;
      $('f_mode').value   = baru ? 'baru' : 'tetap';
      $('f_nama_h').value = d.nama || '';
      $('f_action').value = action;

      if (!dlg.open) dlg.showModal();
    };

    // Tombol "Ubah" / "Atur Jadwal" di mana pun (termasuk elemen yang dirender belakangan)
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('.btn-ubah');
      if (!btn) return;
      try { window.bukaFormJadwal(JSON.parse(btn.dataset.jadwal)); } catch (err) { console.error(err); }
    });

    dlg.querySelectorAll('.tutup').forEach(b => b.addEventListener('click', () => dlg.close()));
    dlg.addEventListener('click', (e) => { if (e.target === dlg) dlg.close(); });   // klik area gelap = tutup
  })();
</script>

{{-- Validasi gagal -> buka lagi dengan isian sebelumnya --}}
@if($adaErr)
  @php
    $dataLama = [
      'mode'       => old('_mode', 'baru'),
      'id_lamaran' => old('id_lamaran'),
      'nama'       => old('_nama'),
      'action'     => old('_action'),
      'tgl'        => old('tgl_interview'),
      'jam'        => old('jam_mulai'),
      'lokasi'     => old('lokasi_atau_link'),
      'catatan'    => old('catatan'),
    ];
  @endphp
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      window.bukaFormJadwal({!! json_encode($dataLama, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!});
    });
  </script>
@endif
@endonce