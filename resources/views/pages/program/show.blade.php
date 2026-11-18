@php
    $panah = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M7 17 17 7M8 7h9v9"/></svg>';

    $ceklis = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>';

    $user = auth()->user();

    $isPelamar = $user && $user->isPelamar();
    $isGuest = !$user;

    $formatList = fn ($p) => is_array($p->format_list)
        ? $p->format_list
        : array_values(array_filter(array_map(
            'trim',
            explode(',', (string) ($p->format_file ?? $p->format_list))
        )));

    $inisial = mb_strtoupper(
        mb_substr($program->nama_program, 0, 1)
    );

    $tanggal = fn ($value) => $value
        ? \Carbon\Carbon::parse($value)->translatedFormat('d M Y')
        : '-';
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    @include('pages.lowongan._head', [
        'title' => $program->nama_program . ' · Program Magang'
    ])
</head>

<body class="overflow-x-hidden bg-white font-sans text-ink antialiased">

@include('partials.navbar')

<main class="relative z-10 bg-white pt-28">

    <section class="mx-auto max-w-7xl px-6 pb-24 md:px-10">

        {{-- KEMBALI --}}
        <a
            href="{{ route('internship.index') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-500 hover:text-ewforange transition"
        >
            <svg
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="m15 18-6-6 6-6"/>
            </svg>

            Semua program magang
        </a>

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div
                class="mt-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-800"
                role="status"
            >
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div
                class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700"
                role="alert"
            >
                {{ session('error') }}
            </div>
        @endif

        {{-- HEADER PROGRAM --}}
        <header class="mt-6 flex flex-col gap-6 md:flex-row md:items-center">

            <div class="grid h-20 w-20 shrink-0 place-items-center rounded-3xl bg-ink font-head text-4xl font-medium text-white">
                {{ $inisial }}
            </div>

            <div class="min-w-0">

                <div class="flex flex-wrap items-center gap-2">

                    <p class="text-[11px] font-bold uppercase tracking-[.14em] text-orange-700">
                        PROGRAM MAGANG
                    </p>

                    @if($program->status === 'dibuka')
                        <span class="rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-bold tracking-wider text-green-700">
                            PENDAFTARAN DIBUKA
                        </span>
                    @else
                        <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-bold tracking-wider text-zinc-500">
                            {{ strtoupper($program->status) }}
                        </span>
                    @endif

                </div>

                <h1 class="mt-1 break-words font-head text-4xl font-medium tracking-tight md:text-6xl">
                    {{ $program->nama_program }}
                </h1>

                <div class="mt-3 flex flex-wrap gap-2 text-[12px] font-semibold">

                    <span class="rounded-full bg-sand px-3 py-1.5">
                        PT Equityworld Futures
                    </span>

                    <span class="rounded-full bg-sand px-3 py-1.5">
                        Program Magang
                    </span>

                    <span class="rounded-full bg-sand px-3 py-1.5">
                        {{ ucfirst($program->status) }}
                    </span>

                </div>

            </div>

        </header>

        <div class="mt-10 grid gap-8 lg:grid-cols-[1fr_22rem]">

            {{-- =========================================
                 KONTEN UTAMA
            ========================================== --}}
            <div class="space-y-10">

                {{-- INFORMASI RINGKAS --}}
                <dl class="grid grid-cols-2 gap-3 text-sm md:grid-cols-4">

                    <div class="rounded-2xl bg-sand p-4">
                        <dt class="text-[11px] font-semibold text-zinc-500">
                            Kuota peserta
                        </dt>

                        <dd class="mt-1 font-head text-lg font-medium">
                            {{ $program->sisa_kuota ?? 0 }} posisi
                        </dd>
                    </div>

                    <div class="rounded-2xl bg-sand p-4">
                        <dt class="text-[11px] font-semibold text-zinc-500">
                            Durasi program
                        </dt>

                        <dd class="mt-1 font-head text-lg font-medium">
                            {{ $program->durasi_hari
                                ? $program->durasi_hari . ' hari'
                                : 'Fleksibel' }}
                        </dd>
                    </div>

                    <div class="rounded-2xl bg-sand p-4">
                        <dt class="text-[11px] font-semibold text-zinc-500">
                            Dibuka
                        </dt>

                        <dd class="mt-1 font-semibold">
                            {{ $tanggal($program->tgl_buka) }}
                        </dd>
                    </div>

                    <div class="rounded-2xl bg-sand p-4">
                        <dt class="text-[11px] font-semibold text-zinc-500">
                            Ditutup
                        </dt>

                        <dd class="mt-1 font-semibold">
                            {{ $tanggal($program->tgl_tutup) }}
                        </dd>
                    </div>

                </dl>

                {{-- PERIODE MAGANG --}}
                <section>

                    <h2 class="font-head text-2xl font-medium tracking-tight">
                        Periode Pelaksanaan Magang
                    </h2>

                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">

                        <div class="rounded-2xl border border-black/10 p-4">
                            <p class="text-[11px] font-semibold text-zinc-500">
                                Tanggal mulai
                            </p>

                            <p class="mt-1 font-semibold">
                                {{ $tanggal($program->tgl_mulai) }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-black/10 p-4">
                            <p class="text-[11px] font-semibold text-zinc-500">
                                Tanggal selesai
                            </p>

                            <p class="mt-1 font-semibold">
                                {{ $tanggal($program->tgl_selesai) }}
                            </p>
                        </div>

                    </div>

                </section>

                {{-- DESKRIPSI --}}
                <section>

                    <h2 class="font-head text-2xl font-medium tracking-tight">
                        Deskripsi & Jobdesk
                    </h2>

                    <div class="mt-4 whitespace-pre-line break-words text-[15px] leading-relaxed text-zinc-600">
                        {{ $program->deskripsi ?: 'Deskripsi program belum tersedia.' }}
                    </div>

                </section>

                {{-- SILABUS --}}
                <section>

                    <h2 class="font-head text-2xl font-medium tracking-tight">
                        Silabus Program
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Pelajari materi dan kegiatan yang akan dijalani selama program magang.
                    </p>

                    @if($silabusAda && $silabusUrl)

                        <div class="mt-4 flex flex-col gap-4 rounded-2xl border border-black/10 p-4 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex min-w-0 items-center gap-3">

                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-sand text-xl">
                                    📄
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-semibold">
                                        Silabus Program Magang
                                    </p>

                                    <p class="mt-1 truncate text-xs text-zinc-500">
                                        {{ basename($program->silabus) }}
                                    </p>
                                </div>

                            </div>

                            <a
                                href="{{ $silabusUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="press inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-ewforange px-5 py-3 text-xs font-semibold text-ink"
                            >
                                Lihat Silabus
                                {!! $panah !!}
                            </a>

                        </div>

                    @else

                        <div class="mt-4 rounded-2xl border border-black/10 p-4">
                            <p class="text-sm text-zinc-500">
                                Silabus program belum tersedia.
                            </p>
                        </div>

                    @endif

                </section>

                {{-- PERSYARATAN BERKAS --}}
                @if($program->persyaratan->isNotEmpty())

                    <section>

                        <h2 class="font-head text-2xl font-medium tracking-tight">
                            Berkas yang Perlu Disiapkan
                        </h2>

                        <p class="mt-1 text-sm text-zinc-500">
                            Berkas berikut perlu diunggah saat mengisi formulir pendaftaran.
                        </p>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">

                            @foreach($program->persyaratan as $p)

                                <div class="rounded-2xl border border-black/10 p-4">

                                    <div class="flex items-center justify-between gap-3">

                                        <p class="text-sm font-semibold">
                                            {{ $p->nama_berkas }}
                                        </p>

                                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $p->wajib ? 'bg-ink text-ewforange' : 'bg-sand text-zinc-500' }}">
                                            {{ $p->wajib ? 'WAJIB' : 'OPSIONAL' }}
                                        </span>

                                    </div>

                                    <p class="mt-1 text-xs text-zinc-500">
                                        {{ $p->keterangan ?: 'Format ' . strtoupper(implode(', ', $formatList($p))) }}
                                        · maks {{ number_format(($p->maks_kb_attr ?? 5120) / 1024, 1, ',', '.') }} MB
                                    </p>

                                </div>

                            @endforeach

                        </div>

                    </section>

                @endif

                {{-- INFORMASI GRATIS --}}
                <p class="flex items-start gap-3 rounded-[1.5rem] border border-black/10 p-4 text-xs leading-relaxed text-zinc-600">

                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-ewforange text-ink">
                        {!! $ceklis !!}
                    </span>

                    <span>
                        <strong class="text-ink">Pendaftaran 100% gratis.</strong>
                        PT Equityworld Futures tidak pernah meminta biaya pendaftaran atau biaya apa pun kepada peserta magang.
                    </span>

                </p>

            </div>

            {{-- =========================================
                 SIDEBAR PENDAFTARAN
            ========================================== --}}
            <aside class="lg:sticky lg:top-24 lg:self-start">

                <div class="rounded-[1.75rem] bg-sand p-6">

                    <p class="text-[11px] font-bold uppercase tracking-[.12em] text-zinc-500">
                        Pendaftaran program
                    </p>

                    <p class="mt-1 font-head text-xl font-medium">
                        {{ $program->nama_program }}
                    </p>

                    <p class="mt-0.5 text-xs font-semibold text-zinc-500">
                        {{ ucfirst($program->status) }}
                    </p>

                    <div class="mt-5">

                        {{-- 1. PROGRAM SUDAH DITUTUP --}}
                        @if($program->status !== 'dibuka')

                            <button
                                type="button"
                                disabled
                                class="w-full cursor-not-allowed rounded-full bg-zinc-300 px-8 py-3.5 text-sm font-semibold text-zinc-500"
                            >
                                Pendaftaran ditutup
                            </button>

                        {{-- 2. GUEST --}}
                        @elseif($isGuest)

                            <a
                                href="{{ route('login') }}"
                                class="press flex items-center justify-center gap-2 rounded-full bg-ewforange px-8 py-3.5 text-sm font-semibold text-ink"
                            >
                                Masuk untuk mendaftar
                                {!! $panah !!}
                            </a>

                            <p class="mt-3 text-xs leading-relaxed text-zinc-500">
                                Untuk mendaftar, kamu wajib memiliki akun pelamar dan melengkapi profil serta berkas persyaratan.
                            </p>

                            <a
                                href="{{ route('register') }}"
                                class="press dk mt-3 flex items-center justify-center rounded-full border border-ink/20 bg-white px-8 py-3 text-sm font-semibold"
                            >
                                Belum punya akun? Daftar
                            </a>

                        {{-- 3. LOGIN TAPI BUKAN PELAMAR --}}
                        @elseif(!$isPelamar)

                            <p class="rounded-2xl bg-white px-4 py-3 text-xs font-semibold text-zinc-600">
                                Akun {{ $user->role->nama_role ?? '' }} tidak dapat mendaftar program magang. Gunakan akun pelamar.
                            </p>

                        {{-- 4. PELAMAR --}}
                        @else

                            <p class="mb-3 text-xs leading-relaxed text-zinc-500">
                                Pastikan profil dan seluruh berkas persyaratan sudah kamu siapkan sebelum mendaftar.
                            </p>

                            <a
                                href="{{ route('pelamar.lamaran.create', [
                                    'id_program' => $program->id_program
                                ]) }}"
                                class="press flex items-center justify-center gap-2 rounded-full bg-ewforange px-8 py-3.5 text-sm font-semibold text-ink"
                            >
                                Daftar Sekarang
                                {!! $panah !!}
                            </a>

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
