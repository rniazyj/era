@extends('pelamar.profil.layout')

@section('title', 'Curriculum Vitae - Galeri Magang')

@section('konten')
<div class="stack">

  <section class="card" id="status-cv">
    <div class="card-head">
      <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h5"/></svg></div>
      <div class="grow">
        <h3>Status Curriculum Vitae (CV ATS)</h3>
        <p>Progres mengikuti data yang akan tercetak di CV (PDF)</p>
      </div>
    </div>

    <div class="progress-info">
      <span>Progres Pengisian CV</span>
      <span style="color: var(--orange-dark)">{{ $kelengkapan['persen'] }}%</span>
    </div>
    <div class="bar"><i style="width: {{ $kelengkapan['persen'] }}%"></i></div>

    <ul class="checklist">
      @foreach($kelengkapan['item'] as $label => $ada)
        <li class="{{ $ada ? 'ya' : '' }}">{{ $label }}</li>
      @endforeach
    </ul>

    @if(count($kelengkapan['kurang']))
      <p class="cv-note">Lengkapi data di menu samping, lalu CV otomatis ikut diperbarui.</p>
    @else
      <p class="cv-note done">✓ Data CV sudah lengkap dan siap dicetak.</p>
    @endif

    <div class="row-actions">
      <a class="btn btn-primary" href="{{ route('pelamar.profil.cv.pdf') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
        Unduh CV (PDF)
      </a>
      <a class="btn btn-outline" href="{{ route('pelamar.profil.cv.pdf', ['mode' => 'lihat']) }}" target="_blank" rel="noopener">Buka di tab baru</a>
    </div>
  </section>

  <section class="card" id="pratinjau">
    <div class="card-head">
      <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg></div>
      <div class="grow">
        <h3>Pratinjau CV</h3>
        <p>Tampilan sama persis dengan file PDF yang diunduh</p>
      </div>
    </div>

    <iframe class="preview" src="{{ route('pelamar.profil.cv.pdf', ['mode' => 'lihat']) }}" title="Pratinjau CV"></iframe>
  </section>

  @if(session('cv_import'))
    @php $hasilCv = session('cv_import'); @endphp
    <section class="card" id="hasil-impor-cv">
      <div class="card-head">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg></div>
        <div class="grow"><h3>Hasil Pembacaan CV</h3><p>Periksa data yang ditemukan sebelum diterapkan</p></div>
      </div>

      <div class="fields">
        <div class="field"><label>Nama</label><input class="input" value="{{ $hasilCv['name'] ?? '-' }}" readonly></div>
        <div class="field"><label>No. Telepon</label><input class="input" value="{{ $hasilCv['no_telepon'] ?? '-' }}" readonly></div>
        <div class="field full"><label>Email</label><input class="input" value="{{ $hasilCv['email'] ?? '-' }}" readonly><span class="hint">Email tidak diubah otomatis demi keamanan akun.</span></div>
        <div class="field"><label>Jenis Kelamin</label><input class="input" value="{{ $hasilCv['jenis_kelamin'] ?? '-' }}" readonly></div>
        <div class="field"><label>Tempat Lahir</label><input class="input" value="{{ $hasilCv['tempat_lahir'] ?? '-' }}" readonly></div>
        <div class="field"><label>Tanggal Lahir</label><input class="input" value="{{ $hasilCv['tgl_lahir'] ?? '-' }}" readonly></div>
        <div class="field"><label>Kota Domisili</label><input class="input" value="{{ $hasilCv['kota'] ?? '-' }}" readonly></div>
        <div class="field full"><label>Alamat</label><input class="input" value="{{ $hasilCv['alamat'] ?? '-' }}" readonly></div>
        <div class="field"><label>LinkedIn</label><input class="input" value="{{ $hasilCv['linkedin_url'] ?? '-' }}" readonly></div>
        <div class="field"><label>Instagram</label><input class="input" value="{{ $hasilCv['instagram_url'] ?? '-' }}" readonly></div>
        @if(filled($hasilCv['pendidikan'] ?? null))
          <div class="field full"><label>Pendidikan ditemukan</label>
            @foreach($hasilCv['pendidikan'] as $pendidikan)
              <div class="cv-note">{{ $pendidikan['jenjang'] ?? '-' }} · {{ $pendidikan['institusi'] }} {{ filled($pendidikan['tahun']) ? '('.$pendidikan['tahun'].')' : '' }}</div>
            @endforeach
          </div>
        @endif
        @if(filled($hasilCv['skills'] ?? null))
          <div class="field full"><label>Skill ditemukan</label><p class="cv-note">{{ implode(', ', $hasilCv['skills']) }}</p></div>
        @endif
      </div>

      <div class="row-actions">
        <a class="btn btn-outline" href="{{ route('pelamar.profil.cv') }}">Batalkan</a>
        <form method="POST" action="{{ route('pelamar.profil.cv.terapkan') }}">
          @csrf
          <button type="submit" class="btn btn-primary">Gunakan Data Ini</button>
        </form>
      </div>
    </section>
  @else
    <section class="card" id="impor-cv">
      <div class="card-head">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 16V4m0 0L7 9m5-5l5 5"/><path d="M5 20h14"/></svg></div>
        <div class="grow"><h3>Isi Otomatis dari CV</h3><p>Unggah CV PDF, JPG, atau PNG untuk mengisi nama, kontak, tautan, pendidikan, dan skill</p></div>
      </div>

      <form method="POST" action="{{ route('pelamar.profil.cv.impor') }}" enctype="multipart/form-data" class="row-actions">
        @csrf
        <input class="input" type="file" name="cv_pdf" accept="application/pdf,.pdf,image/jpeg,image/png" required>
        <button type="submit" class="btn btn-primary">Baca &amp; Isi Otomatis</button>
      </form>
      @error('cv_pdf') <p class="error" style="margin-top:.75rem">{{ $message }}</p> @enderror
      <p class="cv-note">Gunakan CV dengan resolusi jelas. PDF berteks akan dibaca langsung; PDF hasil scan atau foto akan diproses dengan OCR lokal gratis. Hasil tetap perlu diperiksa sebelum disimpan.</p>
    </section>
  @endif
</div>
@endsection