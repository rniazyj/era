@extends('pelamar.profil.layout')

@section('title', 'Data Diri & Profil - ERA')

{{-- Menandai halaman ini boleh mengubah foto & bio di header (input terhubung ke #formProfil lewat atribut form="") --}}
@section('editable', '1')

@php
  // Jenis kelamin -> nilai L / P untuk <select>
  $jkVal = '';
  if (filled($profil->jenis_kelamin)) {
    $jkVal = match (strtoupper(trim($profil->jenis_kelamin))) {
      'L', 'LAKI-LAKI', 'LAKI LAKI' => 'L',
      'P', 'PEREMPUAN'              => 'P',
      default                       => '',
    };
  }

  // Tanggal lahir -> format Y-m-d untuk <input type="date">
  $tglLahir = '';
  if (filled($profil->tgl_lahir)) {
    try { $tglLahir = \Carbon\Carbon::parse($profil->tgl_lahir)->format('Y-m-d'); }
    catch (\Throwable $e) { $tglLahir = ''; }
  }
@endphp

@section('konten')
<form id="formProfil" method="POST" action="{{ route('pelamar.profil.update') }}" enctype="multipart/form-data" novalidate>
  @csrf
  @method('PUT')

  <div class="stack">

    {{-- Data Pribadi --}}
    <section class="card" id="data-pribadi">
      <div class="card-head">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="11" r="2.5"/><path d="M15 9h3M15 13h3M6 17c.6-1.5 1.8-2.5 3-2.5s2.4 1 3 2.5"/></svg></div>
        <div class="grow">
          <h3>Data Pribadi</h3>
          <p>Informasi dasar yang tampil di CV kamu</p>
        </div>
      </div>

      <div class="fields">
        <div class="field">
          <label for="name">Nama Lengkap</label>
          <input class="input @error('name') is-invalid @enderror" type="text" id="name" name="name"
                 value="{{ old('name', $user->name) }}" placeholder="Nama lengkap sesuai KTP" required>
          @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
          <label for="no_telepon">No. Telepon / WhatsApp</label>
          <input class="input @error('no_telepon') is-invalid @enderror" type="tel" id="no_telepon" name="no_telepon"
                 value="{{ old('no_telepon', $user->no_telepon) }}" placeholder="Contoh: 081234567890">
          @error('no_telepon') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
          <label for="jenis_kelamin">Jenis Kelamin</label>
          <select class="select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin">
            <option value="">Pilih jenis kelamin</option>
            <option value="L" @selected(old('jenis_kelamin', $jkVal) === 'L')>Laki-laki</option>
            <option value="P" @selected(old('jenis_kelamin', $jkVal) === 'P')>Perempuan</option>
          </select>
          @error('jenis_kelamin') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
          <label for="kota">Kota Domisili</label>
          <input class="input @error('kota') is-invalid @enderror" type="text" id="kota" name="kota"
                 value="{{ old('kota', $profil->kota) }}" placeholder="Contoh: Surabaya">
          @error('kota') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
          <label for="tempat_lahir">Tempat Lahir</label>
          <input class="input @error('tempat_lahir') is-invalid @enderror" type="text" id="tempat_lahir" name="tempat_lahir"
                 value="{{ old('tempat_lahir', $profil->tempat_lahir) }}" placeholder="Contoh: Sidoarjo">
          @error('tempat_lahir') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
          <label for="tgl_lahir">Tanggal Lahir</label>
          <input class="input @error('tgl_lahir') is-invalid @enderror" type="date" id="tgl_lahir" name="tgl_lahir"
                 value="{{ old('tgl_lahir', $tglLahir) }}">
          @error('tgl_lahir') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field full">
          <label for="alamat">Alamat Lengkap</label>
          <textarea class="textarea @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                    placeholder="Nama jalan, RT/RW, kelurahan, kecamatan">{{ old('alamat', $profil->alamat) }}</textarea>
          @error('alamat') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field full">
          <label for="nik">NIK</label>
          <input class="input @error('nik') is-invalid @enderror" type="text" id="nik" name="nik"
                 inputmode="numeric" maxlength="16" autocomplete="off"
                 placeholder="{{ filled($profil->nik_terenkripsi) ? '•••• •••• •••• ••••  (kosongkan jika tidak diubah)' : '16 digit NIK' }}">
          @if(filled($profil->nik_terenkripsi))
            <span class="secure">🔒 Tersimpan terenkripsi</span>
          @else
            <span class="hint">Data NIK disimpan terenkripsi dan tidak dicetak di CV.</span>
          @endif
          @error('nik') <span class="error">{{ $message }}</span> @enderror
        </div>
      </div>
    </section>

    {{-- Media Sosial & Tautan --}}
    <section class="card" id="tautan">
      <div class="card-head">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div>
        <div class="grow">
          <h3>Media Sosial &amp; Tautan</h3>
          <p>Tampil di bagian "Data Media Sosial" pada CV</p>
        </div>
      </div>

      <div class="fields">
        <div class="field">
          <label for="linkedin_url">LinkedIn</label>
          <div class="input-group">
            <span class="lead"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6Z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></span>
            <input class="input @error('linkedin_url') is-invalid @enderror" type="url" id="linkedin_url" name="linkedin_url"
                   value="{{ old('linkedin_url', $profil->linkedin_url) }}" placeholder="https://linkedin.com/in/username">
          </div>
          @error('linkedin_url') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
          <label for="instagram_url">Instagram</label>
          <div class="input-group">
            <span class="lead"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6" fill="currentColor"/></svg></span>
            <input class="input @error('instagram_url') is-invalid @enderror" type="url" id="instagram_url" name="instagram_url"
                   value="{{ old('instagram_url', $profil->instagram_url) }}" placeholder="https://instagram.com/username">
          </div>
          @error('instagram_url') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field full">
          <label for="portofolio_url">Portofolio</label>
          <div class="input-group">
            <span class="lead"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/></svg></span>
            <input class="input @error('portofolio_url') is-invalid @enderror" type="url" id="portofolio_url" name="portofolio_url"
                   value="{{ old('portofolio_url', $profil->portofolio_url) }}" placeholder="https://portofolio-kamu.com">
          </div>
          @error('portofolio_url') <span class="error">{{ $message }}</span> @enderror
        </div>
      </div>
    </section>

  </div>

  {{-- Bar simpan: muncul otomatis saat ada perubahan --}}
  <div class="savebar" id="savebar" role="status">
    <span>Ada perubahan yang belum disimpan</span>
    <div class="actions">
      <button type="button" class="btn btn-ghost" id="btnBatal">Batalkan</button>
      <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        Simpan
      </button>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
  (function () {
    const form     = document.getElementById('formProfil');
    const savebar  = document.getElementById('savebar');
    const batal    = document.getElementById('btnBatal');
    const foto     = document.getElementById('fotoInput');
    const img      = document.getElementById('avatarImg');
    const fallback = document.getElementById('avatarFallback');
    const nameIn   = document.getElementById('name');
    const heroName = document.getElementById('heroName');
    const nik      = document.getElementById('nik');

    // Input foto & bio ada di header (di luar <form>) tapi terhubung lewat atribut form="formProfil",
    // jadi dengarkan di document dan cek e.target.form.
    const show = (e) => { if (e.target.form === form) savebar.classList.add('show'); };
    document.addEventListener('input', show);
    document.addEventListener('change', show);

    batal.addEventListener('click', () => window.location.reload());

    // Nama di header ikut berubah saat mengetik
    const namaAwal = @json($user->name);
    nameIn.addEventListener('input', () => { heroName.textContent = nameIn.value.trim() || namaAwal; });

    // Preview foto
    foto.addEventListener('change', () => {
      const file = foto.files && foto.files[0];
      if (!file) return;
      if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran foto maksimal 2 MB.');
        foto.value = '';
        return;
      }
      img.src = URL.createObjectURL(file);
      img.style.display = '';
      if (fallback) fallback.style.display = 'none';
    });

    // NIK hanya angka
    nik.addEventListener('input', () => { nik.value = nik.value.replace(/\D/g, '').slice(0, 16); });
  })();
</script>
@endpush