@extends('pelamar.profil.layout')

@section('title', 'Skill & Keahlian - ERA')

@php
  $bagian = [
    'hard' => [
      'judul' => 'Hard Skill',
      'sub'   => 'Keahlian teknis beserta levelnya. Tampil sebagai "Data Hard Skill" di CV',
      'data'  => $hard,
      'icon'  => '<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"/>',
    ],
    'soft' => [
      'judul' => 'Soft Skill',
      'sub'   => 'Contoh: Adaptive, Creative, Public Speaking. Tampil sebagai "Data Soft Skill" di CV',
      'data'  => $soft,
      'icon'  => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8Z"/>',
    ],
    'tools' => [
      'judul' => 'Tools',
      'sub'   => 'Aplikasi atau software yang dikuasai. Contoh: Figma, Canva, VS Code',
      'data'  => $tools,
      'icon'  => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9l-3.8 3.8Z"/>',
    ],
  ];
@endphp

@section('konten')
<div class="stack">
  @foreach($bagian as $jenis => $b)
    <section class="card" id="{{ $jenis }}">
      <div class="card-head">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $b['icon'] !!}</svg></div>
        <div class="grow">
          <h3>{{ $b['judul'] }}</h3>
          <p>{{ $b['sub'] }}</p>
        </div>
      </div>

      @if($b['data']->isNotEmpty())
        <div class="chips">
          @foreach($b['data'] as $s)
            <span class="chip">
              {{ $s->nama_skill }}@if(filled($s->level_skill)) <small>{{ $s->level_skill }}</small>@endif
              <form method="POST" action="{{ route('pelamar.profil.skill.destroy', $s->id_skill) }}">
                @csrf @method('DELETE')
                <button type="submit" title="Hapus {{ $s->nama_skill }}" aria-label="Hapus {{ $s->nama_skill }}">×</button>
              </form>
            </span>
          @endforeach
        </div>
      @else
        <p class="empty" style="padding-top:0">Belum ada data.</p>
      @endif

      <form method="POST" action="{{ route('pelamar.profil.skill.store') }}" class="add-row" novalidate>
        @csrf
        <input type="hidden" name="jenis" value="{{ $jenis }}">

        <input class="input" type="text" name="nama" required
               placeholder="{{ $jenis === 'hard' ? 'Nama skill, contoh: Design' : ($jenis === 'soft' ? 'Pisahkan dengan koma: Adaptive, Creative' : 'Pisahkan dengan koma: Figma, Canva') }}">

        @if($jenis === 'hard')
          <select class="select" name="level" required>
            <option value="">Pilih level</option>
            @foreach($level as $l)
              <option value="{{ $l }}">{{ $l }}</option>
            @endforeach
          </select>
        @endif

        <button type="submit" class="btn btn-primary btn-sm">+ Tambah</button>
      </form>
    </section>
  @endforeach
</div>
@endsection