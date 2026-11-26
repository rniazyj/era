@php
    $nama = $lamaran->pelamar?->name ?? '-';
    $program = $lamaran->program?->nama_program ?? '-';

    $tanggalMulai = old(
        'tgl_mulai_magang',
        $lamaran->tgl_mulai_magang
            ? \Carbon\Carbon::parse($lamaran->tgl_mulai_magang)->format('Y-m-d')
            : ''
    );

    $tanggalSelesai = old(
        'tgl_selesai_magang',
        $lamaran->tgl_selesai_magang
            ? \Carbon\Carbon::parse($lamaran->tgl_selesai_magang)->format('Y-m-d')
            : ''
    );

    $nomorSurat = old('nomor_surat', $lamaran->surat?->nomor_surat ?? '');
    $tanggalTerbit = old(
        'tgl_terbit',
        $lamaran->surat?->tgl_terbit
            ? \Carbon\Carbon::parse($lamaran->surat->tgl_terbit)->format('Y-m-d')
            : now()->format('Y-m-d')
    );

    $backUrl = route('admin.pelamar.show', $lamaran->id_lamaran);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Terbitkan LOA Magang - Galeri Magang</title>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        [x-cloak] { display: none !important; }

        .field {
            width: 100%;
            border: 1px solid rgba(0,0,0,.1);
            border-radius: .75rem;
            background: white;
            padding: .7rem .9rem;
            font-size: .8rem;
            outline: none;
            transition: all .2s;
        }

        .field:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,.15);
        }
    </style>
</head>

<body class="bg-paper text-zinc-900 font-sans antialiased overflow-x-hidden">

<div class="flex min-h-screen flex-col lg:flex-row">

    @include('partials.sidebar')

    <main class="min-w-0 w-full flex-1 px-4 sm:px-6 md:px-8 lg:px-10 pt-20 sm:pt-24 lg:pt-10 pb-20">

        <div class="max-w-4xl mx-auto space-y-6">

            <div>
                <a href="{{ $backUrl }}"
                   class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-400 hover:text-ewforange transition mb-4">
                    ← Kembali ke Detail Pelamar
                </a>

                <span class="inline-block text-[10px] tracking-[.2em] font-bold text-ewforange bg-orange-50 px-3 py-1 rounded-full uppercase">
                    PENERBITAN SURAT
                </span>

                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight mt-3">
                    Letter of Acceptance
                </h1>

                <p class="text-xs text-zinc-500 mt-2">
                    Lengkapi informasi surat penerimaan magang sebelum menerbitkan PDF.
                </p>
            </div>

            @if($errors->any())
                <div class="rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xs px-5 py-3 space-y-1">
                    @foreach($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <section class="bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-5 sm:p-7 shadow-sm space-y-5">

                <div class="flex items-center justify-between border-b border-black/5 pb-4">
                    <h2 class="text-sm font-bold uppercase tracking-wide">
                        Informasi Pelamar
                    </h2>

                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold">
                        DITERIMA
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs">

                    <div>
                        <p class="text-[11px] font-semibold text-zinc-400 mb-1">
                            Nama Pelamar
                        </p>
                        <p class="font-bold text-zinc-800">{{ $nama }}</p>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold text-zinc-400 mb-1">
                            Program Magang
                        </p>
                        <p class="font-bold text-zinc-800">{{ $program }}</p>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold text-zinc-400 mb-1">
                            Email
                        </p>
                        <p class="font-semibold text-zinc-700">
                            {{ $lamaran->pelamar?->email ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold text-zinc-400 mb-1">
                            Status Lamaran
                        </p>
                        <p class="font-bold text-emerald-600 capitalize">
                            {{ $lamaran->status_lamaran }}
                        </p>
                    </div>

                </div>
            </section>

            <form method="POST"
                  action="{{ route('admin.pelamar.loa.terbitkan', $lamaran->id_lamaran) }}"
                  class="bg-white rounded-2xl sm:rounded-3xl border border-black/5 p-5 sm:p-7 shadow-sm space-y-6">

                @csrf

                <div class="border-b border-black/5 pb-4">
                    <h2 class="text-sm font-bold uppercase tracking-wide">
                        Informasi Surat LOA
                    </h2>
                    <p class="text-[11px] text-zinc-400 mt-1">
                        Data bertanda * wajib diisi.
                    </p>
                </div>

                <div>
                    <label for="nomor_surat" class="block text-xs font-semibold text-zinc-600 mb-2">
                        Nomor Surat <span class="text-rose-500">*</span>
                    </label>

                    <input type="text"
                           id="nomor_surat"
                           name="nomor_surat"
                           value="{{ $nomorSurat }}"
                           maxlength="100"
                           required
                           placeholder="Contoh: 001/LOA/EWF/XI/2026"
                           class="field">
                </div>

                <div>
                    <label for="tgl_terbit" class="block text-xs font-semibold text-zinc-600 mb-2">
                        Tanggal Terbit <span class="text-rose-500">*</span>
                    </label>

                    <input type="date"
                           id="tgl_terbit"
                           name="tgl_terbit"
                           value="{{ $tanggalTerbit }}"
                           required
                           class="field">
                </div>

                <div class="border-t border-black/5 pt-5 space-y-4">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-800">
                            Periode Magang
                        </h3>
                        <p class="text-[11px] text-zinc-400 mt-1">
                            Diisi otomatis berdasarkan data lamaran. Admin dapat menyesuaikannya.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label for="tgl_mulai_magang" class="block text-xs font-semibold text-zinc-600 mb-2">
                                Tanggal Mulai <span class="text-rose-500">*</span>
                            </label>

                            <input type="date"
                                   id="tgl_mulai_magang"
                                   name="tgl_mulai_magang"
                                   value="{{ $tanggalMulai }}"
                                   required
                                   class="field">
                        </div>

                        <div>
                            <label for="tgl_selesai_magang" class="block text-xs font-semibold text-zinc-600 mb-2">
                                Tanggal Selesai <span class="text-rose-500">*</span>
                            </label>

                            <input type="date"
                                   id="tgl_selesai_magang"
                                   name="tgl_selesai_magang"
                                   value="{{ $tanggalSelesai }}"
                                   required
                                   class="field">
                        </div>

                    </div>
                </div>

                <div class="border-t border-black/5 pt-5 space-y-3">
    <div>
        <h3 class="text-xs font-bold text-zinc-800">
            Tanda Tangan dan Stempel
        </h3>

        <p class="text-[11px] text-zinc-400 mt-1">
            Logo perusahaan serta gambar tanda tangan dan stempel
            Supervisor Magang akan otomatis dimasukkan ke dalam PDF.
        </p>
    </div>

    <div class="rounded-2xl bg-orange-50 border border-orange-100 p-4">
        <p class="text-xs font-bold text-orange-700">
            Aset surat tersedia
        </p>
        <p class="text-[11px] text-orange-600 mt-1">
            Tanda tangan dan stempel menggunakan gambar resmi
            yang telah disiapkan.
        </p>
    </div>
</div>

                <div class="rounded-2xl bg-orange-50 border border-orange-100 p-4">
                    <p class="text-xs font-bold text-orange-700">
                        Tanda tangan dan stempel perusahaan
                    </p>
                    <p class="text-[11px] text-orange-600 mt-1">
                        Gambar gabungan tanda tangan dan stempel akan otomatis dimasukkan ke dokumen PDF.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <a href="{{ $backUrl }}"
                       class="flex-1 text-center px-5 py-3 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-bold transition">
                        Batal
                    </a>

                    <button type="submit"
                            onclick="return confirm('Terbitkan LOA untuk {{ addslashes($nama) }}?')"
                            class="flex-1 px-5 py-3 rounded-full bg-ewforange hover:bg-orange-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20 transition">
                        Terbitkan LOA (PDF)
                    </button>
                </div>

            </form>

        </div>
    </main>
</div>

</body>
</html>
