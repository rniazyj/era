
{{--
    Form pendaftaran kerja dan magang
    Satu file untuk dua jenis lamaran.
--}}

@php
    // Menentukan jenis lamaran
    $jenisLamaran = $jenisLamaran ?? 'kerja';

    // Menyamakan objek yang digunakan oleh Blade
    $itemLamaran = $itemLamaran ?? $lowongan;
    $persyaratan = $persyaratan ?? $lowongan->persyaratan;

    $isMagang = $jenisLamaran === 'magang';

    // Data dinamis
    $judul = $isMagang
        ? $itemLamaran->nama_program
        : $itemLamaran->judul;

    $backUrl = $isMagang
        ? route('program.show', $itemLamaran->id_program)
        : route('lowongan.show', $itemLamaran->id_lowongan);

    $formAction = $isMagang
        ? route('pelamar.lamaran.store')
        : route('lowongan.store', $itemLamaran->id_lowongan);

    $ceklis = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>';

    $silang = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>';

    $formatList = fn ($p) => is_array($p->format_list)
        ? $p->format_list
        : array_values(array_filter(array_map('trim', explode(',', (string) $p->format_list))));
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    @include('pages.lowongan._head', [
        'title' => 'Lamar ' . $judul
    ])
</head>

<body class="overflow-x-hidden bg-white font-sans text-ink antialiased">

@include('partials.navbar')

<main class="relative z-10 bg-white pt-28">
    <section class="mx-auto max-w-5xl px-6 pb-24 md:px-10">

        {{-- Kembali ke detail --}}
        <a href="{{ $backUrl }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-500 hover:text-ewforange">

            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>

            Kembali ke detail {{ $isMagang ? 'program magang' : 'lowongan' }}
        </a>

        {{-- Judul --}}
        <p class="mt-6 text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">
            Formulir pendaftaran {{ $isMagang ? 'magang' : 'kerja' }}
        </p>

        <h1 class="mt-2 font-head text-3xl font-normal tracking-tight md:text-5xl">
            Melamar sebagai
            <strong class="font-semibold text-ewforange">
                {{ $judul }}
            </strong>
        </h1>

        @if($isMagang)
            <p class="mt-3 text-sm text-zinc-500">
                Program Magang · PT Equityworld Futures
            </p>
        @else
            <p class="mt-3 text-sm text-zinc-500">
                {{ $itemLamaran->kategori_label }} · Surabaya, On-Site · {{ $itemLamaran->status_label }}
            </p>
        @endif

        {{-- Pesan error --}}
        @if(session('error'))
            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700"
                 role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700"
                 role="alert">

                <p class="font-semibold">Ada yang perlu diperbaiki:</p>

                <ul class="mt-2 list-disc space-y-0.5 pl-5">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Profil belum lengkap --}}
        @unless($kelayakan['profil_lengkap'])

            <div class="mt-8 rounded-[1.75rem] bg-sand p-6 md:p-8">

                <p class="font-head text-2xl font-medium">
                    Lengkapi profilmu dulu
                </p>

                <p class="mt-1 text-sm text-zinc-500">
                    Formulir baru bisa diisi setelah data profil di bawah ini lengkap.
                </p>

                <ul class="mt-5 grid gap-2 sm:grid-cols-2">
                    @foreach($kelayakan['profil_item'] as $label => $ok)
                        <li class="flex items-center gap-3 rounded-2xl bg-white px-4 py-3 text-sm font-semibold {{ $ok ? 'text-green-700' : 'text-rose-600' }}">

                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full {{ $ok ? 'bg-green-100' : 'bg-rose-100' }}">
                                {!! $ok ? $ceklis : $silang !!}
                            </span>

                            {{ $label }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ $linkProfil }}"
                   class="press dk mt-6 inline-flex rounded-full bg-ink px-8 py-3.5 text-sm font-semibold text-white">
                    Lengkapi profil sekarang
                </a>
            </div>

        @else

            {{-- Form utama --}}
            <form method="POST"
                  action="{{ $formAction }}"
                  enctype="multipart/form-data"
                  class="mt-8 grid gap-8 lg:grid-cols-[1fr_20rem]"
                  id="formLamar">

                @csrf

                {{-- ID program khusus magang --}}
                @if($isMagang)
                    <input type="hidden" name="id_program" value="{{ $itemLamaran->id_program }}">
                @endif

                <div class="space-y-6">

                    
{{-- Data diri --}}
@if($isMagang)

    <section class="rounded-[1.75rem] bg-sand p-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-head text-xl font-medium">
                Ketua tim
            </h2>

            <span class="rounded-full bg-ink px-3 py-1 text-[10px] font-bold text-ewforange">
                KETUA
            </span>
        </div>

        <p class="mt-1 text-sm text-zinc-500">
            Data ketua diambil otomatis dari akun dan profil ERA yang sedang digunakan.
        </p>

        
<div class="mt-5 grid gap-4 sm:grid-cols-2">

    {{-- Nama --}}
    <div>
        <label class="text-xs font-semibold">Nama lengkap *</label>
        <input type="text"
               name="anggota[0][nama]"
               value="{{ old('anggota.0.nama', $user->name) }}"
               required
               maxlength="255"
               class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-ewforange">
    </div>

    {{-- Email --}}
    <div>
        <label class="text-xs font-semibold">Email *</label>
        <input type="email"
               name="anggota[0][email]"
               value="{{ old('anggota.0.email', $user->email) }}"
               required
               maxlength="255"
               class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-ewforange">
    </div>

    {{-- Nomor telepon --}}
    <div>
        <label class="text-xs font-semibold">No. telepon *</label>
        <input type="text"
               name="anggota[0][no_telepon]"
               value="{{ old('anggota.0.no_telepon', $user->no_telepon) }}"
               required
               maxlength="30"
               class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-ewforange">
    </div>

    {{-- NPM --}}
    <div>
        <label class="text-xs font-semibold">NPM *</label>
        <input type="text"
               name="anggota[0][npm]"
               value="{{ old('anggota.0.npm', $pendidikanUtama->npm ?? '') }}"
               required
               maxlength="30"
               class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-ewforange">
    </div>

    {{-- Jenjang --}}
    <div>
        <label class="text-xs font-semibold">Jenjang pendidikan *</label>
        <select name="anggota[0][jenjang]"
                required
                class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-ewforange">
            <option value="">Pilih jenjang</option>
            @foreach(['SMA', 'SMK', 'D3', 'D4', 'S1', 'S2', 'S3'] as $jenjang)
                <option value="{{ $jenjang }}"
                    {{ old('anggota.0.jenjang', $pendidikanUtama->jenjang ?? '') === $jenjang ? 'selected' : '' }}>
                    {{ $jenjang }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Institusi --}}
    <div>
        <label class="text-xs font-semibold">Institusi *</label>
        <input type="text"
               name="anggota[0][institusi]"
               value="{{ old('anggota.0.institusi', $pendidikanUtama->institusi ?? '') }}"
               required
               maxlength="255"
               class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-ewforange">
    </div>

    {{-- Fakultas --}}
    <div>
        <label class="text-xs font-semibold">Fakultas *</label>
        <input type="text"
               name="anggota[0][fakultas]"
               value="{{ old('anggota.0.fakultas', $pendidikanUtama->fakultas ?? '') }}"
               required
               maxlength="255"
               class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-ewforange">
    </div>

    {{-- Program studi --}}
    <div>
        <label class="text-xs font-semibold">Program studi *</label>
        <input type="text"
               name="anggota[0][program_studi]"
               value="{{ old('anggota.0.program_studi', $pendidikanUtama->jurusan ?? '') }}"
               required
               maxlength="255"
               class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-ewforange">
    </div>

</div>

        <p class="mt-3 text-xs text-zinc-500">
            Data akademik diambil dari pendidikan yang ditandai sebagai pendidikan utama di profilmu.
        </p>

        <a href="{{ $linkProfil }}"
           class="mt-3 inline-block text-xs font-semibold text-zinc-500 underline hover:text-ewforange">
            Ubah data melalui profil
        </a>
    </section>

    {{-- Anggota tambahan --}}
    <section class="rounded-[1.75rem] border border-black/10 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-head text-xl font-medium">Anggota tim</h2>
                <p class="mt-1 text-sm text-zinc-500">
                    Tambahkan maksimal 3 orang selain ketua.
                </p>
            </div>

            <span class="rounded-full bg-sand px-3 py-1.5 text-xs font-bold text-zinc-600"
                  id="jumlahAnggota">
                1 / 4 orang
            </span>
        </div>

        <div id="daftarAnggota" class="mt-5 space-y-5"></div>

        <button type="button"
                id="tambahAnggota"
                class="mt-5 inline-flex items-center gap-2 rounded-full border border-ewforange px-5 py-3 text-sm font-semibold text-ewforange transition hover:bg-orange-50">
            <span class="text-lg leading-none">+</span>
            Tambah anggota
        </button>

        <p class="mt-3 text-xs text-zinc-500">
            Anggota tambahan tidak perlu memiliki akun ERA. Isi data sesuai identitas masing-masing.
        </p>
    </section>

@else

    {{-- Data diri untuk pendaftaran kerja: tetap seperti sebelumnya --}}
    <section class="rounded-[1.75rem] bg-sand p-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-head text-xl font-medium">Data pelamar</h2>

            <a href="{{ $linkProfil }}"
               class="text-xs font-semibold text-zinc-500 underline-offset-4 hover:text-ewforange hover:underline">
                Ubah di profil
            </a>
        </div>

        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
            <div class="rounded-2xl bg-white p-4">
                <dt class="text-[11px] font-semibold text-zinc-500">Nama lengkap</dt>
                <dd class="mt-1 font-semibold">{{ $user->name }}</dd>
            </div>

            <div class="rounded-2xl bg-white p-4">
                <dt class="text-[11px] font-semibold text-zinc-500">Email</dt>
                <dd class="mt-1 break-all font-semibold">{{ $user->email }}</dd>
            </div>

            <div class="rounded-2xl bg-white p-4">
                <dt class="text-[11px] font-semibold text-zinc-500">No. telepon</dt>
                <dd class="mt-1 font-semibold">{{ $user->no_telepon }}</dd>
            </div>

            <div class="rounded-2xl bg-white p-4">
                <dt class="text-[11px] font-semibold text-zinc-500">Kota</dt>
                <dd class="mt-1 font-semibold">{{ $user->profilPelamar->kota ?? '-' }}</dd>
            </div>
        </dl>

        <p class="mt-3 text-xs text-zinc-500">
            CV dibuat otomatis dari profilmu dan ikut terkirim bersama lamaran.
        </p>
    </section>

@endif

@if($isMagang)
    <template id="templateAnggota">
        <div class="anggota-card rounded-2xl border border-black/10 p-5"
             data-member-card>

            <div class="flex items-center justify-between gap-3">
                <h3 class="font-semibold text-sm">
                    Anggota <span data-nomor></span>
                </h3>

                <button type="button"
                        data-hapus-anggota
                        class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                    Hapus anggota
                </button>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">

                <div>
                    <label class="text-xs font-semibold">Nama lengkap *</label>
                    <input type="text"
                           name="anggota[__INDEX__][nama]"
                           required
                           maxlength="255"
                           class="mt-2 w-full rounded-xl border border-black/10 bg-sand px-4 py-3 text-sm outline-none focus:border-ewforange">
                </div>

                <div>
                    <label class="text-xs font-semibold">Email *</label>
                    <input type="email"
                           name="anggota[__INDEX__][email]"
                           required
                           maxlength="255"
                           class="mt-2 w-full rounded-xl border border-black/10 bg-sand px-4 py-3 text-sm outline-none focus:border-ewforange">
                </div>

                <div>
                    <label class="text-xs font-semibold">No. telepon *</label>
                    <input type="text"
                           name="anggota[__INDEX__][no_telepon]"
                           required
                           maxlength="30"
                           class="mt-2 w-full rounded-xl border border-black/10 bg-sand px-4 py-3 text-sm outline-none focus:border-ewforange">
                </div>

                <div>
                    <label class="text-xs font-semibold">NPM *</label>
                    <input type="text"
                           name="anggota[__INDEX__][npm]"
                           required
                           maxlength="30"
                           class="mt-2 w-full rounded-xl border border-black/10 bg-sand px-4 py-3 text-sm outline-none focus:border-ewforange">
                </div>

                <div>
                    <label class="text-xs font-semibold">Jenjang pendidikan *</label>
                    <select name="anggota[__INDEX__][jenjang]"
                            required
                            class="mt-2 w-full rounded-xl border border-black/10 bg-sand px-4 py-3 text-sm outline-none focus:border-ewforange">
                        <option value="">Pilih jenjang</option>
                        <option value="SMA">SMA</option>
<option value="SMK">SMK</option>
<option value="D3">D3</option>
<option value="D4">D4</option>
<option value="S1">S1</option>
<option value="S2">S2</option>
<option value="S3">S3</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold">Institusi *</label>
                    <input type="text"
                           name="anggota[__INDEX__][institusi]"
                           required
                           maxlength="255"
                           class="mt-2 w-full rounded-xl border border-black/10 bg-sand px-4 py-3 text-sm outline-none focus:border-ewforange">
                </div>

                <div>
                    <label class="text-xs font-semibold">Fakultas *</label>
                    <input type="text"
                           name="anggota[__INDEX__][fakultas]"
                           required
                           maxlength="255"
                           class="mt-2 w-full rounded-xl border border-black/10 bg-sand px-4 py-3 text-sm outline-none focus:border-ewforange">
                </div>

                <div>
                    <label class="text-xs font-semibold">Program studi *</label>
                    <input type="text"
                           name="anggota[__INDEX__][program_studi]"
                           required
                           maxlength="255"
                           class="mt-2 w-full rounded-xl border border-black/10 bg-sand px-4 py-3 text-sm outline-none focus:border-ewforange">
                </div>

            </div>

            <div class="mt-6 border-t border-black/10 pt-5">
                <h4 class="text-sm font-semibold">Berkas persyaratan</h4>
                <p class="mt-1 text-xs text-zinc-500">
                    Unggah berkas milik anggota ini, bukan milik ketua atau anggota lain.
                </p>

                <div class="mt-4 space-y-5">
                    @foreach($persyaratan as $p)
                        <div>
                            <label class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                {{ $p->nama_berkas }}

                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $p->wajib ? 'bg-ink text-ewforange' : 'bg-sand text-zinc-500' }}">
                                    {{ $p->wajib ? 'WAJIB' : 'OPSIONAL' }}
                                </span>
                            </label>

                            <p class="mt-1 text-xs text-zinc-500">
                                {{ strtoupper(implode(', ', $formatList($p))) }} · maks 5 MB
                            </p>

                            <input type="file"
                                   name="anggota[__INDEX__][berkas][{{ $p->id_persyaratan }}]"
                                   accept="{{ collect($formatList($p))->map(fn ($f) => '.' . trim($f))->implode(',') }}"
                                   data-maks="5120"
                                   {{ $p->wajib ? 'required' : '' }}
                                   class="field-file mt-2 block w-full rounded-2xl border border-dashed border-black/20 bg-sand p-3 text-xs text-zinc-600">

                            <p class="mt-1 hidden text-xs font-semibold text-rose-600" data-err></p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </template>
@endif

                    {{-- Tanggal magang --}}
                    @if($isMagang)
                        <section class="rounded-[1.75rem] border border-black/10 p-6">

                            <h2 class="font-head text-xl font-medium">
                                Periode magang
                            </h2>

                            <p class="mt-1 text-sm text-zinc-500">
                                Tentukan rencana tanggal mulai dan selesai magangmu.
                            </p>

                            <div class="mt-5 grid gap-4 sm:grid-cols-2">

                                <div>
                                    <label for="tanggal_mulai"
                                           class="text-sm font-semibold">
                                        Tanggal mulai
                                        <span class="text-rose-500">*</span>
                                    </label>

                                    <input type="date"
                                           id="tanggal_mulai"
                                           name="tanggal_mulai"
                                           value="{{ old('tanggal_mulai') }}"
                                           required
                                           class="mt-2 block w-full rounded-2xl border {{ $errors->has('tanggal_mulai') ? 'border-rose-400 bg-rose-50' : 'border-black/10 bg-sand' }} px-4 py-3 text-sm text-zinc-700 outline-none focus:border-ewforange">

                                    @error('tanggal_mulai')
                                        <p class="mt-1 text-xs font-semibold text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tanggal_selesai"
                                           class="text-sm font-semibold">
                                        Tanggal selesai
                                        <span class="text-rose-500">*</span>
                                    </label>

                                    <input type="date"
                                           id="tanggal_selesai"
                                           name="tanggal_selesai"
                                           value="{{ old('tanggal_selesai') }}"
                                           required
                                           class="mt-2 block w-full rounded-2xl border {{ $errors->has('tanggal_selesai') ? 'border-rose-400 bg-rose-50' : 'border-black/10 bg-sand' }} px-4 py-3 text-sm text-zinc-700 outline-none focus:border-ewforange">

                                    @error('tanggal_selesai')
                                        <p class="mt-1 text-xs font-semibold text-rose-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>
                        </section>
                    @endif

                    
{{-- Berkas --}}
<section class="rounded-[1.75rem] border border-black/10 p-6">

    <h2 class="font-head text-xl font-medium">
        {{ $isMagang ? 'Berkas persyaratan ketua' : 'Unggah berkas' }}
    </h2>

    @if($persyaratan->isEmpty())

        <p class="mt-2 text-sm text-zinc-500">
            {{ $isMagang ? 'Program magang' : 'Lowongan' }} ini tidak membutuhkan berkas tambahan.
        </p>

    @elseif($isMagang)

        <p class="mt-1 text-sm text-zinc-500">
            Unggah berkas milik ketua tim.
        </p>

        <div class="mt-5 space-y-5">

            @foreach($persyaratan as $p)
                @php
                    $nama = 'anggota.0.berkas.' . $p->id_persyaratan;
                @endphp

                <div>
                    <label for="berkas_ketua_{{ $p->id_persyaratan }}"
                           class="flex flex-wrap items-center gap-2 text-sm font-semibold">
                        {{ $p->nama_berkas }}

                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $p->wajib ? 'bg-ink text-ewforange' : 'bg-sand text-zinc-500' }}">
                            {{ $p->wajib ? 'WAJIB' : 'OPSIONAL' }}
                        </span>
                    </label>

                    <p class="mt-0.5 text-xs text-zinc-500">
                        Berkas ketua · {{ strtoupper(implode(', ', $formatList($p))) }} · maks 5 MB
                    </p>

                    <input type="file"
                           id="berkas_ketua_{{ $p->id_persyaratan }}"
                           name="anggota[0][berkas][{{ $p->id_persyaratan }}]"
                           accept="{{ collect($formatList($p))->map(fn ($f) => '.' . trim($f))->implode(',') }}"
                           data-maks="5120"
                           {{ $p->wajib ? 'required' : '' }}
                           class="field-file mt-2 block w-full rounded-2xl border border-dashed border-black/20 bg-sand p-3 text-xs text-zinc-600">

                    <p class="mt-1 hidden text-xs font-semibold text-rose-600" data-err></p>
                </div>
            @endforeach

        </div>

    @else

        {{-- Berkas pendaftaran kerja: tidak diubah --}}
        <div class="mt-5 space-y-5">

            @foreach($persyaratan as $p)
                @php
                    $nama = 'berkas.' . $p->id_persyaratan;
                @endphp

                <div>
                    <label for="berkas_{{ $p->id_persyaratan }}"
                           class="flex flex-wrap items-center gap-2 text-sm font-semibold">
                        {{ $p->nama_berkas }}

                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $p->wajib ? 'bg-ink text-ewforange' : 'bg-sand text-zinc-500' }}">
                            {{ $p->wajib ? 'WAJIB' : 'OPSIONAL' }}
                        </span>
                    </label>

                    <p class="mt-0.5 text-xs text-zinc-500">
                        {{ strtoupper(implode(', ', $formatList($p))) }} · maks 5 MB
                    </p>

                    <input type="file"
                           id="berkas_{{ $p->id_persyaratan }}"
                           name="berkas[{{ $p->id_persyaratan }}]"
                           accept="{{ collect($formatList($p))->map(fn ($f) => '.' . trim($f))->implode(',') }}"
                           data-maks="5120"
                           {{ $p->wajib ? 'required' : '' }}
                           class="field-file mt-2 block w-full rounded-2xl border border-dashed border-black/20 bg-sand p-3 text-xs text-zinc-600">

                    <p class="mt-1 hidden text-xs font-semibold text-rose-600" data-err></p>
                </div>
            @endforeach

        </div>

    @endif
</section>

                    {{-- Pernyataan --}}
                    <label class="flex items-start gap-3 rounded-[1.5rem] border border-black/10 p-5 text-sm leading-relaxed text-zinc-600">

                        <input type="checkbox"
                               name="pernyataan"
                               value="1"
                               required
                               {{ old('pernyataan') ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 shrink-0 accent-orange-500">

                        <span>
                            Saya menyatakan seluruh data dan berkas yang saya kirim adalah benar.
                            Saya memahami bahwa data yang tidak benar dapat membatalkan proses seleksi.
                        </span>

                    </label>

                </div>

                {{-- Ringkasan --}}
                <aside class="lg:sticky lg:top-24 lg:self-start">

                    <div class="rounded-[1.75rem] bg-ink p-6 text-white">

                        <p class="text-[11px] font-bold uppercase tracking-[.12em] text-white/50">
                            Ringkasan
                        </p>

                        <p class="mt-1 font-head text-xl font-medium">
                            {{ $judul }}
                        </p>

                        <dl class="mt-4 space-y-2 text-sm">

                            @if($isMagang)

                                <div class="flex justify-between gap-3">
                                    <dt class="text-white/60">Jenis</dt>
                                    <dd class="font-semibold">Program Magang</dd>
                                </div>

                                <div class="flex justify-between gap-3">
                                    <dt class="text-white/60">Durasi program</dt>
                                    <dd class="font-semibold">
                                        {{ $itemLamaran->durasi_hari ? $itemLamaran->durasi_hari . ' hari' : '-' }}
                                    </dd>
                                </div>

                                <div class="flex justify-between gap-3">
                                    <dt class="text-white/60">Kuota</dt>
                                    <dd class="font-semibold">
                                        {{ $itemLamaran->kuota ?? '-' }}
                                    </dd>
                                </div>

                                <div class="flex justify-between gap-3">
                                    <dt class="text-white/60">Pendaftaran ditutup</dt>
                                    <dd class="font-semibold">
                                        {{ $itemLamaran->tgl_tutup ? \Carbon\Carbon::parse($itemLamaran->tgl_tutup)->translatedFormat('d M Y') : '-' }}
                                    </dd>
                                </div>

                            @else

                                <div class="flex justify-between gap-3">
                                    <dt class="text-white/60">Gaji</dt>
                                    <dd class="font-semibold">
                                        {{ $itemLamaran->gaji_label }}
                                    </dd>
                                </div>

                                <div class="flex justify-between gap-3">
                                    <dt class="text-white/60">Lokasi</dt>
                                    <dd class="font-semibold">Surabaya</dd>
                                </div>

                                <div class="flex justify-between gap-3">
                                    <dt class="text-white/60">Ditutup</dt>
                                    <dd class="font-semibold">
                                        {{ optional($itemLamaran->tgl_tutup)->translatedFormat('d M Y') ?? '-' }}
                                    </dd>
                                </div>

                            @endif

                        </dl>

                        <button type="submit"
                                id="btnKirim"
                                class="press mt-6 w-full rounded-full bg-ewforange px-8 py-3.5 text-sm font-semibold text-ink">

                            Kirim lamaran

                        </button>

                        <p class="mt-3 text-center text-[11px] text-white/50">
                            Lamaran tidak bisa diubah setelah dikirim.
                        </p>

                    </div>

                </aside>

            </form>

        @endunless

    </section>
</main>

<script>
    // Validasi ukuran berkas maksimal 5 MB
    // Validasi ukuran berkas maksimal 5 MB
document.addEventListener('change', event => {
    const inp = event.target;

    if (!inp.matches('input[type="file"][data-maks]')) return;

    const err = inp.parentElement.querySelector('[data-err]');
    const file = inp.files[0];

    if (file && file.size > 5 * 1024 * 1024) {
        if (err) {
            err.textContent = 'Ukuran file maksimal 5 MB.';
            err.classList.remove('hidden');
        }

        inp.value = '';
    } else if (err) {
        err.classList.add('hidden');
        err.textContent = '';
    }
});

    // Cegah pengiriman ganda
    const form = document.getElementById('formLamar');

    if (form) {
        form.addEventListener('submit', () => {
            const button = document.getElementById('btnKirim');

            button.disabled = true;
            button.textContent = 'Mengirim...';
            button.classList.add('opacity-70');
        });
    }


    // Pengelolaan anggota tim magang
    const daftarAnggota = document.getElementById('daftarAnggota');
    const templateAnggota = document.getElementById('templateAnggota');
    const tombolTambah = document.getElementById('tambahAnggota');
    const jumlahAnggota = document.getElementById('jumlahAnggota');

    if (daftarAnggota && templateAnggota && tombolTambah) {
        const MAX_ANGGOTA = 4;

        
function perbaruiAnggota() {
    const cards = daftarAnggota.querySelectorAll('[data-member-card]');

    cards.forEach((card, index) => {
        const nomor = index + 2;
        const indeks = index + 1;

        card.querySelector('[data-nomor]').textContent = nomor;

        card.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace(
                /anggota\[(?:\d+|__INDEX__)\]/,
                `anggota[${indeks}]`
            );
        });
    });

    jumlahAnggota.textContent = `${cards.length + 1} / ${MAX_ANGGOTA} orang`;

    tombolTambah.disabled = cards.length + 1 >= MAX_ANGGOTA;

    tombolTambah.classList.toggle(
        'opacity-40',
        tombolTambah.disabled
    );

    tombolTambah.classList.toggle(
        'cursor-not-allowed',
        tombolTambah.disabled
    );
}


        tombolTambah.addEventListener('click', () => {
            const cards = daftarAnggota.querySelectorAll('[data-member-card]');

            if (cards.length + 1 >= MAX_ANGGOTA) return;

            const fragment = templateAnggota.content.cloneNode(true);
            daftarAnggota.appendChild(fragment);

            perbaruiAnggota();
        });

        daftarAnggota.addEventListener('click', event => {
            const tombolHapus = event.target.closest('[data-hapus-anggota]');

            if (!tombolHapus) return;

            tombolHapus.closest('[data-member-card]').remove();
            perbaruiAnggota();
        });

        perbaruiAnggota();
    }

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<div class="relative z-30 bg-white">
    @include('partials.footer')
</div>

</body>
</html>
