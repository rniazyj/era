{{--
  resources/views/admin/konten/form.blade.php
  Formulir tambah / ubah (dipakai semua jenis konten; isian dibuat dari ProfilPerusahaan::TIPE).
  Memakai @extends('layouts.dashboard') dengan @section('title') dan @section('content'); sesuaikan bila layoutmu beda.
--}}
@extends('layouts.dashboard')

@section('title', ($row ? 'Edit ' : 'Tambah ') . $cfg['nama'] . ' - Konten Website')

@section('content')
@include('admin.konten._gaya')

@php
  use Illuminate\Support\Str;

  $daftar = $cfg['mode'] === 'daftar';
  $aksi   = $row ? route('admin.konten.update', [$tipe, $row->getKey()]) : route('admin.konten.store', $tipe);
  $batal  = $daftar ? route('admin.konten.daftar', $tipe) : route('admin.konten.index');

  // Kelompokkan isian: isi teks | foto | pengaturan (centang)
  $isi = $foto = $opsi = [];
  foreach ($cfg['field'] as $k => $f) {
    if ($f['tipe'] === 'foto')        $foto[$k] = $f;
    elseif ($f['tipe'] === 'centang') $opsi[$k] = $f;
    else                              $isi[$k]  = $f;
  }

  $judulHalaman = $tipe === 'hero'
      ? 'Banner atas · ' . $halaman['nama']
      : $cfg['nama'];
@endphp

<div class="kw">
  <nav class="kw-crumb" aria-label="Lokasi">
    <a href="{{ route('admin.konten.index') }}">Konten Website</a> ›
    <span>{{ $halaman['nama'] }}</span> ›
    @if($daftar)
      <a href="{{ $batal }}">{{ $cfg['nama'] }}</a> › <b>{{ $row ? 'Edit' : 'Tambah' }}</b>
    @else
      <b>{{ $cfg['nama'] }}</b>
    @endif
  </nav>

  <div class="kw-head">
    <div>
      <h1>{{ $daftar ? ($row ? 'Edit ' : 'Tambah ') . Str::lower($cfg['nama']) : $judulHalaman }}</h1>
      <p>{{ $cfg['bantuan'] }}</p>
    </div>
  </div>

  @include('admin.konten._pesan')

  <div class="kw-layout">
    @include('admin.konten._menu')

    <div>
      <div class="kw-where">
        <span>Bagian ini tampil di <strong>{{ $halaman['nama'] }}</strong>.</span>
        <a class="kw-btn ghost sm" href="{{ $urlSitus }}" target="_blank" rel="noopener">Lihat di situs ↗</a>
      </div>

      <form class="kw-card" id="kwForm" method="POST" action="{{ $aksi }}" enctype="multipart/form-data" novalidate>
        @csrf
        @if($row) @method('PUT') @endif

        {{-- ===== 1. ISI ===== --}}
        @if($isi)
          <div class="kw-sec">
            <div class="kw-sec-t">Isi</div>
            <div class="kw-grid">
              @foreach($isi as $key => $f)
                @php
                  $nilai = old($key, $row?->{$key});
                  $aturan = $f['aturan'] ?? '';
                  $wajib  = str_contains($aturan, 'required');
                  $maks   = preg_match('/max:(\d+)/', $aturan, $m) && $f['tipe'] !== 'url' ? (int) $m[1] : null;
                  $lebar  = $f['tipe'] === 'area' ? 'full' : '';
                @endphp

                <div class="kw-field {{ $lebar }} {{ $errors->has($key) ? 'bad' : '' }}">
                  <label for="f-{{ $key }}">{{ $f['label'] }} @if($wajib)<b>*</b>@endif</label>

                  @if($f['tipe'] === 'area')
                    <textarea id="f-{{ $key }}" name="{{ $key }}" @if($maks) maxlength="{{ $maks }}" @endif>{{ $nilai }}</textarea>
                  @else
                    <input id="f-{{ $key }}"
                           type="{{ $f['tipe'] === 'angka' ? 'number' : ($f['tipe'] === 'url' ? 'url' : 'text') }}"
                           name="{{ $key }}" value="{{ $nilai }}"
                           @if($maks && $f['tipe'] === 'teks') maxlength="{{ $maks }}" @endif
                           @if($f['tipe'] === 'url') placeholder="https://" @endif
                           @if($f['tipe'] === 'angka') placeholder="mis. 2015" @endif>
                  @endif

                  @if(!empty($f['bantuan']) || ($maks && in_array($f['tipe'], ['teks', 'area'], true)))
                    <div class="kw-hint">
                      <span>{{ $f['bantuan'] ?? '' }}</span>
                      @if($maks && in_array($f['tipe'], ['teks', 'area'], true))
                        <span class="kw-count" data-count-for="f-{{ $key }}" data-max="{{ $maks }}"></span>
                      @endif
                    </div>
                  @endif
                  @error($key)<span class="kw-err">{{ $message }}</span>@enderror
                </div>
              @endforeach
            </div>
          </div>
        @endif

        {{-- ===== 2. FOTO / LOGO ===== --}}
        @foreach($foto as $key => $f)
          @php
            $logo = ($f['gaya'] ?? null) === 'logo';
            $url  = $row?->fotoUrl();
          @endphp
          <div class="kw-sec">
            <div class="kw-sec-t">{{ $f['label'] }}</div>

            <div class="kw-foto {{ $logo ? 'logo' : '' }}">
              <div class="kw-foto-prev">
                <img id="prev-foto" src="{{ $url }}" alt="Pratinjau" @if(!$url) hidden @endif>
                <span id="prev-kosong" @if($url) hidden @endif>{{ $logo ? 'Belum ada logo' : 'Belum ada foto' }}</span>
              </div>

              <div class="kw-foto-ctl">
                <label class="kw-btn ghost sm" for="f-foto">{{ $url ? 'Ganti' : 'Pilih' }} {{ $logo ? 'logo' : 'foto' }}…</label>
                <input id="f-foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp" hidden>
                <span class="nama" id="nama-file">Belum ada file baru dipilih</span>

                @if($row && $row->foto)
                  <input type="hidden" name="hapus_foto" value="0">
                  <label class="kw-check" style="font-weight:500;font-size:.8rem">
                    <input type="checkbox" name="hapus_foto" value="1"> Hapus {{ $logo ? 'logo' : 'foto' }} saat ini
                  </label>
                @endif
                @if(!empty($f['bantuan']))<small style="max-width:20rem;font-size:.75rem;color:#71717a">{{ $f['bantuan'] }}</small>@endif
                @error('foto')<span class="kw-err">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>
        @endforeach

        {{-- ===== 3. PENGATURAN TAMPIL ===== --}}
        @if($opsi || $daftar)
          <div class="kw-sec">
            <div class="kw-sec-t">Pengaturan</div>
            <div class="kw-grid">
              @foreach($opsi as $key => $f)
                <div class="kw-field full">
                  <input type="hidden" name="{{ $key }}" value="0">
                  <label class="kw-switch">
                    <input type="checkbox" name="{{ $key }}" value="1" @checked((bool) old($key, $row?->{$key} ?? false))>
                    <div><strong>{{ $f['label'] }}</strong><span>Hanya satu kantor yang bisa menjadi pusat; yang lain otomatis dicabut.</span></div>
                  </label>
                </div>
              @endforeach

              @if($daftar)
                <div class="kw-field {{ $errors->has('urutan') ? 'bad' : '' }}">
                  <label for="f-urutan">Urutan tampil</label>
                  <input id="f-urutan" type="number" min="0" name="urutan" value="{{ old('urutan', $row?->urutan ?? $urutanBaru ?? 0) }}">
                  <div class="kw-hint"><span>Angka lebih kecil tampil lebih dulu.</span></div>
                  @error('urutan')<span class="kw-err">{{ $message }}</span>@enderror
                </div>

                <div class="kw-field">
                  <input type="hidden" name="aktif" value="0">
                  <label class="kw-switch" style="height:100%">
                    <input type="checkbox" name="aktif" value="1" @checked((bool) old('aktif', $row ? $row->aktif : true))>
                    <div><strong>Tampilkan di situs</strong><span>Matikan untuk menyembunyikan tanpa menghapus.</span></div>
                  </label>
                </div>
              @endif
            </div>
          </div>
        @endif

        {{-- ===== Bar simpan ===== --}}
        <div class="kw-savebar">
          <span class="info" id="kwInfo">Perubahan langsung tampil di situs setelah disimpan.</span>
          <div class="tombol">
            <a class="kw-btn ghost" href="{{ $batal }}">{{ $daftar ? 'Batal' : 'Kembali' }}</a>
            <button type="submit" class="kw-btn dark">Simpan perubahan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function () {
  const form = document.getElementById('kwForm');
  const info = document.getElementById('kwInfo');
  let kotor = false;

  const tandai = () => {
    if (kotor) return;
    kotor = true;
    info.textContent = 'Ada perubahan yang belum disimpan.';
    info.classList.add('dirty');
  };
  form.addEventListener('input', tandai);
  form.addEventListener('change', tandai);
  form.addEventListener('submit', () => { kotor = false; });
  window.addEventListener('beforeunload', (e) => { if (kotor) { e.preventDefault(); e.returnValue = ''; } });

  // Penghitung karakter
  document.querySelectorAll('[data-count-for]').forEach(el => {
    const input = document.getElementById(el.dataset.countFor);
    const max = +el.dataset.max;
    const update = () => {
      el.textContent = input.value.length + ' / ' + max;
      el.style.color = input.value.length >= max * 0.95 ? '#dc2626' : '';
    };
    input.addEventListener('input', update);
    update();
  });

  // Pratinjau foto yang baru dipilih
  const file = document.getElementById('f-foto');
  if (file) {
    file.addEventListener('change', () => {
      const f = file.files[0];
      if (!f) return;
      const img = document.getElementById('prev-foto');
      img.src = URL.createObjectURL(f);
      img.hidden = false;
      document.getElementById('prev-kosong').hidden = true;
      document.getElementById('nama-file').textContent = f.name;
    });
  }
})();
</script>
@endsection