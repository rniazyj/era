@extends('pelamar.profil.layout')

@section('title', 'Curriculum Vitae - ERA')

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

</div>
@endsection