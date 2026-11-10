{{--
  Form tambah/edit satu entri. Dipakai halaman akademik & pengalaman.
  Variabel: $jenis = akademik | organisasi | profesional | proyek
            $row   = model (baru = form tambah, sudah ada = form edit)
  ID wadah (collapse) = "tambah-{jenis}" atau "edit-{jenis}-{id}".
--}}
@php
  $id = $row->exists ? "edit-{$jenis}-" . $row->getKey() : "tambah-{$jenis}";

  // Nilai field: pakai old() hanya untuk form yang barusan dikirim, selain itu isi dari database
  $v = function (string $f) use ($row, $id) {
    $x = old('_form') === $id ? old($f) : $row->{$f};
    return $x instanceof \DateTimeInterface ? $x->format('Y-m-d') : $x;
  };

  if ($jenis === 'akademik') {
    $aksi = $row->exists
      ? route('pelamar.profil.akademik.update', $row->getKey())
      : route('pelamar.profil.akademik.store');
  } else {
    $aksi = $row->exists
      ? route('pelamar.profil.pengalaman.update', [$jenis, $row->getKey()])
      : route('pelamar.profil.pengalaman.store', $jenis);
  }

  $berjalan = old('_form') === $id ? (bool) old('masih_berjalan') : (bool) ($row->masih_berjalan ?? false);
@endphp

<form method="POST" action="{{ $aksi }}" novalidate>
  @csrf
  @if($row->exists) @method('PUT') @endif
  <input type="hidden" name="_form" value="{{ $id }}">

  <div class="fields">

    @if($jenis === 'akademik')
      <div class="field">
        <label>Jenjang</label>
        <select class="select" name="jenjang" required>
          <option value="">Pilih jenjang</option>
          @foreach(\App\Http\Controllers\Pelamar\AkademikController::JENJANG as $j)
            <option value="{{ $j }}" @selected($v('jenjang') === $j)>{{ $j }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label>Institusi</label>
        <input class="input" type="text" name="institusi" value="{{ $v('institusi') }}" placeholder='Contoh: UPN "Veteran" Jawa Timur' required>
      </div>
      <div class="field">
        <label>Jurusan / Program Studi</label>
        <input class="input" type="text" name="jurusan" value="{{ $v('jurusan') }}" placeholder="Contoh: Sistem Informasi">
      </div>
      <div class="field">
        <label>IPK / Nilai</label>
        <input class="input" type="number" step="0.01" min="0" max="4" name="ipk" value="{{ $v('ipk') }}" placeholder="Contoh: 3.84">
      </div>
      <div class="field">
        <label>Tahun Masuk</label>
        <input class="input" type="number" min="1950" max="2100" name="tahun_masuk" value="{{ $v('tahun_masuk') }}" placeholder="2022" required>
      </div>
      <div class="field">
        <label>Tahun Lulus</label>
        <input class="input" type="number" min="1950" max="2100" name="tahun_lulus" value="{{ $v('tahun_lulus') }}" placeholder="Kosongkan jika belum lulus">
      </div>
      <div class="field full">
        <label>Deskripsi (opsional)</label>
        <textarea class="textarea" name="deskripsi" maxlength="500" placeholder="Prestasi, fokus studi, dll.">{{ $v('deskripsi') }}</textarea>
      </div>

    @elseif($jenis === 'organisasi')
      <div class="field">
        <label>Jabatan</label>
        <input class="input" type="text" name="jabatan" value="{{ $v('jabatan') }}" placeholder="Contoh: Staff Bidang Seni dan Olahraga" required>
      </div>
      <div class="field">
        <label>Organisasi</label>
        <input class="input" type="text" name="nama_organisasi" value="{{ $v('nama_organisasi') }}" placeholder="Contoh: BEM Fakultas Ilmu Komputer" required>
      </div>
      <div class="field full">
        <label>Kategori (opsional)</label>
        <input class="input" type="text" name="kategori" value="{{ $v('kategori') }}" placeholder="Contoh: Kesenian dan Kebudayaan">
      </div>
      <div class="field">
        <label>Tanggal Mulai</label>
        <input class="input" type="date" name="tgl_mulai" value="{{ $v('tgl_mulai') }}" required>
      </div>
      <div class="field">
        <label>Tanggal Selesai</label>
        <input class="input" type="date" name="tgl_selesai" value="{{ $v('tgl_selesai') }}">
      </div>
      <div class="field full">
        <label class="check"><input type="checkbox" name="masih_berjalan" value="1" @checked($berjalan)> Masih menjabat</label>
      </div>
      <div class="field full">
        <label>Deskripsi Kegiatan</label>
        <textarea class="textarea" name="deskripsi" maxlength="1000" placeholder="Tugas, program kerja, dan pencapaian">{{ $v('deskripsi') }}</textarea>
      </div>

    @elseif($jenis === 'profesional')
      <div class="field">
        <label>Posisi</label>
        <input class="input" type="text" name="posisi" value="{{ $v('posisi') }}" placeholder="Contoh: Web Developer Intern" required>
      </div>
      <div class="field">
        <label>Perusahaan</label>
        <input class="input" type="text" name="nama_perusahaan" value="{{ $v('nama_perusahaan') }}" placeholder="Nama perusahaan" required>
      </div>
      <div class="field">
        <label>Jenis Kerja (opsional)</label>
        <select class="select" name="jenis_kerja">
          <option value="">Pilih tipe</option>
          @foreach(['Full-time','Part-time','Magang','Freelance','Kontrak'] as $t)
            <option value="{{ $t }}" @selected($v('jenis_kerja') === $t)>{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label>Lokasi (opsional)</label>
        <input class="input" type="text" name="lokasi" value="{{ $v('lokasi') }}" placeholder="Contoh: Surabaya">
      </div>
      <div class="field">
        <label>Tanggal Mulai</label>
        <input class="input" type="date" name="tgl_mulai" value="{{ $v('tgl_mulai') }}" required>
      </div>
      <div class="field">
        <label>Tanggal Selesai</label>
        <input class="input" type="date" name="tgl_selesai" value="{{ $v('tgl_selesai') }}">
      </div>
      <div class="field full">
        <label class="check"><input type="checkbox" name="masih_berjalan" value="1" @checked($berjalan)> Masih bekerja di sini</label>
      </div>
      <div class="field full">
        <label>Deskripsi Pekerjaan</label>
        <textarea class="textarea" name="deskripsi_tugas" maxlength="1000" placeholder="Tanggung jawab dan pencapaian">{{ $v('deskripsi_tugas') }}</textarea>
      </div>

    @else {{-- proyek --}}
      <div class="field">
        <label>Nama Proyek</label>
        <input class="input" type="text" name="nama_proyek" value="{{ $v('nama_proyek') }}" placeholder="Contoh: Website UKM Penalaran dan Kreativitas" required>
      </div>
      <div class="field">
        <label>Peran / Tipe (opsional)</label>
        <input class="input" type="text" name="peran" value="{{ $v('peran') }}" placeholder="Contoh: Self project">
      </div>
      <div class="field">
        <label>Teknologi (opsional)</label>
        <input class="input" type="text" name="teknologi" value="{{ $v('teknologi') }}" placeholder="Contoh: Laravel, MySQL, Tailwind">
      </div>
      <div class="field full">
        <label>Tautan Proyek (opsional)</label>
        <input class="input" type="url" name="tautan_proyek" value="{{ $v('tautan_proyek') }}" placeholder="https://github.com/...">
      </div>
      <div class="field full">
        <label>Deskripsi</label>
        <textarea class="textarea" name="deskripsi" maxlength="1000" placeholder="Apa yang dikerjakan dan apa yang dipelajari">{{ $v('deskripsi') }}</textarea>
      </div>
    @endif

    <div class="form-actions">
      <button type="button" class="btn btn-outline btn-sm" data-toggle="{{ $id }}">Batal</button>
      <button type="submit" class="btn btn-primary btn-sm">{{ $row->exists ? 'Simpan Perubahan' : 'Tambahkan' }}</button>
    </div>
  </div>
</form>