{{--
  Form Pendaftaran (route: lowongan.apply) -> resources/views/pages/lowongan/apply.blade.php
  Hanya untuk pelamar yang login. Variabel: $lowongan, $kelayakan, $user, $linkProfil
--}}
@php
  $ceklis = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>';
  $silang = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>';
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  @include('pages.lowongan._head', ['title' => 'Lamar ' . $lowongan->judul])
</head>
<body class="overflow-x-hidden bg-white font-sans text-ink antialiased">

@include('partials.navbar')

<main class="relative z-10 bg-white pt-28">
  <section class="mx-auto max-w-5xl px-6 pb-24 md:px-10">

    <a href="{{ route('lowongan.show', $lowongan->id_lowongan) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-500 hover:text-ewforange">
      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
      Kembali ke detail lowongan
    </a>

    <p class="mt-6 text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">Formulir pendaftaran</p>
    <h1 class="mt-2 font-head text-3xl font-normal tracking-tight md:text-5xl">Melamar sebagai <strong class="font-semibold text-ewforange">{{ $lowongan->judul }}</strong></h1>
    <p class="mt-3 text-sm text-zinc-500">{{ $lowongan->kategori_label }} · Surabaya, On-Site · {{ $lowongan->status_label }}</p>

    @if(session('error'))
      <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700" role="alert">{{ session('error') }}</div>
    @endif

    @if($errors->any())
      <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700" role="alert">
        <p class="font-semibold">Ada yang perlu diperbaiki:</p>
        <ul class="mt-2 list-disc space-y-0.5 pl-5">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    {{-- ===== Profil belum lengkap: form diblokir ===== --}}
    @unless($kelayakan['profil_lengkap'])
      <div class="mt-8 rounded-[1.75rem] bg-sand p-6 md:p-8">
        <p class="font-head text-2xl font-medium">Lengkapi profilmu dulu</p>
        <p class="mt-1 text-sm text-zinc-500">Formulir baru bisa diisi setelah data profil di bawah ini lengkap.</p>
        <ul class="mt-5 grid gap-2 sm:grid-cols-2">
          @foreach($kelayakan['profil_item'] as $label => $ok)
            <li class="flex items-center gap-3 rounded-2xl bg-white px-4 py-3 text-sm font-semibold {{ $ok ? 'text-green-700' : 'text-rose-600' }}">
              <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full {{ $ok ? 'bg-green-100' : 'bg-rose-100' }}">{!! $ok ? $ceklis : $silang !!}</span>{{ $label }}
            </li>
          @endforeach
        </ul>
        <a href="{{ $linkProfil }}" class="press dk mt-6 inline-flex rounded-full bg-ink px-8 py-3.5 text-sm font-semibold text-white">Lengkapi profil sekarang</a>
      </div>
    @else

    {{-- ===== Form ===== --}}
    <form method="POST" action="{{ route('lowongan.store', $lowongan->id_lowongan) }}" enctype="multipart/form-data" class="mt-8 grid gap-8 lg:grid-cols-[1fr_20rem]" id="formLamar">
      @csrf

      <div class="space-y-6">
        {{-- Data diri (dari profil, hanya baca) --}}
        <section class="rounded-[1.75rem] bg-sand p-6">
          <div class="flex items-center justify-between gap-3">
            <h2 class="font-head text-xl font-medium">Data pelamar</h2>
            <a href="{{ $linkProfil }}" class="text-xs font-semibold text-zinc-500 underline-offset-4 hover:text-ewforange hover:underline">Ubah di profil</a>
          </div>
          <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
            <div class="rounded-2xl bg-white p-4"><dt class="text-[11px] font-semibold text-zinc-500">Nama lengkap</dt><dd class="mt-1 font-semibold">{{ $user->name }}</dd></div>
            <div class="rounded-2xl bg-white p-4"><dt class="text-[11px] font-semibold text-zinc-500">Email</dt><dd class="mt-1 break-all font-semibold">{{ $user->email }}</dd></div>
            <div class="rounded-2xl bg-white p-4"><dt class="text-[11px] font-semibold text-zinc-500">No. telepon</dt><dd class="mt-1 font-semibold">{{ $user->no_telepon }}</dd></div>
            <div class="rounded-2xl bg-white p-4"><dt class="text-[11px] font-semibold text-zinc-500">Kota</dt><dd class="mt-1 font-semibold">{{ $user->profilPelamar->kota ?? '-' }}</dd></div>
          </dl>
          <p class="mt-3 text-xs text-zinc-500">CV dibuat otomatis dari profilmu dan ikut terkirim bersama lamaran.</p>
        </section>

        {{-- Berkas --}}
        <section class="rounded-[1.75rem] border border-black/10 p-6">
          <h2 class="font-head text-xl font-medium">Unggah berkas</h2>
          @if($lowongan->persyaratan->isEmpty())
            <p class="mt-2 text-sm text-zinc-500">Lowongan ini tidak membutuhkan berkas tambahan.</p>
          @else
            <div class="mt-5 space-y-5">
              @foreach($lowongan->persyaratan as $p)
                @php $nama = 'berkas.' . $p->id_persyaratan; @endphp
                <div>
                  <label for="berkas_{{ $p->id_persyaratan }}" class="flex flex-wrap items-center gap-2 text-sm font-semibold">
                    {{ $p->nama_berkas }}
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $p->wajib ? 'bg-ink text-ewforange' : 'bg-sand text-zinc-500' }}">{{ $p->wajib ? 'WAJIB' : 'OPSIONAL' }}</span>
                  </label>
                  <p class="mt-0.5 text-xs text-zinc-500">
                    {{ $p->keterangan ? $p->keterangan . ' · ' : '' }}{{ strtoupper(str_replace(',', ', ', $p->format_list)) }} · maks {{ number_format($p->maks_kb_attr / 1024, 1, ',', '.') }} MB
                  </p>
                  <input type="file" id="berkas_{{ $p->id_persyaratan }}" name="berkas[{{ $p->id_persyaratan }}]"
                         accept="{{ collect(explode(',', $p->format_list))->map(fn ($f) => '.' . trim($f))->implode(',') }}"
                         data-maks="{{ $p->maks_kb_attr }}" {{ $p->wajib ? 'required' : '' }}
                         class="field-file mt-2 block w-full rounded-2xl border border-dashed {{ $errors->has($nama) ? 'border-rose-400 bg-rose-50' : 'border-black/20 bg-sand' }} p-3 text-xs text-zinc-600">
                  <p class="mt-1 hidden text-xs font-semibold text-rose-600" data-err></p>
                  @error($nama)<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                </div>
              @endforeach
            </div>
          @endif
        </section>

        {{-- Pernyataan --}}
        <label class="flex items-start gap-3 rounded-[1.5rem] border border-black/10 p-5 text-sm leading-relaxed text-zinc-600">
          <input type="checkbox" name="pernyataan" value="1" required {{ old('pernyataan') ? 'checked' : '' }} class="mt-1 h-4 w-4 shrink-0 accent-orange-500">
          <span>Saya menyatakan seluruh data dan berkas yang saya kirim adalah benar. Saya memahami bahwa data yang tidak benar dapat membatalkan proses seleksi.</span>
        </label>
      </div>

      {{-- Ringkasan --}}
      <aside class="lg:sticky lg:top-24 lg:self-start">
        <div class="rounded-[1.75rem] bg-ink p-6 text-white">
          <p class="text-[11px] font-bold uppercase tracking-[.12em] text-white/50">Ringkasan</p>
          <p class="mt-1 font-head text-xl font-medium">{{ $lowongan->judul }}</p>
          <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between gap-3"><dt class="text-white/60">Gaji</dt><dd class="font-semibold">{{ $lowongan->gaji_label }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-white/60">Lokasi</dt><dd class="font-semibold">Surabaya</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-white/60">Ditutup</dt><dd class="font-semibold">{{ optional($lowongan->tgl_tutup)->translatedFormat('d M Y') ?? '-' }}</dd></div>
          </dl>
          <button type="submit" id="btnKirim" class="press mt-6 w-full rounded-full bg-ewforange px-8 py-3.5 text-sm font-semibold text-ink">Kirim lamaran</button>
          <p class="mt-3 text-center text-[11px] text-white/50">Lamaran tidak bisa diubah setelah dikirim.</p>
        </div>
      </aside>
    </form>
    @endunless
  </section>
</main>

<script>
  // Cek ukuran file di sisi klien (validasi final tetap di server)
  document.querySelectorAll('input[type=file][data-maks]').forEach(inp => {
    inp.addEventListener('change', () => {
      const err = inp.parentElement.querySelector('[data-err]');
      const f = inp.files[0];
      if (f && f.size / 1024 > +inp.dataset.maks) {
        err.textContent = 'Ukuran file melebihi ' + (inp.dataset.maks / 1024).toFixed(1) + ' MB.';
        err.classList.remove('hidden');
        inp.value = '';
      } else { err.classList.add('hidden'); }
    });
  });
  // Cegah kirim ganda
  const form = document.getElementById('formLamar');
  if (form) form.addEventListener('submit', () => {
    const b = document.getElementById('btnKirim');
    b.disabled = true; b.textContent = 'Mengirim...'; b.classList.add('opacity-70');
  });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<div class="relative z-30 bg-white">
  @include('partials.footer')
</div>
</body>
</html>