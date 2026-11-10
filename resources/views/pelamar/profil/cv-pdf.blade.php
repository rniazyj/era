@php
  // Format tanggal: 02 Apr 2025
  $tgl = fn ($d) => $d ? \Carbon\Carbon::parse($d)->locale('id')->translatedFormat('d M Y') : null;

  $rentang = function ($row) use ($tgl) {
    $selesai = $row->masih_berjalan ? 'Sekarang' : $tgl($row->tgl_selesai);
    return trim($tgl($row->tgl_mulai) . ($selesai ? ' - ' . $selesai : ''));
  };

  // Baris kontak di bawah nama
  $kontak = collect([$user->email, $user->no_telepon])->filter(fn ($x) => filled($x))->implode(' | ');

  // Data pribadi (hanya yang terisi)
  $jk = match (strtoupper(trim((string) $profil->jenis_kelamin))) {
    'L', 'LAKI-LAKI', 'LAKI LAKI' => 'Laki-laki',
    'P', 'PEREMPUAN'              => 'Perempuan',
    default                       => null,
  };
  $ttl = collect([$profil->tempat_lahir, $tgl($profil->tgl_lahir)])->filter()->implode(', ');
  $pribadi = collect([
    $ttl ? 'Tempat, Tanggal Lahir: ' . $ttl : null,
    $jk ? 'Jenis Kelamin: ' . $jk : null,
    filled($profil->kota) ? 'Domisili: ' . $profil->kota : null,
  ])->filter()->implode(' | ');

  // Media sosial
  $sosial = collect([
    filled($profil->instagram_url) ? 'Instagram: ' . $profil->instagram_url : null,
    filled($profil->linkedin_url) ? 'LinkedIn: ' . $profil->linkedin_url : null,
    filled($profil->portofolio_url) ? 'Portofolio: ' . $profil->portofolio_url : null,
  ])->filter()->implode(' | ');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>CV {{ $user->name }}</title>
  <style>
    @page { margin: 30px 36px; }
    body { font-family: Helvetica, Arial, sans-serif; font-size: 10.5px; color: #2b2f38; line-height: 1.45; }
    table { border-collapse: collapse; width: 100%; }
    td { vertical-align: top; padding: 0; }

    .nama { font-size: 24px; font-weight: bold; color: #1f2937; text-transform: uppercase; margin: 0 0 8px; }
    .kontak { font-size: 11px; color: #4b5563; }

    .sec { border-top: 1px solid #d9d9d9; margin-top: 14px; padding-top: 12px; }
    .sec-first { margin-top: 14px; }
    h2 { font-size: 12.5px; color: #111827; margin: 0 0 7px; }

    .entri { margin-bottom: 9px; page-break-inside: avoid; }
    .judul { font-weight: bold; color: #111827; font-size: 10.5px; }
    .tanggal { font-size: 9.5px; color: #6b7280; font-style: italic; text-align: right; width: 150px; white-space: nowrap; }
    .kategori { color: #374151; }
    .deskripsi { color: #4b5563; font-size: 9.8px; margin-top: 1px; }
    .muted { color: #6b7280; }
  </style>
</head>
<body>

  {{-- ===== HEADER: foto di kiri, nama & kontak di kanan ===== --}}
  <table>
    <tr>
      @if($fotoData)
        <td style="width: 140px; padding-right: 16px;">
          <img src="{{ $fotoData }}" alt="Foto" style="width: 124px;">
        </td>
      @endif
      <td style="vertical-align: middle; padding-top: {{ $fotoData ? '38px' : '0' }};">
        <div class="nama">{{ $user->name }}</div>
        @if($kontak) <div class="kontak">{{ $kontak }}</div> @endif
      </td>
    </tr>
  </table>

  {{-- ===== RINGKASAN ===== --}}
  @if(filled($cv?->ringkasan_profil))
    <div class="sec">
      <h2>Ringkasan</h2>
      <div class="deskripsi" style="font-size: 10.3px; color:#374151">{{ $cv->ringkasan_profil }}</div>
    </div>
  @endif

  {{-- ===== DATA PRIBADI ===== --}}
  @if($pribadi || filled($profil->alamat))
    <div class="sec">
      <h2>Data Pribadi</h2>
      @if($pribadi) <div>{{ $pribadi }}</div> @endif
      @if(filled($profil->alamat)) <div>Alamat: {{ $profil->alamat }}</div> @endif
    </div>
  @endif

  {{-- ===== PENDIDIKAN ===== --}}
  @if($akademik->isNotEmpty())
    <div class="sec">
      <h2>Pendidikan</h2>
      @foreach($akademik as $a)
        <div class="entri">
          <table>
            <tr>
              <td>
                {{ collect([
                    $a->institusi,
                    trim($a->jenjang . ' ' . $a->jurusan),
                    filled($a->ipk) ? 'IPK: ' . number_format((float) $a->ipk, 2) : null,
                ])->filter()->implode(' | ') }}
              </td>
              <td class="tanggal">
                {{ filled($a->tahun_lulus) ? $a->tahun_masuk . ' - ' . $a->tahun_lulus : 'Belum Lulus' }}
              </td>
            </tr>
          </table>
          @if(filled($a->deskripsi)) <div class="deskripsi">{{ $a->deskripsi }}</div> @endif
        </div>
      @endforeach
    </div>
  @endif

  {{-- ===== PENGALAMAN PROFESIONAL ===== --}}
  @if($profesional->isNotEmpty())
    <div class="sec">
      <h2>Pengalaman Profesional</h2>
      @foreach($profesional as $p)
        <div class="entri">
          <table>
            <tr>
              <td class="judul">{{ $p->posisi }} | {{ $p->nama_perusahaan }}</td>
              <td class="tanggal">{{ $rentang($p) }}</td>
            </tr>
          </table>
          @php $sub = collect([$p->jenis_kerja, $p->lokasi])->filter()->implode(' | '); @endphp
          @if($sub) <div class="kategori">{{ $sub }}</div> @endif
          @if(filled($p->deskripsi_tugas)) <div class="deskripsi">{{ $p->deskripsi_tugas }}</div> @endif
        </div>
      @endforeach
    </div>
  @endif

  {{-- ===== PENGALAMAN ORGANISASI ===== --}}
  @if($organisasi->isNotEmpty())
    <div class="sec">
      <h2>Pengalaman Organisasi</h2>
      @foreach($organisasi as $o)
        <div class="entri">
          <table>
            <tr>
              <td class="judul">{{ $o->jabatan }} | {{ $o->nama_organisasi }}</td>
              <td class="tanggal">{{ $rentang($o) }}</td>
            </tr>
          </table>
          @if(filled($o->kategori)) <div class="kategori">{{ $o->kategori }}</div> @endif
          @if(filled($o->deskripsi)) <div class="deskripsi">{{ $o->deskripsi }}</div> @endif
        </div>
      @endforeach
    </div>
  @endif

  {{-- ===== PENGALAMAN PROYEK ===== --}}
  @if($proyek->isNotEmpty())
    <div class="sec">
      <h2>Pengalaman Proyek</h2>
      @foreach($proyek as $pr)
        <div class="entri">
          <div class="judul">{{ $pr->nama_proyek }}@if(filled($pr->peran)) | {{ $pr->peran }}@endif</div>
          @if(filled($pr->deskripsi)) <div class="deskripsi">{{ $pr->deskripsi }}</div> @endif
          @if(filled($pr->teknologi)) <div class="deskripsi">Teknologi: {{ $pr->teknologi }}</div> @endif
          @if(filled($pr->tautan_proyek)) <div class="deskripsi">{{ $pr->tautan_proyek }}</div> @endif
        </div>
      @endforeach
    </div>
  @endif

  {{-- ===== HARD SKILL ===== --}}
  @if($hard->isNotEmpty())
    <div class="sec">
      <h2>Data Hard Skill</h2>
      <div>{{ $hard->map(fn ($s) => $s->nama_skill . (filled($s->level_skill) ? ' - ' . $s->level_skill : ''))->implode(' | ') }}</div>
    </div>
  @endif

  {{-- ===== SOFT SKILL ===== --}}
  @if($soft->isNotEmpty())
    <div class="sec">
      <h2>Data Soft Skill</h2>
      <div>{{ $soft->pluck('nama_skill')->implode(' | ') }}</div>
    </div>
  @endif

  {{-- ===== TOOLS ===== --}}
  @if($tools->isNotEmpty())
    <div class="sec">
      <h2>Tools</h2>
      <div>{{ $tools->pluck('nama_skill')->implode(', ') }}</div>
    </div>
  @endif

  {{-- ===== MEDIA SOSIAL ===== --}}
  @if($sosial)
    <div class="sec">
      <h2>Data Media Sosial</h2>
      <div>{{ $sosial }}</div>
    </div>
  @endif

</body>
</html>