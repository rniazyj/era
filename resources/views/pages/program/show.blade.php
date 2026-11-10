@php
    $tanggal = fn ($value) => $value
        ? \Carbon\Carbon::parse($value)->translatedFormat('d F Y')
        : 'Belum tersedia';

    $user = auth()->user();

    $isPelamar = $user && $user->isPelamar();

    $isGuest = auth()->guest();

    $isBisaLamar = $isGuest || $isPelamar;
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $program->nama_program }} - ERA</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        paper: '#faf9fc',
                        ewforange: '#f97316'
                    }
                }
            }
        };
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .detail-card {
            transition: all .25s ease;
        }

        .detail-card:hover {
            border-color: rgba(249, 115, 22, .2);
        }
    </style>
</head>

<body class="bg-paper text-zinc-900 antialiased overflow-x-hidden">

    <div class="flex min-h-screen flex-col lg:flex-row">

        {{-- NAVBAR --}}
        @include('partials.navbar')

        {{-- MAIN CONTENT --}}
        <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10
                     pt-20 sm:pt-24 lg:pt-10 pb-20">

            <div class="max-w-5xl mx-auto space-y-6">

                {{-- HEADER --}}
                <div>

                    <a
                        href="{{ route('internship.index') }}"
                        class="inline-flex items-center gap-2 text-xs font-semibold
                               text-zinc-400 hover:text-ewforange transition mb-5"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>

                        Kembali ke Internship
                    </a>

                    <div class="flex items-center gap-2 mb-3">

                        <span
                            class="px-3 py-1 rounded-full bg-orange-50
                                   text-ewforange text-[10px] font-bold
                                   uppercase tracking-wider"
                        >
                            PROGRAM MAGANG
                        </span>

                        <span
                            class="px-3 py-1 rounded-full text-[10px] font-bold
                                   {{ $program->status === 'dibuka'
                                       ? 'bg-green-50 text-green-600'
                                       : 'bg-zinc-100 text-zinc-500' }}"
                        >
                            {{ ucfirst($program->status) }}
                        </span>

                    </div>

                    <h1 class="text-2xl sm:text-3xl md:text-4xl
                               font-bold tracking-tight break-words">
                        {{ $program->nama_program }}
                    </h1>

                    <p class="text-sm text-zinc-400 mt-3">
                        Informasi lengkap program magang
                        PT Equityworld Futures
                    </p>

                </div>


                {{-- INFORMASI PROGRAM --}}
                <section
                    class="detail-card bg-white rounded-3xl
                           border border-black/5 shadow-sm p-5 sm:p-8"
                >

                    <div class="flex items-center gap-3 mb-6">

                        <div>
                            <h2 class="text-sm font-bold">
                                Informasi Program
                            </h2>

                            <p class="text-[11px] text-zinc-400 mt-1">
                                Detail posisi magang yang dipilih
                            </p>
                        </div>

                    </div>

                    {{-- INFORMASI RINGKAS --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2
                                lg:grid-cols-3 gap-3 mb-8">

                        <div class="rounded-2xl bg-paper p-4">

                            <p class="text-[10px] text-zinc-400
                                      font-semibold uppercase tracking-wide">
                                Kuota
                            </p>

                            <p class="text-lg font-bold mt-2">
                                {{ $program->kuota ?? 0 }}
                                <span class="text-xs font-medium text-zinc-400">
                                    Posisi
                                </span>
                            </p>

                        </div>

                        <div class="rounded-2xl bg-paper p-4">

                            <p class="text-[10px] text-zinc-400
                                      font-semibold uppercase tracking-wide">
                                Periode Pendaftaran
                            </p>

                            <p class="text-xs font-semibold mt-2">
                                {{ $tanggal($program->tgl_buka) }}
                            </p>

                            <p class="text-xs text-zinc-400 mt-1">
                                s/d {{ $tanggal($program->tgl_tutup) }}
                            </p>

                        </div>

                        <div class="rounded-2xl bg-paper p-4">

                            <p class="text-[10px] text-zinc-400
                                      font-semibold uppercase tracking-wide">
                                Periode Magang
                            </p>

                            <p class="text-xs font-semibold mt-2">
                                {{ $tanggal($program->tgl_mulai) }}
                            </p>

                            <p class="text-xs text-zinc-400 mt-1">
                                s/d {{ $tanggal($program->tgl_selesai) }}
                            </p>

                        </div>

                    </div>


                    {{-- DESKRIPSI / JOBDESK --}}
                    <div class="mb-8">

                        <h2 class="text-sm font-bold mb-4">
                            Deskripsi & Jobdesk
                        </h2>

                        <div
                            class="text-sm text-zinc-600 leading-7
                                   whitespace-pre-line break-words"
                        >
                            {{ $program->deskripsi ?: 'Deskripsi program belum tersedia.' }}
                        </div>

                    </div>


                    {{-- SILABUS --}}
                    <div class="mb-8">

                        <h2 class="text-sm font-bold mb-4">
                            Silabus Program
                        </h2>

                        @if($silabusAda && $silabusUrl)

                            <div
                                class="flex flex-col sm:flex-row sm:items-center
                                       justify-between gap-4 rounded-2xl
                                       bg-orange-50/70 border border-orange-100
                                       p-4"
                            >

                                <div class="flex items-center gap-3 min-w-0">

                                    <div
                                        class="w-11 h-11 rounded-xl bg-white
                                               flex items-center justify-center
                                               text-xl shrink-0"
                                    >
                                        📄
                                    </div>

                                    <div class="min-w-0">

                                        <p class="text-xs font-bold text-zinc-900">
                                            Silabus Program Magang
                                        </p>

                                        <p class="text-[10px] text-zinc-400 mt-1">
                                            {{ basename($program->silabus) }}
                                        </p>

                                    </div>

                                </div>

                                <a
                                    href="{{ $silabusUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center
                                           gap-2 px-5 py-2.5 rounded-full
                                           bg-ewforange hover:bg-orange-600
                                           text-white text-xs font-bold
                                           transition whitespace-nowrap"
                                >
                                    Lihat Silabus

                                    <svg
                                        class="w-3.5 h-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M14 3h7v7m0-7L10 14M19 14v5a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h5"
                                        />
                                    </svg>

                                </a>

                            </div>

                        @else

                            <div
                                class="rounded-2xl bg-zinc-50
                                       border border-black/5 p-4"
                            >
                                <p class="text-xs text-zinc-400">
                                    Silabus belum tersedia.
                                </p>
                            </div>

                        @endif

                    </div>


                    {{-- PERSYARATAN BERKAS --}}
                    <div>

                        <div class="flex items-center justify-between
                                    gap-3 mb-4">

                            <h2 class="text-sm font-bold">
                                Persyaratan Berkas
                            </h2>

                            <span
                                class="text-[10px] text-zinc-400
                                       font-medium whitespace-nowrap"
                            >
                                {{ $program->persyaratan->count() }}
                                berkas
                            </span>

                        </div>

                        @forelse($program->persyaratan as $persyaratan)

                            <div
                                class="flex items-center justify-between
                                       gap-3 py-3 border-b border-black/5
                                       last:border-b-0"
                            >

                                <div class="flex items-center gap-3 min-w-0">

                                    <div
                                        class="w-9 h-9 rounded-xl bg-orange-50
                                               text-ewforange flex items-center
                                               justify-center shrink-0"
                                    >
                                        📄
                                    </div>

                                    <div class="min-w-0">

                                        <p class="text-xs font-semibold
                                                  text-zinc-800 break-words">
                                            {{ $persyaratan->nama_berkas }}
                                        </p>

                                        <p class="text-[10px] text-zinc-400 mt-1">
                                            Format:
                                            {{ $persyaratan->format_file ?: 'Bebas' }}
                                        </p>

                                    </div>

                                </div>

                                <span
                                    class="px-2.5 py-1 rounded-full
                                           text-[10px] font-bold shrink-0
                                           {{ $persyaratan->wajib
                                               ? 'bg-rose-50 text-rose-500'
                                               : 'bg-zinc-100 text-zinc-500' }}"
                                >
                                    {{ $persyaratan->wajib ? 'Wajib' : 'Opsional' }}
                                </span>

                            </div>

                        @empty

                            <div
                                class="rounded-2xl bg-zinc-50
                                       border border-black/5 p-4"
                            >
                                <p class="text-xs text-zinc-400">
                                    Belum ada persyaratan berkas
                                    yang ditambahkan.
                                </p>
                            </div>

                        @endforelse

                    </div>

                </section>


                {{-- TOMBOL AKSI --}}
                <section
                    class="bg-white rounded-3xl border border-black/5
                           shadow-sm p-5 sm:p-6"
                >

                    <div
                        class="flex flex-col-reverse sm:flex-row
                               sm:items-center sm:justify-between gap-3"
                    >

                        {{-- KEMBALI --}}
                        <a
                            href="{{ route('internship.index') }}"
                            class="inline-flex items-center justify-center
                                   px-6 py-3 rounded-full bg-zinc-100
                                   hover:bg-zinc-200 text-zinc-700
                                   text-xs font-semibold transition"
                        >
                            Kembali
                        </a>


                        {{-- LAMAR POSISI --}}
                        @if($isGuest)

                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center justify-center
                                       gap-2 px-6 py-3 rounded-full
                                       bg-ewforange hover:bg-orange-600
                                       text-white text-xs font-bold
                                       shadow-lg shadow-orange-500/20
                                       transition"
                            >
                                Lamar Posisi

                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 12h14m-7-7l7 7-7 7"
                                    />
                                </svg>

                            </a>

                        @elseif($isPelamar)

                            @if($program->status === 'dibuka')

                                <a
                                    href="{{ route('pelamar.lamaran.create', [
                                        'id_program' => $program->id_program
                                    ]) }}"
                                    class="inline-flex items-center justify-center
                                           gap-2 px-6 py-3 rounded-full
                                           bg-ewforange hover:bg-orange-600
                                           text-white text-xs font-bold
                                           shadow-lg shadow-orange-500/20
                                           transition"
                                >
                                    Lamar Posisi

                                    <svg
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 12h14m-7-7l7 7-7 7"
                                        />
                                    </svg>

                                </a>

                            @else

                                <span
                                    class="inline-flex items-center justify-center
                                           px-6 py-3 rounded-full bg-zinc-100
                                           text-zinc-400 text-xs font-semibold"
                                >
                                    Pendaftaran Ditutup
                                </span>

                            @endif

                        @endif

                    </div>

                </section>

            </div>

        </main>

    </div>

</body>
</html>