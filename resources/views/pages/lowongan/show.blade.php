{{--
  Detail Lowongan (route: lowongan.show) -> resources/views/pages/lowongan/show.blade.php
  Bisa dibuka siapa saja (guest maupun login). Tombol lamar menyesuaikan status pengguna.
  Variabel dari controller: $lowongan, $kelayakan (null jika bukan pelamar login), $linkProfil
--}}
@php
  $panah = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M7 17 17 7M8 7h9v9"/></svg>';
  $ceklis = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>';
  $silang = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>';
  $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  @include('pages.lowongan._head', ['title' => $lowongan->judul . ' · Lowongan'])
</head>
<body class="overflow-x-hidden bg-white font-sans text-ink antialiased">

@include('partials.navbar')

<main class="relative z-10 bg-white pt-28">
  <section class="mx-auto max-w-7xl px-6 pb-24 md:px-10">

    <a href="{{ route('lowongan.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-500 hover:text-ewforange">
      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
      Semua lowongan
    </a>

    @if(session('success'))
      <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-800" role="status">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700" role="alert">{{ session('error') }}</div>
    @endif

    {{-- Header --}}
    <header class="mt-6 flex flex-col gap-6 md:flex-row md:items-center">
      <div class="grid h-20 w-20 shrink-0 place-items-center rounded-3xl font-head text-4xl font-medium {{ $lowongan->is_mendesak ? 'bg-ewforange text-ink' : 'bg-ink text-white' }}">{{ $lowongan->huruf }}</div>
      <div class="min-w-0">
        <div class="flex flex-wrap items-center gap-2">
          <p class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">{{ $lowongan->kategori_label }}</p>
          @if($lowongan->is_mendesak && $lowongan->sedang_buka)
            <span class="rounded-full bg-ink px-2 py-0.5 text-[10px] font-bold tracking-wider text-ewforange">URGENT</span>
          @endif
        </div>
        <h1 class="mt-1 font-head text-4xl font-medium tracking-tight md:text-6xl">{{ $lowongan->judul }}</h1>
        <div class="mt-3 flex flex-wrap gap-2 text-[12px] font-semibold">
          <span class="rounded-full bg-sand px-3 py-1.5">Surabaya · On-Site</span>
          <span class="rounded-full bg-sand px-3 py-1.5">Full-Time</span>
          <span class="rounded-full bg-sand px-3 py-1.5">{{ $lowongan->status_label }}</span>
        </div>
      </div>
    </header>

    <div class="mt-10 grid gap-8 lg:grid-cols-[1fr_22rem]">

      {{-- ====== Konten ====== --}}
      <div class="space-y-10">
        <dl class="grid grid-cols-2 gap-3 text-sm md:grid-cols-4">
          <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Gaji</dt><dd class="mt-1 font-head text-lg font-medium">{{ $lowongan->gaji_label }}</dd></div>
          <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Posisi tersedia</dt><dd class="mt-1 font-head text-lg font-medium">{{ $lowongan->kuota }} posisi</dd></div>
          <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Dibuka</dt><dd class="mt-1 font-semibold">{{ optional($lowongan->tgl_buka)->translatedFormat('d M Y') ?? '-' }}</dd></div>
          <div class="rounded-2xl bg-sand p-4"><dt class="text-[11px] font-semibold text-zinc-500">Ditutup</dt><dd class="mt-1 font-semibold">{{ optional($lowongan->tgl_tutup)->translatedFormat('d M Y') ?? 'Hingga terisi' }}</dd></div>
        </dl>

        <section>
          <h2 class="font-head text-2xl font-medium tracking-tight">Deskripsi pekerjaan</h2>
          @if(count($lowongan->deskripsi_list) > 1)
            <ul class="mt-4 space-y-2.5 text-[15px] leading-relaxed text-zinc-600">
              @foreach($lowongan->deskripsi_list as $baris)
                <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-ewforange"></span><span>{{ $baris }}</span></li>
              @endforeach
            </ul>
          @else
            <p class="mt-4 text-[15px] leading-relaxed text-zinc-600">{{ $lowongan->deskripsi }}</p>
          @endif
        </section>

        @if(count($lowongan->kualifikasi_list))
          <section>
            <h2 class="font-head text-2xl font-medium tracking-tight">Kualifikasi</h2>
            <ul class="mt-4 space-y-2.5 text-[15px] leading-relaxed text-zinc-600">
              @foreach($lowongan->kualifikasi_list as $baris)
                <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-ewforange"></span><span>{{ $baris }}</span></li>
              @endforeach
            </ul>
          </section>
        @endif

        @if($lowongan->persyaratan->isNotEmpty())
          <section>
            <h2 class="font-head text-2xl font-medium tracking-tight">Berkas yang perlu disiapkan</h2>
            <p class="mt-1 text-sm text-zinc-500">Berkas ini diunggah saat mengisi formulir pendaftaran.</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
              @foreach($lowongan->persyaratan as $p)
                <div class="rounded-2xl border border-black/10 p-4">
                  <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold">{{ $p->nama_berkas }}</p>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $p->wajib ? 'bg-ink text-ewforange' : 'bg-sand text-zinc-500' }}">{{ $p->wajib ? 'WAJIB' : 'OPSIONAL' }}</span>
                  </div>
                  <p class="mt-1 text-xs text-zinc-500">
                    {{ $p->keterangan ?: 'Format ' . strtoupper(str_replace(',', ', ', $p->format_list)) }} · maks {{ number_format($p->maks_kb_attr / 1024, 1, ',', '.') }} MB
                  </p>
                </div>
              @endforeach
            </div>
          </section>
        @endif

        <p class="flex items-start gap-3 rounded-[1.5rem] border border-black/10 p-4 text-xs leading-relaxed text-zinc-600">
          <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-ewforange text-ink">{!! $ceklis !!}</span>
          <span><strong class="text-ink">Rekrutmen 100% gratis.</strong> Kami tidak pernah meminta biaya akomodasi, transportasi, atau biaya apa pun kepada kandidat.</span>
        </p>
      </div>

      {{-- ====== Sidebar lamar ====== --}}
      <aside class="lg:sticky lg:top-24 lg:self-start">
        <div class="rounded-[1.75rem] bg-sand p-6">
          <p class="text-[11px] font-bold uppercase tracking-[.12em] text-zinc-500">Lamar posisi ini</p>
          <p class="mt-1 font-head text-xl font-medium">{{ $lowongan->judul }}</p>
          <p class="mt-0.5 text-xs font-semibold text-zinc-500">{{ $lowongan->status_label }}</p>

          <div class="mt-5">
            {{-- 1. Lowongan sudah tutup --}}
            @if(! $lowongan->sedang_buka)
              <button type="button" disabled class="w-full cursor-not-allowed rounded-full bg-zinc-300 px-8 py-3.5 text-sm font-semibold text-zinc-500">Pendaftaran ditutup</button>

            {{-- 2. Guest --}}
            @elseif(! $user)
              <a href="{{ route('lowongan.apply', $lowongan->id_lowongan) }}" class="press flex items-center justify-center gap-2 rounded-full bg-ewforange px-8 py-3.5 text-sm font-semibold text-ink">Masuk untuk melamar {!! $panah !!}</a>
              <p class="mt-3 text-xs leading-relaxed text-zinc-500">Untuk melamar kamu wajib punya akun dan melengkapi profil serta berkas persyaratan.</p>
              <a href="{{ route('register') }}" class="press dk mt-3 flex items-center justify-center rounded-full border border-ink/20 bg-white px-8 py-3 text-sm font-semibold">Belum punya akun? Daftar</a>

            {{-- 3. Login tapi bukan pelamar --}}
            @elseif(! $user->isPelamar())
              <p class="rounded-2xl bg-white px-4 py-3 text-xs font-semibold text-zinc-600">Akun {{ $user->role->nama_role ?? '' }} tidak dapat melamar. Gunakan akun pelamar.</p>

            {{-- 4. Sudah melamar --}}
            @elseif($kelayakan['sudah_melamar'])
              <p class="flex items-center gap-2 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-green-700"><span class="grid h-6 w-6 place-items-center rounded-full bg-green-100">{!! $ceklis !!}</span>Kamu sudah melamar lowongan ini</p>

            {{-- 5. Profil belum lengkap --}}
            @elseif(! $kelayakan['profil_lengkap'])
              <div class="rounded-2xl bg-white p-4">
                <p class="text-sm font-semibold">Lengkapi profil dulu</p>
                <p class="mt-1 text-xs text-zinc-500">Data berikut belum terisi:</p>
                <ul class="mt-3 space-y-1.5">
                  @foreach($kelayakan['profil_kurang'] as $k)
                    <li class="flex items-center gap-2 text-xs font-semibold text-rose-600"><span class="grid h-5 w-5 place-items-center rounded-full bg-rose-100">{!! $silang !!}</span>{{ $k }}</li>
                  @endforeach
                </ul>
              </div>
              <a href="{{ $linkProfil }}" class="press dk mt-3 flex items-center justify-center rounded-full bg-ink px-8 py-3.5 text-sm font-semibold text-white">Lengkapi profil</a>

            {{-- 6. Siap melamar --}}
            @else
              <p class="mb-3 flex items-center gap-2 text-xs font-semibold text-green-700"><span class="grid h-5 w-5 place-items-center rounded-full bg-green-100">{!! $ceklis !!}</span>Profilmu sudah lengkap</p>
              <a href="{{ route('lowongan.apply', $lowongan->id_lowongan) }}" class="press flex items-center justify-center gap-2 rounded-full bg-ewforange px-8 py-3.5 text-sm font-semibold text-ink">Lamar sekarang {!! $panah !!}</a>
            @endif
          </div>
        </div>
      </aside>
    </div>
  </section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<div class="relative z-30 bg-white">
  @include('partials.footer')
</div>
</body>
</html>