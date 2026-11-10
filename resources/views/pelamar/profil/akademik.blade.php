@extends('pelamar.profil.layout')

@section('title', 'Data Akademik - ERA')

@section('konten')
<section class="card" id="akademik">
  <div class="card-head">
    <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c3 2 9 2 12 0v-5"/></svg></div>
    <div class="grow">
      <h3>Data Akademik</h3>
      <p>Riwayat pendidikan yang tampil di bagian "Pendidikan" pada CV</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm" data-toggle="tambah-akademik">+ Tambah</button>
  </div>

  {{-- Form tambah (terbuka otomatis kalau belum ada data atau barusan gagal validasi) --}}
  <div class="collapse form-box {{ ($daftar->isEmpty() || old('_form') === 'tambah-akademik') ? 'open' : '' }}" id="tambah-akademik" style="margin-top:0;margin-bottom:1rem">
    @include('pelamar.profil.partials.form-entri', ['jenis' => 'akademik', 'row' => new \App\Models\DataAkademik()])
  </div>

  @foreach($daftar as $row)
    @php
      $editId = 'edit-akademik-' . $row->id_akademik;
      $periode = $row->tahun_masuk . ' - ' . ($row->tahun_lulus ?: 'Belum lulus');
    @endphp
    <div class="item">
      <div class="item-top">
        <div style="min-width:0">
          <h4>{{ $row->institusi }}</h4>
          <div class="sub">{{ $row->jenjang }}{{ filled($row->jurusan) ? ' · ' . $row->jurusan : '' }}{{ filled($row->ipk) ? ' · IPK ' . number_format((float) $row->ipk, 2) : '' }}</div>
          <div class="meta">{{ $periode }}</div>
          @if(filled($row->deskripsi)) <div class="desc">{{ $row->deskripsi }}</div> @endif
        </div>
        <div class="item-actions">
          <button type="button" class="btn btn-outline btn-sm" data-toggle="{{ $editId }}">Edit</button>
          <form method="POST" action="{{ route('pelamar.profil.akademik.destroy', $row->id_akademik) }}" onsubmit="return confirm('Hapus data pendidikan ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </div>
      </div>

      <div class="collapse form-box {{ old('_form') === $editId ? 'open' : '' }}" id="{{ $editId }}">
        @include('pelamar.profil.partials.form-entri', ['jenis' => 'akademik', 'row' => $row])
      </div>
    </div>
  @endforeach

  @if($daftar->isEmpty())
    <p class="empty">Belum ada data pendidikan. Isi form di atas untuk menambahkan.</p>
  @endif
</section>
@endsection