{{--
  Tombol "Atur Jadwal" untuk halaman detail pelamar (admin).
  Pasang di tempat tombol "Buat Surat LOA" yang lama:

      @include('admin.jadwal._atur-jadwal', ['lamaran' => $lamaran])

  Variabel: $lamaran (model Lamaran).
  Modal-nya memakai form bersama (_form-jadwal), jadi tidak ada kode form ganda.
  Form dikirim ke route admin.pelamar.jadwal (AdminJadwalController@simpanUntukLamaran).
--}}
@php
  $jd       = $lamaran->jadwal;
  $sudahAda = $jd && $jd->status_jadwal === \App\Models\JadwalInterview::TERJADWAL;

  $payload = [
    'mode'       => 'tetap',
    'id_lamaran' => $lamaran->id_lamaran,
    'nama'       => ($lamaran->pelamar->name ?? 'Pelamar') . ' · ' . $lamaran->nama_posisi,
    'action'     => route('admin.pelamar.jadwal', $lamaran->id_lamaran, false),   // path relatif ("/admin/pelamar/5/jadwal")
    'tgl'        => $sudahAda ? $jd->tgl_interview->toDateString() : '',
    'jam'        => $sudahAda ? $jd->jam : '',
    'lokasi'     => $sudahAda ? $jd->lokasi_atau_link : '',
    'catatan'    => $sudahAda ? $jd->catatan : '',
  ];
@endphp

<button type="button" class="btn-ubah inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold shadow-sm transition"
        data-jadwal="{{ json_encode($payload) }}">
  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
  {{ $sudahAda ? 'Ubah Jadwal' : 'Atur Jadwal' }}
</button>

@if($sudahAda)
  <p class="mt-2 text-[11px] text-zinc-500">
    Terjadwal: <b>{{ $jd->tgl_interview->locale('id')->translatedFormat('d M Y') }}</b>, pukul <b>{{ $jd->jam }}</b>
  </p>
@endif

@include('admin.jadwal._form-jadwal')